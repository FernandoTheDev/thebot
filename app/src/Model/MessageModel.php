<?php

namespace Fernando\Model;

use Zanzara\Context;

final class MessageModel
{
    protected string $input;

    public function handler(Context $ctx): void
    {
        if ($ctx->getMessage()->getText() !== null) {
            $input = $ctx->getMessage()->getText();
        }

        if ($ctx->getMessage()->getCaption() !== null) {
            $input = $ctx->getMessage()->getCaption();
        }

        if ($input === null) {
            return;
        }

        $this->input = $input;
        $this->searchCommand($ctx);
    }

    private function searchCommand(Context $ctx): void
    {
        if (!$this->isCommand()) {
            return;
        }

        $class = "Fernando\\Commands\\" . ucfirst($this->getCommand()) . "Command";

        if (class_exists($class)) {
            $this->execute($class, $ctx);
            return;
        }
    }

    private function execute(string $class, Context $ctx): void
    {
        $dq = new DataQueryModel($this, $ctx);
        $instance = new $class($dq);
        $instance->handler($ctx);
    }

    public function getCommand(): string
    {
        return str_replace("/", "", explode(" ", $this->input)[0]);
    }

    public function getArgument(): string
    {
        return str_replace("/{$this->getCommand()}", "", $this->input);
    }

    private function isCommand(): bool
    {
        return $this->input[0] === "/";
    }
}
