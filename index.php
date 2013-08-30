<?php

/*
 * This system is not an MVC or Framework of any type.
 * It take a simple flow like approach using a decision making
 * process to figure out what it needs to do.
 *
 * If you have a problem, and are trying to debug, start at the
 * top and work your way down following the decisions
 *
 */

include("xboard/init.php");

if($var[1]=="post"){ // Making a post
	$id = is_numeric($var[2]) ? $var[2] : time();
	submitNewPost($id, $_POST);
}elseif($var[1]=="build"){ // Build the Index Page
	buildIndex();
	goToIndex();
}elseif(is_numeric($var[1])){ // Viewing a thread?
	viewThread($var[1]);
}else{ // Everything else
	goToIndex();
}



?>