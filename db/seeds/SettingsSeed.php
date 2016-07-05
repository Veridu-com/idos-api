<?php
/*
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

use Phinx\Seed\AbstractSeed;

class SettingsSeed extends AbstractSeed {
    public function run() {
        $faker = Faker\Factory::create();

        $data = [];
        $now  = date('Y-m-d H:i:s');

        for ($i = 0; $i < 30; $i++) {
            $data[] = [
                'company_id'    => mt_rand(1, 2),
                'section'       => $faker->word,
                'property'      => $faker->word,
                'value'         => $faker->colorName,
                'created_at'    => $now,
                'updated_at'    => $now
            ];
        }

        $settings = $this->table('settings');
        $settings
            ->insert($data)
            ->save();
    }
}
