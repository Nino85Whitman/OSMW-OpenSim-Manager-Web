
<?php 

 foreach($_POST as $key => $val) echo '$_POST["'.$key.'"]='.$val.'<br />';
 foreach($_GET as $key => $val) echo '$_GET["'.$key.'"]='.$val.'<br />';
 foreach($_SESSION as $key => $val) echo '$_SESSION["'.$key.'"]='.$val.'<br />';
 
 
/*
// Return Ok else NOK

//http://YOUR_OSMW_WEBSERVER/api/?api_key=API_KEY_CONFIG&opensim_select=NAME_OPENSIMULATOR&cmd=COMMANDE
// COMMANDE FOR SIMULATOR:
Start
Stop
Generate Map
Estate
Windlight enable
Windlight disable
Windlight load
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
list_simu
list_regions

*/


require_once ('../inc/config/config.php');
require_once ('../inc/config/fonctions.php');
if ($_GET['api_key'] == $api_key || $_POST['api_key'] == $api_key)
{
	$_SESSION['authentification_api'] = "autorized";
	$_SESSION['opensim_select'] = $_GET['opensim_select'];
}
else{echo "ERROR API KEY";exit;}

//############################################################################################################

if (isset($_SESSION['authentification_api']))
{
	require_once ('../inc/config/radmin.php');
	
	try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
	catch (Exception $e){       die('Erreur : ' . $e->getMessage());    }
		
	$req_sql = "SELECT * FROM moteurs WHERE id_os ='".$_SESSION['opensim_select']."'";
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
		
		if($_GET['cmd']=='Estate')	
		{
			$myRemoteAdmin = new RemoteAdmin(trim($hostname), trim($RemotePort), trim($access_password2));
			$myRemoteAdmin->SendCommand('admin_estate_reload',  array());
			$messageInfo = "OK";
		}
		
		if($_GET['cmd'] == 'Update Client')		{$parameters = array('command' => 'force update');}	
		if($_GET['cmd'] == 'Generate Map')		{$parameters = array('command' => 'generate map');}
		
		if($_GET['cmd'] == 'Windlight enable')	{$parameters = array('command' => 'windlight enable');}
		if($_GET['cmd'] == 'Windlight disable')	{$parameters = array('command' => 'windlight disable');}
		if($_GET['cmd'] == 'Windlight load')	{$parameters = array('command' => 'windlight load');}

		if($_GET['cmd'] == 'StartLogin')		{$parameters = array('command' => 'login enable');}
		if($_GET['cmd'] == 'StopLogin')			{$parameters = array('command' => 'login disable');}
		if($_GET['cmd'] == 'StatusLogin')		{$parameters = array('command' => 'login status');}
		
		if($_GET['cmd'] == 'kick_user')			{ $kick = 'kick user '.$_GET["avatar_name"].' ejected by administrator.' ;$parameters = array('command' =>  $kick );}
		if($_GET['cmd'] == 'appearance_user')	{ $appearance = 'appearance show '.$_GET["avatar_name"] ;$parameters = array('command' =>  $appearance );}
		if($_GET['cmd']<>"")
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

		$osmw_simu =$_GET['opensim_select'];
		// on se connecte a MySQL
		try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
		catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}
		
		if($_GET['cmd_tmux'] == 'list_simu')
		{

			$messageInfo = "OK"; 
		}
		if($_GET['cmd_tmux'] == 'list_regions')
		{

			$messageInfo = "OK"; 
		}		
	}
	//#################################################################################################################
	echo $messageInfo;
	$_SESSION = array();
}
else {echo "NO API KEY";}


?>
