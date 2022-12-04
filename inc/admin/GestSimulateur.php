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

    echo '<h1>'.$osmw_index_17.'</h1>';
    echo '<div class="clearfix"></div>';	
	
 	echo '<form class="form-group" method="post" action="">';
    echo '<input type="hidden" name="cmd" value="Ajouter" '.$btnN3.'>';
	echo '<button class="btn btn-success" type="submit" value="Ajouter un Simulateur" '.$btnN3.'><i class="glyphicon glyphicon-plus"></i></button> ';
	echo '<button class="btn btn-warning" type="submit" name="cmd" value="tmux_load_all" '.$btnN3.'><i class="glyphicon glyphicon-play"></i> ALL Tmux</button> ' ;
	echo '<button class="btn btn-danger" type="submit" name="cmd" value="tmux_kill_all" '.$btnN3.'><i class="glyphicon glyphicon-stop"></i> ALL Tmux</button> ';
	echo '</form>';
 
	//******************************************************
	// CONSTRUCTION de la commande pour ENVOI sur la console via  SSH
	//******************************************************
	if (isset($_POST['cmd']))
	{
		$osmw_simu =$_POST['name'];
		// on se connecte a MySQL
		try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
		catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}
		
		if($_POST['cmd'] == 'tmux_load')
		{
			$cmd = 'tmux new -d -s '.$_POST['name'];
			CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " ".$osmw_simu." <strong> LOADED - NO ACTIVATED </strong></p>";
		}				
		if($_POST['cmd'] == 'tmux_kill')
		{
			$cmd = 'tmux kill-session -t '.$_POST['name'];
			CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " ".$osmw_simu." <strong> KILLED </strong></p>";
		}				
		if($_POST['cmd'] == 'tmux_kill_all')
		{
			// on se connecte a MySQL
			try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
			catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}
			$reponse = $bdd->query('SELECT * FROM moteurs');
			// On affiche chaque entrée une à une
			while ($data = $reponse->fetch())
			{
					$cmd = 'tmux kill-session -t '.$data['name'];
					CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
			}
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " ".$osmw_simu." <strong> KILLED </strong></p>";
		}	
		if($_POST['cmd'] == 'tmux_load_all')
		{
			// on se connecte a MySQL
			try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
			catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}
			$reponse = $bdd->query('SELECT * FROM moteurs');
			// On affiche chaque entrée une à une
			while ($data = $reponse->fetch())
			{
					$cmd = 'tmux new -d -s '.$data['name'];
					CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
			}
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " ".$osmw_simu." <strong> LOADED </strong></p>";
		}		
		
		//********************************************************
		if($_POST['cmd'] == 'Ajouter')
		{
			$i = NbOpensim() + 1;
			echo '<form method=post sction="">';
			echo '<table class="table table-hover">';
			echo '<tr class="info">';
			echo '<th>Name</th>';   
			echo '<th>Version</th>';
			echo '<th>Path</th>';
			echo '<th>HG url</th>';
			echo '<th>Database</th>';
            echo '<th>Save</th>';
			echo '</tr>';
			echo '<tr>';
            echo '<td><input class="form-control" type="text" name = "NewName" value="My_Simulator_'.$i.'" '.$btnN3.'"></td>';
            echo '<td><input class="form-control" type="text" name = "version" value="0.9.1" '.$btnN3.'"></td>';
            echo '<td><input class="form-control" type="text" name = "address" value="/home/user/simulateur'.$i.'/" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name = "hypergrid" value="hg.simulator'.$i.'.com:80" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name = "DB_OS" value="My_Simulator_'.$i.'_database" '.$btnN3.'></td>';
            echo '<td><input class="btn btn-success" type="submit" value="Enregistrer" name="cmd" '.$btnN3.'></td>';
            echo '</tr></table></form>';
		}

		if ($_POST['cmd'] == 'Enregistrer')
		{	
			$sqlIns = "INSERT INTO moteurs (`osAutorise` ,`id_os` ,`name` ,`version` ,`address` , `DB_OS`, `hypergrid`)
                        VALUES (NULL , '".$_POST['NewName']."', '".$_POST['NewName']."', '".$_POST['version']."', '".$_POST['address']."', '".$_POST['DB_OS']."', '".$_POST['hypergrid']."')";
			
            
			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " ".$osmw_simu." <strong>".$_POST['NewName']."</strong> ".$osmw_save_user_ok."</p>";
		} 
        
		if($_POST['cmd'] == 'Update')
		{
			$sqlIns = "
                UPDATE moteurs 
                SET 
                    id_os = '".$_POST['id_os']."',
                    name = '".$_POST['name']."',
                    version = '".$_POST['version']."',
                    address = '".$_POST['address']."',
                    DB_OS = '".$_POST['DB_OS']."',
                    hypergrid = '".$_POST['hypergrid']."'
                WHERE osAutorise = '".$_POST['osAutorise']."'
            ";

			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " ".$osmw_simu." <strong>".$_POST['NewName']."</strong> ".$osmw_edit_user_ok."</p>";
		}

		if($_POST['cmd'] == 'Supprimer')
		{			
			$sqlIns = "DELETE FROM moteurs WHERE `moteurs`.`osAutorise` = ".$_POST['osAutorise'];

			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " ".$osmw_simu." <strong>".$_POST['NewName']."</strong> ".$osmw_delete_user_ok."</p>";
		}
		// Exécution de la requete
		if($sqlIns){$bdd->query($sqlIns);}
    }

    //******************************************************
    //  Affichage page principale
    //******************************************************

    echo '<p>'.$osmw_label_totl_simulator.' <span class="badge">'.NbOpensim().'</span></p>';
	echo '<table class="table table-hover">';
	echo '<tr class="info">';
	echo '<th>Name Tmux</th>';
	echo '<th>Version</th>';
	echo '<th>Path BIN</th>';
	echo '<th>HG url</th>';
	echo '<th>Path INI private</th>';
	echo '<th>Save</th>';
    echo '<th>Delete</th>';
	echo '<th>Load Tmux</th>';
	echo '<th>Kill Tmux</th>';
	echo '<th>State Tmux</th>';
	echo '</tr>';
	
		// on se connecte a MySQL
	try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
	catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}

	$reponse = $bdd->query('SELECT * FROM moteurs');

	// On affiche chaque entrée une à une
	while ($data = $reponse->fetch())
	{
			$cmd = 'tmux ls | grep '.$data['name'];
			$retour =  CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
			$name_tmux = explode(":",$retour);

			if ($name_tmux[0] == $data['name'])
			{$check_tmux ='<p class="btn btn-success"><i class="glyphicon glyphicon-ok-sign"></i></p>';}
			else{$check_tmux ='<p class="btn btn-danger"><i class="glyphicon glyphicon-remove-sign"></i></p>';}

		echo '<tr>';
		echo '<form method=post action="">';
		echo '<input type="hidden" name="osAutorise" value="'.$data['osAutorise'].'" >';
		echo '<input type="hidden" name="id_os" value="'.$data['id_os'].'" >';
		echo '<tr>';
		echo '<td><input class="form-control" type="text" name="name" value="'.$data['name'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="version" value="'.$data['version'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="address" value="'.$data['address'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="hypergrid" value="'.$data['hypergrid'].'" '.$btnN3.'></td>';
		echo '<td><input class="form-control" type="text" name="DB_OS" value="'.$data['DB_OS'].'" '.$btnN3.'></td>';
		echo '<td><button class="btn btn-success" type="submit" name="cmd" value="Update" '.$btnN3.'><i class="glyphicon glyphicon-edit"></i></button></td>';
        echo '<td><button class="btn btn-danger" type="submit" name="cmd" value="Supprimer" '.$btnN3.'><i class="glyphicon glyphicon-trash"></i></button></td>';
		echo '<td><button class="btn btn-warning" type="submit" name="cmd" value="tmux_load" '.$btnN3.'><i class="glyphicon glyphicon-play"></i></button></td>';
		echo '<td><button class="btn btn-danger" type="submit" name="cmd" value="tmux_kill" '.$btnN3.'><i class="glyphicon glyphicon-stop"></i></button></td>';
		echo '<td>'.$check_tmux.'</td>';
		echo '</tr>';	
		echo '</form>';
		echo '</tr>';
	}
	
	echo '</table>';
    $reponse->closeCursor(); 
}
else {header('Location: index.php');}
?>
