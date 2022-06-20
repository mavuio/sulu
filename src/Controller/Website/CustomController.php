<?php


namespace App\Controller\Website;

use Sulu\Bundle\WebsiteBundle\Controller\DefaultController;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sulu\Bundle\WebsiteBundle\Navigation\NavigationMapperInterface;
use Sulu\Component\DocumentManager\Exception\DocumentNotFoundException;
use Sulu\Component\Webspace\Analyzer\RequestAnalyzerInterface;
use Sulu\Component\DocumentManager\DocumentManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


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

        $locale = $this->get('request_stack')->getCurrentRequest()->getLocale();
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

    /**
     * Handle Login page.
     */
    public function loginAction(Request $request): Response
    {

        dd("Login controller");
        // $document = $this->documentManager->find("6532f5a6-3e6a-46d2-bd86-523bebebf958");
        // $structure= $document->getStructure();

        // return $this->indexAction($structure);
        //         $authenticationUtils = $this->getAuthenticationUtils();

        //         // get the login error if there is one
        //         $error = $authenticationUtils->getLastAuthenticationError();


        //         // last username entered by the user
        //         $lastUsername = $authenticationUtils->getLastUsername();

        // // dd([$error, $lastUsername]);

        //         return $this->renderTemplate(Configuration::TYPE_LOGIN, [
        //             'last_username' => $lastUsername,
        //             'error' => $error,
        //         ]);
    }

    protected function getAttributes($attributes, StructureInterface $structure = null, $preview = false)
    {
        $attributes = parent::getAttributes($attributes, $structure, $preview);
        $attributes['LocalHelpers'] = $this->get('Mavu\LocalBundle\Core\LocalHelpers');
        return $attributes;
    }

    public static function getSubscribedServices()
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
        $segment = $this->requestAnalyzer->getSegment();
        $webspaceKey = $this->requestAnalyzer->getWebspace()->getKey();
        $locale = $this->requestAnalyzer->getCurrentLocalization()->getLocale();


        try {
            return $this->navigationMapper->getNavigation(
                $pageId,
                $webspaceKey,
                $locale,
                1, // $depth
                true,
                "main", //$context,
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
