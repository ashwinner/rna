<!DOCTYPE html>
<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<html>
<body>

<h1> VOTE </h1>
<?php 
	require('Math/BigInteger.php');
	$key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('keyGenerator.pem')));
    
   
    $n = new Math_BigInteger($key['rsa']['n'], 256);
    $e = new Math_BigInteger($key['rsa']['e'], 256);
echo "<form name='vote' action='keyGenerator.php' method='post' onsubmit='return storePVID();'>
	Enter your PVID : <input type='text' name='pvid'>
	<br/>
	<input type='hidden' name='n' value='$n'>
	<input type='hidden' name='e' value='$e'>
	<input type='submit' value='submit'>
	</form>
	";

?>
<script>
    function storePVID() 
    {
	var PVID= new BigInteger(document.getElementsByName('pvid')[0].value);
	var n= new BigInteger(document.getElementsByName('n')[0].value);
	var e= new BigInteger(document.getElementsByName('e')[0].value);
        document.getElementsByName('pvid')[0].value=PVID.modPow(e,n);
	localStorage.setItem('PVID', PVID.toString());
	return true;
    }
</script>
</body>
</html>

