<?php 
	include("../sqlsafe.php");
	include("../conn.php");
	$_SESSION['user'] = $_SESSION['admin'] = NULL;
	$username = $_POST['username'];
	$passwd = $_POST['passwd'];

	$len_u = strlen($username);
	$len_p = strlen($passwd);
	
	if(is_numeric($username)){
	}else{
                echo "<script>alert('学号或密码错误！')</script>";
        	echo "<meta http-equiv=\"Refresh\" content=\"0;url=login.html\">";
		exit();
	}
	// 定义用户名长度：8~10，密码长度：4~6
	if($len_u > 10 || $len_u < 8 || $len_p > 6 || $len_p < 4){
        	echo "<script>alert('输入的学号或密码长度非法！')</script>";
                echo "<meta http-equiv=\"Refresh\" content=\"0;url=login.html\">";
	}else{
		$sql = "select * from users where username='$username' and passwd='$passwd';";
		$result = $db->query($sql);
		if($result->num_rows > 0){
			@session_start();
			$_SESSION['user']=true;
			$_SESSION['admin']=false;
			$_SESSION['name']=$username;
			echo "<meta http-equiv=\"Refresh\" content=\"0;url=../\">";
		}else{
			echo "<script>alert('学号或密码错误！')</script>";
			echo "<meta http-equiv=\"Refresh\" content=\"0;url=login.html\">";
		}
	}
?>
