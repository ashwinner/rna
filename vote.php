<!DOCTYPE html>
<script src='js/jsbn/jsbn.js'></script>
<script src='js/jsbn/jsbn2.js'></script>
<html>
<body>

<h1> VOTE </h1>
<?php 

echo "<form name='vote' action='keyGenerator.php' method='post' onsubmit='return storePVID();'>
	Enter your PVID : <input type='text' name='pvid'>
	<br/>
	<input type='submit' value='submit'>
	</form>
	";

?>
<script>
    function storePVID() 
    {
	var PVID= new BigInteger(document.getElementsByName('pvid')[0].value);
	localStorage.setItem('PVID', PVID.toString());
	return true;
    }
</script>
</body>
</html>

