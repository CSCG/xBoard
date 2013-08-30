<?php

/*
 * This is the authentication script.
 * It keeps track if a user specifies
 * a name for themself and a password.
 * The password will eventually let them
 * delete their own post
 *
 */

session_start();

$user = json_decode(base64_decode(urldecode($_COOKIE['session'])),true);

//Trying to hack? Lets just destroy your credentials then
if(sha1($user['name'].$user['pass'].$setting['name']) != $user['hash'])
	$user['hash'] = "";


if($user['name']=="" || getVar('name')!="")
	$user['name'] = getVar('name') ? getVar('name') : makeSalt();

if($user['pass']=="" || getVar('pass')!="")
	$user['pass'] = getVar('pass') ? getVar('pass') : makeSalt();

if($user['hash']=="" || sha1($user['name'].$user['pass'].$setting['name']) != $user['hash'])
	$user['hash'] = sha1($user['name'].$user['pass'].$setting['name']);

$_SESSION['name'] = $user['name'];
$_SESSION['pass'] = $user['pass'];

setcookie("session",base64_encode(json_encode($user)), time()+60*60*24*30);

?>