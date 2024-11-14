<?php
namespace src\Utils;

class OcaiUtilities {
    public function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function dd($variable) {
        echo json_encode($variable, JSON_PRETTY_PRINT);
        die();
    }

    public function getPassingScore($totalQuestions, $minPassingRate) {
        return ceil(($minPassingRate / 100) * $totalQuestions);
    }

    public function getCountsOfVideosAndActivities($videos, $activities) {
        return count(array_merge($videos, $activities));
    }    
}