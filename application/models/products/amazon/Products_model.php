<?php
/**
 * Amazon FBA Products model
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Model 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed'); 

class Products_model extends CI_Model 
{   
    public function __construct()
    {
        parent::__construct(); 

        $this->load->database(); 
    }

    /**
     * Get FBA product details 
     *
     * @param   integer    $amz_acct_id     Amazon account id     
     * @param   string     $seller_sku      Seller SKU
     * @return  array
     */
    public function get_fba_prod($amz_acct_id, $seller_sku)
    {
        $query = $this->db->get_where('fba_products', array('amz_acct_id' => $amz_acct_id, 'seller_sku' => $seller_sku)); 

        return ($query->num_rows() > 0) ? $query->result() : null;
    }
}

?>