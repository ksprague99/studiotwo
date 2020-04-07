<?php
require_once('classes/class-initialize.php');

/** Create post via rest api - handler */
add_filter("wcra_create_post_callback" , "wcra_create_post_callback_handler");
function wcra_create_post_callback_handler($param){

	$Cath_Api_Handler = new Cath_Api_Handler();
    $get_params = $param->get_params();
	if(isset($get_params[REQ_TITLE])){
        $create_post = $Cath_Api_Handler->create_post($get_params);
        $postdata = get_post($create_post['post_id']);
		if($create_post['action'] == 'create'){
            $data = array(
				'msg' => 'Post successfully created',
				'post_id' => $create_post['post_id'],
			);
        }else{
            $data = array(
				'msg' => 'Post successfully updated',
				'post_id' => $create_post['post_id'],
            );
        }	
		return array('RequestedData' => $get_params, 'ResponseData' =>  $data);
	}
	return array('RequestedData' => $get_params, 'ResponseData' =>  'Parameter required !');	
}


add_filter("wcra_upload_image_callback" , "wcra_upload_image_callback_handler");
function wcra_upload_image_callback_handler($request){
	$Cath_File_Upload = new Cath_File_Upload();
	$files = $request->get_file_params();
	$get_headers = $request->get_headers();
	return $Cath_File_Upload->upload_file($files);
	
}


