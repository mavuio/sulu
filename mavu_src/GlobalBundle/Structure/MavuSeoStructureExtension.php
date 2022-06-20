<?php

namespace Mavu\GlobalBundle\Structure;

use PHPCR\NodeInterface;
use Sulu\Component\Content\Extension\AbstractExtension;
use Sulu\Component\Content\Extension\ExportExtensionInterface;
use Symfony\Cmf\Api\Slugifier\SlugifierInterface;


class MavuSeoStructureExtension extends AbstractExtension implements ExportExtensionInterface
{
    /**
     * name of structure extension.
     */
    const MAVU_SEO_EXTENSION_NAME = 'mavu_seo';

    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    protected $properties = [
        'og_image',
        'fixed_path',
    ];

    protected $name = self::MAVU_SEO_EXTENSION_NAME;

    protected $additionalPrefix = 'mavu_seo';

    public function __construct(
        SlugifierInterface $slugifier,
    ) {
        $this->slugifier = $slugifier;
    }

    public function save(NodeInterface $node, $data, $webspaceKey, $languageCode)
    {
        $this->setLanguageCode($languageCode, 'i18n', null);

        $data = $this->encodeImages($data);
        $data = $this->slugifyPaths($data);

        $this->saveProperty($node, $data, 'og_image');
        $this->saveProperty($node, $data, 'fixed_path');
    }

    public function load(NodeInterface $node, $webspaceKey, $languageCode)
    {
        $ogImageNode = $this->loadProperty($node, 'og_image');
        $ogImage = null;
        if ($ogImageNode) {
            $ogImage = json_decode($ogImageNode, true);
        }

        return [
            'og_image' => $ogImage,
            'fixed_path' => $this->loadProperty($node, 'fixed_path'),
        ];
    }

    public function export($properties, $format = null)
    {
        $data = [];
        foreach ($properties as $key => $property) {
            $value = $property;
            if (\is_bool($value)) {
                $value = (int) $value;
            }

            $data[$key] = [
                'name' => $key,
                'value' => $value,
                'type' => '',
            ];
        }

        return $data;
    }

    public function import(NodeInterface $node, $data, $webspaceKey, $languageCode, $format)
    {
        $this->setLanguageCode($languageCode, 'i18n', null);

        $this->save($node, $data, $webspaceKey, $languageCode);
    }

    public function getImportPropertyNames()
    {
        return $this->properties;
    }

    private function encodeImages(array $data)
    {
        if ($data['og_image']) {
            $data['og_image'] = json_encode($data['og_image']);
        }

        return $data;
    }

    private function slugifyPaths(array $data)
    {
        if ($data['fixed_path']) {
            $data['fixed_path'] = $this->slugifier->slugify($data['fixed_path']);
        }

        return $data;
    }
}
