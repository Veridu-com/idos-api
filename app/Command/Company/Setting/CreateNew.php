<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Command\Company\Setting;

use App\Command\AbstractCommand;

/**
 * Setting "Create New" Command.
 */
class CreateNew extends AbstractCommand {
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
     * Setting's protected value.
     *
     * @var bool
     */
    public $protected;

    /**
     * Setting's property value (user input).
     *
     *
     * @var object
     */
    public $value;

    /**
     * Company.
     *
     * @var \App\Entity\Company
     */
    public $company;

    /**
     * {@inheritdoc}
     *
     * @return App\Command\Company\Setting\CreateNew
     */
    public function setParameters(array $parameters) : self {
        if (isset($parameters['section'])) {
            $this->section = $parameters['section'];
        }

        if (isset($parameters['property'])) {
            $this->property = $parameters['property'];
        }

        if (isset($parameters['value'])) {
            $this->value = $parameters['value'];
        }

        if (isset($parameters['protected'])) {
            $this->protected = $parameters['protected'];
        }

        return $this;
    }
}