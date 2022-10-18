<?php

/*
 * This file is part of Sulu.
 *
 * (c) mavu.io 2022
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mavu\GlobalBundle\Exception;

use Sulu\Component\Rest\Exception\TranslationErrorMessageExceptionInterface;

/**
 * Exception is thrown when a Role is created or updated with an already existing name.
 */
class CustomizableException extends \Exception implements TranslationErrorMessageExceptionInterface
{
    /**
     * @var string
     */
    private $customMessage;


    public function __construct($customMessage, $additionalInfo = [], $previous = null)
    {
        $this->customMessage = $customMessage;
        parent::__construct(json_encode($additionalInfo, 1), 9001);
    }

    public function getMessageTranslationKey(): string
    {
        return 'mavu_global.customizable_error';
    }

    public function getMessageTranslationParameters(): array
    {
        return ['{customMessage}' => $this->customMessage];
    }
}
