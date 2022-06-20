<?php

declare(strict_types=1);

namespace Mavu\GlobalBundle\Admin;

use Mavu\GlobalBundle\Entity\Dekor;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ListItemAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;

class DekorAdmin extends Admin
{
    public const LIST_VIEW = 'app.dekor.list';
    public const ADD_FORM_VIEW = 'app.dekor.add_form';
    public const ADD_FORM_DETAILS_VIEW = 'app.dekor.add_form.details';
    public const EDIT_FORM_VIEW = 'app.dekor.edit_form';
    public const EDIT_FORM_DETAILS_VIEW = 'app.dekor.edit_form.details';
    public const SECURITY_CONTEXT = 'sulu.dekor';

    private ViewBuilderFactoryInterface $viewBuilderFactory;
    private SecurityCheckerInterface $securityChecker;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        SecurityCheckerInterface $securityChecker
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->securityChecker = $securityChecker;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(DekorAdmin::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $rootNavigationItem = new NavigationItem('Style-Presets');
            $rootNavigationItem->setIcon('su-paint');
            $rootNavigationItem->setPosition(30);
            $rootNavigationItem->setView(static::LIST_VIEW);

            $navigationItemCollection->add($rootNavigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $formToolbarActions = [];
        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(DekorAdmin::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.add');
        }

        if ($this->securityChecker->hasPermission(DekorAdmin::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
        }

        if ($this->securityChecker->hasPermission(DekorAdmin::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.delete');
            $listToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        if ($this->securityChecker->hasPermission(DekorAdmin::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.export');
        }

        if ($this->securityChecker->hasPermission(DekorAdmin::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, '/dekors')
                    ->setResourceKey(Dekor::RESOURCE_KEY)
                    ->setListKey(Dekor::LIST_KEY)
                    ->setTitle('Styles')
                    ->addListAdapters(['table'])
                    ->setAddView(static::ADD_FORM_VIEW)
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($listToolbarActions)
                    ->addItemActions([
                        new ListItemAction('mavu_global.duplicate'),
                    ])
            );



            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, '/dekors/add')
                    ->setResourceKey(Dekor::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Dekor::RESOURCE_KEY)
                    ->setFormKey(Dekor::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::ADD_FORM_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/dekors/:id')
                    ->setResourceKey(Dekor::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Dekor::RESOURCE_KEY)
                    ->setFormKey(Dekor::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::EDIT_FORM_VIEW)
            );
        }
    }


    /**
     * @return mixed[]
     */
    public function getSecurityContexts(): array
    {
        return [
            'Sulu' => [
                'Dekors' => [
                    DekorAdmin::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
