<?php

/*
 * These are functions specific to renders.
 * They are moved to a different functions
 * file to make things easier to understand
 */

function buildIndex(){
	global $m, $settings, $user;
	$data['threads'] = getThreads();
	$data['user'] = $user;
	$data = array_merge($data,$settings);
	$index = $m->render("index",$data);
	file_put_contents($settings["indexName"], $index);
}

function viewThread($id){
	global $m, $settings, $user;
	$data['thread'] = readThread($id);
	$data['user'] = $user;
	$data = array_merge($data,$settings);
	echo $m->render("thread",$data);
}

?>