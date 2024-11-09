<?php
namespace src\Services;

use PDO;

class AdminService {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function addUser(
        $schoolId,
        $username,
        $password,
        $photo,
        $lastName,
        $firstName,
        $middleName,
        $gender,
        $role,
        $contactNumber,
        $homeAddress,
        $isArchived,
    )
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO users (
                schoolId,
                lastName,
                firstName,
                middleName,
                gender,
                role,
                contactNumber,
                homeAddress,
                username,
                password,
                photo,
                isArchived
            ) VALUES (
                :schoolId,
                :lastName,
                :firstName,
                :middleName,
                :gender,
                :role,
                :contactNumber,
                :homeAddress,
                :username,
                :password,
                :photo,
                :isArchived
            )";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':schoolId', $schoolId);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':middleName', $middleName);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':contactNumber', $contactNumber);
            $stmt->bindParam(':homeAddress', $homeAddress);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':photo', $photo);
            $stmt->bindParam(':isArchived', $isArchived);
            $stmt->execute();

            $this->conn->commit();
            

        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Insert user failed" . $e->getMessage());
        }
    }

    public function editUser(
        $id,
        $schoolId,
        $username,
        $password,
        $photo,
        $lastName,
        $firstName,
        $middleName,
        $gender,
        $role,
        $contactNumber,
        $homeAddress,
        $isArchived,
    )
    {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE users SET 
                schoolId = :schoolId,
                lastName = :lastName,
                firstName = :firstName,
                middleName = :middleName,
                gender = :gender,
                role = :role,
                contactNumber = :contactNumber,
                homeAddress = :homeAddress,
                username = :username,
                password = :password,
                photo = :photo,
                isArchived = :isArchived
                WHERE id = :id
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':schoolId', $schoolId);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':middleName', $middleName);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':contactNumber', $contactNumber);
            $stmt->bindParam(':homeAddress', $homeAddress);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':photo', $photo);
            $stmt->bindParam(':isArchived', $isArchived);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Update user failed" . $e->getMessage());
        } 
    }

    public function getUsers()
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM users";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(mode: PDO::FETCH_ASSOC);
            
            $this->conn->commit();

            return $data ?? [];
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get users failed" . $e->getMessage());
        } 
    }

    public function getUser($id)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $data = $stmt->fetch(mode: PDO::FETCH_ASSOC);

            $this->conn->commit();

            return $data ?? [];
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Get user failed" . $e->getMessage());
        }
    }

    public function sendMessage($userId, $recipientId, $encryptedMessage)
    {
       try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO messages (
                userId,
                recipientId,
                encryptedMessage
            ) VALUES (
                :userId,
                :recipientId,
                :encryptedMessage
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId", $userId);
            $stmt->bindParam(":recipientId", $recipientId);
            $stmt->bindParam(":encryptedMessage", $encryptedMessage);
            $stmt->execute();

            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Insert message failed" . $e->getMessage());
        }
    }

    public function getSentMessages($userId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM messages WHERE userId = :userId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":userId",$userId);
            $stmt->execute();
            $data = $stmt->fetchAll(mode: PDO::FETCH_ASSOC);

            $this->conn->commit();

            return $data ?? [];
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Query sent messages failed" . $e->getMessage());
        }
    }

    public function getRecievedMessages($recipientId)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT * FROM messages WHERE recipientId = :recipientId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":recipientId",$recipientId);
            $stmt->execute();
            $data = $stmt->fetchAll(mode: PDO::FETCH_ASSOC);
            $this->conn->commit();

            return $data ?? [];
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Query received messages failed" . $e->getMessage());
        }
    }

    public function deleteMessages($userId, $messageIds)
    {
        if (empty($messageIds)) {
            throw new \Exception("No messages selected for deletion.");
        }

        try {
            $this->conn->beginTransaction();
            $placeholders = implode(',', array_fill(0, count($messageIds), '?'));

            $sql = "UPDATE messages SET isDeleted = true WHERE id IN ($placeholders) AND userId = ?";
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([...$messageIds, $userId]);
            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Error deleting messages: " . $e->getMessage());
        }
    }

    public function archiveMessages($userId, $messageIds)
    {
        if (empty($messageIds)) {
            throw new \Exception("No messages selected for deletion.");
        }

        try {
            $this->conn->beginTransaction();
            $placeholders = implode(',', array_fill(0, count($messageIds), '?'));

            $sql = "UPDATE messages SET isArchived = true WHERE id IN ($placeholders) AND userId = ?";
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([...$messageIds, $userId]);
            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Error archiving messages: " . $e->getMessage());
        }
    }

    public function retrieveMessages($userId, $messageIds)
    {
        if (empty($messageIds)) {
            throw new \Exception("No messages selected for deletion.");
        }

        try {
            $this->conn->beginTransaction();
            $placeholders = implode(',', array_fill(0, count($messageIds), '?'));

            $sql = "UPDATE messages SET isArchived = false WHERE id IN ($placeholders) AND userId = ?";
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([...$messageIds, $userId]);
            $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Error archiving messages: " . $e->getMessage());
        }
    }

}