--TEST--
phploc --json ../_fixture
--FILE--
<?php declare(strict_types=1);
require __DIR__ . '/_phploc.php';

phploc('--json', __DIR__ . '/../_fixture');
--EXPECTF--
{
    "directories": 1,
    "files": 4,
    "linesOfCode": {
        "total": 152,
        "comment": 32,
        "commentPercentage": %f,
        "nonComment": 120,
        "nonCommentPercentage": %f,
        "logical": 40,
        "logicalPercentage": %f,
        "logicalInClasses": 24,
        "logicalInClassesPercentage": %f,
        "logicalInFunctions": 12,
        "logicalInFunctionsPercentage": %f,
        "logicalNotInClassesOrFunctions": 4,
        "logicalNotInClassesOrFunctionsPercentage": %f
    },
    "length": {
        "class": {
            "minimum": 12,
            "average": %f,
            "maximum": 12
        },
        "method": {
            "minimum": 12,
            "average": %f,
            "maximum": 12
        },
        "methodsPerClass": {
            "minimum": 1,
            "average": %f,
            "maximum": 2
        },
        "averageFunction": %f
    },
    "averageComplexityPerLogicalLine": %f,
    "classesOrTraits": 2,
    "classes": {
        "count": 1,
        "abstract": 1,
        "concrete": 0,
        "final": 0,
        "nonFinal": 0,
        "cyclomaticComplexity": {
            "lowest": 14,
            "average": %f,
            "highest": 14
        }
    },
    "methods": {
        "count": 4,
        "cyclomaticComplexity": {
            "lowest": 14,
            "average": %f,
            "highest": 14
        },
        "nonStatic": 4,
        "static": 0,
        "public": 4,
        "protected": 0,
        "private": 0
    },
    "functions": {
        "count": 1,
        "cyclomaticComplexity": {
            "lowest": 14,
            "average": %f,
            "highest": 14
        },
        "named": 1,
        "anonymous": 0
    },
    "namespaces": 1,
    "interfaces": 1,
    "traits": 1,
    "enums": 0,
    "constants": {
        "count": 0,
        "global": 0,
        "class": 0,
        "publicClass": 0,
        "nonPublicClass": 0
    },
    "dependencies": {
        "globalAccesses": {
            "count": 0,
            "constants": 0,
            "variables": 0,
            "superGlobalVariables": 0
        },
        "attributeAccesses": {
            "count": 0,
            "nonStatic": 0,
            "static": 0
        },
        "methodCalls": {
            "count": 0,
            "nonStatic": 0,
            "static": 0
        }
    },
    "tests": {
        "classes": 0,
        "methods": 0
    },
    "errors": []
}
