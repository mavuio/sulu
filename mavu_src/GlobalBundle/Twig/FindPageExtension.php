<?php

namespace Mavu\GlobalBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Sulu\Bundle\MediaBundle\Api\Media;
use Sulu\Component\DocumentManager\DocumentManager;


class FindPageExtension extends AbstractExtension
{

    private $documentManager;


    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('find_page', [$this, 'FindPage']),
        ];
    }


    public function FindPage($path)
    {
        try {
            return  $this->documentManager->find($path);
        } catch (\Exception $e) {
           return null;
        }

    }

}
