
<?php 
/*
 foreach($_POST as $key => $val) echo '$_POST["'.$key.'"]='.$val.'<br />';
 foreach($_GET as $key => $val) echo '$_GET["'.$key.'"]='.$val.'<br />';
 foreach($_SESSION as $key => $val) echo '$_SESSION["'.$key.'"]='.$val.'<br />';
 */
 
/*
$api_key ="TRn9bh2Jg4dw1kBWf35mcF6KDrSNsQixUYZyHuEMCzGt8XL7jepqvoVAaP";

// Return Ok else NOK

//http://YOUR_OSMW_WEBSERVER/api/?api_key=API_KEY_CONFIG&opensim_select=NAME_OPENSIMULATOR&cmd=COMMANDE
// COMMANDE FOR SIMULATOR:
Start
Stop
Restart
Alerte General&msg_alert=MESSAGE 
Region Root
Update Client
FCache Assets
FCache ClearF
Generate Map
Windlight enable
Windlight disable
Windlight load
Elevate
Lower
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
*/


require_once ('../inc/config/config.php');
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
		
		if($_GET['cmd'] == 'Restart')			{$parameters = array('command' => 'restart');}
		if($_GET['cmd'] == 'Alerte General')	{$parameters = array('command' => 'alert '.$_GET["msg_alert"]);}
		if($_GET['cmd'] == 'Region Root')		{$parameters = array('command' => 'change region root');}
		if($_GET['cmd'] == 'Update Client')		{$parameters = array('command' => 'force update');}
		if($_GET['cmd'] == 'FCache Assets')		{$parameters = array('command' => 'fcache assets');}
		if($_GET['cmd'] == 'FCache ClearF')		{$parameters = array('command' => 'fcache clear file');}		
		if($_GET['cmd'] == 'Generate Map')		{$parameters = array('command' => 'generate map');}
		if($_GET['cmd'] == 'Windlight enable')	{$parameters = array('command' => 'windlight enable');}
		if($_GET['cmd'] == 'Windlight disable')	{$parameters = array('command' => 'windlight disable');}
		if($_GET['cmd'] == 'Windlight load')	{$parameters = array('command' => 'windlight load');}
		if($_GET['cmd'] == 'Elevate')			{$parameters = array('command' => 'terrain elevate 1');}
		if($_GET['cmd'] == 'Lower')				{$parameters = array('command' => 'terrain lower 1');}
		if($_GET['cmd'] == 'StartLogin')		{$parameters = array('command' => 'login enable');}
		if($_GET['cmd'] == 'StopLogin')			{$parameters = array('command' => 'login disable');}
		if($_GET['cmd'] == 'StatusLogin')		{$parameters = array('command' => 'login status');}
		if($_GET['cmd'] == 'kick_user')			{ $kick = 'kick user '.$_GET["avatar_name"].' ejected by administrator.' ;$parameters = array('command' =>  $kick );}
		if($_GET['cmd'] == 'appearance_user')	{ $appearance = 'appearance show '.$_GET["avatar_name"] ;$parameters = array('command' =>  $appearance );}
		if($_GET['cmd'] == 'estate_name')		{$estate_name = 'estate set name 101 "'.$_GET["estate_name"].'"' ;$parameters = array('command' => $estate_name );}
		if($_GET['cmd'] == 'estate_owner')		{$estate_owner = 'estate set owner 101 '.$_GET["estate_owner"] ;$parameters = array('command' => $estate_owner );}
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
	echo $messageInfo;
	$_SESSION = array();
}
else {echo "NO API KEY";}


//--------------------------------------------------------------------------------------------
    /* Fonction envoi commande SSH  */
    function CommandeSSH($hostname2,$usernameSSH2,$passwordSSH2,$commande)
    {
        if($commande <> '')
        {
            if (!function_exists("ssh2_connect")) die(" function ssh2_connect doesn't exist");
            // log in at server1.example.com on port 22
            if(!($con = ssh2_connect($hostname2, 22))){
                echo " fail: unable to establish connection\n";
            } else 
            {// try to authenticate with username root, password secretpassword
                if(!ssh2_auth_password($con,$usernameSSH2,$passwordSSH2)) {
                    echo "fail: unable to authenticate\n";
                } else {
                //echo " ok: logged in...\n";
                    if (!($stream = ssh2_exec($con, $commande ))) {
                        echo " fail: unable to execute command\n";
                    } else {
                        // collect returning data from command
                        stream_set_blocking($stream, true); $data = "";
                        while ($buf = fread($stream,4096)) 
                        {
                        $data .= $buf."\n";}
                       // echo $data;                   
                        fclose($stream);
                    }
                }
            } 
		return $data;    
        }
    }
?>
