<?php

namespace Fernando\Callbacks;

use Exception;
use Zanzara\Context;
use Fernando\Db\UserDb;
use Fernando\Db\ContainerDb;
use Fernando\Model\MenuModel;
use Fernando\Model\DataQueryModel;

final class WalletCallback
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $user = new UserDb($ctx);
        $data = $user->getData();

        // Verificação de depuração
        if (method_exists($user, 'getUser')) {
            $userObj = $user->getUser();
            $firstName = $userObj->getFirstName();
        } else {
            throw new Exception("Método getUser não encontrado na classe UserDb");
        }

        $text = sprintf("👾 *Carteira*

*• UUID:* `%s`
*• ID:* `%d`
*• Nome:* `%s`

*• Saldo:* `R$%s`

*• Recargas:* `%d`
*• Compras:* `%d`

_Sistema de histórico ainda em desenvolvimento._",
            // UUID
            $data["uuid"],
            // ID user
            $data["user_id"],
            // First Name from user
            $firstName,
            // Balance format
            number_format($data["balance"], 2, ',', '.'),
            0,
            count((!$user->getHistoryShopping()) ? [] : $user->getHistoryShopping()),
        );

        $menu = new MenuModel();
        $menu->add("Developement", "developement", 0);
        $menu->add("Históricos", "historics", 0);
        $menu->add("Voltar", "start", 0);

        $ctx->editMessageText($text, [
            "reply_markup" => $menu->menu(2)
        ]);
    }
}
