<?php 
	include("../sqlsafe.php");
	include("../conn.php");
	
	$user = $_GET['user'];
	$key = $_GET['active'];

	$len_u = strlen($user);
	$len_k = strlen($key);


	// 定义用户名长度：8~10
	if($len_u > 10 || $len_u < 8 || $len_k != 32){
        	echo "<script>alert('请求的链接非法！')</script>";
                echo "<meta http-equiv=\"Refresh\" content=\"0;url=../index.php\">";
		exit();
	}else{
		//先判断是否已经激活
		$sql = "select * from users where username='".$user."' and isactive='1'";	
		$res = $db->query($sql);
		if($res->num_rows > 0){
			echo "<script>alert('你的帐号已经激活！')</script>";
			echo "<meta http-equiv=\"Refresh\" content=\"0;url=../index.php\">";
			exit();
		}else{
			//激活邮箱
			$sql = "select username,passwd from users where username='".$user."'";
			$res = $db->query($sql);
			if($res->num_rows == 1){
				$userinfo = mysqli_fetch_assoc($res);	
				$md5str = $user.$userinfo['passwd']."heheda";
				$keystring = md5($md5str);
			}else{
				echo "<script>alert('用户不存在！')</script>";
                                echo "<meta http-equiv=\"Refresh\" content=\"0;url=../index.php\">";
				exit();
			}
	
			
			if($keystring == $key){
				$sql = "update users set isactive='1' where username='".$userinfo['username']."'";
				$res = $db->query($sql);
				if($res){
					echo "<script>alert('激活成功，请刷新原进行投票！')</script>";
					echo "<meta http-equiv=\"Refresh\" content=\"0;url=../index.php\">";
				}else{
					echo "<script>alert('未知错误,请稍后再试！')</script>";
					echo "<meta http-equiv=\"Refresh\" content=\"0;url=../index.php\">";
					exit();
				}
			}else{
				echo "<script>alert('请求的链接非法！')</script>";
                		echo "<meta http-equiv=\"Refresh\" content=\"0;url=../index.php\">";
				exit();
			}
		}
	}
?>
