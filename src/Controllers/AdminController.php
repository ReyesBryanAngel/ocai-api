<?php
namespace src\Controllers;

use src\Database\Database;
use src\Services\AdminService;
use src\Utils\OcaiUtilities;

class AdminController {

    private $db;
    private $conn;
    private $adminService;

    private $utils;
    public function __construct() {
        // $this->jwt = new JsonWebToken();
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->adminService = new AdminService($this->conn);
        $this->utils = new OcaiUtilities();
    }

    public function AddUser()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        
        $schoolId = $payload['schoolId'] ?? null;

        $lastName = $payload['lastName'] ?? null;
        $firstName = $payload['firstName'] ?? null;
        $middleName = $payload['middleName'] ?? null;
        $gender = $payload['gender'] ?? null;
        $role = $payload['role'] ?? null;
        $contactNumber = $payload['contactNumber'] ?? null;
        $homeAddress = $payload['homeAddress'] ?? null;
        $username = $payload['username'] ?? null;
        $password = $payload['password'] ?? null;
        $photo = $payload['photo'] ?? null;
        // $isArchived = $payload['isArchived'] ? 1 : 0;

        $this->adminService->addUser(
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
            // $isArchived,
        );

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'User has been created.'
        ], 201);
    }

    public function editUser ()
    {
        $id = $_GET['userId'] ?? null;
        $payload = json_decode(file_get_contents('php://input'), true);

        $schoolId = $payload['schoolId'] ?? null;
        $lastName = $payload['lastName'] ?? null;
        $firstName = $payload['firstName'] ?? null;
        $middleName = $payload['middleName'] ?? null;
        $gender = $payload['gender'] ?? null;
        $role = $payload['role'] ?? null;
        $contactNumber = $payload['contactNumber'] ?? null;
        $homeAddress = $payload['homeAddress'] ?? null;
        $username = $payload['username'] ?? null;
        $password = $payload['password'] ?? null;
        $photo = $payload['photo'] ?? null;
        $isArchived = $payload['isArchived'] ? 1 : 0;

        $this->adminService->editUser(
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
        );

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'User has been updated.'
        ], 200);
    }

    public function getUsers()
    {
        $allUsers = $this->adminService->getUsers();

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'data' => $allUsers
        ], 200);
    }

    public function getUser()
    {
        $id = $_GET['userId'] ?? null;
        $userData = $this->adminService->getUser($id);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get the user.',
            'data' => $userData
        ], 200);
    }

    public function sendMessage()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $userId = $payload['userId'] ?? null;
        $userData = $this->adminService->getUser($userId);

        if (!$userData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'User not found.'
            ], 404);
        }
        
        $recipientId = $payload['recipientId'] ?? null;
        $recipientData = $this->adminService->getUser($recipientId);
        $recipientName = $recipientData['firstName'] ?? null;
        // $recipientType = $payload['recipientType'] ?? null;

        if (!$recipientData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'Recipient not found.'
            ], 404);
        }
        $encryptedMessage = $payload['encryptedMessage'] ?? null;

        $this->adminService->sendMessage($userId, $recipientId, $encryptedMessage);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => "Message has been sent to $recipientName"
        ], 201);
    }

    //ARCHIVING
    public function archiveUser ()

    {

    }

    //MESSAGES
    public function sentMessages()
    {
        $id = $_GET['userId'] ?? null;

        $sentMessages = $this->adminService->getSentMessages($id);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => "Successfully get sent messages.",
            'data' => $sentMessages
        ], 200);
    }

    public function receivedMessages()
    {
        $recipientId = $_GET['recipientId'] ?? null;

        $sentMessages = $this->adminService->getRecievedMessages($recipientId);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => "Successfully get recieved messages.",
            'data' => $sentMessages
        ], 200);
    }

    public function deleteMessages()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        $userId = $payload['userId'] ?? null;
        $messageIds = $payload['messageIds'] ?? [];
        $userData = $this->adminService->getUser($userId);

        if (!$userData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'User not found.'
            ], 404);
        }

        $this->adminService->deleteMessages($userId, $messageIds);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => "Successfully get recieved messages.",
            'data' => count($messageIds) . ' messages deleted successfully.'
        ], 200);
    }

    public function archiveMessages()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        $userId = $payload['userId'] ?? null;
        $messageIds = $payload['messageIds'] ?? [];
        $userData = $this->adminService->getUser($userId);

        if (!$userData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'User not found.'
            ], 404);
        }

        $this->adminService->archiveMessages($userId, $messageIds);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => "Successfully get recieved messages.",
            'data' => count($messageIds) . ' messages deleted successfully.'
        ], 200);
    }

    public function retrieveMessages()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        $userId = $payload['userId'] ?? null;
        $messageIds = $payload['messageIds'] ?? [];
        $userData = $this->adminService->getUser($userId);

        if (!$userData) {
            $this->utils->jsonResponse([
                'code' => 404,
                'status' => 'failed',
                'message' => 'User not found.'
            ], 404);
        }

        $this->adminService->retrieveMessages($userId, $messageIds);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => "Successfully get recieved messages.",
            'data' => count($messageIds) . ' messages deleted successfully.'
        ], 200);
    }
}