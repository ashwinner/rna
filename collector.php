<!DOCTYPE html>

<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<html>
<body>
<h2>Confirm to Vote</h2>

<?php 
    require('collectordb.php');
    $userId=$_POST['email'];
    $pin=$_POST['pin'];
    $query= "select * from validate where userId= '{$userId}' ;";
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

	    $blindedEncryptedVote = new Math_BigInteger($_POST['blindedEncryptedVote'], 10);
	    $blindedPVID = new Math_BigInteger($_POST['blindedPVID'], 10);
	    //echo "blindedPVID : "+ $blindedPVID->toString();
	    
	    $signedBlindedEncryptedVote = $blindedEncryptedVote->powMod($d_c, $n_c);
	    $signedBlindedPVID = $blindedPVID->powMod($d_c, $n_c);  
	   // echo "signedBlindedPVID : "+ $signedBlindedPVID->toString();
	    
	    //echo $signedBlindedEncryptedVote->toString();
	    //echo $signedBlindedPVID->toString();

	    echo "<input type='hidden' name='signedBlindedEncryptedVote' value='$signedBlindedEncryptedVote'>
		  <br/>
		  <input type='hidden' name='signedBlindedPVID' value='$signedBlindedPVID'>
		  <br/>
		  <input type='hidden' name='n' value='$n_c'>
		  <br/>
		  
		  ";
	    echo "<form name='authenticate' action='collectorOfVote.php' method='post' onsubmit ='return unblind()'  >
		  <input type='hidden' name='collectorAuthenticatedVote' value=''>
		  <input type='hidden' name='collectorAuthenticatedPVID' value=''>
		  <input type='hidden' name='PVID' value=''>
		  <input type='hidden' name='encryptedVote' value=''>
		  <input type= 'submit' value= 'Proceed to Vote' >
		  </form>
		  ";
  	}
   //echo "blindedEncryptedVote "+ $d.tostring();
   
?>

        
</body>
</html>

<script>
   function unblind() {
        
     n = new BigInteger(document.getElementsByName('n')[0].value);
     
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
     
     document.getElementsByName('collectorAuthenticatedVote')[0].value=collectorAuthenticatedVote;
     document.getElementsByName('collectorAuthenticatedPVID')[0].value=collectorAuthenticatedPVID;
     document.getElementsByName('PVID')[0].value=PVID;
     document.getElementsByName('encryptedVote')[0].value=encryptedVote;
     //alert(signedPVID);
     return true;
    }

</script>
