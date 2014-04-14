<!DOCTYPE html>

<html>
<body>

<h1> VOTE </h1>
<?php 

echo "<form name='vote' action='keyGenerator.php' method='post' onsubmit='storePVID();'>
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
    }

</body>
</html>

