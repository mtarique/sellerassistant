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
 * Get amazon accounts as select options
 *
 * @return void
 */
function _options_amz_accts($userid, $selected = null)
{
    $CI = & get_instance(); 

    $CI->load->model('settings/channels/amazon_model');

    $result = $CI->amazon_model->get_amz_accts($userid); 

    if(!empty($result)) 
    {   
        $options = ''; 
        
        foreach($result as $row)
        {   
            if(isset($selected) && $selected == $row->amz_acct_id)
            {   
                $options .= '<option value="'.$row->amz_acct_id.'" selected>'.$row->amz_acct_name.'</option>'; 
            }
            else $options .= '<option value="'.$row->amz_acct_id.'">'.$row->amz_acct_name.'</option>'; 
        }
        return $options; 
    }
    else return null;
}

/**
 * Get amazon marketplaces as select options
 *
 * @return void
 */
function _options_marketplaces($selected = null)
{
    $CI = & get_instance(); 

    $CI->load->model('settings/channels/amazon_model'); 

    $result = $CI->amazon_model->get_marketplaces(); 

    if(!empty($result)) 
    {   
        $options = ''; 
        
        foreach($result as $row)
        {   
            $mp_id   = $row->marketplace_id; 
            $mp_name = $row->sales_channel.' ('.$row->marketplace_name.')'; 

            if(isset($selected) && $selected == $row->marketplace_id)
            {   
                $options .= '<option value="'.$mp_id.'" selected>'.$mp_name.'</option>'; 
            }
            else $options .= '<option value="'.$mp_id.'">'.$mp_name.'</option>'; 
        }
        return $options; 
    }
    else return null;
}

 ?>


