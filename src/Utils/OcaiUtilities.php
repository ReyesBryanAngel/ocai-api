<?php
namespace src\Utils;

class OcaiUtilities {
    public function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    function dd($variable) {
        echo json_encode($variable, JSON_PRETTY_PRINT);
        die();
    }
}