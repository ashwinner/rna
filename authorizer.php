<!DOCTYPE html>

<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<html>
<body>
<h2>Result</h2>

<?php 

    if(!isset($_POST['email']) || !isset($_POST['pin']) || !isset($_POST['pseudoID'])) {
        header('location:index.html');
    }

    require('db.php');

    $encryptedUserId=$_POST['email'];
	openssl_private_decrypt(base64_decode($encryptedUserId), &$userId, 'file://key.pem');
	var_dump($userId);

    $encryptedPin = $_POST['pin'];
	openssl_private_decrypt(base64_decode($encryptedPin), &$pin, 'file://key.pem');

	var_dump($pin);
    $query = "select * from validate where userId = '{$userId}' ;";
    
    $result = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_array($result);

    if($row==NULL)
        echo "<h3>User Doesnt Exist!</h3>";

    else if(strcmp($row['pin'], $pin)!=0) 
        echo "<h3>Invalid PIN</h3>";
    
    else if(strcmp($row['generatedPVID'], "0")!=0)
        echo "<h3>You already have a PVID...</h3>";
    
    else {
    require('Math/BigInteger.php');
    $key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('key.pem')));
    
    $d = new Math_BigInteger($key['rsa']['d'], 256);
    $n = new Math_BigInteger($key['rsa']['n'], 256);
    $e = new Math_BigInteger($key['rsa']['e'], 256);
    $encryptedPseudoID=new Math_BigInteger($_POST['pseudoID']);
//    openssl_private_decrypt(base64_decode($encryptedPseudoID), &$pseudoIDstr, 'file://key.pem');
    $pseudoID = $encryptedPseudoID->powMod($d,$n);

    $signedPseudoID = $pseudoID->powMod($d, $n);
    
    $query = "update validate set generatedPVID='1' where userId='$userId';";
    mysql_query($query) or die (mysql_error());

    echo "<input type='hidden' name='signedPseudoID' value='$signedPseudoID'>
          <br/>
          <input type='hidden' name='n' value='$n'>
          <br/>
          ";
    }

?>

          <h4 id='PVID'></h4>
</body>
</html>

    <script>
    window.onload = function unblind() {
        
        n = new BigInteger(document.getElementsByName('n')[0].value);
        signedPseudoID = new BigInteger(document.getElementsByName('signedPseudoID')[0].value);

        blindingFactor = new BigInteger(localStorage.getItem('blindingFactor'));
        localStorage.setItem('blindingFactor', '');
        
        PVID = blindingFactor.modInverse(n).multiply(signedPseudoID).mod(n);
        document.getElementById('PVID').innerHTML=PVID;
        return true;
    }

    </script>
