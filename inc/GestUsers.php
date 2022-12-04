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

    echo '<h1>'.$osmw_index_8.'</h1>';
    echo '<div class="clearfix"></div>';
	    //******************************************************
    //  Affichage page principale
    //******************************************************
    // on se connecte a MySQL
    try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
    catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }

	if (isset($_POST['cmd']))
	{
		// ******************************************************
		// ****************** ACTION BOUTON *********************
		// ******************************************************

		// ******************************************************
		if ($_POST['cmd'] == 'Enregistrer')
		{
		
			// *** Lecture BDD users  ***
			$UserSelected = explode(" ", $_SESSION['login']);
			$sql = 'SELECT * FROM users WHERE id="'.$_POST['id_user'].'"';
			$reponse = $bdd->query($sql);
			$data = $reponse->fetch();
			
			$_SESSION['login'] = $_POST['firstname']." ".$_POST['lastname'];
			
			if(trim($data['password']) == trim($_POST['password']))
			{
				$sqlIns = "UPDATE `users` SET `firstname`='".$_POST['firstname']."', `lastname`='".$_POST['lastname']."' WHERE `id`='".$_POST['id_user']."';";
				$reqIns = mysql_query($sqlIns) or die('Erreur SQL !<p>'.$sqlIns.'</p>'.mysql_error());
			}
			else
			{
				$encryptedPassword = sha1($_POST['password']);
				$sql = "UPDATE `users` SET `firstname`='".$_POST['firstname']."', `lastname`='".$_POST['lastname']."', `password`='".$encryptedPassword."' WHERE `id`='".$_POST['id_user']."';";
				$reponse = $bdd->query($sql);
			}
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " Modification pour <strong>".$_POST['firstname']." ".$_POST['lastname']."</strong> enregistre avec succes</p>";  
		}
    }

    //******************************************************
    //  Affichage page principale
    //******************************************************
	// *** Lecture BDD users  ***
	$UserSelected = explode(" ", $_SESSION['login']);
	$sql = 'SELECT * FROM users WHERE (firstname="'.$UserSelected[0].'" AND lastname="'.$UserSelected[1].'")';
	$reponse = $bdd->query($sql);
	$data = $reponse->fetch();

		echo '<form class="form-group" method="post" action="">';
		echo '<input type="hidden" value="'.$data['id'].'" name="id_user">';
		echo '<table class="table table-hover">';
		echo '<tr class="info">';
		echo '<td>Firstname:</td>';
		echo '<td><input class="form-control" type="text" value="'.$data['firstname'].'" name="firstname" ></td>';
		echo '</tr><tr class="info">';
		echo '<td>Lastname:</td>';
		echo '<td><input class="form-control" type="text" value="'.$data['lastname'].'" name="lastname" ></td>';
		echo '</tr><tr class="warning">';		
		echo '<td>Password:</td>';
		echo '<td><input class="form-control" type="text" value="'.$data['password'].'" name="password" ></td>';		
		echo '</tr><tr>';
		echo '</form>';
		echo '</table>';
        echo' <button type="submit" class="btn btn-success" name="cmd" value="Enregistrer">';
        echo '<i class="glyphicon glyphicon-ok"></i> '.$osmw_btn_enregistrer.'</button>';
	
    $reponse->closeCursor();
}
else {header('Location: index.php');}
?>