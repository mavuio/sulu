<?php

namespace Mavu\GlobalBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Sulu\Bundle\MediaBundle\Api\Media;

class DecodeJsonExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('decode_json', [$this, 'decodeJson']),
        ];
    }


    public function decodeJson($str)
    {
        $data=@json_decode($str, 1);
        if (!is_array($data)) {
            $data=array();
        }
        return $data;
    }

}
