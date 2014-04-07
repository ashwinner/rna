<!DOCTYPE html>

<html>

<body>
<script src='js/bin/jsencrypt.min.js'></script>
<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<script src='js/jsbn/rng.js'></script>
<script src='js/jsbn/prng4.js'></script>

<h2>Generate your PVID</h2>

<?php 
    include('Crypt/RSA.php');

    $rsa = new Crypt_RSA();

    $rsa->loadKey(file_get_contents('key.pem'));
    $rsa->setPublicKey();

    $publicKey = $rsa->getPublicKey();
	echo "<form name='authenticate' action='genpvid.php' method='post' onsubmit='return blind()'>
	Email : <input type='text' name='email'>
	<br/>
	PIN : &nbsp;&nbsp;&nbsp;<input type='password' name='pass'>
	<br/>
    <input type='hidden' name='pseudoID' value=''>
    <input type='hidden' name='publicKey' value='$publicKey'>
	<input type='submit' value='submit'>
	</form>
	";

?>

</body>
</html>

<script>
    function blind() {
        var crypt = new JSEncrypt();
        crypt.setKey(document.getElementsByName('publicKey')[0].value);
        var key = crypt.getKey();
        var n = new BigInteger(key.n.toString());
        var e = new BigInteger(key.e.toString());

        var rng = new SecureRandom();

        var blindingFactor;

        do {
            blindingFactor = new BigInteger(1024, rng);
        } while(blindingFactor.compareTo(n)>=0 || blindingFactor.compareTo(BigInteger.ONE)<=0 || !blindingFactor.gcd(n).equals(BigInteger.ONE));

        var numberToBlind = new BigInteger("1000" + new BigInteger(32, rng).toString());
        var pseudoID = blindingFactor.modPow(e, n).multiply(numberToBlind).mod(n);
        console.log(numberToBlind.toString());


return true;
    }
</script>

