<?php
namespace Mavu\GlobalBundle\Structure;

use PHPCR\NodeInterface;
use Sulu\Component\Content\Extension\AbstractExtension;
use Sulu\Component\Content\Extension\ExportExtensionInterface;

class TwClassesStructureExtension extends AbstractExtension implements ExportExtensionInterface
{
    /**
    * name of structure extension.
    */
    const TW_CLASSES_EXTENSION_NAME = 'tw_classes';

    protected $properties = [
        'classnames',
    ];

    protected $name = self::TW_CLASSES_EXTENSION_NAME;

    protected $additionalPrefix = 'tw_classes';

    /**
     * Undocumented function
     *
     * @param NodeInterface<int,mixed> $node
     * @param mixed[] $data
     * @param string $webspaceKey
     * @param string $languageCode
     * @return void
     */
    public function save(NodeInterface $node, $data, $webspaceKey, $languageCode)
    {
        $this->setLanguageCode($languageCode, 'i18n', "");

        $this->saveProperty($node, $data, 'classnames');
    }

    /**
     * Undocumented function
     *
     * @param NodeInterface<int,mixed> $node
     * @param string $webspaceKey
     * @param string $languageCode
     * @return array<string,mixed>
     */
    public function load(NodeInterface $node, $webspaceKey, $languageCode)
    {
        return [
            'classnames' => $this->loadProperty($node, 'classnames'),
        ];
    }

    /**
     * Undocumented function
     *
     * @param array<string,mixed> $properties
     * @param mixed $format
     * @return array<string,mixed>
     */
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
    /**
     * Undocumented function
     *
     * @param NodeInterface<int,mixed> $node
     * @param array<string,mixed> $data
     * @param string $webspaceKey
     * @param string $languageCode
     * @param mixed $format
     * @return void
     */
    public function import(NodeInterface $node, $data, $webspaceKey, $languageCode, $format)
    {
        $this->setLanguageCode($languageCode, 'i18n', "");

        $this->save($node, $data, $webspaceKey, $languageCode);
    }

    public function getImportPropertyNames()
    {
        return $this->properties;
    }
}
