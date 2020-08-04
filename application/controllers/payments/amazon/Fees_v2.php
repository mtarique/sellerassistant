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
                        // Take -2 day or -3 day for safety sake
                        if(strtotime($last_submitted_date) < strtotime('-2 day'))
                        {   
                            $processing = $this->request_report($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $report_type); 
                        }
                        else {
                            // Get Fee Preview report
                            $processing = $this->get_report($amz_acct_id, $seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $xml->GetReportRequestListResult->ReportRequestInfo->GeneratedReportId); 
                        }
                    }
                    else {
                        // Request a new report
                        $processing = $this->request_report($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $report_type); 
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
            $processing['status']     = true; 
            $processing['message']    = "REPORT_REQUESTED";
            $processing['rep_req_id'] = $xml->RequestReportResult->ReportRequestInfo->ReportRequestId; 
        }
        else {
            $processing['status']  = false;
            $processing['message'] = show_alert('danger', $xml->Error->Message); 
        }

        return $processing; 
    }

    /**
     * Get report status
     *
     * @return void
     */
    public function get_report_status()
    {
        $amz_acct_id = $this->input->get('txtAmzAcctId'); 
        $rep_req_id  = $this->input->get('repreqid'); 
        $report_type = "_GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA_";

        // Get Amazon MWS Access keys by amazon account id
        $result = $this->amazon_model->get_mws_keys($amz_acct_id);

        if(!empty($result)) 
        {
            $seller_id         = $this->encryption->decrypt($result[0]->seller_id); 
            $mws_auth_token    = $this->encryption->decrypt($result[0]->mws_auth_token); 
            $aws_access_key_id = $this->encryption->decrypt($result[0]->aws_access_key_id); 
            $secret_key        = $this->encryption->decrypt($result[0]->secret_key); 

            $response = $this->reports->GetReportRequestList($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, null, null, null, $rep_req_id);
        
            $xml = new SimpleXMLElement($response); 

            if(isset($xml->GetReportRequestListResult->ReportRequestInfo))
            {   
                if(isset($xml->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus))
                {
                    $report_status = $xml->GetReportRequestListResult->ReportRequestInfo->ReportProcessingStatus; 

                    if($report_status == "_DONE_")
                    {   
                        $ajax['status'] = true; 
                        $ajax['report_status'] = "_DONE_"; 
                    }
                    elseif($report_status == "_SUBMITTED_" || $report_status == "_IN_PROGRESS_")
                    {   
                        $ajax['status'] = true; 
                        $ajax['report_status'] = "_IN_PROGRESS_"; 
                    }
                    else {
                        $ajax['status'] = false;
                        $ajax['message'] = show_alert('danger', "Report cancelled or have no data in it.");
                    }
                }
                else {
                    $ajax['status']  = false;
                    $ajax['message'] = show_alert('danger', "Report request error."); 
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

    /**
     * Get report 
     *
     * @return void
     */
    public function get_report($amz_acct_id, $seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $gen_rep_id)
    {
        // GetReport MWS Reports API
        $response = $this->reports->GetReport($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $gen_rep_id);

        if(!empty($response))
        {   
            // UTF8 encode response array
            $response = utf8_encode($response); 

            // Break the response in rows
            $rows = str_getcsv($response, "\n"); 

            $html_table = '
                <table class="table table-sm border border-grey-200 table-bordered" id="tblFeePrev">
                    <thead>
                        <tr>
                            <th class="align-middle small font-weight-bold">Product Name</th>
                            <th class="align-middle small font-weight-bold">SKU</th>
                            <th class="align-middle small font-weight-bold">ASIN</th>
                            <th class="align-middle small font-weight-bold">LS</th>
                            <th class="align-middle small font-weight-bold">MS</th>
                            <th class="align-middle small font-weight-bold">SS</th>
                            <!--<th class="align-middle small font-weight-bold">Dimension</th>-->
                            <th class="align-middle small font-weight-bold">WT</th>
                            <th class="align-middle small font-weight-bold">Size Tier</th>
                            <th class="align-middle small font-weight-bold">FBA Fees</th>
                            <th class="align-middle small font-weight-bold">LS</th>
                            <th class="align-middle small font-weight-bold">MS</th>
                            <th class="align-middle small font-weight-bold">SS</th>
                            <th class="align-middle small font-weight-bold">WT</th>
                            <th class="align-middle small font-weight-bold">Size Tier</th>
                            <th class="align-middle small font-weight-bold">Calculated FBA Fees</th>
                            <th class="align-middle small font-weight-bold">FBA Fees Difference</th>
                        </tr>
                    </thead>
                    <tbody>
            '; 

            // Loop through response rows slicing first heading row
            foreach(array_slice($rows, 1) as $row)
            {   
                // Extract cells data from row
                $data = ($this->get_delimiter($response, 5) == '\t') ? str_getcsv($row, "\t") : str_getcsv($row, ","); 

                $ProdDimAmz = number_format($data[9], 2).' x '.number_format($data[10], 2).' x '.number_format($data[11], 2);

                // Get own product dimensions and calculate FBA Fees
                $result = $this->products_model->get_fba_prod($amz_acct_id, $data[0]); 

                if(!empty($result)) 
                {
                    $ls = $result[0]->pkgd_prod_ls; 
                    $ms = $result[0]->pkgd_prod_ms; 
                    $ss = $result[0]->pkgd_prod_ss; 
                    $wt = $result[0]->pkgd_prod_wt/453.59237; // Gram to pound
                    $dt = date('Y-m-d');

                    //$ProdDimOwn = number_format($ls, 2).' x '.number_format($ms, 2).' x '.number_format($ss, 2);
                    $SizeTierCalculated = get_size_tier(get_size_code($ls, $ms, $ss, $wt, $dt), $dt);  
                    $FBAPerUnitFulfillmentFeeCalculated = get_fba_ful_fees($ls, $ms, $ss, $wt, $dt); 
                }
                else {
                    $ls = 0.00; 
                    $ms = 0.00; 
                    $ss = 0.00; 
                    $wt = 0.00; 

                    //$ProdDimOwn = number_format($ls, 2).' x '.number_format($ms, 2).' x '.number_format($ss, 2);
                    $SizeTierCalculated = "--"; 
                    $FBAPerUnitFulfillmentFeeCalculated = 0;
                } 

                $excess_marker = (($data[24]-$FBAPerUnitFulfillmentFeeCalculated) > 0) ? 'bg-red-200' : 'bg-green-200'; 
                $html_table .= '
                    <tr>
                        <td class="align-middle text-left w-50">
                            <div class="d-flex jutify-content-between align-items-center">
                                <div class="col-md-2">
                                    <img src="https://ws-na.amazon-adsystem.com/widgets/q?_encoding=UTF8&MarketPlace=US&ASIN='.$data[2].'&ServiceVersion=20070822&ID=AsinImage&WS=1&Format=_SL250_" alt="Loading..." class="float-left prod-img"/>
                                </div>
                                <div class="col-md-10 small">
                                    '.$data[3].'
                                </div>
                            </div>
                        </td>
                        <td class="align-middle text-center">'.$data[0].'</td>
                        <td class="align-middle text-center">'.$data[2].'</td>
                        <td class="align-middle text-center">'.$data[9].'</td>
                        <td class="align-middle text-center">'.$data[10].'</td>
                        <td class="align-middle text-center">'.$data[11].'</td>
                        <!--<td class="align-middle text-center">
                            '.$ProdDimAmz.'
                            <div class="d-flex flex-column">
                                <span class="text-nowrap"></span>
                                <span class="text-nowrap"></span>
                            </div>
                        </td>-->
                        <td class="align-middle text-center">'.$data[14].'</td>
                        <td class="align-middle text-left">'.$data[16].'</td>
                        <td class="align-middle text-center">'.$data[24].'</td>

                        <td class="align-middle text-center">'.number_format($ls, 2).'</td>
                        <td class="align-middle text-center">'.number_format($ms, 2).'</td>
                        <td class="align-middle text-center">'.number_format($ss, 2).'</td>
                        <td class="align-middle text-center">'.number_format($wt, 2).'</td>
                        <td class="align-middle text-center">'.$SizeTierCalculated.'</td>

                        <td class="align-middle text-center">'.number_format((float)$FBAPerUnitFulfillmentFeeCalculated, '2', '.', '').'</td>
                        <td class="align-middle text-center '.$excess_marker.'">'.number_format((float)($data[24]-$FBAPerUnitFulfillmentFeeCalculated), '2', '.', '').'</td>
                    </tr>
                '; 
            }
            
            $html_table .= '</tbody></table>'; 

            //return $html_table; 
            $processing['status']  = true;
            $processing['message'] = 'REPORT_GENERATED';
            $processing['report']  = $html_table;
        }
        else {
            $processing['status']  = false; 
            $processing['message'] = show_alert('danger', "FBA Fee Preview report is not available, please try again."); 
        }

        return $processing; 
    }

    /**
     * Undocumented function
     *
     * @param [type] $data
     * @param integer $checkLines
     * @return void
     */
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