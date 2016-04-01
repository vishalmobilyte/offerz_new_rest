<?php
header("Connection: Keep-Alive"); 
header("Keep-Alive: timeout=300");
header("Keep-Alive: max=100");
include('../inc/db_connection.php');
include('../inc/functions.php');
//die('ddd');
require '../vendor/autoload.php';
// -------- TWITTER NAMESPACES ------------
use Abraham\TwitterOAuth\TwitterOAuth;
	
// ---------- FACEBOOK NAMESPACES  --------
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;


	
$WS_obj = new Offerz_web_services();

class Offerz_web_services
{
	protected $params;
	protected $json_response = array();
	protected $ws_response = array();
	// ---- FB DETAILS ------
	public $app_id;
	public $app_secret;
	public $default_graph_version;
	// -------------------------
	
	public function __construct()
	{
		
		
		$handle = fopen ( 'php://input', 'r' );
		$jsonInput = fgets ( $handle );
		$this->params = json_decode( $jsonInput );
	//	print_r($this->params); die('==');
		$this->app_id = "1664076740532832";
		$this->app_secret = "a180fa4be0822cce909ecf69d1eb23e8";
		$this->default_graph_version = "v2.5"; // For Offerz-develop app
		
		$this->process();
		
		
	
	}
	
	private function process()
	{
		
		if( $this->params->method == "register_user" )
		{
			$result = $this->register_user();
		}
			
		else if( $this->params->method == "login_user" )
		{
			$result = $this->login_user();
		}
		
		else if( $this->params->method == "logout_user" )
		{
			$result = $this->logout_user();
		}
		
		else if( $this->params->method == "connect_user_fb" )
		{
			$result = $this->connect_user_fb();
		}
		
		else if( $this->params->method == "connect_user_instagram" )
		{
			$result = $this->connect_user_instagram();
		}
		
		else if( $this->params->method == "update_user_profile" )
		{
			$result = $this->update_user_profile();
		}
    
		else if( $this->params->method == "get_requests" )
		{
			$result = $this->get_requests();
		}
		
		else if( $this->params->method == "user_stats" )
		{
			$result = $this->user_stats();
		}
		else if( $this->params->method == "user_teams_joined" )
		{
			$result = $this->user_teams_joined();
		}
		
		else if( $this->params->method == "list_user_query" )
		{
			$result = $this->list_user_query();
		}
		
		else if( $this->params->method == "get_offers" )
		{
			$result = $this->get_offers();
		}
		
		else if( $this->params->method == "forgot_password_user" )
		{
			$result = $this->forgot_password_user();
		}
		
		else if( $this->params->method == "submit_user_query" )
		{
			$result = $this->submit_user_query();
		}
		
		else if( $this->params->method == "get_request_response" )
		{
			$result = $this->get_request_response();
		}
		
		else if( $this->params->method == "update_user_offer" )
		{
			$result = $this->update_user_offer();
		}
    
		echo json_encode($this->ws_response);
	}
	
	private function success_failure_msgs( $code, $message, $result = array())
	{
		$currentDateTime = new \DateTime();
		if($code == 200)
		{
			$this->ws_response = array("Response"=>array("Code"=>$code,"Status"=>"OK","message"=>$message,"result"=>$result, "CurrentDateTime"=>$currentDateTime));
		}
		else
		{
			$this->ws_response = array("Response"=>array("Code"=>$code,"Status"=>"Error","message"=>$message));
		}
		return $this->ws_response;
	}	
	
	/* METHOD: register_user */
	private function register_user(){
	global $conn;
	
	if( $this->params->name && $this->params->password && $this->params->email)
		{
		$device_token = @$this->params->device_token;
		$name = 	$this->params->name;
		$email = $this->params->email;
		$password = $this->params->password;
		$oauth_token = @$this->params->oauth_token;
		$oauth_secret_token = @$this->params->oauth_secret_token;
		$screen_name = @$this->params->screen_name;
		$twitter_id = @$this->params->twitter_id;
		// check if email already exisits or not in db
		$sql_chk_email = "SELECT * from users WHERE email='$email'";
		//die;
		$result_chk_email=mysqli_query($conn,$sql_chk_email);
		$row=mysqli_fetch_array($result_chk_email,MYSQLI_ASSOC);
		
		$count=mysqli_num_rows($result_chk_email);
		//echo $count; die("--");
		if($count < 1){
		$get_data_twt = $this->get_twitter_all_data($screen_name);
	//	print_r($get_data_twt); die("--heee");
		$twt_followers = @$get_data_twt->user->followers_count;
		//$twt_pic = str_replace("_normal","",$get_data_twt->user->profile_image_url);
		$twt_pic = @$get_data_twt->user->profile_image_url;
		//print_r($get_data_twt); die('--eee');
		$sql=	"INSERT INTO users SET 
		name='$name', 
		device_token='$device_token', 
		email='$email',
		oauth_token='$oauth_token',
		oauth_secret_token='$oauth_secret_token',
		screen_name='$screen_name',
		twitter_id='$twitter_id',
		twt_followers='$twt_followers',
		twt_pic='$twt_pic',
		password='$password'"; 
		
		$result=mysqli_query($conn,$sql);
		
			if($result)
			{
				
				$ret_array['success']='1';
				$ret_array['message']='User Registered Successfully';
				$ret_array['user_id']=$conn->insert_id;
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "User Registered Success.", $this->json_response);
			}
			else
			{
				$msg = "Failed To Insert User!";
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}
		else{
		$msg = "Email id already exisits.";
		$this->json_response = "";
		$this->success_failure_msgs(200, $msg, $this->json_response);
		}
			
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
		/* METHOD: update_user_profiles */
	private function update_user_profile(){
	global $conn;
	// print_r($this->params); die;
	if( $this->params->name && $this->params->email && $this->params->user_id )
		{
		
		$name = $this->params->name;
		$email = $this->params->email;
		$user_id = $this->params->user_id;
		$password = @$this->params->password;
		$oauth_token = @$this->params->oauth_token;
		$oauth_secret_token = @$this->params->oauth_secret_token;
		$screen_name = @$this->params->screen_name;
		$twitter_id = @$this->params->twitter_id;
		
		// Facebook data 
		$fb_id = @$this->params->fb_id;
		$fb_token = @$this->params->fb_token;
		//Instgram data
		$instag_id = @$this->params->instag_id;
		$instag_token = @$this->params->instag_token;
		
		// check if email already exisits or not in db
		$sql_chk_email = "SELECT * from users WHERE email='$email' AND  id != $user_id";
		 
		//die;
		$result_chk_email=mysqli_query($conn,$sql_chk_email);
		$row=mysqli_fetch_array($result_chk_email,MYSQLI_ASSOC);
		//print_r($row); die;
		$extra_qry = '';
		if($password!=''){
		$extra_qry = "password='$password',";
		}
		if($oauth_token!='' && $oauth_secret_token!='' && $screen_name!='' && $twitter_id!='' ){
		$get_data_twt = $this->get_twitter_all_data($screen_name);
		$twt_followers = @$get_data_twt->user->followers_count;
		//$twt_pic = str_replace("_normal","",$get_data_twt->user->profile_image_url);
		$twt_pic = @$get_data_twt->user->profile_image_url;
		
		$extra_qry .= "oauth_token='$oauth_token',
		oauth_secret_token='$oauth_secret_token',
		screen_name='$screen_name',
		twitter_id='$twitter_id',
		twt_followers='$twt_followers',
		twt_pic='$twt_pic',";
		
		
		}
		else{
		$extra_qry .= "oauth_token='',
		oauth_secret_token='',
		screen_name='',
		twitter_id='',
		twt_followers='',
		twt_pic='',";
		
		
		}
		
		if($fb_id!='' && $fb_token!='' ){
		$extra_qry .="fb_token='$fb_token', 
		
		fb_id='$fb_id', ";
		}
		else{
		$extra_qry .="fb_token='', 
		
		fb_id='', ";
		}
		
		if($instag_id!='' && $instag_token!='' ){
		$extra_qry .="instag_token='$instag_token', 
		
		instag_id='$instag_id', ";
		}
		else{
		$extra_qry .="instag_token='', 
		
		instag_id='', ";
		}
		
		$count=mysqli_num_rows($result_chk_email);
		//echo $count; die("--");
		if($count < 1){

		$sql=	"UPDATE users SET 
		".$extra_qry."
		name='$name', 
		
		email='$email'
		
		WHERE id='$user_id'";
		
		$result=mysqli_query($conn,$sql);
		
			if($result)
			{
				$ret_array['success']='1';
				$ret_array['message']='User Updated Successfully';
				$ret_array['user_id']=$user_id;
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "User Update Success.", $this->json_response);
			}
			else
			{
				$msg = "Failed To Update User!";
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}
		else{
		$msg = "Email id already exisits.";
		$this->json_response = "";
		$this->success_failure_msgs(200, $msg, $this->json_response);
		}
			
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	
	// ========================== LOGIN USER ============================
	
	private function login_user(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->email && $this->params->password )
		{
		$email = 	$this->params->email;
		$password =$this->params->password;
		$device_token =$this->params->device_token;
	
		$sql="SELECT * FROM users WHERE email='$email' and password='$password'";
		$result=mysqli_query($conn,$sql);
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		
	 	$count=mysqli_num_rows($result);

			if($count>0)
			{
				$sql_upd="UPDATE users SET 
	
				device_token='$device_token'
				
				WHERE email='$email'";
				$result_upd=mysqli_query($conn,$sql_upd);
				
				$ret_array['success']='1';
				$ret_array['data']=$row;
				$ret_array['message']='Login User Successfully';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Login User Successfully", $this->json_response);
			}
			else
			{
				$msg = "Invalid Email or Password!";
				$ret_array['success']='0';
				$ret_array['message']='Invalid Email or Password!';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(301, $msg, $this->json_response);
			}
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	// ========================== LOGOUT USER ============================
	
	private function logout_user(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->user_id )
		{
		$user_id = 	$this->params->user_id;
		
	
	
			if($user_id>0)
			{
				$sql_upd="UPDATE users SET 
	
				device_token='',
				is_logged_in='0'
				
				
				WHERE id='$user_id'";
				$result_upd=mysqli_query($conn,$sql_upd);
				
				$ret_array['success']='1';
				$ret_array['data']='Logged Out Successfully';
				$ret_array['message']='Logged Out Successfully';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Logged Out Successfully", $this->json_response);
			}
			else
			{
				$msg = "Invalid User Id!";
				$ret_array['success']='0';
				$ret_array['message']='Invalid User Id';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(301, $msg, $this->json_response);
			}
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	// =========== UPDATE FACEBOOK CONNECT OF MOBILE USER ==============================
	private function connect_user_fb(){
	global $conn;
	// print_r($this->params); die;
	if( $this->params->fb_token && $this->params->fb_id && $this->params->user_id )
	{
	
		$user_id = $this->params->user_id;
		$fb_id = $this->params->fb_id;
		$fb_token = @$this->params->fb_token;
		
		// check if email already exisits or not in db
		$sql_chk_email = "SELECT id from users WHERE id='$user_id'";
		 
		//die;
		$result_chk_email=mysqli_query($conn,$sql_chk_email);
		$row=mysqli_fetch_array($result_chk_email,MYSQLI_ASSOC);
	
		$count=mysqli_num_rows($result_chk_email);
		//echo $count; die("--");
		if($count >0){

		
		$get_fb_data = $this->getFbData($fb_token);
		$sql=	"UPDATE users SET 
		
		fb_token='$fb_token', 
		fb_friends='$get_fb_data', 
		
		fb_id='$fb_id'
		WHERE id='$user_id'";
		
		$result=mysqli_query($conn,$sql);
			if($result)
			{

				$ret_array['success']='1';
				$ret_array['message']='Facebook data Updated Successfully';
				$ret_array['user_id']=$user_id;
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "User Update Success.", $this->json_response);
			}
			else
			{
				$msg = "Failed To Update User!";
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}
		else{
		$msg = "Email id already exisits.";
		$this->json_response = "";
		$this->success_failure_msgs(200, $msg, $this->json_response);
		}
			
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
	}
	// =========== UPDATE INSTAGRAM CONNECT OF MOBILE USER =============================
	
	private function connect_user_instagram(){
	global $conn;
	// print_r($this->params); die;
	if( $this->params->instag_token && $this->params->instag_id && $this->params->user_id )
	{
	
		$user_id = $this->params->user_id;
		$instag_id = $this->params->instag_id;
		$instag_token = @$this->params->instag_token;
		
		// check if email already exisits or not in db
		$sql_chk_email = "SELECT id from users WHERE id='$user_id'";
		 
		//die;
		$result_chk_email=mysqli_query($conn,$sql_chk_email);
		$row=mysqli_fetch_array($result_chk_email,MYSQLI_ASSOC);
	
		$count=mysqli_num_rows($result_chk_email);
		//echo $count; die("--");
		if($count >0){

		$sql=	"UPDATE users SET 
		
		instag_token='$instag_token', 
		
		instag_id='$instag_id'
		WHERE id='$user_id'";
		
		$result=mysqli_query($conn,$sql);
		
			if($result)
			{
				$ret_array['success']='1';
				$ret_array['message']='Instagram data Updated Successfully';
				$ret_array['user_id']=$user_id;
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "User Update Success.", $this->json_response);
			}
			else
			{
				$msg = "Failed To Update User!";
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}
		else{
		$msg = "Email id already exisits.";
		$this->json_response = "";
		$this->success_failure_msgs(200, $msg, $this->json_response);
		}
			
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
	}
	
	// ========================== GET INVITES REQUESTS USER ============================
	
	private function get_requests(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->email )
		{
		$email = 	$this->params->email;
		
		$limit = 	@$this->params->limit;
		$offset = 	@$this->params->offset;
		if($limit !='' && $offset !=''){
		$limit_condition = 'LIMIT '.$offset.','.$limit;
		}
		else{
		$limit_condition = '';
		}
		
		$sql="SELECT  I.*, C.screen_name,C.email, C.username, C.name as sponsor_name 
						FROM invites I JOIN clients C ON I.client_id=C.id
						WHERE I.email='$email' AND I.is_accepted=0 AND I.is_deleted=0 $limit_condition";
		//die;
		$result=mysqli_query($conn,$sql);
		//$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		
		// Find total records
		$actual_row_count = mysqli_query($conn,"Select Found_Rows() as total_records");
		$sql_total_rows = mysqli_fetch_object($actual_row_count);
		$total_records =$sql_total_rows->total_records;
		
	 	$count=mysqli_num_rows($result);
		$data_rows = array();
		while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
		$data_rows[] = $row;
		}

			if($count>0)
			{
			
				$ret_array['success']='1';
				$ret_array['total_records']=$total_records;
				//$ret_array['id']=$row['id'];
				$ret_array['data']=$data_rows;
				$ret_array['message']='Team Requests got Successfully';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Team Requests got Successfully", $this->json_response);
			}
			else
			{
				$msg = "No Request Found";
				$ret_array['success']='0';
				$ret_array['message']='No Record found.';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}
			
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	// ===================== GET RESPONSE FROM USER ======================
	private function get_request_response(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->email && $this->params->client_id && $this->params->is_accepted )
		{
		$email = 	$this->params->email;
		$client_id = 	$this->params->client_id;
		$is_accepted = 	$this->params->is_accepted;
		$result = update_invites($is_accepted, $email, $client_id);
		
			if($result)
			{
			//update_invites($is_accepted, $email, $client_id);
			// update RECENT ACTIVITY
			update_req_activity($is_accepted,$email, $client_id);
				$ret_array['success']='1';
				$ret_array['message']='Updated Successfully';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Invite Requests got Successfully", $this->json_response);
			}
			else
			{
				$msg = "There was an error while updating request response.";
				$ret_array['success']='0';
				$ret_array['message']='Error in Executing Query.';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(301, $msg, $this->json_response);
			}
		}
			
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}	
	}
	
	// ========================== GET OFFERZ NEW OFFERS AND SHARED OFFERS ============================
	
	private function get_offers(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->user_id )
		{
		$user_id = 	$this->params->user_id;
		$limit = 	@$this->params->limit;
		$offset = 	@$this->params->offset;
		if($limit !='' && $offset !=''){
		$limit_condition = 'LIMIT '.$offset.','.$limit;
		}
		else{
		$limit_condition = '';
		}
		$images_path = SITE_URL.'/uploads/offers_images/';
		
		$sql="SELECT SQL_CALC_FOUND_ROWS DATE_FORMAT(UO.created_at,'%b %d') AS offer_received_time, 
		(CASE
		WHEN (UO.status = 0) THEN 'New' 
		WHEN (UO.status = 1) THEN 'Shared' 
		WHEN (UO.status = 2) THEN 'Declined' 
		ELSE 'Shared' 
		 END)
 
		as offer_status, UO.id as user_offer_id, O.client_id as client_id, O.editable_text,
		CONCAT('".$images_path."',O.image_name) 
		
		AS offer_image_path, 
		O.not_editable_text, O.start_date as when_to_send, O.date_send_on as offer_start_date,O.title as offer_title,
		CL.name as sponsor_name
		FROM user_offers UO 
		
		LEFT JOIN offers O ON UO.offer_id = O.id  
		
		LEFT JOIN users U ON UO.user_id = U.id 

		LEFT JOIN clients CL ON O.client_id = CL.id 
		
		WHERE UO.user_id = $user_id AND O.is_deleted = 0 AND O.is_paused !=1 AND O.date_send_on <= CURDATE() ORDER BY UO.created_at DESC $limit_condition";
		
		
	//	echo $sql; die;
		$result=mysqli_query($conn,$sql);
		
		// Find total records
		$actual_row_count = mysqli_query($conn,"Select Found_Rows() as total_records");
		$sql_total_rows = mysqli_fetch_object($actual_row_count);
		$total_records =$sql_total_rows->total_records;
		
	 	$count=mysqli_num_rows($result);
		$data_rows = array();
		while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
		if($row['offer_image_path'] != ''){
		//$img_field = SITE_URL.'/timthumb.php?src='.$row['offer_image_path'].'&w=350&h220';
		$img_field = $row['offer_image_path'];
		}
		else{
		$img_field = '';
		}
		$row['offer_image_path'] = $img_field;
		$data_rows[] = $row;
		}

			if($count>0)
			{
			
				$ret_array['success']='1';
				$ret_array['total_records']=$total_records;
				//$ret_array['id']=$row['id'];
				$ret_array['data']=$data_rows;
				$ret_array['message']='Offers got Successfully';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Team Requests got Successfully", $this->json_response);
			}
			else
			{
				$msg = "There Is No Offer Yet!";
				$ret_array['success']='0';
				$ret_array['message']='No Record found.';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}
			
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	private function update_user_offer(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->user_id && $this->params->user_offer_id && $this->params->status && $this->params->client_id && $this->params->shared_via)
		{
		$user_id = 	$this->params->user_id;
		$user_offer_id = 	$this->params->user_offer_id;
		$status = 	$this->params->status;
		$client_id = 	$this->params->client_id;
		$shared_via = 	$this->params->shared_via;
		$post_id_fb = 	@$this->params->post_id_fb;
		$twt_id = 	@$this->params->twt_id;
		$instag_id = 	@$this->params->instag_id;
		$screen_name = 	@$this->params->screen_name;
		// 0 = Not shared, 1 = Shared, 2 = Declined
		if($status == '0' || $status == '1' || $status == '2'){
		
		// ----------- GET SHARED TWEET ID -----------------------------
			if($shared_via == 'TWITTER'){
			$get_twitter_data = @$this->get_twitter_all_data($screen_name);
			//print_r($get_twitter_data->id);
			$twt_id = @$get_twitter_data->id;
			}
		//die('----------');
		$sql="UPDATE user_offers 
					SET 
					status = $status,
					shared_via = '$shared_via',
					post_id_fb = '$post_id_fb',
					twt_id = '$twt_id',
					instag_id = '$instag_id'
					
					
					WHERE user_id=$user_id AND id= $user_offer_id";

		//$sql; die;
		$result=mysqli_query($conn,$sql);

			if($result)
			{
			$date = date('d-m-Y');
			if($status =='1'){
			$sql_upd ="UPDATE offers_stat SET 
			
			offer_accepted = offer_accepted+1,
			last_offer_date = '$date'
			

			WHERE user_id=$user_id AND client_id= $client_id";
			}
			else{
			$sql_upd ="UPDATE offers_stat SET 
			
			offer_declined = offer_declined+1

			WHERE user_id=$user_id AND client_id= $client_id";
			}
			$result_upd=mysqli_query($conn,$sql_upd);
			// UPdate REcent Activity Log Now
			
			update_offer_activity($user_id, $user_offer_id, $status);
			
			
			
				$ret_array['success']='1';
				$ret_array['message']='Offer Updated Successfully';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Offer Updated Successfully", $this->json_response);
			}
			else
			{
				$msg = "No Record";
				$ret_array['success']='0';
				$ret_array['message']='No Record found.';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}
		else{
				$msg = "Invalid Status Parameter";
				$ret_array['success']='0';
				$ret_array['message']='Invalid Status Parameter';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(301, $msg, $this->json_response);
		}
		}
			
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	// ============= FORGOT Password ======================
	
	private function forgot_password_user(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->email )
		{
		$email = 	$this->params->email;
	
		$sql="SELECT * FROM users WHERE email='$email'";
		$result=mysqli_query($conn,$sql);
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		
	 	$count=mysqli_num_rows($result);

			if($count>0)
			{
				$id = $row['id'];
				$ret_array['success']='1';
				//$ret_array['data']=$row;
				sendEmailResetPass($email,$id);
				$ret_array['message']='Email sent for reset password';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Email sent for reset password", $this->json_response);
			}
			else
			{
				$msg = "Email Not Found!";
				$ret_array['success']='0';
				$ret_array['message']='Email Not Found!';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	private function user_stats(){
	global $conn;
	// echo $this->params->email; die("--3333");
	
	if( $this->params->user_id)
		{
		
		$user_id = 	$this->params->user_id;
		$email_user = getEmailById($user_id);
		$screen_name = getTwtScreenName($user_id);
		if(!empty($screen_name)){
		$sql_impression = "SELECT twt_followers, fb_friends FROM users WHERE id=$user_id LIMIT 1";
		$result_impression=mysqli_query($conn,$sql_impression);
		$row_impression=mysqli_fetch_array($result_impression,MYSQLI_ASSOC);
		$twt_followers = $row_impression['twt_followers'];
		$fb_friends = $row_impression['fb_friends'];
		
		$total_impressions = array_sum(array($twt_followers,$fb_friends));
		//$total_impressions = $this->get_twitter_data($screen_name);
		}
		else{
		$total_impressions ="NA";
		}
		
		
		//$sql2 = "SELECT COUNT(*) as total_teams FROM invites WHERE email='$email_user' AND is_accepted=1";
		$sql2="SELECT  COUNT(*) as total_sponsors 
						FROM invites I JOIN clients C ON I.client_id=C.id
						WHERE I.email='$email_user' AND I.is_accepted=1 AND I.is_deleted=0";
						
		$result2=mysqli_query($conn,$sql2);
		$row2=mysqli_fetch_array($result2,MYSQLI_ASSOC);
		$total_sponsors_joined = $row2['total_sponsors'];
		
		$sql = "SELECT (CASE
		WHEN (status = 0) THEN 'New' 
		WHEN (status = 1) THEN 'Shared' 
		WHEN (status = 2) THEN 'Declined' 
		ELSE 'Shared' 
		 END) as status, COUNT(*) as count_status  FROM   user_offers WHERE user_id = ".$user_id." GROUP BY status";
		
		
		//echo $sql; die;
		$result=mysqli_query($conn,$sql);
		$total_shared ='0';
		$total_declined ='0';
		$total_new ='0';
		$total_received ='0';
		
		while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
		
		if($row['status'] == 'Shared'){
		//$total_impressions = getTotalImpressions($screen_name);
		
		$total_shared = $row['count_status'];
		}
		elseif($row['status'] == 'Declined'){
		
		$total_declined = $row['count_status'];
		}
		
		elseif($row['status'] == 'New'){
		
		$total_new = $row['count_status'];
		}
		else{
		
		}
		}
		$total_received_offer = $total_shared+$total_declined+$total_new;
	 	//$count=mysqli_num_rows($result);
		
			if($result)
			{
				$ret_array['success']='1';
				$ret_array['message']='User\'s';
				$ret_array['total_shared']=$total_shared;
				$ret_array['total_received']=$total_received_offer;
				$ret_array['total_impressions']=$total_impressions;
				$ret_array['total_sponsors_joined']=$total_sponsors_joined;
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "User Stats", $this->json_response);
			}
			else
			{
				$msg = "No Record";
				$ret_array['success']='0';
				$ret_array['message']='No Record found.';
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		
		}
			
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	// ============= SUBMIT USER QUERY ======================
	
	private function submit_user_query(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->user_id && $this->params->help_content )
		{
		$user_id = 	$this->params->user_id;
		$help_content = 	$this->params->help_content;
		
		$sql="INSERT INTO users_queries
						SET content_query='$help_content',
							user_id='$user_id'";
		$result=mysqli_query($conn,$sql);
		
			if($result)
			{
			
				$ret_array['success']='1';
				//$ret_array['data']=$row;
				
				$ret_array['message']='Message sent successfully.';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Message sent successfully", $this->json_response);
			}
			else
			{
				$msg = "Message Not Sent Please try again later.";
				$ret_array['success']='0';
				$ret_array['message']=$msg;
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				$this->success_failure_msgs(301, $msg, $this->json_response);
			}
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	// ================ LISTING THE QUERIES FOR MOBILE USERS HELP MESSAGES ================
	
	private function list_user_query(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->user_id )
		{
		$user_id = 	$this->params->user_id;
		
		$limit = 	@$this->params->limit;
		$offset = 	@$this->params->offset;
		if($limit !='' && $offset !=''){
		$limit_condition = 'LIMIT '.$offset.','.$limit;
		}
		else{
		$limit_condition = '';
		}
			$sql="SELECT  SQL_CALC_FOUND_ROWS * , DATE_FORMAT(created_at,'%l:%i %p') AS user_query_time FROM users_queries
						WHERE user_id='$user_id' $limit_condition";
		//echo $sql; die;
		$result=mysqli_query($conn,$sql);
		//$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		
		// Find total records
		$actual_row_count = mysqli_query($conn,"Select Found_Rows() as total_records");
		$sql_total_rows = mysqli_fetch_object($actual_row_count);
		$total_records =$sql_total_rows->total_records;
		
	 	$count=mysqli_num_rows($result);
		$data_rows = array();
		while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
		//array_push($row,'posted_by'=>'user');
		$row['posted_by']='user';
		$data_rows[] = $row;
		if($row['response_content'] !=''){
		$row['posted_by']='admin';
		$data_rows[] = $row;
		}
		}

			if($count>0)
			{
			
				$ret_array['success']='1';
				$ret_array['total_records']=$total_records;
				$ret_array['data']=$data_rows;
				$ret_array['total_msg']=$count;
				
				$ret_array['message']='Messages fetched successfully';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Messages fetched successfully", $this->json_response);
			}
			else
			{
				$msg = "No Record found.";
				$ret_array['success']='0';
				$ret_array['message']=$msg;
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	// ================ GET ALL TEAMS of SPONSOR JOINED BY THE MOBILE USERS ================
	
	private function user_teams_joined(){
	global $conn;
	// echo $this->params->email; die("--3333");
	if( $this->params->email)
		{
		$email = 	$this->params->email;
	
		$sql="SELECT DISTINCT  I.*, C.screen_name, C.name as sponsor_name 
						FROM invites I JOIN clients C ON I.client_id=C.id
						WHERE I.email='$email' AND I.is_accepted=1 AND is_deleted=0";
		//echo $sql; die;
		$result=mysqli_query($conn,$sql);
		//$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		
	 	$count=mysqli_num_rows($result);
		$data_rows = array();
		while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
		$data_rows[] = $row;
		}

			if($count>0)
			{
			
				$ret_array['success']='1';
				$ret_array['data']=$data_rows;
				$ret_array['total_msg']=$count;
				
				$ret_array['message']='Teams fetched successfully';
				array_push($this->json_response,$ret_array);
				$this->success_failure_msgs(200, "Teams fetched successfully", $this->json_response);
			}
			else
			{
				$msg = "No Record found.";
				$ret_array['success']='0';
				$ret_array['message']=$msg;
				array_push($this->json_response,$ret_array);
				//echo("Validation errors:<br/>");
				$this->success_failure_msgs(200, $msg, $this->json_response);
			}
		}	
		else
		{
		$msg = "Required Parameters Are Missing.";
		$this->json_response = "";
		$this->success_failure_msgs(301, $msg, $this->json_response);
		}
			
	}
	
	
	function get_twitter_data($screen_name){
	$oauth_access_token = '';
	$oauth_access_token_secret = '';
	$consumer_key = "LEqoRF6gLyLPxIFlGDjze5xd0";
	$consumer_secret = "c0B582T95BFWUUzR2UnOFqWb2RaDQpQ1BH7qPC0aD7w1cf6hVR";
	//$connection_tw = new TwitterOAuth($consumer_key, $consumer_secret,$oauth_access_token , $oauth_access_token_secret );
	//var_dump($connection_tw); die;
	$connection_tw = new TwitterOAuth($consumer_key, $consumer_secret );
	
	$tweets = $connection_tw->get("statuses/user_timeline",array("screen_name"=>$screen_name,"count"=>1));
	//print_r($tweets[0]); die;
	$followers_count = @$tweets[0]->user->followers_count;
	return $followers_count; 
	
	}
	
	function get_twitter_all_data($screen_name){
	$oauth_access_token = '';
	$oauth_access_token_secret = '';
	$consumer_key = "LEqoRF6gLyLPxIFlGDjze5xd0";
	$consumer_secret = "c0B582T95BFWUUzR2UnOFqWb2RaDQpQ1BH7qPC0aD7w1cf6hVR";
	//$connection_tw = new TwitterOAuth($consumer_key, $consumer_secret,$oauth_access_token , $oauth_access_token_secret );
	//var_dump($connection_tw); die;
	$connection_tw = new TwitterOAuth($consumer_key, $consumer_secret );
	
	
	$tweets = $connection_tw->get("statuses/user_timeline",array("screen_name"=>$screen_name,"count"=>1));
	// print_r($tweets[0]); die;
	$ret_data = $tweets[0];
	//$followers_count = @$tweets[0]->user->followers_count;
	return $ret_data; 
	
	}
	// ========= FACEBOOK CONNECTION =======
	public function getFacebookConn(){
	$app_idd = $this->app_id;
	$app_secrett = $this->app_secret;
	$fb = new \Facebook\Facebook([
		'app_id' => $app_idd,
		'app_secret' => $app_secrett,
		'default_graph_version' => 'v2.5',
	]);
	return $fb;
		
	}
	
	public function getFbData($fb_token){
	$fb = $this->getFacebookConn();

	$resp = $fb->get('/me/friends', $fb_token);
//	$response = $fb->get('/'.$fb_id.'?fields=id,name', $asscee_t_2);
	//$graphNode = $resp->getGraphEdge();
	$get_data = @$resp->getDecodedBody();
	//print_r($get_data);
	return @$get_data['summary']['total_count']; 
	
	}
	

}

?>