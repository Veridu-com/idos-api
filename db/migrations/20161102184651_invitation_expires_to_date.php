<?php

use Phinx\Migration\AbstractMigration;

class InvitationExpiresToDate extends AbstractMigration
{
    public function change() {
        $invitations = $this->table('invitations');
        $invitations->changeColumn('expires', 'date');
    }
}
