<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SebastianBergmann\Complexity\CyclomaticComplexityCalculatingVisitor;

/**
 * Measures a single file and reports what it finds to the shared Collector.
 *
 * @internal
 */
final class MetricsVisitor extends NodeVisitorAbstract
{
    private const SUPER_GLOBALS = [
        '_ENV' => true,
        '_POST' => true,
        '_GET' => true,
        '_COOKIE' => true,
        '_SERVER' => true,
        '_FILES' => true,
        '_REQUEST' => true,
        '_SESSION' => true,
        'HTTP_ENV_VARS' => true,
        'HTTP_POST_VARS' => true,
        'HTTP_GET_VARS' => true,
        'HTTP_COOKIE_VARS' => true,
        'HTTP_SERVER_VARS' => true,
        'HTTP_POST_FILES' => true,
    ];

    /**
     * @var array<int, true>
     */
    private array $countedLines = [];

    /**
     * @var list<Scope>
     */
    private array $classScopes = [];

    /**
     * @var list<Scope>
     */
    private array $methodScopes = [];

    private int $functionDepth = 0;

    private int $testClassDepth = 0;

    private int $excludedDecisionPoints = 0;

    public function __construct(
        private readonly Collector $collector,
        private readonly TestClassRegistry $tests,
        private readonly bool $isPestFile,
    ) {
    }

    /**
     * @param Node[] $nodes
     */
    public function beforeTraverse(array $nodes): null
    {
        if ($this->isPestFile) {
            $this->collector->addTestClass();
        }

        return null;
    }

    public function enterNode(Node $node): null
    {
        // A Pest file has no test class to skip, so the whole file is skipped
        if ($this->isPestFile) {
            if ($node instanceof Expr\FuncCall && TestClassRegistry::isPestTestCall($node)) {
                $this->collector->addTestMethod();
            }

            return null;
        }

        if ($node instanceof Stmt\ClassLike) {
            $this->enterClassLike($node);

            return null;
        }

        if ($this->testClassDepth > 0) {
            if ($node instanceof Stmt\ClassMethod && TestClassRegistry::isTestMethod($node)) {
                $this->collector->addTestMethod();
            }

            return null;
        }

        if ($node instanceof Expr) {
            $this->countLogicalLine($node->getStartLine());
            $this->countDependency($node);
        }

        if ($node instanceof Stmt\Namespace_) {
            if (null !== $node->name) {
                $this->collector->addNamespace($node->name->toString());
            }
        } elseif ($node instanceof Stmt\ClassMethod) {
            $this->collector->addMethod($node->isStatic(), $this->visibility($node));

            ++$this->functionDepth;
            $this->methodScopes[] = new Scope();
        } elseif ($node instanceof Stmt\Function_ || $node instanceof Expr\Closure || $node instanceof Expr\ArrowFunction) {
            ++$this->functionDepth;
        } elseif ($node instanceof Stmt\Const_) {
            foreach ($node->consts as $constant) {
                $this->collector->addGlobalConstant($constant->name->toString());
            }
        } elseif ($node instanceof Stmt\ClassConst) {
            $this->collector->addClassConstants(\count($node->consts), $node->isPublic());
        } elseif ($node instanceof Stmt\Global_) {
            $this->collector->addGlobalVariableAccess();
        }

        return null;
    }

    public function leaveNode(Node $node): null
    {
        if ($this->isPestFile) {
            return null;
        }

        if ($node instanceof Stmt\ClassLike) {
            $this->leaveClassLike();

            return null;
        }

        if ($this->testClassDepth > 0) {
            return null;
        }

        if ($node instanceof Stmt\ClassMethod) {
            $scope = array_pop($this->methodScopes);

            \assert($scope instanceof Scope);

            --$this->functionDepth;

            if (null !== $node->stmts) {
                $this->collector->addMethodBody($scope->logicalLines, $this->cyclomaticComplexity($node->stmts));
            }
        } elseif ($node instanceof Stmt\Function_) {
            --$this->functionDepth;

            $this->collector->addNamedFunction($this->cyclomaticComplexity($node->stmts));
        } elseif ($node instanceof Expr\Closure) {
            --$this->functionDepth;

            $this->collector->addAnonymousFunction($this->cyclomaticComplexity($node->stmts));
        } elseif ($node instanceof Expr\ArrowFunction) {
            --$this->functionDepth;

            $this->collector->addAnonymousFunction($this->cyclomaticComplexity([$node->expr]));
        }

        return null;
    }

    /**
     * @param Node[] $nodes
     */
    public function afterTraverse(array $nodes): null
    {
        if ($this->isPestFile) {
            return null;
        }

        $this->collector->addDecisionPoints(
            max(0, $this->cyclomaticComplexity($nodes) - 1 - $this->excludedDecisionPoints),
        );

        return null;
    }

    private function enterClassLike(Stmt\ClassLike $node): void
    {
        // Anything nested inside a test class is skipped along with it
        if ($this->testClassDepth > 0) {
            ++$this->testClassDepth;
            $this->classScopes[] = new Scope();

            return;
        }

        if ($this->isTestClass($node)) {
            $this->collector->addTestClass();
            $this->excludedDecisionPoints += $this->cyclomaticComplexity([$node]) - 1;

            $this->testClassDepth = 1;
            $this->classScopes[] = new Scope();

            return;
        }

        if ($node instanceof Stmt\Interface_) {
            $this->collector->addInterface();
        } elseif ($node instanceof Stmt\Trait_) {
            $this->collector->addTrait();
        } elseif ($node instanceof Stmt\Enum_) {
            $this->collector->addEnum();
        } elseif ($node instanceof Stmt\Class_) {
            if ($node->isAbstract()) {
                $this->collector->addAbstractClass();
            } elseif ($node->isFinal()) {
                $this->collector->addFinalClass();
            } else {
                $this->collector->addNonFinalClass();
            }
        }

        $this->classScopes[] = new Scope(
            $this->cyclomaticComplexity([$node]),
            $this->declaredMethods($node),
            !$node instanceof Stmt\Interface_,
        );
    }

    private function leaveClassLike(): void
    {
        $scope = array_pop($this->classScopes);

        \assert($scope instanceof Scope);

        if ($this->testClassDepth > 0) {
            --$this->testClassDepth;

            return;
        }

        $this->collector->addClassLike(
            $scope->logicalLines,
            $scope->complexity,
            $scope->methods,
            $scope->includeInStatistics,
        );
    }

    private function isTestClass(Stmt\ClassLike $node): bool
    {
        if (!$node instanceof Stmt\Class_ || null === $node->name) {
            return false;
        }

        return $this->tests->isTestClass($node->namespacedName?->toString() ?? $node->name->toString());
    }

    /**
     * @return non-negative-int
     */
    private function declaredMethods(Stmt\ClassLike $node): int
    {
        $methods = 0;

        foreach ($node->stmts as $statement) {
            if ($statement instanceof Stmt\ClassMethod) {
                ++$methods;
            }
        }

        return $methods;
    }

    private function visibility(Stmt\ClassMethod $node): Visibility
    {
        if ($node->isPrivate()) {
            return Visibility::Private;
        }

        if ($node->isProtected()) {
            return Visibility::Protected;
        }

        return Visibility::Public;
    }

    private function countLogicalLine(int $line): void
    {
        if (isset($this->countedLines[$line])) {
            return;
        }

        $this->countedLines[$line] = true;

        if ([] !== $this->methodScopes) {
            ++$this->methodScopes[array_key_last($this->methodScopes)]->logicalLines;
        }

        if ([] !== $this->classScopes) {
            ++$this->classScopes[array_key_last($this->classScopes)]->logicalLines;
        } elseif ($this->functionDepth > 0) {
            $this->collector->addLogicalLineInFunction();
        } else {
            $this->collector->addLogicalLineNotInClassOrFunction();
        }
    }

    private function countDependency(Expr $node): void
    {
        if ($node instanceof Expr\PropertyFetch || $node instanceof Expr\NullsafePropertyFetch) {
            $this->collector->addAttributeAccess(false);
        } elseif ($node instanceof Expr\StaticPropertyFetch) {
            $this->collector->addAttributeAccess(true);
        } elseif ($node instanceof Expr\MethodCall || $node instanceof Expr\NullsafeMethodCall) {
            $this->collector->addMethodCall(false);
        } elseif ($node instanceof Expr\StaticCall) {
            $this->collector->addMethodCall(true);
        } elseif ($node instanceof Expr\ConstFetch) {
            $this->countConstantAccess($node);
        } elseif ($node instanceof Expr\Variable) {
            $this->countVariableAccess($node);
        } elseif ($node instanceof Expr\FuncCall) {
            $this->countDefine($node);
        }
    }

    private function countConstantAccess(Expr\ConstFetch $node): void
    {
        $name = $node->name->getLast();

        if (\in_array(strtolower($name), ['true', 'false', 'null'], true)) {
            return;
        }

        $this->collector->addConstantAccess($name);
    }

    private function countVariableAccess(Expr\Variable $node): void
    {
        if (!\is_string($node->name)) {
            return;
        }

        if ('GLOBALS' === $node->name) {
            $this->collector->addGlobalVariableAccess();
        } elseif (isset(self::SUPER_GLOBALS[$node->name])) {
            $this->collector->addSuperGlobalVariableAccess();
        }
    }

    private function countDefine(Expr\FuncCall $node): void
    {
        if (!$node->name instanceof Node\Name || 'define' !== strtolower($node->name->toString())) {
            return;
        }

        $arguments = $node->getArgs();

        if ([] === $arguments || !$arguments[0]->value instanceof Node\Scalar\String_) {
            $this->collector->addGlobalConstant(null);

            return;
        }

        $name = $arguments[0]->value->value;
        $position = strrpos($name, '\\');

        $this->collector->addGlobalConstant(false === $position ? $name : substr($name, $position + 1));
    }

    /**
     * @param Node[] $nodes
     *
     * @return positive-int
     */
    private function cyclomaticComplexity(array $nodes): int
    {
        $traverser = new NodeTraverser();
        $visitor = new CyclomaticComplexityCalculatingVisitor();

        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);

        return $visitor->cyclomaticComplexity();
    }
}
