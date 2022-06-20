<?php

namespace Mavu\GlobalBundle\Twig;

use Mavu\GlobalBundle\Core\TwClassesCore;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Sulu\Bundle\MediaBundle\Api\Media;

class TwClassExtractorExtension extends AbstractExtension
{


    /**
    * @var TwClassesCore
    */
    private $classesCoreService;
    

    public function __construct(
        TwClassesCore $classesCoreService,
        ) {
            $this->classesCoreService = $classesCoreService;
        }
        

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('extract_classes', [$this, 'extractClasses']),
        ];
    }


    public function extractClasses(array $settings, $prefix='',$defaultCss='')
    {
        
        if (array_key_exists('classes', $settings) ) {
            $classStr=$this->classesCoreService->enrichWithStylePresetsForBlock("".$settings['classes'], $settings);
            $classes=$this->getClassesFromString($classStr);
        
            $matching_classes=array_filter($classes, function ($str) use ($prefix) {
                return $this->classHasPrefix($str, $prefix);
            });

            $ret= $prefix." ".$defaultCss." ".implode(" ", array_map(function ($str) {
                return preg_replace("/^c.\:/", "", $str);
            }, $matching_classes));
        } else {
            $ret= $defaultCss;
        }


        return $this->classesCoreService->customizeBreakpointPrefixes($ret, $settings);
    }

    function classHasPrefix($str,$prefix) {
        if($prefix=='') {
            return ! preg_match("/^c.\:/", $str);
        }else {
            return str_starts_with($str, $prefix.":");
        }
    }


    public function getClassesFromString(string $str)
    {


        return preg_split("/[\s]+/",$this->classesCoreService->removeComments($str));
    }

}
