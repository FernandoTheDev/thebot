<?php

namespace Fernando\Callbacks;

use Fernando\Db\CardsDb;
use Zanzara\Context;
use Fernando\Model\MenuModel;
use Fernando\Model\DataQueryModel;

final class UnityCallback
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $cards = CardsDb::getCardsValids();

        if (count($cards) < 1) {
            $this->warning($ctx);
            return;
        }

        $levels = CardsDb::getLevelsValids();

        $text = sprintf("*💳 Unitárias*

*• Cartões disponível:* `%d`
*• Níveis disponíveis:* `%d`

_Veja os níveis disponíveis e seus respectivos valores abaixo:_",
            count($cards),
            count($levels)
        );

        $menu = new MenuModel();

        for ($i = 0; $i < count($levels); $i++) {
            $menu->add(sprintf("%s - R$%s", $levels[$i]["level"], number_format($levels[$i]["value"], 2, ",", ".")), "selectType {$levels[$i]['level']}", 0);
        }

        if (!count($menu->menu(2)) % 2 == 0) {
            $menu->add("", ".", 0);
        }

        $menu->add("Voltar", "cards", 0);

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
