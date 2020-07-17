<?php 
	
	/*
		* Validation.class.php
		* Generic PHP Server Side Validator 1.0
		* Author: Nilesh Gamit 
		* Date: Oct 12, 2006
		* For updates visit http://sourceforge.net/projects/php-validator
		*
		* This script is distribuited under GNU LESSER GENERAL PUBLIC LICENSE
	*/
	
	Class validation {
		
		function validation() {
			$this->id = 0;
		}
		
		function add_fields($postVar, $authType, $error) {
			$index = $this->id++;
			$this->check_vars[$index]['data'] = $postVar;
			$this->check_vars[$index]['authtype'] = $authType;
			$this->check_vars[$index]['error'] = $error;
		}
		
		function validate() {
			$errorMsg = "";
			
			for($i = 0; $i < $this->id; $i++) {
				$postVar  = $this->check_vars[$i]['data'];
				$authType = $this->check_vars[$i]['authtype'];
				$error    = $this->check_vars[$i]['error'];
				
				$pos = strpos($authType, '=');
				if($pos !== false) {
					$authType = substr($this->check_vars[$i]['authtype'], 0, $pos);
					$value    = substr($this->check_vars[$i]['authtype'], $pos+1);
				}
				
				switch($authType) {
					
					case "req": {
						if(is_array($postVar['name'])) {
							$count = count($postVar['name']);
							
							for($j=0; $j<$count; $j++) {
								$length = strlen(trim($postVar['name'][$j]));
								if(!$length)
								$errorMsg .= $error." :File ".($j+1)."<br>";
							}
						}
						elseif(isset($postVar['name']) && empty($postVar['name'])) {
   							$length = strlen(trim($postVar['name']));
							if(!$length)
							$errorMsg .= "- ".$error."<br>";
						}
						else{
							$length = strlen(trim($postVar));
							if(!$length)
							$errorMsg .= "* ".$error."<br>";
						}
						break;
					}
					
					case "alpha": {
						$regexp = '/^[A-za-z]$/';
						if (!preg_match($regexp, trim($postVar))) {
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "alphanum": {
						$regexp = '/^[A-za-z0-9]$/';
						if (!preg_match($regexp, trim($postVar))) {
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "num": {
						$regexp = '/^[0-9]$/';
						if (!preg_match($regexp, trim($postVar))) {
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
    				case "max": {
						$length = strlen(trim($postVar));
						if ($length > $value)
						$errorMsg .= $error."<br>";
						break;
					}
					
					case "min": {
						$length = strlen(trim($postVar));
						if ($length < $value && $length != 0)
						$errorMsg .= $error."<br>";
						break;
					}
					
					case "lte": {
						if(is_array($postVar)) {
							$count = count($postVar);
							if ($count > $value)
							$errorMsg .= $error."<br>";
						}
						else {
							if ($postVar > $value)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "gte":{
						if(is_array($postVar)){
							$count = count($postVar);
							if ($count < $value)
							$errorMsg .= $error."<br>";
						}
						else {
							if ($postVar < $value){
								$length = strlen(trim($postVar));
								if($length)
								$errorMsg .= $error."<br>";
							}
						}
						break;
					}
					
					case "username": {
						$regexp1 = '/^[0-9]$/';
						$regexp2 = '/^[a-zA-Z]+[a-zA-Z0-9\.\_]*[a-zA-Z0-9]+$/';
						if (!preg_match($regexp1, trim($postVar)) && !preg_match($regexp2, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "name":{
						$regexp = "/^[a-zA-Z]+[a-zA-Z\.\- ]*[a-zA-Z]+$/";
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "address":{
						$regexp = '/^[a-zA-Z0-9]+.*$/';
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "phone": {
						
						if(isset($value)){
							$found = strpos($value, ',');
							if($found === false){
								$options[0] = $value;
							}
							else{
								$options = explode(",", $value);
							}
						}
						
						$patternMatch = 0;
						foreach($options as $opt){
							$type = $this->availablePhoneType($opt);
							foreach($type as $regexp) {
								if(preg_match($regexp, $postVar)) {
									$patternMatch = 1;
								}
							}
					        if($patternMatch)           break;
						}
						
						if(!$patternMatch) {
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "mobile": {
						$regexp1 = '/^[0-9]{10}$/';
						# (+91)1111111111
						$regexp2 = '/^[\(][\+][0-9]{2}[\)][0-9]{10}$/';
						# +911111111111
						$regexp3 = '/^[\+][0-9]{2}[0-9]{10}$/';
						#91-1111111111
						$regexp4 = '/^[0-9]{2}[\-][0-9]{10}$/';
						
						if (!preg_match($regexp1, trim($postVar)) && !preg_match($regexp2, trim($postVar)) && !preg_match($regexp3, trim($postVar)) && !preg_match($regexp4, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "zip":{
						$regexp = '/^[0-9]{6,10}$/';
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "uszip":{
						# 12345-6789
						$regexp = '/^[0-9]{5}[\-]{1}[0-9]{4}$/';
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "ukzip":{
						# ZW3 3SW
						$regexp = '/^[a-zA-Z]{2}[0-9]{1}[ ]{1}[0-9]{1}[a-zA-Z]{2}$/';
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "ssn":{
						$regexp = '/^(?!000)([0-6][0-9]{2}|7([0-6][0-9]|7[012]))([ -]?)(?!00)[0-9][0-9]\3(?!0000)[0-9]{4}$/';
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "currency":{
						$regexp1 = '/^[0-9]+\.[0-9]+$/';
						$regexp2 = '/^[0-9]+$/';
						if (!preg_match($regexp1, trim($postVar)) && !preg_match($regexp2, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "email":{
						$regexp = '/^[A-Za-z0-9]+((\.|\_){1}[a-zA-Z0-9]+)*@([a-zA-Z0-9]+([\-]{1}[a-zA-Z0-9]+)*[\.]{1})+[a-zA-Z]{2,12}$/';
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "url":{
						$regexp = '|^http(s)?://[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i';
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "ip":{
						$regexp = '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/';
						if (!preg_match($regexp, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
    				case "date":{
						$errorMsg .= $this->validateDate(trim($postVar), $value, $error);
						break;
					}
					
					case "ftype":{
						$errorMsg .= $this->validateFileType($postVar, $value, $error);
						break;
					}
					
					case "fsize":{
						$errorMsg .= $this->validateFileSize($postVar, $value, $error);
						break;
					}
					
					case "imgwh":{
						$errorMsg .= $this->validateImageHeightWidth($postVar, $value, $error);
						break;
					}
					
					case "custom":{
						if (!preg_match($value, trim($postVar))){
							$length = strlen(trim($postVar));
							if($length)
							$errorMsg .= $error."<br>";
						}
						break;
					}
					
					case "daterange":{ #echo "hi"; exit;
						$Drange = explode("|",$postVar);
						$Ddate = $Drange[0];
						$Edate = $Drange[1];
						$Dtime = $Drange[2];
						$Etime = $Drange[3];
						
						$Distance_time = explode(" ",$Drange[4]);
						#$Distance_time = $Distance_time[0].':'.$Distance_time[2];
						
						if($Ddate > $Edate)
						{
							
							$errorMsg .= $error."<br>";
						}
						break;
					}
					case "timerange":{
						$Trange = explode("|",$postVar);
						//print_r($Trange);exit;
						$Ddate = $Trange[0];
						$Edate = $Trange[1];
						$Dtime = $Trange[2];
						$Etime = $Trange[3];
						$Ddate2 = $Trange[0]." ".$Trange[2];
						$Edate2 = $Trange[1]." ".$Trange[3];
						/* current date in second*/
						$curdate=strtotime(Date('Y-m-d H:i'));
						/* add 1 hour to date */
						$futDate=$curdate+(60*60);
						$reqDate=Date('Y-m-d H:i',$futDate);
						if(strtotime($Ddate2) < strtotime($reqDate))
						{
							$errorMsg .= $error."<br>";
						}
						else
						{
							if($Ddate >= $Edate )
							{
								if(strtotime($Ddate2) >= strtotime($Edate2))
								{	
									$errorMsg .= $error."<br>";
								}
							}
						}
						break;
					}
					
					case "singledaterange":{
						$SingleDrange = explode("|",$postVar);
						$sdate = $SingleDrange[0];
						$stime=$SingleDrange[2];
						$sdate1=$sdate." ".$stime;
						$date2=strtotime($sdate1);
						#echo $test=Date('Y-m-d H:i');exit;
						/* current date time on server in second */
						$curdate=strtotime(Date('Y-m-d H:i'));
					    /* add 1 hour to date */
						$futDate=$curdate+(60*60);
						$reqDate=Date('Y-m-d H:i',$futDate); 
						
						if($date2 < strtotime($reqDate))
						{
							$errorMsg .= $error."<br>";	
						}
						break;
					}
					case "latlongblank":{ 
						$Lat_long_location = explode("|",$postVar);
						if (in_array("", $Lat_long_location))
  						{
							$errorMsg .= $error."<br>";	
						}
						break;
					}
				}
			}
			
			return $errorMsg;
		}
		
		function validateDate($postVar, $value, $error) {
    		$errorMsg = "";
			
			$length = strlen(trim($postVar));
			if($length){
				
				if(isset($value)){
					$found = strpos($value, ',');
					if($found === false){
						$options[0] = $value;
					}
					else{
						$options = explode(",", $value);
					}
				}
				else{
					$options[0] = 'dd-mm-yyyy';
				}
				
				$patternMatch = 0;
				foreach($options as $opt){
					$pos1 = strpos($opt, '-');
					$pos2 = strpos($opt, '/');
					$pos3 = strpos($opt, '.');
					
					if($pos1 !== false){
						if($pos1==2){
							if(strlen($opt) == 8)
							$regexp = '/^[0-9]{2}[\-][0-9]{2}[\-][0-9]{2}$/';
							else
							$regexp = '/^[0-9]{2}[\-][0-9]{2}[\-][0-9]{4}$/';
						}
						if($pos1==4)
						$regexp = '/^[0-9]{4}[\-][0-9]{2}[\-][0-9]{2}$/';
					}
					
					if($pos2 !== false){
						if($pos2==2){
							if(strlen($opt) == 8)
							$regexp = '/^[0-9]{2}[\/][0-9]{2}[\/][0-9]{2}$/';
							else
							$regexp = '/^[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$/';
						}
						if($pos2==4)
						$regexp = '/^[0-9]{4}[\/][0-9]{2}[\/][0-9]{2}$/';
					}
					
					if($pos3 !== false){
						if($pos3==2){
							if(strlen($opt) == 8)
							$regexp = '/^[0-9]{2}[\.][0-9]{2}[\.][0-9]{2}$/';
							else
							$regexp = '/^[0-9]{2}[\.][0-9]{2}[\.][0-9]{4}$/';
						}
						if($pos3==4)
						$regexp = '/^[0-9]{4}[\.][0-9]{2}[\.][0-9]{2}$/';
					}
					
					if(preg_match($regexp, $postVar)){
						$patternMatch = 1;
						if((isset($pos1) && $pos1==2) || (isset($pos2) && $pos2==2) || (isset($pos3) && $pos3==2)){
							$str1 = substr($opt, 0, 2);
							$str2 = substr($opt, 3, 2);
							
							if($str1 == 'dd'){
								$DD = substr($postVar, 0, 2);
								$MM = substr($postVar, 3, 2);
								$YY = substr($postVar, 6);
							}
							if($str1 == 'mm'){
								$MM = substr($postVar, 0, 2);
								$DD = substr($postVar, 3, 2);
								$YY = substr($postVar, 6);
							}
							if($str1 == 'yy'){
								if($str2 == 'mm'){
									$YY = substr($postVar, 0, 2);
									$MM = substr($postVar, 3, 2);
									$DD = substr($postVar, 6);
								}
								else{
									$MM = substr($postVar, 0, 2);
									$DD = substr($postVar, 3, 2);
									$YY = substr($postVar, 6);
								}
							}
						}
						
						if((isset($pos1) && $pos1==4) || (isset($pos2) && $pos2==4) || (isset($pos3) && $pos3==4)){
							$str = substr($opt, 5, 2);
							
							if($str == 'dd'){
								$YY = substr($postVar, 0, 4);
								$DD = substr($postVar, 6, 2);
								$MM = substr($postVar, 8, 2);
							}
							if($str == 'mm'){
								$YY = substr($postVar, 0, 4);
								$MM = substr($postVar, 6, 2);
								$DD = substr($postVar, 6, 2);
							}
						}
						
						if($DD == 0 || $MM == 0 || $YY==0){
							$errorMsg .= "Invalid Date...<br>";
						}
						
						if($MM <= 12){
							switch($MM) {
								case 4:
								case 6:
								case 9:
								case 11:
								if ($DD > 30){
									$errorMsg .= "Selected month has maximum 30 days.<br>";
								}
								default:
								if ($DD > 31){
									$errorMsg .= "Selected month has maximum 31 days.<br>";
								}
								break;
							}
						}
						
						if (($YY % 4) == 0) {
							if (($MM == 2) && ($DD > 29)) {
								$errorMsg .= "Invalid days in February for leap year.<br>";
							}
						}
						else {
							if (($MM == 2) && ($DD > 28)) {
								$errorMsg .= "Invalid days in February for non leap year.<br>";
							}
						}
					}
					
					if($patternMatch)           break;
				}
				
				if(!$patternMatch)	$errorMsg .= $error."<br>";
				
			}
			return $errorMsg;
		}
		
		function validateFileType($postVar, $value, $error) {
			$errorMsg = "";
			// echo "<pre>";
			// print_r($postVar);

			// die;
			if(isset($value)){
				$found = strpos($value, ',');
				if($found === false){
					$options[0] = $value;
				}
				else{
					$options = explode(",", $value);
				}
			}
			
			if(is_array($postVar['name'])){
				$totalFiles = count($postVar['name']);
				
				for($i=0; $i < $totalFiles; $i++){
					if($postVar['name'][$i]){
						$fileTypeMatch = 0;
						foreach($options as $id => $type){
							$typeArray = $this->availableFileTypes($type);
							if(in_array($postVar['type'][$i], $typeArray)){
								$fileTypeMatch = 1;
							}
							if($fileTypeMatch)	break;
						}
						
						if(!$fileTypeMatch){
							$errorMsg .= $error." (".$postVar['name'][$i].")<br>";
						}
					}
				}
			}
			else {
				if($postVar['name']){
					$fileTypeMatch = 0;
					foreach($options as $id => $type){
						$typeArray = $this->availableFileTypes($type);
						if(in_array($postVar['type'], $typeArray)){
							$fileTypeMatch = 1;
						}
						if($fileTypeMatch)  break;
					}
					
					if(!$fileTypeMatch){
						$errorMsg .= $error." (".$postVar['name'].")<br>";
					}
				}
			}
			
			return $errorMsg;
		}
		
		function availableFileTypes($ext)
		{
			switch($ext){
				
				case "txt":
				$type[0] = "text/plain";
				break;
				
				case "xml":
				$type[0] = "text/xml";
				$type[1] = "application/xml";
				break;
				
				case "csv":
				$type[0] = "text/x-comma-separated-values";
				$type[1] = "application/octet-stream";
				$type[2] = "text/plain";
				break;
				
				case "zip":
				$type[0] = "application/zip";
				break;
				
				case "tar":
				$type[0] = "application/x-gzip";
				break;
				
				case "ctar":
				$type[0] = "application/x-compressed-tar";
				break;
				
				case "pdf":
				$type[0] = "application/pdf";
				break;
				
				case "doc":
				$type[0] = "application/msword";
				$type[1] = "application/octet-stream";
				break;
				
				case "xls":
				$type[0] = "application/vnd.ms-excel";
				$type[1] = "application/vnd.oasis.opendocument.spreadsheet";
				break;
				
				case "ppt":
				$type[0] = "application/vnd.ms-powerpoint";
				break;
				
				case "jpg":
				$type[0] = "image/jpg";
				$type[1] = "image/jpeg";
				$type[2] = "image/pjpeg";
				break;
				
				case "gif":
				$type[0] = "image/gif";
				break;
				
				case "png":
				$type[0] = "image/png";
				break;
				
				case "bmp":
				$type[0] = "image/bmp";
				break;
				
				case "icon":
				$type[0] = "image/x-ico";
				break;
				
				case "font":
				$type[0] = "application/x-font-ttf";
				break;
			}
			
			return $type;
		}
		
		function validateFileSize($postVar, $value, $error)
		{
			$errorMsg = "";
			if(is_array($postVar['name'])){
				$totalFiles = count($postVar['name']);
				
				for($i=0; $i < $totalFiles; $i++){
					if($postVar['name'][$i]){
						if($postVar['size'][$i] > $value){
							$errorMsg .= $error." (".$postVar['name'][$i].")<br>";
						}
					}
				}
			}
			else {
				if($postVar['size'] > $value){
					$errorMsg .= $error." (".$postVar['name'].")<br>";
				}
			}
			
			return $errorMsg;
		}
		
		function validateImageHeightWidth($postVar, $value, $error)
		{
			$errorMsg = "";
			if(isset($value)){
				$found = strpos($value, ',');
				if($found === false){
					$options[0] = $value;
				}
				else{
					$options = explode(",", $value);
					$W = $options[0];
					$H = $options[1];
				}
			}
			
			if(is_array($postVar['name'])){
				$totalFiles = count($postVar['name']);
				
				for($i=0; $i < $totalFiles; $i++){
					if($postVar['name'][$i]){
						list($width, $height) = getimagesize($postVar['tmp_name'][$i]);
						
						if( ($height > $W || $width > $H) && $postVar['tmp_name'][$i]) {
							$errorMsg .= $error." (".$postVar['name'][$i].")<br>";
						}
					}
				}
				} else {
				list($width, $height) = getimagesize($postVar['tmp_name']);
				if( ($height < $H || $width < $W) && $postVar['tmp_name']) {
					$errorMsg .= $error." (".$postVar['name'].")<br>";
				}
			}
			
			return $errorMsg;
		}
		
		function availablePhoneType($country) {
			
			switch($country) {
				
				case "in": # India
				$type[0]  = '/^[0-9]{6,10}$/';
				# (+91)[022]111111
				$type[1]  = '/^[\(][\+][0-9]{2}[\)][\[][0-9]{3,5}[\]][0-9]{6,10}$/';
				# +91022111111
				$type[2]  = '/^[\+][0-9]{2}[0-9]{3,5}[0-9]{6,10}$/';
				# 91-111111
				$type[3]  = '/^[0-9]{2}[\-][0-9]{6,10}$/';
				break;
				
				case "br": # Brazil
				$type[0] = '/^([0-9]{2})?(\([0-9]{2})\)([0-9]{3}|[0-9]{4})-[0-9]{4}$/';
				break;
				
				case "fr": # France
				$type[0] = '/^([0-9]{2})?(\([0-9]{2})\)([0-9]{3}|[0-9]{4})-[0-9]{4}$/';
				break;
				
				case "us": # US
				$type[0] = '/^[\(][0-9]{3}[\)][0-9]{3}[\-][0-9]{4}$/';
				break;
				
				case "sw": # Swedish
				$type[0] = '/^(([+][0-9]{2}[ ][1-9][0-9]{0,2}[ ])|([0][0-9]{1,3}[-]))(([0-9]{2}([ ][0-9]{2}){2})|([0-9]{3}([ ][0-9]{3})*([ ][0-9]{2})+))$/';
				break;
			}
			
			return $type;
		}
		
	}
	
?>