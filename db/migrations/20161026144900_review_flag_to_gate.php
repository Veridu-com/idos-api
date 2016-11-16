<?php

use Phinx\Migration\AbstractMigration;

class ReviewFlagToGate extends AbstractMigration
{
    /**
     * Changes "reviews->flag" relationship to "review->gate".
     */
    public function change() {
        $this->table('reviews')->drop();

        $reviews = $this->table('reviews');
        $reviews
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('identity_id', 'integer', ['null' => false])
            ->addColumn('gate_id', 'integer', ['null' => false])
            ->addColumn('positive', 'boolean', ['null' => false])
            ->addTimestamps()
            ->addIndex(['user_id', 'gate_id'], ['unique' => true])
            ->addForeignKey('identity_id', 'identities', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('gate_id', 'gates', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();

    }
}
