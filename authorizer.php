<!DOCTYPE html>

<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<html>
<body>
<h2>Result</h2>

<?php 

    include_once('Math/BigInteger.php');
    $key=openssl_pkey_get_details(openssl_pkey_get_private(file_get_contents('key.pem')));
    
    $d = new Math_BigInteger($key['rsa']['d'], 256);
    $n = new Math_BigInteger($key['rsa']['n'], 256);
    $e = new Math_BigInteger($key['rsa']['e'], 256);

    $pseudoID = new Math_BigInteger($_POST['pseudoID'], 10);

    $signedPseudoID = $pseudoID->powMod($d, $n);

    echo "<input type='hidden' name='signedPseudoID' value='$signedPseudoID'>
          <br/>
          <input type='hidden' name='n' value='$n'>
          <br/>
          ";
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
