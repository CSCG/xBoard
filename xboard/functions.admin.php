<?php

/*
 * These are the admin functions
 *
 */

function renderAdmin(){
	global $m, $settings;
	$data['threads'] = getThreads();
	$data = array_merge($data,$settings);
	echo $m->render("admin",$data);
}

function renderAdminLogin(){
	global $m, $settings;
	echo $m->render("adminLogin",$settings);
}

function adminDelete($data){
	global $settings;
	if(isset($data["post"])){
		$thread = readThread($data["thread"]);
		if($data["permanent"]){
			unset($thread["posts"][$data["post"]]);
			$thread["count"]--;
		}else{
			$oldPost = $thread["posts"][$data["post"]];
			unset($oldPost["versions"]);
			$thread["posts"][$data["post"]]["versions"][] = $oldPost;
			$thread["posts"][$data["post"]]["post"] = "This post has been delete";
			$thread["posts"][$data["post"]]["name"] = "removed";
			$thread["posts"][$data["post"]]["secret"] = "removed";
		}
		saveThread($data["thread"], $thread);
		goToAdmin("?action=view&thread={$data['thread']}");
	}elseif(isset($data["thread"])){
		unlink(BASE."/{$settings["storageLocation"]}/{$data["thread"]}.json");
		goToAdmin();
	}else{
		goToAdmin();
	}
}

function adminView($data){
	global $m, $settings;
	$data['thread'] = readThread($data["thread"]);
	$data = array_merge($data,$settings);
	echo $m->render("adminView",$data);
}

function banUser($data){
	global $settings;
	$block = BASE."/block.conf";
	$contents = file_get_contents($block);
	$contents .= "deny {$data["ip"]}; #Banned using xBoard Admin\n";
	file_put_contents($block, $contents);
	$htaccess = BASE."/.htaccess";
	$contents = file_get_contents($htaccess);
	$contents = str_replace("allow from all", "deny from {$data["ip"]}\nallow from all", $contents);
	file_put_contents($htaccess, $contents);
	echo "{$data["ip"]} has been banned.  If you are using nginx, you will need to reload the configuration before ban will be in effect";
}
?>