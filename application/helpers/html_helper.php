<?php 
/**
 * HTML Helper
 * 
 * Provides custom html element printing functions 
 * which are not available in codeigniter's default html helper.
 * 
 * Call this helper autoload config file.
 * 
 * NOTE: Don't included any helper or library that has already been
 * called in autoload. 
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Helper 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

/**
 * Generates options for select element
 *
 * @param   array       $options
 * @param   string      $attributes
 * @return  string
 */
function _options($options = array(), $attributes = '', $selected = null)
{
    if(!is_array($options)) return null; 
    
    $opt_tags = ''; 

    foreach($options as $key => $val)
    {
        $opt_tags .= '<option value="'.$val.'" '._stringify_attributes($attributes).'>'.$key.'</option>';
    }

    return $opt_tags; 
}

?>