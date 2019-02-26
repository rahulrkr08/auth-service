<?php 

/**
 * Default routes
 */
$app->get('/', 'HomeController:index');
$app->get('/swagger.json', 'SwaggerController:get_swagger_json');
$app->get('/swagger', 'SwaggerController:get_swagger_ui');

/**
 * @OA\Parameter(
 *   parameter="code",
 *   name="code",
 *   description="Authentication code return from auth0.",
 *   @OA\Schema(
 *     type="string",
 *   ),
 *   in="query",
 *   required=true
 * )
 * 
 * @OA\Parameter(
 *   parameter="state",
 *   name="state",
 *   description="State which passed during login",
 *   @OA\Schema(
 *     type="string",
 *   ),
 *   in="query",
 *   required=true
 * )
 * @OA\Parameter(
 *   parameter="redirect_url",
 *   name="redirect_url",
 *   description="URL which user will redirect once login verify is succeed",
 *   @OA\Schema(
 *     type="string",
 *   ),
 *   in="query",
 *   required=false
 * )
 * @OA\Response(response="302", description="Redirect to auth0 login")
 * @OA\Response(response="200", description="Response user session information")
 */

/**
 * @OA\Get(
 *     path="/session",
 *     summary="Get user session",
 *     @OA\Response(response="200", ref="#/components/responses/200")
 * )
 */
$app->get('/session', 'SessionController:get_session');

/**
 * @OA\Get(
 *     path="/login",
 *     summary="Redirect to auth0 login",
 *     @OA\Parameter(ref="#/components/parameters/redirect_url"),
 *     @OA\Response(ref="#/components/responses/302")
 * )
 */
$app->get('/login', 'SessionController:login');



/**
 * @OA\Get(
 *     path="/verify",
 *     summary="Verifying authentication code",
 *     @OA\Parameter(ref="#/components/parameters/code"),
 *     @OA\Parameter(ref="#/components/parameters/state"),
 *     @OA\Response(response="302", description="Redirect to URL encoded into state"),
 *     @OA\Response(response="200", ref="#/components/responses/200")
 * )
 */
$app->get('/verify', 'SessionController:verify');
