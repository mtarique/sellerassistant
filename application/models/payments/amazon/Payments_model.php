<?php
/**
 * Amazon payment model
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Model 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed'); 

class Payments_model extends CI_Model 
{   
    public function __construct()
    {
        parent::__construct(); 

        $this->load->database(); 
    }

    /**
     * Undocumented function
     *
     * @param   array     $fees_data  Multi-dimensional array
     * @return  void
     */
    public function insert_fba_fees($fees_data)
    {   
        $this->db->trans_start(); 

        // Loop through fees rows 
        foreach($fees_data as $data) 
        {
            $this->db->insert('fba_fees', $data); 
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
       /*  foreach($fees_data as $data) 
        {
            $this->db->insert('fba_fees', $data); 
        }
        return ($query) ? true : $this->db->error()['message'];
        return ($query) ? true : $this->db->error()['message']; */
    }
}

?>