<?php


namespace App\Controller\Website;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sulu\Bundle\WebsiteBundle\Controller\DefaultController;

use Sulu\Component\Webspace\Analyzer\RequestAnalyzerInterface;
use Sulu\Bundle\WebsiteBundle\Navigation\NavigationMapperInterface;
use Sulu\Component\DocumentManager\Exception\DocumentNotFoundException;


class CustomController extends DefaultController
{
    /**
     * @var NavigationMapperInterface
     */
    private $navigationMapper;

    /**
     * @var RequestAnalyzerInterface
     */
    private $requestAnalyzer;

    /**
     * @var DocumentManager
     */
    private $documentManager;


    public function __construct(
        NavigationMapperInterface $navigationMapper,
        RequestAnalyzerInterface $requestAnalyzer = null,
        DocumentManager $documentManager,

    ) {
        $this->navigationMapper = $navigationMapper;
        $this->requestAnalyzer = $requestAnalyzer;
        $this->documentManager = $documentManager;
    }



    public function assetsAction()
    {

        $response = $this->render('esi/assets.html.twig', []);
        $response->setSharedMaxAge(10);
        return $response;
    }


    public function indexAction($structure, $preview = false, $partial = false)
    {

        $response = $this->renderStructure(
            $structure,
            [],
            $preview,
            $partial
        );
        return $response;
    }


    public function redirectAction($structure, $preview = false, $partial = false)
    {

        $locale = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $pageId = $structure->getUuid();

        $sub = $this->getSubnavForPage($pageId);
        $level = $this->getLevelForPage($structure);


        // redirect to first tab if page has subnav-tabs
        if (sizeof($sub) > 0 && $level == 1 && !$preview && !$partial) {
            $url = $sub[0]['url'];
            if ($locale == 'en') {
                $url = "/$locale" . $url;
            }
            return new RedirectResponse($url, 301, ['Cache-Control' => 'no-store']);
        }

        $response = $this->renderStructure(
            $structure,
            [],
            $preview,
            $partial
        );
        return $response;
    }


    protected function getAttributes($attributes, StructureInterface $structure = null, $preview = false)
    {
        $attributes = parent::getAttributes($attributes, $structure, $preview);
        $attributes['LocalHelpers'] = $this->container->get('Mavu\LocalBundle\Core\LocalHelpers');
        return $attributes;
    }

    public static function getSubscribedServices(): array
    {
        $subscribedServices = parent::getSubscribedServices();
        $subscribedServices['Mavu\LocalBundle\Core\LocalHelpers'] = 'Mavu\LocalBundle\Core\LocalHelpers';

        return $subscribedServices;
    }


    function getSubnavForPage(?string $pageId)
    {

        if ($pageId == null) {
            return [];
        }
        $document = $this->documentManager->find($pageId);
        $structure = $document->getStructure();


        $segment = $this->requestAnalyzer->getSegment();
        $webspaceKey = $this->requestAnalyzer->getWebspace()->getKey();
        $locale = $this->requestAnalyzer->getCurrentLocalization()->getLocale();


        $navigationContext = $structure->getProperty('redirectNavContext')->getValue();

        try {
            return $this->navigationMapper->getNavigation(
                $pageId,
                $webspaceKey,
                $locale,
                1, // $depth
                true,
                $navigationContext,
                false, //$loadExcerpt
            );
        } catch (DocumentNotFoundException $exception) {
            return [];
        }
    }

    public function getLevelForPage($structure)
    {
        $path = $structure->getDocument()->getResourceSegment();
        if ($path == "/") {
            return 0;
        }
        $ret = sizeof(explode("/", trim($path, "/")));
        return $ret;
    }
}
