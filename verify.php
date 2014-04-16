<!DOCTYPE html>

<html>
<body>

<h1> Vote Verification</h1>

<form name='verify' action='verifyResult.php' method='post' onsubmit='encrypt()'>
PVID : <input type='text' name='pvid'>
<br/>
Key &nbsp;&nbsp; : <input type='text' name='key'>
<br/>
Vote &nbsp;&nbsp;: <input type='text' name='vote'>
<br/>
<input type='submit' value='submit'>
<br/>
</form>

</body>
</html>

<script>
function encrypt() {
	
	var key = document.getElementsByName('key')[0].value;
	var vote = document.getElementsByName('vote')[0].value;

	vote = vote; //encrypt vote with key here

	return true;
}
