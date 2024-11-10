<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMessagesTable extends AbstractMigration
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
        $table = $this->table('messages', ['signed' => false]);
        
        $table->addColumn('userId', 'integer',  ['signed' => false])
              ->addForeignKey('userId', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addColumn('recipientId', 'integer', ['limit' => 11, 'null' => true])
              ->addColumn('recipientType', 'enum', [
                  'values' => ['Admin', 'Student', 'Teacher'],
                  'null' => true
              ])
              ->addColumn('encryptedMessage', 'text', ['null' => false])
              ->addColumn('isDeleted', 'boolean', ['default' => false])
              ->addColumn('isArchived', 'boolean', ['default' => false])
              ->addTimestamps()
              ->create();
    }
}
