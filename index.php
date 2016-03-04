<?php
	
	require 'lib/simple_html_dom.php';

	if (isset($_GET['d'])) {
		$html = file_get_html('http://v2.subscene.com'.$_GET['d']);
		$dllink = $html->find('a#downloadButton',0)->href;
		$data = file_get_contents('http://subscene.com'.$dllink);
		$file_name = trim(str_replace( array('/', '.aspx'), '_', $_GET['d']), '_').'.zip';
		header("Content-Type: application/zip");
	    header("Content-Disposition: attachment; filename=$file_name");
	    header("Content-Length: " . strlen($data));
	    echo $data;
	    exit;
	}

	if (isset($_GET['title'])) {
		$title = $_GET['title'];
		$html = file_get_html('http://subscene.com/subtitles/' . $title);
		$byFilm = $html->find('div.byFilm', 0);
		if (!is_null($byFilm)) {
			$film = array();
			if (!empty($byFilm->find('div.poster img', 0)->src)) {
				$film['poster'] = $byFilm->find('div.poster img',0)->src;
			}
			$film['title'] = $byFilm->find('div.header h2', 0)->plaintext;
			$imdburl = $byFilm->find('div.header', 0)->find('a.imdb', 0)->href;
			if (!filter_var($imdburl, FILTER_VALIDATE_URL) === false) {
				$film['imdb'] = $imdburl;
			}
			$film['year'] = $byFilm->find('div.header ul li', 0)->innertext;
		}
		$content = $byFilm->find('div.content', 0);
		if(!is_null($content)){

		}
	}

	if (isset($_GET['s'])) {
		$searchquery = $_GET['s'];

		try {
			$html = file_get_html('http://subscene.com/subtitles/title?q=' . urlencode($searchquery));
			$content = $html->find('div.search-result', 0);
			if (!is_null($content)) {
				foreach ($content->find('h2') as $h2) {
					$h2->class = 'group';
					foreach ($content->find('ul') as $ul) {
						$ul->class = 'list-group';
						foreach ($ul->find('li') as $li) {
							$li->class = 'list-group-item';
							$a = $li->find('div.title a', 0);
							$a->class = 'list-group-item-heading';
							$a->href = str_replace('/subtitles/', '?title=' , $a->href );
						}
					}
				}
				$content->find('div.alternativeSearch', 0)->outertext = '';
			}
			
		} catch (Exception $e) {
			
		}
	}
/*
	if (isset($_GET['q'])) {
		$query = $_GET['q'];
		$language = array("English", "Indonesian", "Malay");
		$cookie = "subscene_sLanguageIds=44-13-50; subscene_sLanguageNames=%28Indonesian%2C%20English%2C%20Malay%29";
		$opts = array('http' => array('header'=> 'Cookie: ' . $cookie .'\r\n'));
		$context = stream_context_create($opts);

		try {

			$html = file_get_html('http://v2.subscene.com/s.aspx?q=' . urlencode($query) , false, $context);

			$content = $html->find('table.filmSubtitleList', 0);
			if (!is_null($content)) {
				$subs = array();
				foreach ($content->find('tr') as $e) {
					$td = $e->find('td', 0);					
					$a = $td->find('a.a1', 0);
					if(!is_null($a)) {
						if (in_array(trim($a->find('span', 0)->innertext), $language)) {
							$url = $a->href;
							$lang = trim($a->find('span', 0)->innertext);
							$title = trim($a->find('span', 1)->innertext);
							$uploader = trim($e->find('td.a4',0)->find('a',0)->innertext);
							$rawrating = $a->find('span', 0)->class;
							switch ($rawrating) {
								case 'r100':
									$rating = "Good";
									break;

								case 'r0':
									$rating = "Not rated";
									break;
								default:
									$rating = "Error";
									break;
							}

							$subs[] = array(
								'title' 	=> $title,
								'language'	=> $lang,
								'url'   	=> $url,
								'uploader'	=> $uploader,
								'rating'	=> $rating
							);
						}
					}					
				}	
			}

			
		} catch (Exception $e) {
			
		}
#		print_r($subs);
#		exit;
	}
*/
?>
<!DOCTYPE html>
<html lang=en>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Subtitle by mblonyox</title>
	<link rel="shortcut icon" type="image/x-icon" href="/template/favicon.ico">
	<link rel="icon" type="image/x-icon" href="/template/favicon.ico">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
</head>
<body>
	<div class="site-wrapper">
		<div class="site-wrapper-inner">
			<div class="cover-container">
				<div class="masthead clearfix">
					<div class="inner">
						<nav class="navbar navbar-fixed-top">
							<h3 class="masthead-brand">Subtitles</h3>
							<ul class="nav masthead-nav">
								<li>
									<a href="#">Home</a>
								</li>
								<li>
									<a href="#">Popular</a>
								</li>
								<li>
									<a href="#">Recent</a>
								</li>
								<li>
									<a href="#">F.A.Q</a>
								</li>
							</ul>
						</nav>
					</div>
				</div>
				<div class="inner cover">
					<form class="form-horizontal">
						<div class="form-group">
							<div class="col-sm-10">
								<input type="text" name="s" placeholder="Search..." class="form-control input-lg search-query" <?php if(isset($searchquery)) {echo 'value="'.$searchquery.'"';} ?>>
							</div>
							<button type="submit" class="btn btn-lg btn-default">Go</button>
						</div>
						<div class="form-group">
							<div class="col-sm-4">
								<label class="radio-inline">
									<input type="radio" name="l" id="langEng" value="en" checked>English
								</label>
							</div>
							<div class="col-sm-4">
								<label class="radio-inline">
									<input type="radio" name="l" id="langId" value="id">Bahasa Indonesia
								</label>
							</div>
						</div>
					</form>
					<?php
/*					if(isset($subs)) {
						if (empty($subs)) {
							echo '<hr/><h2>No subtitles found.</h2>';
						}else{							
							echo '<hr/><table class="table table-hover"><tr><th>#</th><th>Language</th><th>Release name</th><th>Rate</th><th>Author</th></tr>';
							$i = 1;
							foreach ($subs as $sub) {
								$l = $sub['language'];
								$t = $sub['title'];
								$u = $sub['url'];
								$r = $sub['rating'];
								$ul = $sub['uploader'];
								echo "<tr><td>$i</td><td>$l</td><td><a href='?d=$u'>$t</a></td><td>$r</td><td>$ul</td></tr>";
								$i++;
							}
							echo '</table>';
						}
					}
*/

					if(isset($content)) {
						echo "<hr/>";
						echo $content;
					}
					?>
				</div>
				<div class="mastfoot">
				</div>
			</div>
		</div>
	</div>
	<script src="js/jquery-2.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>