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
        {
            $table = $this->table('users', ['signed' => false]);
    
            $table->addColumn('schoolId', 'string', ['limit' => 50, 'null' => true])
                ->addColumn('lastName', 'string', ['limit' => 50, 'null' => true])
                ->addColumn('firstName', 'string', ['limit' => 50, 'null' => true])
                ->addColumn('middleName', 'string', ['limit' => 50, 'null' => true])
                ->addColumn('gender', 'enum', ['values' => ['Male', 'Female', 'Other'], 'null' => true])
                ->addColumn('role', 'enum', ['values' => ['Admin', 'Teacher', 'Student'], 'null' => true])
                ->addColumn('contactNumber', 'string', ['limit' => 15, 'null' => true])
                ->addColumn('homeAddress', 'text', ['null' => true])
                ->addColumn('username', 'string', ['limit' => 50, 'null' => true])
                ->addColumn('password', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('photo', 'string', ['limit' => 255, 'null' => true])
                ->addColumn('isArchived', 'boolean', ['default' => false])
                ->addTimestamps()
                ->create();
        }
    }
}
