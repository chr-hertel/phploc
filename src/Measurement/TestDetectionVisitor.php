<?php

/*
 * This file is part of PHPLOC.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hertel\PhpLoc\Measurement;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

/**
 * Collects the class hierarchy of a file so that test classes can be
 * recognised before the measuring pass runs.
 *
 * This pass runs over every analysed file, so it does as little as it can get
 * away with: it never descends into the body of a class-like or of a function,
 * which leaves out the vast majority of the nodes of a file. A class that is
 * declared inside a function is therefore not registered and can only be
 * recognised as a test class by its own name.
 *
 * @internal
 */
final class TestDetectionVisitor extends NodeVisitorAbstract
{
    /**
     * @param non-empty-string $file
     */
    public function __construct(
        private readonly TestClassRegistry $registry,
        private readonly string $file,
    ) {
    }

    public function enterNode(Node $node): ?int
    {
        if ($node instanceof ClassLike) {
            // NameResolver has resolved the name and the parent of the class
            // before this runs, and the methods are read from the node itself
            if ($node instanceof Class_ && null !== $node->name) {
                $this->registry->addClass(
                    $node->namespacedName?->toString() ?? $node->name->toString(),
                    $node->extends?->toString(),
                    $this->hasTestMethods($node),
                );
            }

            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        if ($node instanceof FunctionLike) {
            return NodeVisitor::DONT_TRAVERSE_CHILDREN;
        }

        // Since neither class-likes nor functions are descended into, every
        // call that is reached here is one at the top level of the file. Only
        // those declare Pest tests, so that production code which happens to
        // call a function named test() or it() is not mistaken for a test.
        if ($node instanceof FuncCall && TestClassRegistry::declaresPestTests($node)) {
            $this->registry->addPestFile($this->file);
        }

        return null;
    }

    private function hasTestMethods(Class_ $node): bool
    {
        foreach ($node->stmts as $statement) {
            if ($statement instanceof ClassMethod && TestClassRegistry::isTestMethod($statement)) {
                return true;
            }
        }

        return false;
    }
}
