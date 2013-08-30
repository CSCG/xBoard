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

if(is_numeric($var[1])){ // Viewing a thread?
	$id = is_numeric($var[1]) ? $var[1] : false;
	if($id)
		viewThread($id);
	else
		buildIndex();
}elseif($var[1]=="post"){ // Making a post
	$post = array_merge($_POST, $user);
	$id = is_numeric($var[2]) ? $var[2] : time();
	submitNewPost($id, $post);
	buildIndex();
	if($var[2]!="")
		goToThread($id);
	else
		goToIndex();
}else{ // Everything else
	buildIndex();
	goToIndex();
}



?>