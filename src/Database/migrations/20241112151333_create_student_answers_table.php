<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStudentAnswersTable extends AbstractMigration
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
        $table = $this->table('studentAnswers', ['signed' => false]);
        
        $table->addColumn('userId', 'integer',  ['signed' => false])
              ->addForeignKey('userId', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addColumn('activityId', 'integer',  ['signed' => false])
              ->addForeignKey('activityId', 'activities', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addColumn('questionId', 'integer',  ['signed' => false])
              ->addForeignKey('questionId', 'questions', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
              ->addColumn('answer', 'string', ['limit' => 18, 'null' => true])
              ->addColumn('result', 'enum', ['values' => ['Correct', 'Incorrect'], 'null' => true ])
              ->addTimestamps()
              ->create();
    }
}
