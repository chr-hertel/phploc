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

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * Knows which of the analysed classes are test classes.
 *
 * Because a test class may extend a class that is declared in a file that is
 * analysed later, the registry is filled in a pass of its own before the
 * measuring pass runs.
 *
 * @internal
 */
final class TestClassRegistry
{
    /**
     * Base classes of the test frameworks that are recognised, lowercased.
     */
    private const BASE_CLASSES = [
        'phpunit\\framework\\testcase' => true,
        'phpunit_framework_testcase' => true,
        'codeception\\test\\unit' => true,
        'codeception\\test\\cest' => true,
        'phpspec\\objectbehavior' => true,
    ];

    /**
     * Lowercased class name => lowercased name of its parent class.
     *
     * @var array<string, ?string>
     */
    private array $parents = [];

    /**
     * @var array<string, true>
     */
    private array $withTestMethods = [];

    /**
     * @var array<string, true>
     */
    private array $pestFiles = [];

    /**
     * Whether a file needs to be parsed for the test detection pass at all.
     *
     * The detection pass runs over every analysed file, so ruling a file out
     * by looking at its source is worth a lot: it saves the parse, which is
     * what that pass spends nearly all of its time on.
     *
     * This mirrors what isTestClass() and declaresPestTests() below look for,
     * and **has to be kept in sync with them**, which is why it lives here
     * rather than in the Analyser. A file can only matter if it declares a
     * class that extends something (every class in an inheritance chain that
     * leads to a test case class does), a class that is named like a test, or
     * a Pest test. Erring on the side of parsing is free; erring the other way
     * would silently measure a test class as production code.
     */
    public static function couldDeclareTests(string $source): bool
    {
        if (false !== stripos($source, 'extends') || false !== stripos($source, 'test')) {
            return true;
        }

        return 1 === preg_match('/\b(?:it|describe|uses)\s*\(/i', $source);
    }

    /**
     * A test method is a public, non-static method that is named like a test,
     * is marked as one by an attribute, or is annotated as one.
     */
    public static function isTestMethod(ClassMethod $node): bool
    {
        if (!$node->isPublic() || $node->isStatic()) {
            return false;
        }

        $name = strtolower($node->name->toString());

        if (str_starts_with($name, 'test') || str_starts_with($name, 'it_') || str_starts_with($name, 'its_')) {
            return true;
        }

        foreach ($node->attrGroups as $attributeGroup) {
            foreach ($attributeGroup->attrs as $attribute) {
                if ('test' === strtolower($attribute->name->getLast())) {
                    return true;
                }
            }
        }

        $docComment = $node->getDocComment()?->getText();

        if (null === $docComment) {
            return false;
        }

        return str_contains($docComment, '@test') || str_contains($docComment, '@scenario');
    }

    /**
     * Pest declares its tests as function calls instead of as classes.
     */
    public static function declaresPestTests(FuncCall $node): bool
    {
        return self::isCallTo($node, ['test', 'it', 'describe', 'uses']);
    }

    public static function isPestTestCall(FuncCall $node): bool
    {
        return self::isCallTo($node, ['test', 'it']);
    }

    public function addClass(string $name, ?string $parent, bool $hasTestMethods): void
    {
        $name = strtolower($name);

        $this->parents[$name] = null === $parent ? null : strtolower($parent);

        if ($hasTestMethods) {
            $this->withTestMethods[$name] = true;
        }
    }

    /**
     * @param non-empty-string $file
     */
    public function addPestFile(string $file): void
    {
        $this->pestFiles[$file] = true;
    }

    /**
     * @param non-empty-string $file
     */
    public function isPestFile(string $file): bool
    {
        return isset($this->pestFiles[$file]);
    }

    public function isTestClass(string $name): bool
    {
        $name = strtolower($name);

        if (!\array_key_exists($name, $this->parents)) {
            return false;
        }

        $parent = $this->parents[$name];
        $seen = [];

        while (null !== $parent && !isset($seen[$parent])) {
            $seen[$parent] = true;

            if (isset(self::BASE_CLASSES[$parent])) {
                return true;
            }

            // Fallback for base classes that are not part of the analysed code
            if (str_ends_with($parent, 'testcase')) {
                return true;
            }

            $parent = $this->parents[$parent] ?? null;
        }

        // Fallback for test frameworks that are not recognised: a class that is
        // named like a test and that declares test methods is one
        return isset($this->withTestMethods[$name]) && str_ends_with($name, 'test');
    }

    /**
     * @param list<non-empty-string> $names
     */
    private static function isCallTo(FuncCall $node, array $names): bool
    {
        if (!$node->name instanceof Name) {
            return false;
        }

        return \in_array(strtolower($node->name->toString()), $names, true) && [] !== $node->getArgs();
    }
}
