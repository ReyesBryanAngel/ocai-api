<?php
namespace src\Controllers;

use src\Database\Database;
use src\Services\TeacherService;
use src\Services\AdminService;
use src\Utils\OcaiUtilities;

class TeacherController {
    private $db;
    private $conn;
    private $teacherService;
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

    public function addLesson()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        
        $userId = $payload['userId'] ?? null;
        $lessonName = $payload['lessonName'] ?? null;
        $description = $payload['description'] ?? null;
        $coverPic = $payload['coverPic'] ?? null;
        $userData = $this->adminService->getUser($userId);

        if (!$userData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'User not found.'
            ], 404);
        }

        $this->teacherService->addLesson($userId, $lessonName, $description, $coverPic);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Lesson has been created.'
        ], 201);
    }

    public function getLessons()
    {
        $userId = $_GET['userId'];
        $userData = $this->adminService->getUser($userId);

        if (!$userData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'User not found.'
            ], 404);
        }

        $lessons = $this->teacherService->getLessons($userId);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get lessons.',
            'data' => $lessons
        ], 200);
    }

    public function addTopic()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $userId = $payload['userId'] ?? null;
        $lessonId = $payload['lessonId'] ?? null;
        $topicName = $payload['topicName'] ?? null;
        $userData = $this->adminService->getUser($userId);
        $lessonData = $this->teacherService->getLesson($lessonId);

        if (!$userData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'User not found.'
            ], 404);
        }

        if (!$lessonData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Lesson not found.'
            ], 404);
        }

        $this->teacherService->addTopic($userId, $lessonId, $topicName);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Topic has been created.'
        ], 201);
    }

    public function getTopics()
    {
        $topics = $this->teacherService->getTopics();

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get the topics',
            'data' => $topics
        ], 200);
    }
}