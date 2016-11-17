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
 *      3. "confidence_level" added to composite index
 */
class GatesGroupUpdate extends AbstractMigration
{
    public function up() {
        $gates = $this->table('gates');
        
        $this->query('DELETE FROM "gates"');

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

        $this->updateCategories();

        $subscriptions = $this->table('subscriptions');
        $subscriptions
            ->renameColumn('category_slug', 'category_name')
            ->save();
    }

    /**
     * Updates the categories table.
     * 
     * @return void
     */
    private function updateCategories() {
        $categories = $this->query('SELECT * FROM "categories"');

        if (! count($categories)) {
            return;
        }
        
        foreach ($categories as $category) {
            $sql = sprintf('UPDATE "%s" SET "name" = \'%s\' where id = \'%s\'', 'categories', $this->slugToCamelCase($category['name']), $category['id']);
            $this->query($sql);
        }
    }

    /**
     * Transforms a "slugified-string" to a "camelCaseString".
     *
     * @param string  $slug   The slug
     *
     * @return string
     */
    private function slugToCamelCase(string $slug) : string {
        $words  = explode('-', strtolower($slug));
        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst(trim($word));
        }

        return lcfirst($return);
    }

}
