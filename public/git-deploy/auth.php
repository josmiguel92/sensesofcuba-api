<?php
//AUTHENTICATION
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Next time!';
    exit;
} 

if($_SERVER['PHP_AUTH_USER']!='habanatech' or $_SERVER['PHP_AUTH_PW'] !='habanatechpass')
{
	echo 'Next time!';
    exit;
}
 
