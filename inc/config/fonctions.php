<?php

    function debug($variable)    {        echo '<pre>' . print_r($variable) . '</pre>';    }

    function str_random($length)
    {
        $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }
    
    /* ************************************ */
    /* FONCTION choix du simulateur */
    /* ************************************ */
    function Select_Simulateur($simu)
    {   
        require 'inc/config/config.php';
        
        // Formulaire de choix du moteur a selectionne
        // on se connecte a MySQL
        try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
        catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }

        $reponse = $bdd->query('SELECT * FROM moteurs');

        echo '<form class="form-group" method="post" action="">';   
        echo '<div class="form-inline">';
        echo '<select class="form-control form-control" name="OSSelect">';
			while ($data = $reponse->fetch())
			{
				$sel = "";
				if ($data['id_os'] == $_SESSION['opensim_select']) {$sel = "selected";}
				echo '<option value="'.$data['id_os'].'" '.$sel.'>'.$data['name'].' '.$data['version'].'</option>';
			}
        echo'</select>';
        echo' <button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-saved"></i></button>';
        echo '</div>';
        echo'</form>';

        $reponse->closeCursor();
    }       
 
    /* ************************************ */
    /* FONCTION affichage Entete Simulateur Selectionné et Niveau de securité */
    /* ************************************ */
    function Affichage_Entete($simu)
    {       
        if (isset($_POST['OSSelect'])) {$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
		return Select_Simulateur($_SESSION['opensim_select']);
    }   

    /* ************************************ */
    /* FONCTION Defini affichage bouton en fonction du Niveau de securité */
    /* ************************************ */
    function Securite_Simulateur()
    {       
        if($_SESSION['osAutorise'] != '')
        {
            $osAutorise = explode("|", $_SESSION['osAutorise']);
            // echo count($osAutorise);
            // echo $_SESSION['osAutorise'];
            for ($i = 0; $i < count($osAutorise); $i++)
            {
                if (INI_Conf_Moteur($_SESSION['opensim_select'], "osAutorise") == $osAutorise[$i])
                {
                    $moteursOK = "OK";
                }
            }
        }
        else {$moteursOK = "NOK";}
        return $moteursOK;
    }       
    
    /* ************************************ */
    /* FONCTION Recuperation en BDD de la config de OSMW */
    /* ************************************ */
    function INI_Conf($cles, $valeur)
    {
        require 'inc/config/config.php';
        // on se connecte a MySQL
        try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
        catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }

        $reponse = $bdd->query('SELECT * FROM config');
        $data = $reponse->fetch();
        
        switch ($valeur)
        {
            default:
                $Version = "N.C";
            case "cheminAppli":
                $Version = $data['cheminAppli'];
                break;
            case "destinataire":
                $Version = $data['destinataire'];
                break;
            case "Autorized":
                $Version = $data['Autorized'];
                break;
            case "NbAutorized":
                $Version = $data['NbAutorized'];
                break;
            case "VersionOSMW":
                $Version = $data['VersionOSMW'];
                break;
            case "urlOSMW":
                $Version = $data['urlOSMW'];
                break;
            }
            $reponse->closeCursor(); 
        return $Version;
    }

    /* ************************************ */
    /* FONCTION Recuperation en BDD en fonction du simulateur sélectionné */
    /* ************************************ */
    function INI_Conf_Moteur($cles, $valeur)
    {
        require 'inc/config/config.php';
        // on se connecte a MySQL
        try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
        catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }

        $reponse = $bdd->query("SELECT * FROM moteurs WHERE id_os ='".$cles."'");
        $data = $reponse->fetch();

        $Version = "";

        switch ($valeur)
        {
            default:
                $Version = "N.C";
            case "name":
                $Version = $data['name'];
                break;
            case "version":
                $Version = $data['version'];
                break;
            case "address":
                $Version = $data['address'];
                break;
            case "DB_OS":
                $Version = $data['DB_OS'];
                break;
            case "host_simu":
                $Version = $data['host_simu'];
                break;
            case "login_host":
                $Version = $data['login_host'];
                break;              
            case "pass_host":
                $Version = $data['pass_host'];
                break;
            case "port_host":
                $Version = $data['port_host'];
                break;
            case "osAutorise":
                $Version = $data['osAutorise'];
                break;
            case "id_os":
                $Version = $data['id_os'];
                break;
            }
            $reponse->closeCursor(); 
        return $Version;
    }

    /* ************************************ */
    /* FONCTION Retourne le nombre de simulateur */
    /* ************************************ */
    function NbOpensim()
    {
        require 'inc/config/config.php';
        // On se connecte à MySQL
        try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
        catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }

        $reponse = $bdd->query("SELECT * FROM moteurs");
        $num_rows = $reponse->rowCount();
        $reponse->closeCursor(); 
        
        return $num_rows;
    }

    /* ************************************ */
    /* FONCTION generation de UUID pour region */
    /* ************************************ */
    function GenUUID()
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }

    /* ************************************ */
    /* FONCTION Test url complete pour image de la region sélectionné */
    /* ************************************ */
    function Test_Url($server)
    {
        $tab = parse_url($server);
        $tab['port'] = isset($tab['port']) ? $tab['port'] : 40;

        error_reporting(E_ERROR | E_PARSE);
        if (!fsockopen($tab['host'], $tab['port'], $errno, $errstr, 5))
        {
             return false;
        } else 
        {
             return true;
        }
    
       error_reporting(-1);
    }
    
    /* ************************************ */
    /* FONCTION Matrice pour transfert de fichiers */
    /* ************************************ */   
    function gen_matrice($cur)
    {
        global $PHP_SELF, $order, $asc, $order0;

        if ($dir = opendir($cur))
        {
            /* tableaux */
            $tab_dir = array();
            $tab_file = array();

            /* extraction */
            while($file = readdir($dir))
            {
                if (is_dir($cur."/".$file))
                {
                    if (!in_array($file, array(".", "..")))
                    {
                        $tab_dir[] = addScheme($file, $cur, 'dir');
                    }
                }
                else {$tab_file[] = addScheme($file, $cur, 'file');}
            }

            /* affichage */
            foreach($tab_file as $elem) 
            {
                if (assocExt($elem['ext']) <> 'inconnu')
                {
                    // echo "<p><input type='checkbox' name='matrice[]' value='".$elem['name']."'> ".$elem['name']."</p>";
                    echo '<div class="checkbox">';
                    echo '<label><input type="checkbox" name="matrice[]" value="'.$elem['name'].'">';
                    echo ' <i class="glyphicon glyphicon-saved text-success"></i> '.$elem['name'].' ';
                    echo '</label> ';
                    echo formatSize($elem['size']);
                    echo '</div>';
                }
            }
             closedir($dir);
        }
    }
    
    /* ************************************ */
    /* FONCTION GestDirectory.php */
    /* ************************************ */   
    /* Files List */
    function list_file($cur)
    {
        global $PHP_SELF, $order, $asc, $order0;

        if ($dir = opendir($cur))
        {
            /* tableaux */
            $tab_dir = array();
            $tab_file = array();

            /* extraction */
            while($file = readdir($dir))
            {
                if (is_dir($cur."/".$file))
                {
                    if (!in_array($file, array(".", "..")))
                    {
                        $tab_dir[] = addScheme($file, $cur, 'dir');
                    }
                }
                else {$tab_file[] = addScheme($file, $cur, 'file');}
            }

            /* affichage */
            echo "<table class='table table-condensed'>";
            echo '<tr class="info">';
            echo "<th>".(($order == 'name') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Nom</th>";
            echo "<th>".(($order == 'size') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Taille</th>";
            echo "<th>".(($order == 'date') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Date</th>";
            echo "<th>".(($order == 'time') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Time</th>";
            echo "<th>".(($order == 'ext') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Type</th>";
            echo "<th>".(($order == 'name') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Download</th>";
            echo "<th>".(($order == 'name') ? (($asc == 'a')?'/\\ ':'\\/ '):'')."Delete</th>";
            echo "</tr>";

            foreach($tab_file as $elem) 
            {
                if (assocExt($elem['ext']) <> 'inconnu')
                {
                    echo '<tr>';
                    echo '<td>';
                    echo '<h5><i class="glyphicon glyphicon-saved text-success"></i>';
                    echo ' <input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
                    echo '<input type="hidden" value="'.$elem['name'].'" name="name_file">'.$elem['name'].'';
                    echo '</h5></td>';
                    echo '<td><h5>'.formatSize($elem['size']).'</h5></td>';
                    echo '<td><h5><span class="badge">'.date("d-m-Y", $elem['date']).'</span></h5></td>';
                    echo '<td><h5><span class="badge">'.date("H:i:s a", $elem['date']).'</span></h5></td>';
                    echo '<td><h5>'.assocExt($elem['ext']).'</h5></td>';
                    echo '<td>';

                    $moteursOK = Securite_Simulateur();
                    /* ************************************ */
                    //SECURITE MOTEUR
                    $btnN1 = "disabled";$btnN2 = "disabled";$btnN3 = "disabled";
                    if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 4
                    if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 3
                    if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}              // Niv 2
                    if ($moteursOK == "OK" )
                    {
                        if($_SESSION['privilege'] == 1)
                        {$btnN1 = "";$btnN2 = "";$btnN3 = "";}
                    }
                     //SECURITE MOTEUR
                    /* ************************************ */

                    if ($_SESSION['privilege'] >= 3)
                    {
                        $action = "inc/download.php?file=".INI_Conf_Moteur($_SESSION['opensim_select'], "address").$elem['name'];
                        // $btnN3 = "";
                        echo '<form method="post" action="'.$action.'">';
                        echo '<input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
                        echo '<input type="hidden" value="'.$elem['name'].'" name="name_file">';
                        echo '<button class="btn btn-success btn-sm" type="submit" value="download" name="cmd" >';
                        echo '<i class="glyphicon glyphicon-download-alt"></i> Download</button>';
                        echo '</form>';
                        echo '<td>';
                        echo '<form method="post" action="">';
                        echo '<input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
                        echo '<input type="hidden" value="'.$elem['name'].'" name="name_file">';
                        echo '<input type="hidden" value="dir" name="pageDir">';
                        echo ' <button class="btn btn-danger btn-sm" type="submit" value="delete" name="cmd" >';
                        echo '<i class="glyphicon glyphicon-trash"></i> Delete</button>';
                        echo '</td>';
                        echo '</form>';
                    }

                    else if ($moteursOK == "OK")
                    {

                        echo '<form method="post" action="">';
                        echo '<input type="hidden" value="'.$_SESSION['opensim_select'].'" name="name_sim">';
                        echo '<input type="hidden" value="'.$elem['name'].'" name="name_file">';
                        echo '<button class="btn btn-success" type="submit" value="download" name="cmd" '.$btnN2.'>';
                        echo '<i class="glyphicon glyphicon-download-alt"></i> Download</button>';
                        echo '<td>';
                        echo ' <button class="btn btn-danger" type="submit" value="delete" name="cmd" '.$btnN2.'>';
                        echo '<i class="glyphicon glyphicon-trash"></i> Delete</button>';
                        echo '</td>';
                        echo '</form>';
                    }
                    else
                    {
                        echo '<form method="post" action="">';
                        echo '<button class="btn btn-success" type="submit" name="cmd" disabled>';
                        echo '<i class="glyphicon glyphicon-download-alt"></i> Download</button>';
                        echo '<td>';
                        echo ' <button class="btn btn-danger" type="submit" name="cmd" disabled>';
                        echo '<i class="glyphicon glyphicon-trash"></i> Delete</button>';
                        echo '</td>';
                        echo '</form>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
            closedir($dir);
        }
    }

    /* Directory List */
    function list_dir($base, $cur, $level = 0)
    {
        global $PHP_SELF, $order, $asc;

        if ($dir = opendir($base)) 
        {
            $tab = array();

            while($entry = readdir($dir)) 
            {
                if (is_dir($base."/".$entry) && !in_array($entry, array(".", "..")))
                {
                    $tab[] = addScheme($entry, $base, 'dir');
                }
            }
            /* tri */
            usort($tab, "cmp_name");
            foreach($tab as $elem) 
            {
                $entry = $elem['name'];
                /* chemin relatif a la racine */
                $file = $base."/".$entry;
                /* marge gauche */
                for ($i = 1; $i <= (4*$level); $i++) {echo "&nbsp;";}
                
                /* l'entree est-elle le dossier courant */
                if ($file == $cur)
                {
                    echo "<p><i class='glyphicon glyphicon-star'></i> $entry</p>\n";
                }

                else
                {
                    echo "<p><i class='glyphicon glyphicon-star'></i>";
                    echo " <a href=\"$PHP_SELF?dir=". rawurlencode($file) ."&order=$order&asc=$asc\">$entry</a></p>\n";
                }

                /* l'entree est-elle dans la branche dont le dossier courant est la feuille */
                if (ereg($file."/", $cur."/")) {list_dir($file, $cur, $level + 1);}
            }
            closedir($dir);
        }
    }

    /* Extract Infos */
    function addScheme($entry,$base,$type)
    {
        $tab['name']    = $entry;
        $tab['type']    = filetype($base."/".$entry);
        $tab['date']    = filemtime($base."/".$entry);
        $tab['size']    = filesize($base."/".$entry);
        $tab['perms']   = fileperms($base."/".$entry);
        $tab['access']  = fileatime($base."/".$entry);
        $exp            = explode(".", $entry);
        $tab['ext']     = $exp[count($exp) - 1];
        return $tab;
    }

    function formatSize($bytes)
{
    $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
     return "<span class='badge'>".$result."</span>";
}


    /* Formate Type */
    function assocType($type) {
      /* tableau de conversion */
      $t = array(
        'fifo'      => "file",
        'char'      => "fichier special en mode caractere",
        'dir'       => "dossier",
        'block'     => "fichier special en mode bloc",
        'link'      => "lien symbolique",
        'file'      => "fichier",
        'unknown'   => "inconnu"
      );
      return $t[$type];
    }

    /* Description des Extension */
    function assocExt($ext)
    {
        $e = array(
            ''      => "inconnu",
            'oar'   => "<i class='glyphicon glyphicon-compressed'></i> Archive OAR",
            'iar'   => "<i class='glyphicon glyphicon-briefcase'></i> Archive IAR",
            'xml2'  => "<i class='glyphicon glyphicon-compressed'></i> Archive XML",
            'jpg'   => "<i class='glyphicon glyphicon-picture'></i> Image JPG",
            'gz'    => "<i class='glyphicon glyphicon-compressed'></i> Backup GZ",
            'raw'   => "<i class='glyphicon glyphicon-picture'></i> Image RAW"
        );

        if (in_array($ext, array_keys($e)))
            return $e[$ext];
        return $e[''];
    }

    /* */
    function cmp_name($a, $b)
    {
        global $asc;
        if ($a['name'] == $b['name']) return 0;
        if ($asc == 'a') return ($a['name'] < $b['name']) ? -1 : 1;
        return ($a['name'] > $b['name']) ? -1 : 1;
    }

    /* */
    function cmp_size($a, $b)
    {
        global $asc;
        if ($a['size'] == $b['size']) return cmp_name($a, $b);
        if ($asc == 'a') return ($a['size'] < $b['size']) ? -1 : 1;
        return ($a['size'] > $b['size']) ? -1 : 1;
    }

    /* */
    function cmp_date($a, $b)
    {
        global $asc;
        if ($a['date'] == $b['date']) return cmp_name($a, $b);
        if ($asc == 'a') return ($a['date'] < $b['date']) ? -1 : 1;
        return ($a['date'] > $b['date']) ? -1 : 1;
    }

    /* */
    function cmp_access($a, $b)
    {
        global $asc;
        if ($a['access'] == $b['access']) return cmp_name($a, $b);
        if ($asc == 'a') return ($a['access'] < $b['access']) ? -1 : 1;
        return ($a['access'] > $b['access']) ? -1 : 1;
    }

    /* */
    function cmp_perms($a, $b)
    {
        global $asc;
        if ($a['perms'] == $b['perms']) return cmp_name($a, $b);
        if ($asc == 'a') return ($a['perms'] < $b['perms']) ? -1 : 1;
        return ($a['perms'] > $b['perms']) ? -1 : 1;
    }

    /* */
    function cmp_type($a, $b)
    {
        global $asc;
        if ($a['type'] == $b['type']) return cmp_name($a, $b);
        if ($asc == 'a') return ($a['type'] < $b['type']) ? -1 : 1;
        return ($a['type'] > $b['type']) ? -1 : 1;
    }

    /* */
    function cmp_ext($a, $b)
    {
        global $asc;
        if ($a['ext'] == $b['ext']) return cmp_name($a, $b);
        if ($asc == 'a') return ($a['ext'] < $b['ext']) ? -1 : 1;
        return ($a['ext'] > $b['ext']) ? -1 : 1;
    }

    /* FORMULAIRES */
    /* Fonctionp pour nettoyer et enregistrer un texte */
    function Rec($text)
    {
        $text = trim($text); // Delete white spaces after & before text
        
        if (1 === get_magic_quotes_gpc()){            $stripslashes = create_function('$txt', 'return stripslashes($txt);');        }
        else{            $stripslashes = create_function('$txt', 'return $txt;');        }

        // Magic quotes ?
        $text = $stripslashes($text);
        // Converts to string with " and ' as well
        $text = htmlspecialchars($text, ENT_QUOTES);
        $text = nl2br($text);
        return $text;
    }

    /* Fonction pour verifier la syntaxe d'un email */
    function IsEmail($email)
    {
        $pattern = "^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,7}$";
        return (eregi($pattern,$email)) ? true : false;
    }

//--------------------------------------------------------------------------------------------
    /* Fonction envoi commande SSH  */
    function CommandeSSH($hostname2,$usernameSSH2,$passwordSSH2,$commande)
    {
        if($commande <> '')
        {
            if (!function_exists("ssh2_connect")) die(" function ssh2_connect doesn't exist");
            // log in at server1.example.com on port 22
            if(!($con = ssh2_connect($hostname2, 22))){
                echo " fail: unable to establish connection\n";
            } else 
            {// try to authenticate with username root, password secretpassword
                if(!ssh2_auth_password($con,$usernameSSH2,$passwordSSH2)) {
                    echo "fail: unable to authenticate\n";
                } else {
                //echo " ok: logged in...\n";
                    if (!($stream = ssh2_exec($con, $commande ))) {
                        echo " fail: unable to execute command\n";
                    } else {
                        // collect returning data from command
                        stream_set_blocking($stream, true); $data = "";
                        while ($buf = fread($stream,4096)) 
                        {
                        $data .= $buf."\n";}
                       // echo $data;                   
                        fclose($stream);
                    }
                }
            } 
		return $data;    
        }
    }
