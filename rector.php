<?php

// declare(strict_types=1);

// use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
// use Rector\Config\RectorConfig;
// use Rector\Set\ValueObject\LevelSetList;
// use Rector\Symfony\Set\SymfonySetList;


// return static function (RectorConfig $rectorConfig): void {
//     $rectorConfig->paths([
//         __DIR__ . '/mavu_src'
//     ]);


//     $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/admin/dev/App_KernelDevDebugContainer.xml');

//     $rectorConfig->sets([
//         SymfonySetList::SYMFONY_60,
//         SymfonySetList::SYMFONY_CODE_QUALITY,
//         SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
//     ]);



// };

use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Set\SensiolabsSetList;
use Rector\Config\RectorConfig;

return function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SensiolabsSetList::FRAMEWORK_EXTRA_61,
    ]);
};
