<?php

namespace Mavu\GlobalBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Sulu\Bundle\MediaBundle\Api\Media;

class MergedMediaMetaExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('merge_meta', [$this, 'mergeMeta']),
        ];
    }


    public function mergeMeta(Media $media)
    {
        $metas = [];
        $translations = $this->getTranslations($media);
        foreach (['title', 'copyright', 'credits', 'description'] as $field) {
            $val = $media->{'get' . ucwords($field)}();
            if (empty($val)) {
                $val = $this->getFallbackValue($field, $translations);
            }
            $metas[$field] = trim($val);
        }

        $metas['fototext'] = $this->getFotoText($metas);
        return $metas;
    }


    public function getFotoText(array $metas)
    {
        $parts = [];
        $lang = "de";
        if ($metas['copyright'] && !strstr($metas['copyright'], ':')) {
            $parts[] = $lang == "de" ? "Foto: " : "Image: ";
        }

        if ($metas['copyright']) {
            $parts[] = $metas['copyright'];
        }
        if ($metas['credits'] && $metas['copyright']) {
            $parts[] = " / ";
        }

        if ($metas['credits']) {
            $parts[] = $metas['credits'];
        }



        return implode("", $parts);
    }

    public function getFallbackValue(string $fieldName, array $translations)
    {
        foreach ($translations as $locale => $fields) {
            if (!empty($fields[$fieldName])) {
                return $fields[$fieldName];
            }
        }
        return null;
    }

    public function getTranslations(Media $media)
    {
        $metas = [];
        foreach ($media->getFileVersion()->getMeta() as $meta) {
            if ($meta->getLocale() != $media->getLocale()) {
                $metas[$meta->getLocale()] = $this->getFieldsFromObject($meta);
            }
        }
        return $metas;
    }

    public function getFieldsFromObject($object)
    {
        $fields = [];
        foreach (['title', 'copyright', 'credits', 'description'] as $field) {
            $val = $object->{'get' . ucwords($field)}();
            $fields[$field] = $val;
        }
        return $fields;
    }
}
