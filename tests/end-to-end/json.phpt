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
        "commentPercentage": 21.%d,
        "nonComment": 120,
        "nonCommentPercentage": 78.%d,
        "logical": 40,
        "logicalPercentage": 26.%d
    },
    "classesOrTraits": 2,
    "methods": {
        "count": 2,
        "cyclomaticComplexity": {
            "lowest": 14,
            "average": 14.0,
            "highest": 14
        }
    },
    "functions": {
        "count": 1,
        "cyclomaticComplexity": {
            "lowest": 14,
            "average": 14.0,
            "highest": 14
        }
    },
    "errors": []
}
