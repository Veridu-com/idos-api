<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Subscription;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Subscription Repository Implementation.
 */
class DBSubscription extends AbstractSQLDBRepository implements SubscriptionInterface {
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

    /**
     * {@inheritdoc}
     */
    public function getAllByCredentialId(int $credentialId) : Collection {
        $gateRepository = $this->repositoryFactory->create('Profile\Gate');
        $warningRepository = $this->repositoryFactory->create('Profile\Warning');
        
        $collection = $this->findBy(
            [
                'credential_id' => $credentialId
            ]
        );
        var_dump($collection->toArray());

        return $collection->map(function ($credential) {

            if ($credetial->gate) {
                # code...
            }

        });
    }
}
