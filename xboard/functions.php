<?php

/*
 * These are the functions that make the
 * site do what it is supposed to... Each
 * function is supposed to be responsible
 * for one action which keeps things a
 * little more orderly
 *
 * There are more function files that work
 * in tandom to these. They are separated
 * out to make everything easier to follow
 */

function getVar($var){
	return isset($_SESSION[$var]) && $_SESSION[$var] != "" ? $_SESSION[$var] : (isset($_COOKIE[$var]) && $_COOKIE[$var] != "" ? $_COOKIE[$var] : (isset($_REQUEST[$var]) ? $_REQUEST[$var] : false));
}

function makeSalt(){
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randstring = '';
	for ($i = 0; $i < 7; $i++){
		$randstring .= $characters[rand(0, strlen($characters)-1)];
	}
	return $randstring;
}

function goToIndex(){
	global $settings;
	header("Location: {$settings["siteURL"]}/{$settings["indexName"]}");
}

function goToThread($id){
	header("Location: {$settings["siteURL"]}/{$id}");
}

?>