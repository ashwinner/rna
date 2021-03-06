<!DOCTYPE html>

<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<html>
<body>
<h2>Confirm to Vote</h2>

<?php 
    require('db.php');
    $encryptedUserId=$_POST['email'];
        openssl_private_decrypt(base64_decode($encryptedUserId), &$userId, 'file://collectorKey.pem');

    $encryptedPin = $_POST['pin'];
        openssl_private_decrypt(base64_decode($encryptedPin), &$pin, 'file://collectorKey.pem');
    
	var_dump($userId);
	var_dump($pin);    
    $query= "select * from collectorValidate where userId= '{$userId}' ;";
    $result = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_array($result);

    if($row==NULL)
        echo "<h3>User Doesnt Exist!</h3>";

    else if(strcmp($row['pin'], $pin)!=0) 
        echo "<h3>Invalid PIN</h3>";
    else{
	    //echo "Valid emailId and pin";
	    require('Math/BigInteger.php');
	    $collectorKey=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('collectorKey.pem')));
	    
	    $d_c = new Math_BigInteger($collectorKey['rsa']['d'], 256);
	    $n_c = new Math_BigInteger($collectorKey['rsa']['n'], 256);
	    $e_c = new Math_BigInteger($collectorKey['rsa']['e'], 256);

	    $encryptedBlindedEncryptedVote = new Math_BigInteger($_POST['blindedEncryptedVote'], 10);
	    $encryptedBlindedPVID = new Math_BigInteger($_POST['blindedPVID'], 10);
	    //echo "blindedPVID : "+ $blindedPVID->toString();
	    $blindedEncryptedVote=$encryptedBlindedEncryptedVote->powMod($d_c,$n_c);
	    $signedBlindedEncryptedVote = $blindedEncryptedVote->powMod($d_c, $n_c);
	    $blindedPVID=$encryptedBlindedPVID->powMod($d_c,$n_c);
	    $signedBlindedPVID = $blindedPVID->powMod($d_c, $n_c);  
	   // echo "signedBlindedPVID : "+ $signedBlindedPVID->toString();
	    
	    //echo $signedBlindedEncryptedVote->toString();
	    //echo $signedBlindedPVID->toString();

	    echo "<input type='hidden' name='signedBlindedEncryptedVote' value='$signedBlindedEncryptedVote'>
		  <br/>
		  <input type='hidden' name='signedBlindedPVID' value='$signedBlindedPVID'>
		  <br/>
		  <input type='hidden' name='n' value='$n_c'>
		  <input type='hidden' name='e' value='$e_c'>
		  <br/>
		  
		  ";
	    echo "<form id='collectionOfVote' action='collectorOfVote.php' method='post'   >
		  <input type='hidden' name='collectorAuthenticatedVote' value=''>
		  <input type='hidden' name='collectorAuthenticatedPVID' value=''>
		  </form>
		  ";
  	}
   //echo "blindedEncryptedVote "+ $d.tostring();
   
?>

        
</body>
</html>

<script>
window.onload = function unblind() {
        
     n = new BigInteger(document.getElementsByName('n')[0].value);
     e = new BigInteger(document.getElementsByName('e')[0].value);
     
     signedBlindedEncryptedVote = new BigInteger(document.getElementsByName('signedBlindedEncryptedVote')[0].value);
     signedBlindedPVID= new BigInteger(document.getElementsByName('signedBlindedPVID')[0].value);
     
    
     //console.log("signedBlindedEncryptedVote " + signedBlindedEncryptedVote);
     //console.log("signedBlindedPVID " + signedBlindedPVID);
     blindingFactor = new BigInteger(localStorage.getItem('blindingFactorCollector'));
     localStorage.setItem('blindingFactorCollector', '');
     PVID = new BigInteger(localStorage.getItem('PVID'));
     localStorage.setItem('PVID', '');
     encryptedVote = new BigInteger(localStorage.getItem('encryptedVote'));
     localStorage.setItem('encryptedVote', '');
     //console.log("blindingFactor " + blindingFactor);  
     collectorAuthenticatedVote = blindingFactor.modInverse(n).multiply(signedBlindedEncryptedVote).mod(n);
     collectorAuthenticatedPVID= blindingFactor.modInverse(n).multiply(signedBlindedPVID).mod(n);
     
     //console.log("collectorAuthenticatedVote " + collectorAuthenticatedVote);
    // console.log("collectorAuthenticatedPVID " + collectorAuthenticatedPVID);
     
     document.getElementsByName('collectorAuthenticatedVote')[0].value=collectorAuthenticatedVote.modPow(e,n);
     document.getElementsByName('collectorAuthenticatedPVID')[0].value=collectorAuthenticatedPVID.modPow(e,n);
    
     document.getElementById("collectionOfVote").submit();
     //alert(signedPVID);
     return true;
    }

</script>
