<?php 
/**
 * Amazon Sales Channel Integrations
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Amazon extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper('auth_helper');

        $this->load->library('encryption');

        $this->load->model('settings/channels/amazon_model'); 
    }

    public function index()
    {
        $page_data['title'] = "Amazon Account";
        $page_data['descr'] = "Manage your connected Amazon seller central account and connect a new one."; 

        $this->load->view('settings/channels/amazon/accounts', $page_data);
    }

    public function new()
    {
        $page_data['title'] = "Connect Amazon Account";
        $page_data['descr'] = "Connect your amazon seller central account using MWS API Keys."; 

        $this->load->view('settings/channels/amazon/connect', $page_data);
    }
   
    /**
     * Connect to MWS or add seller MWS credentials
     *
     * @return void
     */
    public function connect()
    {
        // Set form validation rules 
        $this->form_validation->set_rules('inputMWSAcctName', 'Account Name', 'required'); 
        $this->form_validation->set_rules('inputMpId', 'Marketplace', 'required');
        $this->form_validation->set_rules('inputSellerId', 'Seller Id', 'required');
        $this->form_validation->set_rules('inputMwsAuthToken', 'Seller Id', 'required');
        $this->form_validation->set_rules('inputAWSAccessKeyId', 'AWS Access Key ID', 'required');
        $this->form_validation->set_rules('inputSecretKey', 'Secret Key', 'required');  

        // Validate user input
        if($this->form_validation->run() == true)
        {   
            // Amazon seller MWS account data array
            $seller_data['user_id']           = $this->session->userdata('_userid');  
            $seller_data['account_name']      = $this->input->post('inputMWSAcctName'); 
            $seller_data['marketplace_id']    = $this->input->post('inputMpId'); 
            $seller_data['seller_id']         = $this->encryption->encrypt($this->input->post('inputSellerId')); 
            $seller_data['mws_auth_token']    = $this->encryption->encrypt($this->input->post('inputMwsAuthToken'));
            $seller_data['aws_access_key_id'] = $this->encryption->encrypt($this->input->post('inputAWSAccessKeyId')); 
            $seller_data['secret_key']        = $this->encryption->encrypt($this->input->post('inputSecretKey')); 

            // Query to add MWS Account
            $result = $this->amazon_model->insert_mws_account($seller_data);
            
            // Validate query response
            if($result == 1)
            {
                $ajax['status']  = true; 
                $ajax['message'] = show_alert('success', "Connected!"); 
            }
            else {
                $ajax['status']  = false; 
                $ajax['message'] = show_alert('danger', $result);    
            }
        }
        else {
            $ajax['status']  = false; 
            $ajax['message'] = show_alert('danger', validation_errors());
        }

        echo json_encode($ajax); 
    }
}

?>