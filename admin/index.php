<?php
	$admin_pass = '112233'; // Пароль для входу
	$file_path = './language/ua.php'; // Щлях до мовного файла
	$backup_path = $file_path . '.bak';
	
	session_start();
	
	// 🔐 Вхід
	if (isset($_POST['password']) && $_POST['password'] === $admin_pass) {
		$_SESSION['admin_logged_in'] = true;
		header("Location: index.php");
		exit;
	}
	
	// 🚪 Вихід
	if (isset($_GET['logout'])) {
		session_destroy();
		header("Location: index.php");
		exit;
	}
	
	// 🆕 Створення стартового мовного файлу
	if (isset($_GET['create']) && $_GET['create'] === '1') {
		if (!is_dir('./language')) {
			mkdir('./language', 0755, true);
		}
		$default = "<?php\n";
		$default .= "define('site_status', 'on');\n";
		$default .= "define('SITE_OFF', 'Сайт тимчасово недоступний!');\n";
		$default .= "define('WELCOME', 'Ласкаво просимо!');\n";
		file_put_contents($file_path, $default);
		header("Location: index.php?created=1");
		exit;
	}
	
	// 🔄 Відновлення з копії
	if (isset($_GET['restore']) && $_GET['restore'] === '1') {
		if (file_exists($backup_path)) {
			copy($backup_path, $file_path);
			header("Location: index.php?restored=1");
			exit;
		} else {
			header("Location: index.php?restored=0");
			exit;
		}
	}
	
	// 🔐 Якщо ще не увійшли
	if (!isset($_SESSION['admin_logged_in'])):
?>
<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<title>Вхід в адмінку</title>
	<link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
	<form method="post" class="login-form">
		<h2>🔐 Вхід в адмінку</h2>
		<input type="password" name="password" placeholder="Введіть пароль">
		<input type="submit" value="Увійти">
	</form>
</body>
</html>
<?php exit; endif; ?>

<?php if (!file_exists($file_path)): ?>
<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<title>Мовний файл не знайдено</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="topbar">
	<div>🛠️ Редактор ua.php</div>
	<div>
		<a href="/" target="_blank">🏠 На сайт</a>
		<a href="#" id="btn-faq">❓ FAQ</a>
		<a href="?logout=1">🚪 Вийти</a>
	</div>
</div>

<div class="container">
	<h2>⚠️ Мовний файл відсутній</h2>
	<p>Файл <code>/language/ua.php</code> не знайдено. Натисніть кнопку нижче, щоб створити базовий файл.</p>
	<a href="?create=1" class="btn-create">🆕 Створити стартовий мовний файл</a>
</div>

<!-- FAQ Modal -->
<div id="faq-modal" class="modal">
	<div class="modal-content">
		<span class="close" onclick="document.getElementById('faq-modal').style.display='none'">&times;</span>
		<div id="faq-content">Завантаження...</div>
	</div>
</div>

<script>
document.getElementById('btn-faq').addEventListener('click', function(e) {
	e.preventDefault();
	fetch('faq.html')
		.then(res => res.text())
		.then(data => {
			document.getElementById('faq-content').innerHTML = data;
			document.getElementById('faq-modal').style.display = 'block';
		});
});
</script>
</body>
</html>
<?php exit; endif; ?>

<?php
	$defines = [];
	$file_content = file_get_contents($file_path);
	preg_match_all('/define\s*\(\s*[\'"](.+?)[\'"]\s*,\s*[\'"](.*?)[\'"]\s*\)\s*;/', $file_content, $matches, PREG_SET_ORDER);
	foreach ($matches as $match) {
		$defines[$match[1]] = $match[2];
	}

	// 💾 Збереження
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$new_defines = $_POST['defines'] ?? [];
		copy($file_path, $backup_path);
		$new_content = "<?php\n";
		foreach ($new_defines as $key => $value) {
			$new_content .= "define(" . var_export($key, true) . ", " . var_export($value, true) . ");\n";
		}
		file_put_contents($file_path, $new_content);
		header("Location: index.php?saved=1");
		exit;
	}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8">
	<title>Редагування мовного файлу</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="topbar">
	<div>🛠️ Редактор ua.php</div>
	<div>
		<a href="/" target="_blank">🏠 На сайт</a>
		<a href="#" id="btn-faq">❓ FAQ</a>
		<a href="?restore=1" onclick="return confirm('Відновити останню резервну копію?')">🔄 Відновити</a>
		<a href="?logout=1">🚪 Вийти</a>
	</div>
</div>

<div class="container">
	<h2>🔧 Редагування мови</h2>

	<?php if (isset($_GET['saved'])): ?>
		<div class="saved">✅ Дані збережено. Копію оновлено.</div>
	<?php elseif (isset($_GET['created'])): ?>
		<div class="saved">🆕 Стартовий файл створено!</div>
	<?php elseif (isset($_GET['restored']) && $_GET['restored'] == '1'): ?>
		<div class="saved">♻️ Відновлено з резервної копії!</div>
	<?php elseif (isset($_GET['restored']) && $_GET['restored'] == '0'): ?>
		<div class="saved" style="color:red;">⚠️ Копію не знайдено!</div>
	<?php endif; ?>

	<form method="post">
		<?php
		if (isset($defines['site_status'])):
			$val = $defines['site_status'];
		?>
			<label>site_status:</label>
			<select name="defines[site_status]">
				<option value="on" <?= $val === 'on' ? 'selected' : '' ?>>on</option>
				<option value="off" <?= $val === 'off' ? 'selected' : '' ?>>off</option>
			</select>
		<?php unset($defines['site_status']); endif; ?>

		<?php foreach ($defines as $key => $value): ?>
			<label><?= htmlspecialchars($key) ?>:</label>
			<input type="text" name="defines[<?= htmlspecialchars($key) ?>]" value="<?= htmlspecialchars($value) ?>">
		<?php endforeach; ?>

		<input type="submit" value="💾 Зберегти зміни">
	</form>
</div>

<!-- FAQ Modal -->
<div id="faq-modal" class="modal">
	<div class="modal-content">
		<span class="close" onclick="document.getElementById('faq-modal').style.display='none'">&times;</span>
		<div id="faq-content">Завантаження...</div>
	</div>
</div>

<script>
	document.getElementById('btn-faq').addEventListener('click', function(e) {
		e.preventDefault();
		fetch('faq.html')
			.then(res => res.text())
			.then(data => {
				document.getElementById('faq-content').innerHTML = data;
				document.getElementById('faq-modal').style.display = 'block';
			});
	});
</script>

</body>
</html>
