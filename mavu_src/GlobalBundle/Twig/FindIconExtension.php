<?php

namespace Mavu\GlobalBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Sulu\Bundle\MediaBundle\Api\Media;
use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Bundle\MediaBundle\Entity\MediaRepositoryInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;


class FindIconExtension extends AbstractExtension
{

    private $documentManager;
    /**
     * @var MediaRepositoryInterface
     */
    protected $mediaRepository;


    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;

    /**
     * @var CacheInterface
     */
    private $cache;



    const COLLECTION_ID = 10;

    public function __construct(
        MediaRepositoryInterface $mediaRepository,
        MediaManagerInterface $mediaManager,
        CacheInterface $cache,

    ) {
        $this->mediaRepository = $mediaRepository;
        $this->mediaManager = $mediaManager;
        $this->cache = $cache;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('find_icon', [$this, 'FindCachedIcon']),
        ];
    }


    public function FindIcon($filename, $collection_id = self::COLLECTION_ID)
    {


        $icon = $this->mediaRepository->findMediaWithFilenameInCollectionWithId($filename, $collection_id);
        if ($icon) {

            $media = $this->mediaManager->addFormatsAndUrl(new Media($icon, "de", null));

            return $media->getFormats()['original_web'];
        } else {
            return null;
        }
    }

    public function FindCachedIcon($filename, $collection_id = self::COLLECTION_ID)
    {
        return $this->cache->get('find_icon_' . $filename, function () use ($filename, $collection_id) {
            return $this->FindIcon($filename, $collection_id);
        });
    }
}
