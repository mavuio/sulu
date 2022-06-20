<?php
namespace Mavu\GlobalBundle\Admin;

use Mavu\GlobalBundle\Entity\Dekor;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\PageBundle\Admin\PageAdmin;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\PageBundle\Document\BasePageDocument;
use Sulu\Bundle\AdminBundle\Admin\View\ListItemAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;


class MavuSeoAdmin extends Admin
{
    /**
    * @var ViewBuilderFactoryInterface
    */
    private $viewBuilderFactory;

    /**
    * @var WebspaceManagerInterface
    */
    private $webspaceManager;

    /**
    * @var SecurityCheckerInterface
    */
    private $securityChecker;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        WebspaceManagerInterface $webspaceManager,
        SecurityCheckerInterface $securityChecker
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->webspaceManager = $webspaceManager;
        $this->securityChecker = $securityChecker;
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $formToolbarActionsWithoutType = [
            new ToolbarAction('sulu_admin.save_with_publishing'),
        ];

        $routerAttributesToFormRequest = ['parentId', 'webspace'];
        $routerAttributesToFormMetdata = ['webspace'];

        $previewCondition = 'nodeType == 1';

        if ($this->hasSomeWebspacePermission()) {
            $viewCollection->add(
                $this->viewBuilderFactory
                    ->createPreviewFormViewBuilder('sulu_page.page_edit_form.seo', '/mavu_seo') // overwrite default seo-tab
                    ->disablePreviewWebspaceChooser()
                    ->setResourceKey(BasePageDocument::RESOURCE_KEY)
                    ->setFormKey('page_mavu_seo')
                    ->setTabTitle('sulu_page.seo')
                    ->setTabCondition('nodeType == 1 && shadowOn == false')
                    ->addToolbarActions($formToolbarActionsWithoutType)
                    ->addRouterAttributesToFormRequest($routerAttributesToFormRequest)
                    ->setPreviewCondition($previewCondition)
                    ->setTitleVisible(true)
                    ->setTabOrder(2050)
                    ->setParent(PageAdmin::EDIT_FORM_VIEW)
            );

            // $viewCollection->add(
            //     $this->viewBuilderFactory
            //         ->createPreviewFormViewBuilder('sulu_page.page_edit_form.socials', '/socials')
            //         // ->disablePreviewWebspaceChooser()
            //         ->setResourceKey('pages')
            //         ->setFormKey('page_socials')
            //         ->setTabTitle('Socials')
            //         ->setTabPriority(256)
            //         ->addToolbarActions($formToolbarActionsWithoutType)
            //         ->addRouterAttributesToFormRequest($routerAttributesToFormRequest)
            //         ->setPreviewCondition($previewCondition)
            //         ->setTitleVisible(true)
            //         ->setTabOrder(1536)
            //         ->setParent(PageAdmin::EDIT_FORM_VIEW)
            // );
        }
    }

    private function hasSomeWebspacePermission(): bool
    {
        foreach ($this->webspaceManager->getWebspaceCollection()->getWebspaces() as $webspace) {
            $hasWebspacePermission = $this->securityChecker->hasPermission(
                PageAdmin::SECURITY_CONTEXT_PREFIX . $webspace->getKey(),
                PermissionTypes::EDIT
            );

            if ($hasWebspacePermission) {
                return true;
            }
        }

        return false;
    }
}
