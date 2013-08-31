<?php

/*
 * This is the authentication script.
 * It keeps track if a user specifies
 * a name for themself and a secret.
 *
 * The secret allows others to identify
 * each person as the same person or not
 * no matter their name. If the user
 * tries to send in a secret the same
 * as someone else, then the system
 * will scrap the secret and make a
 * new one.
 */

session_start();

$user = json_decode(base64_decode(urldecode($_COOKIE['session'])),true);

if($user['name']=="" || (isset($_POST['name']) && $_POST['name']!=""))
	$user['name'] = isset($_POST['name']) ? $_POST['name'] : $settings['anonymousUser'];

//Trying to hack? Lets just destroy your credentials then
if(sha1($user['name'].$user['secret'].$setting['siteSalt']) != $user['hash'])
	$user['secret'] = "";

if($user['secret']=="")
	$user['secret'] = makeSalt();

if($user['hash']=="" || sha1($user['name'].$user['secret'].$setting['siteSalt']) != $user['hash'])
	$user['hash'] = sha1($user['name'].$user['secret'].$setting['siteSalt']);

$_SESSION['name'] = $user['name'];
$_SESSION['secret'] = $user['secret'];

//If the admin is trying to login... lets update the session
if(isset($_POST['adminPass']))
	$_SESSION['adminHash'] = sha1($_POST['adminPass'].$settings['siteSalt']);

setcookie("session",base64_encode(json_encode($user)), time()+60*60*24*30);

?>