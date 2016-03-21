<?php  require_once ('lib/layout.lib.php'); ?>
<html>
    <head>
        <title>..:: Wartungsmodus::..</title>
        <meta name="Content-language" content="DE">
        <meta name="Page-type" content="Nicht Gewinnorientiert">
        <meta name="Robots" content="INDEX,FOLLOW">
        <link rel="stylesheet" href="/css/main.css" type="text/css">
    </head>
    <body topmargin="0" leftmargin="0">


        <?php
            // Variabeln
            $filename = 'lib/texte/wartung.txt';

            // Datei auslesen
            $fp = fopen ($filename, "r");
            $inhalt = fread ($fp, filesize ($filename));
            fclose ($fp);
            $inhalt = str_replace('\\', '', $inhalt);
        ?>
        <table width="500" align="center" height="100%">
            <tr>
                <td height="100%" align="center" valign="middle">
                    <?php
                        head("Diese Seite ist im Wartungsmodus");
                        echo nl2br($inhalt);
                        foot();
                    ?>
                </td>
            </tr>
        </table>
    </body>
</html>