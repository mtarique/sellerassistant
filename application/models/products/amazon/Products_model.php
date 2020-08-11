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

    /**
     * Bulk update product dimensions from excel upplod
     *
     * @param  integer  $amz_acct_id
     * @param  array    $sheetdata
     * @return boolean
     */
    public function upd_prod_dim($amz_acct_id, $sheetdata)
    {
        $this->db->trans_start(); 

        // Loop through fees rows 
        foreach($sheetdata as $data) 
        {
            $sku  = $data[0]; 
            $asin = $data[1]; 
            $wt   = $data[2]; 
            $ls   = $data[3]; 
            $ms   = $data[4]; 
            $ss   = $data[5];

            if(empty($this->get_fba_prod($amz_acct_id, $sku)))
            {
                $this->db->insert('fba_products', array(
                    'amz_acct_id'    => $amz_acct_id, 
                    'seller_sku'     => $sku, 
                    'asin'           => $asin, 
                    'pkgd_prod_wt'   => $wt, 
                    'unit_of_weight' => 'g', 
                    'longest_side'   => $ls, 
                    'median_side'    => $ms, 
                    'shortest_side'  => $ss, 
                    'unit_of_dimension' => 'in'
                )); 
            } 
            else {
                $this->db
                        ->where(array('amz_acct_id' => $amz_acct_id, 'seller_sku' => $sku))
                        ->update('fba_products', array(
                            'asin'           => $asin, 
                            'pkgd_prod_wt'   => $wt, 
                            'unit_of_weight' => 'g', 
                            'longest_side'   => $ls, 
                            'median_side'    => $ms, 
                            'shortest_side'  => $ss, 
                            'unit_of_dimension' => 'in'
                        )); 
            }
        }

        $this->db->trans_complete(); 

        // Commit the transactions
        if($this->db->trans_status() === TRUE)
        {
            $this->db->trans_commit();    
            return true; 
        }
        else {
            $this->db->trans_rollback();  
            return false; 
        }
    }
}

?>