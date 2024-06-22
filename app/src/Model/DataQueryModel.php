<?php

namespace Fernando\Model;

use Zanzara\Context;

final class DataQueryModel
{
    public function __construct(
        private object $message,
        private Context      $context
    ) {
    }

    public function getMessage(): MessageModel
    {
        return $this->message;
    }

    public function getCallback(): CallbackModel
    {
        return $this->message;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
