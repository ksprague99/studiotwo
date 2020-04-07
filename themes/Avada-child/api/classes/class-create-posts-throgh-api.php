<?php
/**
 * This class handle post insertion, set tag/categories , image upload/set
 * @package classes/class-create-posts-throgh-api/Cath_Api_Handler
 * 
 * @author Dipankar Pal (dipankarpal212@gmail.com)
 * @since 1.0.0
 */

class Cath_Api_Handler{

    public $post_id;

    function __construct(){

    }

    /**
     * Save/update a post 
     * @param array $post_data
     * 
     * @return array $post_id, $action
     */
    public function create_post($post_data){
        $save_action = cath_post_save_action($post_data[REQ_SCC_STORY_ID]);
        if($save_action['act'] == 'create'){         
            $args = array(
                'post_title'    => sanitize_text_field($post_data[REQ_TITLE]),
                'post_content'  => $post_data[REQ_CONTENT],
                'post_status'   => 'publish', // Automatically publish the post.
                'post_author'   => AUTHOR_ID,
                'post_type'     => 'post' // defaults to "post". Can be set to CPTs.
            );
            if(!empty($post_data[REQ_SLUG])){
                $args['post_name'] = $post_data[REQ_SLUG];
            }
            if(!empty($post_data[REQ_DATE])){
                $args['post_modified'] = $post_data[REQ_DATE];
                $args['post_modified_gmt'] = $post_data[REQ_DATE];
            }
        
            // Lets insert the post now.
            $post_id = wp_insert_post( $args );
            $this->update_additional_fields($post_id,$post_data);
            $this->post_id = $post_id;
        }else{
            $up_args = array(
                'ID'                => $save_action['post_id'],
                'post_content'      => $post_data[REQ_CONTENT],
                'post_title'        => sanitize_text_field($post_data[REQ_TITLE]),
                'post_type'         => 'post',
                'post_modified'     => $post_data[REQ_DATE],
                'post_modified_gmt' => $post_data[REQ_DATE],
            );
            $result = wp_update_post($up_args, true);
            if (is_wp_error($result)){
                return 'Post not saved';
            }
            $this->update_additional_fields( $save_action['post_id'] , $post_data );
            $this->post_id = $save_action['post_id'];          
        }

        /** Set tags */
        $this->set_tags( $this->post_id , $post_data[REQ_TAGS] );

        /** Set categories */
        $this->set_categories( $this->post_id , $post_data[REQ_CATS] );

        /** Set movie rating tax */
        $this->set_tags( $this->post_id , $post_data[REQ_MOV_RATINGS] , TAX_MOV_RATE  );

        /** Set moview classification tax */
        $this->set_tags( $this->post_id , $post_data[REQ_MOV_CLASS] , TAX_MOV_CLASS  );

        /** Set feature image  */
        /*if(!empty($post_data[REQ_FEATURE_IMAGE]['image_url'])){
            $this->set_feature_image( $this->post_id , $post_data );
        }*/

        /** Set gallery images */
       /* if(!empty($post_data[REQ_FEATURE_GALLERY])){
            $this->set_gallery_image( $this->post_id , $post_data );
        }*/
        $Cath_File_Upload = new Cath_File_Upload();
        $Cath_File_Upload->set_media_id();
        $Cath_File_Upload->set_gallery($post_data[REQ_SCC_STORY_ID]);
        return array( 
            'post_id' => $this->post_id,
            'action' => $save_action['act']
        );

    }

    /**
     * Update toolset additional fields
     * 
     * @param int $post_id
     * @param array $post_data , requested data
     * @return void
     */
    public function update_additional_fields($post_id,$post_data){
        if(empty($post_id) && empty($post_data)){
            return false;
        }
        update_post_meta( $post_id, SCC_BYLINE , $post_data[REQ_BYLINE] );
        update_post_meta( $post_id, SCC_LANGUAGE , $post_data[REQ_LANGUAGE] );
        update_post_meta( $post_id, SCC_STORY_ID , $post_data[REQ_SCC_STORY_ID] );

        $Cath_Connect_Tool = new Cath_Connect_Tool();
        /**update mov classification */
        /*$Cath_Connect_Tool->set_field('movie-classification');
        $update_checkbox_fields = $Cath_Connect_Tool->update_checkbox_fields($post_id,$post_data[REQ_MOV_CLASS]);*/

        /**update mov ratings */
        /*$Cath_Connect_Tool->set_field('movie-rating');
        $update_checkbox_fields = $Cath_Connect_Tool->update_checkbox_fields($post_id,$post_data[REQ_MOV_RATINGS]);*/

    }


    /**
     * check and Insert a TAG into DB
     * @param int $post_id
     * @param array $tags
     * @param string $taxonomy
     * @return void
     */
    function set_tags($post_id,$tags,$taxonomy='post_tag'){
        if(empty($tags)){
            return false;
        }
        wp_set_object_terms( 
            $post_id, 
            $tags, 
            $taxonomy 
        );
    }


    /**
     * check and Insert a category into DB
     * @param int $post_id
     * @param array $categories
     * @param string $taxonomy
     * @return void
     */
    function set_categories($post_id,$categories,$taxonomy='category'){
        if(empty($categories)){
            return false;
        }
        wp_set_object_terms( 
            $post_id, 
            $categories, 
            $taxonomy 
        );
    }

    /**
     * Set post feature image
     * @param int $post_id
     * @param array $post_data
     * 
     * @return int $attach_id
     */
    /*function set_feature_image($post_id,$post_data){
        $attach_id =0;
        $metadata = array(
            'description' => sanitize_text_field($post_data[REQ_FEATURE_IMAGE]['description']) ,
            'caption' => sanitize_text_field($post_data[REQ_FEATURE_IMAGE]['caption']) ,
            'alt_text' => sanitize_text_field($post_data[REQ_FEATURE_IMAGE]['alt_text'])
        );
        $image_url = $post_data[REQ_FEATURE_IMAGE]['image_url'];
        if(!empty($image_url)){
            $attach_id = $this->upload_single_image($image_url,$metadata,$post_id);
            if( $attach_id > 0 ){
                set_post_thumbnail( $post_id, $attach_id );
                return $attach_id;
            }
        }
    }*/

    /**
     * Set post gallery image
     * @param int $post_id
     * @param array $post_data
     * 
     * @return array $attach_ids
     */
    /*function set_gallery_image($post_id,$post_data){      
        $attach_ids = array();
        if(!empty($post_data[REQ_FEATURE_GALLERY])){
            foreach( $post_data[REQ_FEATURE_GALLERY] as $key => $values ){
                $image_url = $values['image_url'];
                if(!empty($image_url)){
                    $attach_ids[] = $this->upload_single_image($image_url,$values);                  
                }
            }
            if(!empty($attach_ids)){
                update_post_meta( $post_id , '_gallery_ids' , $attach_ids  );
            }
        }
        return $attach_ids;
    }*/

    
    /**
     * Upload an image to database by externel image url
     * 
     * @param string $image_url
     * @param array $metadata
     * @param int $post_id (optional)
     * @return int $attach_id
     */
    /*function upload_single_image($image_url,$metadata,$post_id=''){
        $pathinfo = pathinfo($image_url);
        $upload_dir = wp_upload_dir();
       // return $upload_dir;
        $image_data = file_get_contents($image_url);
        $filename = basename($image_url);
        if(wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
        else
        $file = $upload_dir['basedir'] . '/' . $filename;
        file_put_contents($file, $image_data);
        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($pathinfo['filename']),
            'post_content' => $metadata['description'],
            'post_excerpt' => $metadata['caption'] ,
            'post_status' => 'inherit',
            'post_author' => AUTHOR_ID
        );
        $attach_id = wp_insert_attachment( $attachment, $file , $post_id );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        $res1 = wp_update_attachment_metadata( $attach_id, $attach_data );
        update_post_meta( $attach_id , '_wp_attachment_image_alt' ,  $metadata['alt_text'] );
        return $attach_id; 

    }*/

    function check_thumbnail_id($post_id){
        $_thumbnail_id = get_post_meta( $post_id , '_thumbnail_id' , true );
        if(!empty($_thumbnail_id)){
            return $_thumbnail_id;
        }
        return false;
    }

    function get_story_id($post_id){
        $SCC_STORY_ID = get_post_meta( $post_id , SCC_STORY_ID , true );
        if(!empty($SCC_STORY_ID)){
            return $SCC_STORY_ID;
        }
        return false;
    }

    public function sync_log($data){      
        $sync_log = get_option('wcra_sync_log');
        if(!empty($sync_log)){
            $sync_log[] = $data;
        }else{
            $sync_log = array();
            $sync_log[] = $data;
        }        
        update_option('wcra_sync_log',$sync_log);
    }

}