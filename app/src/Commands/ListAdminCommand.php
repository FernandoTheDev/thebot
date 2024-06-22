<?php

namespace Fernando\Commands;

use Zanzara\Context;
use Fernando\Db\ContainerDb;
use Fernando\Model\DataQueryModel;

final class ListAdminCommand
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

        $pdo = ContainerDb::getContainer()["pdo"];
        $stmt = $pdo->prepare("SELECT uuid, user_id FROM users WHERE admin = true");
        $stmt->execute();
        $fetch = $stmt->fetchAll();

        $txt = "Nada a mostrar.";

        if (count($fetch) > 0) {
            $txt = "*Lista de admins do bot:*\n";

            foreach ($fetch as $row => $value) {
                $txt .= "- UUID: `". $value["uuid"] ."`\n- ID: `". $value["user_id"] ."`\n\n";
            }
        }

        $ctx->sendMessage($txt, [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId()
        ]);
    }

    private function permission(Context $ctx): void
    {
        $ctx->sendMessage("Você não tem permissão pra isso.", [
            "reply_to_message_id" => $ctx->getMessage()->getMessageId()
        ]);
    }
}
