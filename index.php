<?php 
require('bootstrap.php');

$router = new AltoRouter();
$router->setBasePath('/');
//map routes to Controller class and action methods
$router->map('GET|POST','home', array('c' => 'DefaultController', 'a' => 'indexAction'), 'home');
$router->map('GET|POST','admin', array('c' => 'DefaultController', 'a' => 'adminAction'), 'admin');
$router->map('GET|POST','register', array('c' => 'DefaultController', 'a' => 'registerAction'), 'register');

/* example routes
	$router->map('GET','/users/', array('c' => 'UserController', 'a' => 'ListAction'));
	$router->map('GET','/users/[i:id]', 'users#show', 'users_show');
	$router->map('POST','/users/[i:id]/[delete|update:action]', 'usersController#doAction', 'users_do');
*/

// match current request
$match = $router->match();
//debug
//var_dump($match);

if($match){
	$controller = new $match['target']['c'];
	$action = $match['target']['a'];
	$params = $match['params'];
	$controller->$action( $params, $router );
} else {
	$controller = new DefaultController();
	//defual action
	$controller->indexAction();
}

?>
