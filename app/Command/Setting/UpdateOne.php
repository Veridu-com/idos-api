<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types=1);

namespace App\Command\Setting;

use App\Command\AbstractCommand;

/**
 * Setting "Update One" Command.
 */
class UpdateOne extends AbstractCommand {
    /**
     * Setting's section name identifier (comes from URI).
     *
     * @var object
     */
    public $sectionNameId;
    /**
     * Setting's property name identifier (comes from URI).
     *
     * @var object
     */
    public $propNameId;
    /**
     * Setting's section name (user input).
     *
     * @var object
     */
    public $section;
    /**
     * Setting's property name (user input).
     *
     * @var object
     */
    public $property;
    /**
     * Setting's property value (user input).
     *
     * @var object
     */
    public $value;
    /**
     * Setting Id.
     *
     * @var int
     */
    public $companyId;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Setting\UpdateOne
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['sectionNameId'])) {
            $this->sectionNameId = $parameters['sectionNameId'];
        }

        if (isset($parameters['propNameId'])) {
            $this->propNameId = $parameters['propNameId'];
        }

        if (isset($parameters['section'])) {
            $this->section = $parameters['section'];
        }

        if (isset($parameters['property'])) {
            $this->property = $parameters['property'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        if (isset($parameters['companyId'])) {
            $this->companyId = $parameters['companyId'];
        }

        return $this;
    }
}
