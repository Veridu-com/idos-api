<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Migration\AbstractMigration;

/**
 * DATA SUPPORT TABLES.
 */
class SupportInit extends AbstractMigration {
    public function change() {

        $cityList = $this->table('city_list');
        $cityList
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('alternate_name', 'text', ['null' => true])
            ->addColumn('region', 'text', ['null' => true])
            ->addColumn('country', 'text', ['null' => true])
            ->addIndex('name')
            ->addIndex('region')
            ->addIndex('country')
            ->create();

        $countryList = $this->table(
            'country_list',
            [
                'id'          => false,
                'primary_key' => 'code',
            ]
        );
        $countryList
            ->addColumn('code', 'char', ['null' => false, 'limit' => 2])
            ->addColumn('name', 'text', ['null' => false])
            ->create();

        $knownNameList = $this->table(
            'known_name_list',
            [
                'id'          => false,
                'primary_key' => ['name', 'type'],
            ]
        );
        $knownNameList
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('type', 'text', ['null' => false])
            ->addColumn('soundex', 'text', ['null' => false])
            ->addColumn('metaphone', 'text', ['null' => false])
            ->addColumn('dmetaphone1', 'text', ['null' => false])
            ->addColumn('dmetaphone2', 'text', ['null' => false])
            ->addIndex('soundex')
            ->addIndex('metaphone')
            ->addIndex('dmetaphone1')
            ->addIndex('dmetaphone2')
            ->create();

        $nameList = $this->table(
            'name_list',
            [
                'id'          => false,
                'primary_key' => ['country', 'name'],
            ]
        );
        $nameList
            ->addColumn('country', 'text', ['null' => false])
            ->addColumn('name', 'text', ['null' => false])
            ->addColumn('gender', 'char', ['null' => false, 'limit' => 1])
            ->addColumn('soundex', 'text', ['null' => false])
            ->addColumn('metaphone', 'text', ['null' => false])
            ->addColumn('dmetaphone1', 'text', ['null' => false])
            ->addColumn('dmetaphone2', 'text', ['null' => false])
            ->addIndex('soundex')
            ->addIndex('metaphone')
            ->addIndex('dmetaphone1')
            ->addIndex('dmetaphone2')
            ->create();
    }
}
