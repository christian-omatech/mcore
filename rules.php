<?php declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff;
use SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff;

return [
    'preset' => 'default',
    'remove' => [
        ParameterTypeHintSniff::class,
        ReturnTypeHintSniff::class,
        PropertyTypeHintSniff::class,
        SuperfluousExceptionNamingSniff::class,
        SuperfluousInterfaceNamingSniff::class,
        ForbiddenSetterSniff::class,
        ForbiddenNormalClasses::class,
        OrderedImportsFixer::class
    ],
    'config' => [
        DeclareStrictTypesSniff::class => [
            'declareOnFirstLine' => true
        ],
        CyclomaticComplexityIsHigh::class => [
            'maxComplexity' => 10
        ],
        LineLengthSniff::class => [
            'lineLimit' => 100,
            'absoluteLineLimit' => 120
        ],
        OrderedClassElementsFixer::class => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
            ]
        ],
        ForbiddenFunctionsSniff::class => [
            'forbiddenFunctions' => [
                'dd' => null,
                'dump' => null
            ]
        ]
    ]
];
