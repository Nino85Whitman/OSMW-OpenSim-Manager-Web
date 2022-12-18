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

    echo '<h1>'.$osmw_index_15.'</h1>';
    echo '<div class="clearfix"></div>';
	    //******************************************************
    //  Affichage page principale
    //******************************************************
        // on se connecte a MySQL
    try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
    catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }

	if (isset($_POST['cmd']))
	{
		$clesprivilege = "";
		// ******************************************************
		// ****************** ACTION BOUTON *********************
		// ******************************************************

		if ($_POST['cmd'] == 'Reset')
		{
            echo '<h3>'.$osmw_label_modifier_password.'</h3>';
			echo '<form method="post" action="">';
			echo '<table class="table table-hover">';
			echo '<input type="hidden" name="oldFirstName" value="'.$_POST['NewFirstName'].'" >';
			echo '<input type="hidden" name="oldLastName" value="'.$_POST['NewLastName'].'" >';

            echo '<tr class="info">';
            echo '<th>Firstname</th>';
            echo '<th>Lastname</th>';
            echo '<th>'.$osmw_label_new_password.'</th>';
            echo '<th>'.$osmw_label_new_password_confirm.'</th>';
            echo '<th>Action</th>';
            echo '</tr>';

			echo '<tr>';
            echo '<td>'.$_POST['NewFirstName'].'</td>';
            echo '<td>'.$_POST['NewLastName'].'</td>';
			echo '<td><input class="form-control" type="password" name="NewPass1" value="" '.$btnN3.'></td>';
			echo '<td><input class="form-control" type="password" name="NewPass2" value="" '.$btnN3.'></td>';
			echo '<td><input class="btn btn-success" type="submit" value="'.$osmw_btn_modifier.'" name="cmd" '.$btnN3.'></td>';
			echo '</tr>';
			echo '</table>';
			echo '</form>';
		}

		// ******************************************************
		if ($_POST['cmd'] == 'Ajouter')
		{
            echo '<button class="btn btn-danger pull-right" type="submit" value="Annuler" onclick=location.href="index.php?a=15" '.$btnN3.'>';
            echo '<i class="glyphicon glyphicon-remove"></i> '.$osmw_btn_annuler.'</button>';

            echo '<h3>'.$osmw_btn_ajout_user.'</h3>';

            echo '<form method="post" action="">';
			echo '<table class="table table-hover">';

            echo '<tr class="info">';
            echo '<th>'.$osmw_label_simulator.'</th>';
            echo '<th>Privilege</th>';
            echo '<th>Firstname</th>';
            echo '<th>Lastname</th>';
            echo '<th>Password</th>';
            echo '<th>Action</th>';
            echo '</tr>';

			echo '<tr>';
            echo '<td>';
            $sql = 'SELECT * FROM moteurs';
            $reponse = $bdd->query($sql);
            
            while ($data = $reponse->fetch())
			{
                echo '<div class="checkbox">';
                echo '<label><input type="checkbox" value="'.$data['osAutorise'].'" name="'.$data['id_os'].'">'.$data['name'].'</label>';
                echo '</div>';
            }
            echo '</td>';
			echo '<td>';
            echo '<select class="form-control" name="username_priv">';
            echo '<option value="1">Level 1</option>';
            echo '<option value="2">Level 2</option>';
            echo '<option value="3" >Level 3</option>';
            echo '</select>';
			echo '</td>';

			echo '<td><input class="form-control" type="text" name="NewFirstName" placeholder="Firstname" '.$btnN3.'></td>';
			echo '<td><input class="form-control" type="text" name="NewLastName" placeholder="Lastname" '.$btnN3.'></td>';
			echo '<td><input class="form-control" type="password" name="username_pass" placeholder="Password" '.$btnN3.'></td>';
            echo '<td>';
            echo '<button class="btn btn-success" type="submit" value="Enregistrer" name="cmd" '.$btnN3.'>';
            echo '<i class="glyphicon glyphicon-ok"></i> '.$osmw_btn_enregistrer;
            echo '</button>';
            echo '</td>';
			echo '</tr>';

            echo '<tr>';
            echo '<td colspan="6">';
            echo '<div class="alert alert-warning fade in">';
            echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
            echo '<i class="glyphicon glyphicon-info-sign"></i>';
            echo '  <strong>Level 1</strong>: '.$osmw_label_simu_ok.' -- <strong>Level 1</strong>: '.$osmw_label_simu_nok ;
            echo '</div>';
            echo '</td>';
            echo '</tr>';

			echo '</table>';
			echo '</form>';
		}

		// ******************************************************
		if ($_POST['cmd'] == 'Change')
		{
		    if ($_POST['NewPass1'] == $_POST['NewPass2'])
		    {	
		        $encryptedPassword = sha1($_POST['NewPass1']);
		        $sqlUp = "
                    UPDATE users 
                    SET `password` = '".$encryptedPassword."' 
                    WHERE `firstname` = '".$_POST['oldFirstName']."' 
                    AND `lastname` = '".$_POST['oldLastName']."'
                ";	
		        $reponse = $bdd->query($sqlUp);

                echo "<p class='alert alert-success alert-anim'>";
                echo "<i class='glyphicon glyphicon-ok'></i>";
		        echo $osmw_change_user_ok."</p>";
		    }
			
			else
			{
                echo "<p class='alert alert-success alert-anim'>";
                echo "<i class='glyphicon glyphicon-ok'></i>";
			    echo $osmw_change_user_nok."!</p>";
			}
		}

		// ******************************************************
		if ($_POST['cmd'] == 'Enregistrer')
		{
			$sql = 'SELECT * FROM moteurs';
			$reponse = $bdd->query($sql);

			while ($data = $reponse->fetch())
			{
			    if ($_POST[$data['id_os']] != '')
				{
				    if($clesprivilege =='' ){$clesprivilege = $_POST[$data['id_os']];}else{$clesprivilege = $clesprivilege.'|'.$_POST[$data['id_os']];}
				}
			}

			$encryptedPassword = sha1($_POST['username_pass']);
			$sqlIns = "
                INSERT INTO users (`firstname` ,`lastname` ,`password` ,`privilege`, `osAutorise`)
                VALUES (
                    '".$_POST['NewFirstName']."', 
                    '".$_POST['NewLastName']."', 
                    '".$encryptedPassword."', '".$_POST['username_priv']."', 
                    '".$clesprivilege."'
                )
            ";
			$reponse = $bdd->query($sqlIns);

			echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " User <strong>".$_POST['NewFirstName']." ".$_POST['NewLastName']."</strong> ".$osmw_save_user_ok."</p>";  
		}

		// ******************************************************
		if ($_POST['cmd'] == 'Modifier')
		{
            $sql = 'SELECT * FROM moteurs';
            $reponse = $bdd->query($sql);

            while ($data = $reponse->fetch())
            {
                if ($_POST[$data['id_os']] != '')
                {
					if($clesprivilege =='' ){$clesprivilege = $_POST[$data['id_os']];}else{$clesprivilege = $clesprivilege.'|'.$_POST[$data['id_os']];}
                }
            }

            $sqlUp = "
                UPDATE users 
                SET `firstname` = '".$_POST['NewFirstName']."', 
                    `lastname` = '".$_POST['NewLastName']."', 
                    `privilege` = '".$_POST['username_priv']."', 
                    `osAutorise` = '".$clesprivilege."' 
                WHERE `firstname` = '".$_POST['oldFirstName']."' 
                AND `lastname` = '".$_POST['oldLastName']."'
            ";
            $reponse = $bdd->query($sqlUp);

            echo "<p class='alert alert-success alert-anim'>";
            echo "<i class='glyphicon glyphicon-ok'></i>";
            echo " User <strong>".$_POST['NewFirstName']." ".$_POST['NewLastName']."</strong> ".$osmw_edit_user_ok."</p>";            
        }
    }
		
    // ******************************************************
    if ($_POST['cmd'] == 'Supprimer')
    {	
        $sqlDel = "
            DELETE FROM users 
            WHERE `firstname` = '".$_POST['oldFirstName']."' 
            AND `lastname` = '".$_POST['oldLastName']."' 
        ";	
        $reponse = $bdd->query($sqlDel);

        echo "<p class='alert alert-success alert-anim'>";
        echo "<i class='glyphicon glyphicon-ok'></i>";
        echo " User <strong>".$_POST['NewFirstName']." ".$_POST['NewLastName']."</strong> ".$osmw_delete_user_ok."</p>";  
    }

    // ******************************************************
    // ************** LISTE DES UTILISATEURS ****************
    // ******************************************************
    if ($_POST['cmd'] != 'Ajouter')
    {
        echo '<form class="form-group pull-right" method="post" action="">';
        echo '<input type="hidden" value="Ajouter" name="cmd" '.$btnN3.'>';
        echo '<button class="btn btn-success" type="submit" '.$btnN3.'>';
        echo '<i class="glyphicon glyphicon-ok"></i> '.$osmw_btn_ajout_user.'</button>';
        echo '</form>';

        echo '<h3>'.$osmw_label_list_user.'</h3>';

        $sql = 'SELECT * FROM users ORDER BY id ASC';
        $reponse = $bdd->query($sql);

        while ($data = $reponse->fetch()) {$n++;}

        echo '<p>'.$osmw_label_total_user.' <span class="badge">'.$n.'</span></p>';

        echo '<table class="table table-hover">';
        echo '<tr class="info">';
        echo '<th>#</th>';
        echo '<th>'.$osmw_label_simulator.'</th>';
        echo '<th>Privilege</th>';
        echo '<th>Firstname</th>';
        echo '<th>Lastname</th>';
        echo '<th>Password</th>';
        echo '<th>'.$osmw_btn_modifier.'</th>';
        echo '<th>'.$osmw_btn_supprimer.'</th>';
        echo '</tr>';

        $sql = 'SELECT * FROM users ORDER BY id ASC';
        $reponse = $bdd->query($sql);
        $n = 0;

        while ($data = $reponse->fetch())
        {
            $n++;
            $privilegetxt1 = $privilegetxt2 = $privilegetxt3 = 0;
            $privilege = $data['privilege'];
            $oldbtnN3 =  $btnN3;

            switch ($privilege)
            {
                case 1: $privilegetxt1 = "selected"; break;
                case 2: $privilegetxt2 = "selected"; break;
                case 3: $privilegetxt3 = "selected"; break;
                case 4: 

                if ($_SESSION['privilege'] == 4)
                {
                    $privilegetxt4 = "<option value='4' selected>Level 4</option>";
                    $block = "";
                    $btnN3 = "";
                    break;
                }

                else
                {
                    $privilegetxt4 = "<option value='4' selected>Level 4</option>";
                    $block = "disabled";
                    $btnN3 = "disabled";
                    break;
                }
            }

            echo '<tr>';
            echo '<form class="form-group" method="post" action="">';
            echo '<td><div class="badge">'.$n.'</div></td>';

            if ($data['privilege'] > 1) echo '<td>'.$osmw_label_all_simulator.'</td>';

            if ($data['privilege'] == 1)
            {
                echo '<td>';
                
                $sql1 = 'SELECT * FROM moteurs';
                $reponse = $bdd->query($sql1);

                while ($data1 = $reponse->fetch())
                {
                    $moteursOK = "";
                    $osAutorise = explode("|", $data['osAutorise']);

                    // echo "osAutorise =  ".$data['osAutorise'];

                    for ($i = 0; $i < count($osAutorise); $i++)
                    {
                        if ($data1['osAutorise'] == $osAutorise[$i])
                        {
                            $moteursOK = "CHECKED";
                            break;
                        }
                    }

                    echo '<div class="checkbox">';
                    echo '<label>';
                    echo '<label><input type="checkbox" value="'.$data1['osAutorise'].'" name="'.$data1['id_os'].'" '.$moteursOK.'>'.$data1['name'].'</label>';
                    echo '</div>';
                }
                echo '</td>';
            }

            echo '<td>';
            echo '<input type="hidden" name="oldFirstName" value="'.$data['firstname'].'" >';
            echo '<input type="hidden" name="oldLastName" value="'.$data['lastname'].'" >';

            echo '<select class="form-control" name="username_priv" '.$block.'>';
            echo '<option value="1" '.$privilegetxt1.' >Level 1</option>';
            echo '<option value="2" '.$privilegetxt2.'>Level 2</option>';
            echo '<option value="3" '.$privilegetxt3.'>Level 3</option>';
			if( $_SESSION['privilege']>= 4)
				{echo '<option value="4" '.$privilegetxt4.'>Level 4</option>';}
			else
				{echo '<option value="4" '.$privilegetxt4.' disabled>Level 4</option>';}
            echo '</select>';
            echo '</td>';

            echo '<td><input class="form-control" type="text" name="NewFirstName" value="'.$data['firstname'].'" '.$btnN3.'></td>';
            echo '<td><input class="form-control" type="text" name="NewLastName" value="'.$data['lastname'].'" '.$btnN3.'></td>';
            echo '<td><button class="btn btn-danger" type="submit" name="cmd" value="Reset" '.$btnN3.' disabled><i class="glyphicon glyphicon-refresh"></i> '.$osmw_btn_reset.'</button></td>';

            echo '<td><button class="btn btn-success" type="submit" value="Modifier" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-edit"></i> '.$osmw_btn_modifier.'</button></td>';
            echo '<td><button class="btn btn-danger" type="submit" value="Supprimer" name="cmd" '.$btnN3.'><i class="glyphicon glyphicon-trash"></i> '.$osmw_btn_supprimer.'</button></td>';

            echo '</form>';
            echo '</tr>';

            if ($data['privilege'] == "1")
            {
                echo '<tr>';
                // echo '<td></td><td></td>';
                echo '<td colspan="8">';
                echo '<div class="alert alert-warning fade in">';
                echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                echo '<i class="glyphicon glyphicon-info-sign">  </i>';
                echo '  <strong>Level 1</strong>: '.$osmw_label_simu_ok.' -- <strong>Level 1</strong>: '.$osmw_label_simu_nok ;
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }

            $btnN3 = $oldbtnN3;
            $privilegetxt4 = "";
            $block = "";
        }
    }

    echo '</table>';
	$reponse->closeCursor();
}
else {header('Location: index.php');}
?>