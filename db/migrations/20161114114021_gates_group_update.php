<?php

use Phinx\Migration\AbstractMigration;

/**
 * Gates update:.
 * 
 * Gates needs to be grouped - they have a clear relationship with each other:
 * 		firstNameHigh, firstNameMedium, firstNameLow
 * 		
 * At this point we could only group gates using regular expression - which is not the best thing to do.
 * Changes:
 * 		1. "gates"."name" should be a FK to "categories" table
 * 		2. "gates" should have a "conficence_level" string column
 *      5. "confidence_level" added to composite index
 */
class GatesGroupUpdate extends AbstractMigration
{
    public function up()
    {
        $gates = $this->table('gates');
        $gates
            ->addColumn('confidence_level', 'text', ['null' => true])
            ->removeIndex(['user_id', 'creator', 'name'], 'gates_user_id_creator_name')
            ->addIndex(['user_id', 'creator', 'name', 'confidence_level'], ['unique' => true])
            ->addForeignKey('name', 'categories', 'slug', ['delete' => 'NO ACTION', 'update' => 'CASCADE'])
            ->save();

        $categories = $this->table('categories');
        $categories
            ->renameColumn('name', 'display_name')
            ->renameColumn('slug', 'name')
            ->save();

        $subscriptions = $this->table('subscriptions');
        $subscriptions
            ->renameColumn('category_slug', 'category_name')
            ->save();
    }
}
