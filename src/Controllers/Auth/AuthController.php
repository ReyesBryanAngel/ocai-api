<?php
namespace src\Controllers\Auth;

use src\Database\Database;
use src\Utils\OcaiUtilities;
use src\Services\JsonWebTokenService;
use PDO;

class AuthController {
    private $db;
    private $jwtService;
    private $utils;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->jwtService = new JsonWebTokenService();
        $this->utils = new OcaiUtilities();
    }

    public function login()
    {
        try {
            $payload = json_decode(file_get_contents('php://input'), true);
            $username = $payload['username'] ?? null;
            $password = $payload['password'] ?? null;

            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$result) {
                $this->utils->jsonResponse([
                    'code' => 404,
                    'status' => 'failed',
                    'message' => 'User not found.'
                ], 404);
            }

            $dataToEncrypt = array_diff_key($result, ['password' => '']);
            $storedHashedPassword = $result['password'];
            $token = $this->jwtService->generateToken($dataToEncrypt);

            if (password_verify($password, $storedHashedPassword)) {
                $this->utils->jsonResponse([
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Login Successfully.',
                    'token' => $token
                ], 200);
            } else {
                $this->utils->jsonResponse([
                    'code' => 401,
                    'status' => 'failed',
                    'message' => 'Invalid Credentials.',
                ], 401);
            }
        } catch (\Exception $e) {
            throw new \Exception("Login failed: " . $e->getMessage());
        }
    }

    public function profile()
    {
        $userData = $this->jwtService->verifyToken($this->jwtService->getBearerToken());
        
        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully get user data.',
            'data' => $userData
        ], 200);
    }

    public function logout()
    {
        $this->jwtService->verifyToken($this->jwtService->getBearerToken());

        $this->utils->jsonResponse([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully logout.',
        ], 200);

    }
    
}