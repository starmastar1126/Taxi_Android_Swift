<?php 
require_once 'config.php';
$db = new Cl_DBclass();





if( isset( $_POST['password'] ) && !empty($_POST['password'])){
	$password =md5( trim( $_POST['password'] ) );
	$email = $_POST['email'];
	
	if( !empty( $email) && !empty($password) ){
		$query = " SELECT count(email) cnt FROM users where password = '$password' and email = '$email' ";
		$result = mysqli_query($db->con, $query);
		$data = mysqli_fetch_assoc($result);
		if($data['cnt'] == 1){
			echo 'true';
		}else{
			echo 'false';
		}
	}else{
		echo 'false';
	}
	exit;
}


if( isset( $_POST['email'] ) && !empty($_POST['email'])){
	$email = $_POST['email'];
	$query = " SELECT count(email) cnt FROM users where email = '$email' ";
	$result = mysqli_query($db->con, $query);
	$data = mysqli_fetch_assoc($result);
	if($data['cnt'] > 0){
		echo 'false';
	}else{
		echo 'true';
	}
	exit;
}

if( isset( $_GET['email'] ) && !empty($_GET['email'])){
	$email = $_GET['email'];
	$query = " SELECT count(email) cnt FROM users where email = '$email' ";
	$result = mysqli_query($db->con, $query);
	$data = mysqli_fetch_assoc($result);
	if($data['cnt'] == 1){
		echo 'true';
	}else{
		echo 'false';
	}
	exit;
}