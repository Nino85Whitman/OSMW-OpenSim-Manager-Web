<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicon.ico">
    <title>OpenSimulator Manager Web</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="container">
<h1>Open Simulator Manager Web Installer</h1>

<?php if (!isset($_POST['etape'])): ?>

<form class="form-horizontal" action="" method="post">
    <input type="hidden" name="etape" value="1" />

    <div class="form-group">
    <label for="hote" class="col-sm-2 control-label">Database Host :</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="hote" maxlength="40" />
        </div>
    </div>

    <div class="form-group">
    <label for="login" class="col-sm-2 control-label">Database User :</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="login" maxlength="40" />
        </div>
    </div>

    <div class="form-group">
    <label for="mdp" class="col-sm-2 control-label">Database Password :</label>
        <div class="col-sm-4">
            <input class="form-control" type="password" name="mdp" maxlength="40" />
        </div>
    </div>

    <div class="form-group">
    <label for="base" class="col-sm-2 control-label">Database Name :</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="base" maxlength="40" />
        </div>
    </div>
	

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button class="btn btn-success" type="submit" name="submit" value="Installer">Install</button>
        </div>
    </div>

</form>

<?php endif ?>

<?php if (isset($_POST['delete']))
{
    unlink('install.php');
    header('Location: ./');
}
?>

<?php
 foreach($_POST as $key => $val) echo '$_POST["'.$key.'"]='.$val.'<br />';
 foreach($_GET as $key => $val) echo '$_GET["'.$key.'"]='.$val.'<br />';
if (isset($_POST['etape']) AND $_POST['etape'] == 1)
{
    // on crée une constante dont on se servira plus tard
    define('RETOUR', '<input class="btn btn-primary" type="button" value="Return of form" onclick="history.back()">');

    $fichier = './inc/config/config.php';

    if (file_exists($fichier) AND filesize($fichier ) > 0)
    {
        // si le fichier existe et qu'il n'est pas vide alors
        exit('<div class="alert alert-danger">Not this configuration file, installation corrupt ...</div>'. RETOUR);
    }

    // on crée nos variables, et au passage on retire les éventuels espaces	
    $hote   = trim($_POST['hote']);
    $login  = trim($_POST['login']);
    $pass   = trim($_POST['mdp']);
    $base   = trim($_POST['base']);


    // on vérifie la connectivité avec le serveur avant d'aller plus loin
	try{		$bdd = new PDO('mysql:host='.$hote.';dbname='.$base.';charset=utf8', $login, $pass);	}
	catch (Exception $e){		die('Erreur : ' . $e->getMessage());	}

    // le texte que l'on va mettre dans le config.php
    $texte = '
<?php

$hostnameBDD = "'. $hote .'";		// IP of Bdd
$userBDD = "'. $login .'";       	// login
$passBDD  = "'. $pass .'";     	// password
$database = "'. $base .'"; 		// Name of BDD

$hostname = "ip_host";
$usernameSSH = "user_ssh_tmux";
$passwordSSH = "pass_user_tmux";

$api_key ="";

/* Position center of your MAP */
$px = 7000;
$py = 7000;

/* Themes */
$themes = true;

/* Languages */
$translator = true;
$languages=array("fr" => "French","en" => "English");

?>';

    if (!$ouvrir = fopen($fichier, 'w'))
    {
        exit('<div class="alert alert-danger">Unable to open file : <strong>'. $fichier .'</strong>, installation corrupt ...</div>'. RETOUR);
    }

    if (fwrite($ouvrir, $texte) == FALSE)
    {
        exit('<div class="alert alert-danger">Can not write to the file : <strong>'. $fichier .'</strong>, installation corrupt ...</div>'. RETOUR);
    }

    echo '<div class="alert alert-success">Creation of effected configuration file with success ...</div>';
    fclose($ouvrir);


    $requetes = '';
    $sql = file('./inc/config/sql/database.sql');
    foreach($sql as $lecture)
    {
        if (substr(trim($lecture), 0, 2) != '--')
        {
            $requetes .= $lecture;
        }
    }
	$reponse = $bdd->query($requetes);

    echo '<div class="alert alert-success">Installing the database tables of data effected with success...</div>';
    echo '<div class="alert alert-warning">Please delete the file <strong>install.php</strong> of server ...</div>';
    echo '<form class="form-group" action="" method="post">';
    echo '<input type="hidden" name="delete" value="1" />';
    echo '<div class="form-group">';
    echo '<button class="btn btn-danger" type="submit" name="submit" >Delete file install.php</button>';
    echo '</div>';
    echo '</form>';
	
}

?>
<div class="clearfix"></div>

<footer class="footer">
    <p><CENTER>Open Simulator Manager Web Intaller <?php echo date(Y); ?></CENTER></p>
</footer>
</div>

</body>
</html>
