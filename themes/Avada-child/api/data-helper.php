<?php
function get_post_by_stroy_id($scc_story_id){
    if(empty($scc_story_id)){
        return array('post_id' => 0 , 'msg' => 'Story id required');
    }
    $args = array(
        'post_type'  => 'post',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key'     => SCC_STORY_ID ,
                'value'   => $scc_story_id ,
                'compare' => '=',
            ),
        ),
    );
    $query = new WP_Query( $args );
    if(!empty($query->posts)){
        return array('post_id' => $query->posts[0]->ID , 'msg' => 'Story id fetched');
    }
}

function cath_post_save_action($scc_story_id){
    $get_post_by_stroy_id = get_post_by_stroy_id($scc_story_id);
    if($get_post_by_stroy_id['post_id'] > 0 ){
        return array('act' => 'update' , 'post_id' => $get_post_by_stroy_id['post_id']);
    }else{
        return array('act' => 'create');
    }
}

$MSD = array(
    'A-I',
    'A-II',
    'A-III',
    'L',
    'O'
);

$MPR = array(
    'G',
    'PG',
    'PG-13',
    'R',
    'NC-17'
);

global $MSD;
global $MPR;
/**
 * Return HTML or array , depends on parameter - movie_classification
 * @param array $return, value = html || NULL (if NULL return array)
 * @return string $html || array $return
 */

function cath_movie_classification_data( $data = array() ){
    global $MSD;
    $return_type = isset($data['return'])  ? '' : 'html';
    $html = '';
    $chunk = $MSD;
    if( empty($return_type) ){
        return $chunk;
    }

    for( $loop=0 ; $loop < count($chunk) ; $loop++ ){
        if( isset($data['selected']) && $data['selected'] == $chunk[$loop] ){
            $html .= '<input type="checkbox" value="'.$chunk[$loop].'" name="movie_class" checked >'.$chunk[$loop];
        }else{
            $html .= '<input type="checkbox" value="'.$chunk[$loop].'" name="movie_class">'.$chunk[$loop];
        }
    }

    return $html;
}

/**
 * Return HTML or array , depends on parameter  (MPAA RATINGS)
 * @param array $return, value = html || NULL (if NULL return array)
 * @return string $html || array $return
 */

function cath_mpaa_rating_data( $data = array() ){
    global $MPR;
    $return_type = isset($data['return'])  ? '' : 'html';
    $html = '';
    $chunk = $MPR;
    if( empty($return_type) ){
        return $chunk;
    }

    for( $loop=0 ; $loop < count($chunk) ; $loop++ ){
        if( isset($data['selected']) && $data['selected'] == $chunk[$loop] ){
            $html .= '<input type="checkbox" value="'.$chunk[$loop].'" name="mpaa_rating" checked >'.$chunk[$loop];
        }else{
            $html .= '<input type="checkbox" value="'.$chunk[$loop].'" name="mpaa_rating">'.$chunk[$loop];
        }
    }

    return $html;
}


/*** add meta box */
function add_gallery_handler(){
        add_meta_box(
            'Gallery',           // Unique ID
            'Gallery',  // Box title
            'gallery_callback',  // Content callback, must be of type callable
            'post'                   // Post type
        );

}
add_action('add_meta_boxes', 'add_gallery_handler');

function gallery_callback($post){
    $Cath_File_Upload = new Cath_File_Upload();
    $SCC_STORY_ID = $Cath_File_Upload->get_story_id($post->ID);
    if(empty($SCC_STORY_ID)){
        echo 'No gallery found';
        return false;
    }
    $get_image_ids = $Cath_File_Upload->get_image_ids($SCC_STORY_ID);
    if(!empty($get_image_ids)){
        echo '<table class="wcra_custom_gallery"><tr class="items">';
        foreach($get_image_ids as $attch_id){
    ?>
        <td>
            <a href="<?php echo $Cath_File_Upload->src($attch_id);?>" target="_blank"><img src="<?php echo $Cath_File_Upload->src($attch_id);?>" alt=""></a>
            <div class="img_overlay">
                <a>Delete</a>
                <a>Set as feature image</a>
            </div>
        </td>
    <?php
        }
        echo '</tr></table>';
    }
}

add_action('admin_footer','custom_style_h');
function custom_style_h(){
    ?>
    <style>
    .wcra_custom_gallery tr td{
        padding: 10px;
    }
    .wcra_custom_gallery tr {
        background : #f4f4f4;
    }
    .wcra_custom_gallery tr td:hover{
        background : silver;
    }
    </style>
    <?php
}