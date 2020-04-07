<?php
class Cath_Api_Init {

    public function __construct(){
        require_once( dirname(__dir__) . '/constants.php');
        require_once( dirname(__dir__) . '/data-helper.php');
        require_once('class-create-posts-throgh-api.php');
        require_once('class-connect-tooset.php');
        require_once( 'class-upload.php');
    }
}

new Cath_Api_Init();