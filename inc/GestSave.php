<?php 

if (isset($_SESSION['authentification']))
{
	echo Affichage_Entete($_SESSION['opensim_select']);
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

    echo '<h1>'.$osmw_index_2.'</h1>';
    echo '<div class="clearfix"></div>';

    //******************************************************
    //  Affichage page principale
    //******************************************************

    // *** Lecture Fichier Regions.ini ***
 	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/Regions.ini";	 
	if (file_exists($filename2)) {$filename = $filename2;}
	$tableauIni = parse_ini_file($filename, true);
	if ($tableauIni == FALSE) {echo '<p>Error: Reading ini file '.$filename.'</p>';}
	
	// *** Recuperation du port Http du Simulateur
	$FichierConfINIPrivate = INI_Conf_Moteur($_SESSION['opensim_select'], "address").INI_Conf_Moteur($_SESSION['opensim_select'], "DB_OS");
	$tableauIniSimu = parse_ini_file($FichierConfINIPrivate, true);
	$srvOS  = $tableauIniSimu['Network']['http_listener_port'];

	echo '<form class="form-inline" method="post" action="">';
	echo '<table class="table-condensed"><tr><td align=left >';
	echo '<button type="submit" class="btn btn-info btn-sm" value="dir" name="pageDir" '.$btnN1.'>';
	echo '<i class="glyphicon glyphicon-check"></i> '.$osmw_btn_view_save.'</button> ';
	echo '<button type="submit" class="btn btn-info btn-sm" value="inventaire" name="pageInv" '.$btnN1.'>';
	echo '<i class="glyphicon glyphicon-save"></i> '.$osmw_btn_save_inventory.'</button> ';
	echo '<button type="submit" class="btn btn-info btn-sm" value="log" name="cmdlog" '.$btnN1.'>';
    echo '<i class="glyphicon glyphicon-list-alt"></i> Log</button> ';
	echo '</td></tr></table>';
	echo '</form>';
		
	echo '<table class="table table-hover">';
	echo '<tr class="info">';
    echo '<th>Name</th>';
    echo '<th>Image</th>';
    echo '<th>Location</th>';
    echo '<th>Public IP/Host</th>';
    echo '<th>Port</th>';
    echo '<th>Action</th>';
    echo '</tr>';

	while (list($key, $val) = each($tableauIni))
	{
        $uuid = str_replace("-", "", $tableauIni[$key]['RegionUUID']);
		$ImgMap = "http://".$hostname.":".trim($srvOS)."/index.php?method=regionImage".$uuid;
        if (Test_Url($ImgMap) == false) {$ImgMap = "img/offline.jpg";}
        echo '<tr>';
        echo '<td><h5>'.$key.'</h5></td>';
		echo '<td><img  style="height: 90px;" class="img-thumbnail" alt="" src="'.$ImgMap.'"></td>';
        echo '<td><h5><span class="badge">'.$tableauIni[$key]['Location'].'</span></h5></td>';
        echo '<td><h5><span class="badge">'.$tableauIni[$key]['ExternalHostName'].'</span></h5></td>';
        echo "<td><h5><span class='badge'>".$tableauIni[$key]['InternalAddress']."</span></h5></td>";
		echo '<td><table class="table table-condensed"><tr><td>';
		echo '<form method="post" action="">';
        echo '<input type="hidden" name="backup_sim" value="1" >';
		echo '<input type="hidden" name="name_sim" value="'.$key.'">';
		echo '<div class="btn-group" role="group" aria-label="...">';		
		echo '<button type="submit" name="cmd" class="btn btn-warning btn-sm" value="Save OAR" '.$btnN2.'><i class="glyphicon glyphicon-save"></i> Save OAR</button>';
		echo '</td><td>';
		echo '<button type="submit" name="cmd" class="btn btn-warning btn-sm" value="Save XML" '.$btnN2.'><i class="glyphicon glyphicon-save"></i> Save XML</button>';
		echo '</div>';		
		echo '</form>';
		echo '</td></tr><tr><td>';
		echo '<form method="post" action="">';
        echo '<input type="hidden" name="save_terrain" value="1" >';
		echo '<input type="hidden" name="name_sim" value="'.$key.'">';
		echo '<div class="btn-group" role="group" aria-label="...">';		
		echo '<button type="submit" name="cmd" class="btn btn-success btn-sm" value="Save JPG" '.$btnN2.'><i class="glyphicon glyphicon-save"></i> Save JPG</button>';
		echo '</td><td>';
		echo '<button type="submit" name="cmd" class="btn btn-success btn-sm" value="Save RAW" '.$btnN2.'><i class="glyphicon glyphicon-save"></i> Save RAW</button>';
		echo '</div>';		
		echo '</form></td></tr>';
		echo '</table></td></tr>';
	}
	echo '</table>';	
	//################################################

	if (isset($_POST['pageDir'])=="pageDir")
    {
		$dir = "";
		$dir = INI_Conf_Moteur($_SESSION['opensim_select'], "address");

		if ($dir) {list_file(rawurldecode($dir));}

		else
		{
			echo '<div class="alert alert-danger alert-anim" role="alert">';
			echo 'Le <strong>chemin</strong> est incorrecte ...';
			echo '</div>';
		}
	}
	//################################################
	if (isset($_POST['pageInv'])=="inventaire")
    {
		echo '<h4>'.$osmw_label_info_iar_id.'</h3>';
		echo '<h5>'.$osmw_label_info_iar.'</h5>';
		
		echo '<form method="post" action="">';
		echo '<table class="table table-hover">';
		echo '<tr class="info">';
		echo '<th> Choix</th>';
		echo '<th>Firstname</th>';
		echo '<th>Lastname</th>';
		echo '<th>Password</th>';
		echo '<th>Action</th>';
		echo '</tr>';

		echo '<tr>';
		echo '<td colspan = 5>
				<div class="radio ">
				  <label><input type="radio" value="section" name="choix" checked>Option 1: Repertory name <b>"OSMWExport"</b> in your inventory</label>
				</div>
				<div class="radio">
				  <label><input type="radio" value="all" name="choix">Option 2: All inventory</label>
				</div>
			</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td><input class="form-control" type="text" name="first"></td>';
		echo '<td><input class="form-control" type="text" name="last"></td>';
		echo '<td><input class="form-control" type="password" name="pass"></td>';
		echo '<td colspan =2 >
				  <button class="btn btn-success btn-sm" type="submit" value="Save IAR" name="cmd" '.$btnN1.'>
				  <i class="glyphicon glyphicon-save"></i>  Save IAR</button>
			  </td>';
		echo '</tr>';
		
		
		echo '</table>';
		echo '</form>';
	}
	//******************************************************
    //* Selon ACTION bouton => Envoi Commande via Remote Admin 
    //******************************************************
    if (isset($_POST['cmd']))
    {
		$FichierConfINIPrivate = INI_Conf_Moteur($_SESSION['opensim_select'], "address").INI_Conf_Moteur($_SESSION['opensim_select'], "DB_OS");
		$tableauIniSimu = parse_ini_file($FichierConfINIPrivate, true);
		$RemotePort  =  $tableauIniSimu['RemoteAdmin']['port'] ;
		$access_password2  =  $tableauIniSimu['RemoteAdmin']['access_password'] ;
		
        $myRemoteAdmin = new RemoteAdmin(trim($hostname), trim($RemotePort), trim($access_password2));

        //*********************************
        // === Commande BACKUP REGION ===
        //*********************************
		$messageInfo ='<div class="alert alert-success alert-anim" role="alert">
		<strong><center>'.$osmw_label_consult_log.' ... <br> <br></center></strong>
		<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%"><span class="sr-only">85% Complete</span></div></div></div>';
		
		if ($_POST['backup_sim'])
		{
			if ($_POST['backup_sim'] == '1' && $_POST['cmd'] == 'Save OAR')
			{
				$parameters = array(
					'region_name' => $_POST['name_sim'], 
					'filename' => 'BackupOAR_'.$_POST['name_sim'].'_'.date(d_m_Y_H_i).'.oar'
				);
				$myRemoteAdmin->SendCommand('admin_save_oar', $parameters);
				echo $messageInfo ;
			}
			if ($_POST['backup_sim'] == '1' && $_POST['cmd'] == 'Save XML')
			{
				$parameters = array(
					'region_name' => $_POST['name_sim'], 
					'filename' => 'BackupXML_'.$_POST['name_sim'].'_'.date(d_m_Y_H_i).'.xml2');
				$myRemoteAdmin->SendCommand('admin_save_xml', $parameters);
				echo $messageInfo ;
			}
		}
		//*********************************
		// === Commande BACKUP TERRAIN ===
		//*********************************
		if ($_POST['save_terrain'])
		{
			if ($_POST['save_terrain'] == '1' && $_POST['cmd'] == 'Save JPG')
			{
				$parameters = array(
                'region_name' => $_POST['name_sim'], 
                'filename' => 'BackupMAP_'.$_POST['name_sim'].'_'.date(d_m_Y_h).'.jpg'
				);
				$myRemoteAdmin->SendCommand('admin_save_heightmap', $parameters);
				echo $messageInfo ;
			}
			if ($_POST['save_terrain'] == '1' && $_POST['cmd'] == 'Save RAW')
			{
				$parameters = array(
                'region_name' => $_POST['name_sim'], 
                'filename' => 'BackupMAP_'.$_POST['name_sim'].'_'.date(d_m_Y_h).'.raw'
				);
				$myRemoteAdmin->SendCommand('admin_save_heightmap', $parameters);
				echo $messageInfo ;
			}	
		}
		// Demande de telechargement
		if ($_POST['cmd'] == "download")
		{ 
            echo INI_Conf_Moteur($_SESSION['opensim_select'], "address").$_POST['name_file']."<br />";
			$a = DownloadFile(INI_Conf_Moteur($_SESSION['opensim_select'], "address").$_POST['name_file']);
			echo $messageInfo ;
        }
		// suppression d'un fichier de sauvegarde
		if ($_POST['cmd'] == "delete")
		{
			$file_delete = INI_Conf_Moteur($_SESSION['opensim_select'],"address").$_POST['name_file'];
			$cmd = 'rm "'.$file_delete.'"';
			CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
            echo '<div class="alert alert-success alert-anim" role="alert">Fichier '.$chemin.$_POST['name_file'].' '.$osmw_delete_user_ok.'<strong> OpenSim.log</strong</div>';
			echo $messageInfo ;
		}

		if ($_POST['cmd'] == "Save IAR")
		{
			if (!empty($_POST['first']) && !empty($_POST['last']) && !empty($_POST['pass']))
            {
                $fullname = $_POST['first']." ".$_POST['last'];
                
				if($_POST["choix"]=="section")
				{
					$parameters = array('command' => 'save iar '.$fullname.' /OSMWExport '.$_POST['pass'].' BackupIAR_'.$_POST['first'].'_'.$_POST['last'].'_'.date(d_m_Y_h).'.iar');
				}
				if($_POST["choix"]=="all")
				{
					$parameters = array('command' => 'save iar '.$fullname.' / '.$_POST['pass'].' BackupIAR_'.$_POST['first'].'_'.$_POST['last'].'_'.date(d_m_Y_h).'.iar');
				}
				//print_r($parameters);
                $myRemoteAdmin->SendCommand('admin_console_command', $parameters);

                echo "<div class='alert alert-success alert-anim'>";
                echo "<i class='glyphicon glyphicon-ok'></i>";
                echo " ".$osmw_label_msg_inventaire1." <strong>".$fullname."</strong>, ".$osmw_label_msg_inventaire2." ...</div>";
            }
            
            else
            {
                echo "<div class='alert alert-danger alert-anim'>";
                echo "<i class='glyphicon glyphicon-remove'></i>";
                echo " <strong>Login</strong> or <strong>Password</strong> error ...</div>";
            }
			echo $messageInfo ;
		}
	}
	
//**************************
	// COMMANDE POUR LES STATS ET LOG
	//**************************	
	if($_POST["cmdlog"]=="log")	
	{
		$fichierLog = INI_Conf_Moteur($_SESSION['opensim_select'], "address").'OpenSim.log';
		
		if (file_exists(INI_Conf_Moteur($_SESSION['opensim_select'], "address").'OpenSim.log'))
		{
			echo '<div class="alert alert-success alert-anim" role="alert">';
			echo "File exist: <strong>" .$fichierLog.'</strong>';
			echo '</div>';
		}
		else if ($_POST['cmd'])
		{
			echo '<div class="alert alert-danger alert-anim" role="alert">';
			echo "File not exist: <strong>" .$fichierLog.'</strong>';
			echo '</div>';
		}
		
		$taille_fichier = filesize($fichierLog);

		if ($taille_fichier >= 1073741824) {$taille_fichier = round($taille_fichier / 1073741824 * 100) / 100 . " Go";}
		else if ($taille_fichier >= 1048576) {$taille_fichier = round($taille_fichier / 1048576 * 100) / 100 . " Mo";}
		else if ($taille_fichier >= 1024) {$taille_fichier = round($taille_fichier / 1024 * 100) / 100 . " Ko";}
		else {$taille_fichier = $taille_fichier . " o";}

		echo '<p>'.$osmw_label_file_size.' <span class="badge">'.$taille_fichier.'</span></p>';
		
		$fcontents = file($fichierLog);
		$i = sizeof($fcontents) - 30;
		$aff = "";

		while ($fcontents[$i] != "")
		{
			$aff .= $fcontents[$i];
			$i++;
		}

		if (!$aff)
		{
			if (!$logfile) $aff = "File not exist...";
			else $aff = "File Log ".$logfile." is empty ...";
		}
		echo '<pre>'.$aff.'</pre>';

		echo '</td>';
		echo '</tr>';
		echo '</table>';		
	}
	
}
else {header('Location: index.php');}
?>
