<?php
/**
 * Marketplace integrations model
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Model 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed'); 

class Integrations_model extends CI_Model 
{   
    public function __construct()
    {
        parent::__construct(); 

        $this->load->database(); 
    }

    /**
     * Add new seller MWS credentials
     *
     * @param   array   $seller_data    Contains sellers MWS credentials
     * @return  void
     */
    public function add_seller($seller_data)
    {
        $query = $this->db->insert('mws_sellers', $seller_data); 

        return ($query) ? true : $this->db->error()['message'];
    }
}
?>