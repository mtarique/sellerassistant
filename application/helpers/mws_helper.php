<?php 
/**
 * MWS Credentials and Information Helper
 * 
 * Authenticates active session, role, permissions etc.
 * 
 * Call this helper inside contructor function only if required as shown below: 
 * $this->load->helper('auth_helper'); 
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

// Get instance, access CI superobject
$CI = & get_instance(); 

$CI->load->library(array('session', 'user_agent')); 

$CI->load->helper('url');



/**
 * Amazon accounts in select options
 *
 * @return void
 */
function amz_accounts_options()
{
    $CI = & get_instance(); 

    $CI->load->model('settings/channels/amazon_model');
    
    $result = $CI->amazon_model->get_amz_accounts(); 

    if(!empty($result)) 
    {   
        $options = ''; 

        foreach($result as $row)
        {
            $options .= '<option value="'.$row->account_id.'">'.$row->account_name.'</options>';
        }

        return $options; 
    }
    else return '<option>Oops!</option>'; 
} 