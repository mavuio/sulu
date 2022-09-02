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



use Rector\Core\Configuration\Option;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TheCodingMachine\GraphQLite\Annotations as GraphQLite;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets
    // $containerConfigurator->import(SetList::CODE_QUALITY);

    // Set parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $services = $containerConfigurator->services();

    // @Validate and @Assertion are part of other libraries, include if necessary
    $services->set(AnnotationToAttributeRector::class)
        ->configure([
            new AnnotationToAttribute(GraphQLite\Query::class),
            new AnnotationToAttribute(GraphQLite\Mutation::class),
            new AnnotationToAttribute(GraphQLite\Type::class),
            new AnnotationToAttribute(GraphQLite\ExtendType::class),
            new AnnotationToAttribute(GraphQLite\Input::class),
            new AnnotationToAttribute(GraphQLite\Field::class),
            new AnnotationToAttribute(GraphQLite\SourceField::class),
            new AnnotationToAttribute(GraphQLite\MagicField::class),
            new AnnotationToAttribute(GraphQLite\Logged::class),
            new AnnotationToAttribute(GraphQLite\Right::class),
            new AnnotationToAttribute(GraphQLite\FailWith::class),
            new AnnotationToAttribute(GraphQLite\HideIfUnauthorized::class),
            new AnnotationToAttribute(GraphQLite\InjectUser::class),
            new AnnotationToAttribute(GraphQLite\Security::class),
            new AnnotationToAttribute(GraphQLite\Factory::class),
            new AnnotationToAttribute(GraphQLite\UseInputType::class),
            new AnnotationToAttribute(GraphQLite\Decorate::class),
            new AnnotationToAttribute(GraphQLite\Autowire::class),
            new AnnotationToAttribute(GraphQLite\HideParameter::class),
            new AnnotationToAttribute(GraphQLite\EnumType::class),
        ]);
};
