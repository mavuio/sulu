<?php

namespace Mavu\GlobalBundle\Core;

use Monolog\Level;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Mavu\GlobalBundle\Entity\Dekor;
use Doctrine\Persistence\ManagerRegistry;
use Mavu\GlobalBundle\Content\Select\BlockTypeSelect;


class DekorCore
{

    private $dekorRepository;

    /**
     * @var LoggerInterface
     */
    private $mavuDebugLogger;


    /**
     * @var BlockTypeSelect
     */
    private $blockTypeSelect;


    public function __construct(
        ManagerRegistry $doctrine,
        LoggerInterface $mavuDebugLogger,
        BlockTypeSelect $blockTypeSelect

    ) {

        $this->logger = $mavuDebugLogger ?: new NullLogger();
        $this->doctrine = $doctrine;
        $this->blockTypeSelect = $blockTypeSelect;
    }


    function checkDefaultStyles()
    {
        $contentTypes = $this->blockTypeSelect->getValues("en");

        $updatedStyleCount = 0;
        foreach ($contentTypes as $key => $ctype) {
            $updatedStyleCount += $this->checkDefaultStyleForContentType($ctype);
        }



        return $updatedStyleCount;
    }

    function checkDefaultStyleForContentType($ctype)
    {
        $slug = $this->getSlugForContentTypeName($ctype['name']);

        $dekor = $this->doctrine->getRepository(Dekor::class)->findOneBy(['slug' => $slug]);
        if ($dekor == null) {

            $this->createDefaultStyleForContentType($ctype);
            $this->logger->log(Level::Info, "style {$slug} not found, creating it now", ["cname" => $ctype['name']]);
            return 1;
        }

        return 0;
    }

    function createDefaultStyleForContentType($ctype)
    {
        $entityManager = $this->doctrine->getManager();

        $cname = $ctype['name'];
        $dekor = new Dekor();
        $dekor->setName("Default Style for '{$cname}'");
        $dekor->setSlug(self::getSlugForContentTypeName($cname));
        $dekor->setBlockType($cname);
        $dekor->setClasses($ctype['defaultClasses']);
        $dekor->setIgnoreDefaults(false);
        $entityManager->persist($dekor);
        $entityManager->flush();
    }


    static function getSlugForContentTypeName(string $cname)
    {
        $cname = str_replace('_', '-', $cname);
        return "{$cname}-default";
    }
}
