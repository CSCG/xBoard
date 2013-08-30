<?php

include('config.php');

//Lets define our base
define("BASE", $settings["baseDir"]);

$path = explode("?", $_SERVER['REQUEST_URI']);
$var = explode("/", $path[0]);

//Lets register our vendor modules
require_once(BASE."/xboard/functions.php");
require_once(BASE."/vendor/Mustache/Autoloader.php");

//If we are using a db, lets setup a connection
if($settings["storageType"]=="db"){
	require_once(BASE."/vendor/storage/rb.php");
	R::setup($settings["storageLocation"],$settings["databaseUser"],$settings["databasePass"]);
}

Mustache_Autoloader::register();
$m = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader(BASE."/templates"),
	'partials_loader' => new Mustache_Loader_FilesystemLoader(BASE."/templates/partials")
));

require_once(BASE."/vendor/recaptcha/recaptchalib.php");

require_once(BASE."/xboard/authentication.php");

?>