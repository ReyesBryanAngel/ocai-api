<?php
namespace src\Controllers;

use src\Database\Database;
use src\Services\TeacherService;
use src\Services\AdminService;
use src\Utils\OcaiUtilities;
use src\Services\JsonWebTokenService;
use src\Utils\Enums;

class TeacherController {
    private $db;
    private $conn;
    private $teacherService;
    private $adminService;
    private $utils;
    private $jwtService;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->teacherService = new TeacherService($this->conn);
        $this->adminService = new AdminService($this->conn);
        $this->jwtService = new JsonWebTokenService();
        $this->utils = new OcaiUtilities();
    }

    public function addLesson()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }
        
        $lessonName = $payload['lessonName'] ?? null;
        $description = $payload['description'] ?? null;
        $coverPic = $payload['coverPic'] ?? null;

        $this->teacherService->addLesson($userData->id, $lessonName, $description, $coverPic);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Lesson has been created.'
        ], 201);
    }

    public function getLessons()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }

        $lessons = $this->teacherService->getLessons($userData->id);

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
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $lessonId = $payload['lessonId'] ?? null;
        $topicName = $payload['topicName'] ?? null;
        $isLocked = $payload['isLocked'] ? 1 : 0;
        $lessonData = $this->teacherService->getLesson($lessonId);

        if (!$lessonData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Lesson not found.'
            ], 404);
        }

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }

        $this->teacherService->addTopic($lessonId, $topicName, $isLocked);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Topic has been created.'
        ], 201);
    }

    public function getTopics()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }

        $topics = $this->teacherService->getTopics($userData->id);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get the topics',
            'data' => $topics
        ], 200);
    }

    public function addActivity()
    {
        $userData =$this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);
        $topicId = $payload['topicId'] ?? null;
        $activityName = $payload['activityName'] ?? null;
        $timeLimit = $payload['timeLimit'] ?? null;
        $topic = $this->teacherService->getTopic($topicId);

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }

        if (!$topic) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Topic not found.'
            ], 404);
        }

        $this->teacherService->addActivity($topicId, $activityName, $timeLimit);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Successfully added activity.',
        ], 201);
        
    }

    public function getActivities()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }

        $activities = $this->teacherService->getActivities($userData->id);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get activities.',
            'data' => $activities
        ], 200);
    }

    public function addQuestions() {
        $payload = json_decode(file_get_contents('php://input'), true);
        $questions = $payload['questions'] ?? [];
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }
 
        if (empty($questions)) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'No questions provided.'
            ], 400);
        }
    
        $this->teacherService->addQuestions($userData->id, $questions);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Questions added successfully.',
        ], 201);
    }

    public function getQuestions()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }

        $questions = $this->teacherService->getAllQuestionsWithChoices($userData->id);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get questions.',
            'data' => $questions
        ], 200);
    }

    public function addAward()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $activityId = $payload['activityId'] ?? null;
        $awardName = $payload['awardName'] ?? null;
        $criteria = $payload['criteria'] ?? null;
        $awardType = $payload['awardType'] ?? null;
        $filePath = $payload['filePath'] ?? null;
        
        $this->teacherService->addAward($activityId, $filePath, $awardName, $criteria, $awardType);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Award has been created.',
        ], 201);

    }

    public function getAwards()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        if ($userData->role != Enums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }

        $awards = $this->teacherService->getAwards($userData->id);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get questions.',
            'data' => $awards
        ], 200);
    }

    public function uploadVideo()
    {
        if (isset($_FILES['file'])) {
            $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
            $topicId = $_POST['topicId'] ?? null;
            $topic = $this->teacherService->getTopic($topicId);

            if (!$topic) {
                $this->utils->jsonResponse([
                    'code' => 404,
                    'status' => 'failed',
                    'message' => 'Topic not found.'
                ], 404);
            }

            if ($userData->role != Enums::TEACHER->value) {
                $this->utils->jsonResponse([
                    'code' => 400,
                    'status' => 'failed',
                    'message' => 'User that is not a teacher cannot perform such action.'
                ], 400);
            }

            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileType = $_FILES['file']['type'];
    
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $filePath = $uploadDir . basename($fileName);
    
            if (move_uploaded_file($fileTmpPath, $filePath)) {
                $this->teacherService->uploadVideo($topicId, $fileName, $filePath, $fileType);

                $this->utils->jsonResponse([
                    'code' => 201,
                    'status' => 'success',
                    'message' => 'Successfully uploaded topic video.'
                ], 201);
            }
        }
    }
    
}