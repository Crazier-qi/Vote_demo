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
<title><?php echo $row['vote_name'];?></title>

<script type="text/javascript" src="./user/js/jquery.min.js"></script>
<link rel="stylesheet" href="main.css" type="text/css" media="screen" />

</head>
<body>
<div class="main">
	<div style="width:auto; height:auto; background:#EFF4F7; border-bottom:solid #F0F0F0 1px; text-align:right; ">
		<div style=" padding:0.25em  0.5em 0.25em  0;">
		<?php if( !isset($_SESSION['user']) || $_SESSION['user']!==true ){ ?>
			<a href="index.php">返回首页&nbsp;&nbsp;</a>
			<a href="user/login.html">登录投票</a>
		<?php }else{ ?>
			<span>你好,<?php echo $_SESSION['name']; ?></span>
			<a href="index.php">返回首页&nbsp;&nbsp;</a>
			<a href="index.php?do=logout">登出</a>
		<?php } ?>
		</div>
	</div>
	<div style="width:auto; height:auto;"><img src="./images/header2.png"></div>
	<form action="vote.php" method="post">
	<div class="content">
                <div class="votetime">投票终止时间：<?php echo $row['dietime']?> 11:00 &nbsp;&nbsp;投票用户：全校师生</div>
                        <div class="description">
                                <?php echo $row['description']; ?>
                        </div>
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
                $result_option = $db->query ( "select * from voteoption where upid='" . $row_name ['cid'] . "' order by votenum desc;" );
                $sumnum = $row_name['sumvotenum'];
                while ( $row_option = mysqli_fetch_assoc ( $result_option ) ) {
                ?>
                        <div class="controls" style=" float: left; width: auto; margin: 2px 0 0 2em; clear: both;">
                                <div style="width: 180px; float:left;"><?php echo $row_option['optionname']; ?></div>
                                <div style="float:left;">
                                        <div style="float:left; text-align:right; width:40px;"><?php echo $row_option['votenum'] ?>票</div>&nbsp;
                                        <img src="images/100.jpg" height="5" width="<?php echo $row_option['votenum']/$sumnum*100 ?>"/>
                                        <?php echo round($row_option['votenum']/$sumnum*100); ?>%
                                </div>
                        </div>
                        <?php } ?>

			</div>
			<?php } ?>
			<div style="clear:both;"></div>
		</div>
		<?php } ?>

	</div>
  </form>

        <div class="foot" style="text-algin:center;">
        <p style="text-align: center;">Copyright &copy; 2015 西安邮电大学 信息中心
        <br>技术支持：智邮普创教育技术团队 <!--by <a href="http://www.s0nnet.com">s0nnet</a>--></p>
        </div>
	
</div>
</body>
</html>
