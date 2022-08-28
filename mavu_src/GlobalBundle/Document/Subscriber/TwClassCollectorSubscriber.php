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
use PHPCR\NodeInterface;
use Psr\Log\LoggerInterface;
use Mavu\GlobalBundle\Core\TwClassesCore;
use Sulu\Component\DocumentManager\Events;
use Sulu\Component\DocumentManager\PropertyEncoder;
use Sulu\Component\DocumentManager\DocumentAccessor;
use Sulu\Bundle\PageBundle\Document\BasePageDocument;
use Sulu\Component\DocumentManager\Event\HydrateEvent;
use Sulu\Component\DocumentManager\Event\PersistEvent;
use Sulu\Component\DocumentManager\Event\PublishEvent;
use Sulu\Component\DocumentManager\Event\RestoreEvent;
use Sulu\Component\Content\Document\Behavior\BlameBehavior;
use Sulu\Bundle\DocumentManagerBundle\Bridge\DocumentInspector;


use Sulu\Component\Content\Document\Behavior\ExtensionBehavior;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Collects tailwind-classes for current document
 */
class TwClassCollectorSubscriber implements EventSubscriberInterface
{
    public const TW_CLASSES = 'tw_classes-classnames';
    /**
     * @var TwClassesCore
     */
    private $twClassesCore;


    /**
     * @var LoggerInterface
     */
    private $mavuDebugLogger;     // @phpstan-ignore-line


    /**
     * @var PropertyEncoder
     */
    private $propertyEncoder;

    public function __construct(
        PropertyEncoder $propertyEncoder,
        LoggerInterface $mavuDebugLogger,
        TwClassesCore $twClassesCore,
        string $bundleConfig = "",
    ) {
        $this->propertyEncoder = $propertyEncoder;
        $this->logger = $mavuDebugLogger ?: new NullLogger();
        $this->twClassesCore = $twClassesCore;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::PERSIST => 'setTwClassesOnNodeForPersist',
            Events::PUBLISH => 'setTwClassesOnNodeForPublish',

        ];
    }

    /**
     * Sets the twClasses and creator of the document.
     *
     * @param HydrateEvent $event
     * @return void
     */
    public function setTwClassesOnDocument(HydrateEvent $event)
    {
        //$document = $event->getDocument();


        // if (!$this->supports($document)) {
        //     return;
        // }

        // $node = $event->getNode();
        // $locale = $event->getLocale();
        // $encoding = $this->getPropertyEncoding($document);

        // $accessor = $event->getAccessor();

        // $accessor->set(
        //     static::TW_CLASSES,
        //     $node->getPropertyValueWithDefault(
        //         $this->propertyEncoder->encode($encoding, static::TW_CLASSES, $locale),
        //         null
        //     )
        // );

        // $accessor->set(
        //     static::CREATOR,
        //     $node->getPropertyValueWithDefault(
        //         $this->propertyEncoder->encode($encoding, static::CREATOR, $locale),
        //         null
        //     )
        // );
    }

    /**
     * Undocumented function
     *
     * @param PersistEvent $event
     * @return void
     */
    public function setTwClassesOnNodeForPersist(PersistEvent $event)
    {
        $document = $event->getDocument();


        if (!$this->supports($document) || $event->getLocale() == null) {
            return;
        }


        if (is_a($document, BasePageDocument::class)) {
            $collectedClassString = implode(" ", $this->twClassesCore->collectClassesFromDocument($document));


            $this->setTwClassesOnNode(
                $document,
                $event->getNode(),
                $event->getLocale(),
                $event->getAccessor(),
                $collectedClassString
            );
            $this->twClassesCore->writeClassFiles($collectedClassString);
        }
    }


    /**
     * Sets the creator and twClasses for the publish event.
     */
    public function setTwClassesOnNodeForPublish(PublishEvent $event)
    {
        $document = $event->getDocument();

        if (!$this->supports($document)) {
            return;
        }

        if ($document instanceof ExtensionBehavior) {
            $str = $document->getExtensionsData()['tw_classes']["classnames"] ?? '';
            $this->setTwClassesOnNode(
                $document,
                $event->getNode(),
                $event->getLocale(),
                $event->getAccessor(),
                $str
            );
        }
    }

    /**
     * Persists the data of twClasses to the Node.
     *
     * @param string $locale
     * @param string $classes
     */
    public function setTwClassesOnNode(
        $document,
        NodeInterface $node,
        $locale,
        DocumentAccessor $accessor,
        $classes
    ) {


        $encoding = $this->getPropertyEncoding($document);
        // $accessor->set(self::TW_CLASSES, "DUMMY CLASSES");
        $node->setProperty(
            $this->propertyEncoder->encode($encoding, static::TW_CLASSES, $locale),
            $classes
        );
    }

    /**
     * Sets the twClasses for the restore event.
     */
    public function setTwClassesForRestore(RestoreEvent $event)
    {
        // $document = $event->getDocument();
        // if (!$this->supports($event->getDocument())) {
        //     return;
        // }

        // $encoding = $this->getPropertyEncoding($document);

        // $event->getNode()->setProperty(
        //     $this->propertyEncoder->encode($encoding, self::TW_CLASSES, $event->getLocale()),
        //     $event->getOption('user')
        // );
    }

    /**
     * Returns the encoding kind for the given document.
     *
     * @param object $document
     *
     * @return string
     */
    private function getPropertyEncoding($document)
    {
        $encoding = 'system_localized';

        return $encoding;
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
        return true;
        // return $document instanceof LocalizedBlameBehavior;
    }
}
