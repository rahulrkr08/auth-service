<?php 

$app->get('/', 'HomeController:index');
$app->get('/session', 'SessionController:get_session');
$app->get('/swagger.json', 'SwaggerController:get_swagger_json');
$app->get('/swagger', 'SwaggerController:get_swagger_ui');