<?php
function init_session()
{

	if (!session_id()) {
		session_start();
		session_regenerate_id();
		return true;
	} else {
		return false;
	}
}

function delete_session()
{
	session_unset();
	session_destroy();
}

function is_connected()
{
	if (isset($_SESSION['id']) && $_SESSION['id'] > 0) {
		return true;
	} else {
		return false;
	}
}

function checkRole()
{
	if (isset($_SESSION['role']) && !empty($_SESSION['role'])) {
		if ($_SESSION['role'] != "ADMIN") {
			echo "<script>alert('Accès refusé !');</script>";
			echo '<script> window.location="deconnexion.php"</script>';
		}
	} else {
		echo "<script>alert('Veuillez vous connecter avant de continuer !');</script>";
		echo '<script> window.location="deconnexion.php"</script>';
	}
}

function hasInvalidString(...$strings)
{
	if ($strings === null) {
		return true;
	}
	foreach ($strings as $string) {
		if ($string === null || trim($string) === '') {
			return true;
		}
	}
	return false;
}

function chechUploarDirectory($target_dir)
{
	if (!file_exists($target_dir)) {
		mkdir($target_dir, 0777, true);
	}
}
function generateRandomSerialNumber(int $val)
{
	$letter = [
		'A',
		'B',
		'C',
		'D',
		'E',
		'F',
		'G',
		'H',
		'I',
		'J',
		'K',
		'L',
		'M',
		'N',
		'O',
		'P',
		'Q',
		'R',
		'S',
		'T',
		'U',
		'V',
		'W',
		'X',
		'Y',
		'Z',
		"a",
		"b",
		"c",
		"d",
		"e",
		"f",
		"g",
		"h",
		"i",
		"j",
		"k",
		"l",
		"m",
		"n",
		"o",
		"p",
		"q",
		"r",
		"s",
		"t",
		"u",
		"v",
		"w",
		"x",
		"y",
		"z",
		'0',
		'1',
		'2',
		'3',
		'4',
		'5',
		'6',
		'7',
		'8',
		'9'
	];
	$matri = [];
	for ($i = 0; $i < $val; $i++) {
		$il = array_rand($letter);
		array_unshift($matri, $letter[$il]);
	}

	return implode("", $matri);
}
