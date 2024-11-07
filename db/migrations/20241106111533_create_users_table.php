<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('first_name', 'string', ['limit' => 50])
            ->addColumn('last_name', 'string', ['limit' => 50])
            ->addColumn('phone', 'string', ['limit' => 12, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('password', 'string', ['limit' => 255])
            ->addColumn('note', 'text', ['null' => true])
            ->addColumn('last_login', 'timestamp', ['null' => true])
            ->addTimestamps()
            ->addIndex(['phone'], ['unique' => true])
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }
}
