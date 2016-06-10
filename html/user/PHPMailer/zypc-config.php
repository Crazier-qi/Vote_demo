<?php
	//发送邮件

	require 'PHPMailerAutoload.php';
	$mail = new PHPMailer(); //实例化 
	$mail->IsSMTP(); // 启用SMTP 
	$mail->SMTPSecure = "ssl";	
	$mail->Host = "smtp.exmail.qq.com"; //SMTP服务器

	$mail->Port = 465;  //邮件发送端口 
	$mail->SMTPAuth   = true;  //启用SMTP认证 

	$mail->CharSet  = "UTF-8"; //字符集 
	$mail->Encoding = "base64"; //编码方式 

	$choice = rand(1,3);
	switch($choice){
		case 1: $mail->Username = "admin@smartxupt.com"; $mail->Password = "XYvote8";break;
		case 2: $mail->Username = "root@smartxupt.com"; $mail->Password = "XYvote9";break;
		case 3: $mail->Username = "vote@smartxupt.com"; $mail->Password = "XYetov10";break;
		default: break;
	}

	$mail->Subject = "西安邮电大学网站和互联网应用投票"; //邮件标题 

	$mail->From = $mail->Username;  //发件人地址（也就是你的邮箱） 

	$mail->FromName = "投票平台管理员";  //发件人姓名 

	$mail->AddAddress($email, "西邮学生");//添加收件人（地址，昵称）

        $mail->IsHTML(true); //支持html格式内容
        $mail->Body = '用户'.$username.',你好！ <p>&nbsp;&nbsp;&nbsp;&nbsp;你收到的这封邮件是来自西安邮电大学2015年度网站和互联网应用评比投票平台（http://vote.xupt.edu.cn）。如非你本人操作，请忽略该邮件，或者回复本邮件进行问题反馈。</p><p>&nbsp;&nbsp;&nbsp;&nbsp;请点击以下链接激活你的账户进行投票：http://vote.xupt.edu.cn/user/active.php?user='.$username.'&amp;active='.$keystring.'<br>(如果不能点击该链接地址，请复制并粘贴到浏览器的地址输入框)</p>';

?>
