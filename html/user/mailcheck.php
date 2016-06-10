<?php
	include("../sqlsafe.php");
	include("../conn.php");


        @session_start();
        if(!isset($_SESSION['user'])){
		echo "<script>alert('请先登录!');</script>";
                echo "<meta http-equiv=\"Refresh\" content=\"0;url=login.html\">";
                exit();
        }
	
	$email = $_POST['email'];	
	$vcode = $_POST['code_num2'];

	$username = $_SESSION['name'];
	if($vcode != $_SESSION['VCODE2'] || $vcode == ''){
		echo "<script>alert('验证码错误!');</script>";
		echo "<script>history.go(-1);</script>";	
		exit();	
	}

	if(strlen($email) < 13 || strlen($email)>25){
                echo "<script>alert('邮箱输入非法!');</script>";
                echo "<script>history.go(-1);</script>";
                exit();
	}
	if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)){
		echo "<script>alert('邮箱输入非法!');</script>";
                echo "<script>history.go(-1);</script>";
                exit();
	}else{
		//先判断是否已经激活
		$sql = "select * from users where username='".$username."' and isactive='1'";	
		$res = $db->query($sql);
		if($res->num_rows > 0){
			echo "<script>alert('你的帐号已经激活！')</script>";
			echo "<meta http-equiv=\"Refresh\" content=\"0;url=../index.php\">";
			exit();
		}else{
			$sql_2 = "select username from votelogs where email='".$email."'";
			$res_2 = $db->query($sql_2);
			if($res_2->num_rows > 0){
				echo "<script>alert('该邮箱已被其他用户使用!');</script>";
				echo "<script>history.go(-1);</script>";
				exit();
			}else{
				$sql = "select passwd from users where username='".$username."'"; 
				$res = $db->query($sql);
				$user = mysqli_fetch_assoc($res);
				$md5str = $username.$user['passwd']."heheda";
				$keystring = md5($md5str);
				
				//发送邮件
				require 'PHPMailer/config.php';

				if($mail->Send()) {
    					//邮件发送成功
					$sql = "update users set email='".$email."' where username='".$username."'";
					$res = $db->query($sql);
					echo "<meta http-equiv=\"Refresh\" content=\"0;url=../index.php\">";
					echo "<script>alert('邮件已发送，请登录你的邮箱激活帐号！');</script>";
				}else{
					echo "<script>alert('邮件发送失败，请确定你的邮箱正确！')";
					exit();
				}
			}
		}
	}
?>
