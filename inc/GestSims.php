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
	
    echo '<h1>'.$osmw_index_1.'</h1>';
    echo '<div class="clearfix"></div>';
	
    //******************************************************
    /* Selon ACTION bouton => Envoi Commande via Remote Admin sauf START */
    //******************************************************
	$messageInfo = '<div class="alert alert-success alert-anim" role="alert">
	<strong><center>Consulter le fichier log / Refer to the log file.<br> <br></center></strong>
	<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%"><span class="sr-only">85% Complete</span></div></div>
	</div>';

	$FichierConfINIPrivate = INI_Conf_Moteur($_SESSION['opensim_select'], "address").INI_Conf_Moteur($_SESSION['opensim_select'], "DB_OS");
	$tableauIniSimu = parse_ini_file($FichierConfINIPrivate, true);
	$RemotePort  =  $tableauIniSimu['RemoteAdmin']['port'] ;
	$access_password2  =  $tableauIniSimu['RemoteAdmin']['access_password'] ;

	//**************************
	// EXECUTION COMMANDE SYSTEME
	//**************************
	if($_POST['cmdStart'])
	{			
		//echo PHP_OS;
		
		// WINDOWS ***********************
		//Exemple: "start /D C:\OpenSimulator\opensim_FestAvi2015_0821 OpenSim.exe"	
		//$new_chemin = str_replace("/", "\\",INI_Conf_Moteur($_SESSION['opensim_select'], "address") );			
		//$cmd ="start /D ".$new_chemin." OpenSim.exe";
		
		// LINUX ***********************
		if(PHP_OS == "Linux")
		{
			$cmd = 'tmux send-keys -t '.INI_Conf_Moteur($_SESSION['opensim_select'], "name").' "cd '.INI_Conf_Moteur($_SESSION['opensim_select'], "address").';./opensim.sh" Enter';
			CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
		}
		echo $messageInfo;
	}	
	//**************************
	// COMMANDE PAR REMOTE ADMIN (specifique)
	//**************************
	if($_POST['cmdEstate'])	
	{
		$myRemoteAdmin = new RemoteAdmin(trim($hostname), trim($RemotePort), trim($access_password2));
		$myRemoteAdmin->SendCommand('admin_estate_reload',  array());
	}
	//**************************
	// COMMANDE PAR REMOTE ADMIN (admin_console_command)
	//**************************

	if($_POST['cmd'] == 'Alerte General')	{$parameters = array('command' => 'alert '.$_POST["msg_alert"]);}
	if($_POST['cmd'] == 'Stop')				{$parameters = array('command' => 'quit'); 
			$cmd = "rm ".INI_Conf_Moteur($_SESSION['opensim_select'], "address")."OpenSim.log";
			CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);}
	if($_POST['cmd'] == 'Restart')			{$parameters = array('command' => 'restart');}
	if($_POST['cmd'] == 'Region Root')		{$parameters = array('command' => 'change region root');}
	if($_POST['cmd'] == 'Update Client')	{$parameters = array('command' => 'force update');}
	if($_POST['cmd'] == 'FCache Assets')	{$parameters = array('command' => 'fcache assets');}
	if($_POST['cmd'] == 'FCache ClearF')	{$parameters = array('command' => 'fcache clear file');}		
	if($_POST['cmd'] == 'Generate Map')		{$parameters = array('command' => 'generate map');}
	if($_POST['cmd'] == 'Windlight enable')	{$parameters = array('command' => 'windlight enable');}
	if($_POST['cmd'] == 'Windlight disable'){$parameters = array('command' => 'windlight disable');}
	if($_POST['cmd'] == 'Windlight load')	{$parameters = array('command' => 'windlight load');}
	if($_POST['cmd'] == 'Elevate')			{$parameters = array('command' => 'terrain elevate 1');}
	if($_POST['cmd'] == 'Lower')			{$parameters = array('command' => 'terrain lower 1');}
	
	if($_POST['cmd'] == 'StartLogin')		{$parameters = array('command' => 'login enable');}
	if($_POST['cmd'] == 'StopLogin')		{$parameters = array('command' => 'login disable');}
	if($_POST['cmd'] == 'StatusLogin')		{$parameters = array('command' => 'login status');}
	
	if($_POST['cmd'] == 'kick_user')		{ $kick = 'kick user '.$_POST["avatar_name"].' ejected by administrator.' ;$parameters = array('command' =>  $kick );}
	if($_POST['cmd'] == 'appearance_user')	{ $appearance = 'appearance show '.$_POST["avatar_name"] ;$parameters = array('command' =>  $appearance );}
	
	if($_POST['cmd'] == 'estate_name')		{$estate_name = 'estate set name 101 "'.$_POST["estate_name"].'"' ;$parameters = array('command' => $estate_name );}
	if($_POST['cmd'] == 'estate_owner')		{echo $estate_owner = 'estate set owner 101 '.$_POST["estate_owner"] ;$parameters = array('command' => $estate_owner );}
	
	if($_POST['cmd']<>"")
	{
		$myRemoteAdmin = new RemoteAdmin(trim($hostname), trim($RemotePort), trim($access_password2));
		$retour_radmin = $myRemoteAdmin->SendCommand('admin_console_command', $parameters);
	}	
	if($_POST['cmd'])
	{
		//debug($retour_radmin);
		echo $messageInfo;
	}
		
    //******************************************************
    //  Affichage page principale
    //******************************************************

	echo '<form class="form-inline" method="post" action="">';
	echo '<table class="table-condensed"><tr><td align=left >';

	echo '<button type="submit" class="btn btn-info btn-sm" value="section1" name="section1" '.$btnN1.'>';
	echo '<i class="glyphicon glyphicon-modal-window"></i> '.$osmw_menu_sim_section1.'</button> ';
	
	echo '<button type="submit" class="btn btn-info btn-sm" value="section2" name="section2" '.$btnN1.'>';
	echo '<i class="glyphicon glyphicon-th"></i> '.$osmw_menu_sim_section2.'</button> ';

	echo '<button type="submit" class="btn btn-info btn-sm" value="section3" name="section3" '.$btnN1.'>';
	echo '<i class="glyphicon glyphicon-globe"></i> '.$osmw_menu_sim_section3.'</button> ';
	
	echo '<button type="submit" class="btn btn-info btn-sm" value="section4" name="section4" '.$btnN1.'>';
	echo '<i class="glyphicon glyphicon-tasks"></i> '.$osmw_menu_sim_section4.'</button> ';
	

	echo '<button type="submit" class="btn btn-default btn-sm" value="log" name="cmdlog" '.$btnN1.'>';
	echo '<i class="glyphicon glyphicon-list-alt"></i> Log</button>';
		
	echo '</td></tr></table>';
	echo '</form>';
	
	//################################################
	// Section 1 : Simulateur
	//################################################
	if (isset($_POST['section1'])=="section1")
    {
		$cmd = 'tmux ls | grep '.INI_Conf_Moteur($_SESSION['opensim_select'], "name");
		$retour =  CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
		$name_tmux = explode(":",$retour);

		if ($name_tmux[0] == INI_Conf_Moteur($_SESSION['opensim_select'], "name"))
		{$check_tmux ='<a href="index.php?a=AdminSims"><p class="btn btn-success">Tmux check <i class="glyphicon glyphicon-ok-sign"></i></p></a>';}
		else{$check_tmux ='<a href="index.php?a=AdminSims"><p class="btn btn-danger">Tmux check <i class="glyphicon glyphicon-remove-sign"></i></p></a>';}
		
		echo '<form class="form-inline" method="post" action="">';
		echo '<table class="table table-hover"><tr><td align=left >';

		echo '<div class="btn-group" role="group" aria-label="...">';
		echo '<button type="submit" class="btn btn-success btn-sm" value="Start" name="cmdStart" '.$btnN3.'>';
		echo '<i class="glyphicon glyphicon-play"></i> Start</button>';
		echo '<button type="submit" class="btn btn-danger btn-sm" value="Stop" name="cmd" '.$btnN3.'>';
		echo '<i class="glyphicon glyphicon-stop"></i> Stop</button>';	
		echo '<button type="submit" class="btn btn-warning btn-sm" value="Restart" name="cmd" '.$btnN2.'>';
		echo '<i class="glyphicon glyphicon-retweet"></i> Restart</button>';
		echo '</div> ';

		echo '<div class="btn-group" role="group" aria-label="...">';
		echo '<button type="submit" class="btn btn-success btn-sm" value="StartLogin" name="cmd" '.$btnN3.'>';
		echo '<i class="glyphicon glyphicon-play"></i> Start login</button>';
		echo '<button type="submit" class="btn btn-danger btn-sm" value="StopLogin" name="cmd" '.$btnN3.'>';
		echo '<i class="glyphicon glyphicon-stop"></i> Stop login</button>';	
		echo '<button type="submit" class="btn btn-primary btn-sm" value="StatusLogin" name="cmd" '.$btnN3.'>';
		echo '<i class="glyphicon glyphicon-repeat"></i> Status login</button>';
		echo '</div> ';
		
		echo '</td><td>';
		
				echo '<button type="submit" class="btn btn-default btn-sm" value="json" name="cmdlog" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-signal"></i> Stats</button>';
		echo '</div> ';
		
		echo ''.$check_tmux.'';
		
		echo '</td></tr></table>';
		echo '</form>';	
 
	}
	//################################################
	// Section 2 : Region
	//################################################
	if (isset($_POST['section2'])=="section2")
    {
		echo '<table class="table table-hover table-condensed"><tr><td>';
		
		echo '<form class="form-inline" method="post" action="">';
		echo '<div class="btn-group " role="group" aria-label="..."><div class="input-group col-xs-100">';
		echo '<input type="text" class="form-control" name="msg_alert" placeholder="'.$osmw_label_msg_send.'">';
		echo '<span class="input-group-btn"><button type="submit" class="btn btn-primary" value="Alerte General" name="cmd" '.$btnN2.'><i class="glyphicon glyphicon-bullhorn"></i> '.$osmw_btn_msg_send.'</button></span>';
		echo '</div></div>';
		echo '</form>';
		
		echo '<form class="form-inline" method="post" action="">';
		echo '<br><div class="btn-group " role="group" aria-label="..."><div class="input-group col-xs-100">';
		echo '<input type="text" class="form-control" name="avatar name" placeholder="avatar name">';
		echo '<span class="input-group-btn"><button type="submit" class="btn btn-danger" value="kick_user" name="cmd" '.$btnN2.'><i class="glyphicon glyphicon-eye-close"></i> Kick User</button></span>';
		echo '</div></div>';
		echo '</form>';
		
		echo '<form class="form-inline" method="post" action="">';
		echo '<br><div class="btn-group " role="group" aria-label="..."><div class="input-group col-xs-100">';
		echo '<input type="text" class="form-control" name="avatar name" placeholder="avatar name">';
		echo '<span class="input-group-btn"><button type="submit" class="btn btn-success" value="appearance_user" name="cmd" '.$btnN2.'><i class="glyphicon glyphicon-eye-open"></i> Appearence User</button></span>';
		echo '</div></div>';
		echo '</form>';
		
		echo '</td><td>';
		
		echo '<form class="form-inline" method="post" action="">';
		echo '<div class="btn-group" role="group" aria-label="...">';
		echo '<button type="submit" class="btn btn-primary btn-sm" value="Region Root" name="cmd" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-th-large"></i> Region Root</button>';
		echo '<button type="submit" class="btn btn-success btn-sm" value="Update Client" name="cmd" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-random"></i> Update Client</button>';
		echo '</div>';
		echo '<br><br>';
		echo '<div class="btn-group" role="group" aria-label="...">';
		echo '<button type="submit" class="btn btn-warning btn-sm" value="FCache Assets" name="cmd" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-repeat"></i> FCache Assets</button>';
		echo '<button type="submit" class="btn btn-warning btn-sm" value="FCache ClearF" name="cmd" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-repeat"></i> Clear File</button>';	
		echo '</form>';
		
		echo '</td></tr></table>';

	}
	//################################################
	// Section 3 : Terrain
	//################################################
	if (isset($_POST['section3'])=="section3")
    {
		echo '<form class="form-inline" method="post" action="">';
		echo '<table class="table table-hover"><tr><td align=left >';
		echo '<div class="btn-group" role="group" aria-label="...">';
		echo '<button type="submit" class="btn btn-success btn-sm" value="Generate Map" name="cmd" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-picture"></i> Generate Map</button>';	
		echo '</div>';

		echo ' <div class="btn-group" role="group" aria-label="...">';	
		echo '<button type="submit" class="btn btn-warning btn-sm" value="Elevate" name="cmd" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-picture"></i> Terrain +1</button>';
		echo '<button type="submit" class="btn btn-danger btn-sm" value="Lower" name="cmd" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-picture"></i> Terrain -1</button>';
		echo '</div>';
			
		echo '</td></tr></table>';
		echo '</form>';	
	}
	//################################################
	// Section 4 : Divers
	//################################################
	if (isset($_POST['section4'])=="section4")
    {
		echo '<form class="form-inline" method="post" action="">';
		echo '<table class="table table-hover"><tr><td align=left >';

		echo '<div class="btn-group" role="group" aria-label="...">';
		echo '<button type="submit" class="btn btn-danger btn-sm" value="Windlight disable" name="cmd" '.$btnN2.'>';
		echo '<i class="glyphicon glyphicon-stop"></i> Windlight disable </button>';
		echo '<button type="submit" class="btn btn-success btn-sm" value="Windlight enable" name="cmd" '.$btnN3.'>';
		echo '<i class="glyphicon glyphicon-play"></i> Windlight enable</button>';
		echo '<button type="submit" class="btn btn-warning btn-sm" value="Windlight load" name="cmd" '.$btnN3.'>';
		echo '<i class="glyphicon glyphicon-retweet"></i> Windlight load</button>';
		echo '</div>';	
		
		echo '</td><td>';

		echo ' <button type="submit" class="btn btn-primary btn-sm" value="Reload Estate" name="cmdEstate" '.$btnN1.'>';
		echo '<i class="glyphicon glyphicon-repeat"></i> Reload Estate</button>';	
		echo '</div><br>';
		
		echo '</td></tr><tr><td>';

		echo '<form class="form-inline" method="post" action=""><b>Estate name</b>';
		echo '<br><div class="btn-group " role="group" aria-label="..."><div class="input-group col-xs-100">';
		echo '<input type="text" class="form-control" name="estate_name" placeholder="estate name">';
		echo '<span class="input-group-btn"><button type="submit" class="btn btn-success" value="estate_name" name="cmd" '.$btnN2.'><i class="glyphicon glyphicon-download-alt"></i> '.$osmw_menu_GestSave.'</button></span>';
		echo '</div></div>';
		echo '</form>';

		echo '</td><td>';
		
		echo '<form class="form-inline" method="post" action=""><b>Estate Owner</b>';
		echo '<br><div class="btn-group " role="group" aria-label="..."><div class="input-group col-xs-100">';
		echo '<input type="text" class="form-control" name="estate_owner" placeholder="avatar name">';
		echo '<span class="input-group-btn"><button type="submit" class="btn btn-success" value="estate_owner" name="cmd" '.$btnN2.'><i class="glyphicon glyphicon-download-alt"></i> '.$osmw_menu_GestSave.'</button></span>';
		echo '</div></div>';
		echo '</form>';
		
		echo '</td></tr></table>';
		echo '</form>';	
	}
	//################################################
	
	//***************************************
    // *** Lecture Fichier Regions.ini ***
 	$filename2 = INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/Regions.ini";	 
	if (file_exists($filename2)) {$filename = $filename2;}
	$tableauIni = parse_ini_file($filename, true);
	if ($tableauIni == FALSE) {echo '<p>Error: Reading ini file '.$filename.'</p>';}

	// *** Recuperation du port Http du Simulateur
	$FichierConfINIPrivate = INI_Conf_Moteur($_SESSION['opensim_select'], "address").INI_Conf_Moteur($_SESSION['opensim_select'], "DB_OS");
	$tableauIniSimu = parse_ini_file($FichierConfINIPrivate, true);
	$srvOS  = $tableauIniSimu['Network']['http_listener_port'];
			 
	$tableauIni = parse_ini_file($filename, true);
	
    echo '<table  class="table table-hover">';
	//echo '<tr class="default"><td colspan=7><p><strong>'.$osmw_label_total_sim.'<span class="badge">'.count($tableauIni).'</span></strong></td><tr>';
    echo '<tr class="info">';
    echo '<th>Name</th>';
    echo '<th>Image</th>';
    echo '<th>Location</th>';
    echo '<th>Public IP/Host</th>';
    echo '<th>Port</th>';
    echo '</tr>';
	
	while (list($key, $val) = each($tableauIni))
	{
		$ImgMap = "http://".$hostname.":".trim($srvOS)."/index.php?method=regionImage".str_replace("-","",$tableauIni[$key]['RegionUUID']);
		
        if (Test_Url($ImgMap) == false){$ImgMap = "img/offline.jpg";}
	
		echo '<tr>';
        echo '<td><h5>'.$key.'</h5></td>';
        echo '<td><img style="height:64px;" class="img-thumbnail" alt="" src="'.$ImgMap.'"></td>';
        echo '<td><span class="badge">'.$tableauIni[$key]['Location'].'</span></td>';
        echo '<td><span class="badge">'.$tableauIni[$key]['ExternalHostName'].'</span></td>';
        echo '<td><span class="badge">'.$tableauIni[$key]['InternalPort'].'</span></td>';
        echo '</tr>';
	}
	echo '</table>';

	//**************************
	// COMMANDE POUR LES STATS ET LOG
	//**************************
	if($_POST["cmdlog"]=="json")	
	{
		$FichierConfINIPrivate = INI_Conf_Moteur($_SESSION['opensim_select'], "address").INI_Conf_Moteur($_SESSION['opensim_select'], "DB_OS");
		$tableauIniSimu = parse_ini_file($FichierConfINIPrivate, true);
		$port_simu  =  $tableauIniSimu['Network']['http_listener_port'] ;
		echo $jsonURL = "http://".$hostname.":".$port_simu."/jsonSimStats";
		$json = @file_get_contents($jsonURL, FILE_USE_INCLUDE_PATH);
		if ($json)
		{
			
			$json = json_decode($json, true);
			$name=  INI_Conf_Moteur($_SESSION['opensim_select'], "name");	
			$url_send = '../'.INI_Conf("", "cheminAppli").'inc/jauge_json.php?name='.$name.'&url='.$jsonURL;
			//echo '<center><iframe src="'.$url_send.'" width=450 height=300 style="border:none"></iframe></center>';
			
			echo "<div><strong>Simulator :</strong> ".$json['Version']."</div>";
			echo '<div class="table-responsive">';
			echo '<table class="table table-condensed">';
			echo '<thead><tr><th>Name</th><th class="text-right">Value</th></tr></thead>';
			echo '<tbody>';
			foreach ($json as $key => $value)
			{
				if ($key <> "Version")
				{
					echo '<tr><td><strong>'.$key.'</strong></td><td class="text-right"><span class="label label-primary">'.$value.'</span></td></tr>';
				}
			}
			echo '</tbody></table></div>';
		}
		else
		{
			echo '<article><h3>OpenSim.ini</h3><pre>
			[Startup]
			Agent_Stats_URI = "jsonUserStats"
			</pre></article>';
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
