<?php

$conection = mysql_connect('localhost', 'dev', 'dev');
mysql_select_db('parsing', $conection);

$whatPar = "SELECT * FROM articles ORDER BY id ASC";
$answerPar = mysql_query($whatPar, $conection) or die (mysql_error());
$forwardPar = mysql_num_rows($answerPar);

?>

<html>
	<head>
		<meta charset="UTF-8" />
		<title>Print parsed contents from my DB</title>
		<style type="text/css">
			body {
				font-family: "Trebuchet MS", Tahoma, Verdana;
				font-size: 12px;
				font-weight: normal;
				color: #666666;
				text-decoration: none;
				padding: 20px;
			}
			h2 {
				color: rgb(155, 0, 0);
				font-size: 18pt;
				text-align: center;
			}
			h3 {
				font-size: 11pt;
				font-weight: bold;
			}
			.noheight {
				display: block;
				width: 250px;
				clear: left;
			}
		</style>
	</head>
	<body>
		<h2>Parsing Articles - My Custom Contenct</h2>
		<?php 
			if ($forwardPar > 0) {
				while ($rowPar = mysql_fetch_assoc($answerPar)) {
					echo "<div class='noheight'>";
						echo "<div>";
							echo "<strong>ID Number</strong>: "					.	$rowPar['id']				.	"<br>";
						echo "</div>";
						echo "<a>";
							echo "<strong>Title</strong>: <h3>"					.	$rowPar['title']			.	"</h3><br>";
						echo "</a>";
						echo "<a>";
							echo "<p>";
								echo "<strong>Description</strong>: "			.	$rowPar['description']		.	"<br>";
							echo "</p>";
							echo "<strong>Image Path</strong> : <img src='"		.	$rowPar['img_path']			.	"' width='192' heigh='128' /><br>";
						echo "</a>";
						echo "<strong>Image Path</strong>: "					.	$rowPar['img_path']			.	"<br>";
						echo "<strong>Last Updated</strong>: "					.	$rowPar['cur_timestamp']	.	"<br><br>";
					echo "</div>";
				}
			}
		?>
	</body>
</html>