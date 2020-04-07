<?php
    
    $Cath_Api_Handler = new Cath_Api_Handler();
    $image_url = 'http://localhost/wp/CatholicNews/wp-content/uploads/2012/02/mountain-house.jpg';
    $metadata = array(
        'description' => 'coding descrition',
        'caption' => 'coding caption',
        'alt_text' => 'coding alt text'
    );
    $set_feature_image = $Cath_Api_Handler->set_feature_image('1380',$image_url,$metadata);
   // print_r($set_feature_image);die;
    $Cath_Connect_Tool = new Cath_Connect_Tool();
    $Cath_Connect_Tool->set_field('movie-classification');
    $post_id = '1360'; //the ID of the post to which to update the custom field
    $api_result = array('A-I' );
    $update_checkbox_fields = $Cath_Connect_Tool->update_checkbox_fields($post_id,$api_result);
   // print_r($update_checkbox_fields);
    $get_post_by_stroy_id = get_post_by_stroy_id('1023');
   // print_r($get_post_by_stroy_id);


    $get_post = get_post(5);
    $wp_get_post_categories = wp_get_post_categories(5);
    foreach($wp_get_post_categories as $v){
        $get_term = get_term($v);
        $cats[] = $get_term->name;
    }
    $wp_get_post_tags = wp_get_post_tags(5);
    foreach($wp_get_post_tags as $k=>$lv){
        $tags[] = $lv->name;
    }

    $featured_image = wp_get_attachment_url( get_post_thumbnail_id(5) );
    $featured_image = 'https://cdn.britannica.com/77/170477-004-B774BDDF.jpg';

    $data[REQ_TITLE] = 'Technology Can Cause Breakdown of Emotional Values';
    $data[REQ_CONTENT] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut id auctor erat, eget auctor est. Integer ultrices nibh tortor, ut aliquam tortor finibus at. Vivamus vestibulum arcu eget lectus dictum tristique. Nam quis tincidunt ipsum, eget condimentum libero. Cras suscipit ligula sit amet quam finibus fringilla.

    Integer consequat metus a euismod aliquet. Donec dignissim leo in risus feugiat egestas. Proin id magna ligula. Vivamus in ultrices ligula, quis rhoncus mauris.Integer consequat metus a euismod aliquet. Donec dignissim leo in risus feugiat egestas. Proin id magna ligula. Vivamus in ultrices ligula, quis rhoncus mauris.
    
    Integer consequat metus a euismod aliquet. Donec dignissim leo in risus feugiat egestas. Proin id magna ligula. Vivamus in ultrices ligula, quis rhoncus mauris.
    
    Integer consequat metus a euismod aliquet. Donec dignissim leo in risus feugiat egestas. Proin id magna ligula. Vivamus in ultrices ligula, quis rhoncus mauris.
    Integer consequat metus a euismod aliquet. Donec dignissim leo in risus feugiat egestas. Proin id magna ligula. Vivamus in ultrices ligula, quis rhoncus mauris.Integer consequat metus a euismod aliquet. Donec dignissim leo in risus feugiat egestas. Proin id magna ligula. Vivamus in ultrices ligula, quis rhoncus mauris.';
    $data[REQ_SLUG] = 'technology-can-cause-breakdown-of-emotional-values';
    $data[REQ_DATE] = '2020-04-02 12:45:23';
    $data[REQ_STATUS] = 'publish';
    $data[REQ_EXCERPT] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut id auctor erat, eget auctor est. Integer ultrices nibh tortor, ut aliquam tortor finibus at. Vivamus vestibulum arcu eget lectus dictum tristique. Nam quis tincidunt ipsum, eget condimentum libero. Cras suscipit ligula sit amet quam finibus fringilla.
    
    Integer consequat metus a euismod aliquet. Donec dignissim leo in risus feugiat egestas. Proin id magna ligula. Vivamus in ultrices ligula, quis rhoncus mauris.';
    $data[REQ_BYLINE] = 'Byline text here';
    $data[REQ_SCC_STORY_ID] = '15615';
    $data[REQ_MOV_CLASS] = array('A-I','A-II','A-III');
    $data[REQ_MOV_RATINGS] = array('PG' , 'PG-13');
    $data[REQ_CATS] = array('Journey','Adventure','Travelling');
    $data[REQ_TAGS] = array('Railway','Track','Enjoy','Fun');
    $data[REQ_LANGUAGE] = 'English';
    $data[REQ_FEATURE_IMAGE] = array(
        'image_url' => 'https://catholicnews.flywheelsites.com/wp-content/uploads/2012/06/truck.jpg',
        'description' => 'This is description line',
        'caption' => 'This is a caption, caption identifies the image',
        'alt_text' => 'This is a alternative text of image'
    );
    $data[REQ_FEATURE_GALLERY] = array(
        array(
            'image_url' => 'https://catholicnews.flywheelsites.com/wp-content/uploads/2012/02/shape-red.jpg',
            'description' => 'This is description line 1',
            'caption' => 'This is a caption, caption identifies the image 1',
            'alt_text' => 'This is a alternative text of image 1'
        ),
        array(
            'image_url' => 'https://catholicnews.flywheelsites.com/wp-content/uploads/2012/02/shape-red.jpg',
            'description' => 'This is description line 2',
            'caption' => 'This is a caption, caption identifies the image 2',
            'alt_text' => 'This is a alternative text of image 2'
        ),
        array(
            'image_url' => 'https://catholicnews.flywheelsites.com/wp-content/uploads/2012/02/shape-red.jpg',
            'description' => 'This is description line 3',
            'caption' => 'This is a caption, caption identifies the image 3',
            'alt_text' => 'This is a alternative text of image 3'
        )
    );

    echo json_encode($data);die;