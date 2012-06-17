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


	include 'tableExtractor.class.php';
	include 'auth.php';
	
	function table2array($input,$anchor)
	{	 
		$tx = new tableExtractor;
		$tx->source=$input;
		$tx->anchor = $anchor;
		$tx->anchorWithin = true;
		$tx->headerRow = true;
		$tx->stripTags = true;	
		$registrati = $tx->extractTable();		
		return $registrati;
	}
	
	function getUserDetails($username,$S,$T)
	{
		$detail="https://$S/cgi-bin/kerbynet?Section=User&Action=Edit&STk=$T&type=user&user_sel=$username";
		$ch = curl_init($detail);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$detail = curl_exec($ch);       
		curl_close($ch);
		
		//name="givenName" value="
		$x0=strpos($detail,"name=\"givenName\" value=\"")+24;
		$x1=strpos($detail,"\" size=\"19\"",$x0);
		$nome=trim(substr($detail,$x0,$x1-$x0));
		
		$x0=strpos($detail,"name=\"sn\" value=\"")+17;
		$x1=strpos($detail,"\" size=\"19\"",$x0);
		$cognome=trim(substr($detail,$x0,$x1-$x0));
		
		return $nome." ".$cognome;
	}
	
	function getConnectedUsers($S,$T)
	{
		$url="https://$S/cgi-bin/kerbynet?STk=$T&Action=Render&Object=cpConnectedClients";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$url = curl_exec($ch);       
		curl_close($ch);
		
		return table2array($url,"<table width=100% cellpadding=0>");				
	}
	
	function getRegisteredUsers($S,$T)
	{
		$url="https://$S/cgi-bin/kerbynet?Section=User&STk=$T&Action=List";
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$pagina = curl_exec($ch);       
		curl_close($ch);
		
		return table2array($pagina,"<table border=1 bordercolor=#c0c0c0 cellspacing=0 width=100% style='color: #606060'>");	
	}
	
	function isUserConnected($user,$connessi)
	{
		foreach($connessi as $connesso)
		{
			if(strstr($connesso['Username'],$user)) 
			{
				return true;
			}
		}
		return false;
	}
	
	function getIPMAC($user,$connessi)
	{
		foreach($connessi as $connesso)
		{
			if(strstr($connesso['Username'],$user)) 
			{
				return $IPMAC=array($connesso["IP Address"],$connesso["MAC Address"]);
			}
		}
		return false;	
	}
	
	function disconnectUser($userIP,$S,$T)
	{
		$url="https://$S/cgi-bin/kerbynet";
		$ch = curl_init($url);
		$postdata = array (
			"Section" => "CP",
			"STk" => $T,
			"Action" => "DisconnectClient",
			"CLT" => $userIP
			);
					
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);       
		curl_close($ch);		
	}
	
	function deleteUser($user,$S,$T)
	{
		$url="https://$S/cgi-bin/kerbynet?Section=User&STk=$T&Action=SaveDelete&uid=$user";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);       
		curl_close($ch);		
	}
	
	if(isset($_GET['delete']))
	{
		echo "<b>User ".$_GET['delete']." DELETED!</b><br>";
		deleteUser($_GET['delete'],$ZS_SERVER,$TOKEN);
	}
	
	if(isset($_GET['disconnect']))
	{
		echo "<b>User ".$_GET['disconnect']." DISCONNECTED!</b><br>";
		disconnectUser($_GET['disconnect'],$ZS_SERVER,$TOKEN);
	}
	
	
	
	$registrati=getRegisteredUsers($ZS_SERVER,$TOKEN);
	$connessi=getConnectedUsers($ZS_SERVER,$TOKEN);
	
	echo "<html><head></head><body align=\"center\">";
	echo "There are ".(count($registrati)-1)." registered users:";
	?>
		<input type=button id="lista" name="lista" value="Refresh" onClick="javascript:document.getElementById('lista').value='Please wait...'; window.location.href='lista.php'"> 
	<?php
	
	echo "<table border=\"1\" align=\"center\">";
	
	echo "<form name=aggiungi action=lista.php method=get>";
	echo "<tr><td>Name and Surname</td><td>Username</td><td>Description</td><td>Conn. Status</td><td>Action</td></tr>";
	
	for($i=1;$i<=count($registrati);$i++)
	{
		if($registrati[$i]['Username']!="admin")
		{
			echo "<tr><td>";
			echo getUserDetails($registrati[$i]['Username'],$ZS_SERVER,$TOKEN); //nome e cognome
			echo "</td><td>";
			print_r($registrati[$i]['Username']); //username
			echo "</td><td>";
			print_r($registrati[$i]['Description']); //descrizione
			echo "</td>";
			$ipmac=0;
			if(isUserConnected($registrati[$i]['Username'],$connessi)) 
			{
				echo "<td bgcolor=#ccffcc>";
				$ipmac=getIPMAC($registrati[$i]['Username'],$connessi);
				echo "IP: ".$ipmac[0]."<br>MAC: ".$ipmac[1]."</td>";
			}
			else 
				echo "<td bgcolor=#ffcccc>Disconnected</td>";
			
			echo "<td><input type=\"button\" onclick=\"javascript: if(confirm('Are you sure you want to delete and disconnect the user ".$registrati[$i]['Username']."? You can\'t undo this action.')) window.location.href='lista.php?disconnect=".$ipmac[0]."&delete=".$registrati[$i]['Username']."';\" value=\"Delete and disconnect\">";
			
			echo "<input type=\"button\" name=\"disconnect\" value=\"Disconnect\"";
			if(!isUserConnected($registrati[$i]['Username'],$connessi)) 
				echo " disabled=\"disabled\">";
			else
				echo " onclick=\"javascript: if(confirm('Are you sure you want to disconnect the user ".$registrati[$i]['Username']."? You can\'t undo this action.')) window.location.href='lista.php?disconnect=".$ipmac[0]."';\" >";
			
				
			echo "</td></tr>";
		}
	}
	

	?>
	</form>
	</table>
	<input type=button name=print value="Back" onClick="window.location.href='index.php'"> 
	</body>
	</html>