<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class AdminSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $table = $this->table('users');

        $rows = [
            [
                'id' => 1,
                'schoolId' => 'SCH001',
                'lastName' => 'Doe',
                'firstName' => 'John',
                'middleName' => 'A',
                'gender' => 'Male',
                'role' => 'Admin',
                'contactNumber' => '1234567890',
                'homeAddress' => '123 Main St, Springfield',
                'username' => 'johndoe',
                'password' => password_hash('password123', PASSWORD_DEFAULT), // hashed for security
                'photo' => 'path/to/photo.jpg',
                'isArchived' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'schoolId' => 'SCH002',
                'lastName' => 'Smith',
                'firstName' => 'Jane',
                'middleName' => 'B',
                'gender' => 'Female',
                'role' => 'Teacher',
                'contactNumber' => '0987654321',
                'homeAddress' => '456 Elm St, Springfield',
                'username' => 'janesmith',
                'password' => password_hash('securePass456', PASSWORD_DEFAULT),
                'photo' => 'path/to/photo2.jpg',
                'isArchived' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        

        $table->insert($rows)->saveData();
    }
}
