<?php
/**
 * This class will create a bridge between Rest endpoint and toolset fields
 * @package classes/class-connect-tooset
 * @author Dipankar Pal (dipankarpal212@gmail.com)
 * @since 1.0.0
 */

class Cath_Connect_Tool {

    function __construct(){

    }

    public $field_slug='';

    /**
     * Fetch field data of a field created by toolset plugin
     * @param string $field_slug, EX. movie-classification
     * @return array $field_data 
     */
    public function get_field_keys($field_slug){
        $field_data = wpcf_admin_fields_get_field($field_slug);
        return $field_data;
    }

    /**
     * Set a field for a certain moment, before calling `update_checkbox_fields` 
     * @param string $field_slug, EX. movie-classification
     * @return array $field_slug 
     */
    public function set_field($field_slug){
        if(empty($field_slug)){
            return false;
        }
        $this->field_slug = $field_slug;
    }

    /**
     * unSet a field for a certain moment, before calling `update_checkbox_fields`
     * @param void 
     * @return void 
     */
    public function unset_field(){
        $this->field_slug = '';
    }

    /**
     * Update checkbox values to a field for a post id
     * @param int $post_id,
     * @param array $checkbox_values,
     * @return void 
     */
    public function update_checkbox_fields($post_id,$checkbox_values){
        if(empty($this->field_slug)){
            return 'Filed slug missing, try this method - set_field()';
        }
        if( empty($post_id) && empty($checkbox_values)){
            return 'Few Arguments : Min 2 arguments required';
        }
        $fieldset = $this->get_field_keys($this->field_slug);

        if(empty($fieldset)){
            return 'Field is not registered.';
        }

        if (isset($fieldset['data']['options'])){
            $res = array();
            foreach ($fieldset['data']['options'] as $key => $option){
                if (in_array($option['set_value'], $checkbox_values)){
                    $res[$key] = $option['set_value'];
                }
            }   
            update_post_meta( $post_id, 'wpcf-'.$this->field_slug  , $res );
            return 'updated';
            $this->unset_field();
        }

    }
}