<?php

namespace Fernando\Callbacks;

use Exception;
use Zanzara\Context;
use Fernando\Model\MenuModel;
use Fernando\Model\DataQueryModel;

final class DevelopementCallback
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {
        $text = sprintf("⚙️ *Desenvolvimento do bot*

Com o objetivo de ser *OS* (OpenSource), a source tenta trazer tudo o que há de bom na forma tradicional de vender, misturado com uma pitada de automação, e uma pequena parte constituída por álgebra.

*• Versão: %s*

Ainda há muito o que fazer, versões mais completas são vendidas separadamente pelo criador. Apenas pelo criador!",
            $_ENV["BOT_VERSION"]
        );

        $menu = new MenuModel();
        $menu->add("Developer", "t.me/fernandothedev", 1);
        $menu->add("Canal", "t.me/thebot_project", 1);
        $menu->add("Voltar", "wallet", 0);

        $ctx->editMessageText($text, [
            "reply_markup" => $menu->menu(2)
        ]);
    }
}
