<!DOCTYPE html>
<html>
<body>
<?php 


    if(!isset($_POST['pvid'])) { //redirecct browser
        header('location:vote.html');
    }

    require 'keyGeneratordb.php';
    require 'Math/BigInteger.php';
    $PVID=new Math_BigInteger($_POST['pvid'],10);

    //verify if the PVID has got the authorizer's sign
	$key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('key.pem')));    
    	$n_a = new Math_BigInteger($key['rsa']['n'], 256);
    	$e_a = new Math_BigInteger($key['rsa']['e'], 256);
	$decryptedPVID= $PVID->powMod($e_a,$n_a);
	$checkPVID= $decryptedPVID->toString();
	if(substr($checkPVID,0,4)==='1000')
		echo "Valid PVID\n";	
	else 
	{
		//echo '<script type="text/javascript">', 'alertBox();', '</script>';
		header('location:vote.html');
	}
	
	//if valid PVID
    $query = "select * from table_of_keys where pvid = '{$PVID}' ;";
    $result = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_array($result);

    if($row==NULL)	//the valid PVID has to be added to the keyGenerator's database and a symmetric key pair has to be generated
	{
		echo "<h3> generating key pair for voter!</h3>";
		$key=openssl_random_pseudo_bytes(16);//returns a random string of 16 bytes
		echo $key;
		$key1 = new Math_BigInteger($key, 256); 
		echo $key1;		
		//insert into table_of_keys
		$query = "insert into table_of_keys values ('{$PVID}','{$key1->toString()}');";
		mysql_query($query) or die("Error here".mysql_error());
		
	}
    else			//pvid and key already present in table, so just return the key
	{
		$key=$row['key'];
	}
	
	 
	 echo "<input type='hidden' name='key' value='$key'>
          <br/>";

?>
<button type="button"  onclick="document.location.href='ballot.php';">Display Ballot</button> 
<script>
	window.onload = function storeKey() {
	{
		var key= new BigInteger(document.getElementsByName('key')[0].value);
		localStorage.setItem('key', key.toString());	
	}

	function alertBox()
	{
		alert("Invalid PVID\n");
	}
</script>
</body>
</html>