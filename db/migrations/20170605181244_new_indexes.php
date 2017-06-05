<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

class NewIndexes extends AbstractMigration{
    public function change() {
        // User sources
        $sources = $this->table('sources');
        $sources
            ->addIndex('name')
            ->update();

        // Features
        $features = $this->table('features');
        $features
            ->addIndex('name')
            ->update();
    }
}
