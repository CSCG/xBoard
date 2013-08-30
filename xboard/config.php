<?php

/*
 * These are the site wide settings.
 * Be sure to go through and change
 * what you need to ensure your site
 * is secure and configured properly.
 *
 */

$settings = array(
	"siteName" => "WTPAF",					// Site name
	"siteFavicon" => "favicon.ico"	,		// Site favicon
	"adminPass" => "ChangeMe!",				// Admin Password. THIS NEEDS TO BE CHANGED!
	"siteSalt" => "4seIO93D4jnEj3",			// Site Salt to break hacking attempts. THIS NEEDS TO BE CHANGED!
	"siteURL" => "http://wtpaf.com",		// Site URL for redirection, etc
	"indexName" => "index.html",			// This is the generated 'front' page
	"anonymousUser" => "Anon",				// Anonymous Default Username
	"storageType" => "file",				// (file|database)
											// Database requires > MySQL 5 or > SQLite 3.6.19 or > PostgreSQL 8
	"storageLocation" => "data",			// If file, give the folder the data will be stored in
											// If database, give the pdo connection wrapper
											//"storageLocation" => "mysql:host=localhost;dbname=mydatabase",
	"databaseUser" => "root",				// Database User
	"databasePass" => "password",			// Database Password
	"baseDir" => "/srv/www/wtpaf.com",		// Base Directory
	"threadDisplay" => 20,					// Amount of threads on the home page
	"recaptcha" => false,					// Turn reCaptcha on or off (true|false)
	"publicRecaptcha" => "",
											// Public recaptcha key
	"privateRecaptcaha" => "",
											// Private recaptcha key
);

?>