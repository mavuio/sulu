<?php

namespace Mavu\LocalBundle\Core;

use Psr\Log\LoggerInterface;
use Mavu\LocalBundle\Core\ArtworksCore;
use Sulu\Bundle\MediaBundle\Api\Media;
use Mavu\LocalBundle\Core\ColorPalettesCore;
use Sulu\Component\DocumentManager\DocumentManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use Sulu\Bundle\MediaBundle\Entity\MediaRepositoryInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;


class LocalHelpers
{
    private $documentManager;
    /**
     * @var PropertyAccess
     */
    private $accessor;


    /**
     * @var MediaManagerInterface
     */
    protected $mediaManager;
    /**
     * @var MediaRepositoryInterface
     */
    protected $mediaRepository;




    public function __construct(
        DocumentManager $documentManager,
        PropertyAccessorInterface $propertyAccessor = null,
        MediaRepositoryInterface $mediaRepository,
        MediaManagerInterface $mediaManager,
    ) {
        $this->accessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        $this->documentManager = $documentManager;
        $this->mediaRepository = $mediaRepository;
        $this->mediaManager = $mediaManager;
    }

    public function test(): string
    {
        return "test worked";
    }



    function phpinfo()
    {
        phpinfo();
        die();
    }


    public function resolveImage($lang, $id, $formatKey = "original_web")
    {
        if ($id) {
            $mediaEntity = $this->mediaRepository->findMediaByIdForRendering($id, $formatKey);
            if ($mediaEntity) {

                $media = new Media($mediaEntity, $lang, null);
                $media = $this->mediaManager->addFormatsAndUrl($media);

                return ["url" => $media->getFormats()[$formatKey]];
            } else {
                return null;
            }
        }
    }
}
