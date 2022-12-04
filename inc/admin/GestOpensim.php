<?php 
if (isset($_SESSION['authentification']) && $_SESSION['privilege']>= 4)
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

    echo '<h1>'.$osmw_index_5.'</h1>';
    echo '<div class="clearfix"></div>';
    //******************************************************
    //  Affichage page principale
    //******************************************************
    // *** Test des Fichiers suivants ***	
    $FichierConfINIPrivate = INI_Conf_Moteur($_SESSION['opensim_select'], "address").INI_Conf_Moteur($_SESSION['opensim_select'], "DB_OS");
	$filename1 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.ini";		
	$filename1b = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSimDefaults.ini";		
	$filename2 = $FichierConfINIPrivate;
	$filename3 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/FlotsamCache.ini";	
	$filename4 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."config-include/GridCommon.ini";
	$filename5 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.log";
	$filename6 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."OpenSim.32BitLaunch.log";
	$filename7 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."startuplogo.txt";
	$filename8 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."startup_commands.txt";
	$filename9 = INI_Conf_Moteur($_SESSION['opensim_select'],"address")."shutdown_commands.txt";
    //******************************************************

    $dispo = "";

    if (file_exists($filename1))
    {
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="OpenSim.ini"></p>';
    }
	else if (!$_POST['affichage']) 
    {
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>OpenSim.ini</strong> '.$osmw_erreur_file_exist.'</div>';
    }

	if (file_exists($filename1b))
    {
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="OpenSimDefaults.ini"></p>';
    }
	else if (!$_POST['affichage']) 
    {
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>OpenSimDefaults.ini</strong> '.$osmw_erreur_file_exist.'</div>';
    }
	
    if (file_exists($filename2))
    {
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="'.$FichierConfINIPrivate.'"></p>';
	}
	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>".FichierConfINIPrivate."</strong> '.$osmw_erreur_file_exist.'</div>';
	}

	if (file_exists($filename3))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="FlotsamCache.ini"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>FlotsamCache.ini</strong> '.$osmw_erreur_file_exist.'</div>';
	}


	if (file_exists($filename4))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="GridCommon.ini"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>GridCommon.ini</strong> '.$osmw_erreur_file_exist.'</div>';
	}

	if (file_exists($filename5))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="OpenSim.log"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>OpenSim.log</strong> '.$osmw_erreur_file_exist.'</div>';
	}

	if (file_exists($filename6))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="OpenSim.32BitLaunch.log"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>OpenSim.32BitLaunch.log</strong> '.$osmw_erreur_file_exist.'</div>';
	}

	if (file_exists($filename7))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="startuplogo.txt"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>startuplogo.txt</strong> '.$osmw_erreur_file_exist.'</div>';
	}

	if (file_exists($filename8))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="startup_commands.txt"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>startup_commands.txt</strong> '.$osmw_erreur_file_exist.'</div>';
	}

	if (file_exists($filename9))
	{
        $dispo = $dispo.'<p><input class="btn btn-default btn-block" type="submit" name="affichage" value="shutdown_commands.txt"></p>';
	}

	else if (!$_POST['affichage'])
	{
        echo '<div class="alert alert-danger alert-anim" role="alert"> <strong>shutdown_commands.txt</strong> '.$osmw_erreur_file_exist.'</div>';
	}

    echo '<h4>'.$osmw_menu_choix_change.'</h4>';
    echo '<form class="form-group" method="post" action="">';
    echo $dispo;
    echo '</form>';
    
    if ($_POST['affichage'] == "OpenSim.ini"){$fichier = $filename1;}
	if ($_POST['affichage'] == "OpenSimDefaults.ini"){$fichier = $filename1b;}
	if ($_POST['affichage'] == $FichierConfINIPrivate){$fichier = $filename2;}
	if ($_POST['affichage'] == "FlotsamCache.ini"){$fichier = $filename3;}
	if ($_POST['affichage'] == "GridCommon.ini"){$fichier = $filename4;}
	if ($_POST['affichage'] == "OpenSim.log"){$fichier = $filename5;}
	if ($_POST['affichage'] == "OpenSim.32BitLaunch.log"){$fichier = $filename6;}
	if ($_POST['affichage'] == "startuplogo.txt"){$fichier = $filename7;}
	if ($_POST['affichage'] == "startup_commands.txt"){$fichier = $filename8;}
	if ($_POST['affichage'] == "shutdown_commands.txt"){$fichier = $filename9;}

    // Enregistre le fichier
    if (isset($_POST['button']))
    {
        unlink($fichier);
        $ouverture = fopen("$fichier", "a+");
        fwrite($ouverture, "$_POST[modif]");
        fclose($ouverture);
        echo '<div class="alert alert-success alert-anim" role="alert">';
        echo '<strong>'.$_POST['affichage'].'</strong> '.$osmw_edit_user_ok.'</div>';
    }
        
    if (isset($_POST['affichage']))	// Affiche le fichier
    {  	
        echo '<div class="alert alert-warning" role="alert">';
        echo '<p>'.$osmw_file_change.' :<strong>'.$_POST['affichage'].'</strong>';
        echo '</div>';

        echo '<form class="form-group" method="post" action="">';
        echo '<input type="hidden" name="affichage" value="'.$_POST['affichage'].'">';
        echo '<input type="hidden" name="button" value="Modifier" '.$btnN3.'>';

        echo '<textarea class="form-control preformat" name="modif" rows="10">';
        echo file_get_contents($fichier); 
        echo '</textarea>';
        echo '<p></p>';

        echo '<button class="btn btn-success" type="submit" name="button" '.$btnN3.'>';
        echo '<i class="glyphicon glyphicon-ok"></i> '.$osmw_btn_modifier.'</button>';
        echo '</form>';
    }
}
else {header('Location: index.php');}
?>
