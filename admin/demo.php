<?php
include './language/ua.php';

// Якщо сайт вимкнений
if (defined('site_status') && site_status === 'off') {
	?>
	<!DOCTYPE html>
	<html lang="uk">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="style.css">
		<title><?= htmlspecialchars(SITE_OFF) ?></title>
	</head>
	<body>
		<div class="box">
			<h1><?= htmlspecialchars(SITE_OFF) ?></h1>
		</div>
	</body>
	</html>
	<?php
	exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="style.css">
	<title><?= htmlspecialchars(WELCOME) ?></title>
</head>
<body>
	<div class="box">
		<h1><?= htmlspecialchars(WELCOME) ?></h1>
	</div>
</body>
</html>
