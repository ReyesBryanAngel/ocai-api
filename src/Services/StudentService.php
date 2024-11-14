<?php
namespace src\Services;
use src\Enums\RoleEnums;
use src\Utils\OcaiUtilities;
use src\Enums\ResultEnums;
use src\Services\TeacherService;
use PDO;

class StudentService {

    private $conn;
    private $utils;
    private $teacherService;
    public function __construct(PDO $conn) {
        $this->conn = $conn;
        $this->teacherService = new TeacherService($this->conn);
        $this->utils = new OcaiUtilities();
    }

    public function getSectionLessons($sectionId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM lessons WHERE sectionId = :sectionId";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":sectionId", $sectionId);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get lessons failed" . $e->getMessage());
        }
    }
    public function getLessonTopics($lessonId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM topics WHERE lessonId = :lessonId";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":lessonId", $lessonId);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get topics failed" . $e->getMessage());
        }
    }

    public function getTopicActivities($topicId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM activities WHERE topicId = :topicId";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":topicId", $topicId);

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get activities failed" . $e->getMessage());
        }
    }

    public function getTopicVideos($topicId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM videos WHERE topicId = :topicId";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":topicId", $topicId);

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get activities failed" . $e->getMessage());
        }
    }

    public function getActivityQuestions($activityId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM questions WHERE activityId = :activityId";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":activityId", $activityId);

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get questions failed" . $e->getMessage());
        }
    }

    public function answerQuestion($userId, $questionId, $activityId, $answer, $answerResult)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO studentAnswers 
            (
                userId,
                questionId,
                activityId,
                answer,
                result
            ) VALUES (
                :userId,
                :questionId,
                :activityId,
                :answer,
                :result
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":questionId", $questionId);
            $stmt->bindParam(":activityId", $activityId);
            $stmt->bindParam(":answer", $answer);
            $stmt->bindParam(":result", $answerResult);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Insert studentAnswer failed: " . $e->getMessage());
        }
    }

    public function totalStudentAnswers($userId, $activityId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT COUNT(*) as totalStudentAnswers FROM studentAnswers WHERE activityId = :activityId AND userId = :userId";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":activityId", $activityId);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data['totalStudentAnswers'] ?? [];
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get student answers failed" . $e->getMessage());
        }
    }

    public function countCorrectAnswers($userId, $activityId)
    {
        try {
            $sql = "SELECT COUNT(*) AS correctCount 
                    FROM studentAnswers 
                    WHERE userId = :userId 
                    AND activityId = :activityId 
                    AND result = 'Correct'";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":activityId", $activityId);
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['correctCount'] ?? 0;
            
        } catch (\Exception $e) {
            throw new \Exception("Count correct answers failed: " . $e->getMessage());
        }
    }

    public function getQuestion($questionId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT id, activityId, answer FROM questions WHERE id = :questionId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":questionId", $questionId);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get question failed" . $e->getMessage());
        }
    }

    public function getChoices($questionId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT choice FROM choices WHERE questionId = :questionId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":questionId", $questionId);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            array_column($data, "choice");
            $this->conn->commit();

            return array_column($data, "choice") ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get question failed" . $e->getMessage());
        }
    }

    public function evaluateActivityResult($userId, $activityId, $status)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO activityAttempts (userId, activityId, status) VALUES (:userId, :activityId, :status)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":activityId", $activityId);
            $stmt->bindParam(":status", $status);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Insert activity attempts failed" . $e->getMessage());
        }
    }

    public function watchVideo($userId, $videoId, $status)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO watchedVideos (userId, videoId, status) VALUES (:userId, :videoId, :status)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":videoId", $videoId);
            $stmt->bindParam(":status", $status);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Insert activity attempts failed" . $e->getMessage());
        }
    }

    public function finishVideo($userId, $videoId, $status)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE watchedVideos SET status = :status WHERE userId = :userId AND videoId = :videoId";
            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":videoId", $videoId);
            $stmt->bindParam(":status", $status);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Finish watching video failed" . $e->getMessage());
        }
    }

    public function getVideo($videoId)
    {
        try {
            $sql = "SELECT id FROM videos WHERE id = :videoId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":videoId", $videoId);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data ?? null;
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve a video. " . $e->getMessage());
        }
    }

    public function countTotalQuestions($userId)
    {
        try {
            $sqlStudentAnswer = "SELECT questionId FROM studentAnswers WHERE userId = :userId";
            $stmtStudentAnswer = $this->conn->prepare($sqlStudentAnswer);
            $stmtStudentAnswer->bindParam(":userId", $userId);
            $stmtStudentAnswer->execute();
            $studentAnswerData = $stmtStudentAnswer->fetch(PDO::FETCH_ASSOC);

            $questionId = $studentAnswerData['questionId'] ?? null;

            $sqlQuestions = "SELECT activityId FROM questions WHERE id = :questionId";
            $stmtQuestions = $this->conn->prepare($sqlQuestions);
            $stmtQuestions->bindParam(":questionId", $questionId);
            $stmtQuestions->execute();
            $questionData = $stmtQuestions->fetch(PDO::FETCH_ASSOC);

            $activityId = $questionData['activityId'] ?? null;   

            $sqlCountTotalQuestion = "SELECT COUNT(*) AS totalQuestions FROM questions WHERE activityId = :activityId";
            $stmtCountTotalQuestion = $this->conn->prepare($sqlCountTotalQuestion);
            $stmtCountTotalQuestion->bindParam(":activityId", $activityId);
            $stmtCountTotalQuestion->execute();
            $countResult = $stmtCountTotalQuestion->fetch(PDO::FETCH_ASSOC);

            return $countResult['totalQuestions'] ?? null;
            
        } catch (\Exception $e) {
            throw new \Exception("Get total questions failed: " . $e->getMessage());
        }
    }

    public function provideAwardToStudent($userId, $activityId, $filePath, $description)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO activityAwards (userId, activityId, filePath, description) VALUES (:userId, :activityId, :filePath, :description)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":activityId", $activityId);
            $stmt->bindParam(":filePath", $filePath);
            $stmt->bindParam(":description", $description);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Provide activity award failed." . $e->getMessage());
        }
    }
    
    public function createLessonAward($userId, $lessonId, $filePath, $description)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO lessonAwards (userId, lessonId, filePath, description) VALUES(:userId, :lessonId, :filePath, :description)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":lessonId", $lessonId);
            $stmt->bindParam(":filePath", $filePath);
            $stmt->bindParam(":description", $description);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Provide activity award failed." . $e->getMessage());
        }
    }

    public function validateLessonCompletion($userId, $lessonId)
    {
        $progressReport = $this->teacherService->getProgressReport(
            $userId,
            ResultEnums::WATCHED->value, 
            ResultEnums::PASSED->value,
            $lessonId
        );
        $totalVideos = $progressReport['totalVideos'];
        $watchedVideos = $progressReport['watchedVideos'];
        $totalActivities = $progressReport['totalActivities'];
        $passedActivities = $progressReport['passedActivities'];

        $studentProgress = $watchedVideos + $passedActivities;
        $videosAndActivties = $totalVideos + $totalActivities;

        return $studentProgress === $videosAndActivties ? true : false;
    }
}