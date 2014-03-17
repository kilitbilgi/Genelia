<?php
require_once('../../../wp-load.php');
$homepage = get_option('home');
$name="";$email="";$nameError="";$bool1="";$body="";$emailError="";$commentError="";$bool2="";$bool3="";$bool4="";$topic="";$emailTo="";$message="";$subject="";$error_message="";
if(isset($_POST['submit'])) {

	if(trim($_POST['name']) === '') {
		$nameError = 'Lütfen adınız giriniz.';
	} else {
		$name = trim($_POST['name']);
		$bool1 = 1;
	}

	if(trim($_POST['email']) === '')  {
		$emailError = 'Lütfen E-Posta Adresinizi Giriniz';
	} else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['email']))) {
		$emailError = 'Geçersiz E-Posta Adresi Girdiniz.';
	} else {
		$email = trim($_POST['email']);
		$bool2 = 1;
	}

	if(trim($_POST['message']) === '') {
		$commentError = 'Lütfen bir mesaj yazınınz.';
	} else {
		if(function_exists('stripslashes')) {
			$message = stripslashes(trim($_POST['message']));
			$bool3 = 1;
		} else {
			$message = trim($_POST['message']);
			$bool3 = 1;
		}
	}
		$topic = $_POST['type'];
		if(get_option('securemovie_contact_page_mail')==""){
		$emailTo = get_option('admin_email');
		}
		else{
		$emailTo = get_option('securemovie_contact_page_mail');
		}
		$subject = 'İletişim Formu Gelen Mesaj , Gönderen:'.$name;
		$body = "İsim: {$name} \n\nE-Posta Adresi: {$email} \n\n Konu:{$topic} \n\n Mesajı:{$message}";
		if($bool1==1 && $bool2==1 && $bool3==1)
		{
		mail($emailTo, $subject, $body);
		$error_message = "Mesaj Gönderildi";
		$bool4 = 1;
		}
		else{
		$error_message = "Gönderim hatası";
		$bool4 = 0;
		}
}
		else{
		header("Location: {$homepage}");
		}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="UTF-8"/>
	<title>İletişim</title>
	<script type="text/javascript">
	function goBack(){
	window.history.back()
	}
	<?php if($bool4==1){?>
	var count=3;
	var counter=setInterval(timer, 1000);
	function timer(){
  	count=count-1;
  	if (count < 0){
    clearInterval(counter);
    return;
  	}
  	document.getElementById("timer").innerHTML=count + " saniye içerisinde anasayfaya yönlendirileceksiniz.";
	}
	<?php }?>
    </script>
    <?php if($bool4==1){?>
    <meta http-equiv="refresh" content="3;URL='<?php echo $homepage;?>'"/>
    <?php }?>
</head>
<body>
<?php echo $error_message;?><br/>
<?php echo $nameError;?><br/>
<?php echo $emailError;?><br/>
<?php echo $commentError;?><br/>

<?php if($bool4==0){
echo "<input type='button' value='Sayfaya geri dön' onclick='goBack()'/>";
}
elseif($bool4==1){
echo "<span id='timer'></span>";
}?>
</body>
</html>