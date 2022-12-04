<?php 
if (isset($_SESSION['authentification']))
{
	//echo Affichage_Entete($_SESSION['opensim_select']);
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

    echo '<h1>'.$osmw_index_22.'</h1>';
    echo '<div class="clearfix"></div>';
        

    // *******************************************************
    // Initialisation des variables ET du tableau
    // *******************************************************

    // Offset
    $ox = -10;
    $oy = 0;

    // Limite de 50x50
    $max = 15;
    
    for ($x = -$max; $x < ($max - 1); $x++)
    {
        // echo "<hr>X:".$x.'<hr>';
        // Limite de 50x50
        for($y = -$max; $y < ($max - 1); $y++)
        {
            // echo "<hr>Y:".$y.'<hr>';
            $Matrice[$x][$y]['name'] = "";	
            $Matrice[$x][$y]['img']  = "";
            $Matrice[$x][$y]['ip']   = "";
            $Matrice[$x][$y]['port'] = "";	
            $Matrice[$x][$y]['uuid'] = "";
        } 
    } 
    //*******************************************************

    // *******************************************************	
    // Lecture des regions.ini et enregistrement dans Matrice
    // *******************************************************
    // on se connecte a MySQL
    try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
    catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }

    $reponse = $bdd->query('SELECT * FROM moteurs');

    // On affiche chaque entrée une à une
    while ($data = $reponse->fetch())
    {
        // Pour chaque serveur
        $tableauIni = parse_ini_file($data['address']."Regions/Regions.ini", true);

        if ($tableauIni == FALSE)
        {
            echo 'Probleme Lecture Fichier .ini '.$data['address']."Regions/Regions.ini".'<br>';
        }


        while (list($keyi) = each($tableauIni))
        {
			// *** Recuperation du port Http du Simulateur
			 $FichierConfINIPrivate = INI_Conf_Moteur($_SESSION['opensim_select'], "address").INI_Conf_Moteur($_SESSION['opensim_select'], "DB_OS");
             $tableauIniSimu = parse_ini_file($FichierConfINIPrivate, true);
			 $srvOS  = $tableauIniSimu['Network']['http_listener_port'];
 
            // Recuperation des valeurs ET enregistrement des valeurs dans le tableau
            // echo $key.$tableauIni[$key]['RegionUUID'].$tableauIni[$key]['Location'].$tableauIni[$key]['InternalPort'].'<br>';
            $location                               = explode(",", $tableauIni[$keyi]['Location']);
            $coordX                                 = $location[0] - $px - $ox;
            $coordY                                 = $location[1] - $py - $oy;
            $Matrice[$coordX][$coordY]['name']      = $keyi;
            $uuid                                   = str_replace("-", "", $tableauIni[$keyi]['RegionUUID']);
           // $ImgMap                                 = "http://".$tableauIni[$keyi]['ExternalHostName'].":".trim($srvOS)."/index.php?method=regionImage".$uuid;
		    $ImgMap                                 = "vps1.fgagod.fr".":".trim($srvOS)."/index.php?method=regionImage".$uuid;
            $Matrice[$coordX][$coordY]['img']       = $ImgMap;
            $Matrice[$coordX][$coordY]['ip']        = $tableauIni[$keyi]['ExternalHostName'];
            $Matrice[$coordX][$coordY]['port']      = $tableauIni[$keyi]['InternalPort'];	
            $Matrice[$coordX][$coordY]['uuid']      = $key.$tableauIni[$keyi]['RegionUUID'];
            $Matrice[$coordX][$coordY]['hypergrid'] = $data[hypergrid];
        }
    }
    $reponse->closeCursor();

    // ****************************
    // *** Map en construction ****
    // ****************************
    // echo $_POST['zooming'];
    if (isset($_POST['zooming']))
    {
        $widthMap = $_POST['zooming'];
        $heightMap = $_POST['zooming'];

        // $select = "";
        $_SESSION['zooming_select'] = trim($_POST['zooming']);
        if ($_SESSION['zooming_select'] == 30) {$select1 = "selected";}
        if ($_SESSION['zooming_select'] == 40) {$select2 = "selected";}
        if ($_SESSION['zooming_select'] == 50) {$select3 = "selected";}
        if ($_SESSION['zooming_select'] == 60) {$select4 = "selected";}
        if ($_SESSION['zooming_select'] == 70) {$select5 = "selected";}
    }

    else
    {
        if ($_SESSION['zooming_select'] == 30) {$select1 = "selected";}
        if ($_SESSION['zooming_select'] == 40) {$select2 = "selected";}
        if ($_SESSION['zooming_select'] == 50) {$select3 = "selected";}
        if ($_SESSION['zooming_select'] == 60) {$select4 = "selected";}
        if ($_SESSION['zooming_select'] == 70) {$select5 = "selected";}
        $widthMap = $_SESSION['zooming_select'];
        $heightMap = $_SESSION['zooming_select'];
    }

    echo '<form class="form-group" method=post action="">';
    echo '<div class="form-inline">';
    echo '<select class="form-control" name="zooming">';
    echo '<option value="30" name="zooming" '.$select1.'>Zoom 1</option>';
    echo '<option value="40" name="zooming" '.$select2.'>Zoom 2</option>';
    echo '<option value="50" name="zooming" '.$select3.'>Zoom 3</option>';
    echo '<option value="60" name="zooming" '.$select4.'>Zoom 4</option>';
    echo '<option value="70" name="zooming" '.$select5.'>Zoom 5</option>';
    echo '</select>';
    echo ' <button type="submit" class="btn btn-success" name="goto">';
    echo '<i class="glyphicon glyphicon-ok"></i> '.$osmw_btn_map_zoom;
    echo '</button>';
    echo '</div>';
    
    echo '<br />';
    echo '<center>';
    echo '<table>';
    for ($y = $max; $y > (-$max - 1); $y--) // Limite Y
    //for ($x = -$max; $x < ($max + 1); $x++) // Limite X
    {
        echo '<tr>';
        for ($x = -$max; $x < $max+1; $x++) // Limite X
        //for($y = -$max; $y < ($max + 1); $y++) // Limite Y
        {
            echo '<td>';

            if ($Matrice[$x][$y]['img'])
            {
                $textemap = $Matrice[$x][$y]['name'];
                $locX = $Matrice[$x][$y]['locX'];
                $locY = $Matrice[$x][$y]['locY'];
                echo '<img class="img-responsive" src="'.$Matrice[$x][$y]['img'].'" width="'.$widthMap.'" height="'.$heightMap.'" alt="'.$textemap.'" title="'.$textemap.' '.$locX.' '.$locY.'" data-toggle="tooltip" data-placement="top">';
            }

            else
            {
                $textemap = "Water (Free)";
                echo '<img class="img-responsive" src="./img/water.jpg" width="'.$widthMap.'" height="'.$heightMap.'" alt="'.$textemap.'" title="'.$textemap.' '.$locX.' '.$locY.'" data-toggle="tooltip" data-placement="top">';
           }
            echo '</td>';
        } 
        echo '</tr>';
    } 
    echo '</table>';
    echo '</center>';		
}
else {header('Location: index.php');}
?>
