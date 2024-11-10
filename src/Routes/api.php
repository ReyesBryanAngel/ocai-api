<?php
use src\Controllers\AdminController;
use src\Controllers\TeacherController;
use src\Controllers\Auth\AuthController;


$apiRoutes = [
    '/api/v1/addSection' => [new AdminController(), 'addSection'],
    '/api/v1/addUser' => [new AdminController(), 'AddUser'],
    '/api/v1/editUser' => [new AdminController(), 'editUser'],
    '/api/v1/getUsers' => [new AdminController(), 'getUsers'],
    '/api/v1/getUser' => [new AdminController(), 'getUser'],
    '/api/v1/sendMessage' => [new AdminController(), 'sendMessage'],
    '/api/v1/sentMessages' => [new AdminController(), 'sentMessages'],
    
    //Message Actions
    '/api/v1/receivedMessages' => [new AdminController(), 'receivedMessages'],
    '/api/v1/deleteMessages' => [new AdminController(), 'deleteMessages'],
    '/api/v1/archiveMessages' => [new AdminController(), 'archiveMessages'],
    '/api/v1/retrieveMessages' => [new AdminController(), 'retrieveMessages'],

    //Archive Actions
    '/api/v1/archiveUser' => [new AdminController(), 'archiveUser'],
    '/api/v1/retrieveUser' => [new AdminController(), 'retrieveUser'],
    '/api/v1/archivedUsers' => [new AdminController(), 'archivedUsers'],

    // Auth
    '/api/v1/login' => [new AuthController(), 'login'],
    '/api/v1/profile' => [new AuthController(), 'profile'],
    '/api/v1/logout' => [new AuthController(), 'logout'],

    //Teacher
    //Activities
    '/api/v1/addLesson' => [new TeacherController(), 'addLesson'],
    '/api/v1/getLessons' => [new TeacherController(), 'getLessons'],
    '/api/v1/addTopic' => [new TeacherController(), 'addTopic'],
    '/api/v1/getTopics' => [new TeacherController(), 'getTopics'],
    '/api/v1/addActivity' => [new TeacherController(), 'addActivity'],
    '/api/v1/getActivities' => [new TeacherController(), 'getActivities'],
    '/api/v1/addQuestions' => [new TeacherController(), 'addQuestions'],
    '/api/v1/getQuestions' => [new TeacherController(), 'getQuestions'],
    '/api/v1/addAward' => [new TeacherController(), 'addAward'],
    '/api/v1/getAwards' => [new TeacherController(), 'getAwards'],
    '/api/v1/uploadVideo' => [new TeacherController, 'uploadVideo']
    
];

