<?php
	
	require 'lib/simple_html_dom.php';

	if (isset($_GET['q'])) {
		$query = $_GET['q'];
		$language = array("English", "Indonesian", "Malay");

		try {

			$html = file_get_html('http://v2.subscene.com/s.aspx?q=' . urlencode($query));

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
?>
<!DOCTYPE html>
<html lang=en>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Subtitle by mblonyox</title>
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
								<input type="text" name="q" placeholder="Search..." class="form-control input-lg search-query" <?php if(isset($query)) {echo 'value="'.$query.'"';} ?>>
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
					if(isset($subs) && !is_null($subs)) {
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