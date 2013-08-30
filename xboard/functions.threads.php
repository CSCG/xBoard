<?php

/*
 * These are functions specific to threads.
 * They are moved to a different functions
 * file to make things easier to understand
 */

function readThread($id=""){
	global $settings;
	$id = is_numeric($id) ? $id : time();
	if($settings["storageType"]=="database"){
		$threadObject = R::load('thread', $id);
		$postsObject = $threadObject->ownPosts;

		$thread = $threadObject->export();
		$posts = R::exportAll( $postsObject );
		$thread['posts'] = $posts;
	}else{
		$file = BASE."/{$settings["storageLocation"]}/{$id}.json";
		if(file_exists($file)){
			$thread = json_decode(file_get_contents($file),true);
		}else{
			file_put_contents($file, json_encode(""));
			$thread = array();
		}
	}
	return $thread;
}

function getThreads(){
	global $settings,$var;
	if(is_numeric($var[1]))
		$start = $settings["threadDisplay"] * $var[1];
	else
		$start = 0;
	if($settings["storageType"]=="database"){
		$threads = R::getAll( "select * from threads order by 'updated' desc limit {$start},{$settings["threadDisplay"]}" );
	}else{
		if ($handle = opendir(BASE."/{$settings["storageLocation"]}/")) {
			while (false !== ($entry = readdir($handle))) {
				if(strpos($entry, ".json")!==false){
					$file = BASE."/{$settings["storageLocation"]}/{$entry}";
					$threadList[$file] = filemtime($file);
				}
			}
			closedir($handle);
		}
		arsort($threadList);
		$count = 0;
		foreach($threadList as $file => $updated){
			if($count>=$start && $count < $start + $settings["threadDisplay"]){
				$thread = json_decode(file_get_contents($file),true);
				if(empty($thread))
					unlink($file);
				else
					$threads[] = $thread;
			}
		}
	}
	return $threads;
}

?>