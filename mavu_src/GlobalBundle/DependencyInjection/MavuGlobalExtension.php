<?php


namespace Mavu\GlobalBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Mavu\GlobalBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;


class MavuGlobalExtension extends Extension implements PrependExtensionInterface
{




    public function prepend(ContainerBuilder $container): void
    {
        // if ($container->hasExtension('sulu_search')) {
        //     $container->prependExtensionConfig(
        //         'sulu_search',
        //         [
        //             'indexes' => [
        //                 'contact' => [
        //                     'name' => 'Dekor',
        //                     'icon' => 'su-paint',
        //                     'view' => [
        //                         'name' => DekorAdmin::EDIT_FORM_VIEW,
        //                         'result_to_view' => [
        //                             'id' => 'id',
        //                             'locale' => 'locale',
        //                         ],
        //                     ],
        //                     'security_context' => DekorAdmin::SECURITY_CONTEXT,
        //                 ],
        //             ],
        //         ]
        //     );
        // }

        // if ($container->hasExtension('sulu_route')) {
        //     $container->prependExtensionConfig(
        //         'sulu_route',
        //         [
        //             'mappings' => [
        //                 DekorPost::class => [
        //                     'generator' => 'schema',
        //                     'options' => ['route_schema' => '/dekor/{object.getTitle()}'],
        //                     'resource_key' => DekorPost::RESOURCE_KEY,
        //                 ],
        //             ],
        //         ]
        //     );
        // }


        if ($container->hasExtension('sulu_admin')) {
            $container->prependExtensionConfig(
                'sulu_admin',
                [
                    'lists' => [
                        'directories' => [
                            __DIR__ . '/../Resources/config/lists',
                        ],
                    ],
                    'forms' => [
                        'directories' => [
                            __DIR__ . '/../Resources/config/forms',
                        ],
                    ],
                    'resources' => [
                        'dekors' => [
                            'routes' => [
                                'list' => 'app.get_dekors',
                                'detail' => 'app.get_dekor',
                            ],
                        ],
                    ],
                    'field_type_options' => [
                        'selection' => [
                            'dekor_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => 'dekors',
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => 'dekor_chooser',
                                        'display_properties' => [
                                            0 => 'name',
                                        ],
                                        'icon' => 'su-paint',
                                        'label' => 'select style-presets',
                                        'overlay_title' => 'select Style-Preset',
                                    ],
                                ],
                            ],
                        ],
                        'single_selection' => [
                            'single_dekor_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => 'dekors',
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => 'dekor_chooser',
                                        'display_properties' => [
                                            0 => 'name',
                                        ],
                                        'icon' => 'su-paint',
                                        'empty_text' => 'no Style-Preset chosen',
                                        'overlay_title' => 'select Style-Preset',
                                    ],
                                    'auto_complete' => [
                                        'display_property' => 'name',
                                        'search_properties' => [
                                            0 => 'name',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],

                ]
            );
        }





        // $container->prependExtensionConfig(
        //     'sulu_dekors',
        //     ['templates' => ['view' => 'dekors/index.html.twig']]
        // );

        // $container->loadFromExtension('framework', [
        //     'default_locale' => 'en',
        //     'translator' => ['paths' => [__DIR__.'/../Resources/config/translations/']],
        //     // ...
        // ]);
    }



    /**
     * Undocumented function
     *
     * @param mixed[] $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {


        $loader = new PhpFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.php');
        $loader->load('controller.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $definition = $container->getDefinition("mavu.tw_classes_core");
        $definition->setArgument('$bundleConfig', $config);
    }
}
