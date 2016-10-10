<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Invitation;
use App\Exception\AppException;
use App\Repository\AbstractSQLDBRepository;
use App\Repository\Company\InvitationInterface;
use Illuminate\Support\Collection;

/**
 * Database-based Invitation Repository Implementation.
 */
class DBInvitation extends AbstractSQLDBRepository implements InvitationInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'invitations';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company\Invitation';
    /**
     * {@inheritdoc}
     */
    protected $relationships = [
    ];

    /**
     * {@inheritdoc}
     */
    public function findOneByHash(string $hash) : Invitation {
        return $this->findOneBy([
            'hash' => $hash
        ]);        
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
        return $this->findBy([
            'company_id' => $companyId
        ]);        
    }
}
