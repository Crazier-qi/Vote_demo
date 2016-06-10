<?php 
	include("sqlsafe.php");
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	include("conn.php");
	
	@session_start();

	$ss0 = file_get_contents("php://input");
	$ss = explode("&", $ss0);

	//判断是否登录
 	if( !isset($_SESSION['user']) || $_SESSION['user']!==true ){
		echo "<script>alert('请先登录！');</script>";
		echo "<meta http-equiv=\"Refresh\" content=\"0;url=user/login.html\">";
		exit();
	}

	//核对验证码
	if($_POST['code_num'] != $_SESSION['VCODE'] || $_POST['code_num']==''){
		echo "<script>alert('验证码错误');</script>";
		echo "<script>history.go(-1);</script>";	
		exit();	
	}

        //校验数据合法性与完整性
	$upids = array();
	$cids = array();
	foreach($ss as &$value){
		$kv = explode("=", $value);
		$upid = $kv[0]; //题目编号
		$cid = $kv[1]; //选项编号
		if($upid == "code_num"){
			break;		
		}
		//检查是否为数字
		if (is_numeric($cid) && is_numeric($upid)) {
		}else{
			echo "<script>alert('非法数据输入！');</script>";
			echo "<script>history.go(-1);</script>";        
              		exit();
		}
		array_push($upids, $upid);
		array_push($cids, $cid);
	}

	//检测选项个数
	$arr = array_count_values($upids);
	if($arr[17]>3){
		echo "<script>alert('特色网站最多选3个！');</script>";
		echo "<script>history.go(-1);</script>";
		exit();
	}
	if($arr[18]>11){
                echo "<script>alert('特色互联网应用最多选11个！');</script>";
                echo "<script>history.go(-1);</script>";
                exit();
	}

	//校验选项是否重复
	if (count($cids) != count(array_unique($cids))) {
		echo "<script>alert('非法数据输入！');</script>";
                echo "<script>history.go(-1);</script>";        
                exit();
	}
	//校验输入是否完整
        $sql = "select * from votename";
        $res = $db->query($sql);
	$upids = array_unique($upids);
        if(count($upids) != $res->num_rows){
                echo "<script>alert('请完善你的选择');</script>";
                echo "<script>history.go(-1);</script>";        
                exit();
        }

	//判断是否激活
	$sql = "select username from users where username='".$_SESSION['name']."' and isactive='1'";
	$res = $db->query($sql);
	if($res->num_rows != 1){
		echo "<script>alert('请先使用邮箱激活你的账户！');</script>";
		echo "<meta http-equiv=\"Refresh\" content=\"0;url=user/active.html\">";
                exit();
	}
	
	function voteing($ss, $db)
	{
		/************************************************************
		*功能：把投票信息一条条写进数据库
		*缺点：错误处理机制基本没有，对数据库读写太多，数据库开销大
		*************************************************************/		
		
		$success = true;
		foreach($ss as &$value){
			$va = explode("=", $value)[1];
			$result = $db->query("select votenum from voteoption where cid='".$va."';");
			$row = mysqli_fetch_assoc($result);
			$result = $db->query("update voteoption set votenum='".($row['votenum']+1)."' where cid='".$va."'");
			if(!$result){
				$success = false;
			}
		}
		if($success){
			foreach($ss as &$value){
				$kv = explode("=", $value);
				$key = $kv[0];	
				$value = $kv[1];	
				$result = $db->query("select sum(votenum) from voteoption where upid='".$key."';");
				$row = mysqli_fetch_assoc($result);
				$result = $db->query("update votename set sumvotenum='".$row['sum(votenum)']."' where cid='$key';");
				if(!$result){
					$success = false;
				}
			}
			if($success){
				return true;
			}
		}
		return false;
	}	
	
	
	$result = $db->query("select * from sysconfig");
	$row = mysqli_fetch_assoc($result);


	$now = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
	$dietimelist = explode("-",$row['dietime']);
	$dietime = mktime(0, 0, 0, $dietimelist[1]  , $dietimelist[2], $dietimelist[0]);
	$dietime = $dietime + 39600;

	if(round(($dietime-$now)/3600/24) < 0){
		echo "<script>alert('已经过了投票日期');</script>";
		echo "<meta http-equiv=\"Refresh\" content=\"0;url=index.php\">";
		exit();
	}
/*	
	if($row['method'] == 1){//ip统计投票
		$clientip = getenv("REMOTE_ADDR");
		$ips = $db->query("select ip from voteips where ip='$clientip';");
		if($ips->num_rows > 0){
			echo "<script>alert('你已经投过票了');</script>";
			echo "<meta http-equiv=\"Refresh\" content=\"0;url=index.php\">";
			exit();
		}else{
			voteing($ss, $db);
			$db->query("insert into voteips (ip) values ('$clientip');");
			echo "<script>alert('投票成功');</script>";
			echo "<meta http-equiv=\"Refresh\" content=\"0;url=index.php\">";	
			exit();
		}
			
	}else if($row['method'] == 2){//登录投票
*/
		if($_SESSION['user'] == true){
			$test = $db->query("select isvote,email from users where username='".$_SESSION['name']."';");
			$test_row = mysqli_fetch_assoc($test);
			if($test_row['isvote']==1){
				echo "<script>alert('你已经投过票了');</script>";
				echo "<meta http-equiv=\"Refresh\" content=\"0;url=index.php\">";
				exit();
			}else{
				$voteres = voteing($ss, $db);
				if($voteres){
					$db->query("update users set isvote='1' where username='".$_SESSION['name']."';");
					$username = $_SESSION['name'];
					$email = $test_row['email'];
					$votetime = date("Y-m-d H:i:s");
					$voteip = $_SERVER['REMOTE_ADDR'];
					$browser = $_SERVER['HTTP_USER_AGENT'];

					$sql = "insert into votelogs(username,email,voteop,votetime,voteip,browser) values('$username','$email','$ss0','$votetime','$voteip','$browser')";
					echo $db->query($sql);
					echo "<script>alert('投票成功,日志已记录！');</script>";
					echo "<meta http-equiv=\"Refresh\" content=\"0;url=result.php\">";
					exit();
				}else{
		
					//send email to administrator.

					echo "<script>alert('投票失败，请稍后再试！');</script>";
					exit();
				}
			}
		}else{
			echo "<script>alert('请登录再投票');</script>";
			echo "<script>history.go(-1);</script>";
			exit();
		}
		
//	} 
?>
