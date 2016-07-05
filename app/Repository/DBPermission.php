<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Entity\Permission;

/**
 * Database-based Permission Repository Implementation.
 */
class DBPermission extends AbstractDBRepository implements PermissionInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'permissions';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Permission';

    /**
     * Find one permission given its identifiers.
     *
     * @param int    companyId permission's company_id
     * @param string section   permission's section
     * @param string propName  permission's propName
     */
    public function findOne($companyId, $routeName) {
        return $this->getOneByWhereConstraints([
            'company_id' => $companyId,
            'route_name' => $routeName
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId($companyId) {
        return $this->getAllByKey('company_id', $companyId);
    }

    /**
     * Deletes one permissions from company
     *
     * @param int    companyId permission's company_id
     * @param string routeName   permission's routeName
     */
    public function deleteOne($companyId, $routeName, $property) {
        return $this->query()
            ->where('company_id', $companyId)
            ->where('route_name', $routeName)
            ->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId($companyId) {
        return $this->deleteByKey('company_id', $companyId);
    }

}