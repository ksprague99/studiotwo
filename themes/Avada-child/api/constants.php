<?php


define('AUTHOR_ID' , '7');
/** Predefined some constant of meta keys ( KEY : meta_key_name)*/

define('SCC_STORY_ID' , 'wpcf-story-id');
define('SCC_BYLINE' , 'wpcf-byline');
define('SCC_LANGUAGE' , 'wpcf-language');
define('SCC_MOV_CLASS' , 'wpcf-movie-classification');
define('SCC_MOV_RATEING' , 'wpcf-movie-rating');

/** parameter will be received from request */
define('cathbase' , 'post');
define('REQ_SCC_STORY_ID' , cathbase.'_cns_story_id');
define('REQ_TITLE' , cathbase.'_title');
define('REQ_CONTENT' , cathbase.'_content');
define('REQ_EXCERPT' , cathbase.'_excerpt');
define('REQ_SLUG' , cathbase.'_slug');
define('REQ_STATUS' , cathbase.'_status');
define('REQ_DATE' , cathbase.'_date');
define('REQ_BYLINE' , cathbase.'_byline');
define('REQ_CATS' , cathbase.'_categories');
define('REQ_TAGS' , cathbase.'_tags');
define('REQ_LANGUAGE' , cathbase.'_language');
define('REQ_MOV_CLASS' , cathbase.'_mov_classification');
define('REQ_MOV_RATINGS' , cathbase.'_mov_ratings');
define('REQ_FEATURE_IMAGE' , cathbase.'_feature_image');
define('REQ_FEATURE_GALLERY' , cathbase.'_image_gallery');


/**Taxonomies */
define('TAX_MOV_RATE' , 'movie-rating');
define('TAX_MOV_CLASS' , 'movie-classification');