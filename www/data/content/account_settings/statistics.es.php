<?php

	session_start();

	// Check user logged in
	if (!isset($_SESSION['user_id'])) {
		header('Location: /es/login/');
		exit;
	}

	// Check permissions
	if (!isset($_SESSION['user_can_view_statistics']) || !$_SESSION['user_can_view_statistics']) {
		header('Location: /es/restricted_area/');
		exit;
	}

?>

<section id="content">
<header>
	<hgroup>
		<h1>Configuración de cuenta</h1>
	</hgroup>
</header>
<article>
	<nav class="tabs_nav">
		<ul>
			<li><a href="/es/account_settings/session/">Sesión</a></li>
			<li><a href="/es/account_settings/password/">Contraseña</a></li>
<?php
	if (isset($_SESSION['user_can_invite']) && $_SESSION['user_can_invite']) {
		echo '<li><a href="/es/account_settings/invite/">Invitar</a></li>';
	}
	if (isset($_SESSION['user_can_share_permissions']) && $_SESSION['user_can_share_permissions']) {
		echo '<li><a href="/es/account_settings/permissions/">Permisos</a></li>';
	}
	if (isset($_SESSION['user_can_view_statistics']) && $_SESSION['user_can_view_statistics']) {
		echo '<li class="current">Estadísticas</li>';
	}
?>
		</ul>
	</nav>
	<div class="tabs_nav_div"></div>

	<p>Algunas estadísticas de la base de datos:</p>

<?php

	require_once(strstr(getcwd(), '/build', 1).'/config.php');

	$db = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
	mysqli_set_charset($db, 'utf8');

	$query = "select user from students_personal_data";
	$result = mysqli_query($db, $query);

	echo "<ul><li>Número de estudiantes por ahora: <strong>".mysqli_num_rows($result)."</strong></li></ul>";

	$query = "select * from session_log group by user";
	$result = mysqli_query($db, $query);

	$registrations[] = array();
	$N_DAYS = 14;
	date_default_timezone_set('UTC');
	while(($row =  mysqli_fetch_assoc($result))) {
		$diff = floor((strtotime(date("Y-m-d H:i:s")) - strtotime($row['date'])) / (60 * 60 * 24));
		if ($diff > $N_DAYS) {
			break;
		} else {
			if (isset($registrations[$diff])) $registrations[$diff]++;
			else $registrations[$diff] = 1;
		}
	}

	echo "<table><caption>Nuevos registros en los últimos ".$N_DAYS." días</caption>";
	echo "<thead><tr><th>Hace X días</th><th>Nuevos registros</th></tr></thead><tbody>";
	for ($i = 1; $i <= $N_DAYS; $i++) {
		echo "<tr><td>".$i."</td>";
		if (isset($registrations[$i])) echo "<td><strong>".$registrations[$i]."</strong></td></tr>";
		else echo "<td>0</td></tr>";
	}
	echo "</tbody></table>";

	mysqli_free_result($result);
	mysqli_close($db);

?>

</article>
<footer>
	<p class="section_title">Configuración de cuenta</p>
</footer>
</section>
