<?php

namespace Fernando\Callbacks;

use Zanzara\Context;
use Fernando\Db\UserDb;
use Fernando\Model\MenuModel;
use Fernando\Model\DataQueryModel;

final class HistoricsCallback
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $user = new UserDb($ctx);
        $historyShopping = count((!$user->getHistoryShopping()) ? [] : $user->getHistoryShopping());
        $text = "*Escoha qual histórico deseja ver:*";

        $menu = new MenuModel();
        $menu->add("Compras ({$historyShopping})", "historicShopping", 0);
        $menu->add("Recargas (0)", "historicRecharges", 0);
        $menu->add("Voltar", "wallet", 0);

        $ctx->editMessageText($text, [
            "reply_markup" => $menu->menu(2)
        ]);
    }
}
