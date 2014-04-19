<!DOCTYPE html>
<html>
<body>

<?php
	require('Crypt/Hash.php');
	require('db.php');
	require ('Math/BigInteger.php');

	if(!isset($_POST['pvid']) || !isset($_POST['vote']))
		header('location:index.html');

	$encryptedVoteBase64 = $_POST['vote'];
	$encryptedVoteBinary = base64_decode($encryptedVoteBase64); 
	
        $PVID=new Math_BigInteger($_POST['pvid'],10);
        //verify if the PVID has got the authorizer's sign
        $key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('key.pem')));
        $n_a = new Math_BigInteger($key['rsa']['n'], 256);
        $e_a = new Math_BigInteger($key['rsa']['e'], 256);
        $decryptedPVID= $PVID->powMod($e_a,$n_a);
        $checkPVID= $decryptedPVID->toString();
	if(strcmp(substr($checkPVID,0,4), '1000')!=0)
		echo"<h3>Your PVID is INVALID!!</h3>";

	else {

		$query = "select * from cbb where PVID = '$PVID'";
	
		$result = mysql_query($query) or die("Error fetching encrypted vote :  " . mysql_error());
		$row = mysql_fetch_array($result);

		if($row==NULL || $row=="")
			echo "<h3>You havent voted yet</h3>";

		else {
			$hash= new Crypt_Hash('sha512');
	                $hashOfEncryptedVote= bin2hex($hash->hash($encryptedVoteBinary));
			
			if(strcmp($row['HashOfEncryptedVote'], $hashOfEncryptedVote)!=0) 
				echo "<h3>Vote Mismatch!!</h3>";
			else
				echo "<h3>Your vote has been recorded correctly</h3>";
		}
	}
?>

</body>
</html>

