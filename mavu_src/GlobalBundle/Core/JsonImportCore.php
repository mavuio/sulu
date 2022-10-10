<?php

namespace Mavu\GlobalBundle\Core;

use RuntimeException;

use Psr\Log\NullLogger;

use OutOfBoundsException;

use PHPCR\SessionInterface;
use Psr\Log\LoggerInterface;
use Tarsana\Functional as F;
use Doctrine\ORM\AbstractQuery;
use Mavu\HhcBundle\Entity\Termin;
use Doctrine\ORM\EntityManagerInterface;
use Mavu\HhcBundle\Entity\Veranstaltung;
use Sulu\Bundle\MediaBundle\Entity\File;
use Doctrine\Persistence\ManagerRegistry;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Contracts\Cache\CacheInterface;

use Symfony\Component\Filesystem\Filesystem;
use Sulu\Bundle\MediaBundle\Entity\MediaType;
use Symfony\Component\HttpFoundation\Response;
use Sulu\Bundle\MediaBundle\Entity\FileVersion;
use Symfony\Component\Console\Input\ArrayInput;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\Content\Document\RedirectType;
use Symfony\Component\HttpKernel\KernelInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

use Sulu\Component\Content\Document\WorkflowStage;
use Sulu\Bundle\MediaBundle\Entity\FileVersionMeta;
use Sulu\Component\DocumentManager\DocumentManager;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\PropertyAccess\PropertyAccess;

use Sulu\Component\DocumentManager\Event\PublishEvent;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Sulu\Bundle\MediaBundle\Entity\CollectionInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sulu\Bundle\MarkupBundle\Markup\MarkupParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sulu\Bundle\DocumentManagerBundle\Bridge\DocumentInspector;
use Sulu\Bundle\MediaBundle\Entity\CollectionRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;
use Sulu\Component\DocumentManager\Exception\DocumentNotFoundException;
use Sulu\Bundle\MediaBundle\Media\Storage\StorageInterface;



class JsonImportCore
{


    final public const LOCALE_EN = 'en';
    final public const LOCALE_DE = 'de';


    /**
     * @var MarkupParserInterface
     */
    private $markupParser;

    /**
     * @var DocumentInspector
     */
    private $inspector;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var SessionInterface
     */
    private $liveSession;

    /**
     * @var LoggerInterface
     */
    private $mavuDebugLogger;

    private string $projectDir;

    /**
     * @var ContainerInterface
     */
    private $container;

    private $logger;

    /**
     * @var CacheInterface
     */
    private $cache;

    // /**
    // * @var ManagerRegistry
    // */
    // private $registry;

    private EntityManagerInterface $entityManager;



    /**
     * @var Filesystem
     */
    private $filesystem;

    private $documentManager;

    private $kernel;

    /**
     * @var CollectionRepositoryInterface
     */
    private $collectionRepository;


    public function __construct(
        CollectionRepositoryInterface $collectionRepository,
        StorageInterface $storage,
        EntityManagerInterface $entityManager,
        ContainerInterface $container,
        KernelInterface $kernel,
        CacheInterface $cache,
        Filesystem $filesystem,
        DocumentManager $documentManager,
        LoggerInterface $mavuDebugLogger,
        ManagerRegistry $registry,
        DocumentInspector $inspector,
        MarkupParserInterface $markupParser,
        string $projectDir
    ) {

        $this->kernel = $kernel;
        $this->entityManager = $entityManager;
        $this->storage = $storage;
        $this->markupParser = $markupParser;

        $this->filesystem = $filesystem;
        $this->documentManager = $documentManager;
        $this->inspector = $inspector;
        $this->collectionRepository = $collectionRepository;
        $this->container = $container;
        $this->cache = $cache;
        $this->logger = $mavuDebugLogger ?: new NullLogger();
        // $this->registry = $registry;
        $this->projectDir = $projectDir;
        $this->session = $this->container->get('sulu_document_manager.default_session');
        $this->liveSession = $this->container->get('sulu_document_manager.live_session');
        // $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
        //     ->disableExceptionOnInvalidPropertyPath()
        //     ->getPropertyAccessor();
    }

    public function importPage($data)
    {
        return $this->createPage($data["url"], $data);
    }

    private function createPage($relPath, $data): PageDocument
    {
        $locale = 'de';

        if (array_key_exists('debug', $data)) {
            unset($data['debug']);
        }

        $path = rtrim("/cmf/mainsite/contents{$relPath}", '/');
        $data['url'] = str_replace('/cmf/mainsite/contents', '', $path);

        // dump("create page {$path}");
        $extensionData = [
            'seo' => $data['seo'] ?? [],
            'excerpt' => $data['excerpt'] ?? [],
        ];

        unset($data['excerpt']);
        unset($data['seo']);

        /** @var PageDocument $pageDocument */
        $pageDocument = null;
        $this->logger->info('CREATE PAGE called');
        try {
            $pageDocument = $this->documentManager->find($path, $locale);
        } catch (DocumentManagerException | OutOfBoundsException $e) {
            $pageDocument = $this->documentManager->create('page');
            $this->createParentIfNeeded($path, $locale);
        }

        $pageDocument->setNavigationContexts($data['navigationContexts'] ?? ["main"]);
        $pageDocument->setLocale($locale);
        $pageDocument->setTitle($data['title']);
        $pageDocument->setResourceSegment($data['url']);
        $pageDocument->setStructureType('default');
        $pageDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $pageDocument->getStructure()->bind($data);
        $pageDocument->setAuthor(1);
        $pageDocument->setExtensionsData($extensionData);

        $this->documentManager->persist(
            $pageDocument,
            $locale,
            // ['path' => $path],
            ['parent_path' => dirname($path)]
        );
        $this->documentManager->publish($pageDocument, $locale);
        $this->documentManager->flush();


        return $pageDocument;
    }

    public function createParentIfNeeded($path, $locale)
    {

        $parent_path = dirname($path);
        try {
            $parentDocument = $this->documentManager->find($parent_path, $locale);
        } catch (DocumentManagerException | OutOfBoundsException $e) {
            echo " $parent_path not found, creating empty page for $parent_path";
            $parent_rel_path = str_replace('/cmf/mainsite/contents', '', $parent_path);
            $data = [
                "title" => basename($path),
            ];
            $this->createPage($parent_rel_path, $data);
        }
    }

    public function importMedia(
        $filename,
        $collection_str,
        $copyright,
        $description,
        $credits,
    ) {



        $file = new SplFileInfo($filename, "",   "");

        $collection = $this->findCollectionByName($collection_str);
        if (!$collection) {

            return "no collection $collection_str found";
        }
        $media = $this->createMedia(
            $collection,
            $file,
            $copyright,
            $description,
            $credits
        );
        if ($media) {
            return json_encode(["status" => "ok", "id" => $media->getId()]);
        }
    }

    public function findCollectionByName($name)
    {
        // $this->collectionRepository->getTree(self::LOCALE_EN, 0, 1000, "downloads", 0, [], false);

        foreach ($this->collectionRepository->findCollections() as $coll) {
            $collMeta = $coll->getMeta()[0];
            if ($collMeta->getTitle() == $name) {
                return $coll;
            }
        }

        return null;
    }

    private function createMedia(
        CollectionInterface $collection,
        SplFileInfo $fileInfo,
        $copyright,
        $description,
        $credits
    ): MediaInterface {
        $fileName = $fileInfo->getBasename();
        $title = $fileInfo->getFilename();
        $uploadedFile = new UploadedFile($fileInfo->getPathname(), $fileName);

        $storageOptions = $this->storage->save(
            $uploadedFile->getPathname(),
            $fileName,
        );

        $mediaTypeId = 2; //image
        if (strstr($fileInfo->getPathname(), "/downloads/")) {
            $mediaTypeId = 1; //document
        }


        $mediaType = $this->entityManager->getRepository(MediaType::class)->find($mediaTypeId);
        if (!$mediaType instanceof MediaType) {
            throw new RuntimeException('MediaType "2" not found. Have you loaded the Sulu fixtures?');
        }

        $media = new Media();

        $file = new File();
        $file->setVersion(1)
            ->setMedia($media);

        $media->addFile($file)
            ->setType($mediaType)
            ->setCollection($collection);


        $mimeType = $uploadedFile->getMimeType() ?: 'image/jpeg';

        if (!$mimeType) {
            throw "no mime type for {$fileInfo->getPathname()}";
        }

        $fileVersion = new FileVersion();
        $fileVersion->setVersion($file->getVersion())
            ->setSize($uploadedFile->getSize())
            ->setName($fileName)
            ->setStorageOptions($storageOptions)
            ->setMimeType($mimeType)
            ->setFile($file);

        $file->addFileVersion($fileVersion);

        $fileVersionMeta = new FileVersionMeta();
        $fileVersionMeta->setTitle($title)
            ->setDescription($description)
            ->setCopyright($copyright)
            ->setCredits($credits)
            ->setLocale(self::LOCALE_DE)
            ->setFileVersion($fileVersion);

        $fileVersion->addMeta($fileVersionMeta)
            ->setDefaultMeta($fileVersionMeta);

        $this->entityManager->persist($fileVersionMeta);
        $this->entityManager->persist($fileVersion);
        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }
}
