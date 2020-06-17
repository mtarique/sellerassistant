<?php 
/**
 * Amazon payment analyzer
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Payment_analyzer extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper('auth_helper');

        $this->load->library('mws/reports'); 
    }

    /**
     * View Amazon payment analyzer page
     *
     * @return void
     */
    public function index()
    {
        $page_data['title'] = "Payment Analyzer";
        $page_data['descr'] = "Let you anazlyze your Amazon payments."; 

        $this->load->view('payments/amazon/payment_analyzer_view', $page_data);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function get_payments_list()
    {   
        $report_type_list = array('_GET_V2_SETTLEMENT_REPORT_DATA_FLAT_FILE_V2_', '_GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA_', '_GET_V2_SETTLEMENT_REPORT_DATA_XML_'); 
        
        $response = $this->reports->GetReportList('QTFYV0dRQ1VTOTVEOTY=', 'YW16bi5td3MuOTZhNjljMDUtZWQyNy0xMjkzLTllZTktMmY0NjBmMzdhNmIy', 'QUtJQUpCTU1UVlVQVlJGTVVPNUE=', 'WUpiV2xSZEVFeW8xaHFYVmMxU0NSbVdVZHFQVmpKeDF0bTJ6L250dg==', $report_type_list);

        $xml = new SimpleXMLElement($response); 

        if(isset($xml->GetReportListResult->ReportInfo))
        {   
            $report_list = ''; 

            foreach($xml->GetReportListResult->ReportInfo as $report)
            {
                $report_list .= '
                    <tr>
                        <td>'.$report->ReportType.'</td>
                        <td>'.$report->ReportId.'</td>
                    </tr>'; 
            }

            if($xml->GetReportListResult->HasNext)
            {
                $ajax['load_more']= '<a href="#" id="btnLoadMore" next-token="'.$xml->GetReportListResult->NextToken.'" class="btn btn-sm btn-dark">Load more...</a>'; 
            }
    
            $ajax['status'] = true; 
            $ajax['report_list'] = $report_list; 
            
        }
        else {
            $ajax['status'] = false; 
            $ajax['message'] = '<tr><td colspan="2" class="text-center">No payments found!</td></tr>'; 
            //$ajax['message'] = $xml; 
        }
 
        echo json_encode($ajax); 
    }

}
?>