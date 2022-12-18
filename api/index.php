
<?php 
/*
 foreach($_POST as $key => $val) echo '$_POST["'.$key.'"]='.$val.'<br />';
 foreach($_GET as $key => $val) echo '$_GET["'.$key.'"]='.$val.'<br />';
 foreach($_SESSION as $key => $val) echo '$_SESSION["'.$key.'"]='.$val.'<br />';
*/


/*
// Return Ok else NOK
//http://YOUR_OSMW_WEBSERVER/api/?api_key=API_KEY_CONFIG&opensim_select=NAME_OPENSIMULATOR&cmd=COMMANDE
// COMMANDE FOR SIMULATOR:
Start
Stop
GenerateMap
WindlightEnable
WindlightDisable
WindlightLoad
StartLogin
StopLogin
StatusLogin

kick_user&avatar_name=AVATAR_NAME
appearance_user&avatar_name=AVATAR_NAME
estate_name&estate_name=ESTATE
estate_owner&estate_owner=AVATAR_NAME

//http://YOUR_OSMW_WEBSERVER/api/?api_key=API_KEY_CONFIG&opensim_select=NAME_OPENSIMULATOR&cmd_tmux=COMMANDE
// COMMANDE FOR TMUX:
tmux_load
tmux_kill

//http://YOUR_OSMW_WEBSERVER/api/?api_key=API_KEY_CONFIG&cmd_tmux=COMMANDE
// COMMANDE FOR TMUX:
tmux_load_all
tmux_kill_all

//http://YOUR_OSMW_WEBSERVER/api/?api_key=API_KEY_CONFIG&opensim_select=NAME_OPENSIMULATOR&cmd_gest=COMMANDE
// COMMANDE DE GESTION
list_simulateurs
list_regions

*/


if ($_GET['api_key'] == $api_key || $_POST['api_key'] == $api_key)
{
	$_SESSION['authentification_api'] = "autorized";
	$opensim_select ="";
	
	if ($_GET['opensim_select']){$opensim_select = $_GET['opensim_select'];}
	if ($_POST['opensim_select']){$opensim_select = $_POST['opensim_select'];}
	
	if ($_GET['msg_alert']){$msg_alert = $_GET['msg_alert'];}
	if ($_POST['msg_alert']){$msg_alert = $_POST['msg_alert'];}
	
	if ($_GET['avatar_name']){$avatar_name = $_GET['avatar_name'];}
	if ($_POST['avatar_name']){$avatar_name = $_POST['avatar_name'];}	
	
	if ($_GET['estate_name']){$estate_name = $_GET['estate_name'];}
	if ($_POST['estate_name']){$estate_name = $_POST['estate_name'];}	

	if ($_GET['estate_owner']){$estate_owner = $_GET['estate_owner'];}
	if ($_POST['estate_owner']){$estate_owner = $_POST['estate_owner'];}
	
	// AJOUTER FONCTION TEST DES COMMANDEES AUTORISEES 
	
	// TOUT PASSE EN POST
	
}
else{echo "ERROR API KEY";exit;}

//############################################################################################################

if (isset($_SESSION['authentification_api']))
{
	
	require_once ('../inc/config/config.php');
	require_once ('../inc/config/fonctions.php');
	require_once ('../inc/config/radmin.php');
	
	try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
	catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }
		
	$req_sql = "SELECT * FROM moteurs WHERE id_os ='".$opensim_select."'";
	$reponse = $bdd->query($req_sql);	
    $data = $reponse->fetch();
	$FichierConfINIPrivate = $data['address']. $data['DB_OS'];
	$reponse->closeCursor(); 	
	 
	$tableauIniSimu = parse_ini_file($FichierConfINIPrivate, true);
	$RemotePort  =  $tableauIniSimu['RemoteAdmin']['port'] ;
	$access_password2  =  $tableauIniSimu['RemoteAdmin']['access_password'] ;

	$messageInfo = "NOK"; 
	
	//#################################################################################################################
	if (isset($_GET['cmd']))
	{
		if($_GET['cmd'] == 'get'){$messageInfo = "OK";}	
		if($_GET['cmd'] == 'info'){$messageInfo = $_SERVER['SERVER_NAME'];}	
			
		if($_GET['cmd'] == 'Start')	
		{			
			// LINUX ***********************
			if(PHP_OS == "Linux")
			{
				$cmd = 'tmux send-keys -t '.$data['name'].' "cd '.$data['address'].';./opensim.sh" Enter';
				CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
				$messageInfo = "OK"; 
			}
		}	

		if($_GET['cmd'] == 'Stop')				{
			$parameters = array('command' => 'quit'); 
			$cmd = "rm ".$data['address']."OpenSim.log";
			CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
			$messageInfo = "OK";
		}
		
		if($_GET['cmd'] == 'GenerateMap')		{$parameters = array('command' => 'generate map');}
		
		if($_GET['cmd'] == 'WindlightEnable')	{$parameters = array('command' => 'windlight enable');}
		if($_GET['cmd'] == 'WindlightDisable')	{$parameters = array('command' => 'windlight disable');}
		if($_GET['cmd'] == 'WindlightLoad')		{$parameters = array('command' => 'windlight load');}

		if($_GET['cmd'] == 'StartLogin')		{$parameters = array('command' => 'login enable');}
		if($_GET['cmd'] == 'StopLogin')			{$parameters = array('command' => 'login disable');}
		if($_GET['cmd'] == 'StatusLogin')		{$parameters = array('command' => 'login status');}
		
		if($_GET['cmd'] == 'Alerte')			{$parameters = array('command' => 'alert '.$msg_alert);}
		if($_GET['cmd'] == 'kick_user')			{$kick = 'kick user '.$avatar_name.' ejected by administrator.' ; $parameters = array('command' =>  $kick );}
		if($_GET['cmd'] == 'appearance_user')	{$appearance = 'appearance show '.$avatar_name ; $parameters = array('command' =>  $appearance );}

		//if($_POST['cmd'] == 'estate_name')		{echo $estate_cmd = 'estate set name 101 "'.$estate_name.'"' ; $parameters = array('command' => $estate_cmd );}
		//if($_POST['cmd'] == 'estate_owner')		{echo $estate_cmd = 'estate set owner 101 '.$estate_owner ; $parameters = array('command' => $estate_cmd );}
			
			echo $estate_cmd;
			
		if($_GET['cmd']=='ReloadEstate')	
		{
			$myRemoteAdmin = new RemoteAdmin(trim($hostname), trim($RemotePort), trim($access_password2));
			$myRemoteAdmin->SendCommand('admin_estate_reload',  array());
		}		
		else
		{
			$myRemoteAdmin = new RemoteAdmin(trim($hostname), trim($RemotePort), trim($access_password2));
			$retour_radmin = $myRemoteAdmin->SendCommand('admin_console_command', $parameters);
			$messageInfo = "OK";
		}

	}
	//#################################################################################################################
	if (isset($_GET['cmd_tmux']))
	{

		$osmw_simu =$_GET['opensim_select'];
		// on se connecte a MySQL
		try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
		catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}
		
		if($_GET['cmd_tmux'] == 'tmux_load')
		{
			$cmd = 'tmux new -d -s '.$_GET['opensim_select'];
			CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
			$messageInfo = "OK"; 
		}				
		if($_GET['cmd_tmux'] == 'tmux_kill')
		{
			$cmd = 'tmux kill-session -t '.$_GET['opensim_select'];
			CommandeSSH($hostname,$usernameSSH,$passwordSSH,$cmd);
			$messageInfo = "OK"; 
		}				
		if($_GET['cmd_tmux'] == 'tmux_kill_all')
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
			$messageInfo = "OK"; 
		}	
		if($_GET['cmd_tmux'] == 'tmux_load_all')
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
			$messageInfo = "OK"; 
		}	
	}
	//#################################################################################################################
	if (isset($_GET['cmd_gest']))
	{
		$list ="";
		if($_GET['cmd_gest'] == 'list_simulateurs')
		{
			// on se connecte a MySQL
			try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
			catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}
			$reponse = $bdd->query('SELECT * FROM moteurs');
			
			$list ='';
			// On affiche chaque entrée une à une
			while ($data = $reponse->fetch())
			{
					$list .= $data['id_os'].";";
			}
			$messageInfo = "OK;" . $list;
			
		}
		//****************************************************************************************
		if($_GET['cmd_gest'] == 'list_regions')
		{

		$filename2 = $data['address']."Regions/Regions.ini";	 
	
		if (file_exists($filename2)) {$filename = $filename2;}
		$tableauIni = parse_ini_file($filename, true);
		if ($tableauIni == FALSE) {$messageInfo = "OK;";}

		// *** Recuperation du port Http du Simulateur
		$FichierConfINIPrivate = $data['address']. $data['DB_OS'];
		$tableauIniSimu = parse_ini_file($FichierConfINIPrivate, true);
		$srvOS  = $tableauIniSimu['Network']['http_listener_port'];
				 
		$tableauIni = parse_ini_file($filename, true);
		//print_r($tableauIni);
		
		$list ="";
		while (list($key, $val) = each($tableauIni))	{$list = $list.$key.";";}

		$messageInfo = "OK;" . $list;
		}		
	}
	//#################################################################################################################
	
	echo $messageInfo;
	$_SESSION = array();
}
else {echo "NO API KEY";}


?>
