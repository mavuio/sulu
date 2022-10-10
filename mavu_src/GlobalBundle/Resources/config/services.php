<?php

declare(strict_types=1);

use Mavu\GlobalBundle\Admin\DekorAdmin;
use Mavu\GlobalBundle\Admin\DoctrineListRepresentationFactory;
use Mavu\GlobalBundle\Admin\MavuSeoAdmin;
use Mavu\GlobalBundle\Behavior\UrlBasedAutoNameSubscriber;
use Mavu\GlobalBundle\Content\Select\BlockTypeSelect;
use Mavu\GlobalBundle\Content\Types\DekorSelection;
use Mavu\GlobalBundle\Content\Types\MavuSvelteField;
use Mavu\GlobalBundle\Content\Types\SingleDekorSelection;
use Mavu\GlobalBundle\Core\DekorCore;
use Mavu\GlobalBundle\Core\InformationProvider;
use Mavu\GlobalBundle\Core\JsonImportCore;
use Mavu\GlobalBundle\Core\TwClassesCore;
use Mavu\GlobalBundle\Document\Subscriber\TwClassCollectorSubscriber;
use Mavu\GlobalBundle\Form\Types\DateType;
use Mavu\GlobalBundle\Preview\Preview;
use Mavu\GlobalBundle\Structure\MavuSeoStructureExtension;
use Mavu\GlobalBundle\Structure\TwClassesStructureExtension;
use Mavu\GlobalBundle\Tags\LinkTag;
use Mavu\GlobalBundle\Twig\BlockPathsExtension;
use Mavu\GlobalBundle\Twig\DecodeJsonExtension;
use Mavu\GlobalBundle\Twig\FindIconExtension;
use Mavu\GlobalBundle\Twig\FindPageExtension;
use Mavu\GlobalBundle\Twig\MergedMediaMetaExtension;
use Mavu\GlobalBundle\Twig\TransExtension;
use Mavu\GlobalBundle\Twig\TwClassExtractorExtension;
use Psr\Log\LoggerInterface;
use Sulu\Bundle\MarkupBundle\Markup\HtmlTagExtractor;
use Symfony\Cmf\Api\Slugifier\SlugifierInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();



    $parameters->set('env(NPM_BINARY)', 'npm');

    $services = $containerConfigurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$projectDir', '%kernel.project_dir%');

    $services->set('mavu.tw_classes_core', TwClassesCore::class)
        ->arg('$bundleConfig', '');
    $services->alias(TwClassesCore::class, 'mavu.tw_classes_core');

    $services->set('mavu.dekor_core', DekorCore::class);
    $services->alias(DekorCore::class, 'mavu.dekor_core');

    $services->set('mavu.json_import_core', JsonImportCore::class)
        ->args([service("sulu_media.collection_repository")]);

    $services->alias(JsonImportCore::class, 'mavu.json_import_core');

    $services->set('mavu.document.subscriber.tw_class_collector', TwClassCollectorSubscriber::class)
        ->tag('sulu_document_manager.event_subscriber')
        ->args([service('sulu_document_manager.property_encoder'), service(LoggerInterface::class), service(TwClassesCore::class)]);

    $services->set(TwClassExtractorExtension::class)
        ->tag('twig.extension');

    $services->set(MergedMediaMetaExtension::class)
        ->tag('twig.extension');

    $services->set(BlockPathsExtension::class)
        ->tag('twig.extension');

    $services->set(DecodeJsonExtension::class)
        ->tag('twig.extension');

    $services->set(FindPageExtension::class)
        ->tag('twig.extension');

    $services->set(FindIconExtension::class)
        ->tag('twig.extension');

    $services->set(TransExtension::class)
        ->tag('twig.extension');

    $services->set('mavu.global.mavu_seo.admin', MavuSeoAdmin::class)
        ->tag('sulu.admin')
        ->tag('sulu.context', ['context' => 'admin'])
        ->args([service('sulu_admin.view_builder_factory'), service('sulu_core.webspace.webspace_manager'), service('sulu_security.security_checker')]);

    $services->set(InformationProvider::class)
        ->public();

    $services->set('app.tw_classes_structure_extension', TwClassesStructureExtension::class)
        ->tag('sulu.structure.extension');

    $services->set('app.mavu_seo_structure_extension', MavuSeoStructureExtension::class)
        ->tag('sulu.structure.extension');

    $services->set('mavu.global.dekor.admin', DekorAdmin::class)
        ->tag('sulu.admin')
        ->tag('sulu.context', ['context' => 'admin'])
        ->args([service('sulu_admin.view_builder_factory'), service('sulu_security.security_checker')]);

    $services->set(Preview::class)


        ->args([service('sulu_preview.preview_object_provider_registry'), service('sulu_preview.preview.cache'), service('sulu_preview.preview.renderer'), service(TwClassesCore::class)]);

    $services->set(BlockTypeSelect::class)
        ->public();

    $services->set('mavu_global_dekor.list.praesentation', DoctrineListRepresentationFactory::class)
        ->public()
        ->tag('sulu.context', ['context' => 'admin'])
        ->args([service('sulu_core.rest_helper'), service('sulu_core.list_rest_helper'), service('sulu_core.doctrine_list_builder_factory'), service('sulu_core.list_builder.field_descriptor_factory')]);

    $services->set(DekorSelection::class)
        ->tag('sulu.content.type', ['alias' => 'dekor_selection']);

    $services->set(SingleDekorSelection::class)
        ->tag('sulu.content.type', ['alias' => 'single_dekor_selection']);

    $services->set(MavuSvelteField::class)
        ->tag('sulu.content.type', ['alias' => 'mavu_svelte_field']);

    $services->set('sulu_document_manager.subscriber.behavior.auto_name', UrlBasedAutoNameSubscriber::class)
        ->tag('sulu_document_manager.event_subscriber')
        ->args([service('sulu_document_manager.document_registry'), service('sulu_document_manager.node_name_slugifier'), service('sulu_document_manager.name_resolver'), service('sulu_document_manager.node_manager'), service('sulu_document_manager.live_session'), service('sulu_document_manager.default_session')]);

    $services->set('sulu_form.dynamic.type_date');

    $services->set(DateType::class)
        ->decorate('sulu_form.dynamic.type_date');

    $services->set('app.html_extractor', HtmlTagExtractor::class)
        ->tag('sulu_markup.parser.html_extractor')
        ->args(['mavu']);

    $services->set('mavu_markup.link_tag', LinkTag::class)
        ->tag('sulu_markup.tag', ['namespace' => 'sulu', 'tag' => 'link', 'type' => 'html'])


        ->args([service('sulu_markup.link_tag.provider_pool'), expr('container.hasParameter(\'sulu.preview\') ? parameter(\'sulu.preview\') : false'), service('url_helper')]);

    $services->alias(ContainerInterface::class, 'service_container');


    $services->alias(SlugifierInterface::class, 'sulu_document_manager.node_name_slugifier');
};
