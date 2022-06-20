<?php

declare(strict_types=1);

namespace Mavu\GlobalBundle\Content\Types;


use Sulu\Component\Content\SimpleContentType;

class MavuSvelteField extends SimpleContentType
{
    public function __construct()
    {
        parent::__construct('mavu_svelte_field');
    }
}
