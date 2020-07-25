<?php 
/**
 * Amazon FBA Fee preview
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

date_default_timezone_set('UTC');

class Fees extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper(array('auth_helper', 'fba_fees_calc_helper'));

        $this->load->library(array('mws/reports', 'encryption')); 

        $this->load->model(array('payments/amazon/payments_model', 'settings/channels/amazon_model', 'products/amazon/products_model')); 
    }

    /**
     * View Amazon fee peview page
     *
     * @return void
     */
    public function index()
    {
        $page_data['title'] = "Fee Preview";
        $page_data['descr'] = "Estimated Amazon Selling and Fulfillment Fees for your current FBA inventory."; 

        $this->load->view('payments/amazon/fees', $page_data);
    }

    /**
     * Request FBA Estimated Fees Report
     *
     * @return void
     */
    public function req_fba_est_fees_report()
    {
        $amz_acct_id = $this->input->post('inputAmzAcctId'); 
        $report_type = "_GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA_"; 

        // Get Amazon MWS Access keys by amazon account id
        $result = $this->amazon_model->get_mws_keys($amz_acct_id);

        if(!empty($result)) 
        {
            $seller_id         = $this->encryption->decrypt($result[0]->seller_id); 
            $mws_auth_token    = $this->encryption->decrypt($result[0]->mws_auth_token); 
            $aws_access_key_id = $this->encryption->decrypt($result[0]->aws_access_key_id); 
            $secret_key        = $this->encryption->decrypt($result[0]->secret_key); 

            $response = $this->reports->RequestReport($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $report_type);
        
            $xml = new SimpleXMLElement($response); 

            if(isset($xml->RequestReportResult->ReportRequestInfo->ReportRequestId))
            {
                $ajax['status'] = true; 
                $ajax['report_request_id'] = $xml->RequestReportResult->ReportRequestInfo->ReportRequestId; 
            }
            else {
                $ajax['status']  = false;
                $ajax['message'] = show_alert('danger', $xml->Error->Message); 
            }
        }
        else {
            $ajax['status']  = false; 
            $ajax['message'] = show_alert('danger', "Amazon account details not found.");     
        }

        echo json_encode($ajax); 
    }

    /**
     * Get report status
     *
     * @return void
     */
    public function get_report_status()
    {
        $amz_acct_id = $this->input->get('amzacctid'); 
        $rep_red_id  = $this->input->get('repreqid'); 

        // Get Amazon MWS Access keys by amazon account id
        $result = $this->amazon_model->get_mws_keys($amz_acct_id);

        if(!empty($result)) 
        {
            $seller_id         = $this->encryption->decrypt($result[0]->seller_id); 
            $mws_auth_token    = $this->encryption->decrypt($result[0]->mws_auth_token); 
            $aws_access_key_id = $this->encryption->decrypt($result[0]->aws_access_key_id); 
            $secret_key        = $this->encryption->decrypt($result[0]->secret_key); 

            $response = $this->reports->GetReportRequestList($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, null, null, null, $rep_red_id);
        
            $xml = new SimpleXMLElement($response); 

            if(isset($xml->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus))
            {
                
                if($xml->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus == '_DONE_')
                {
                    $ajax['status'] = true; 
                    $ajax['pro_status'] = $xml->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus; 
                    $ajax['gen_rep_id'] = $xml->GetReportRequestListResult->ReportRequestInfo->GeneratedReportId; 
                }
                else {
                    $ajax['status'] = false; 
                    $ajax['pro_status'] = $xml->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus; 
                }
            }
            else {
                $ajax['status']  = false;
                $ajax['message'] = show_alert('danger', $xml->Error->Message); 
            }
        }
        else {
            $ajax['status']  = false; 
            $ajax['message'] = show_alert('danger', "Amazon account details not found.");     
        }

        echo json_encode($ajax);
    }
}
?>