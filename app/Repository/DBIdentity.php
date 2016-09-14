<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Identity;
use Illuminate\Support\Collection;

/**
 * Database-based Identity Repository Implementation.
 */
class DBIdentity extends AbstractSQLDBRepository implements IdentityInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'identities';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Identity';

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $pubKey) : Identity {
        return $this->findOneBy(['public_key' => $pubKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBySourceNameAndProfileId(string $sourceName, string $profileId) : Collection {
        $reference = sprintf(
            '%s:%s',
            $sourceName,
            $profileId
        );

        return $this->findBy(['reference' => md5($reference)]);
    }
}
