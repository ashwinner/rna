<!DOCTYPE html>
<html>
<body>
<?php 


    if(!isset($_POST['pvid'])) { //redirecct browser
        header('location:vote.html');
    }

    require 'db.php';
    require 'Math/BigInteger.php';
   	$key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('keyGenerator.pem')));
        $n_k = new Math_BigInteger($key['rsa']['n'], 256);
        $d_k = new Math_BigInteger($key['rsa']['d'], 256);

	$encryptedPVID=new Math_BigInteger($_POST['pvid'],10);
	$PVID=$encryptedPVID->powMod($d_k,$n_k);
    //verify if the PVID has got the authorizer's sign
	$key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('key.pem')));    
    	$n_a = new Math_BigInteger($key['rsa']['n'], 256);
    	$e_a = new Math_BigInteger($key['rsa']['e'], 256);
	$decryptedPVID= $PVID->powMod($e_a,$n_a);
	$checkPVID= $decryptedPVID->toString();
	//var_dump($checkPVID);
	if(!(substr($checkPVID,0,4)=='1000')) 
	{
		//echo '<script type="text/javascript">', 'alertBox();', '</script>';
		echo "<h3> The PVID you entered is invalid!</h3>";
	}
	
	else
	{
	
	//if valid PVID
    	$query = "select * from symmetricKeys where pvid = '{$PVID}' ;";
    	$result = mysql_query($query) or die(mysql_error());
    	$row = mysql_fetch_array($result);

    	if($row==NULL)	//the valid PVID has to be added to the keyGenerator's database and a symmetric key pair has to be generated
	{
		echo "<h3> generating key pair for voter!</h3>";
		$key=openssl_random_pseudo_bytes(16);//returns a random string of 16 bytes
		
		$key_base64 = base64_encode($key);
		//insert into keys
		$query = "insert into symmetricKeys values ('{$PVID}','$key_base64');";
		mysql_query($query) or die("Error here".mysql_error());
		
	}
    	else			//pvid and key already present in table, so just return the key
	{
		$key_base64=$row['key_base64'];
	}
	
	 
	 echo "<input type='hidden' name='key_base64' value='$key_base64'>
          <br/>";
	}
?>
<script>
window.onload = function storeKey() {
	
		var key= document.getElementsByName('key_base64')[0].value;
		localStorage.setItem('key_base64', key);
		document.location.href='ballot.php';	
	}

</script>
</body>
</html>
