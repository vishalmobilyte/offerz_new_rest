<?php
$host = $_SERVER['HTTP_HOST'];
$root_path = $_SERVER['DOCUMENT_ROOT'];
//print_r($_SERVER); die;
	if($host == 'localhost' || $host == 'betasoftdev.com' ){
	$req_uri = explode('/',$_SERVER['REQUEST_URI']);
	$proj_folder_name = $req_uri[1];
	define('SITE_URL', "http://". $host."/".$proj_folder_name."/");
	define('DOCUMENT_ROOT', $root_path."/".$proj_folder_name);
	if( $host == 'betasoftdev.com'){
	define('DB_NAME', 'darren_offerz');
	define('DB_HOST', 'localhost');
	define('DB_USER', 'darren_offerz');
	define('DB_PASS', 'mind@123');
	
	}
	else{
	define('DB_NAME', 'darren_offerz');
	define('DB_HOST', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASS', '');
	}
	}
	
	else{
	define('SITE_URL', "http://". $host."/");
	define('DOCUMENT_ROOT', $root_path."/");
	
	define('DB_NAME', 'offerz_new');
	define('DB_HOST', 'localhost');
	define('DB_USER', 'offerz_darren');
	define('DB_PASS', 'mind@123');
	
	}
		$DbName      = DB_NAME ; 
		$DbHost      = DB_HOST;
		$DbUser      = DB_USER; 
		$DbPassword  = DB_PASS;
		
		// Create connection
	$conn = new mysqli($DbHost, $DbUser, $DbPassword,$DbName);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	//ECHO DOCUMENT_ROOT; DIE;
	require_once(DOCUMENT_ROOT.'/vendor_old/autoload.php');
	
	$stripe = array(
	  "secret_key"      => "sk_test_cmu0gkmiIWbUpW2ySzeO3GID",
	  "publishable_key" => "pk_test_bFubBv10bNTCUP6RYvzWryaW"
	);

	\Stripe\Stripe::setApiKey($stripe['secret_key']);
	?>