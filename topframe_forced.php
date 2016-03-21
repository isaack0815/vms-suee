<?php
require_once ('lib/functions.lib.php');
require_once ('lib/session.lib.php');
// Variabeln definieren
if (!isset($headmsg)) $headmsg = "";
if (!isset($puk)) $puk = "";
if (!isset($force_error)) $force_error = "";
if (!isset($wartezeit)) $wartezeit = "";
if (!isset($_GET['art'])) $_GET['art'] = "";
if (!isset($_GET['puk'])) $_GET['puk'] = "";
if (!isset($_GET['auszahlen'])) $_GET['auszahlen'] = "false";
if (!isset($_SESSION['uid'])) $_SESSION['uid'] = "";
if (!isset($forced['ziel'])) $forced['ziel'] = "";
if (!isset($forced['aufendhalt'])) $forced['aufendhalt'] = "";
if (!isset($forced['tan'])) $forced['tan'] = "";

// Nur weiter wenn eingeloggt
if ($_SESSION['login'] != 'true' || $_SESSION['uid'] <= 0) die('Bitte einloggen!');

//DB Verbindung herstellen
db_connect();

// Tan sichern
$_GET['tan'] = mysqli_real_escape_string ($_GET['tan']);

// Werbedaten auslesen! Fixed
$kamp = db_query ("SELECT t1.* FROM " . $db_prefix . "_gebuchte_werbung t1
                               LEFT JOIN " . $db_prefix . "_reloads t2 ON (t1.tan=t2.tan AND (t2.uid=" . $_SESSION['uid'] . " or t2.ip='" . $ip . "') AND t2.bis > " . time() . ")
                               WHERE t1.tan = '" . $_GET['tan'] . "' AND t2.tan IS NULL AND t1.werbeart = 'forcedbanner' AND t1.menge >=1 AND t1.status = 1 AND t1.sponsor != " . $_SESSION['uid'] . " LIMIT 1");
// Reloadprüfen
if (mysqli_num_rows($kamp)) {
    $forced = mysqli_fetch_assoc($kamp);
    $wartezeit = $forced['aufendhalt'];
    $headmsg = 'Vergütung in ' . $forced['aufendhalt'] . ' Sek.!';
    if ($_GET['auszahlen'] != 'true') {
        $puk = md5($_SESSION['uid'] . $forced['aufendhalt'] . date("d.m.Y", time()) . $percode);
        $_SESSION['earlies_payout' . $_GET['tan']] = time() + $wartezeit - 1;
    }
} else {
    $headmsg = 'Banner noch im Reload!';
    $force_error = 'true';
}
// User bezahlen und Reload schreiben
if ($_GET['auszahlen'] == 'true' && $force_error != 'true' && $_GET['puk'] == md5($_SESSION['uid'] . $forced['aufendhalt'] . date("d.m.Y", time()) . $percode) && time() >= $_SESSION['earlies_payout' . $_GET['tan']]) {

    db_query("UPDATE ".$db_prefix."_kontodaten  SET klicks = klicks + 1, kv = kv + ".$forced['verdienst'].", kontostand = kontostand + ".$forced['verdienst'].", fc_klicks = fc_klicks + 1 WHERE uid = '".$_SESSION['uid']."'"); // Hier Zusatz für Fakeschutz  
    refumsatz ($forced['verdienst'], $_SESSION['uid']);
    rallysystem ($_SESSION['uid'], '1', $forced['verdienst']);
    bilanz($forced['preis'], $forced['verdienst']);
    $new_reload = time() + $forced['reload'];
    db_query("INSERT INTO " . $db_prefix . "_reloads (ip,uid,tan,bis) VALUES ('" . $ip . "'," . $_SESSION['uid'] . ",'" . $forced['tan'] . "'," . $new_reload . ")");
    db_query("UPDATE " . $db_prefix . "_gebuchte_werbung SET menge = menge - 1 WHERE tan='" . $_GET['tan'] . "'");
    $_SESSION['earlies_payout' . $_GET['tan']] = '';
    $headmsg = $forced['verdienst'] . ' ' . $waehrung . ' erhalten!';
} elseif ($_GET['auszahlen'] == 'true' && time() < $_SESSION['earlies_payout' . $_GET['tan']]) {
    $headmsg = 'Wartezeit umgangen.';
} elseif ($_GET['auszahlen'] == 'true' && $force_error != 'true') {
    $headmsg = 'Pin abgelaufen';
}

db_close();

echo'<!DOCTYPE HTML />
<html>
    <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bframe.css" type="text/css">';
    if ($wartezeit >= 0 && $_GET['auszahlen'] != 'true') echo '<meta http-equiv="refresh" content="' . $wartezeit . ';url=topframe_forced.php?auszahlen=true&tan=' . $forced['tan'] . '&puk=' . $puk . '">';
    echo '
    </head>
    <body>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
            <tr>
                <td align="left" width="50%"><b>'.$seitenname.' ist für den Inhalt nicht verantwortlich.</b></td>
                <td align="right" width="50%"><b>'.$headmsg.'</b>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </table>
    </body>
</html>';
