<?php
/**
 * The custom model
 * @author Sokha
 */
class Imodel extends CI_Model {
	/**
	 * The constructor of this class
	 */
	function __construct() {
        parent::__construct();
    }

   /**
	 * loading another model
	 * @param $model_name the model name to load
	 * @author Sokha RUM
	 */
	function load_other_model($model_name) {
        $CI =& get_instance();
        $CI->load->model($model_name);
        return $CI->$model_name;
   }
}