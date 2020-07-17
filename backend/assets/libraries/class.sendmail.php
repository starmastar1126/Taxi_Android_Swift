<?php 
Class SendPHPMail
{

	//function to send emails to whole site
	function Send($vType,$vSection,$ToEmail,$bodyArr,$postArr)
	{
		global $obj,$MAIL_FOOTER,$SITE_URL,$SITE_TITLE,$ADMIN_EMAIL, $site_image_url;
		$sql="SELECT iFormatId ,vSub ,tBody   FROM email_template WHERE vType='$vType' AND eSection = '$vSection' ";
		$db_email=$obj->MySQLSelect($sql);
		
		//headers information
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= 'From: '.$SITE_TITLE.' <'.trim($ADMIN_EMAIL).'>' . "\r\n".
					'Reply-To: '.$SITE_TITLE.' <'.trim($ADMIN_EMAIL).'>'. "\r\n".
					'Return-Path: '.$SITE_TITLE.' <'.trim($ADMIN_EMAIL).'>' . "\r\n".
					'X-Mailer: PHP/' . phpversion();

		$Subject = strtr( $db_email[0]["vSub"], "\r\n" , "  " );
		$this->body = $db_email[0]['tBody'];

		$this->body = nl2br(str_replace($bodyArr,$postArr, $this->body));
		//print_r($this->body);exit;
		$To 		= $ToEmail;
		
		$htmlMail = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
					<title>'.$this->xheaders['Subject'].'</title>
					<style>
					body{color:#000000; font-family:Arial, Helvetica, sans-serif; font-size:12px;}
					</style>
					</head>
					<body>
					<table width="610" border="0" bgcolor="#F7FFED" cellspacing="0" cellpadding="0" style="border:3px solid #E1693F;">
					<tr>
						<td><img src="'.$site_image_url.'logo.gif" alt=""/></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">
							<tr>
								<td>'.$this->body.'</td>
							</tr>
							</table>
						</td>
					</tr>
					</table>
					</body>
					</html>';
		  $this->body=$htmlMail;      
		  #print_r($htmlMail);exit;
		 //echo "<pre>";
		 //echo $this->body;
		 //exit;
         //echo $To."<hr>".$Subject."<hr>".$this->body."<hr>".$headers."<hr>";exit;
			return $res = @mail($To,$Subject,$this->body,$headers);
	}		
	function SendMail($From, $To,$Subject,$vBody,$name)
	{
		global $obj,$MAIL_FOOTER,$SITE_URL,$SITE_TITLE;
		//headers information
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= 'From: '.trim($From).' <'.trim($From).'>' . "\r\n".
					'Reply-To: '.trim($From).' <'.trim($From).'>'. "\r\n".
					'Return-Path: '.trim($From).' <'.trim($From).'>' . "\r\n".
					'X-Mailer: PHP/' . phpversion();
		$Subject = strtr($Subject, "\r\n" , "  " );
		$this->body = $vBody;
		$ToEmail = $To;
		$htmlMail = '
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>From  : '.$name.' ( '.trim($From).' )</td>
							</tr>
							<tr>
								<td>To  : '.trim($ToEmail).'</td>
							</tr>
							<tr>
								<td>Subject  : '.$Subject.'</td>
							</tr>
							<tr>
								<td>Body  : '.$this->body.'</td>
							</tr>
						</table>
					';
		##TEMPORARY COMMENT
		//$this->strTo = $this->xheaders['To'];
		
		//echo $ToEmail ."<hr>". $headers."<hr>". $this->body."<hr>".$headers;
		$this->body=$htmlMail;
		$res = @mail( $ToEmail, $Subject, $this->body, $headers);
		
		/*if($_SERVER["HTTP_HOST"] != "192.168.32.150")
			$res = @mail( $ToEmail, $Subject, $this->body, $headers);*/
		return $res;
	}
	// To Send Mail
	function SendMailFriend($From, $To,$Subject,$vBody)
	{
		global $obj,$MAIL_FOOTER,$SITE_URL,$SITE_TITLE;
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= 'From: '.trim($From).' <'.trim($From).'>' . "\r\n".
					'Reply-To: '.trim($From).' <'.trim($From).'>'. "\r\n".
					'Return-Path: '.trim($From).' <'.trim($From).'>' . "\r\n".
					'X-Mailer: PHP/' . phpversion();
		$Subject = strtr($Subject, "\r\n" , "  " );
		$this->body = $vBody;
		$ToEmail = $To;
		
		$this->body=$vBody;
		//print_r($vBody);exit;
		//$res = @mail($ToEmail, $Subject, $this->body, $headers);
		
		//if($_SERVER["HTTP_HOST"] != "192.168.32.150")
			$res = @mail( $ToEmail, $Subject, $this->body, $headers);
		return $res;	
	}
	
	/* SEND NEWSLETTER */
	function SendNewsletter($From_Email,$From_Name,$Subject,$iNewsId,$To_arr,$tContent)
	{ 
		global $obj,$MAIL_FOOTER,$SITE_URL,$SITE_TITLE;
		//headers information
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= 'From: '.trim($From_Name).' <'.trim($From_Email).'>' . "\r\n".
					'Reply-To: '.trim($From_Name).' <'.trim($From_Email).'>'. "\r\n".
					'Return-Path: '.trim($From_Name).' <'.trim($From_Email).'>' . "\r\n".
					'X-Mailer: PHP/' . phpversion();
		
		$Subject = strtr($Subject, "\r\n" , "  " );
		$htmlMail = '
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>'.$tContent.'</td>
			</tr>
			</table>
		';
		
		for($i=0;$i<count($To_arr);$i++)
		{
			$ToEmail = $To_arr[$i]['vEmail'];
			$res = @mail($ToEmail, $Subject, $htmlMail, $headers);
			 
		}
		// echo $ToEmail.'<hr>'.$Subject.'<hr>'.$htmlMail.'<hr>'.$headers;echo '<hr>';  exit;
		return $res;
	}

}
?>
