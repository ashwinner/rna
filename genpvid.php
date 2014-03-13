<!DOCTYPE html>

<html>

<body>

<h2>Generate your PVID</h2>

<?php 

if(isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['pseudoID']))
{
	//check for valid email and password
	//if valid, sign
	//else return error

	var_dump($_POST['email']);
	echo "<br/>";
	var_dump($_POST['pass']);
	echo "<br/>";
	var_dump($_POST['pseudoID']);
	echo "<br/>";
	echo "<script>
		alert(localStorage.getItem('lastname'));
		</script>
		";


}
else
{
	echo "<form name='authenticate' action='genpvid.php' method='post' onsubmit='return blind()'>
	Email : <input type='text' name='email'>
	<br/>
	PIN : &nbsp;&nbsp;&nbsp;<input type='password' name='pass'>
	<br/>
	Blinding Factor : <input type='text' name='bf'>
	<button name=genbf onclick='genbf()'>Generate new BF</button>
	<br/>
	<input type='hidden' name='pseudoID' value=''>
	<input type='submit' value='submit'>
	</form>
	";
}
?>

</body>
</html>

<script>
function genbf() {
document.getElementsByName('genbf')[0].value=100;
return false;
}
function blind() {
//var bf = bf();
//var rn = rn();
//document.getElementsByName('pseudoID')[0].value=blind(1000+rn, bf);
localStorage.setItem("lastname", "Smith");
return true;
}
</script>

