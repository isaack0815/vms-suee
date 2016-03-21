<?php

if (isset ($_GET['content']) && !empty ($_GET['content'])) {
    if (strpos ($_GET['content'], '../') !== false) die ('Zugriffsverletzung !');
    if ($_GET['content'] {
            0} != '/') $_GET['content'] = '/' . $_GET['content'];
}

require ('lib/functions.lib.php');

require ('lib/session.lib.php');
require ('lib/layout.lib.php');
require ('lib/extras.lib.php');
require ('lib/run.inc.php');
require ('lib/extra/mail.php');
if (!isset($_GET['content']) || empty ($_GET['content'])) $_GET['content'] = '/intern/startseite';
if (!file_exists('content' . $_GET['content'] . '.php')) $_GET['content'] = '/error/keine_seite';
if ($_GET['content'] == '/intern/startseite') @require_once ('lib/texte/alt_startseitenpopup.txt');
if ($_GET['content'] == '/betteln') @require_once ('lib/texte/alt_bettelseitenpopup.txt');
?>

<!DOCTYPE html>
<html lang='de'>
    <head>
        <title><?=$seitenname;?></title>
        <meta charset="UTF-8">
        <meta name="generator" content="VMS-SUEE">
        <meta name="Author" content="Designerscripte.net">
        <meta name="Publisher" content="Designerscripte.net">
        <meta name="Keywords" content="vms,paid4,loginscript">
        <meta name="Description" content="Verdien was Du willst">
        <meta name="Robots" content="INDEX,FOLLOW">
        <link rel="stylesheet" href="css/main.css" type="text/css">
		<script src="https://www.google.com/recaptcha/api.js"></script>  
    </head>
    <body>
        <div id="wrapper">
            <header id="header_1">
                <div id="topbar">
                    <?php if ($_SESSION['login'] != true) { ?>
                        <div id="topbar_left">
                            <ul>
                                <li><a href="?content=/intern/anmelden">Registrieren</a></li>
                                <li><a href="?content=/intern/daten">Passwort anfordern!</a></li>
                            </ul>
                        </div>
                    <?php } ?>
                    <div id="topbar_right">
                        <?php if($_SESSION['admin'] == 1){?>
                            <ul>
                                <li><a href="<?php echo $domain.'/adminforce/index.php'; ?>">Adminforce</a></li>
                                <li>
                        <?php } ?>
                        <?php if ($_SESSION['login'] != true) { ?>
                            <form  method="post">
                                <input type="Text" class ="topbar_input" name="nickname" value="<?=$_POST['nickname'];?>" placeholder="Username">
                                <input type="Password" class ="topbar_input" name="passwort" value="" placeholder="Passwort">
                                <input type="hidden" name="autologin" value="true">
                                <button type="submit" name="checkid" value="Login">Login</button>
                            </form>
                        <?php } ?>
                        <?php if($_SESSION['admin'] == 1){?>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </header>
            <header id="header_2">
                <div id="logobar">
                    <a href="<?=$domain;?>"><img src="images/logo.png" alt="<?=$seitenname;?>" id="logo"></a>
                    <div id="banner">
                        <?php @require_once ('lib/texte/alt_headerbanner.txt');?>
                    </div>
                    <br style="clear:both; font-size:0px;">
                    <div id="topnav">
                        <ul>
                            <li><a href="<?=$domain;?>">Startseite</a></li>
                            <li><a href="?content=/news">News</a></li>
                            <li><a href="?content=/intern/mediadaten">Mediadaten</a></li>
                            <li><a href="?content=/intern/agbs">AGB</a></li>
                            <li><a href="?content=/intern/faqs">FAQ</a></li>
                            <li><a href="?content=/intern/impressum">Impressum</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            <br style="clear:both; font-size:0px;">
            <div id="content">
                <div id="left">
                    <?php @include_once('lib/menue_links.php');?>
                </div>
                <div id="middle">
                    <?php 
					if(!empty($meldung)){
						if($meldung['error'] == 1){
							echo '<div id="meldungrot">'.$meldung['meldung'].'</div>';
						}else{
							echo '<div id="meldunggruen">'.$meldung['meldung'].'</div>';
						}
					}		
			require ('./content' . $_GET['content'] . '.php'); ?>
                </div>
            </div>
            <br style="clear:both; font-size:0px;">
            <div id="footer">
                <!-- Diese Seite basiert auf dem VMS1.2 von Designerscripte.net das entfernen dieses Copyrighthinweises ohne Erlaubnis zieht rechtliche Schritte mit sich -->
                &copy; by <a href="http://www.designerscripte.net" target="_new">Designerscripte.net</a>Erweitert durch <a href="http://vms1-scripte.de" target="_new">vms1-scripte.de</a>
            </div>
        </div>
    </body>
</html>
<?php
db_close();
if ($gzip_rate > 0) ob_end_flush();
?>
