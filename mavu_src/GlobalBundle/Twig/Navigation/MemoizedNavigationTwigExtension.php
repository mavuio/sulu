<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mavu\GlobalBundle\Twig\Navigation;

use Twig\Extension\AbstractExtension;
use Sulu\Component\Cache\MemoizeInterface;
use Sulu\Component\Cache\MemoizeTwigExtensionTrait;
use Sulu\Bundle\WebsiteBundle\Twig\Navigation\NavigationTwigExtensionInterface;

/**
 * Provides memoized navigation functions.
 */
class MemoizedNavigationTwigExtension extends AbstractExtension
{
    use MemoizeTwigExtensionTrait;

    /**
     * @param int $lifeTime
     */
    public function __construct(NavigationTwigExtensionInterface $extension, MemoizeInterface $memoizeCache, $lifeTime)
    {
        $this->extension = $extension;
        $this->memoizeCache = $memoizeCache;
        $this->lifeTime = $lifeTime;
    }
}
