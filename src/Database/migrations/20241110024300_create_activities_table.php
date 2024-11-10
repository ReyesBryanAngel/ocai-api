<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateActivitiesTable extends AbstractMigration
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
        $table = $this->table('activities', ['signed' => false]);
        $table->addColumn('topicId', 'integer', ['signed' => false])
        ->addForeignKey('topicId', 'topics', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
        ->addColumn('activityName', 'string', ['limit' => 100, 'null' => true])
        ->addColumn('timeLimit', 'time', ['null' => true])
        ->create();
    }
}
