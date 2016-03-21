<?php
require_once ('lib/functions.lib.php');
db_connect();
require_once ('lib/session.lib.php');
if (!isset($_GET['art']))		$_GET['art']		= "";
if (!isset($forced['tan']))		$forced['tan']		= "";
if (!isset($forced['ziel']))	        $forced['ziel']		= "";

//Tan absichern
$_GET['tan'] = addslashes ($_GET['tan']);

$forced = mysqli_fetch_assoc(db_query("SELECT ziel,tan FROM ".$db_prefix."_gebuchte_werbung WHERE tan='".$_GET['tan']."' LIMIT 1"));

echo '<!DOCTYPE>
<html>
<head>
	<title>Forcedklick by '.$seitenname.'</title>
</head>
<frameset rows="30,*" border="0">
<frame src="topframe_forced.php?tan='.$forced['tan'].'" scrolling="no" frameborder="0">
<frame src="'.$forced['ziel'].'" scrolling="auto" frameborder="0">
</frameset>
</html>';
db_close();