<?php
namespace src\Controllers;

use src\Database\Database;
use src\Services\TeacherService;
use src\Services\AdminService;
use src\Utils\OcaiUtilities;

class StudentController {
    private $db;
    private $conn;
    private $adminService;
    private $utils;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->teacherService = new TeacherService($this->conn);
        $this->adminService = new AdminService($this->conn);
        $this->utils = new OcaiUtilities();
    }
}