<?php

namespace Fernando\Commands;

use Zanzara\Context;
use Fernando\Db\GiftDb;
use Fernando\Db\UserDb;
use Fernando\Model\DataQueryModel;

final class GiftCommand
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $myId = $ctx->getEffectiveUser()->getId();
        $user = new UserDb($ctx);
        $isValid = $myId == $_ENV["BOT_OWNER"] || $myId == $_ENV["BOT_DEVELOPER"] || $user->get("admin") == true;

        if (!$isValid) {
            $this->permission($ctx);
            return;
        }

        $value = (float) trim($this->dataQueryModel->getMessage()->getArgument());
        
        if ($value <= 0) {
            $this->valueInvalid($ctx);
            return;
        }
        
        $gift = new GiftDb;
        $giftCreated = $gift->createGift($user->getData()["uuid"], $value);

        $txt = sprintf("*Gift criado!*
        
*·* Código: `%s`
*·* Valor: `R$%s`

Resgate com o comando `/redeem %s`",
            $giftCreated[1],
            number_format($value, 2, ',', '.'),
            $giftCreated[1]
        );

        $ctx->sendMessage($txt, [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId(),
            "chat_id" => $myId
        ]);
    }

    private function permission(Context $ctx): void
    {
        $ctx->sendMessage("Você não tem permissão pra isso.", [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId()
        ]);
    }

    private function valueInvalid(Context $ctx): void
    {
        $ctx->sendMessage("Adicione um valor válido!", [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId()
        ]);
    }
}
