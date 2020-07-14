<?php
/**
 * Amazon Sales Channel Integrations Model
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Model 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed'); 

class Amazon_model extends CI_Model 
{   
    public function __construct()
    {
        parent::__construct(); 

        $this->load->database(); 
    }

    /**
     * Add new account MWS account credentials
     *
     * @param   array   $seller_data    Contains sellers MWS credentials
     * @return  void
     */
    public function insert_mws_account($seller_data)
    {
        $query = $this->db->insert('amz_accounts', $seller_data); 

        return ($query) ? true : $this->db->error()['message'];
    }
    
    /**
     * Get Amazon Accounts
     *
     * @return void
     */
    public function get_amz_accounts()
    {
        $query = $this->db->get('amz_accounts'); 

        return ($query->num_rows() > 0) ? $query->result() : null;
    }
}
?>