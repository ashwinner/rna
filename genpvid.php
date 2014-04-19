<!DOCTYPE html>

<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<script src='js/jsbn/rng.js'></script>
<script src='js/jsbn/prng4.js'></script>
<script src='js/bin/jsencrypt.min.js'></script>
<html>

<body>

<h2>Generate your PVID</h2>

<?php 
    
    include_once('Math/BigInteger.php');
    $key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('key.pem')));
	$publicKey = file_get_contents('authpub.pem');    
    $n = new Math_BigInteger($key['rsa']['n'], 256);
    $e = new Math_BigInteger($key['rsa']['e'], 256);

	echo "<form name='authenticate' action='authorizer.php' method='post' onsubmit='return blind()'>
	Email : <input type='text' name='email'>
	<br/>
	PIN : &nbsp;&nbsp;&nbsp;<input type='password' name='pin'>
    <br/>
    
    <input type='hidden' name='pseudoID' value=''>
    <input type='hidden' name='n' value='$n'>
    <input type='hidden' name='e' value='$e'>
	<input type='hidden' name='publicKey' value='$publicKey'>
	<input type='submit' value='submit'>
	</form>
	";

?>

</body>
</html>

<script>
    function blind() {
        
        var n = new BigInteger(document.getElementsByName('n')[0].value);
        var e = new BigInteger(document.getElementsByName('e')[0].value);
	var publicKey = document.getElementsByName('publicKey')[0].value;
	var email = document.getElementsByName('email')[0].value;
	var pin= document.getElementsByName('pin')[0].value;
        
	var crypt = new JSEncrypt();
	crypt.setKey(publicKey);

	var rng = new SecureRandom();

        var blindingFactor;

        do {
            blindingFactor = new BigInteger(1024, rng);
        } while(blindingFactor.compareTo(n)>=0 || blindingFactor.compareTo(BigInteger.ONE)<=0 || !blindingFactor.gcd(n).equals(BigInteger.ONE));
        
        localStorage.setItem('blindingFactor', blindingFactor.toString());
        
        var numberToBlind = new BigInteger("1000" + new BigInteger(32, rng).toString());
        console.log("Number to Blind : " + numberToBlind);

        var pseudoID = blindingFactor.modPow(e, n).multiply(numberToBlind).mod(n);
        console.log("pseudoID : " + pseudoID);
        
	
	var encryptedEmail = crypt.encrypt(email);
	var encryptedPin=crypt.encrypt(pin);
	var encryptedPseudoID=pseudoID.modPow(e,n);
	document.getElementsByName('email')[0].value=encryptedEmail;
	document.getElementsByName('pin')[0].value=encryptedPin;
	document.getElementsByName('pseudoID')[0].value=encryptedPseudoID;
        alert(enc);

return true;
    }

</script>

