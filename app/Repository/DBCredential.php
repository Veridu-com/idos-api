<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Credential;
use Illuminate\Support\Collection;
use Lcobucci\JWT;

/**
 * Database-based Credential Repository Implementation.
 */
class DBCredential extends AbstractDBRepository implements CredentialInterface {
    /**
     * The table associated with the repository.
     *
     * @var string
     */
    protected $tableName = 'credentials';
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Credential';

    /**
     * {@inheritdoc}
     */
    public function findByPubKey(string $key) : Credential {
        return $this->findOneBy(['public' => $key]);
    }

    public function findByPrivKey(string $key) : Credential {
        return $this->findOneBy(['private' => $key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllByCompanyId(int $companyId) : Collection {
        return $this->findBy(['company_id' => $companyId]);
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
    public function deleteByPubKey(string $key) : int {
        return $this->deleteByKey('public', $key);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCompanyIdAndPubKey(int $companyId, string $key) : Credential {
        return $this->findOneBy(['company_id' => $companyId, 'public' => $key]);
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken($credentialPubKey, string $servicePrivKey, string $servicePubKey) : string {
        $jwtParser     = new JWT\Parser();
        $jwtValidation = new JWT\ValidationData();
        $jwtSigner     = new JWT\Signer\Hmac\Sha256();
        $jwtBuilder    = new JWT\Builder();

        $jwtBuilder->set('iss', $servicePubKey);
        $jwtBuilder->set('sub', $credentialPubKey);

        return (string) $jwtBuilder
            ->sign($jwtSigner, $servicePrivKey)
            ->getToken();
    }
}
