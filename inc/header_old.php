<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Offerz</title>

    <meta name="description" content="">
    <meta name="author" content="LayoutIt!">
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
	<style>
	.file-wrap{
  position: relative;
}
input#photoimg{
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
}
</style>
  </head>
  <body>
<!-----top-menu----->
<?php
require_once('configure.php'); ?>
<script src="<?php echo SITE_URL; ?>/js/jquery.min.js"></script>
<script src="<?php echo SITE_URL; ?>/js/validation.js"></script>	
<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="<?php echo SITE_URL; ?>/js/source/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/js/source/jquery.fancybox.css?v=2.1.5" media="screen" />
	
<div class="container-fluid top_bg">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-5 col-sm-4">
					<a href="#"><img alt="" class="img-responsive f_l" src="img/logo.png"></a>
				</div>
				<div class="col-md-7 col-sm-8 main_menu">
					<button class="navbar-toggle collapsed" data-target=".bs-navbar-collapse" data-toggle="collapse" type="button">
						<span id="t-button" class="glyphicon glyphicon-align-justify"></span>
					</button>
					<nav class="navbar-collapse bs-navbar-collapse collapse right_menu" role="navigation" style="height: 1px;">
						<ul class="nav navbar-nav">
					<?php 
					if(isset($_SESSION['user_id']) && $_SESSION['user_id'] !=''){ 
					$get_user_data = get_user_data($_SESSION['user_id']);
					
					?>
					
					
							<li><a class="r_brdr" href="<?php echo SITE_URL; ?>">HOME</a></li>
							<li><a class="r_brdr" href="profile.php">PROFILE</a></li>
							<li><a class="r_brdr" href="#teams">TEAMS</a></li>
							<li><a class="r_brdr" href="#offers">OFFERZ</a></li>
							<li><a class="r_brdr" href="#support">SUPPORT</a></li>
							<li><a href="process/logout.php">LOG OUT</a></li>
						
					<?php } else{ ?>
						<li><a class="r_brdr" href="login.php">LOGIN</a></li>
						<li><a  href="register.php">SIGN UP</a></li>
					<?php }?>
					</ul>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="resp_msg">
<?php 
// print_r($_SESSION); 
if(isset($_GET['msg']) ){
//print_r($_SESSION); 
$class_span = $_GET['msg'];

$msg = $_SESSION['flash_msg'];
if($msg!=""){
?>
<span class="<?php echo $class_span; ?>"><?php echo $msg; ?></span>
<?php 
}
//die('ee');
$_SESSION['flash_msg'] ="";
} ?>

</div>

<!----end-top-menu----->