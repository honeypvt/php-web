<?php 
	
	//-------WARNING---------//
	//php mail function is deprecated and no longer used in Android News App v4.3.0 or higher//
	//-----------------------//

	// be careful when you change the email subject and content, do not change or remove the variables, just change the text content
	// inaccuracies in changing or removing variables can cause errors

	$to = $_GET['email'];

	//subject
	$subject = '[IMPORTANT] Android News App Forgot Password Information';

	//message
	$message='<div style="background-color: #f9f9f9;" align="center"><br />
	<table style="font-family: OpenSans,sans-serif; color: #666666;" border="0" width="600" cellspacing="0" cellpadding="0" align="center" bgcolor="#FFFFFF">
	<tbody>
	<tr>
	<td width="600" valign="top" bgcolor="#FFFFFF"><br>
	<table style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; padding: 15px;" border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
	<tbody>
	<tr>
	<td valign="top"><table border="0" align="left" cellpadding="0" cellspacing="0" style="font-family:OpenSans,sans-serif; color: #666666; font-size: 10px; width:100%;">
	<tbody>
	<tr>
	<td><p style="color: #262626; font-size: 28px; margin-top:0px;"><strong>Dear '.$row['name'].'</strong></p>
	<p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;">Thank you for using Android News App,<br>
	Your password is: '.$row['password'].'</p>
	<p style="color:#262626; font-size:20px; line-height:32px;font-weight:500;margin-bottom:30px;">Thanks you,<br />
	Android News App.</p></td>
	</tr>
	</tbody>
	</table></td>
	</tr>

	</tbody>
	</table></td>
	</tr>
	<tr>
	<td style="color: #262626; padding: 20px 0; font-size: 20px; border-top:5px solid #52bfd3;" colspan="2" align="center" bgcolor="#ffffff">Copyright © Android News App.</td>
	</tr>
	</tbody>
	</table>
	</div>';

	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Android News App <don-not-reply@solodroid.net>' . "\r\n";
	
	//Mail it
	@mail($to, $subject, $message, $headers);

?>