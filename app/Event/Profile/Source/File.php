<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Event\Profile\Source;

use App\Entity\Company;
use App\Entity\Company\Credential;
use App\Entity\Profile\Process;
use App\Entity\Profile\Source;
use App\Entity\User;
use App\Event\AbstractServiceQueueEvent;

/**
 * File event.
 */
class File extends AbstractServiceQueueEvent {
    /**
     * Event related Source.
     *
     * @var \App\Entity\Profile\Source
     */
    public $source;
    /**
     * Event related User.
     *
     * @var \App\Entity\User
     */
    public $user;
    /**
     * Event related Credential.
     *
     * @var \App\Entity\Company\Credential
     */
    public $credential;
    /**
     * Event related Company.
     *
     * @var \App\Entity\Company
     */
    public $company;
    /**
     * Event related Process.
     *
     * @var \App\Entity\Profile\Process
     */
    public $process;
    /**
     * Event related File Content.
     *
     * @var string
     */
    public $fileContents;
    /**
     * Event related File Extension.
     *
     * @var string
     */
    public $fileExtension;
    /**
     * Event related IP Address.
     *
     * @var string
     */
    public $ipAddr;

    /**
     * Class constructor.
     *
     * @param \App\Entity\Profile\Source     $source
     * @param \App\Entity\User               $user
     * @param \App\Entity\Company\Credential $credential
     * @param \App\Entity\Company            $company
     * @param \App\Entity\Profile\Process    $process
     * @param string                         $fileContents
     * @param string                         $fileExtension
     * @param string                         $ipAddr
     *
     * @return void
     */
    public function __construct(
        Source $source,
        User $user,
        Credential $credential,
        Company $company,
        Process $process,
        string $fileContents,
        string $fileExtension,
        string $ipAddr
    ) {
        $this->source        = $source;
        $this->user          = $user;
        $this->credential    = $credential;
        $this->company       = $company;
        $this->process       = $process;
        $this->fileContents  = $fileContents;
        $this->fileExtension = $fileExtension;
        $this->ipAddr        = $ipAddr;
    }

    /**
     * {inheritdoc}.
     */
    public function getServiceHandlerPayload(array $merge = []) : array {
        if (property_exists($this->source->tags, 'token_secret')) {
            $merge['tokenSecret'] = $this->source->tags->token_secret;
        }

        return array_merge(
            [
            'accessToken'  => $this->source->tags->access_token,
            'providerName' => $this->source->name,
            'publicKey'    => $this->credential->public,
            'sourceId'     => $this->source->getEncodedId(),
            'processId'    => $this->process->getEncodedId(),
            'userName'     => $this->user->username
            ], $merge
        );
    }

    /**
     * Gets the event identifier.
     *
     * @return string
     **/
    public function __toString() {
        return sprintf('idos:file.%s.created', strtolower($this->source->name));
    }
}
