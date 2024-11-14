<?php
namespace src\Controllers;

use src\Database\Database;
use src\Services\StudentService;
use src\Services\TeacherService;
use src\Utils\OcaiUtilities;
use src\Enums\ResultEnums;
use src\Enums\RoleEnums;
use src\Services\JsonWebTokenService;

class StudentController {
    private $db;
    private $conn;
    private $studentService;
    private $utils;
    private $jwtService;
    private $teacherService;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->studentService = new StudentService($this->conn);
        $this->teacherService = new TeacherService($this->conn);
        $this->utils = new OcaiUtilities();
        $this->jwtService = new JsonWebTokenService();
    }

    public function getSectionLessons()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $section = $this->teacherService->getSection($userData->sectionId);

        if (!$section) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Section not found.'
            ], 404);
        }

        $sectionLessons = $this->studentService->getSectionLessons($userData->sectionId);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get section activities.',
            'data' => $sectionLessons
        ],200);
    }

    public function getLessonTopics()
    {
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $lessonId = $_GET['lessonId'] ?? null;

        $this->teacherService->getLesson($lessonId);

        if (!$lessonId) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Lessons not found.'
            ],404);
        }

        $lessonTopics = $this->studentService->getLessonTopics($lessonId);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get section activities.',
            'data' => $lessonTopics
        ],200);
    }

    public function getTopicActivities()
    {
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $topicId = $_GET['topicId'] ?? null;
        $activities = $this->studentService->getTopicActivities($topicId);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get topic activities.',
            'data' => !$activities ? null : $activities
        ],200);
    }

    public function getTopicVideos()
    {
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $topicId = $_GET['topicId'] ?? null;
        $videos = $this->studentService->getTopicVideos($topicId);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get topic videos.',
            'data' => !$videos ? null : $videos
        ],200);
    }

    public function getActivityQuestions()
    {
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $activityId = $_GET['activityId'] ?? null;

        $this->teacherService->getActivity($activityId);

        if (!$activityId) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Activity not found.'
            ],404);
        }

        $lessonTopics = $this->studentService->getActivityQuestions($activityId);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get activity questions.',
            'data' => $lessonTopics
        ],200);
        
    }

    public function answerQuestion()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);
        $totalQuestions = $this->studentService->countTotalQuestions($userData->id);
    
        if ($userData->role != RoleEnums::STUDENT->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a student cannot perform such action.'
            ], 400);
        }

        $questionId = $payload['questionId'] ?? null;
        $answer = $payload['answer'] ?? null;
        $question = $this->studentService->getQuestion($questionId);
        $choices = $this->studentService->getChoices($questionId);
        
        $totalStudentAnswers = $this->studentService->totalStudentAnswers($userData->id, $question['activityId']);
        $correctAnswer = $question['answer'];
        $answerResult = $answer === $correctAnswer ? ResultEnums::CORRECT->value : ResultEnums::INCORRECT->value;


        if (!$question) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Question not found.'
            ],404);
        }

        if (!in_array($answer, $choices)) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'Answer is not included from the choices list.'
            ],400);
        }

        if ($totalStudentAnswers === $totalQuestions) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'You have already completed the activity.'
            ],400);
        }

        $this->studentService->answerQuestion($userData->id, $questionId, $question['activityId'], $answer, $answerResult);

            $this->utils->jsonResponse([
                'code' => 201,
                'status' => 'success',
                'message' => 'Successfully provided an answer.'
            ],201);
    }

    public function evaluateActivityResult()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);
        $activityId = $payload['activityId'] ?? null;
        $totalQuestions = $this->studentService->countTotalQuestions($userData->id);
        $activity = $this->teacherService->getActivity($activityId);

        if (!$activity) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'activity not found.'
            ],404);
        }

        $passingScore = $this->utils->getPassingScore($totalQuestions, $activity['minPassingRate']);
        $maxPassingScore = $this->utils->getPassingScore($totalQuestions, $activity['maxPassingRate']);
        $totalCorrectAnswers = $this->studentService->countCorrectAnswers($userData->id, $activityId);
        $activityStatus = $totalCorrectAnswers >= $passingScore ? ResultEnums::PASSED->value : ResultEnums::FAILED->value;

        $this->studentService->evaluateActivityResult($userData->id, $activityId, $activityStatus);

        if ($totalCorrectAnswers >= $maxPassingScore) {
            $filePath = ResultEnums::ACTIVITY_AWARD_PATH->value;
            $description = ResultEnums::ACTIVITY_AWARD_DESCRIPTION->value;

            $this->studentService->provideAwardToStudent($userData->id, $activityId, $filePath, $description);
        }

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Successfully finished the activity.',
            'awarded' => $totalCorrectAnswers >= $maxPassingScore ? true : false
        ],201);
    }

   public function createLessonAward()
   {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);
        $lessonId = $payload['lessonId'] ?? null;
        $filePath = ResultEnums::LESSON_AWARD_PATH->value;
        $description = ResultEnums::LESSON_AWARD_DESCRIPTION->value;

        $isLessonCompleted= $this->studentService->validateLessonCompletion($userData->id, $lessonId);

        if (!$isLessonCompleted) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'Lesson is not yet completed.',
            ],400);
        }   

        $this->studentService->createLessonAward($userData->id, $lessonId, $filePath, $description);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Successfully awarded the student for finishing the lesson.',
        ],201);
   }

    public function watchVideo()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);
        $videoId = $payload['videoId'] ?? null;
        $status = ResultEnums::IN_PROGRESS->value;
        $video = $this->studentService->getVideo($videoId);

        if (!$video) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'video not found.'
            ],404);
        }

        if ($userData->role != RoleEnums::STUDENT->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a student cannot perform such action.'
            ], 400);
        }

        $this->studentService->watchVideo($userData->id, $videoId, $status);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Successfully watched a video.'
        ], 201);
    }

    public function finishVideo()
    {
        $videoId = $_GET['videoId'] ?? null;
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $status = ResultEnums::WATCHED->value;

        $video = $this->studentService->getVideo($videoId);

        if (!$video) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'video not found.'
            ],404);
        }

        if ($userData->role != RoleEnums::STUDENT->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not a student cannot perform such action.'
            ], 400);
        }

        $this->studentService->finishVideo($userData->id, $videoId, $status);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully finished a video.'
        ], 200);

    }
}