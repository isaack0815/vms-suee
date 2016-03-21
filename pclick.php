<?php
require_once ('lib/functions.lib.php');
db_connect();

$_GET['uid'] = (int)$_GET['uid'];
$_GET['tan'] = addslashes ($_GET['tan']);

$mail = mysqli_fetch_assoc(db_query("SELECT `ziel` FROM ".$db_prefix."_paidmails_versendet WHERE tan='".$_GET['tan']."' LIMIT 1"));
echo '
<!DOCTYPE>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Paidmail by '.$seitenname.'</title>
    </head>
        <frameset rows="30,*" border="0">
            <frame name="abuse" src="pcheck.php?tan=.'.$_GET['tan'].'&uid='.$_GET['uid'].'" scrolling="no" frameborder="0">
            <frame name="werbung" src="'.$mail['ziel'].'" scrolling="auto" frameborder="0">
        </frameset>
</html>';

db_close();