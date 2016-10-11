<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Member;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Member Repository Implementation.
 */
class DBMember extends AbstractSQLDBRepository implements MemberInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'members';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company\Member';

    private $queryColumns = [
        'members.*',
        'companies.updated_at as company.updated_at',
        'identities.id as identity.id',
        'identities.reference as identity.reference',
        'identities.public_key as identity.public_key',
        'identities.private_key as identity.private_key',
        'identities.created_at as identity.created_at',
        'roles.id as role.id',
        'roles.name as role.name',
        'roles.rank as role.rank',
        'roles.bit as role.bit',
        'roles.created_at as role.created_at'
    ];

    /**
     * {@inheritdoc}
     */
    protected $relationships = [
        'identity' => [
            'type'       => 'MANY_TO_ONE',
            'table'      => 'identities',
            'foreignKey' => 'identity_id',
            'key'        => 'id',
            'entity'     => 'Identity',
            'nullable'   => false,
            'hydrate'    => [
                'id',
                'reference',
                'public_key',
                'created_at',
                'updated_at'
            ]
        ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
    ];

    /**
     * {@inheritdoc}
     */
    public function findMembership(int $identityId, int $companyId) : Member {
        return $this->findOneBy([
            'identity_id' => $identityId,
            'company_id'  => $companyId
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getByCompanyId(int $companyId, array $queryParams = []) : Collection {
        return $this->findBy(
            [
                'company_id' => $companyId
            ], 
            $queryParams
        );
    }
}
