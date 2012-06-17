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

	include_once 'auth.php';

	function goodize($str)
	{
		return ucfirst(strtolower(trim($str)));
	}	

	function genpw($length=8) {
		$consonants = 'bcdfgmnpqrstvz';
		$vowels = "aeiou";
	 
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}

	if( empty($_GET['nome']) ||  empty($_GET['cognome']) || empty($_GET['user']) || empty($_GET['giorni']))
	{
				?>
<html>
	<head>
	</head>
		<body>
			<h1>Error, some parameters are missing...</h1>
				Go back and fill in all the fields!
		</body>
</html>
		
		<?php
		die();
	}
	
	$nome=goodize($_GET['nome']);
	$cognome=goodize($_GET['cognome']);
	$user=trim(strtolower(trim($_GET['user'])));
	$giorni=intval(trim($_GET['giorni']));
	$dettagli=(trim($_GET['appartamento']));
	
	$nnome=str_replace(" ","+",$nome);
	$ccognome=str_replace(" ","+",$cognome);
	
	$password=genpw(8,2);
	
	$expdate=date("m/d/Y",time()+$giorni*86400);
	
	$dd=date("d",time()+$giorni*86400);
	$mm=date("m",time()+$giorni*86400);
	$yy=date("Y",time()+$giorni*86400);
		
	$url="https://$ZS_SERVER/cgi-bin/kerbynet?B1=Submit&Section=User&Action=SaveAdd";
	$url.="&STk=".$TOKEN."&user_sel=&type=user&x509cn=";
	$url.="&shadowExpire=".str_replace("/","%2F",$expdate);
	$url.="&UpdateExpire=yes";
	$url.="&uid=".$user;
	$url.="&uidNumber=&primaryGroup=&gidNumber=";
	$url.="&homeDirectory=%2Fhome%2F".$user;
	$url.="&shell=sh&loginShell=%2Fbin%2Fsh";
	$url.="&givenName=".$nnome;
	$url.="&sn=".$ccognome;
	$url.="&o=&gecos=".$dettagli."+Exp.+".str_replace("/","%2F",date("d/m/Y",time()+$giorni*86400));
	$url.="&mail=&telephoneNumber=";
	$url.="&Expiration_date_mm=".$mm;
	$url.="&Expiration_date_dd=".$dd;
	$url.="&Expiration_date_yyyy=".$yy;
	$url.="&ClassName=DEFAULT";
	$url.="&PW=".$password;
	$url.="&CoPW=".$password;
	$url.="&K5=yes&vlan=&WIFI=yes";

	$fp = fopen($url, 'r');
	$line="";
	while (!feof ($fp)) {
	           $line.= fread ($fp, 1028);
	}
	
	$posErr=strrpos($line,"ERROR:",0);
	$Err=substr($line,$posErr,20);

	$posCN=strrpos($line,"CN=",0);
	$CN=substr($line,$posCN+3,20);
	$posCNin=strrpos($CN,"_",0);
	$CN=substr($CN,0,$posCNin+2);
	
	if ($posErr=="")
	{
		?>
<html>
<head>
</head>
<body>

	<center>
	<font size="6pt">Internet access ticket</font><br>
	<font size="4pt">Albergo Bruttavista</font>
	<hr width="50%">
	<table>
		<tr>
		<td><img src="1.png" width="300"></td>
		<td>	
		<table>
		<tr><td><b>Username: </b></td>
		<td><b><?php echo $user ?></b></td></tr>
		
		<tr><td><b>Password: </b></td>
		<td><b><?php echo $password ?></b></td></tr>
		
		<tr><td>Name: </td>
		<td><?php echo $nome ?></td></tr>
		
		<tr><td>Surname: </td>
		<td><?php echo $cognome ?></td></tr>
		
		<tr><td>Details: </td>
		<td><?php echo $dettagli ?></td></tr>
		
		<tr><td>Creation date: </td>
		<td><?php echo date("d/m/Y"); ?></td></tr>
		
		<tr><td>Expiration date: </td>
		<td><?php echo date("d/m/Y",time()+$giorni*86400); ?></td></tr>
		
		</td></tr>
		</table>
		
		</td></tr>
	</table>
	<input type=button name="print" value="Print this ticket" onClick="javascript:window.print()">
	<input type=button id="lista" name="lista" value="Add new user" onClick="javascript:window.location.href='index.php'"> 
	<input type=button id="lista" name="lista" value="View user list" onClick="javascript:document.getElementById('lista').value='Attendi...'; window.location.href='lista.php'"> 
	</center>
</body>
</html>
		
		<?php
	}
	else
	{
		?>
		<html>
<head>
</head>
<body>

	<h1>Error, unable to create the user.</h1>
	Try changing username.
</body>
</html>
		
		<?php
	
	}

	logoutZS($ZS_SERVER,$TOKEN);
?>