<?php

namespace Fernando\Commands;

use Fernando\Db\UserDb;
use Zanzara\Context;
use Fernando\Model\DataQueryModel;

final class SetAdminCommand
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $myId = $ctx->getEffectiveUser()->getId();
        $isValid = $myId == $_ENV["BOT_OWNER"] || $myId == $_ENV["BOT_DEVELOPER"];

        if (!$isValid) {
            $this->permission($ctx);
            return;
        }

        $idSet = trim($this->dataQueryModel->getMessage()->getArgument());
        $ctx->getEffectiveUser()->setId($idSet);

        $user = new UserDb($ctx);

        if (!$user->set("admin", true)) {
            $this->notFound($ctx, $myId);  
            return;
        }

        $this->notify($ctx, $myId);
    }

    private function permission(Context $ctx): void
    {
        $ctx->sendMessage("Você não tem permissão pra isso.", [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId()
        ]);
    }

    private function notFound(Context $ctx, int $myId): void
    {
        $ctx->sendMessage("Não foi possível adicionar esse usuário a adminstração!", [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId(),
            "chat_id" => $myId
        ]);
    }

    private function notify(Context $ctx, int $myId): void
    {
        $ctx->sendMessage("Usuário adicionado a adminstração do bot.", [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId(),
            "chat_id" => $myId
        ]);

        $ctx->sendMessage("Você foi adicionado a adminstração do bot.");
    }
}
