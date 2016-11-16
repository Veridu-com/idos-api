<?php

use Phinx\Migration\AbstractMigration;

class ReviewFlagToGate extends AbstractMigration {
    /**
     * Changes "reviews->flag" relationship to "review->gate".
     */
    public function up() {
        // Profile reviews values
        $reviews = $this->table('reviews');
        $reviews
            ->dropForeignKey('flag_id')
            ->renameColumn('flag_id', 'gate_id')
            ->addForeignKey('gate_id', 'gates', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->save();
    }
}
