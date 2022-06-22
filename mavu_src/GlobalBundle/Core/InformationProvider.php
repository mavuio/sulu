<?php

namespace Mavu\GlobalBundle\Core;


use Sulu\Component\Webspace\Webspace;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;


class InformationProvider
{

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    public function __construct(WebspaceManagerInterface $webspaceManager)
    {
        $this->webspaceManager = $webspaceManager;
    }

    /**
     * @return array<int, array{name: string, title: string}>
     */
    function getValues(string $webspaceKey, string $locale)
    {


        $webspace = $this->webspaceManager->findWebspaceByKey($webspaceKey);

        $contexts = [];
        foreach ($webspace->getNavigation()->getContexts() as $c) {

            $contexts[] = [
                'title' => $c->getTitle($locale),
                'name' => $c->getKey(),
            ];
        }
        return $contexts;
    }
}
