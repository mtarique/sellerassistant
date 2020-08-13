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
     * Insert Amazon payment header
     *
     * @param string    $fin_event_grp_id       Financial Event Group Id
     * @param Date      $fin_event_grp_start    Financial Event Group Start
     * @param Date      $fin_event_grp_end      Financial Event Group End
     * @return void
     */
    public function insert_amz_pmt_header($fin_event_grp_id, $fin_event_grp_start, $fin_event_grp_end, $amz_acct_id, $fin_event_curr, $beg_bal_amt, $deposit_amt, $fund_trf_date)
    {   
        $header_data = array(
            'fin_event_grp_id'    => $fin_event_grp_id, 
            'fin_event_grp_start' => date('Y-m-d', strtotime($fin_event_grp_start)),  
            'fin_event_grp_end'   => date('Y-m-d', strtotime($fin_event_grp_end)), 
            'fin_event_curr'      => $fin_event_curr, 
            'beg_bal_amt'         => $beg_bal_amt, 
            'deposit-amt'         => $deposit_amt, 
            'fund_trf_date'       => $fund_trf_date,  
            'amz_acct_id'         => $amz_acct_id
        ); 

        $query = $this->db->insert('amz_pmt_header', $header_data);

        return ($query) ? true : $this->db->error()['message'];
    }

    /**
     * Insert Amazon Payment Details 
     *
     * @param   array     $fees_data  Multi-dimensional array
     * @return  void
     */
    public function insert_amz_pmt_details($fees_data)
    {   
        $this->db->trans_start(); 

        // Loop through fees rows 
        foreach($fees_data as $data) 
        {
            $this->db->insert('amz_pmt_details', $data); 
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

    public function get_fba_fees_comp($fin_event_grp_id)
    {
        $query = $this->db
                        ->select('amz_pmt_details.*, amz_pmt_header.*')
                        ->join('amz_pmt_header', 'amz_pmt_details.fin_event_grp_id = amz_pmt_header.fin_event_grp_id', 'left')
                        ->where('amz_pmt_details.fin_event_grp_id', $fin_event_grp_id)
                        ->get('amz_pmt_details'); 

        return ($query->num_rows() > 0) ? $query->result() : null;
    }
}

?>