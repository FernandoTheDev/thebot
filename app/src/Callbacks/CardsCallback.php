<?php

namespace Fernando\Callbacks;

use Zanzara\Context;
use Fernando\Model\MenuModel;
use Fernando\Model\DataQueryModel;

final class CardsCallback
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $menu = new MenuModel();
        $menu->add("Unidade", "unity", 0);
        $menu->add("Mix", "mix", 0);
        $menu->add("Voltar", "start", 0);

        $ctx->editMessageText("Área de escolha:", [
            "reply_markup"=> $menu->menu(2)
        ]);
    }
}
