<!DOCTYPE html>
<html>
<body>
<h2>Vote Cast Confirmation</h2>
<?php
	require('Math/BigInteger.php');
	require('db.php');
	require('Crypt/Hash.php');
	$collectorKey=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('collectorKey.pem')));
    
    	$d_c = new Math_BigInteger($collectorKey['rsa']['d'], 256);
    	$n_c = new Math_BigInteger($collectorKey['rsa']['n'], 256);
    	$e_c = new Math_BigInteger($collectorKey['rsa']['e'], 256);
	
	$key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('key.pem')));
    
    	
    	$n_a = new Math_BigInteger($key['rsa']['n'], 256);
    	$e_a = new Math_BigInteger($key['rsa']['e'], 256);
	
	$collectorAuthenticatedVoteEnc=new Math_BigInteger($_POST['collectorAuthenticatedVote'],10);
	$collectorAuthenticatedPVIDEnc=new Math_BigInteger($_POST['collectorAuthenticatedPVID'],10);
	$collectorAuthenticatedVote=$collectorAuthenticatedVoteEnc->powMod($d_c,$n_c);
	$collectorAuthenticatedPVID=$collectorAuthenticatedPVIDEnc->powMod($d_c,$n_c);

	$PVID= ($collectorAuthenticatedPVID->powMod($e_c, $n_c));
	$encryptedVotePrefixed=($collectorAuthenticatedVote->powMod($e_c, $n_c));


	
	
/*	if(($PVID->compare($calculatedPVID))==0)
	{
		//echo "Both Pvids equal\n";
	
		$decryptedPVID= $PVID->powMod($e_a,$n_a);
		$checkPVID= $decryptedPVID->toString();
	
*/
	
	$encryptedVotePrefixedString=$encryptedVotePrefixed->toString();
	
	if((substr($encryptedVotePrefixedString,0,4)==='1100'))
	{
		
		$encryptedVoteString=substr($encryptedVotePrefixedString,4);
		$encryptedVote= new Math_BigInteger($encryptedVoteString);
		$encryptedVoteBinary=$encryptedVote->toBytes();
		$encryptedVoteBase64 = base64_encode($encryptedVoteBinary);
		
		$hash= new Crypt_Hash('sha512');
		$hashOfEncryptedVote= bin2hex($hash->hash($encryptedVoteBinary));

		$decryptedPVID= $PVID->powMod($e_a,$n_a);
		$checkPVID= $decryptedPVID->toString();
		$PVIDstring=$PVID->toString();
		if(substr($checkPVID,0,4)==='1000')
		{
			//echo "Valid PVID\n";
	//		if(($encryptedVote->compare($calculatedEncryptedVote))==0)
	//		{
				
				//echo "Both encrypted votes same\n";
				//echo "Now add to database and bullettin board\n";
				$query = "select * from PvidToEncryptedVote where PVID = '{$PVIDstring}' ;";
				$result = mysql_query($query) or die(mysql_error());
    				$row = mysql_fetch_array($result);

    				if($row==NULL)
				{
       					$query1 = "insert into PvidToEncryptedVote values ('{$PVIDstring}','{$encryptedVoteBase64}');";
					mysql_query($query1) or die("Error here".mysql_error());
				}
				else
				{
					$query2 = "update PvidToEncryptedVote set EncryptedVote='{$encryptedVoteBase64}' where PVID='$PVIDstring';";
    					mysql_query($query2) or die (mysql_error());
				}
				$query = "select * from cbb where PVID = '{$PVIDstring}' ;";
				$result = mysql_query($query) or die(mysql_error());
    				$row = mysql_fetch_array($result);

    				if($row==NULL)
				{
       					$query1 = "insert into cbb values ('{$PVIDstring}','{$hashOfEncryptedVote}');";
					mysql_query($query1) or die("Error here".mysql_error());
				}
				else
				{
					$query2 = "update cbb set HashOfEncryptedVote='{$hashOfEncryptedVote}' where PVID='$PVIDstring';";
    					mysql_query($query2) or die (mysql_error());
				}

				
	/*		}
			else
			{
				echo "encrypted Votes not same\n";
			}*/
		}
		else
		{
			echo "Not Valid PVID\n";
		}
	}
	else
	{
		echo "Not Collector Signed\n";
	}
	
?>
	<br/>
	<a href='index.html'>Click here to go back to the home page</a>
	<br/>
</body>
</html>
