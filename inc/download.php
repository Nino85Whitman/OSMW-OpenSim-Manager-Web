<?php
if (session_status() == PHP_SESSION_NONE) {session_start();}

$filename = $_GET['file'];

if(ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');

//$file_extension = strtolower(substr(strrchr($filename,"."), 1));

if ($filename == "") 
{
    echo "<html><title> Download Script</title><body>";
    echo "ERROR: download file NOT SPECIFIED. USE inc/download.php?file=filepath";
    echo "</body></html>";
    // $_SESSION[flash][danger] = "ERROR: download file NOT SPECIFIED. USE inc/download.php?file=filepath";
    $_SESSION[flash][danger] = "Une erreur c'est produite, veuillez contacter l'administrateur du site ...";
    header('Location: ../index.php?a=10');
    exit;
}

else if (!file_exists( $filename)) 
{
    echo "<html><title> Download Script</title><body>";
    echo "ERROR: File not found. USE inc/download.php?file=filepath";
    echo "</body></html>";
    // $_SESSION[flash][danger] = "ERROR: File not found. USE inc/download.php?file=filepath";
    $_SESSION[flash][danger] = "Une erreur c'est produite, veuillez contacter l'administrateur du site ...";
    header('Location: ../index.php?a=10');
    exit;
};

/*
switch($file_extension)
{
    case "pdf": $ctype = "application/pdf"; break;
    case "exe": $ctype = "application/octet-stream"; break;
    case "zip": $ctype = "application/zip"; break;
    case "doc": $ctype = "application/msword"; break;
    case "xls": $ctype = "application/vnd.ms-excel"; break;
    case "ppt": $ctype = "application/vnd.ms-powerpoint"; break;
    case "gif": $ctype = "image/gif"; break;
    case "png": $ctype = "image/png"; break;
    case "jpeg":
    case "jpg": $ctype = "image/jpg"; break;
    default: $ctype = "application/force-download";
}

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check = 0, pre-check = 0");
    header("Cache-Control: private", false);
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filename));
    readfile("$filename");
    // return true;
*/	


header("Content-length:".filesize($filename));
header('Content-Type: application/zip'); // ZIP file
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
header('Content-Transfer-Encoding: binary');
ob_end_clean();
readfile($filename);
exit();


?>