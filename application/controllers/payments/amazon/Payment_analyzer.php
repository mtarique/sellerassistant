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
     * Get paymwent list
     *
     * @return void
     */
    public function get_payments_list()
    {   
        $report_type_list = array('_GET_V2_SETTLEMENT_REPORT_DATA_FLAT_FILE_V2_'); 
        
        $response = $this->reports->GetReportList('QTFYV0dRQ1VTOTVEOTY=', 'YW16bi5td3MuOTZhNjljMDUtZWQyNy0xMjkzLTllZTktMmY0NjBmMzdhNmIy', 'QUtJQUpCTU1UVlVQVlJGTVVPNUE=', 'WUpiV2xSZEVFeW8xaHFYVmMxU0NSbVdVZHFQVmpKeDF0bTJ6L250dg==', 2, $report_type_list);

        $xml = new SimpleXMLElement($response); 

        if(isset($xml->GetReportListResult->ReportInfo))
        {   
            $report_list = ''; 

            foreach($xml->GetReportListResult->ReportInfo as $report)
            {
                // Get settlement period & deposits
                $report_file = $this->reports->GetReport('QTFYV0dRQ1VTOTVEOTY=', 'YW16bi5td3MuOTZhNjljMDUtZWQyNy0xMjkzLTllZTktMmY0NjBmMzdhNmIy', 'QUtJQUpCTU1UVlVQVlJGTVVPNUE=', 'WUpiV2xSZEVFeW8xaHFYVmMxU0NSbVdVZHFQVmpKeDF0bTJ6L250dg==', $report->ReportId); 

                /* $report_list .= '
                    <tr>
                        <td>Pending</td>
                        <td>Pending</td>
                        <td>'.$report->ReportType.'</td>
                        <td>'.$report->ReportId.'</td>
                    </tr>
                '; */
                if(!empty($report_file)) 
                {
                    // Read the report
                    // Get rows from flat file report
                    $report_rows = explode("\n", $report_file); 

                    // Get heading by exploding first/top row
                    $report_cols = explode("\t", $report_rows[1]); 

                    $report_list .= '
                        <tr>
                            <td>'.$report_cols[1].' - '.$report_cols[2].'</td>
                            <td>$'.$report_cols[4].'</td>
                            <td>'.$report->ReportType.'</td>
                            <td>'.$report->ReportId.'</td>
                        </tr>
                    ';
                }
                else {
                    $report_list .= '
                        <tr>
                            <td>Error</td>
                            <td>Error</td>
                            <td>'.$report->ReportType.'</td>
                            <td>'.$report->ReportId.'</td>
                        </tr>
                    ';
                } 
            }

            if($xml->GetReportListResult->HasNext)
            {
                $ajax['load_more']= '<button id="btnLoadMore" next-token="'.$xml->GetReportListResult->NextToken.'" class="btn btn-sm btn-dark">Load more...</button>'; 
            }
    
            $ajax['status'] = true; 
            $ajax['report_list'] = $report_list; 
            
        }
        else {
            $ajax['status'] = false; 
            $ajax['message'] = '<tr><td colspan="2" class="text-center">No payments found!</td></tr>'; 
        }
 
        echo json_encode($ajax); 
    }

    /**
     * Get payments report list by next token
     *
     * @param   string  $next_token     Next token
     * @return  void
     */
    public function get_payment_list_by_next_token()
    {   
        $next_token = $this->input->get('nexttoken'); 

        $response = $this->reports->GetReportListByNextToken('QTFYV0dRQ1VTOTVEOTY=', 'YW16bi5td3MuOTZhNjljMDUtZWQyNy0xMjkzLTllZTktMmY0NjBmMzdhNmIy', 'QUtJQUpCTU1UVlVQVlJGTVVPNUE=', 'WUpiV2xSZEVFeW8xaHFYVmMxU0NSbVdVZHFQVmpKeDF0bTJ6L250dg==', $next_token);

        $xml = new SimpleXMLElement($response); 

        if(isset($xml->GetReportListByNextTokenResult->ReportInfo))
        {   
            $report_list = ''; 

            foreach($xml->GetReportListByNextTokenResult->ReportInfo as $report)
            {
                $report_list .= '
                    <tr>
                        <td>'.$report->ReportType.'</td>
                        <td>'.$report->ReportId.'</td>
                    </tr>'; 
            }

            if($xml->GetReportListByNextTokenResult->HasNext)
            {
                $ajax['load_more']= '<button id="btnLoadMore" next-token="'.$xml->GetReportListByNextTokenResult->NextToken.'" class="btn btn-sm btn-dark">Load more...</button>'; 
            }
    
            $ajax['status'] = true; 
            $ajax['report_list'] = $report_list;
        }
        else {
            $ajax['status'] = false; 
            //$ajax['message'] = '<tr><td colspan="2" class="text-center">No more payments reports!</td></tr>';
            $ajax['message'] = $response; 
        }
        /* $ajax['status'] = false; 
        $ajax['message'] = $response; */ 

        echo json_encode($ajax);
    }

    public function read_report()
    {

    }

}
?>