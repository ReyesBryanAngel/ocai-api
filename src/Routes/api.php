<?php
use src\Controllers\AdminController;
use src\Controllers\TeacherController;

$apiRoutes = [
    '/api/v1/addUser' => [new AdminController(), 'AddUser'],
    '/api/v1/editUser' => [new AdminController(), 'editUser'],
    '/api/v1/getUsers' => [new AdminController(), 'getUsers'],
    '/api/v1/getUser' => [new AdminController(), 'getUser'],
    '/api/v1/sendMessage' => [new AdminController(), 'sendMessage'],
    '/api/v1/sentMessages' => [new AdminController(), 'sentMessages'],
    '/api/v1/addLesson' => [new TeacherController(), 'addLesson'],
    '/api/v1/getLessons' => [new TeacherController(), 'getLessons'],
    '/api/v1/addTopic' => [new TeacherController(), 'addTopic'],
    '/api/v1/getTopics' => [new TeacherController(), 'getTopics'],
    '/api/v1/receivedMessages' => [new AdminController(), 'receivedMessages'],
    '/api/v1/deleteMessages' => [new AdminController(), 'deleteMessages'],
    '/api/v1/archiveMessages' => [new AdminController(), 'archiveMessages'],
    '/api/v1/retrieveMessages' => [new AdminController(), 'retrieveMessages']
];

