<?php

/*
 * These are functions specific to threads.
 * They are moved to a different functions
 * file to make things easier to understand
 */

function readThread($id=""){
	global $settings;
	$id = is_numeric($id) ? $id : time();
	$file = BASE."/{$settings["storageLocation"]}/{$id}.json";
	if(file_exists($file)){
		$thread = json_decode(file_get_contents($file),true);
	}else{
		goToIndex();
	}
	foreach($thread["posts"] as $key=>$post){
		$thread["posts"][$key]["key"]=$key;
	}

	return $thread;
}

function saveThread($id, $thread){
	global $settings;
	$thread['posts'] = array_values($thread['posts']);
	$file = BASE."/{$settings["storageLocation"]}/{$id}.json";
	file_put_contents($file, json_encode($thread));
}

function getThreads(){
	global $settings,$var;

	$start = is_numeric($var[1]) ? $settings["threadDisplay"] * $var[1] : 0;

	if ($handle = opendir(BASE."/{$settings["storageLocation"]}/")) {
		while (false !== ($entry = readdir($handle))) {
			if(strpos($entry, ".json")!==false){
				$file = BASE."/{$settings["storageLocation"]}/{$entry}";
				$threadList[$file] = filemtime($file);
			}
		}
		closedir($handle);
	}
	if(!empty($threadList)){
		arsort($threadList);
		$count = 0;
		foreach($threadList as $file => $updated){
			if($count>=$start && $count < $start + $settings["threadDisplay"]){
				$thread = json_decode(file_get_contents($file),true);
				$threads[] = $thread;
			}
		}
	}
	return $threads;
}

?>