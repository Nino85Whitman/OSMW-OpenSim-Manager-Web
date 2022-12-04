<?php
/*
 foreach($_POST as $key => $val) echo '$_POST["'.$key.'"]='.$val.'<br />';
 foreach($_GET as $key => $val) echo '$_GET["'.$key.'"]='.$val.'<br />';
 foreach($_SESSION as $key => $val) echo '$_SESSION["'.$key.'"]='.$val.'<br />';
*/
$fichier = './inc/config/config.php';
if (file_exists($fichier) AND filesize($fichier ) > 0)
{
	require_once ('inc/config/config.php');
	require_once ('inc/config/fonctions.php');
	require_once ('inc/config/radmin.php');

	if ($_GET['a'] == 'logout')
	{
		$_SESSION = array();
		session_destroy();
		session_unset();
		header('Location: index.php');
	}
	session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">
    <title>OpenSimulator Manager Web</title>
    <link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" media="all" type="text/css" id="css" href="<?php echo $url; ?>" />
    <link rel="stylesheet" href="css/btn3d.css" type="text/css" />
    <link rel="stylesheet" href="css/login.css" type="text/css" />
    <link rel="stylesheet" href="css/custom.css" type="text/css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="js/dropdown.js"></script>
	<script src="js/button.js"></script>
	
</head>
<body>
<div class="container">

<?php

// *********************************************************
// IDENTIFICATION ET INITIALISATION Variable OPENSIM[SELECT]
// *********************************************************
if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['pass']))
{

	$_SESSION['login'] = $_POST['firstname'].' '. $_POST['lastname'];
    $auth = false;
	$passwordHash = sha1($_POST['pass']);

	// on se connecte a MySQL
	try{$bdd = new PDO('mysql:host='.$hostnameBDD.';dbname='.$database.';charset=utf8', $userBDD, $passBDD);}
	catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}

	$reponse = $bdd->query('SELECT * FROM users');

	// On affiche chaque entrée une à une
	while ($data = $reponse->fetch())
	{
		if ($data['firstname'] == $_POST['firstname'] and $data['lastname'] == $_POST['lastname'] and $data['password'] == $passwordHash)
		{
			$auth = true;
			$_SESSION['privilege'] = $data['privilege'];
			$_SESSION['osAutorise'] = $data['osAutorise'];
			$_SESSION['authentification']=true;
			$_SESSION['zooming_select']=50;
			$_GET['a'] ="GestSims";
			 break;
		}
	}
	
    if ($auth == false)
    {
        echo '<div class="alert alert-danger alert-anim">'.$osmw_erreur_acces .'</div>';
        header('Location: index.php?erreur=login');
    }
    else
    {
        $reponse = $bdd->query('SELECT * FROM moteurs');
		while($data = $reponse->fetch())
        {
            $_SESSION['opensim_select'] = $data['id_os'];
			break;
        }
    }
	$reponse->closeCursor(); 
}


if ($translator && isset($_SESSION['authentification']))
{
    require_once ('./inc/config/translator.php');
	echo('<div class="pull-right">');
	echo '<span class="label label-danger">Security level <span class="label label-default">'.$_SESSION['privilege'].'</span></span>   ';
	include_once("./inc/config/flags.php");
	echo('</div>');
}

// **********************
// PAGE EN ACCES SECURISE
// **********************
// Verification sur la session authentification 
if (isset($_SESSION['authentification']))
{
	// DISPLAY BOOTSTRAP MENU
	include_once './inc/config/menu.php';
	
	// Si le moteur selectionne a change
	if (isset($_POST['OSSelect'])){$_SESSION['opensim_select'] = trim($_POST['OSSelect']);}
	
    if ($_GET['a'])
    {
        $a = $_GET['a'];
		//***************************************** OpenSim Manager Web **************************************************************************
        if ($a == "GestSims") {include('inc/GestSims.php');	}           				// # Gestion Simulator
        if ($a == "GestSave") {include('inc/GestSave.php');}     						// # Gestion backup
        if ($a == "Map") {include('inc/GestMap.php');}           						// # Map
        if ($a == "AdminLands") {include('inc/admin/GestRegion.php');}         			// admin // # Gestion des Regions par moteur
        if ($a == "AdminUsers") {include('inc/admin/GestAdminUsers.php');}    			// admin // # Gestion des utilisateurs
        if ($a == "AdminSims") {include('inc/admin/GestSimulateur.php');}    			// admin // # Gestion des simulateurs
        if ($a == "AdminOsmw") {include('inc/admin/GestConfig.php');}        			// admin // # Configuration de OSMW
		if ($a == "AdminEdit") {include('inc/admin/GestOpensim.php');}        			// admin // # Edition des fichiers de config		
		if ($a == "GestUsers") {include('inc/GestUsers.php');}   						// # Gestion du compte utilisateur en cours
		
        //***************************************** *******************************************************************************************
        if ($a == "logout")
        {
            session_start();
            $_SESSION = array();
            session_unset();
            header('Location: index.php');
        }
	}else{
		echo Affichage_Entete($_SESSION['opensim_select']);
	    echo '<div class="alert alert-warning fade in">';
        echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
        echo '<i class="glyphicon glyphicon-info-sign"></i> ';
        echo $osmw_message_home  ;
        echo '</div>';
	}
}
else
{
?>

<div class="text-center"><h2 class="title"><span><br>OSMW</span></h2></div>
<form class="form-signin" action="index.php" method="post" name="connect">
    <?php if (isset($_GET['erreur']) && ($_GET['erreur'] == "login")): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Echec d'authentification: login ou mot de passe incorrect ...</strong>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['erreur']) && ($_GET['erreur'] == "delog")): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Deconnexion reussie, a bientot ...
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['erreur']) && ($_GET['erreur'] == "intru")): ?>
        <!-- Affiche l'erreur -->
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Echec d'authentification:<br> Aucune session ouverte ou droits insuffisants pour afficher cette page ...</strong>
        </div>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Deconnexion reussie, a bientot ...</strong>
        </div>
    <?php endif; ?>

    <img style="height:256px;" class="img-thumbnail img-circle center-block" alt="Logo Server" src="img/logo.png">
    <!--<h2 class="form-signin-heading text-center"></h2>-->
    <br />
    <label for="firstname" class="sr-only">Firstname</label>
        <input type="text" id="firstname" name="firstname" class="form-control" placeholder="First Name" required autofocus>
    <label for="lastname" class="sr-only">Lastname</label>
        <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Last Name" required>
    <label for="pass" class="sr-only">Password</label>
        <input type="password" id="pass" name="pass" class="form-control" placeholder="Password" required>

    <button class="btn btn-lg btn-default btn-block" type="submit">
        <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Authentification
    </button>
</form>
<?php } ?>

<div class="clearfix"></div>

</div>

<footer class="footer">
   <p class="text-center"> <u><b>OpenSimulator Manager Web by fgagod</b></u> <span class="badge badge-pill badge-info"><?php echo INI_Conf('VersionOSMW', 'VersionOSMW'); ?></span></p>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/pdf.js"></script>

<!-- FADE ALERT -->
<script>
    window.setTimeout(function() {$(".alert-anim").fadeTo(500, 0).slideUp(500, function() {$(this).remove();});}, 3000);
</script>
<script>$(function () {$('[data-toggle="tooltip"]').tooltip();});</script>
<script>$(document).ready(function(){$('[data-toggle="popover"]').popover();});</script>
<script>$(document).ready(function(){$('.fade-in').hide().fadeIn();});</script>


</body>
</html>
<?php
}
else
{	
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="img/favicon.ico">
		<title>OpenSimulator Manager Web</title>
		<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
		<link rel="stylesheet" media="all" type="text/css" id="css" href="<?php echo $url; ?>" />
		<link rel="stylesheet" href="css/btn3d.css" type="text/css" />
		<link rel="stylesheet" href="css/login.css" type="text/css" />
		<link rel="stylesheet" href="css/custom.css" type="text/css" />

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="js/dropdown.js"></script>
		<script src="js/button.js"></script>
	</head>
	<body>

	<div class="container">
	<?php
	// ********************************************************************************************************************************************
	// si le fichier n'existe  pas 
		exit('<div class="alert alert-danger">!!! configuration file not exist, <a href="install.php"> Installation </a> !!! </div>');
	echo '</body>
	</html>';		
}