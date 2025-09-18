<!--?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
Tested working with PHP5.4 and above (including PHP 7 )

 */
require_once './vendor/autoload.php';

use FormGuide\Handlx\FormHandler;


$pp = new FormHandler(); 

$validator = $pp---><html><head></head><body>getValidator();
$validator-&gt;fields(['name', 'email','phone'])-&gt;areRequired()-&gt;maxLength(50);
$validator-&gt;field('email')-&gt;isEmail();
$validator-&gt;field('message')-&gt;maxLength(6000);




$pp-&gt;sendEmailTo('info@vlhsglove.com'); // ← Your email here

echo $pp-&gt;process($_POST);</body></html>