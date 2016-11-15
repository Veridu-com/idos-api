<?php

use Phinx\Migration\AbstractMigration;

class InvitationExpiresToDate extends AbstractMigration
{
	/**
	 * Converts "invitations"."expires" to "date"
	 */
    public function up()
    {
        $invitations = $this->table('invitations');
        $invitations->changeColumn('expires', 'date');
    }

}
