<?php
/**
 * Amazon FBA Fees Calculator model
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Model 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed'); 

class Calculator_model extends CI_Model 
{   
    public function __construct()
    {
        parent::__construct(); 

        $this->load->database(); 
    }

    /**
     * Get product size chart for oversize and standard size products
     *
     * @param   string  $size_type
     * @param   date    $order_date
     * @return  void
     */
    public function get_prod_size_by_type($size_type, $order_date)
    {
        $this->db->select('*'); 
        $this->db->from('fba_prod_size_usa');
        $this->db->where('prod_size_type', $size_type);
		$this->db->where('valid_from <=', $order_date);
		$this->db->where('valid_upto >=', $order_date);
		$this->db->order_by('prod_size_id', 'asc');
        $query = $this->db->get(); 

        return ($query->num_rows() > 0) ? $query->result() : null; 
    }

    /**
     * Get produc size by size code
     *
     * @param  string   $size_code      Product size code
     * @param  date     $order_date     Order date or fees calculation date
     * @return void
     */
    public function get_prod_size_by_code($size_code, $order_date)
    {
        $this->db->select('*'); 
        $this->db->from('fba_prod_size_usa');
        $this->db->where('prod_size_code', $size_code);
		$this->db->where('valid_from <=', $order_date);
		$this->db->where('valid_upto >=', $order_date);
		$this->db->order_by('prod_size_id', 'asc');
        $query = $this->db->get(); 
        
        return ($query->num_rows() > 0) ? $query->result() : null;
    }

    /**
     * Undocumented function
     *
     * @param [type] $size_code
     * @param [type] $order_date
     * @return void
     */
    public function get_fba_ful_fees($size_code, $order_date)
    {
        $this->db->select('*'); 
        $this->db->from('fba_ful_fees_usa');
        $this->db->where('prod_size_code', $size_code);
		$this->db->where('valid_from <=', $order_date);
		$this->db->where('valid_upto >=', $order_date);
        $query = $this->db->get(); 
        
        return ($query->num_rows() > 0) ? $query->result() : null; 
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function get_fba_stg_fees($size_type, $order_date)
    {
        $this->db->select('*'); 
        $this->db->from('fba_stg_fees_usa');
        $this->db->where('prod_size_type', $size_type);
		$this->db->where('valid_from <=', $order_date);
		$this->db->where('valid_upto >=', $order_date);
        $query = $this->db->get(); 
        
        return ($query->num_rows() > 0) ? $query->result() : null; 
    }

}
?>