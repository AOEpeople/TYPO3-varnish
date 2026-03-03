<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\PHPUnit\CodeQuality\Rector\MethodCall\ScalarArgumentToExpectedParamTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\StmtsAwareInterface\DeclareStrictTypesTestsRector;
use Rector\PHPUnit\PHPUnit120\Rector\Class_\PropertyCreateMockToCreateStubRector;
use Rector\PHPUnit\PHPUnit60\Rector\MethodCall\GetMockBuilderGetMockToCreateMockRector;
use Rector\Privatization\Rector\Class_\FinalizeTestCaseClassRector;
use Rector\Set\ValueObject\SetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictSetUpRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../Classes',
        __DIR__ . '/../Tests',
        __DIR__ . '/rector.php',
    ])
    ->withPhpSets(
        true
    )
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::STRICT_BOOLEANS,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::INSTANCEOF,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY
    ])
    ->withSkip([
        TypedPropertyFromStrictSetUpRector::class,
        AddMethodCallBasedStrictParamTypeRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        DeclareStrictTypesTestsRector::class,
        PropertyCreateMockToCreateStubRector::class,
        GetMockBuilderGetMockToCreateMockRector::class,
        FinalizeTestCaseClassRector::class,
        ScalarArgumentToExpectedParamTypeRector::class => [
            __DIR__ . '/../Tests/Unit/Domain/Model/Tag/PageIdTagTest.php',
            __DIR__ . '/../Tests/Unit/System/HeaderTest.php',
        ],
    ])
    ->withAutoloadPaths([__DIR__ . '/../Classes'])
    ->registerService(RemoveUnusedPrivatePropertyRector::class);
