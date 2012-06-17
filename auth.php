<?php
/*************************************************************************
 Title: 	Simple interface for Zeroshell v0.2
 Author: 	Davide Di Gloria, diglo@email.it
			http://diglo.altervista.org
 Date:		15/05/12
 Notes:		Tested to work with Zeroshell 1.0.beta16
			This software is provided AS-IS and is currently under
			deep beta stage. I don't give any warranties of any kind.
			You are free to modify and enhance these pieces of code, if you want
			to use them, please give credit and don't remove this header
***************************************************************************/

include_once 'configuration.php';

$TOKEN="";

if($ZS_SERVER=="" || $rootuser=="" || $rootpass=="") die("Before starting, have a look into auth.php file, you have to set up your enviroment first! Make sure the openssl module is enabled in your PHP installation or these scripts may not work.");

function checkZS($ip)
{
	$url="https://$ip";
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$pagina = curl_exec($ch);       
	curl_close($ch);
	
	if(strstr($pagina,"<title>ZeroShell</title>"))
	return true;
	else return false;
}

function loginZS($ip,$admin,$pass)
{
	//https token parsing
	$url="https://$ip/cgi-bin/kerbynet?Action=StartSessionSubmit&User=".$admin."&PW=".$pass;
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$pagina = curl_exec($ch);       
	curl_close($ch);
	
	$pos=strrpos($pagina,"STk=",0);
	if($pos)
	{
		$token=substr($pagina,$pos+4,40);
		return $token;
	}		
	else return false;
}

function logoutZS($ip,$token)
{
	$url="https://$ip/cgi-bin/kerbynet?STk=$token&Action=Logout";
	$fp = fopen($url, 'r');
	fclose($fp);
}
	ini_set('user_agent', "PHP\r\nX-HeaderACaso: Sticaz");
	if(checkZS($ZS_SERVER))
	{
		$TOKEN=loginZS($ZS_SERVER,$rootuser,$rootpass);
		if(!$TOKEN) die("Log on ZeroShell server FAILED! Check user and password.");
	}
	else
	die("ZeroShell server not found at $ZS_SERVER:443, check connectivity!");

?>