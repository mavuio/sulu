<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\SearchBundle\Tests\Unit\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Massive\Bundle\SearchBundle\Search\Metadata\ProviderInterface;
use Massive\Bundle\SearchBundle\Search\SearchManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Sulu\Bundle\SearchBundle\Controller\SearchController;
use Sulu\Bundle\SearchBundle\Search\Configuration\IndexConfiguration;
use Sulu\Bundle\SearchBundle\Search\Configuration\IndexConfigurationProviderInterface;
use Sulu\Bundle\SearchBundle\Search\Configuration\Route;
use Sulu\Component\Rest\ListBuilder\ListRestHelperInterface;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class SearchControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var SearchManagerInterface
     */
    private $searchManager;

    /**
     * @var ProviderInterface
     */
    private $metadataProvider;

    /**
     * @var SecurityCheckerInterface
     */
    private $securityChecker;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var ListRestHelperInterface
     */
    private $listRestHelper;

    /**
     * @var IndexConfigurationProviderInterface
     */
    private $indexConfigurationProvider;

    /**
     * @var SearchController
     */
    private $searchController;

    public function setUp(): void
    {
        $this->searchManager = $this->prophesize(SearchManagerInterface::class);
        $this->metadataProvider = $this->prophesize(ProviderInterface::class);
        $this->securityChecker = $this->prophesize(SecurityCheckerInterface::class);
        $this->viewHandler = $this->prophesize(ViewHandlerInterface::class);
        $this->listRestHelper = $this->prophesize(ListRestHelperInterface::class);
        $this->indexConfigurationProvider = $this->prophesize(IndexConfigurationProviderInterface::class);

        $this->searchController = new SearchController(
            $this->searchManager->reveal(),
            $this->metadataProvider->reveal(),
            $this->securityChecker->reveal(),
            $this->viewHandler->reveal(),
            $this->listRestHelper->reveal(),
            $this->indexConfigurationProvider->reveal()
        );
    }

    public function testIndexesAction()
    {
        $this->searchManager->getIndexNames()->willReturn(['index1', 'index2']);

        $indexConfiguration1 = new IndexConfiguration('index1', 'su-test', 'index 1', new Route('test1', []));
        $indexConfiguration2 = new IndexConfiguration('index2', 'su-test', 'index 2', new Route('test2', []));

        $this->indexConfigurationProvider->getIndexConfigurations()->willReturn(
            [$indexConfiguration1, $indexConfiguration2]
        );
        $this->indexConfigurationProvider->getIndexConfiguration('index2')->willReturn($indexConfiguration2);

        $this->securityChecker->hasPermission(Argument::cetera())->willReturn(true);

        $view = View::create(['_embedded' => ['search_indexes' => [$indexConfiguration1, $indexConfiguration2]]]);
        $this->viewHandler->handle($view)->shouldBeCalled();

        $this->searchController->indexesAction();
    }

    public function testIndexesActionWithSecurity()
    {
        $this->searchManager->getIndexNames()->willReturn(['index1', 'index2']);

        $indexConfiguration1 = new IndexConfiguration(
            'index1',
            'su-test',
            'index 1',
            new Route('test1', []),
            'security-context-1'
        );
        $indexConfiguration2 = new IndexConfiguration(
            'index2',
            'su-test',
            'index 2',
            new Route('test2', []),
            'security-context-2'
        );

        $this->indexConfigurationProvider->getIndexConfigurations()->willReturn(
            [$indexConfiguration1, $indexConfiguration2]
        );

        $this->securityChecker->hasPermission('security-context-1', PermissionTypes::VIEW)->willReturn(true);
        $this->securityChecker->hasPermission('security-context-2', PermissionTypes::VIEW)->willReturn(false);

        $view = View::create(['_embedded' => ['search_indexes' => [$indexConfiguration1]]]);
        $this->viewHandler->handle($view)->shouldBeCalled();

        $this->searchController->indexesAction();
    }

    public function testIndexesActionWithContexts()
    {
        $this->searchManager->getIndexNames()->willReturn(['index1', 'index2']);

        $indexConfiguration1 = new IndexConfiguration(
            'index1',
            'index 1',
            'su-test',
            new Route('test1', []),
            'security-context-1',
            ['website']
        );
        $indexConfiguration2 = new IndexConfiguration(
            'index2',
            'index 2',
            'su-icon',
            new Route('test2', []),
            'security-context-2',
            ['admin']
        );

        $this->indexConfigurationProvider->getIndexConfigurations()->willReturn(
            [$indexConfiguration1, $indexConfiguration2]
        );

        $this->securityChecker->hasPermission(Argument::cetera())->willReturn(true);

        $view = View::create(['_embedded' => ['search_indexes' => [$indexConfiguration2]]]);
        $this->viewHandler->handle($view)->shouldBeCalled();

        $this->searchController->indexesAction();
    }
}
