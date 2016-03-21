<?php
require_once ('lib/functions.lib.php');

//Variablen vordefinieren
if (!isset($headmsg))		       $headmsg			= "";
if (!isset($wait))		       $wait			= "";
if (!isset($_GET['auszahlen']))	       $_GET['auszahlen']	= "false";

//Tan / UID Sichern
$_GET['uid'] = (int)$_GET['uid'];
$_GET['tan'] = addslashes ($_GET['tan']);

//DB Verbindung herstellen
db_connect();

$mail =  mysqli_fetch_assoc(db_query("SELECT e.start,e.aufendhalt,e.status,e.uid,v.verdienst FROM ".$db_prefix."_paidmails_empfaenger e
                                                                              LEFT JOIN ".$db_prefix."_paidmails_versendet v ON v.tan = e.tan
                                                                              WHERE e.tan='".$_GET['tan']."' AND e.uid=".$_GET['uid']." AND e.gueltig > ".time()." LIMIT 1"));

if ($_GET['auszahlen']!='true') {
	if (mysqli_num_rows($user_mail)) {
		if ($mail['status'] != 0) {
    		if ($mail['status'] == 1) $headmsg = 'Mail schon bestädigt!';
    		if ($mail['status'] == 2) $headmsg = 'Fakeversuch!';
		} else {
    		$headmsg = 'Bitte warte '.$mail['aufendhalt'].' Sek.!';
    		$wait = '<meta http-equiv="refresh" content="'.$mail['aufendhalt'].';url=pcheck.php?tan='.$_GET['tan'].'&auszahlen=true&uid='.$_GET['uid'].'">';
    		db_query("UPDATE ".$db_prefix."_paidmails_empfaenger SET start=".time()." WHERE tan='".$_GET['tan']."' and uid=".$_GET['uid']."");
		}
	} else  $headmsg = 'Diese Mail ist nicht für Dich!';
}else{
	if (($mail['start']+$mail['aufendhalt']-1) <= time() AND $mail['status'] == 0) {
		db_query("UPDATE ".$db_prefix."_kontodaten  SET fc_klicks = fc_klicks + 1 WHERE uid = '".$_SESSION['uid']."'"); // Hier Zusatz für Fakeschutz  
    	kontobuchung ('+',$mail['verdienst'],$mail['uid']);
    	buchungsliste (create_code(14),$mail['verdienst'],'Paidmailverdienst',$mail['uid']);
    	refumsatz ($mail['verdienst'],$mail['uid']);
    	rallysystem ($mail['uid'],'2',$mail['verdienst']);
        bilanz(0,$mail['verdienst']);
    	db_query("UPDATE ".$db_prefix."_paidmails_empfaenger SET status=1 WHERE tan='".$_GET['tan']."' and uid=".$_GET['uid']." LIMIT 1");
    	db_query("UPDATE ".$db_prefix."_paidmails_versendet SET bestaedigt=bestaedigt+1 WHERE tan='".$_GET['tan']."'  LIMIT 1");
    	$headmsg = $mail['verdienst'].' '.$waehrung.' gutgeschrieben!';
	} else {
        $headmsg = 'Wartezeit umgangen! Paidmail ungültig!';
        db_query("UPDATE ".$db_prefix."_paidmails_empfaenger SET status=2 WHERE tan='".$_GET['tan']."' and uid=".$_GET['uid']." LIMIT 1");
	}
}

echo '
<!DOCTYPE>
<html>
    <head>';
        if ($wait) echo $wait;
        echo '<link rel="stylesheet" href="/css/bframe.css" type="text/css">
    </head>
    <body bgcolor="#c0c0c0" topmargin="0" leftmargin="0">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
            <tr>
            <td align="left" width="50%"><b>'.$seitenname.' ist für den Inhalt nicht verantwortlich.</b></td>
            <td align="right" width="50%"><b>'.$headmsg.'</b>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
        </table>
    </body>
</html>';
db_close();