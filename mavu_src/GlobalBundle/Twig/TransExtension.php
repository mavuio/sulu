<?php

namespace Mavu\GlobalBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Sulu\Bundle\MediaBundle\Api\Media;


class TransExtension extends AbstractExtension
{



    public function getFunctions(): array
    {
        return [
            new TwigFunction('trans', [$this, 'trans']),
        ];
    }


    public function trans($lang,$de,$en)
    {
       
        if($lang=="de") {
            return $de;
        }

        return $en;

    }


}
