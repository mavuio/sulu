<?php

namespace Mavu\GlobalBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\PropertyAccess\PropertyAccess;


class ComponentConfExtension extends AbstractExtension
{


    /**
     * @var PropertyAccess
     */
    private $accessor;


    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
    }



    public function getFunctions(): array
    {
        return [
            new TwigFunction('mavu_get_component_conf', [$this, 'getComponentConf']),
        ];
    }


    function getBaseconf($componentSlug)
    {
    }

    public function getComponentConf($componentSlug, $localConf = [])
    {
        return [
            "conf" => [
                "slug" => $componentSlug,
                "baseconf" => $this->getBaseconf($componentSlug),
                "localconf" => $localConf,
            ]
        ];
    }
}
