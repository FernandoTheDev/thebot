<?php

namespace Fernando\Model;

final class MenuModel
{
    public function __construct(
        protected array $buttons = []
    ) {
    }

    public function add(string $text, string $data, bool $type = false): void
    {
        $dataMenu = [
            "text" => $text,
            "callback_data" => $data
        ];

        if ($type) {
            $dataMenu["url"] = $data;
            unset($dataMenu["callback_data"]);
        }

        $this->buttons[] = $dataMenu;
    }

    public function menu(int $chunk = 1): array
    {
        $menu["inline_keyboard"] = array_chunk($this->buttons, $chunk);
        return $menu;
    }
}
