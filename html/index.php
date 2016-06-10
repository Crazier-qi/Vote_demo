<?php 
	include("conn.php");
	
	@session_start();
	header("Cache-control:private");
	if( $_GET['do'] ){
		if($_GET['do']=="logout"){
			unset($_SESSION['user']);
			unset($_SESSION['name']);
			@session_destroy();
		}
	}
	$result = $db->query("select * from sysconfig");
	$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale=2.0,width=device-width" /> 
<title><?php echo $row['vote_name']; ?></title>

<script type="text/javascript" src="./user/js/jquery.min.js"></script>
<link rel="stylesheet" href="main.css" type="text/css" media="screen" />

</head>
<body>
<div class="main">
	<div style="width:auto; height:auto; background:#EFF4F7; border-bottom:solid #F0F0F0 1px; text-align:right; ">
		<div style=" padding:0.25em  0.5em 0.25em  0;">
		<?php if( !isset($_SESSION['user']) || $_SESSION['user']!==true ){ ?>
			<a href="result.php">投票结果&nbsp;&nbsp;</a>
			<a href="user/login.html">登录投票</a>
		<?php }else{ ?>
			<span>你好,<?php echo $_SESSION['name']; ?></span>
			<a href="result.php">投票结果&nbsp;&nbsp</a>
			<?php 
			$usernm = $_SESSION['name'];
			$res = $db->query("select * from users where username='$usernm' and isactive='1'");
			if($res->num_rows > 0){
				echo "<span>已激活</span>";
			}else{
				echo '<a href="user/active.html">邮箱激活&nbsp;&nbsp;</a>';
			} ?>
			<a href="index.php?do=logout">登出</a>
		<?php } ?>
		</div>
	</div>
	<div style="width:auto; height:auto;"><img src="./images/header2.png"></div>
	<form action="vote.php" method="post">
	<div class="content">
		<div class="votetime">
			<p>投票终止时间：<?php echo $row['dietime']?> 11:00&nbsp;&nbsp;投票用户：全校师生</p>
        	</div>
		<div class="description"><?php echo $row['description']; ?></div>
	</div>
		
		<?php
			$num = 0;
			$result_name = $db->query ( "select * from votename" );
			while ( $row_name = mysqli_fetch_assoc ( $result_name ) ) {
			$num += 1;
		?>
		<div class="mcontent">
			<h3><?php echo $num.".".$row_name['question_name']; ?></h3>
			<?php
				$result_option = $db->query ( "select * from voteoption where upid='" . $row_name ['cid'] . "';" );
				while ( $row_option = mysqli_fetch_assoc ( $result_option ) ) {
			?>
			<div class="obox">
				<?php 
					if($row_name['votetype']=="0"){
						echo '<input name="'.$row_name['cid'].'" type="radio" value="'.$row_option['cid'].'">'.$row_option['optionname'];
					}else if($row_name['votetype']=="1"){
						echo '<input name="'.$row_name['cid'].'" type="checkbox" value="'.$row_option['cid'].'">'.$row_option['optionname'];
					}
				?>
				
			</div>
			<?php } ?>
			<div style="clear:both;"></div>
		</div>
		<?php } ?>
		<?php if($result_name->num_rows > 0){
		?>


		<div class="votebu">
		<table style="min-width=50%"  border="1" cellspacing="0" cellpadding="0" bordercolor="#FFFFFF">
		<tr>
			<td><input name="code_num" type="text" style="width:60px; height:26px; float:left;" placeholder="验证码" maxlength="5" /></td>
		 	<td><div style="width:70px"><img style="height:30px;" onClick="this.src='img.php'" src="img.php"  alt="看不清，点击换一张"></div></td>
			<td><input class="btn btn-success" name="" type="submit" value="立即投票"><td>
		</tr>
		</table>
			<div style="clear:both;"></div>
		</div>
		<?php }else{ ?>
			<h1>当前没有投票</h1>
		<?php } ?>
		<br>
	</div>
  </form>
	<div class="foot" style="text-algin:center;">
	<p style="text-align: center;">Copyright &copy; 2015 西安邮电大学 信息中心
	<br>技术支持：智邮普创教育技术团队 <!--by <a href="http://www.s0nnet.com">s0nnet</a>--></p>
	</div>
</div>
</body>
</html>
