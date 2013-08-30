<?php

include('config.php');

//Lets define our base
define("BASE", $settings["baseDir"]);

$path = explode("?", $_SERVER['REQUEST_URI']);
$var = explode("/", $path[0]);

//Lets register our vendor modules
require_once(BASE."/xboard/functions.php");
require_once(BASE."/xboard/functions.threads.php");
require_once(BASE."/xboard/functions.posts.php");
require_once(BASE."/xboard/functions.renders.php");

//Lets register our templating engine
require_once(BASE."/vendor/Mustache/Autoloader.php");
Mustache_Autoloader::register();
$m = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader(BASE."/templates"),
	'partials_loader' => new Mustache_Loader_FilesystemLoader(BASE."/templates/partials")
));

//If we are using recaptcha (You shouldn't need to unless you remove the spam protection)
if($settings["recaptcha"]){
	require_once(BASE."/vendor/recaptcha/recaptchalib.php");
}

require_once(BASE."/xboard/authentication.php");

?>