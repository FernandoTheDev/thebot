<?php

namespace Fernando\Callbacks;

use Zanzara\Context;
use Fernando\Db\UserDb;
use Fernando\Db\CardsDb;
use Fernando\Model\MenuModel;
use Fernando\Model\DataQueryModel;

final class BuyCardCallback
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $arg = $this->dataQueryModel->getCallback()->getArgument();
        $args = explode(" ", $arg);
        $type = $args[count($args) - 1];
        array_pop($args);

        $level = implode(" ", $args);

        $user = new UserDb($ctx);
        $price = CardsDb::getLevelData($level)["value"];

        if ($price > $user->getData()["balance"]) {
            $ctx->answerCallbackQuery([
                "text" => "Saldo insuficiente para a compra.",
                "show_alert" => true
            ]);
            return;
        }

        $cards = CardsDb::getCardWhereLevelAndTypeIsValid($level, $type);

        if (count($cards) < 1) {
            $this->warning($ctx);
            return;
        }

        $ctx->answerCallbackQuery([
            "text" => "Realizando compra.",
            "show_alert" => true
        ]);

        $this->buy($ctx, $cards);

    }

    private function buy(Context $ctx, array $cards): void
    {
        $preText = "✅ *Compra efetuada com sucesso*

*• Cartão:* `%d`
*• Validade:* `%s`
*• Cvv:* `%d`

*• Bandeira:* `%s`
*• Nível:* `%s`
*• Tipo:* `%s`
*• País:* `%s`
*• Banco:* `%s`

*Valor:* `R$%s`
*Novo saldo:* `R$%s`

_A compra já está disponível em seu histórico de compras, ele ainda não está acessível pois ainda não foi completamente desenvolvido pelo mantenedor._";

        $cardSelected = $cards[0];
        $price = CardsDb::getLevelData($cardSelected["level"])["value"];
        $user = new UserDb($ctx);
        $user->addShopping("unity", json_encode($cards, JSON_UNESCAPED_UNICODE), $price);

        $user->unsetBalance($price);
        CardsDb::unsetCardByUuid($cardSelected["uuid"]);

        $txt = sprintf(
            $preText,
            $cardSelected["card"],
            "{$cardSelected['month']}/{$cardSelected['year']}",
            $cardSelected["cvv"],

            $cardSelected["flag"],
            $cardSelected["level"],
            $cardSelected["type"],
            $cardSelected["country"],
            $cardSelected["bank"],

            number_format($price, 2, ',', '.'),
            number_format($user->getData()["balance"], 2, ',', '.')
        );

        $menu = new MenuModel();
        $menu->add("Voltar", "start", 0);

        $ctx->editMessageText($txt, [
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
