<?php

namespace Fernando\Model;

use Zanzara\Context;

final class CallbackModel
{
    protected string $input;

    public function handler(Context $ctx): void
    {
        $input = $ctx->getCallbackQuery()->getData();

        if ($input === null) {
            return;
        }

        $this->input = $input;
        $this->searchCallback($ctx);
    }

    private function searchCallback(Context $ctx): void
    {
        $class = "Fernando\\Callbacks\\" . ucfirst($this->getCallbackData()) . "Callback";

        if (class_exists($class)) {
            $this->execute($class, $ctx);
            return;
        }

        $ctx->answerCallbackQuery([
            "text" => "Em desenvolvimento!",
            "show_alert"=> true
        ]);
    }

    private function execute(string $class, Context $ctx): void
    {
        $dq = new DataQueryModel($this, $ctx);
        $instance = new $class($dq);
        $instance->handler($ctx);
    }

    public function getCallbackData(): string
    {
        return explode(" ", $this->input)[0];
    }

    public function getArgument(): string
    {
        return str_replace($this->getCallbackData() . " ", "", $this->input);
    }
}
