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

    /**
     * Add new Amazon account
     *
     * @return void
     */
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
        $this->form_validation->set_rules('inputAmzAcctName', 'Account Name', 'required'); 
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
            $seller_data['amz_acct_name']     = $this->input->post('inputAmzAcctName'); 
            $seller_data['marketplace_id']    = $this->input->post('inputMpId'); 
            $seller_data['seller_id']         = $this->encryption->encrypt($this->input->post('inputSellerId')); 
            $seller_data['mws_auth_token']    = $this->encryption->encrypt($this->input->post('inputMwsAuthToken'));
            $seller_data['aws_access_key_id'] = $this->encryption->encrypt($this->input->post('inputAWSAccessKeyId')); 
            $seller_data['secret_key']        = $this->encryption->encrypt($this->input->post('inputSecretKey')); 

            // Query to add MWS Account
            $result = $this->amazon_model->add_amz_acct($seller_data);
            
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

    /**
     * List Amazon accounts in a table
     *
     * @return void
     */
    public function list_amz_accts()
    {   
        // Get active user id from session 
        $user_id = $this->session->userdata('_userid');  

        // Query to get all Amazon accounts for active user
        $result = $this->amazon_model->get_amz_accts($user_id);

        // Validate query response
        if(!empty($result))
        {   
            $amz_accts_tbl = '<table class="table table-sm border border-grey-200 w-50" id="tblAmzAccts">';

            foreach($result as $row)
            {
                $amz_accts_tbl .= '
                    <tr>
                        <td class="align-middle text-left"><img src="'.base_url('assets/img/brands/amazon-32.png').'" alt=""></td>
                        <td class="align-middle text-left">'.$row->amz_acct_name.'</td>
                        <td class="align-middle text-left">'.$row->amz_acct_name.'</td>
                        <td class="align-middle text-left"><span class="badge badge-success"><i class="fas fa-check-circle"></i> Connected</span></td>
                        <td class="align-middle text-center">
                            <div class="d-flex flex-row">
                                <a href="'.base_url('settings/channels/amazon/edit?amzacctid='.urlencode($this->encryption->encrypt($row->amz_acct_id))).'" data-toggle="tooltip" data-placement="top" title="Edit account" class="btn btn-sm btn-light shadow-sm mr-2"><i class="fas fa-pencil-alt"></i></a>
                                <a href="#" amz-acct-id="'.urlencode($this->encryption->encrypt($row->amz_acct_id)).'" data-toggle="tooltip" data-placement="top" title="Delete account" class="btn btn-sm btn-light shadow-sm lnk-del-amz-acct"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                ';
            }            
            
            $amz_accts_tbl .= '</table>'; 

            $ajax['status']  = true; 
            $ajax['message'] = $amz_accts_tbl; 
        }
        else {
            $ajax['status']  = false; 
            $ajax['message'] = "<p><i class='fad fa-info-circle'></i> You have not connected any of your Amazon accounts please connect new account.</p>";    
        }

        echo json_encode($ajax); 
    }

    /**
     * Edit amazon account details
     *
     * @return void
     */
    public function edit()
    {   
        // Get amazon account id from url
        $amz_acct_id = urldecode($this->encryption->decrypt($this->input->get('amzacctid')));

        // Query to get amazon account details along with MWS keys
        $result = $this->amazon_model->get_mws_keys($amz_acct_id);
        
        if(!empty($result))
        {   
            $page_data['amz_acct_id']       = $amz_acct_id; 
            $page_data['amz_acct_name']     = $result[0]->amz_acct_name; 
            $page_data['marketplace_id']    = $result[0]->marketplace_id; 
            $page_data['seller_id']         = $this->encryption->decrypt($result[0]->seller_id); 
            $page_data['mws_auth_token']    = $this->encryption->decrypt($result[0]->mws_auth_token); 
            $page_data['aws_access_key_id'] = $this->encryption->decrypt($result[0]->aws_access_key_id); 
            $page_data['secret_key']        = $this->encryption->decrypt($result[0]->secret_key); 
        }

        $page_data['title'] = "Edit Amazon Account";
        $page_data['descr'] = "Change your amazon seller central account MWS API Keys."; 

        $this->load->view('settings/channels/amazon/edit', $page_data);
    }

    /**
     * Update or save amazon account changes
     *
     * @return void
     */
    public function update()
    {
        // Set form validation rules 
        $this->form_validation->set_rules('inputAmzAcctName', 'Account Name', 'required'); 
        $this->form_validation->set_rules('inputMpId', 'Marketplace', 'required');
        $this->form_validation->set_rules('inputSellerId', 'Seller Id', 'required');
        $this->form_validation->set_rules('inputMwsAuthToken', 'Seller Id', 'required');
        $this->form_validation->set_rules('inputAWSAccessKeyId', 'AWS Access Key ID', 'required');
        $this->form_validation->set_rules('inputSecretKey', 'Secret Key', 'required');  

        // Validate user input
        if($this->form_validation->run() == true)
        {   
            // Amazon seller MWS account data array
            $amz_acct_id                        = $this->encryption->decrypt($this->input->post('inputAmzAcctId'));
            $amz_acct_data['amz_acct_name']     = $this->input->post('inputAmzAcctName'); 
            $amz_acct_data['marketplace_id']    = $this->input->post('inputMpId'); 
            $amz_acct_data['seller_id']         = $this->encryption->encrypt($this->input->post('inputSellerId')); 
            $amz_acct_data['mws_auth_token']    = $this->encryption->encrypt($this->input->post('inputMwsAuthToken'));
            $amz_acct_data['aws_access_key_id'] = $this->encryption->encrypt($this->input->post('inputAWSAccessKeyId')); 
            $amz_acct_data['secret_key']        = $this->encryption->encrypt($this->input->post('inputSecretKey')); 

            // Query to update amazon account data 
            $result = $this->amazon_model->update_amz_acct($amz_acct_id, $amz_acct_data);
            
            // Validate query response
            if($result == 1)
            {
                $ajax['status']  = true; 
                $ajax['message'] = show_alert('success', "Changes saved!"); 
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

    /**
     * Delete amazon account by acct id 
     *
     * @return void
     */
    public function delete()
    {
        $amz_acct_id = urldecode($this->encryption->decrypt($this->input->get('amzacctid'))); 

        $result = $this->amazon_model->delete_amz_acct($amz_acct_id);
        
        if($result == 1)
        {
            $ajax['status'] = true; 
            $ajax['message'] = "Account deteted."; 
        }
        else {
            $ajax['status'] = false; 
            $ajax['message'] = $result; 
        }

        echo json_encode($ajax); 
    }
}

?>