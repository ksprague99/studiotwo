<?php
/**
 * This class will handle file/image uploading
 * Return attachment id
 * 
 * @package classes/class-upload
 * @author Dipankar Pal (dipankarpal212@gmail.com)
 */

class Cath_File_Upload extends Cath_Api_Handler {

    public function __construct(){

    }

    /**
     * Override default upload path
     * @param array $dirs
     * @return array $dirs
     */
    function new_upload_dir( $dirs ) {
        $dirs['subdir'] = '/custom';
        $dirs['path'] = $dirs['basedir'] . '/custom';
        $dirs['url'] = $dirs['baseurl'] . '/custom';
        return $dirs;
    }


    /**
     * Upload new file
     * @param array $filearray
     * @return array 
     */
    function upload_file($filearray){
        if ( !empty( $filearray ) && !empty( $filearray['file'] ) ) {
            $file = $filearray['file'];
            $orig_fileinfo = pathinfo($file['name']);
        }
        $mimes = array(
            'bmp'  => 'image/bmp',
            'gif'  => 'image/gif',
            'jpe'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png',
            'tif'  => 'image/tiff',
            'tiff' => 'image/tiff'
        );
    
        $overrides = array(
            'mimes'     => $mimes,
            'test_form' => false
        );
        add_filter( 'upload_dir', array( $this , 'new_upload_dir' ) );
        $upload = wp_handle_upload( $file, $overrides );
        remove_filter( 'upload_dir', array( $this , 'new_upload_dir' ) );
    
        if ( isset( $upload['error'] ) ){
            return array('act' => 'failed' , 'attach_id'=> 0 , 'msg' => $upload['error'] );
        } else {
            $uploaded_pathinfo = pathinfo($upload['file']);
            $uploaded_file = $upload['file'];
            $wp_filetype = wp_check_filetype($uploaded_pathinfo['basename'], null );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($uploaded_pathinfo['filename']),
                'post_content' => '',
                'post_excerpt' => sanitize_file_name($uploaded_pathinfo['filename']),
                'post_status' => 'inherit',
                'post_author' => AUTHOR_ID
            );
            $attach_id = wp_insert_attachment( $attachment, $uploaded_file  );
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file );
            $res1 = wp_update_attachment_metadata( $attach_id, $attach_data );
            update_post_meta( $attach_id , '_wp_attachment_image_alt' ,  $uploaded_pathinfo['filename'] );
            $process_story_id = explode('__',$orig_fileinfo['filename']);
            $story_id = $process_story_id[1];
            if( !empty($story_id) && !empty($attach_id) ){
                $this->save_attach_ids( $story_id , $attach_id );
            }
            
            return array('act' => 'success' , 'attach_id'=> $attach_id , 'msg' => 'Image Uploaded' );
        }
    
    }

    public function save_attach_ids($story_id,$attach_id){      
        $story_media_pair = get_option('wcra_story_media_pair');
        if(!empty($story_media_pair)){
            $story_media_pair[$story_id][] = $attach_id;
        }else{
            $story_media_pair = array();
            $story_media_pair[$story_id][] = $attach_id;
        }        
        update_option('wcra_story_media_pair',$story_media_pair);
        
        //print_r($story_media_pair);die;
        $this->set_media_id();
        $this->set_gallery($story_id);

    }

    public function set_gallery($story_id){
        if(empty($story_id)){
            return false;
        }
        $post_id = get_post_by_stroy_id($story_id);
        $get_image_ids = $this->get_image_ids($story_id);
        if(!empty($get_image_ids)){
            foreach($get_image_ids as $id){
                $src = $this->src($id);
                if( ! in_array( $src , get_post_meta( $post_id['post_id'] , 'wpcf-gallery' , false ) )  ){
                    $meta_id[] = add_post_meta( $post_id['post_id'] , 'wpcf-gallery' , $src );
                }              
            }
        }
       // $sort_order = get_post_meta($post_id['post_id'] , '_wpcf-gallery-sort-order' , true );
       //echo 'dip';
     // print_r(get_post_meta( $post_id['post_id'] , 'wpcf-gallery' , false ));
     // print_r($meta_id);


    }

    public function set_media_id(){
        $story_media_pair = get_option('wcra_story_media_pair');
        if(!empty($story_media_pair)){
            foreach($story_media_pair as $story_id=>$attach_id){
                $meta_value[] = $story_id;
            }
            
            $args = array(
                'post_type'  => 'post',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => SCC_STORY_ID ,
                        'value'   => $meta_value ,
                        'compare' => 'IN',
                    ),
                ),
            );
            $query = new WP_Query( $args );
            //print_r($query->posts);die;
            if(!empty($query->posts)){
                foreach( $query->posts as $k=>$val){
                    $SCC_STORY_ID = $this->get_story_id($val->ID);
                    if(!empty( $story_media_pair[$SCC_STORY_ID] )){
                        $check_thumbnail_id = $this->check_thumbnail_id($val->ID);
                        if(empty($check_thumbnail_id)){
                            /** set media id to post */
                            $date = current_time('Y-m-d H:i:s');
                            set_post_thumbnail( $val->ID, $story_media_pair[$SCC_STORY_ID][0] );
                            /** update latest log (how many media updated with story) */
                            $uptodate = array('post_id' => $val->ID , 'story_id' => $SCC_STORY_ID, 'attach_id' => $story_media_pair[$SCC_STORY_ID][0] , 'date' => $date);
                            $this->sync_log($uptodate);
                        }                 
                    }
                }
            }
        }
    }

    public function get_image_ids($story_id){
        $attach_ids = array();
        $story_media_pair = get_option('wcra_story_media_pair');
        if(!empty($story_media_pair)){
            $attach_ids = $story_media_pair[$story_id];
        }
        return $attach_ids;
    }

    function src($attach_id){
        $src = wp_get_attachment_image_src($attach_id,'full');
        return $src[0];
    }


}


