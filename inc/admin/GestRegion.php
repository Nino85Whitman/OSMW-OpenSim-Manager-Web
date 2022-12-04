<?php 
if (isset($_SESSION['authentification']) && $_SESSION['privilege']>= 3)
{
	echo Affichage_Entete($_SESSION['opensim_select']);


    echo '<h1>'.$osmw_index_6.'</h1>';
    echo '<div class="clearfix"></div>';
	
    //******************************************************
    //  Affichage page principale
    //******************************************************

	//******************************************************
	// CONSTRUCTION de la commande pour ENVOI via RAdmin
	//******************************************************
    if (isset($_POST['cmd']))
    {
        // *** Affichage mode debug ***
        // echo $_POST['cmd'];echo '<BR>';

        // On charge le fichier INI
        // *** Lecture Fichier Regions.ini *** 
        $filename = INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/Regions.ini";

        if (file_exists($filename)) {;}
        else {echo "<div class='alert alert-danger alert-anim'> <trong>$filename </trong>".$osmw_erreur_file_exist."</div>";}

        $tableauIni = parse_ini_file($filename, true);
        if ($tableauIni == FALSE) {echo '<p>".$osmw_erreur_file_ini_exist." $filename</p>';}

        if ($_POST['cmd'] == 'Ajouter')
        {
            echo '<form method="post" action="">';
            echo '<table class="table table-hover">';
            echo '<tr class="info">';
            echo '<th>Name</th>';
            echo '<th>Location</th>';
            echo '<th>Internal Port</th>';
            echo '<th>Public IP</th>';
            echo '<th>Uuid (auto generate)</th>';
			echo '<th>Size X</th>';
			echo '<th>Size Y</th>';
			echo '<th> </th>';
            echo '</tr>';

            echo '<tr>';
            echo '<td><input class="form-control" type="text" name="NewName" placeholder="Nom de la region" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="Location" placeholder="5000,5000" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="InternalPort" placeholder="9000" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="ExternalHostName" placeholder="domaine.com" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="RegionUUID" value="'.GenUUID().'" '.$btnN3.'></td>';
			echo '<td><input class="form-control" type="text" name="SizeX" value="256" '.$btnN3.'></td>';
			echo '<td><input class="form-control" type="text" name="SizeY" value="256" '.$btnN3.'></td>';
            echo '<td><button class="btn btn-success" type="submit" value="Enregistrer" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-ok"></i> '.$osmw_btn_ajout_user.'</button></td>';
            echo '</table>';
            echo '</form>';
        }

        if ($_POST['cmd'] == 'Enregistrer')
        {
            // AJOUTER chaque valeur
            $tableauIni[$_POST['NewName']]['RegionUUID']        = $_POST['RegionUUID'];
            $tableauIni[$_POST['NewName']]['Location']          = $_POST['Location'];
            $tableauIni[$_POST['NewName']]['InternalAddress']   = "0.0.0.0";
            $tableauIni[$_POST['NewName']]['InternalPort']      = $_POST['InternalPort'];
            $tableauIni[$_POST['NewName']]['ExternalHostName']  = $_POST['ExternalHostName'];
			$tableauIni[$_POST['NewName']]['SizeX']  			= $_POST['SizeX'];
			$tableauIni[$_POST['NewName']]['SizeY']				= $_POST['SizeY'];			

            // Enregistrement du nouveau fichier 
            $fp = fopen (INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/RegionTemp.ini", "w");  
            
            while (list($key, $val) = each($tableauIni))
            {
                fputs($fp, "[".$key."]\r\n");
                fputs($fp, "RegionUUID = ".$tableauIni[$key]['RegionUUID']."\r\n");
                fputs($fp, "Location = ".$tableauIni[$key]['Location']."\r\n");
                fputs($fp, "InternalAddress = 0.0.0.0\r\n");
                fputs($fp, "InternalPort = ".$tableauIni[$key]['InternalPort']."\r\n");
                fputs($fp, "AllowAlternatePorts = False\r\n");
                fputs($fp, "ExternalHostName = ".$tableauIni[$key]['ExternalHostName']."\r\n");
				fputs($fp, "SizeX = ".$tableauIni[$key]['SizeX']."\r\n");
				fputs($fp, "SizeY = ".$tableauIni[$key]['SizeY']."\r\n");					
            }
            fclose ($fp);  
      
            unlink($filename); 
            rename(INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/RegionTemp.ini",$filename);

			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Region <strong>".$_POST['NewName']."</strong> ".$osmw_save_user_ok."</p>";
        }

        if ($_POST['cmd'] == 'Modifier')
        {
            if ($_POST['name_sim'] == $_POST['NewName'])
            {
                //echo $_POST['NewName'].' == '.$_POST['name_sim'].'<br>';
                // AJOUTER chaque valeur
                $tableauIni[$_POST['NewName']]['RegionUUID']        = $_POST['RegionUUID'];
                $tableauIni[$_POST['NewName']]['Location']          = $_POST['Location'];
                $tableauIni[$_POST['NewName']]['InternalAddress']   = "0.0.0.0";
                $tableauIni[$_POST['NewName']]['InternalPort']      = $_POST['InternalPort'];
                $tableauIni[$_POST['NewName']]['ExternalHostName']  = $_POST['ExternalHostName'];
				$tableauIni[$_POST['NewName']]['SizeX']  			= $_POST['SizeX'];
				$tableauIni[$_POST['NewName']]['SizeY']				= $_POST['SizeY'];				
            }

            if ($_POST['name_sim'] <> $_POST['NewName'])
            {
                // echo $_POST['NewName'].' <> '.$_POST['name_sim'];
                // MODIFIER chaque valeur pour la region sellectionner ==> AJOUT Nouveau
                $tableauIni[$_POST['NewName']]['RegionUUID']        = $_POST['RegionUUID'];
                $tableauIni[$_POST['NewName']]['Location']          = $_POST['Location'];
                $tableauIni[$_POST['NewName']]['InternalAddress']   = "0.0.0.0";
                $tableauIni[$_POST['NewName']]['InternalPort']      = $_POST['InternalPort'];
                $tableauIni[$_POST['NewName']]['ExternalHostName']  = $_POST['ExternalHostName'];
				$tableauIni[$_POST['NewName']]['SizeX']  			= $_POST['SizeX'];
				$tableauIni[$_POST['NewName']]['SizeY']				= $_POST['SizeY'];
                
                // MODIFIER chaque valeur pour la region sellectionner ==> SUPPRESSION  Ancien
                unset($tableauIni[$_POST['name_sim']]['RegionUUID']);
                unset($tableauIni[$_POST['name_sim']]['Location']);
                unset($tableauIni[$_POST['name_sim']]['InternalAddress']);
                unset($tableauIni[$_POST['name_sim']]['InternalPort']);
                unset($tableauIni[$_POST['name_sim']]['AllowAlternatePorts']);
                unset($tableauIni[$_POST['name_sim']]['ExternalHostName']);
				unset($tableauIni[$_POST['name_sim']]['SizeX']);
				unset($tableauIni[$_POST['name_sim']]['SizeY']);				
                unset($tableauIni[$_POST['name_sim']]);
            }

            // Enregistrement du nouveau fichier 
            $fp = fopen (INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/RegionTemp.ini", "w");  
            while (list($key, $val) = each($tableauIni))
            {
                fputs($fp, "[".$key."]\r\n");
                fputs($fp, "RegionUUID = ".$tableauIni[$key]['RegionUUID']."\r\n");
                fputs($fp, "Location = ".$tableauIni[$key]['Location']."\r\n");
                fputs($fp, "InternalPort = ".$tableauIni[$key]['InternalPort']."\r\n");
                fputs($fp, "InternalAddress = 0.0.0.0\r\n");
                fputs($fp, "AllowAlternatePorts = False\r\n");
                fputs($fp, "ExternalHostName = ".$tableauIni[$key]['ExternalHostName']."\r\n");
				fputs($fp, "SizeX = ".$tableauIni[$key]['SizeX']."\r\n");
				fputs($fp, "SizeY = ".$tableauIni[$key]['SizeY']."\r\n");				
            }
            fclose ($fp);  
            // Suppression de l'original
            unlink($filename); 
            // Renommer le temp en original
            rename(INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/RegionTemp.ini", $filename); 


			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Region <strong>".$_POST['NewName']."</strong> ".$osmw_edit_user_ok."</p>";
        } 

        if ($_POST['cmd'] == 'Supprimer')
        {			
            // MODIFIER chaque valeur pour la region sellectionner ==> SUPPRESSION  Ancien
            unset($tableauIni[$_POST['name_sim']]['RegionUUID']);
            unset($tableauIni[$_POST['name_sim']]['Location']);
            unset($tableauIni[$_POST['name_sim']]['InternalAddress']);
            unset($tableauIni[$_POST['name_sim']]['InternalPort']);
            unset($tableauIni[$_POST['name_sim']]['AllowAlternatePorts'] );
            unset($tableauIni[$_POST['name_sim']]['ExternalHostName']);
			unset($tableauIni[$_POST['name_sim']]['SizeX']);
			unset($tableauIni[$_POST['name_sim']]['SizeY']);
            unset($tableauIni[$_POST['name_sim']]);

            // Enregistrement du nouveau fichier 
            $fp = fopen (INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/RegionTemp.ini", "w");  
            
            while (list($key, $val) = each($tableauIni))
            {
                fputs($fp, "[".$key."]\r\n");
                fputs($fp, "RegionUUID          = ".$tableauIni[$key]['RegionUUID']."\r\n");
                fputs($fp, "Location            = ".$tableauIni[$key]['Location']."\r\n");
                fputs($fp, "InternalAddress     = 0.0.0.0\r\n");
                fputs($fp, "InternalPort        = ".$tableauIni[$key]['InternalPort']."\r\n");
                fputs($fp, "AllowAlternatePorts = False\r\n");
                fputs($fp, "ExternalHostName    = ".$tableauIni[$key]['ExternalHostName']."\r\n");
				fputs($fp, "SizeX				= ".$tableauIni[$key]['SizeX']."\r\n");
				fputs($fp, "SizeY				= ".$tableauIni[$key]['SizeY']."\r\n");
            }

            fclose ($fp);  
            unlink($filename); 
            rename(INI_Conf_Moteur($_SESSION['opensim_select'],"address")."Regions/RegionTemp.ini",$filename); 

			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Region <strong>".$_POST['NewName']."</strong> ".$osmw_delete_user_ok."</p>";
        } 
    }

    // ******************************************************
    //  Affichage page principale
    // ******************************************************
    // ******************************************************
    // *** Lecture Fichier Regions.ini ***
    $filename2 = INI_Conf_Moteur($_SESSION['opensim_select'], "address")."Regions/Regions.ini";

    if (file_exists($filename2)) {$filename = $filename2;}
    else
    {
        echo "<p class='alert alert-success alert-anim'>";
        echo "<i class='glyphicon glyphicon-ok'></i>";
        echo " <strong>".$filename2."</strong> ".$osmw_erreur_file_exist."</p>";
    }

    $tableauIni = parse_ini_file($filename, true);
    if ($tableauIni == FALSE)
    {
        echo "<p class='alert alert-success alert-anim'>";
        echo "<i class='glyphicon glyphicon-ok'></i>";
        echo $osmw_erreur_file_ini_exist ."<strong>".$filename."</strong> ...</p>";
    }
    
    $i = 0;

	// Autorisation d'ajout de region
	$btn = 'disabled';
	if (INI_Conf("Parametre_OSMW","Autorized") == '1') 
	{
		$RegionMax = INI_Conf("NbAutorized", "NbAutorized");
		echo '<p>'.$osmw_label_nb_max_sim .' <span class="badge">'.$RegionMax.'</span></p>';
		echo '<p>'.$osmw_label_total_sim.' <span class="badge">'.count($tableauIni).'</span></p>';
		if (count($tableauIni) == $RegionMax ){$btn = 'disabled';}
		else {$btn = $btnN3;}

		
		

		echo '<form class="form-group" method="post" action="">';
		echo '<input type="hidden" name="cmd" value="Ajouter">';
		echo '<button class="btn btn-success" type="submit" '.$btn.'><i class="glyphicon glyphicon-ok"></i> '.$osmw_btn_ajout_user.'</button>';
		echo '</form>';
	}


    echo '<table class="table table-hover">';
    echo '<tr class="info">';
    echo '<th>Name</th>';
    echo '<th>Location</th>';
    echo '<th>Port Http</th>';
    echo '<th>Public Ip</th>';
    echo '<th>Uuid</th>';
	echo '<th>Size X</th>';
	echo '<th>Size Y</th>';
    echo '<th>Modify</th>';
    echo '<th>Delete</th>';
    echo '</tr>';

    while (list($key, $val) = each($tableauIni))
    {
        echo '<tr>';
		echo '<form class="form-group" method="post" action="">';
		echo '<input type="hidden" name="name_sim" value="'.$key.'" >';
		echo '<tr>';
		echo '<td><input class="form-control" type="text" name="NewName" value="'.$key.'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="Location" value="'.$tableauIni[$key]['Location'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="InternalPort" value="'.$tableauIni[$key]['InternalPort'].'" '.$btnN3.'></td>';
        echo '<td><input class="form-control" type="text" name="ExternalHostName" value="'.$tableauIni[$key]['ExternalHostName'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="RegionUUID" value="'.$tableauIni[$key]['RegionUUID'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="sizeX" value="'.$tableauIni[$key]['SizeX'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="sizeY" value="'.$tableauIni[$key]['SizeY'].'" '.$btnN3.'></td>';
		echo '<td><button class="btn btn-success" type="submit" value="Modifier" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-edit"></i> '.$osmw_btn_modifier.'</button></td>';
        echo '<td><button class="btn btn-danger" type="submit" value="Supprimer" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-trash"></i> '.$osmw_btn_supprimer.'</button></td>';
		echo '</tr>';
		echo '</form>';
		echo '</tr>';
	}
    echo '</table>';
}
else {header('Location: index.php');}
?>
