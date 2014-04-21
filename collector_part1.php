<!DOCTYPE html>
<html>
<body>

<?php 
include('Crypt/AES.php');

	if(!isset($_POST['email']))
	{ 
		echo "Enter your email id before clicking on submit button";
		header('location:genpin.php');
	}
	require 'db.php';
	$userId=$_POST['email'];
	
	$query = "select pin from collectorValidate where userId = '{$userId}'";//sql query should not end with semi colon

	$result = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_array($result);

    if($row==NULL)
        echo "<h3>User Doesnt Exist!</h3>";

    else if(!($row['pin'])) //pin corresponding to the userId in the query is null
	{
        	// generate_pin() and mail it
		/*create a 4 digit random number*/
		$digits = 4;
		$pin=str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
		echo $pin;
		
		/*insert the new pin generated into db*/
		$query1 = "update collectorValidate set pin= '{$pin}' where userId='{$userId}'";
		mysql_query($query1) or die(mysql_error());
		
		/*email the generated pin to the emailID*/
		
		/* mail setup recipients, subject etc */
		$recipients = $userId;
		$headers["From"] = "niksmd92@gmail.com";
		$headers["To"] = $userId;
		$headers["Subject"] = "pin";
		$mailmsg = $pin;
		require 'sendEmail.php';
		/* Create the mail object using the Mail::factory method */
		$mail_object =& Mail::factory("smtp", $smtpinfo);
		/* Ok send mail */
		$mail_object->send($recipients, $headers, $mailmsg);
		echo "wow! pin sent to inbox";
		
	}
    
    else if($row['pin'])
        echo "<h3>You already have a pin generated earlier...Check your inbox</h3>";
?>

	<br/>
	<a href='index.html'>Click here to go back to the home page</a>
	<br/>
</body>
</html>
