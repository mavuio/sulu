<?php
/*
 * This file is part of the Sulu CMS.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\Content;

use Psr\Log\LoggerInterface;
use Sulu\Component\Content\StructureExtension\StructureExtensionInterface;
use Sulu\Component\Content\Template\Dumper\PHPTemplateDumper;
use Sulu\Component\Content\Template\Exception\InvalidXmlException;
use Sulu\Component\Content\Template\Exception\TemplateNotFoundException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

/**
 * generates subclasses of structure to match template definitions.
 * this classes will be cached in Symfony cache
 */
class StructureManager extends ContainerAware implements StructureManagerInterface
{
    /**
     * @var LoaderInterface XML Loader to load templates
     */
    private $loader;

    /**
     * @var Template\Dumper\PHPTemplateDumper
     */
    private $dumper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $options;

    /**
     * contains all extension
     * @var array
     */
    private $extensions = array();

    /**
     * @param LoaderInterface $loader XMLLoader to load xml templates
     * @param PHPTemplateDumper $dumper
     * @param LoggerInterface $logger
     * @param array $options
     * @internal param string $defaultPath array with paths to search for templates
     */
    function __construct(
        LoaderInterface $loader,
        PHPTemplateDumper $dumper,
        LoggerInterface $logger,
        $options = array()
    )
    {
        $this->loader = $loader;
        $this->dumper = $dumper;
        $this->logger = $logger;
        $this->setOptions($options);
    }

    /**
     * returns a structure for given key
     * @param $key string
     * @throws Template\Exception\TemplateNotFoundException
     * @return StructureInterface
     */
    public function getStructure($key)
    {
        return $this->getStructureByFile($key, $this->getTemplate($key));
    }

    /**
     * Sets the options for the manager
     * @param $options
     */
    public function setOptions($options)
    {
        $this->options = array(
            'template_dir' => array(),
            'cache_dir' => null,
            'debug' => false,
            'cache_class_suffix' => 'StructureCache',
            'base_class' => 'Sulu\Component\Content\Structure'
        );

        // overwrite the default values with the given options
        $this->options = array_merge($this->options, $options);

        // convert template dir to array
        if (!is_array($this->options['template_dir'])) {
            $this->options['template_dir'] = array($this->options['template_dir']);
        }
    }

    /**
     * @return StructureInterface[]
     */
    public function getStructures()
    {
        $result = array();
        foreach ($this->getTemplates() as $file) {
            $fileInfo = pathinfo($file['path']);
            $key = $fileInfo['filename'];

            try {
                $result[] = $this->getStructure($key, $file);
            } catch (TemplateNotFoundException $ex) {
                $this->logger->warning($ex->getMessage());
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(StructureExtensionInterface $extension, $template = 'all')
    {
        if (!isset($this->extensions[$template])) {
            $this->extensions[$template] = array();
        }

        $this->extensions[$template][] = $extension;
    }

    /**
     * returns structure for given template key and file
     * @param string $key
     * @param string $templateConfig
     * @return StructureInterface
     * @throws Template\Exception\TemplateNotFoundException
     */
    private function getStructureByFile($key, $templateConfig)
    {
        $fileName = $templateConfig['path'];

        $class = str_replace('-', '_', ucfirst($key)) . $this->options['cache_class_suffix'];
        $cache = new ConfigCache(
            $this->options['cache_dir'] . '/' . $class . '.php',
            $this->options['debug']
        );

        if (!$cache->isFresh()) {
            try {
                $result = $this->loader->load($fileName);
                $resources[] = new FileResource($fileName);
                $cache->write(
                    $this->dumper->dump(
                        $result,
                        array(
                            'cache_class' => $class,
                            'base_class' => $this->options['base_class']
                        )
                    ),
                    $resources
                );
            } catch (\InvalidArgumentException $iae) {
                $this->logger->warning(
                    'The file "' . $fileName . '" does not match the schema and was skipped'
                );
                throw new TemplateNotFoundException($fileName, $key);
            } catch (InvalidXmlException $iude) {
                $this->logger->warning(
                    'The file "' . $fileName . '" defined some invalid properties and was skipped'
                );
                throw new TemplateNotFoundException($fileName, $key);
            } catch (\Twig_Error $twige) {
                $this->logger->warning(
                    'The file "' . $fileName . '" content cant be rendered with the template'
                );
                throw new TemplateNotFoundException($fileName, $key);
            }
        }

        require_once $cache;

        /** @var StructureInterface $structure */
        $structure = new $class();
        $structure->setInternal($templateConfig['internal']);

        $extensions = isset($this->extensions['all']) ? $this->extensions['all'] : array();
        if (isset($this->extensions[$key])) {
            $extensions = array_merge($extensions, $this->extensions[$key]);
        }

        $structure->setExtensions($extensions);

        return $structure;
    }

    /**
     * returns path to template
     * @param $key
     * @return bool|string
     */
    private function getTemplate($key)
    {
        foreach ($this->options['template_dir'] as $templateDir) {
            $path = $templateDir['path'] . '/' . $key . '.xml';

            if (file_exists($path)) {
                return array(
                    'path' => $path,
                    'internal' => $templateDir['internal']
                );
            }
        }

        return false;
    }

    /**
     * returns a list of existing templates
     * @return string[]
     */
    private function getTemplates()
    {
        $result = array();
        foreach ($this->options['template_dir'] as $templateDir) {
            foreach (glob($templateDir['path'] . '/*.xml', GLOB_BRACE) as $path) {
                $result[] = array(
                    'path' => $path,
                    'internal' => $templateDir['internal']
                );
            }
        }

        return $result;
    }
}
