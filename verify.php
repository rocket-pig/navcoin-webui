<?php


function verifyPassword($password)
{
    global $pass;
    include ("libs/config.php");
    if ($password === $pass){
	return ('right') ;
    }
}

function getpass()
{

	include ("libs/config.php");

	return($pass);
}
?>
