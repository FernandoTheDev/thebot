<?php

namespace Fernando\Callbacks;

use Zanzara\Context;
use Fernando\Model\MenuModel;
use Fernando\Model\DataQueryModel;

final class StartCallback
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        // Mensagem de boas vindas.
        $name = $ctx->getEffectiveUser()->getFirstName();
        $text = "Bem vindo *{$name}*";

        $menu = new MenuModel();
        $menu->add("Cartões", "cards", 0);
        $menu->add("Carteira", "wallet", 0);
        $menu->add("Recarregar", "recharge", 0);
        $menu->add("Ranking", "ranking", 0);
        $menu->add("Suporte", $_ENV["LINK_SUPORTE"], 1);

        $ctx->editMessageText($text, [
            "reply_markup"=> $menu->menu(2)
        ]);
    }
}
