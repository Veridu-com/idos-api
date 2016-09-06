<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Raw;
use App\Exception\NotFound;
use Illuminate\Support\Collection;

/**
 * NoSQL Database-based Raw Data Repository Implementation.
 */
class DBRaw extends AbstractNoSQLDBRepository implements RawInterface {
    /**
     * The collection associated with the repository.
     *
     * @var string
     */
    protected $collectionName = null;
    /**
     * The entity associated with the repository.
     *
     * @var string
     */
    protected $entityName = 'Raw';

    /**
     * {@inheritdoc}
     */
    public function getAllBySource(Source $source) : Collection {

    }

    /**
     * {@inheritdoc}
     */
    public function deleteBySource(Source $source) : int {

    }

    /**
     * {@inheritdoc}
     */
    public function create(Source $source, Raw $raw) : Raw {
        $this->selectDatabase($source->name);

        $_id = md5($source->id);
        $success = $this->query($raw->name)->insert([
            '_id' => $_id,
            'data' => $raw->data]);

        if(! $success) {
            throw new AppException('Error while creating Raw entity');
        }

        return $this->findOneBySourceAndName($source, $raw->name);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySourceAndName(Source $source, string $name) : Raw {
        $this->selectDatabase($source->name);

        $data = $this->query($name)->find(md5($source->id));

        if(! $data) {
            throw new NotFound('A Raw with the given source and name could not be found.');
        }

        $raw = new Raw([
            'id' => $data['_id'],
            'name' => $name,
            'data' => $data['data']
        ]);

        return $raw;
    }

    /**
     * {@inheritdoc}
     */
    public function updateOneBySourceAndName(Source $source, string $name, string $data) : Raw {

    }
    
    /**
     * {@inheritdoc}
     */
    public function deleteOneBySourceAndName(Source $source, string $name) : int {

    }
}
