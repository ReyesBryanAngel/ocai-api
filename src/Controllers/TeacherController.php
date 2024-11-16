<?php
namespace src\Controllers;

use src\Database\Database;
use src\Enums\ResultEnums;
use src\Services\TeacherService;
use src\Services\AdminService;
use src\Utils\OcaiUtilities;
use src\Services\JsonWebTokenService;
use src\Enums\RoleEnums;

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

        if ($userData->role !== RoleEnums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a teacher cannot perform such action.'
            ], 400);
        }
        
        $lessonName = $payload['lessonName'] ?? null;
        $sectionId = $payload['sectionId'] ?? null;
        $description = $payload['description'] ?? null;
        $coverPic = $payload['coverPic'] ?? null;
        $section = $this->teacherService->getSection($sectionId);

        if (!$section) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'Section not found.'
            ], 400);
        }

        $this->teacherService->addLesson($userData->id, $sectionId, $lessonName, $description, $coverPic);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Lesson has been created.'
        ], 201);
    }

    public function getLessons()
    {
        $userData = $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $sectionId = $_GET['sectionId'] ?? null;

        if ($userData->role !== RoleEnums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => "User that is not a " . RoleEnums::TEACHER->value . " cannot perform such action."
            ], 400);
        }

        $lessons = $this->teacherService->getLessons($userData->id, $sectionId);

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

        if ($userData->role !== RoleEnums::TEACHER->value) {
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
        $lessonId = $_GET['lessonId'] ?? null;

        if ($userData->role !== RoleEnums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => "User that is not a " . RoleEnums::TEACHER->value . " cannot perform such action."
            ], 400);
        }

        $topics = $this->teacherService->getTopics($userData->id, $lessonId);

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
        $minPassingRate = $payload['minPassingRate'] ?? null;
        $topic = $this->teacherService->getTopic($topicId);

        if ($userData->role !== RoleEnums::TEACHER->value) {
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

        $this->teacherService->addActivity($topicId, $activityName, $minPassingRate, $timeLimit);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Successfully added activity.',
        ], 201);
        
    }

    public function getActivities()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $topicId = $_GET['topicId'] ?? null;

        if ($userData->role !== RoleEnums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => "User that is not a " . RoleEnums::TEACHER->value . " cannot perform such action."
            ], 400);
        }

        $activities = $this->teacherService->getActivities($userData->id, $topicId);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get activities.',
            'data' => $activities
        ], 200);
    }

    public function getVideos()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $topicId = $_GET['topicId'] ?? null;

        if ($userData->role !== RoleEnums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => "User that is not a " . RoleEnums::TEACHER->value . " cannot perform such action."
            ], 400);
        }

        $activities = $this->teacherService->getVideos($userData->id, $topicId);

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

        if ($userData->role !== RoleEnums::TEACHER->value) {
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

        if (!$userData->role !== RoleEnums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => "User that is not a " . RoleEnums::TEACHER->value . " cannot perform such action."
            ], 400);
        }

        $questions = $this->teacherService->getAllQuestionsWithChoices($userData->id, $userData->sectionId, $userData->role);

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

        if ($userData->role !== RoleEnums::TEACHER->value) {
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

        if ($userData->role !== RoleEnums::TEACHER->value) {
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
            'message' => 'Successfully get awards.',
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

            if ($userData->role !== RoleEnums::TEACHER->value) {
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

    public function progressReport()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $userId = $_GET['userId'];
        $lessonId = $_GET['lessonId'];

        if ($userData->role !== RoleEnums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => "User that is not a " . RoleEnums::TEACHER->value . " cannot perform such action."
            ], 400);
        }

        $progressReport = $this->teacherService->getProgressReport(
            $userId, 
            ResultEnums::WATCHED->value, 
            ResultEnums::PASSED->value,
            $lessonId
        );

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get student progress report.',
            'data' => $progressReport
        ], 200);
    }

    public function setSchedule()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);

        $title = $payload['title'];
        $description = $payload['description'];
        $dayOfWeek = $payload['dayOfWeek'];
        $time = $payload['time'];
        $startDate = $payload['startDate'];
        $endDate = $payload['endDate'];
        $isRecurring = $payload['isRecurring'];
        $sectionId = $payload['sectionId'];
        $section = $this->teacherService->getSection($sectionId);

        if ($userData->role !== RoleEnums::TEACHER->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => "User that is not a " . RoleEnums::TEACHER->value . " cannot perform such action."
            ], 400);
        }

        if (!$section) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Section not found.'
            ], 404);
        }

        $this->teacherService->setSchedule(
            $userData->id, 
            $title, 
            $description, 
            $dayOfWeek, 
            $time, 
            $startDate, 
            $endDate, 
            $isRecurring
        );

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Schedule has been created.'
        ], 201);
    }

    public function assignSectionSchedule()
    {
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);
        $scheduleId = $payload['scheduleId'] ?? null;
        $sectionId = $payload['sectionId'] ?? null;
        $section = $this->teacherService->getSection($sectionId);

        if (!$section) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Section not found.'
            ], 404);
        }

        $this->teacherService->assignSectionSchedule($scheduleId, $sectionId);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Schedule has been assigned to ' . $section['name'] . ' section.'
        ], 201);
    }

    public function getSectionSchedule()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $schedules = $this->teacherService->getSectionSchedule($userData->id);

        $formattedSchedules = array_map(function ($schedule) {
            return [
                'id' => $schedule['scheduleId'],
                'userId' => $schedule['userId'],
                'title' => $schedule['title'],
                'description' => $schedule['description'],
                'dayOfWeek' => $schedule['dayOfWeek'],
                'time' => $schedule['time'],
                'startDate' => $schedule['startDate'],
                'endDate' => $schedule['endDate'],
                'isRecurring' => $schedule['isRecurring'],
                // 'created_at' => $schedule['created_at'],
                // 'updated_at' => $schedule['updated_at'],
                'section' => [
                    'id' => $schedule['sectionId'],
                    'name' => $schedule['sectionName'],
                    // 'grade' => $schedule['sectionGrade'],
                    'created_at' => $schedule['sectionCreatedAt'],
                ],
            ];
        }, $schedules);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully retrieved schedules',
            'data' => $formattedSchedules
        ], 200);
    }

}