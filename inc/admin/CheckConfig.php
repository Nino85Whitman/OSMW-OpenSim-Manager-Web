<?php 
if (isset($_SESSION['authentification']) && $_SESSION['privilege']>= 3)
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
	
   echo '<h1>'.$osmw_index_18.'</h1>'; 
    echo '<div class="clearfix"></div>';   

		// on se connecte a MySQL
	try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
	catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}

	$reponse = $bdd->query('SELECT * FROM users');

    
    // *****************************************************************
    if (isset($_POST['cmd']))
    {
        // *******************************************************************
        // *************** ACTION BOUTON *************************************
        // *******************************************************************
        if ($_POST['cmd'] == 'Enregistrer')
        {	
            $sqlIns = "
                UPDATE `config` 
                SET `cheminAppli` = '".$_POST['cheminAppli']."',
                    `destinataire` = '".$_POST['destinataire']."',
                    `Autorized` = '".$_POST['Autorized']."',
                    `NbAutorized` = '".$_POST['NbAutorized']."',
                    `VersionOSMW` = '".$_POST['VersionOSMW']."' 
                WHERE `config`.`id` = 1
            ";
            $reponse = $bdd->query($sqlIns);
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Configuration <strong>".$_POST['NewName']."</strong> ".$osmw_save_user_ok."</p>";
        }
    }
    // ******************************************************

	// *** Lecture BDD config  ***
	$sql = 'SELECT * FROM config';
	$reponse = $bdd->query($sql);
	
	while ($data = $reponse->fetch())
	{
		echo '<form class="form-group" method="post" action="">';
		echo '<table class="table table-hover">';
		echo '<tr>';
		echo '<td>Path (ex: /manager/):</td>';
		echo '<td><input class="form-control" type="text" value="'.$data['cheminAppli'].'" name="cheminAppli" '.$btnN3.'></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>Email:</td>';
		echo '<td><input class="form-control" type="text" value="'.$data['destinataire'].'" name="destinataire" '.$btnN3.'></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>'.$osmw_label_add_sim_check.':</td>';
		echo '<td><input class="form-control" type="text" value="'.$data['Autorized'].'" name="Autorized" '.$btnN3.'></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>'.$osmw_label_nb_max_sim.':</td>';
		echo '<td><input class="form-control" type="text" value="'.$data['NbAutorized'].'" name="NbAutorized" '.$btnN3.'></td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>Copyright:</td>';
		echo' <td><input class="form-control" type="text" value="'.$data['VersionOSMW'].'" name="VersionOSMW" '.$btnN3.'></td>';
		echo '</tr>';
		echo '</form>';
		echo '</table>';
        echo' <button type="submit" class="btn btn-success" name="cmd" value="Enregistrer" '.$btnN3.'>';
        echo '<i class="glyphicon glyphicon-ok"></i> '.$osmw_btn_enregistrer.'</button>';
	}
	$reponse->closeCursor();
}
else {header('Location: index.php');}
?>