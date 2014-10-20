<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Anonymous Forums">
		<meta name="author" content="Anon">

		<style>
			body {font-family: sans-serif;}
			.container {max-width: 400px;margin:auto;}
			textarea {width: 100%;}
			h1,h2,h3,h4,h5,h6 {font-weight: 300;}
			header {border-bottom: 1px solid #eee;margin-bottom: 1em;padding: 1em 0;}
			header > h1 {border-bottom: 1px solid #eee;}
			header > h1 > a {text-decoration: none;color: #666;}
			section {border-bottom: 1px solid #eee;padding: 1em 0;}
			.text-right {text-align: right;}
			.small {font-size: 75%;}
			img { max-width: 100%;max-height: 125px;}
		</style>

		<title>/wtpaf.com/ - We The People Are Fucked - Anonymous Forums</title>
	</head>

	<body>
		<div class="container">
<?php

define('ROOT',getcwd());

$subdomain = $_SERVER['HTTP_HOST'];
$subdomain = str_replace('.wtpaf.com', '', $subdomain);
$subdomain = str_replace('wtpaf.com', '', $subdomain);
$subdomain = $subdomain == 'www' ? '' : $subdomain;

class Forums {
	protected $board;
	protected $threads = array();
	protected $updated = array();

	function __construct($subdomain=''){
		if($subdomain==''){
			?>
				<header>
					<h1><a href="/">/wtpaf.com/</a></h1>
					<h2>An Anonymous Forum</h2>
				</header>
				<div class="body">
					<section>
						<p>WTPAF is an anonymous forum that is based of boards like 4chan and Kareha style boards</p>
						<p>Check out a board <a href="http://random.wtpaf.com">here</a></p>
						<p>Let Chaos Reign!</p>
					</section>
				</div>
			<?php
		}else{
			$this->board = strtolower($subdomain);
			$this->loadThreads();
		}
	}

	function loadThreads(){
		if(!is_dir(ROOT."/boards/{$this->board}/")){
			mkdir(ROOT."/boards/{$this->board}/");
			mkdir(ROOT."/boards/{$this->board}/images/");
		}

		foreach(glob(ROOT."/boards/{$this->board}/*.json") as $thread) {
			$name = str_replace('.json', '', end(explode('/', $thread)));
			$data = json_decode(file_get_contents($thread),true);
			$this->threads[$name] = $data;
			$this->updated[$data['updated']] = $name;
		}
		krsort($this->updated);
		$i = 0;
		foreach($this->updated as $thread){
			$i++;
			if($i >= 15 && file_exists(ROOT."/boards/{$this->board}/{$thread}.json")){
				unlink(ROOT."/boards/{$this->board}/{$thread}.json");
			}
		}
	}

	function imageUpload($vars){
		if(isset($_FILES['image'])){
			if ($_FILES['image']["error"] > 0) {
				$error = $_FILES['image']["error"];
				return '';
			} else {
				$hash = md5_file($_FILES['image']['tmp_name']);
				$ext = end(explode('.', basename($_FILES['image']['name'])));
				$size = filesize($_FILES['image']['tmp_name']);
				$name = basename($_FILES['image']['name']);
				$image = ROOT."/boards/{$this->board}/images/$hash.$ext";
				if($ext=='jpg' || $ext=='png' || $ext=='jpeg' || $ext=='gif'){
					move_uploaded_file($_FILES['image']['tmp_name'], $image);
					return str_replace(ROOT, '', $image);
				}else{
					return '';
				}
			}
		}else{
			return '';
		}
	}

	function board() {
		?>
			<header>
				<h1><a href="/">/<?php echo $this->board; ?>/</a></h1>
				<form method="post" enctype="multipart/form-data">
					<textarea id="post" rows="3" name="post"></textarea><br>
					<input type="file" name="image" id="image" class="form-control" accept="image/*"><br>
					<input type="submit" value="Post" class="pure-button pure-button-primary">
				</form>
			</header>
			<div class="body">
				<?php foreach($this->updated as $thread){ ?>
					<section>
						<div class="body">
							<?php if($this->threads[$thread]['image']!='') { ?>
								<div>
									<a href="<?php echo $this->threads[$thread]['image']; ?>">
										<img src="<?php echo $this->threads[$thread]['image']; ?>">
									</a>
								</div>
							<?php } ?>
							<?php echo $this->threads[$thread]['post']; ?>
						</div>
						<div class="small text-right">
							<?php echo count($this->threads[$thread]['posts']); ?> Replies
							&nbsp; | &nbsp;
							<?php echo date("Y-m-d H:i:s", $this->threads[$thread]['time']); ?> EST
							&nbsp; | &nbsp;
							<a href="/<?php echo $thread; ?>">View</a>
 						</div>
					</section>
				<?php  } ?>
			</div>
		<?php
	}

	function newThread() {
		$vars = $this->clean($_REQUEST);
		if($vars['post']!=''){
			$name = time();
			while(file_exists(ROOT."/boards/{$this->board}/$name.json")){
				$name++;
			}
			$post = array(
				'time' => time(),
				'post' => $vars['post'],
				'updated' => time(),
				'posts' => array(),
				'image' => $this->imageUpload($vars)
			);
			file_put_contents(ROOT."/boards/{$this->board}/$name.json", json_encode($post));
			$this->loadThreads();
		}
		$this->board();
	}

	function viewThread($threadID){
		$thread = $this->threads[$threadID];
		?>
			<header>
				<h1><a href="/">/<?php echo $this->board; ?>/</a></h1>
				<div class="body">
					<?php if($thread['image']!='') { ?>
						<div>
							<a href="<?php echo $thread['image']; ?>">
								<img src="<?php echo $thread['image']; ?>">
							</a>
						</div>
					<?php } ?>
					<?php echo $thread['post'] ?>
				</div>
				<div class="small text-right"><?php echo date("Y-m-d H:i:s", $thread['time']); ?></div>
			</header>
			<div class="body">
				<?php foreach($thread['posts'] as $post){ ?>
					<section>
						<div class="body">
							<?php if($post['image']!='') { ?>
								<div>
									<a href="<?php echo $post['image']; ?>">
										<img src="<?php echo $post['image']; ?>">
									</a>
								</div>
							<?php } ?>
							<?php echo $post['post']; ?>
						</div>
						<div class="small text-right">
							<?php echo date("Y-m-d H:i:s", $post['time']); ?> EST
						</div>
					</section>
				<?php } ?>
				<form method="post" enctype="multipart/form-data">
					<textarea id="post" rows="3" name="post"></textarea><br>
					<input type="file" name="image" id="image" class="form-control" accept="image/*"><br>
					<input type="submit" value="Post" class="pure-button pure-button-primary">
				</form>
			</div>
		<?php
	}

	function updateThread($threadID){
		$vars = $this->clean($_REQUEST);

		if($vars['post']!=''){
			$thread = $this->threads[$threadID];
			$post = array(
				'time' => time(),
				'post' => $vars['post'],
				'image' => $this->imageUpload($vars)
			);
			$thread['posts'][] = $post;
			$thread['updated'] = time();
			file_put_contents(ROOT."/boards/{$this->board}/$threadID.json", json_encode($thread));
			$this->loadThreads();
		}
		$this->viewThread($threadID);
	}

	function clean($var){
		if(is_array($var))
			return $this->cleanArray($var);
		else
			return $this->cleanString($var);
	}

	function cleanArray($array){
		if(!is_array($array))
			return $array;
		foreach($array as $key=>$value){
			if(is_array($value))
				$array[$key] = $this->cleanArray($value);
			else
				$array[$key] = $this->cleanString($value);
		}
		return $array;
	}

	function cleanString($string){
		$string = strip_tags($string);
		$string = str_replace("\n", '<br>', $string);
		$string = preg_replace('/[[:^print:]]/', '', $string);
		$string = trim($string);
		return $string;
	}
}


class Router {
	protected $uri;
	protected $method;
	protected $match = false;

	function __construct(){
		$this->uri = array_shift(explode("?", $_SERVER['REQUEST_URI']));
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
	}

	public function __call($method, $arguments){
		if(strtolower($method) != $this->method) return;

		$match = preg_replace("/:([^\/.]*)/", "(.[^/]*)", $arguments[0]);
		$match = str_replace("/", "\/", $match);
		$total = preg_match("/^".$match."$/", $this->uri, $matches);

		if($total==0) return;
		if($this->match) return;

		array_shift($matches);
		$this->match = true;
		call_user_func_array($arguments[1],$matches);
	}

	public function notFound($func){
		if(!$this->match)
			call_user_func_array($func,[]);
	}
}

$forums = new Forums($subdomain);
$router = new Router();

if($subdomain!=''){
	$router->post('/:threadID', function($threadID) use ($forums){ $forums->updateThread($threadID); });
	$router->get('/:threadID', function($threadID) use ($forums){ $forums->viewThread($threadID); });
	$router->post('/', function() use ($forums){ $forums->newThread(); });
	$router->get('/', function() use ($forums){ $forums->board(); });
}

?>
			<footer>
				<p>&copy; 2014 <a href="http://wtpaf.com">WTPAF</a></p>
			</footer>
		</div>
	</body>
</html>
