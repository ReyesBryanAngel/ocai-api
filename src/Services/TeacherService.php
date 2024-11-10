<?php
namespace src\Services;
use PDO;
use src\Utils\OcaiUtilities;

class TeacherService {
    private $conn;
    private $utils;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
        $this->utils = new OcaiUtilities();
    }

    public function addLesson($userId, $lessonName, $description, $coverPic)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO lessons (
                userId,
                lessonName,
                description,
                coverPic
            ) VALUES (
                :userId,
                :lessonName,
                :description,
                :coverPic
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":lessonName", $lessonName);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":coverPic", $coverPic);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Failed inserting lesson" . $e->getMessage());
        }
    }

    public function getLessons($userId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM lessons WHERE userId = :userId";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? [];
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get lessons failed" . $e->getMessage());
        }
    }

    public function addTopic($lessonId, $topicName, $isLocked)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO topics (
                lessonId,
                topicName,
                isLocked
            ) VALUES (
                :lessonId,
                :topicName,
                :isLocked
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":lessonId", $lessonId);
            $stmt->bindParam(":topicName", $topicName);
            $stmt->bindParam(":isLocked", $isLocked);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Insert topic failed" . $e->getMessage());
        }
    }

    public function getLesson($lessonId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM lessons WHERE id = :lessonId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":lessonId", $lessonId);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get lesson failed" . $e->getMessage());
        }
    }

    public function getTopics($userId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT topics.*
                FROM topics
                JOIN lessons ON topics.lessonId = lessons.id
                WHERE lessons.userId = :userId
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get topics failed" . $e->getMessage());
        }
    }

    public function getTopic($topicId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM topics WHERE id = :topicId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":topicId", $topicId);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get topics failed" . $e->getMessage());
        }
    }

    public function addActivity($topicId, $activityName, $timeLimit)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO activities (
                topicId,
                activityName,
                timeLimit
            ) VALUES (
                :topicId,
                :activityName,
                :timeLimit
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":topicId", $topicId);
            $stmt->bindParam(":activityName", $activityName);
            $stmt->bindParam(":timeLimit", $timeLimit);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Insert activity failed" . $e->getMessage());
        }
    }

    public function getActivities($userId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT activities.*
                FROM activities
                JOIN topics ON activities.topicId = topics.id
                JOIN lessons ON topics.lessonId = lessons.id
                WHERE lessons.userId = :userId
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get activities failed" . $e->getMessage());
        }
    }

    public function addQuestions($userId, $questions) {
        try {
            $this->conn->beginTransaction();

            foreach ($questions as $questionData) {
                $question = $questionData['question'];
                $answer = $questionData['answer'];
                $activityId = $questionData['activityId'];
                $choices = $questionData['choices'] ?? [];
                
                $sqlQuestion = "INSERT INTO questions (activityId, question, answer) VALUES (:activityId, :question, :answer)";
                $stmtQuestion = $this->conn->prepare($sqlQuestion);
                $stmtQuestion->bindParam(':activityId', $activityId);
                $stmtQuestion->bindParam(':question', $question);
                $stmtQuestion->bindParam(':answer', $answer);
                $stmtQuestion->execute();
        
                $questionId = $this->conn->lastInsertId();
                $this->addChoices($choices, $questionId, $userId);
            }

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get activities failedInsert questionsError" . $e->getMessage());
        }
    }

    private function addChoices($choices, $questionId, $userId)
    {
        foreach ($choices as $choice) {
            $choiceAnswer = $choice['choice'];
            $filePath = $choice['filePath'];

            $sqlChoice = "INSERT INTO choices (userId, choice, filePath, questionId) VALUES (:userId, :choice, :filePath, :questionId)";
            $stmtChoice = $this->conn->prepare($sqlChoice);
            $stmtChoice->bindParam(':userId', $userId);
            $stmtChoice->bindParam(':choice', $choiceAnswer);
            $stmtChoice->bindParam(':filePath', $filePath);
            $stmtChoice->bindParam(':questionId', $questionId);
            $stmtChoice->execute();
        }
    }

    public function getAllQuestionsWithChoices($userId) {
        try {
            $this->conn->beginTransaction();

            $sql = "
                SELECT 
                    q.id AS questionId, 
                    q.question, 
                    q.answer, 
                    q.activityId,
                    c.id AS choiceId, 
                    c.choice,
                    c.filePath,
                    a.id AS activityId,
                    t.id AS topicId,
                    l.id AS lessonId
                FROM 
                    questions q
                JOIN 
                    activities a ON q.activityId = a.id
                JOIN 
                    topics t ON a.topicId = t.id
                JOIN 
                    lessons l ON t.lessonId = l.id
                LEFT JOIN 
                    choices c ON q.id = c.questionId AND c.userId = :userId
                WHERE 
                    l.userId = :userId
                ORDER BY 
                    q.id, c.id;
            ";
    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            $questions = [];
            foreach ($results as $row) {
                $questionId = $row['questionId'];
        
                if (!isset($questions[$questionId])) {
                    $questions[$questionId] = [
                        'questionId' => $questionId,
                        'activityId' => $row['activityId'],
                        'filePath' => $row['filePath'],
                        'question' => $row['question'],
                        'answer' => $row['answer'],
                        'choices' => []
                    ];
                }

                if ($row['choiceId'] !== null) {
                    $questions[$questionId]['choices'][] = [
                        'choiceId' => $row['choiceId'],
                        'choice' => $row['choice'],
                        'filePath' => $row['filePath'],
                    ];
                }
            }
            $this->conn->commit();
            return array_values($questions);
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get questions failed." . $e->getMessage());
        }
    }

    public function addAward($activityId, $filePath, $awardName, $criteria, $awardType)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO awards (activityId, filePath, awardName, criteria, awardType) VALUES (
                :activityId,
                :filePath,
                :awardName,
                :criteria,
                :awardType
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":activityId", $activityId);
            $stmt->bindParam(":filePath", $filePath);
            $stmt->bindParam(":awardName", $awardName);
            $stmt->bindParam(":criteria", $criteria);
            $stmt->bindParam(":awardType", $awardType);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Failed inserting award" . $e->getMessage());
        }
    }

    public function getAwards($userId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT activities.*
                FROM activities
                JOIN topics ON activities.topicId = topics.id
                JOIN lessons ON topics.lessonId = lessons.id
                WHERE lessons.userId = :userId
            ";

            $sql = "SELECT awards.*
                FROM awards
                JOIN activities ON awards.activityId = activities.id
                JOIN topics ON activities.topicId = topics.id
                JOIN lessons ON topics.lessonId = lessons.id
                WHERE lessons.userId = :userId
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get activities failed" . $e->getMessage());
        }
    }  

    public function uploadVideo($topicId, $fileName, $filePath, $fileType)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO videos (topicId, fileName, filePath, fileType) VALUES (:topicId, :fileName, :filePath, :fileType)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":topicId", $topicId);
            $stmt->bindParam(":fileName", $fileName);
            $stmt->bindParam(":filePath", $filePath);
            $stmt->bindParam(":fileType", $fileType);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Upload Video failed" . $e->getMessage());
        }
    }
    
}