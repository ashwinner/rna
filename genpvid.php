<!DOCTYPE html>

<html>

<body>
<script src='js/bin/jsencrypt.min.js'></script>
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
        var modulus = key.n;
        alert(modulus);
return true;
}
</script>

