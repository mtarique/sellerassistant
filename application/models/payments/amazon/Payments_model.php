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
     * Insert FBA Fees Comparison header
     *
     * @param string    $fin_event_grp_id       Financial Event Group Id
     * @param Date      $fin_event_grp_start    Financial Event Group Start
     * @param Date      $fin_event_grp_end      Financial Event Group End
     * @return void
     */
    public function insert_fba_fees_comp_header($fin_event_grp_id, $fin_event_grp_start, $fin_event_grp_end)
    {   
        $header_data = array(
            'fin_event_grp_id'    => $fin_event_grp_id, 
            'fin_event_grp_start' => date('Y-m-d', strtotime($fin_event_grp_start)),  
            'fin_event_grp_end'   => date('Y-m-d', strtotime($fin_event_grp_end))
        ); 

        $query = $this->db->insert('fba_fees_comp_header', $header_data);

        return ($query) ? true : $this->db->error()['message'];
    }

    /**
     * Insert FBA Fees Comparison details 
     *
     * @param   array     $fees_data  Multi-dimensional array
     * @return  void
     */
    public function insert_fba_fees_comp_details($fees_data)
    {   
        $this->db->trans_start(); 

        // Loop through fees rows 
        foreach($fees_data as $data) 
        {
            $this->db->insert('fba_fees_comp_details', $data); 
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