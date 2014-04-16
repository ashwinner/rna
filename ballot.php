<!DOCTYPE html>

<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<script src='js/jsbn/rng.js'></script>
<script src='js/jsbn/prng4.js'></script>
<html>

<body>

<h2>Ballot</h2>

<?php 
    
    include_once('Math/BigInteger.php');
    $collectorKey=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('collectorKey.pem')));
    
    $n = new Math_BigInteger($collectorKey['rsa']['n'], 256);
    $e = new Math_BigInteger($collectorKey['rsa']['e'], 256);
	$d = new Math_BigInteger($collectorKey['rsa']['d'], 256);

	echo "<form name='authenticate' action='collector.php' method='post' onsubmit='return EncryptAndBlind()'>
	Email : <input type='text' name='email'>
	<br/>
	PIN : &nbsp;&nbsp;&nbsp;<input type='password' name='pin'>
    	<br/>
	<input type= 'hidden' name= 'blindedPVID'>
    	<br/>
    	<br/>
    	Candidate: <input type='text' name='blindedEncryptedVote'>
    	<br/>
       	<input type='hidden' name='n' value='$n'>
    	<input type='hidden' name='e' value='$e'>
	<input type='hidden' name='d' value='$d'>
	<input type='submit' value='submit'>
	</form>
	";

?>

</body>
</html>

<script>
    function EncryptAndBlind() {
        
        var n = new BigInteger(document.getElementsByName('n')[0].value);
        var e = new BigInteger(document.getElementsByName('e')[0].value);
	var PVID=new BigInteger(localStorage.getItem('PVID'));
	var PVIDprefixed= new BigInteger("1110" + PVID.toString());
	//var PVIDprefixed=PVID;
	var key= localStorage.getItem('key');
	console.log("key :" +key);	
        var rng = new SecureRandom();

        var blindingFactor;

        do {
            blindingFactor = new BigInteger(1024, rng);
        } while(blindingFactor.compareTo(n)>=0 || blindingFactor.compareTo(BigInteger.ONE)<=0 || !blindingFactor.gcd(n).equals(BigInteger.ONE));
        
        localStorage.setItem('blindingFactorCollector', blindingFactor.toString());
        
        var vote = new BigInteger(document.getElementsByName('blindedEncryptedVote')[0].value);
	var encryptedVote= vote;
//	localStorage.setItem('encryptedVote', encryptedVote.toString());
	var encryptedVotePrefixed= new BigInteger("1110" + encryptedVote.toString());	 
        //console.log("Vote : " + vote);

        var blindedEncryptedVote = blindingFactor.modPow(e, n).multiply(encryptedVotePrefixed).mod(n);
	var blindedPVID= blindingFactor.modPow(e, n).multiply(PVIDprefixed).mod(n);
        //console.log("blindedNo : " + blindedEncryptedVote);
        document.getElementsByName('blindedEncryptedVote')[0].value=blindedEncryptedVote;
	//console.log("blindedPVID" + blindedPVID);
	document.getElementsByName('blindedPVID')[0].value=blindedPVID;
	
       //alert(blindingFactor);

return true;
    }

</script>

