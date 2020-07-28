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

    public function get_done_reports()
    {   
        // Set form validation rules
        $this->form_validation->set_rules('txtAmzAcctId', 'Amazon Account Id', 'required'); 

        if($this->form_validation->run() == true)
        {   
            // Amazon account id 
            $amz_acct_id = $this->input->post('txtAmzAcctId'); 
            $report_type = "_GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA_";

            // Get Amazon MWS Access keys by amazon account id
            $result = $this->amazon_model->get_mws_keys($amz_acct_id);

            if(!empty($result))
            {   
                // MWS API Keys
                $seller_id         = $this->encryption->decrypt($result[0]->seller_id); 
                $mws_auth_token    = $this->encryption->decrypt($result[0]->mws_auth_token); 
                $aws_access_key_id = $this->encryption->decrypt($result[0]->aws_access_key_id); 
                $secret_key        = $this->encryption->decrypt($result[0]->secret_key);

                $response = $this->reports->GetReportRequestList($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, 1, null, null, null, '_GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA_', '_DONE_');
        
                $xml = new SimpleXMLElement($response); 

                /*  */
                if(isset($xml->GetReportRequestListResult))
                {   
                    if(isset($xml->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus) && $xml->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus == "_DONE_")
                    {
                        $last_submitted_date = date("Y-m-d", strtotime($xml->GetReportRequestListResult->ReportRequestInfo->SubmittedDate)); 

                        //if((time()-(60*60*24)) < strtotime($submitted_date))
                        if(strtotime($last_submitted_date) < strtotime('-2 day'))
                        //if(strtotime($last_submitted_date) < )
                        {   
                            $processing['status']  = false; 
                            $processing['message'] = "REQUEST_REPORT";
                            $processing['text'] = "OLD REPORT - Request new report.".date("Y-m-d", strtotime('-1 day'));  
                        }
                        else {

                            // Get report
                            $get_report = $this->get_report($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $xml->GetReportRequestListResult->ReportRequestInfo->GeneratedReportId); 

                            if($get_report['status'] == true)
                            {
                                $processing['status'] = $get_report['status'];
                                $processing['message']   = $get_report['message'];   
                            }
                            else {
                                $processing['status'] = $get_report['status'];
                                $processing['message']   = $get_report['message'];   
                                $processing['text'] = "FORMAT ERROR - REQUEST NEW REPORT - ".$get_report['delim']; 
                            }
                            /*$processing['gen_rep_id'] = $xml->GetReportRequestListResult->ReportRequestInfo->GeneratedReportId;  */
                        }
                    }
                    else {
                        // Request a new report
                        $processing['status']  = false; 
                        $processing['message'] = "REQUEST_REPORT"; 
                        $processing['text'] = "NO DATA - Request new report."; 
                        /* $processing['message'] = show_alert('danger', "No done reports are available request a new one.");  */
                    }
                }
                else {
                    $processing['status']  = false;
                    $processing['message'] = show_alert('danger', $xml->Error->Message); 
                }
            }
            else {
                $processing['status']  = false; 
                $processing['message'] = show_alert('danger', "Amazon account details not found.");  
            }
        }
        else {
            $processing['status']  = false; 
            $processing['message'] = show_alert('danger', validation_errors()); 
        }

        echo json_encode($processing); 
    }

    /**
     * Undocumented function
     *
     * @param [type] $seller_id
     * @param [type] $mws_auth_token
     * @param [type] $aws_access_key_id
     * @param [type] $secret_key
     * @param [type] $report_type
     * @return void
     */
    public function request_report($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $report_type)
    {
        $response = $this->reports->RequestReport($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $report_type, null, date('c', strtotime('-72 hours')), date('c'));
        
        $xml = new SimpleXMLElement($response); 

        if(isset($xml->RequestReportResult->ReportRequestInfo->ReportRequestId))
        {
            $processing['status'] = true; 
            $processing['report_request_id'] = $xml->RequestReportResult->ReportRequestInfo->ReportRequestId; 
        }
        else {
            $processing['status']  = false;
            $processing['message'] = show_alert('danger', $xml->Error->Message); 
        }

        return $processing; 
    }

    /**
     * Get report 
     *
     * @return void
     */
    public function get_report($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $gen_rep_id)
    {
        
        $response = $this->reports->GetReport($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $gen_rep_id);

        if(!empty($response))
        {   
            // UTF8 encode response array
            $response = utf8_encode($response); 

            if($this->get_delimiter($response, 5) == '\t')
            {
                //$rows = explode("\n", $response); 
                $rows = str_getcsv($response, "\n"); 

                //$head = explode("\t", $rows[0]); 

                $html_table = '
                    <table class="table table-sm border border-grey-200 w50" id="tblFeePrev">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>FNSKU</th>
                                <th>ASIN</th>
                                <th>Product Name</th>
                            </tr>
                        </thead>
                        <tbody>
                '; 

                foreach(array_slice($rows, 1) as $row)
                {   
                    
                    //$data = explode("\t", $row); 
                    //$delim = '"""'.$this->get_delimiter($response, 5).'"""'; 
                    $data = str_getcsv($row, "\t"); 

                    $html_table .= '
                        <tr>
                            <td>'.$data[0].'</td>
                            <td>'.$data[1].'</td>
                            <td>'.$data[2].'</td>
                            <td>'.$data[3].'</td>
                        </tr>
                    '; 
                }
                
                $html_table .= '</tbody></table>'; 

                $processing['status'] = true; 
                $processing['message'] = $html_table; 
            }
            elseif($this->get_delimiter($response, 5) == ',')
            {
                //$rows = explode("\n", $response);
                $rows = str_getcsv($response, "\n"); 
                
                $html_table = '
                    <table class="table table-sm border border-grey-200 w50" id="tblFeePrev">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>FNSKU</th>
                                <th>ASIN</th>
                                <th>Product Name</th>
                            </tr>
                        </thead>
                        <tbody>
                '; 

                foreach(array_slice($rows, 1) as $row)
                {   

                    $data = str_getcsv($row, ",", '"'); 

                    $html_table .= '
                        <tr>
                            <td>'.utf8_encode($data[0]).'</td>
                            <td>'.utf8_encode($data[1]).'</td>
                            <td>'.utf8_encode($data[2]).'</td>
                            <td>'.utf8_encode($data[3]).'</td>
                        </tr>
                    '; 
                }

                $html_table .= '</tbody></table>'; 

                $processing['status'] = true; 
                $processing['message'] = $html_table; 
            }
            else {
                $processing['status'] = false; 
                $processing['message'] = "REQUEST_REPORT"; 
                $processing['delim'] = $this->get_delimiter($response, 5); 
            }
            
        }
        else {
            $processing['status'] = false; 
            $processing['message'] = show_alert('danger', "Preview report is not available."); 
        }

        return $processing;
    }

    public function get_delimiter($data, $checkLines = 2){
        //$file = new SplFileObject($file);
        $delimiters = array(
          ',', '\t', ';', '|',':'
        );

        $results = array();

        $i = 0;

        while($i <= $checkLines){
            $line = explode("\n", $data); 
            foreach ($delimiters as $delimiter){
                $regExp = '/['.$delimiter.']/';
                $fields = preg_split($regExp, $line[$i]);
                if(count($fields) > 1){
                    if(!empty($results[$delimiter])){
                        $results[$delimiter]++;
                    } else {
                        $results[$delimiter] = 1;
                    }   
                }
            }
           $i++;
        }

        $results = array_keys($results, max($results));
        return $results[0];
    }
}
?>