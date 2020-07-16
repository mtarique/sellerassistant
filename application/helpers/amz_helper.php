<?php 
/**
 * Amazon helper 
 * 
 * Retrieves Amazon accounts, marketplaces etc.
 * 
 * This helper is loaded using the following code:
 * $this->load->helper('amz_helper'); 
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
 * Get amazon accounts for active user id
 *
 * @param   integer   $userid   Active user id from session variables
 * @return  array 
 */
function get_amz_accts($userid)
{
    $CI = & get_instance(); 

    $CI->load->model('settings/channels/amazon_model');
    
    $result = $CI->amazon_model->get_amz_accts($userid); 

    if(!empty($result)) 
    {   
        $opt_array = array(); 
        
        foreach($result as $row)
        {
            $opt_array[$row->amz_acct_name] = $row->amz_acct_id;
        }

        return $opt_array; 
    }
    else return null;
}

 /**
 * Get Amazon MWS access keys
 *
 * @param   integer   $acctid   Sales channel account id
 * @return  void
 */
function get_mws_keys($acctid)
{
    $CI = & get_instance(); 

    $CI->load->model('settings/channels/amazon_model');

    $result = $CI->amazon_model->get_mws_keys($acctid); 

    /* if(!empty($result)) 
    {   
        $opt_array = array(); 
        
        foreach($result as $row)
        {
            $opt_array[$row->account_name] = $row->account_id;
        }

        return $opt_array; 
    }
    else return null; */

    return null; 
}


