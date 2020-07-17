<?php 
	include_once('common.php');
	if(isset($_REQUEST['type']))
	{
		$type=$_REQUEST['type'];
		if($type=='send'){
			$email=$_REQUEST['vEmail'];
			$name=$_REQUEST['vName'];
			$code=$_REQUEST['vCode'];
			$mail['ToName']=$name;
			$mail['code']=$code;
			$mail['vEmail']=$email;
			$data['code']=$code;
			
			$returnArr=$generalobj->sendCode($_REQUEST['vPhone'],$_REQUEST['vCode']);
			$data['sms']=$returnArr['action'];
			$data['type']='send';
			$data[0]='0';
			$_SESSION['code']=$returnArr['verificationCode'];
			$data['code']=$returnArr['verificationCode'];
			echo json_encode($data);exit;
		}
		else if($type=='verify'){
			if($_REQUEST['vCode']==''){
				$data1['type']='verify';
				$data1['0']=2;
				echo json_encode($data1);exit;
			}
			else{
				$code=trim($_REQUEST['vCode']);
				$vcode=trim($_SESSION['code']);
				if($code!=$vcode){
					$data1['type']='verify';
					$data1['0']=0;
					echo json_encode($data1);exit;
				}
				else{
					unset($_SESSION['code']);
					$data1['type']='verify';
					$data1[0]=1;
					echo json_encode($data1);exit;
				}
			}
		}
	}
?>
