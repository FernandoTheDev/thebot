<?php

namespace Fernando\Commands;

use Fernando\Model\DataQueryModel;
use Zanzara\Context;

final class BroadcastCommand
{
    public function __construct(
        private DataQueryModel $dataQueryModel
    ) {
    }

    public function handler(Context $ctx): void
    {

    }
}
