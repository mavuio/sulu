<?php


namespace Mavu\LocalBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Mavu\LocalBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;


class MavuLocalExtension extends Extension implements PrependExtensionInterface
{

    public function prepend(ContainerBuilder $container): void
    {
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
                ]
            );
        }
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
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // $definition1 = $container->getDefinition("mavu.dummy");
        // $definition1->setArgument('$bundleConfig', $config);

    }


    function error_level_tostring($intval, $separator = ',')
    {
        $errorlevels = array(
            E_ALL => 'E_ALL',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            E_DEPRECATED => 'E_DEPRECATED',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_STRICT => 'E_STRICT',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_NOTICE => 'E_NOTICE',
            E_PARSE => 'E_PARSE',
            E_WARNING => 'E_WARNING',
            E_ERROR => 'E_ERROR'
        );
        $result = '';
        foreach ($errorlevels as $number => $name) {
            if (($intval & $number) == $number) {
                $result .= ($result != '' ? $separator : '') . $name;
            }
        }
        return $result;
    }
}
