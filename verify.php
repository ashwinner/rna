<!DOCTYPE html>
<script src='aes.js'></script>
<html>
<body>

<h1> Vote Verification</h1>
<?php
    
require('db.php');
require('Math/BigInteger.php');

if(!isset($_POST['pvid']) && !isset($_POST['key'])) {
    echo "<form name='verify' action='verify.php' method='post'>
    PVID : <input type='text' name='pvid'>
    <input type='submit' value='Fetch Key'>
    </form>";
}
else {
	$pvid = $_POST['pvid'];
	$PVID = new Math_BigInteger($pvid);
	//verify if the PVID has got the authorizer's sign
        $key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('key.pem')));
        $n_a = new Math_BigInteger($key['rsa']['n'], 256);
        $e_a = new Math_BigInteger($key['rsa']['e'], 256);
        $decryptedPVID= $PVID->powMod($e_a,$n_a);
        $checkPVID= $decryptedPVID->toString();
        if(!(substr($checkPVID,0,4)=='1000'))
        {
		echo "<form name='verify' action='verify.php' method='post'>
        	PVID : <input type='text' name='pvid'>
	        <input type='submit' value='Fetch Key'>
	        </form>";
                echo "<h3>Please enter a valid pvid </h3>";
        }

        else
        {

		if(!isset($_POST['key'])) {

		    $query = "select * from symmetricKeys where pvid = '$pvid';";
		    $results = mysql_query($query) or die("mysql error " . mysql_error());
		    $row = mysql_fetch_array($results);
	
		    if($row==NULL) 
			echo "<h3>You have not yet voted</h3>";
		    
		    else { 

		 $key_base64 = $row['key_base64'];
		 $iv_base64 = base64_encode(pack('H*', '48656c6c6f2c20576f726c6421abcdef'));
    		    echo "<form name='verify' action='verifyResult.php' method='post' onsubmit=' return encrypt()'>
    		    PVID : <input type='text' name='pvid' value='$pvid'>
    		    <br/>
      		    Key &nbsp;&nbsp; : <input type='text' name='key' value='$key_base64'>
		    <br/>
		    Vote &nbsp;&nbsp;: <input type='text' name='vote'>
		    <br/>
		    <input type='hidden' name='iv' value='$iv_base64'>
		    <input type='submit' value='submit'>
		    <br/>
		    </form>";
		    }
		}
	}

}
?>

</body>
</html>

<script>
function encrypt() {
	
	var key = CryptoJS.enc.Base64.parse(document.getElementsByName('key')[0].value);
	var vote = document.getElementsByName('vote')[0].value;
	var iv = CryptoJS.enc.Base64.parse(document.getElementsByName('iv')[0].value);

	var encryptedVote = CryptoJS.AES.encrypt(vote, key, { iv: iv });
	document.getElementsByName('vote')[0].value=encryptedVote.toString();

	return true;
}
</script>
