<?php

namespace Fernando\Callbacks;

use Fernando\Db\CardsDb;
use Zanzara\Context;
use Fernando\Model\MenuModel;
use Fernando\Model\DataQueryModel;

final class SelectTypeCallback
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $arg = $this->dataQueryModel->getCallback()->getArgument();
        $cards = CardsDb::getCardLevelValids($arg);

        if (count($cards) < 1) {
            $this->warning($ctx);
            return;
        }

        $text = "💳 *Escolha o tipo do cartão*

*•* Nível selecionado: *{$arg}*";

        $menu = new MenuModel();
        $types = array_values(array_unique(array_column($cards, "type")));

        for ($i = 0; $i < count($types); $i++) {
            $menu->add($types[$i], "buyCard {$arg} {$types[$i]}", 0);
        }

        if (!count($menu->menu(2)) % 2 == 0) {
            $menu->add("", ".", 0);
        }

        $menu->add("Voltar", "unity", 0);

        $ctx->editMessageText($text, [
            "reply_markup" => $menu->menu(2)
        ]);
    }

    private function warning(Context $ctx): void
    {
        $ctx->answerCallbackQuery([
            "text" => "Nenhum cartão disponível!",
            "show_alert" => true
        ]);
    }
}
