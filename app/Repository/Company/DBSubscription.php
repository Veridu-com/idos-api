<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Repository\AbstractDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Subscription Repository Implementation.
 */
class DBSubscription extends AbstractDBRepository implements SubscriptionInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'subscriptions';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company\Subscription';

    public function getByCredentialId(int $credentialId) : Collection {
        return $this->findBy(
            [
            'credential_id' => $credentialId
            ]
        );
    }
}
