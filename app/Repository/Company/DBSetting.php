<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository\Company;

use App\Entity\Company\Setting;
use App\Exception\NotFound;
use App\Repository\AbstractSQLDBRepository;
use Illuminate\Support\Collection;

/**
 * Database-based Setting Repository Implementation.
 */
class DBSetting extends AbstractSQLDBRepository implements SettingInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'settings';

    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Company\Setting';

    /**
     * {@inheritdoc}
     */
    protected $filterableKeys = [
        'section'    => 'string',
        'property'   => 'string',
        'created_at' => 'date'
    ];

    /**
     * Gets the source tokens.
     *
     * @param      integer  $companyId         The company identifier
     * @param      string   $credentialPubKey  The credential pub key
     * @param      string   $sourceName        The source name
     *
     * @return     Collection   The source tokens.
     */
    public function getSourceTokens(int $companyId, string $credentialPubKey, string $sourceName) : Collection {
        // hosted social application (credential based)
        $credentialSettingKey = sprintf('%s.%s.key', $credentialPubKey, $sourceName);
        $credentialSettingSec = sprintf('%s.%s.secret', $credentialPubKey, $sourceName);
        $credentialSettingVer = sprintf('%s.%s.version', $credentialPubKey, $sourceName);

        // hosted social application (company based)
        $providerSettingKey = sprintf('%s.key', $sourceName);
        $providerSettingSec = sprintf('%s.secret', $sourceName);
        $providerSettingVer = sprintf('%s.version', $sourceName);

        $settings = $this->findByCompanyIdSectionAndProperties(
            $companyId,
            'AppTokens',
            [
                $credentialSettingKey,
                $credentialSettingSec,
                $credentialSettingVer
            ]
        );

        if (count($settings)) {
            return $settings;
        }

        $settings = $this->findByCompanyIdSectionAndProperties(
            $companyId,
            'AppTokens',
            [
                $providerSettingKey,
                $providerSettingSec,
                $providerSettingVer
            ]
        );

        if (count($settings)) {
            return $settings;
        }

        return new Collection();
    }

    /**
     * {@inheritdoc}
     */
    public function getByCompanyId(int $companyId, array $queryParams = []) : array {
        $dbQuery = $this->query()->where('company_id', $companyId);

        return $this->paginate(
            $this->filter($dbQuery, $queryParams),
            $queryParams
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicByCompanyId(int $companyId, array $queryParams = []) : array {
        $dbQuery = $this->query()
            ->where('company_id', $companyId)
            ->where('protected', false);

        return $this->paginate(
            $this->filter($dbQuery, $queryParams),
            $queryParams
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCompanyAndId(int $companyId, int $settingId) : Setting {
        $setting = $this->query()
            ->where('company_id', $companyId)
            ->where('id', $settingId)
            ->first();

        if (empty($setting)) {
            throw new NotFound('Setting not found');
        }

        return $setting;
    }

    /**
     * {@inheritdoc}
     */
    public function findByCompanyId(int $companyId) : Collection {
        return $this->findBy(
            [
                'company_id' => $companyId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCompanyId(int $companyId) : int {
        return $this->deleteByKey('company_id', $companyId);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCompanyIdSectionAndProperties(int $companyId, string $section, array $properties) : Collection {
        return $this->query()
            ->where('company_id', $companyId)
            ->where('section', $section)
            ->whereIn('property', $properties)
            ->get();
    }
}
