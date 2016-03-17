<div class="container-fluid">
	<div class="container" id="support">
		<div class="row">
			<div class="col-md-12">
				<h1>Offerz Support</h1>
			</div>
			<div class="col-md-12">
			<form id="query_form" >
			<div id="response_submit_query"></div>
				<div class="col-md-6 col-sm-6">
					<div class="col-md-4 col-sm-4">
						<img alt="" class="img-responsive f_l" src="img/question.png">
					</div>
					<div class="col-md-8 col-sm-8">
						<p class="offerz_support">Questions? Need to contact us? Fill out the field to the right to send us a message. Our team will respond in 24 hours.</p>
					</div>
				</div>
				<div class="col-md-6 col-sm-6">
					<textarea class="form-control custom-control" name="content_query" id="content_query" rows="5" placeholder="Type your question here..."></textarea>  
						<a href="javascript:void(0);" class="create_new" onclick="submit_form_query(this);">SUBMIT</a>
				</div>
			</form>
			</div>
			
		</div>
		<div class="row text_border">
		<?php 
				
					$get_all_client_queries = get_all_client_queries($client_id);
					// print_r($get_all_offers); die;
					foreach($get_all_client_queries as $query_data){ 
					$content_query = $query_data['content_query'];
					$date_query = $query_data['created_at'];
					$date_to_show =  date("jS F, Y", strtotime($date_query));
					if($query_data['response_content']==''){
					$response = "Sorry, No Response Yet!";
					}else{
					$response =  $query_data['response_content'];
					}
					?>
			<div class="col-md-12">
			
					<div class="col-md-6 col-sm-6">
						<span class="rspnse"><?php echo $date_to_show; ?></span>
						<p class="offerz_support"><?php echo $content_query; ?></p>
					</div>
					<div class="col-md-6 col-sm-6">
					<span class="rspnse">Response</span>
						<p class="offerz_support2"><?php echo $response; ?></p>
					</div>
					
					
					
			</div>
			<?php
					}
					?>
		</div>
	</div>
</div>

<!---end-content----->
   
	<script>
		
		$(document).ready(function(){
		$("#profile_div").slideUp();
		});
		setTimeout(function(){$(".success").slideUp();},10000);
		function toggle_profile_div(){
		// alert('teee');
		$("#profile_div").slideToggle();
		}
		$('#panel-527391 .fa-bars ').click(function (){
		
		//$('#panel-527391').find('fa-chevron-down').addClass('fa-bars');
		//$('#panel-527391').find('fa-bars').removeClass('fa-chevron-down');
		
		$(this).toggleClass('fa-bars fa-chevron-down'); });
	</script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
   
  </body>
</html>
<?php
ob_flush();
?>