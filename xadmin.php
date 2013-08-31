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
include("xboard/functions.admin.php");

$loggedin = false;

if($_SESSION['adminHash']==sha1($settings['adminPass'].$settings['siteSalt']))
	$loggedin = true;

if($loggedin && $action=="delete"){
	adminDelete($_GET);
}elseif($loggedin && $action=="view"){
	adminView($_GET);
}elseif($loggedin && $action=="build"){
	buildIndex();
	renderAdmin();
}elseif($loggedin && $action=="ban"){
	banUser($_GET);
	renderAdmin();
}elseif($loggedin && $action=="logout"){
	session_destroy();
	goToIndex();
}elseif($loggedin){
	renderAdmin();
}else{
	renderAdminLogin();
}


?>