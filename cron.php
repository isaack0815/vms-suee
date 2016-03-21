<?php
@set_time_limit(0);
@ignore_user_abort(true);

require ( 'lib/functions.lib.php' );

if ($_GET['pw'] != $cron_pw) die('Zugriff verweigert!');

$cron = (int)$_GET['id'];

db_connect ();

# Bei jedem Aufruf auszuführende Befehle Start
## Alte Reloads löschen
db_query('DELETE FROM '.$db_prefix.'_reloads WHERE bis <= '.time().'');
## Abgelaufene Paidmails löschen
db_query('DELETE FROM '.$db_prefix.'_paidmails_empfaenger WHERE
              status = 1 || status = 2 || gueltig < '.time().'');
# Bei jedem Aufruf auszuführende Befehle Ende

$sql = db_query ('SELECT datei FROM '.$db_prefix.'_crons WHERE id = '.$cron.' LIMIT 1') or die(mysqli_error());
$result = mysqli_fetch_assoc ($sql);

if ( include ($result['datei']) ){
	db_query ('UPDATE '.$db_prefix.'_crons SET laufzeit = '.time().' WHERE id = '.$cron.' LIMIT 1') or die(mysqli_error());
	echo 'Cron gelaufen';
} else echo 'Cron nicht gelaufen';

db_close();