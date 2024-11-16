<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSchedulesTable extends AbstractMigration
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
        $table = $this->table('schedules', ['signed' => false]);
        
        $table->addColumn('userId', 'integer',  ['signed' => false])
              ->addForeignKey('userId', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addColumn('title', 'string', ['limit' => 100])
              ->addColumn('description', 'text', ['null' => true])
              ->addColumn('dayOfWeek', 'enum', ['values' => [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
                'Saturday', 'Sunday'
              ]])
              ->addColumn('time', 'time')
              ->addColumn('startDate', 'date')
              ->addColumn('endDate', 'date', ['null' => true])
              ->addColumn('isRecurring', 'boolean')
              ->addTimestamps()
              ->create();
    }
}
