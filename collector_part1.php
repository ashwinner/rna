<!DOCTYPE html>


<?php 
include('Crypt/AES.php');

	if(!isset($_POST['email']))
	{ 
		echo "Enter your email id before clicking on submit button";
		header('location:genpin2.php');
	}
	require 'collectordb_part1.php';
	 $userId=$_POST['email'];
	
	$query = "select pin2 from validate where email_id = '{$userId}'";//sql query should not end with semi colon

	$result = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_array($result);

    if($row==NULL)
        echo "<h3>User Doesnt Exist!</h3>";

    else if(!($row['pin2'])) //pin corresponding to the userId in the query is null
	{
        	// generate_pin2() and mail it
		/*create a 4 digit random number*/
		$digits = 4;
		$pin=str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
		echo $pin;
		
		/*insert the new pin generated into db*/
		$query1 = "update validate set pin2= '{$pin}' where email_id='{$userId}'";
		mysql_query($query1) or die(mysql_error());
		
		/*email the generated pin to the emailID*/
		
		/* mail setup recipients, subject etc */
		$recipients = $userId;
		$headers["From"] = "niksmd92@gmail.com";
		$headers["To"] = $userId;
		$headers["Subject"] = "pin2";
		$mailmsg = $pin;
		require 'sendEmail.php';
		/* Create the mail object using the Mail::factory method */
		$mail_object =& Mail::factory("smtp", $smtpinfo);
		/* Ok send mail */
		$mail_object->send($recipients, $headers, $mailmsg);
		echo "wow! pin sent to inbox";
		
	}
    
    else if($row['pin2'])
        echo "<h3>You already have a pin2 generated earlier...Check your inbox</h3>";


//encrypt vote using aes encryption
/*
$aes = new Crypt_AES();
$aes->setKey('abcdefghijklmnop');

*/
?>
