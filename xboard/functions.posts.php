<?php

/*
 * These are functions specific to posts.
 * They are moved to a different functions
 * file to make things easier to understand
 */

function submitNewPost($id="", $post=array()){
	global $settings,$user;

	$post = array_merge($post, $user);

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
	unset($post['recaptcha_challenge_field']);
	unset($post['recaptcha_response_field']);

	$post['useragent'] = $_SERVER['HTTP_USER_AGENT'];
	$post['userip'] = $_SERVER['REMOTE_ADDR'];
	$post['time']=time();

	if(trim($post['post'])!="" && !empty($post['post']) && $user['name']!=""){
		createNewPost($id,$post);
		goToThread($id);
	}else
		goToIndex();
}

function createNewPost($id="",$post=array()){
	global $settings,$user;

	$id = is_numeric($id) ? $id : time();

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
	file_put_contents($file, json_encode($thread));+
	buildIndex();
}

?>