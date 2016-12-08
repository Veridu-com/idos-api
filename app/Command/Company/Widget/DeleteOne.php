<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Widget;

use App\Command\AbstractCommand;

/**
 * Widget "Delete One" Command.
 */
class DeleteOne extends AbstractCommand {
    /**
     * Widget hash.
     *
     * @var string
     */
    public $hash;
    /**
     * Acting identity.
     *
     * @var \App\Entity\Identity
     */
    public $identity;

    /**
     * {@inheritdoc}
     *
     * @return \App\Command\Company\Widget\DeleteOne
     */
    public function setParameters(array $parameters) : self {
        return $this;
    }
}
