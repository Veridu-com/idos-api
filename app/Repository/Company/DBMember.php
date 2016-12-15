<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Member;
use App\Exception\NotFound;
use App\Factory\Entity;
use App\Factory\Repository;
use App\Helper\Vault;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Jenssegers\Optimus\Optimus;

/**
 * Database-based Member Repository Implementation.
 */
class DBMember extends AbstractSQLDBRepository implements MemberInterface {
    /**
     * Compay repository.
     *
     * @var \App\Repository\CompanyInterface
     */
    private $companyRepository;

    public function __construct(
        Entity $entityFactory,
        Repository $repositoryFactory,
        Optimus $optimus,
        Vault $vault,
        ConnectionInterface $sqlConnection
    ) {
        parent::__construct($entityFactory, $repositoryFactory, $optimus, $vault, $sqlConnection);
        $this->companyRepository = $this->repositoryFactory->create('Company');
    }

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
    public function findDirectMembership(int $identityId, int $companyId) : Member {
        return $this->findOneBy(
            [
                'identity_id' => $identityId,
                'company_id'  => $companyId
            ]
        );
    }
    /**
     * {@inheritdoc}
     */
    public function findMembership(int $identityId, int $companyId) : Member {
        try {
            return $this->findDirectMembership($identityId, $companyId);
        } catch (NotFound $e) {
            // this means the user doesn't have a direct relationship with the company
            // but the user may have an indirect relationship
            // if the user has a role in a company that is a parent of the given company
            // it should return the membership relationship with this father

            return $this->hasParentAccess($identityId, $companyId);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasParentAccess(int $identityId, int $companyId) : Member {
        $company = $this->companyRepository->find($companyId);

        try {
            return $this->findDirectMembership($identityId, $companyId);
        } catch (NotFound $e) {
            if (! $company->parentId) {
                throw new NotFound();
            }

            return $this->hasParentAccess($identityId, $company->parentId);
        }
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
