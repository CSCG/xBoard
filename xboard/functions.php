<?php

/*
 * These are the functions that make the
 * site do what it is supposed to... Each
 * function is supposed to be responsible
 * for one action which keeps things a
 * little more orderly
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

function validEmail($email){
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validURL($url){
	return filter_var($url, FILTER_VALIDATE_URL);
}

function cleanString($data){
	$data = preg_replace("/[^A-Za-z0-9 -]/", '', $data);
	$data = trim($data);
	return $data;
}

function cleanText($data){
	$data = html_entity_decode($data);
	$data = htmlspecialchars_decode($data);
	$data = strip_tags($data);
	$data = trim($data);
	return $data;
}

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

function submitNewPost($id="", $post=array()){
	global $settings,$user;
	if($settings['recaptcha']){
		$resp = recaptcha_check_answer (
			$settings["privateRecaptcaha"],
			$_SERVER["REMOTE_ADDR"],
			$_POST["recaptcha_challenge_field"],
			$_POST["recaptcha_response_field"]);

		if (!$resp->is_valid)
			die ("The reCAPTCHA wasn't entered correctly. Go back and try it again. (reCAPTCHA said: " . $resp->error . ")");
	}

	if($post["email"]!="")
		die ("You are a bot");

	unset($post["email"]);
	unset($post['pass']);
	unset($post['recaptcha_challenge_field']);
	unset($post['recaptcha_response_field']);

	$post['useragent'] = $_SERVER['HTTP_USER_AGENT'];
	$post['userip'] = $_SERVER['REMOTE_ADDR'];
	$post['time']=time();

	if(trim($post['post'])!="" && !empty($post['post']) && $user['name']!="")
		newPost($id,$post);
}

function newPost($id="",$post=array()){
	global $settings,$user;

	$id = is_numeric($id) ? $id : time();

	if($settings["storageType"]=="database"){
		$postObject = R::dispense('post');
		$postObject->import($post);
		R::store($postObject);

		$threadObject = R::load('thread', $id);
		if (!$threadObject->id){
			$threadObject->id = time();
			$threadObject->count = 0;
			$threadObject->author = $user['name'];
			$threadObject->title = $post['title']!="" ? $post['title'] : substr($post['post'], 0, 150);
		}
		$threadObject->count++;
		$threadObject->ownPosts[] = $post;
		$threadObject->updated = time();
		R::store($threadObject);

	}else{
		$file = BASE."/{$settings["storageLocation"]}/{$id}.json";
		if(file_exists($file)){
			$thread = json_decode(file_get_contents($file),true);
		}else{
			$thread = array(
				'id'=>time(),
				'count'=>0,
				'title'=>($post['title']!="" ? $post['title'] : substr($post['post'], 0, 150)),
				'author'=>$user['name']);
		}
		$thread['updated'] = time();
		$thread['posts'][] = $post;
		$thread['count']++;
		file_put_contents($file, json_encode($thread));
	}
}

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

function goToIndex(){
	global $settings;
	header("Location: /{$settings["indexName"]}");
}

function goToThread($id){
	header("Location: /{$id}");
}

?>