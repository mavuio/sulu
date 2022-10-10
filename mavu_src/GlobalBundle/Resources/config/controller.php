<?php

declare(strict_types=1);

use Mavu\GlobalBundle\Controller\Admin\DekorController;
use Mavu\GlobalBundle\Controller\Admin\JsonImportController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services()
        ->defaults()
        ->autoconfigure()
        ->autowire();

    $services->set('mavu_global_dekor.rest.controller', DekorController::class)
        ->public()
        ->tag('sulu.context', ['context' => 'admin'])
        ->args([service('mavu_global_dekor.list.praesentation'), service('doctrine.orm.default_entity_manager'), service('fos_rest.view_handler.default'), service('security.token_storage'), service('mavu.tw_classes_core'), service('mavu.dekor_core')]);

    $services->set(JsonImportController::class)
        ->public()
        ->tag('controller.service_arguments')
        ->tag('sulu.context', ['context' => 'admin'])
        ->args([service('mavu.json_import_core')]);
};
