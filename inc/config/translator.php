<?php
if (!empty($_COOKIE['lang']))$lang=$_COOKIE['lang'];
if (!empty($_GET['lang']))$lang=$_GET['lang'];
if (!empty($lang) && array_key_exists($lang, $languages))
{
    include_once('./inc/lang/lang_'.$lang.'.php');
    setcookie('lang',$lang,time()+3600*25*365,'/');
}
else include_once('./inc/lang/lang_fr.php'); // Fr by default
?>
