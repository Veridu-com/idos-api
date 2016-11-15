<?php

use Phinx\Migration\AbstractMigration;

/**
 * Gates update:
 * 
 * Gates needs to be grouped - they have a clear relationship with each other:
 * 		first-name-high, first-name-medium, first-name-low
 * 		
 * At this point we could only group gates using regular expression - which is not the best thing to do.
 * Changes:
 * 		1. "gates"."slug" should be a FK to "categories" table
 * 		2. "gates" should have a "conficence_level" smallint column
 * 			
 */
class GatesGroupUpdate extends AbstractMigration
{
    public function up()
    {
        $gates = $this->table('gates');
		$gates
            ->removeColumn('name')
            ->addColumn('confidence_level', 'text', ['null' => true])
            ->addColumn('attribute', 'text', ['null' => true])
			->addForeignKey('slug', 'categories', 'slug', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
			->addForeignKey('attribute', 'categories', 'slug', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->save();
    }
}
