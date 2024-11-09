<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTopicsTable extends AbstractMigration
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
        $table = $this->table('topics', ['signed' => false]);

        $table->addColumn('userId', 'integer',  ['signed' => false])
              ->addForeignKey('userId', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addColumn('lessonId', 'integer', ['signed' => false])
              ->addForeignKey('lessonId', 'lessons', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addColumn('topicName', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('locked', 'boolean', ['default' => true])
              ->addColumn('completed', 'boolean', ['default' => false])
              ->addTimestamps()
              ->create();
              
    }
}
