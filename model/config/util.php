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

?>