<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mavu\GlobalBundle\Document\Subscriber;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Mavu\GlobalBundle\Core\TeaserCore;

use Sulu\Component\DocumentManager\Events;
use Sulu\Bundle\PageBundle\Document\BasePageDocument;
use Sulu\Component\DocumentManager\Event\PersistEvent;
use Sulu\Bundle\DocumentManagerBundle\Bridge\DocumentInspector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Generates and saves DefaultTeaser-Data.
 */
class TeaserDataSubscriber implements EventSubscriberInterface
{
    

        /**
     * @var TeaserCore
     */
    private $teaserCore;

    /**
     * @var LoggerInterface
     */
    private $logger;     // @phpstan-ignore-line

    /**
     * @var DocumentInspector
     */
    private $documentInspector;

  

    public function __construct(
        LoggerInterface $mavuDebugLogger,
        DocumentInspector $documentInspector,
        TeaserCore $teaserCore
    ) {
        $this->logger = $mavuDebugLogger ?: new NullLogger();
        $this->documentInspector = $documentInspector;
        $this->teaserCore = $teaserCore;
    }

    public static function getSubscribedEvents()
    {
        return [
            // Events::CREATE => 'onCreateNewsPost',
            Events::PERSIST => 'onPersistPage',
        ];
    }

    /*
    * Undocumented function
    *
    * @param PublishEvent $event
    * @return void
    */
    public function onPersistPage(PersistEvent $event)
    {

        
        $document = $event->getDocument();
        
       
        if (is_a($document, BasePageDocument::class)) {
            $defaultData=$this->teaserCore->getDefaultDataForTeaserFromDocument($document);

            // $this->setTwClassesOnNode(
            //     $document,
            //     $event->getNode(),
            //     $event->getLocale(),
            //     $event->getAccessor(),
            //     $str
            // );
            $this->teaserCore->saveDefaultDataForTeaserOnNode($defaultData,
                $event->getNode(),
                $event->getLocale(),
                $event->getAccessor(),            
            );

        }
        

return;
        

    }


    /**
     * Returns if the given document is supported by this subscriber.
     *
     * @param object $document
     *
     * @return bool
     */
    private function supports($document)
    {
        return $document && method_exists($document, 'getStructureType');
    }
}
