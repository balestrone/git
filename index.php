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

function genpw($length=8)
{
	$consonanti = 'bcdfgmnprstvz';
	$vocali = "aeiou";
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonanti[(rand() % strlen($consonanti))];
			$alt = 0;
		} else {
			$password .= $vocali[(rand() % strlen($vocali))];
			$alt = 1;
		}
	}
	return $password;
}

?>

<html>
<head>
</head>
<body>
	
	<center>
	<?php echo date("d/m/Y - H:i"); ?> - 
	<?php 
	$online=checkZS($ZS_SERVER);
	if($online)
		echo "ZeroShell server found.";
	else
		echo "ZeroShell server not found on $ZS_SERVER";
		?><br>
	<font size="6pt">User management control panel</font><br>
	<font size="4pt">Albergo Bruttavista</font>
	<hr width="50%">
	
	<table>
	<tr>
	<td><img src="1.png" width="300px"></td>
	
	<td>
	
		<form name=aggiungi action=aggiungi.php method=get>
		<table>
		
		<tr><td>Username</td>
		<td><input type=text name="user" value="<?php echo genpw(6) ?>"/></td></tr>
		
		<tr><td>Name</td>
		<td><input type=text name="nome" value="" /></td></tr>
		
		<tr><td>Surname</td>
		<td><input type=text name="cognome" value="" /></td></tr>
		
		<tr><td>Details</td>
		<td><input type=text name="appartamento" value="" /></td></tr>
		
		<tr><td>Expires in</td>
		<td><input type=text name="giorni" value="1" /> days</td></tr>
		
		<tr><td>&nbsp;</td>	
		<td>&nbsp;</td></tr>
		
		<tr><td>&nbsp;</td>	
		<td><input type=submit id="crea" name="submit" value="Create this user" onClick="javascript:document.getElementById('crea').value='Please wait...'" <?php if(!$online) echo " disabled=\"disabled\" "; ?> > </td></tr>
		
		<tr><td>&nbsp;</td>	
		<td>
		
		<input type=button id="lista" name="lista" value="View user list" onClick="javascript:document.getElementById('lista').value='Please wait...'; window.location.href='lista.php' <?php if(!$online) echo " disabled=\"disabled\" "; ?> "> 
		<td></tr>
			
		</td></tr>
		</table>
		</form>	
	
	</td>
	</tr>
	</table>
	</center>
</body>
</html>
	
