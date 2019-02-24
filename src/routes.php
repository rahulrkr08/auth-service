<?php 

$app->get('/', 'HomeController:index');
$app->get('/session', 'SessionController:get_session');