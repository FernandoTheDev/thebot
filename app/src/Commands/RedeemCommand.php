<?php

namespace Fernando\Commands;

use Zanzara\Context;
use Fernando\Db\GiftDb;
use Fernando\Db\UserDb;
use Fernando\Model\DataQueryModel;

final class RedeemCommand
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $myId = $ctx->getEffectiveUser()->getId();
        $user = new UserDb($ctx);
        $userData = $user->getData();


        $code = (string) trim($this->dataQueryModel->getMessage()->getArgument());

        if ($code == '') {
            $this->invalid($ctx);
            return;
        }

        $gift = new GiftDb;
        $giftData = $gift->getGift($code);

        if (!$giftData["status"]) {
            $this->alreadyRedeemed($ctx);
            return;
        }

        $gift->consumeGift($code, $userData["uuid"]);
        $balanceNew = $userData["balance"] + $giftData["value"];
        $user->addBalance($giftData["value"]);

        $txt = sprintf("*Gift resgatado!*

*·* Valor: `R$%s`
*·* Novo saldo: `R$%s`

_Vai ter que gastar tudo no bot ;)_",
            number_format($giftData["value"], 2, ',', '.'),
            number_format($balanceNew, 2, ',', '.')
        );

        $ctx->sendMessage($txt, [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId(),
            "chat_id" => $myId
        ]);
    }

    private function alreadyRedeemed(Context $ctx): void
    {
        $ctx->sendMessage("Este gift já foi resgatado!", [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId()
        ]);
    }

    private function invalid(Context $ctx): void
    {
        $ctx->sendMessage("Gift inválido!", [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId()
        ]);
    }
}
