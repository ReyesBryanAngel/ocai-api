<?php
namespace src\Services;
use PDO;

class TeacherService {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
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

    public function addTopic($userId, $lessonId, $topicName)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO topics (
                userId,
                lessonId,
                topicName
            ) VALUES (
                :userId,
                :lessonId,
                :topicName
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":lessonId", $lessonId);
            $stmt->bindParam(":topicName", $topicName);
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

    public function getTopics()
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM topics";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? null;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get topics failed" . $e->getMessage());
        }
    }
}