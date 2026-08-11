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

final readonly class TextResultFormatter
{
    /**
     * @return non-empty-string
     */
    public function format(Result $result): string
    {
        return $this->header($result)
            .$this->size($result->size())
            .$this->complexity($result->complexity())
            .$this->dependencies($result->dependencies())
            .$this->structure($result->structure())
            .$this->tests($result->tests())
            .$this->errors($result);
    }

    /**
     * @return non-empty-string
     */
    private function header(Result $result): string
    {
        return \sprintf(
            <<<'EOT'
                Directories                                 %10s
                Files                                       %10s


                EOT,
            $this->number($result->directories()),
            $this->number($result->files()),
        );
    }

    private function size(Size $size): string
    {
        return \sprintf(
            <<<'EOT'
                Size
                  Lines of Code (LOC)                       %10s
                  Comment Lines of Code (CLOC)              %10s (%.2f%%)
                  Non-Comment Lines of Code (NCLOC)         %10s (%.2f%%)
                  Logical Lines of Code (LLOC)              %10s (%.2f%%)
                    Classes                                 %10s (%.2f%%)
                      Average Class Length                  %10s
                        Minimum Class Length                %10s
                        Maximum Class Length                %10s
                      Average Method Length                 %10s
                        Minimum Method Length               %10s
                        Maximum Method Length               %10s
                      Average Methods Per Class             %10s
                        Minimum Methods Per Class           %10s
                        Maximum Methods Per Class           %10s
                    Functions                               %10s (%.2f%%)
                      Average Function Length               %10s
                    Not in classes or functions             %10s (%.2f%%)


                EOT,
            $this->number($size->linesOfCode()),
            $this->number($size->commentLinesOfCode()),
            $size->commentLinesOfCodePercentage(),
            $this->number($size->nonCommentLinesOfCode()),
            $size->nonCommentLinesOfCodePercentage(),
            $this->number($size->logicalLinesOfCode()),
            $size->logicalLinesOfCodePercentage(),
            $this->number($size->logicalLinesInClasses()),
            $size->logicalLinesInClassesPercentage(),
            $this->number($size->classLength()->average()),
            $this->number($size->classLength()->minimum()),
            $this->number($size->classLength()->maximum()),
            $this->number($size->methodLength()->average()),
            $this->number($size->methodLength()->minimum()),
            $this->number($size->methodLength()->maximum()),
            $this->number($size->methodsPerClass()->average()),
            $this->number($size->methodsPerClass()->minimum()),
            $this->number($size->methodsPerClass()->maximum()),
            $this->number($size->logicalLinesInFunctions()),
            $size->logicalLinesInFunctionsPercentage(),
            $this->number($size->averageFunctionLength()),
            $this->number($size->logicalLinesNotInClassesOrFunctions()),
            $size->logicalLinesNotInClassesOrFunctionsPercentage(),
        );
    }

    private function complexity(Complexity $complexity): string
    {
        return \sprintf(
            <<<'EOT'
                Cyclomatic Complexity
                  Average Complexity per LLOC               %10.2f
                  Average Complexity per Class              %10.2f
                    Minimum Class Complexity                %10.2f
                    Maximum Class Complexity                %10.2f
                  Average Complexity per Method             %10.2f
                    Minimum Method Complexity               %10.2f
                    Maximum Method Complexity               %10.2f
                  Average Complexity per Function           %10.2f
                    Minimum Function Complexity             %10.2f
                    Maximum Function Complexity             %10.2f


                EOT,
            $complexity->averagePerLogicalLine(),
            $complexity->classes()->average(),
            $complexity->classes()->minimum(),
            $complexity->classes()->maximum(),
            $complexity->methods()->average(),
            $complexity->methods()->minimum(),
            $complexity->methods()->maximum(),
            $complexity->functions()->average(),
            $complexity->functions()->minimum(),
            $complexity->functions()->maximum(),
        );
    }

    private function dependencies(Dependencies $dependencies): string
    {
        return \sprintf(
            <<<'EOT'
                Dependencies
                  Global Accesses                           %10s
                    Global Constants                        %10s (%.2f%%)
                    Global Variables                        %10s (%.2f%%)
                    Super-Global Variables                  %10s (%.2f%%)
                  Attribute Accesses                        %10s
                    Non-Static                              %10s (%.2f%%)
                    Static                                  %10s (%.2f%%)
                  Method Calls                              %10s
                    Non-Static                              %10s (%.2f%%)
                    Static                                  %10s (%.2f%%)


                EOT,
            $this->number($dependencies->globalAccesses()),
            $this->number($dependencies->globalConstantAccesses()),
            $dependencies->globalConstantAccessesPercentage(),
            $this->number($dependencies->globalVariableAccesses()),
            $dependencies->globalVariableAccessesPercentage(),
            $this->number($dependencies->superGlobalVariableAccesses()),
            $dependencies->superGlobalVariableAccessesPercentage(),
            $this->number($dependencies->attributeAccesses()),
            $this->number($dependencies->nonStaticAttributeAccesses()),
            $dependencies->nonStaticAttributeAccessesPercentage(),
            $this->number($dependencies->staticAttributeAccesses()),
            $dependencies->staticAttributeAccessesPercentage(),
            $this->number($dependencies->methodCalls()),
            $this->number($dependencies->nonStaticMethodCalls()),
            $dependencies->nonStaticMethodCallsPercentage(),
            $this->number($dependencies->staticMethodCalls()),
            $dependencies->staticMethodCallsPercentage(),
        );
    }

    private function structure(Structure $structure): string
    {
        return \sprintf(
            <<<'EOT'
                Structure
                  Namespaces                                %10s
                  Interfaces                                %10s
                  Traits                                    %10s
                  Enums                                     %10s
                  Classes                                   %10s
                    Abstract Classes                        %10s (%.2f%%)
                    Concrete Classes                        %10s (%.2f%%)
                      Final Classes                         %10s (%.2f%%)
                      Non-Final Classes                     %10s (%.2f%%)
                  Methods                                   %10s
                    Scope
                      Non-Static Methods                    %10s (%.2f%%)
                      Static Methods                        %10s (%.2f%%)
                    Visibility
                      Public Methods                        %10s (%.2f%%)
                      Protected Methods                     %10s (%.2f%%)
                      Private Methods                       %10s (%.2f%%)
                  Functions                                 %10s
                    Named Functions                         %10s (%.2f%%)
                    Anonymous Functions                     %10s (%.2f%%)
                  Constants                                 %10s
                    Global Constants                        %10s (%.2f%%)
                    Class Constants                         %10s (%.2f%%)
                      Public Constants                      %10s (%.2f%%)
                      Non-Public Constants                  %10s (%.2f%%)

                EOT,
            $this->number($structure->namespaces()),
            $this->number($structure->interfaces()),
            $this->number($structure->traits()),
            $this->number($structure->enums()),
            $this->number($structure->classes()),
            $this->number($structure->abstractClasses()),
            $structure->abstractClassesPercentage(),
            $this->number($structure->concreteClasses()),
            $structure->concreteClassesPercentage(),
            $this->number($structure->finalClasses()),
            $structure->finalClassesPercentage(),
            $this->number($structure->nonFinalClasses()),
            $structure->nonFinalClassesPercentage(),
            $this->number($structure->methods()),
            $this->number($structure->nonStaticMethods()),
            $structure->nonStaticMethodsPercentage(),
            $this->number($structure->staticMethods()),
            $structure->staticMethodsPercentage(),
            $this->number($structure->publicMethods()),
            $structure->publicMethodsPercentage(),
            $this->number($structure->protectedMethods()),
            $structure->protectedMethodsPercentage(),
            $this->number($structure->privateMethods()),
            $structure->privateMethodsPercentage(),
            $this->number($structure->functions()),
            $this->number($structure->namedFunctions()),
            $structure->namedFunctionsPercentage(),
            $this->number($structure->anonymousFunctions()),
            $structure->anonymousFunctionsPercentage(),
            $this->number($structure->constants()),
            $this->number($structure->globalConstants()),
            $structure->globalConstantsPercentage(),
            $this->number($structure->classConstants()),
            $structure->classConstantsPercentage(),
            $this->number($structure->publicClassConstants()),
            $structure->publicClassConstantsPercentage(),
            $this->number($structure->nonPublicClassConstants()),
            $structure->nonPublicClassConstantsPercentage(),
        );
    }

    private function tests(Tests $tests): string
    {
        return \sprintf(
            <<<'EOT'

                Tests
                  Classes                                   %10s
                  Methods                                   %10s

                EOT,
            $this->number($tests->classes()),
            $this->number($tests->methods()),
        );
    }

    private function errors(Result $result): string
    {
        if (!$result->hasErrors()) {
            return '';
        }

        $buffer = \PHP_EOL.'Errors:'.\PHP_EOL;

        foreach ($result->errors() as $error) {
            $buffer .= \sprintf('  %s'.\PHP_EOL, $error);
        }

        return $buffer;
    }

    private function number(float|int $value): string
    {
        return number_format($value);
    }
}
