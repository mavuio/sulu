<?php

namespace Mavu\GlobalBundle\Core;

use PHPCR\SessionInterface;
use Sulu\Bundle\DocumentManagerBundle\Bridge\DocumentInspector;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\DocumentManager\NodeManager;
use Sulu\Component\DocumentManager\PropertyEncoder;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tarsana\Functional as F;

class TeaserCore
{
    /**
     * @var NodeManager
     */
    private $nodeManager;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var SessionInterface
     */
    private $liveSession;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var PropertyEncoder
     */
    private $propertyEncoder;
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @var DocumentInspector
     */
    private $documentInspector;

    public function __construct(
        DocumentManagerInterface $documentManager,
        DocumentInspector $documentInspector,
        PropertyEncoder $propertyEncoder,
        NodeManager $nodeManager,
        SessionInterface $session,
        SessionInterface $liveSession
    ) {
        $this->propertyEncoder = $propertyEncoder;
        $this->documentManager = $documentManager;
        $this->documentInspector = $documentInspector;
        $this->nodeManager = $nodeManager;
        $this->session = $session;
        $this->liveSession = $liveSession;

        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
    }

    public function getDefaultDataForTeaserFromDocument($document)
    {
        if ($document->getStructure()->hasProperty('blocks')) {
            $blocks = $document->getStructure()->getProperty('blocks')->getValue();

            $bag = [];
            $this->findPicturesAndTextInBlocks($blocks, $bag);

            $main_picture_id = -1;
            $short_text = -1;
            $firstimageId = null;

            if ($bag['pictures'] ?? null) {
                $firstimageId = array_shift($bag['pictures']);

                if ($firstimageId > 1) {
                    $main_picture_id = $firstimageId;
                }
            }
            $firstShortText = null;

            $short_text = 'n/a';
            if ($bag['texts'] ?? null) {
                $firstShortText = array_shift($bag['texts']);

                if (strlen($firstShortText) > 2) {
                    $short_text = $firstShortText;
                }
            }

            return
                [
                    'short_text' => $firstShortText,
                    'main_image_id' => $firstimageId,
                ];

            // die("\n\n<pre>mwuits-debug 2023-05-13_19:30 " . print_r([$main_picture_id, $short_text], true));
        }

        return [];
    }

    public function saveDefaultDataForTeaserOnNode($defaultData, $node, $locale, $accessor)
    {
        // i18n:de-mavu_teaserdata-image
        // i18n:de-auto_short_text
        // $accessor->set(self::TW_CLASSES, "DUMMY CLASSES");
        $node->setProperty(
            $this->propertyEncoder->encode('system_localized', 'mavu_teaserdata-auto_text', $locale),
            $defaultData['short_text'] ?? ''
        );
        $node->setProperty(
            $this->propertyEncoder->encode('system_localized', 'mavu_teaserdata-auto_image', $locale),
            $defaultData['main_image_id'] ? "{\"displayOption\":null,\"id\":{$defaultData['main_image_id']}}" : null
        );
    }

    // public function findAndSetMainPictureAndShortText()
    // {
    //     $bag=array();

    //     $this->findPicturesAndTextInContentElements($this->C4P->getElementsForPlace("MainContent"), $bag);

    //     $this->MainPictureID=-1;

    //     if ($bag['pictures']) {
    //         $firstimageId=array_shift(array_keys($bag['pictures']));

    //         if ($firstimageId>1) {
    //             $this->MainPictureID=$firstimageId;
    //         }
    //     }
    //     $this->ShortText="n/a";
    //     if ($bag['texts']) {
    //         $firstShortText=array_shift($bag['texts']);

    //         if (strlen($firstShortText)>2) {
    //             $this->ShortText=$firstShortText;
    //         }
    //     }
    // }

    public function findPicturesAndTextInBlocks($blocks, &$bag, $p = [])
    {
        $acc = $this->accessor;

        $textLimit = 140;
        if ($p['textLimit'] ?? null) {
            $textLimit = $p['textLimit'];
        }

        foreach ($blocks ?? [] as $block) {
            if ($img_id = $acc->getValue($block, '[image?][id]')) {
                if (!$acc->getValue($bag, "[pictures][{$img_id}]")) {
                    $bag['pictures'][] = $img_id;
                }
            }

            if ($text = $acc->getValue($block, '[richtext]')) {
                if ($p['keepOriginalTexts'] ?? null) {
                    $bag['texts'][] = $block->Text;
                } else {
                    $bag['texts'][] = $this->ShortenText($text, $textLimit);
                }
            }

            if ($children = $acc->getValue($block, '[leftcol]')) {
                $this->findPicturesAndTextInBlocks($children, $bag, $p);
            }

            if ($children = $acc->getValue($block, '[middlecol]')) {
                $this->findPicturesAndTextInBlocks($children, $bag, $p);
            }

            if ($children = $acc->getValue($block, '[rightcol]')) {
                $this->findPicturesAndTextInBlocks($children, $bag, $p);
            }
        }
    }

    public function ShortenText($text, $length = 60)
    {
        $text = strip_tags($text);

        // no need to trim, already shorter than trim length
        if (mb_strlen($text) > $length - 4) {
            // find last space within length
            $text2check = mb_substr($text, 0, $length);
            $max_last_space = 0;
            foreach ([' ', '/', '-'] as $char) {
                $last_space = mb_strrpos($text2check, $char);
                if ($last_space && $last_space > $max_last_space) {
                    $max_last_space = $last_space;
                }
            }
            if (!$max_last_space) {
                $max_last_space = $length - 1;
            }
            $text = mb_substr($text, 0, $max_last_space + 1).'...';
        }

        return $text;
    }

    /**
     * @param mixed $blocks
     *
     * @return mixed[]
     */
    public function flatten_blocks($blocks)
    {
        if (is_array($blocks)) {
            return F\Stream::of($blocks)
                ->chain(function ($block) {
                    if ($this->isAssoc($block)) {
                        $ret = F\reduce(function ($acc, $pair) {
                            [$key, $val] = $pair;

                            if (is_array($val) and false == $this->isAssoc($val) && 'style_presets' != $key) {
                                foreach ($this->flatten_blocks($val) as $item) {
                                    $acc[] = $item;
                                }

                                return $acc;
                            } elseif (is_array($val) and true == $this->isAssoc($val) && 'style_presets' != $key) {
                                foreach ($this->flatten_blocks([$val]) as $item) {
                                    $acc[] = $item;
                                }

                                return $acc;
                            } else {
                                $acc[0][$key] = $val;

                                return $acc;
                            }
                        }, [['init' => '88']], F\toPairs($block));

                        // $this->logger->info(" ctcyan flatten_blocks", ["ret"=>$ret]);

                        return $ret;
                    } else {
                        return [];
                    }
                })
                ->result();
        } else {
            return [];
        }
    }

    /**
     * is associative array?
     *
     * @param mixed $arr
     *
     * @return bool
     */
    public function isAssoc($arr)
    {
        if (!is_array($arr)) {
            return false;
        }
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function excerptToTeaserdataInFolder($uuid, $locale)
    {
        $document = $this->documentManager->find($uuid, $locale);

        if (null === $document) {
            return;
        }

        foreach ($this->documentInspector->getChildren($document) as $doc) {
            $this->upgradePage($doc);
        }
    }

    public function upgradePage($pageDocument)
    {
        $acc = $this->accessor;

        $extensionData = $pageDocument->getExtensionsData()->toArray();

        $title = $acc->getValue($extensionData, '[excerpt][title]');
        $description = $acc->getValue($extensionData, '[excerpt][description]');
        $image_id = $acc->getValue($extensionData, '[excerpt][images][ids?][0?]');

        if ($title) {
            $acc->setValue($extensionData, '[mavu_teaserdata][title]', $title);
        }
        if ($description) {
            $acc->setValue($extensionData, '[mavu_teaserdata][text]', $description);
        }

        if ($image_id) {
            $acc->setValue($extensionData, '[mavu_teaserdata][image]', [
                'id' => $image_id,
                'displayOption' => null,
            ]
            );
        }

        $pageDocument->setExtensionsData($extensionData);

        $ret = $this->documentManager->persist(
            $pageDocument,
            $pageDocument->getLocale(),
        );

        $this->documentManager->publish(
            $pageDocument,
            $pageDocument->getLocale()
        );
        $this->documentManager->flush();
        echo "\n✔ $title";
    }
}
