<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Setting;

use App\Command\AbstractCommand;

/**
 * Setting "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Setting Id.
     *
     * @var int
     */
    public $settingId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Setting\DeleteOne
     */
    public function setParameters(array $parameters) : self {

        return $this;
    }
}
