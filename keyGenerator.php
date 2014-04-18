<!DOCTYPE html>
<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<html>
<body>
<?php 


    if(!isset($_POST['pvid'])) { //redirecct browser
        header('location:vote.html');
    }

    require 'db.php';
    require 'Math/BigInteger.php';
    $PVID=new Math_BigInteger($_POST['pvid'],10);

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
		echo $key;
		$key1 = new Math_BigInteger($key, 256); 
		echo $key1;		
		//insert into keys
		$query = "insert into symmetricKeys values ('{$PVID}','{$key1->toString()}');";
		mysql_query($query) or die("Error here".mysql_error());
		
	}
    	else			//pvid and key already present in table, so just return the key
	{
		$key=$row['key'];
	}
	
	 
	 echo "<input type='hidden' name='key' value='$key'>
          <br/>";
	}
?>
<script>
window.onload = function storeKey() {
	
		var key= new BigInteger(document.getElementsByName('key')[0].value);
		localStorage.setItem('key', key.toString());
		document.location.href='ballot.php';	
	}

</script>
</body>
</html>
