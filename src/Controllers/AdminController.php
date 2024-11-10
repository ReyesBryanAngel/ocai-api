<?php
namespace src\Controllers;

use src\Database\Database;
use src\Services\AdminService;
use src\Utils\OcaiUtilities;
use src\Services\JsonWebTokenService;
use src\Utils\Enums;

class AdminController {

    private $db;
    private $conn;
    private $adminService;
    private $jwtService;
    private $utils;
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->adminService = new AdminService($this->conn);
        $this->jwtService = new JsonWebTokenService();
        $this->utils = new OcaiUtilities();
    }

    public function addSection()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);

        if ($userData->role != Enums::ADMIN->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not admin cannot perform such action.'
            ], 400);
        }

        $this->adminService->addSection($payload['sectionName'] ?? null);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'Section has been created.'
        ], 201);
    }

    public function AddUser()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);
        $encryptedPassword = password_hash($payload['password'], PASSWORD_DEFAULT);

        if ($userData->role != Enums::ADMIN->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not admin cannot perform such action.'
            ], 400);
        }

        $this->adminService->addUser(
            $payload['schoolId'] ?? null,
            $payload['username'] ?? null,
            $encryptedPassword,
            $payload['lastName'] ?? null,
            $payload['firstName'] ?? null,
            $payload['middleName'] ?? null,
            $payload['gender'] ?? null,
            $payload['role'] ?? null,
            $payload['contactNumber'] ?? null,
            $payload['homeAddress'] ?? null,
            $payload['sectionId'] ?? null,
            $payload['guardianName'] ?? null,
            $payload['guardianContact'] ?? null,
            $payload['disability'] ?? null
        );

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => 'User has been created.'
        ], 201);
    }

    public function editUser ()
    {
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());
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
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $allUsers = $this->adminService->getUsers();

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get users.',
            'data' => $allUsers
        ], 200);
    }

    public function getUser()
    {
        $jwtUser = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $id = $_GET['userId'] ?? null;
        $userData = $this->adminService->getUser($id);

        if ($jwtUser->id == $id) {
            return $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'You cannot get your own data.'
            ], 400);            
        }

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get the user.',
            'data' => $userData
        ], 200);
    }

    public function sendMessage()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $payload = json_decode(file_get_contents('php://input'), true);
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

        $this->adminService->sendMessage($userData->id, $recipientId, $encryptedMessage);

        $this->utils->jsonResponse([
            'code' => 201,
            'status' => 'success',
            'message' => "Message has been sent to $recipientName"
        ], 201);
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
        $messageIds = $payload['messageIds'] ?? [];
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        $this->adminService->deleteMessages($messageIds);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => count($messageIds) . ' messages deleted successfully.'
        ], 200);
    }

    public function archiveMessages()
    {
        $messageIds = $payload['messageIds'] ?? [];
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $this->adminService->archiveMessages($userData->id, $messageIds);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => count($messageIds) . ' messages deleted successfully.'
        ], 200);
    }

    public function retrieveMessages()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $messageIds = $payload['messageIds'] ?? [];
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        $this->adminService->retrieveMessages($userData->id, $messageIds);

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => count($messageIds) . ' messages deleted successfully.'
        ], 200);
    }

    //ARCHIVING USER
    public function archiveUser()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $userIds = $payload['userIds'] ?? [];
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $this->adminService->archiveUser($userIds);

        foreach ($userIds as $userId) {
            if ($userId === $userData->id) {
                $this->utils->jsonResponse([
                    'code' => 400,
                    'status' => 'failed',
                    'message' => 'You cannot archive yourself.'
                ], 400);
            }
        }
 
        if ($userData->role != Enums::ADMIN->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not admin cannot perform such action.'
            ], 400);
        }

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => count($userIds) . ' user archived successfully.'
        ], 200);
    }

    public function retrieveUser()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $userIds = $payload['userIds'] ?? [];
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        $this->adminService->retrieveUser($userIds);

        foreach ($userIds as $userId) {
            if ($userId === $userData->id) {
                $this->utils->jsonResponse([
                    'code' => 400,
                    'status' => 'failed',
                    'message' => 'You cannot retrieve yourself.'
                ], 400);
            }
        }
 
        if ($userData->role != Enums::ADMIN->value) {
            $this->utils->jsonResponse([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User that is not admin cannot perform such action.'
            ], 400);
        }

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => count($userIds) . ' user retrieved successfully.'
        ], 200);
    }

    public function archivedUsers()
    {
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        $allArchivedUsers = $this->adminService->archivedUsers();

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
             'message' => 'Successfully get archived users.',
            'data' => $allArchivedUsers
        ], 200);
    }
}