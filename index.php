<?php
require './vendor/autoload.php';
require './src/Routes/api.php';
use src\Utils\OcaiUtilities;

$utils = new OcaiUtilities();
$request = $_SERVER['REQUEST_URI'];
$urlComponents = parse_url($request);
$path = $urlComponents['path'];
$query = isset($urlComponents['query']) ? $urlComponents['query'] : '';

$routesWithParams = [
    '/api/v1/editUser',
    '/api/v1/getUsers',
    '/api/v1/getUser',
    '/api/v1/sentMessages',
    '/api/v1/getLessons',
    '/api/v1/receivedMessages',
    '/api/v1/profile',
    '/api/v1/archivedUsers',
    '/api/v1/getTopics',
    '/api/v1/getActivities',
    '/api/v1/getQuestions',
    '/api/v1/getAwards',
    '/api/v1/getVideos',
    '/api/v1/finishVideo',
    '/api/v1/progressReport',
    '/api/v1/getSectionLessons',
    '/api/v1/getLessonTopics',
    '/api/v1/getTopicActivities',
    '/api/v1/getTopicVideos',
    '/api/v1/getActivityQuestions',
    '/api/v1/getSectionSchedule'
];

if (!array_key_exists($path, $apiRoutes)) {
    $utils->jsonResponse([
        'code' => 404,
        'status' => 'failed',
        'message' => '404 Not Found'
    ], 404);
    exit;
}

switch (true) {
    case in_array($_SERVER['REQUEST_METHOD'], ['GET', 'PUT']) && in_array($path, $routesWithParams):
        parse_str($query, $params);
        call_user_func($apiRoutes[$path]);
        break;
    case $_SERVER['REQUEST_METHOD'] === 'POST' && !in_array($path, $routesWithParams):
        call_user_func($apiRoutes[$path]);
        break;
    default:
        $utils->jsonResponse([
            'code' => 405,
            'status' => 'failed',
            'message' => 'Method Not Allowed'
        ], 405);
        break;
}