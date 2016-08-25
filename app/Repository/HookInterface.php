<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Hook;
use Illuminate\Support\Collection;

/**
 * Hook Repository Interface.
 */
interface HookInterface extends RepositoryInterface {
    /**
     * Gets all Hooks based on their Credential Id.
     *
     * @param int $credentialId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllByCredentialId(int $credentialId) : Collection;
    /*
     * Deletes all Hooks based on their Credential Id.
     *
     * @param int $credentialId
     *
     * @return int
     */
    public function deleteByCredentialId(int $credentialId) : int;
}
