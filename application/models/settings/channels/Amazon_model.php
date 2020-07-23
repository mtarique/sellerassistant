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
    public function add_amz_acct($seller_data)
    {
        $query = $this->db->insert('amz_accounts', $seller_data); 

        return ($query) ? true : $this->db->error()['message'];
    }

    /**
     * Get Amazon Accounts and its marketplace by user id
     *
     * @param   integer   $userid     
     * @return  array
     */
    public function get_amz_accts($userid)
    {
        $query = $this->db
                        ->select('amz_accounts.*, amz_marketplaces.*')
                        ->join('amz_marketplaces', 'amz_accounts.marketplace_id = amz_marketplaces.marketplace_id', 'left')
                        ->where('amz_accounts.user_id', $userid)
                        ->get('amz_accounts'); 

        return ($query->num_rows() > 0) ? $query->result() : null;
    }

    /**
     * Get Amazon MWS access keys
     *
     * @param   integer   $acctid   Sales channel account id
     * @return  void
     */
    public function get_mws_keys($acctid)
    {
        $query = $this->db
                        ->select('amz_accounts.*, amz_marketplaces.*')
                        ->join('amz_marketplaces', 'amz_accounts.marketplace_id = amz_marketplaces.marketplace_id', 'left')
                        ->where('amz_accounts.amz_acct_id', $acctid)
                        ->get('amz_accounts'); 

        return ($query->num_rows() > 0) ? $query->result() : null;
    }

    /**
     * Get all available amazon marketplaces
     *
     * @return void
     */
    public function get_marketplaces()
    {
        $query = $this->db->get('amz_marketplaces');
        
        return ($query->num_rows() > 0) ? $query->result() : null; 
    } 

    /**
     * Update amazon account details
     *
     * @param   integer     $amz_acct_id    
     * @param   array       $amz_acct_data
     * @return  void
     */
    public function update_amz_acct($amz_acct_id, $amz_acct_data)
    {
        $query = $this->db
                        ->where('amz_acct_id', $amz_acct_id)
                        ->update('amz_accounts', $amz_acct_data); 
                        
        return ($query) ? true : $this->db->error()['message'];
    }

    /**
     * Delete Amazon account from system by account id
     *
     * @param  integer  $amz_acct_id
     * @return void
     */
    public function delete_amz_acct($amz_acct_id)
    {
        $query = $this->db
                        ->where('amz_acct_id', $amz_acct_id)
                        ->delete('amz_accounts'); 
                        
        return ($query) ? true : $this->db->error()['message'];
    }
}
?>