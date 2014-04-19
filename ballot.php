<!DOCTYPE html>

<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<script src='js/jsbn/rng.js'></script>
<script src='js/jsbn/prng4.js'></script>
<script src='aes.js'></script>
<script src='js/bin/jsencrypt.min.js'></script>
<html>

<body>

<h2>Ballot</h2>

<?php 
    
    include('Math/BigInteger.php');
    $collectorKey=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('collectorKey.pem')));
     $publicKey = file_get_contents('collectorpub.pem');
    $n = new Math_BigInteger($collectorKey['rsa']['n'], 256);
    $e = new Math_BigInteger($collectorKey['rsa']['e'], 256);

    $iv_base64 = base64_encode(pack('H*', '48656c6c6f2c20576f726c6421abcdef'));

	echo "<form name='authenticate' action='collector.php' method='post' onsubmit='return EncryptAndBlind()'>
	Email : <input type='text' name='email'>
	<br/>
	PIN : &nbsp;&nbsp;&nbsp;<input type='password' name='pin'>
    	<br/>
	<input type= 'hidden' name= 'blindedPVID'>
    	<br/>
    	<br/>
    	Candidate: <input type='text' name='vote'>
    	<br/>
	<input type='hidden' name='blindedEncryptedVote' value=''>
       	<input type='hidden' name='n' value='$n'>
    	<input type='hidden' name='e' value='$e'>
	<input type='hidden' name='iv_base64' value='$iv_base64'>
	<input type='hidden' name='publicKey' value='$publicKey'>
	<input type='submit' value='submit'>
	</form>
	";

?>

</body>
</html>

<script>
    function EncryptAndBlind() {
 	var publicKey = document.getElementsByName('publicKey')[0].value;
        var email = document.getElementsByName('email')[0].value;
        var pin= document.getElementsByName('pin')[0].value;
        
        var crypt = new JSEncrypt();
        crypt.setKey(publicKey);
	var encryptedEmail = crypt.encrypt(email);
        var encryptedPin=crypt.encrypt(pin);
	document.getElementsByName('email')[0].value=encryptedEmail;
        document.getElementsByName('pin')[0].value=encryptedPin;
    
        var n = new BigInteger(document.getElementsByName('n')[0].value);
        var e = new BigInteger(document.getElementsByName('e')[0].value);
	var PVID=new BigInteger(localStorage.getItem('PVID'));
	
	var key = CryptoJS.enc.Base64.parse(localStorage.getItem('key_base64'));
	var iv = CryptoJS.enc.Base64.parse(document.getElementsByName('iv_base64')[0].value);
	
	var vote = document.getElementsByName('vote')[0].value;

	var encrypted = CryptoJS.AES.encrypt(vote, key, { iv: iv });

	var encryptedVote = new BigInteger(encrypted.ciphertext.toString(), 16);
	var encryptedVotePrefixed = new BigInteger("1100" + encryptedVote.toString());
		
        var rng = new SecureRandom();

        var blindingFactor;

        do {
           blindingFactor = new BigInteger(1024, rng);
        } while(blindingFactor.compareTo(PVID)<=0 || blindingFactor.compareTo(n)>=0 || blindingFactor.compareTo(BigInteger.ONE)<=0 || !blindingFactor.gcd(n).equals(BigInteger.ONE));
        
        localStorage.setItem('blindingFactorCollector', blindingFactor.toString());
        
        var blindedEncryptedVote = blindingFactor.modPow(e, n).multiply(encryptedVotePrefixed).mod(n);
	var blindedPVID= blindingFactor.modPow(e, n).multiply(PVID).mod(n);
        document.getElementsByName('blindedEncryptedVote')[0].value=blindedEncryptedVote.modPow(e,n);
	document.getElementsByName('blindedPVID')[0].value=blindedPVID.modPow(e,n);
	

return true;
    }

</script>
	
