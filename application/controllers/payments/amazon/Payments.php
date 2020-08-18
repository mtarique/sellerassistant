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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set('UTC');

class Payments extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper(array('auth_helper', 'fba_fees_calc_helper'));

        $this->load->library(array('mws/finances', 'encryption')); 

        $this->load->model(array('payments/amazon/payments_model', 'settings/channels/amazon_model', 'products/amazon/products_model'));  
    }

    /**
     * View Amazon payment analyzer page
     *
     * @return void
     */
    public function index()
    {
        $page_data['title'] = "Amazon Payments";
        $page_data['descr'] = "View and do analysis for your Amazon payments."; 

        $this->load->view('payments/amazon/payments', $page_data);
    }

    /**
     * Get Amazon payments table
     *
     * @return void
     */
    public function get_payments()
    {   
        // Set form validation rules
        $this->form_validation->set_rules('txtAmzAcctId', 'Amazon Account Id', 'required'); 
        $this->form_validation->set_rules('txtPmtDateFm', 'Payments From Date', 'required');
        
        if($this->form_validation->run() == true)
        {   
            // User inputs
            $amz_acct_id = $this->input->post('txtAmzAcctId'); 
            $pmt_date_fm = date("Y-m-d", strtotime($this->input->post('txtPmtDateFm')));
            $pmt_date_to = ($this->input->post('txtPmtDateTo') != '') ? date("Y-m-d", strtotime($this->input->post('txtPmtDateTo'))) : null;

            // Get Amazon MWS Access keys by amazon account id
            $result = $this->amazon_model->get_mws_keys($amz_acct_id);

            if(!empty($result)) 
            {
                // MWS Credentials
                $seller_id         = $this->encryption->decrypt($result[0]->seller_id); 
                $mws_auth_token    = $this->encryption->decrypt($result[0]->mws_auth_token); 
                $aws_access_key_id = $this->encryption->decrypt($result[0]->aws_access_key_id); 
                $secret_key        = $this->encryption->decrypt($result[0]->secret_key); 
                $curr_code         = $result[0]->curr_code; 
                
                // MWS request to ListFinancialEventGroups
                $response = $this->finances->ListFinancialEventGroups($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $pmt_date_fm, $pmt_date_to);

                $xml = new SimpleXMLElement($response);

                // Check if node exist
                if(isset($xml->ListFinancialEventGroupsResult->FinancialEventGroupList->FinancialEventGroup))
                {
                    $html = '
                        <table class="table table-hover table-sm small border border-grey-300" id="tblAmzPmts">
                            <thead>
                                <tr class="bg-light">
                                    <th class="align-middle text-center">Settlement Period</th>
                                    <th class="align-middle text-center">Deposit Currency</th>
                                    <th class="align-middle text-right">Deposit Amount</th>
                                    <th class="align-middle text-center">Fund Transfer Date</th>
                                    <th class="align-middle text-left">Processing Status</th>
                                    <th class="align-middle text-left">Fee Comparison</th>
                                </tr>
                            </thead><tbody>
                    ';
                    
                    foreach($xml->ListFinancialEventGroupsResult->FinancialEventGroupList->FinancialEventGroup as $event_group)
                    {   
                        if($event_group->OriginalTotal->CurrencyCode == $curr_code)
                        {
                            // Fund transfer date
                            $fund_transfer_date = (isset($event_group->FundTransferDate)) ? date('M d, Y', strtotime($event_group->FundTransferDate)) : ""; 

                            // Settlement period
                            if($event_group->ProcessingStatus == "Closed") 
                            {
                                $settlement_period = date('m/d/Y', strtotime($event_group->FinancialEventGroupStart)).' - '.date('m/d/Y', strtotime($event_group->FinancialEventGroupEnd)); 

                                // Get download link if comparison data already in database
                                $result = $this->payments_model->is_pmt_exist($event_group->FinancialEventGroupId); 

                                if($result == 1)
                                {
                                    // Dowloand button
                                    $comp_btn = '
                                        Already compared <i class="fas fa-check-circle text-success"></i><br>
                                        <a href="'.base_url('payments/amazon/payments/down_fee_comp_rpt?fineventgrpid='.$event_group->FinancialEventGroupId.'&amzacctid='.$amz_acct_id).'"
                                            class="btn-btn-sm-btn-success">Download Comparison Report</a>
                                    ';
                                }
                                else {
                                    // Comparison button
                                    $comp_btn = '
                                        <a href="#" 
                                            class="btn btn-sm btn-light border-grey-300 btn-comp-pmt-fees" 
                                            fin-event-grp-start="'.date('M d, Y', strtotime($event_group->FinancialEventGroupStart)).'" 
                                            fin-event-grp-end="'.date('M d, Y', strtotime($event_group->FinancialEventGroupEnd)).'" 
                                            fin-event-grp-id="'.$event_group->FinancialEventGroupId.'" 
                                            fin-event-curr="'.$event_group->OriginalTotal->CurrencyCode.'" 
                                            beg-bal-amt="'.$event_group->BeginningBalance->CurrencyAmount.'" 
                                            deposit-amt="'.$event_group->OriginalTotal->CurrencyAmount.'"
                                            fund-trf-date="'.$fund_transfer_date.'"
                                            amz-acct-id="'.$amz_acct_id.'">
                                            Compare Fees <i class="far fa-balance-scale text-secondary"></i>
                                        </a>
                                    '; 
                                }
                            }
                            elseif($event_group->ProcessingStatus == "Open")
                            {
                                $settlement_period = date('m/d/Y', strtotime($event_group->FinancialEventGroupStart)).' - '.date('m/d/Y'); 
                                $comp_btn = '<span class="text-muted">Not available for comparison</span>'; 
                            } 
                            else {
                                $settlement_period = date('m/d/Y', strtotime($event_group->FinancialEventGroupStart)).' - '.date('m/d/Y', strtotime($event_group->FinancialEventGroupEnd)); 
                                $comp_btn = '<span class="text-muted">Not available for comparison</span>'; 
                            }

                            // Html table rows
                            $html .= '
                                <tr class="py1">
                                    <td class="align-middle text-center py-3" data-sort="'.date('Ymd', strtotime($event_group->FinancialEventGroupStart)).'">
                                        <a href="'.base_url('payments/amazon/payments/view_pmt_trans?fineventgrpid='.$event_group->FinancialEventGroupId.'&amzacctid='.$amz_acct_id).'" title="View payment transaction" target="_blank">
                                        '.$settlement_period.'
                                        </a>
                                    </td>
                                    <td class="align-middle text-center">'.$event_group->OriginalTotal->CurrencyCode.'</td>
                                    <td class="align-middle text-right">'.$event_group->OriginalTotal->CurrencyAmount.'</td>
                                    <td class="align-middle text-center">'.$fund_transfer_date.'</td>
                                    <td class="align-middle text-left">'.$event_group->ProcessingStatus.'</td>
                                    <td class="align-middle text-left">'.$comp_btn.'</td>
                                </tr>
                            ';
                        }
                    }

                    $html .= '</tbody></table>';

                    $json_data['status']  = true; 
                    $json_data['message'] = $html;
                }
                else {
                    $json_data['status']  = false; 
                    $json_data['message'] = show_alert('danger', $xml->Error->Message);
                }
            }
            else {
                $json_data['status']  = false;
                $json_data['message'] = show_alert('danger', "Amazon account details not found, please select valid Amazon account.");
            }
        }
        else {
            $json_data['status']  = false;
            $json_data['message'] = show_alert('danger', validation_errors()); 
        }

        echo json_encode($json_data); 
    }

    /**
     * View payment transactions page
     *
     * @return void
     */
    public function view_pmt_trans()
    {
        $page_data['title']            = "Payment Transactions";
        $page_data['descr']            = "Transactional details for your Amazon payment."; 
        $page_data['fin_event_grp_id'] = $this->input->get('fineventgrpid'); 
        $page_data['amz_acct_id']      = $this->input->get('amzacctid'); 

        $this->load->view('payments/amazon/transactions', $page_data);
    }

    /**
     * Get payment transactions using MWS API
     *
     * @return void
     */
    public function get_pmt_trans()
    {   
        // Financial event group id
        $fin_event_grp_id = $this->input->get('fineventgrpid');
        $amz_acct_id      = $this->input->get('amzacctid'); 

        // Get Amazon MWS Access keys by amazon account id
        $result = $this->amazon_model->get_mws_keys($amz_acct_id);

        if(!empty($result)) 
        {
            $row = $result[0]; 

            $seller_id         = $this->encryption->decrypt($row->seller_id); 
            $mws_auth_token    = $this->encryption->decrypt($row->mws_auth_token); 
            $aws_access_key_id = $this->encryption->decrypt($row->aws_access_key_id); 
            $secret_key        = $this->encryption->decrypt($row->secret_key); 
        }

        // MWS request to ListFinancialEvents
        $response = $this->finances->ListFinancialEvents($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, null, null, $fin_event_grp_id, null, null);

        $xml = new SimpleXMLElement($response); 

        if(isset($xml->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent))
        {   
            // Html rows for payment transactions
            $pmts_trans_rows = ''; 

            // Loop through each shipment event
            foreach($xml->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent as $shipment_event)
            {   
                foreach($shipment_event->ShipmentItemList->ShipmentItem as $shipment_item)
                {   
                    // An array to hold amount description and amount
                    $amount = array(); 

                    // Item withheld taxes - Add to amount array
                    if(isset($shipment_item->ItemTaxWithheldList->TaxWithheldComponent->TaxesWithheld->ChargeComponent))
                    {
                        foreach($shipment_item->ItemTaxWithheldList->TaxWithheldComponent->TaxesWithheld->ChargeComponent as $tax_charge_component)
                        {   
                            // Use asXML() or trim() to convert XML object to string
                            $amount[$tax_charge_component->ChargeType->asXML()] = $tax_charge_component->ChargeAmount->CurrencyAmount; 
                        }
                    }

                    // Item charges - Add to amount array
                    if(isset($shipment_item->ItemChargeList->ChargeComponent))
                    {
                        foreach($shipment_item->ItemChargeList->ChargeComponent as $charge_component)
                        {   
                            $amount[$charge_component->ChargeType->asXML()] = $charge_component->ChargeAmount->CurrencyAmount; 
                        }
                    }

                    // Item fees - Add to amount array
                    if(isset($shipment_item->ItemFeeList->FeeComponent))
                    {
                        foreach($shipment_item->ItemFeeList->FeeComponent as $fee_component)
                        {   
                            $amount[trim($fee_component->FeeType)] = $fee_component->FeeAmount->CurrencyAmount; 
                        }
                    }

                    // Loop through each amount description 
                    foreach($amount as $desc => $amt)
                    {
                        $pmts_trans_rows .= '
                            <tr>
                                <td class="align-middle text-center">'.$shipment_event->AmazonOrderId.'</td>
                                <td class="align-middle text-center">'.$shipment_event->PostedDate.'</td>
                                <td class="align-middle text-left">'.$shipment_event->MarketplaceName.'</td>
                                <td class="align-middle text-center">'.$shipment_item->OrderItemId.'</td>
                                <td class="align-middle text-center">'.$shipment_item->SellerSKU.'</td>
                                <td class="align-middle text-center">'.$shipment_item->QuantityShipped.'</td>
                                <td class="align-middle text-left">'.$desc.'</td>
                                <td class="align-middle text-right">'.$amt.'</td>
                            </tr>
                        ';
                    }
                }
            }

            $ajax['status'] = true; 
            $ajax['transactions'] = $pmts_trans_rows; 

            // Show load more button if it has next token 
            if(isset($xml->ListFinancialEventsResult->NextToken)) 
            {   
                $ajax['load_more']= '<button id="btnLoadMore" next-token="'.$xml->ListFinancialEventsResult->NextToken.'" class="btn btn-sm btn-link mb-3">Load more payment transactions...</button>'; 
            }
        }
        else {
            $ajax['status'] = false; 
            $ajax['message'] = '<tr><th colspan="7" class="text-center text-red">'.$xml->Error->Message.'</th></tr>';
        }

        echo json_encode($ajax); 
    }

    /**
     * Get payment transactions by next token
     *
     * @return void
     */
    public function get_pmt_trans_by_next_token()
    {   
        // Next token 
        $next_token  = $this->input->get('nexttoken'); 
        $amz_acct_id = $this->input->get('amzacctid'); 

        // Get Amazon MWS Access keys by amazon account id
        $result = $this->amazon_model->get_mws_keys($amz_acct_id);

        if(!empty($result)) 
        {
            $row = $result[0]; 

            $seller_id         = $this->encryption->decrypt($row->seller_id); 
            $mws_auth_token    = $this->encryption->decrypt($row->mws_auth_token); 
            $aws_access_key_id = $this->encryption->decrypt($row->aws_access_key_id); 
            $secret_key        = $this->encryption->decrypt($row->secret_key); 
        }

        // MWS request to ListFinancialEventsByNextToken
        $response = $this->finances->ListFinancialEventsByNextToken($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $next_token);

        $xml = new SimpleXMLElement($response); 

        if(isset($xml->ListFinancialEventsByNextTokenResult->FinancialEvents->ShipmentEventList->ShipmentEvent))
        {   
            // Html rows for payment transactions
            $pmts_trans_rows = ''; 

            // Loop through each financial events
            foreach($xml->ListFinancialEventsByNextTokenResult->FinancialEvents->ShipmentEventList->ShipmentEvent as $shipment_event)
            {   
                foreach($shipment_event->ShipmentItemList->ShipmentItem as $shipment_item)
                {      
                    // An array to hold amount description and amount
                    $amount = array(); 

                    // Item withheld taxes - Add to amount array
                    if(isset($shipment_item->ItemTaxWithheldList->TaxWithheldComponent->TaxesWithheld->ChargeComponent)){
                        foreach($shipment_item->ItemTaxWithheldList->TaxWithheldComponent->TaxesWithheld->ChargeComponent as $tax_charge_component)
                        {   
                            // Use asXML() or trim() to convert XML object to string
                            $amount[$tax_charge_component->ChargeType->asXML()] = $tax_charge_component->ChargeAmount->CurrencyAmount; 
                        }
                    }

                    // Item charges - Add to amount array
                    if(isset($shipment_item->ItemChargeList->ChargeComponent))
                    {
                        foreach($shipment_item->ItemChargeList->ChargeComponent as $charge_component)
                        {   
                            // Use asXML() or trim() to convert XML object to string
                            $amount[$charge_component->ChargeType->asXML()] = $charge_component->ChargeAmount->CurrencyAmount; 
                        }
                    }

                    // Item fees - Add to amount array
                    if(isset($shipment_item->ItemFeeList->FeeComponent))
                    {
                        foreach($shipment_item->ItemFeeList->FeeComponent as $fee_component)
                        {   
                            $amount[trim($fee_component->FeeType)] = $fee_component->FeeAmount->CurrencyAmount; 
                        }
                    }
                    
                    // Loop through each amount description 
                    foreach($amount as $desc => $amt)
                    {
                        $pmts_trans_rows .= '
                            <tr>
                                <td class="align-middle text-center">'.$shipment_event->AmazonOrderId.'</td>
                                <td class="align-middle text-center">'.$shipment_event->PostedDate.'</td>
                                <td class="align-middle text-left">'.$shipment_event->MarketplaceName.'</td>
                                <td class="align-middle text-center">'.$shipment_item->OrderItemId.'</td>
                                <td class="align-middle text-center">'.$shipment_item->SellerSKU.'</td>
                                <td class="align-middle text-center">'.$shipment_item->QuantityShipped.'</td>
                                <td class="align-middle text-left">'.$desc.'</td>
                                <td class="align-middle text-right">'.$amt.'</td>
                            </tr>
                        ';
                    }
                }
            }

            $ajax['status'] = true; 
            $ajax['transactions'] = $pmts_trans_rows; 

            // Show load more button if it has next token 
            if(isset($xml->ListFinancialEventsByNextTokenResult->NextToken)) 
            {   
                $ajax['load_more']= '<button id="btnLoadMore" next-token="'.$xml->ListFinancialEventsByNextTokenResult->NextToken.'" class="btn btn-sm btn-link mb-3">Load more payment transactions...</button>'; 
            }
        }
        else {
            $ajax['status'] = false; 
            $ajax['message'] = '<tr><th colspan="7" class="text-center text-red">'.$xml->Error->Message.'</th></tr>';
        }

        echo json_encode($ajax); 
    }

    /**
     * Get payment fees and save in database
     *
     * @return void
     */
    public function get_pmt_fees()
    {   
        // URL parameters
        $fin_event_grp_id    = $this->input->get('fineventgrpid');
        $fin_event_grp_start = $this->input->get('fineventgrpstart');  
        $fin_event_grp_end   = $this->input->get('fineventgrpend');  
        $amz_acct_id         = $this->input->get('amzacctid');
        $fin_event_curr      = $this->input->get('fineventcurr');   
        $beg_bal_amt         = $this->input->get('begbalamt');   
        $deposit_amt         = $this->input->get('depositamt');   
        $fund_trf_date       = $this->input->get('fundtrfdate');   

        // Get MWS Access Keys for selecte amazon account
        $result = $this->amazon_model->get_mws_keys($amz_acct_id);

        if(!empty($result)) 
        {
            // MWS Keys
            $seller_id         = $this->encryption->decrypt($result[0]->seller_id); 
            $mws_auth_token    = $this->encryption->decrypt($result[0]->mws_auth_token); 
            $aws_access_key_id = $this->encryption->decrypt($result[0]->aws_access_key_id); 
            $secret_key        = $this->encryption->decrypt($result[0]->secret_key); 

            // MWS request to ListFinancialEvents
            $response = $this->finances->ListFinancialEvents($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, null, null, $fin_event_grp_id, null, null);

            $xml = new SimpleXMLElement($response); 

            if(isset($xml->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent))
            {  
                // Query to insert comp header
                $result = $this->payments_model->insert_amz_pmt_header($fin_event_grp_id, $fin_event_grp_start, $fin_event_grp_end, $amz_acct_id, $fin_event_curr, $beg_bal_amt, $deposit_amt, $fund_trf_date); 

                if($result == 1)
                {
                    $fees_data = array();   // Empty 2D array to hold FBA Fees Data

                    $i = 0; // Initialize loop counter
                    
                    // Loop through each shipment event
                    foreach($xml->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent as $shipment_event)
                    {   
                        foreach($shipment_event->ShipmentItemList->ShipmentItem as $shipment_item)
                        {   
                            // Item fees - Add to amount array
                            if(isset($shipment_item->ItemFeeList->FeeComponent))
                            {
                                // Loop through each amount description 
                                foreach($shipment_item->ItemFeeList->FeeComponent as $fee_component)
                                {   
                                    // Insert only if FeeType is FBAPerUnitFulfillmentFee
                                    if($fee_component->FeeType == 'FBAPerUnitFulfillmentFee')
                                    {   
                                        /* // Fetch weight and dimentions of the product
                                        $result = $this->products_model->get_fba_prod($amz_acct_id, $shipment_item->SellerSKU); 

                                        if(!empty($result)) 
                                        {
                                            $row = $result[0]; 

                                            $ls = $row->longest_side; 
                                            $ms = $row->median_side; 
                                            $ss = $row->shortest_side; 
                                            $wt = $row->pkgd_prod_wt/453.59237; // Gram to pound
                                            $dt = date('Y-m-d', strtotime($shipment_event->PostedDate));

                                            $FBAPerUnitFulfillmentFeeCalculated = get_fba_ful_fees($ls, $ms, $ss, $wt, $dt); 
                                            $calc_remarks = null; 
                                        }
                                        else {
                                            $FBAPerUnitFulfillmentFeeCalculated = 0;
                                            $calc_remarks = "MISSING_PRODUCT_DIMENSIONS"; 
                                        }  */

                                        // Add fees data to array rows
                                        $fees_data[$i]['fin_event_grp_id']    = $fin_event_grp_id; 
                                        $fees_data[$i]['amz_ord_id']          = $shipment_event->AmazonOrderId; 
                                        $fees_data[$i]['posted_date']         = date('Y-m-d H: m: i', strtotime($shipment_event->PostedDate)); 
                                        $fees_data[$i]['mp_name']             = $shipment_event->MarketplaceName; 
                                        $fees_data[$i]['ord_item_id']         = $shipment_item->OrderItemId; 
                                        $fees_data[$i]['seller_sku']          = $shipment_item->SellerSKU; 
                                        $fees_data[$i]['qty_shp']             = $shipment_item->QuantityShipped; 
                                        $fees_data[$i]['amt_type']            = "Fees"; 
                                        $fees_data[$i]['amt_desc']            = $fee_component->FeeType; 
                                        $fees_data[$i]['amt_curr']            = $fee_component->FeeAmount->CurrencyCode; 
                                        $fees_data[$i]['amount']              = $fee_component->FeeAmount->CurrencyAmount; 

                                        $i++; // Increment the loop counter
                                    }
                                }
                            }
                        }
                    }

                    // Query to insert FBA fees data
                    $result = $this->payments_model->insert_amz_pmt_details($fees_data);
                    
                    // Validate the query response
                    if($result == 1)
                    {
                        $json_data['status'] = true; 

                        // If response has next token
                        if(isset($xml->ListFinancialEventsResult->NextToken)) 
                        {   
                            $json_data['next_token']= $xml->ListFinancialEventsResult->NextToken;
                        }                
                        
                        $json_data['message'] = show_alert('success', "Comparison completed! Your report is ready to download."); 
                    }
                    else {
                        $json_data['status'] = false;  
                        $json_data['message'] = show_alert('danger', "FBA Fees data could not be saved.");
                    }
                }
                else {
                    $json_data['status'] = false;  
                    $json_data['message'] = show_alert('danger', "FBA fees comparison data could not be saved.");
                }
            }
            else {
                $json_data['status'] = false; 
                $json_data['message'] = show_alert('danger', $xml->Error->Message);
            }
        }
        else {
            $json_data['status']  = false;
            $json_data['message'] = show_alert('danger', "Amazon account details not found, please select valid Amazon account.");
        }

        echo json_encode($json_data); 
    }

    /**
     * Get and save FBA Fees data by Next Token
     * 
     * The saved fees data wiill be used later to compare fba fees
     * 
     * @return void
     */
    public function get_pmt_fees_by_next_token()
    {
        // URl parameters
        $next_token       = $this->input->get('nexttoken');
        $fin_event_grp_id = $this->input->get('fineventgrpid');
        $amz_acct_id      = $this->input->get('amzacctid'); 

        // Get MWS Access Keys for selecte amazon account
        $result = $this->amazon_model->get_mws_keys($amz_acct_id);

        if(!empty($result)) 
        {
            $row = $result[0]; 

            $seller_id         = $this->encryption->decrypt($row->seller_id); 
            $mws_auth_token    = $this->encryption->decrypt($row->mws_auth_token); 
            $aws_access_key_id = $this->encryption->decrypt($row->aws_access_key_id); 
            $secret_key        = $this->encryption->decrypt($row->secret_key); 
        }

        // MWS request to ListFinancialEventsByNextToken
        $response = $this->finances->ListFinancialEventsByNextToken($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $next_token);

        $xml = new SimpleXMLElement($response); 

        if(isset($xml->ListFinancialEventsByNextTokenResult->FinancialEvents->ShipmentEventList->ShipmentEvent))
        {   
            $fees_data = array(); // Empty 2D array to hold FBA Fees Data
            
            $i = 0; // Initialize loop counter

            // Loop through each financial events
            foreach($xml->ListFinancialEventsByNextTokenResult->FinancialEvents->ShipmentEventList->ShipmentEvent as $shipment_event)
            {   
                foreach($shipment_event->ShipmentItemList->ShipmentItem as $shipment_item)
                {      
                    // Item fees - Add to amount array
                    if(isset($shipment_item->ItemFeeList->FeeComponent))
                    {
                        foreach($shipment_item->ItemFeeList->FeeComponent as $fee_component)
                        {   
                            // Insert only if FeeType is FBAPerUnitFulfillmentFee
                            if($fee_component->FeeType == 'FBAPerUnitFulfillmentFee')
                            {   
                                /* // Fetch weight and dimentions of the product
                                $result = $this->products_model->get_fba_prod($amz_acct_id, $shipment_item->SellerSKU); 

                                if(!empty($result)) 
                                {
                                    $row = $result[0]; 

                                    $ls = $row->longest_side; 
                                    $ms = $row->median_side; 
                                    $ss = $row->shortest_side; 
                                    $wt = $row->pkgd_prod_wt/453.59237; // Gram to pound
                                    $dt = date('Y-m-d', strtotime($shipment_event->PostedDate));

                                    $FBAPerUnitFulfillmentFeeCalculated = get_fba_ful_fees($ls, $ms, $ss, $wt, $dt); 
                                    $calc_remarks = null; 
                                }
                                else {
                                    $FBAPerUnitFulfillmentFeeCalculated = 0;
                                    $calc_remarks = "MISSING_PRODUCT_DIMENSIONS"; 
                                }  */

                                // Add fees data to array rows
                                $fees_data[$i]['fin_event_grp_id']    = $fin_event_grp_id; 
                                $fees_data[$i]['amz_ord_id']          = $shipment_event->AmazonOrderId; 
                                $fees_data[$i]['posted_date']         = date('Y-m-d H: m: i', strtotime($shipment_event->PostedDate)); 
                                $fees_data[$i]['mp_name']             = $shipment_event->MarketplaceName; 
                                $fees_data[$i]['ord_item_id']         = $shipment_item->OrderItemId; 
                                $fees_data[$i]['seller_sku']          = $shipment_item->SellerSKU; 
                                $fees_data[$i]['qty_shp']             = $shipment_item->QuantityShipped; 
                                $fees_data[$i]['amt_type']            = "Fees"; 
                                $fees_data[$i]['amt_desc']            = $fee_component->FeeType; 
                                $fees_data[$i]['amt_curr']            = $fee_component->FeeAmount->CurrencyCode; 
                                $fees_data[$i]['amount']              = $fee_component->FeeAmount->CurrencyAmount; 
                    
                                $i++; // Increment the loop counter
                            }
                        }
                    }
                }
            }

            // Query to insert FBA fees data
            $result = $this->payments_model->insert_amz_pmt_details($fees_data);
            
            // Validate the query response
            if($result == 1)
            {
                $json_data['status'] = true; 
                
                // Show load more button if it has next token 
                if(isset($xml->ListFinancialEventsByNextTokenResult->NextToken)) 
                {   
                    $json_data['next_token']= $xml->ListFinancialEventsByNextTokenResult->NextToken; 
                }

                $json_data['message'] = show_alert('success', "Comparison completed! D."); 
            }
            else {
                $json_data['status'] = false;  
                $json_data['message'] = show_alert('danger', "FBA Fees data by Next Token could not be saved.");
            }
        }
        else {
            $json_data['status'] = false; 
            //$json_data['message'] = show_alert('danger', $xml->Error->Message);
            $json_data['message'] = show_alert('danger', "XML Response error");
        }

        echo json_encode($json_data); 
    }

    /**
     * Download fee comparison report 
     *
     * @return void
     */
    public function down_fee_comp_rpt()
    {
        // Finanicial event group id
        $fin_event_grp_id = $this->input->get('fineventgrpid');
        $amz_acct_id = $this->input->get('amzacctid');

        // New spreadsheet object
        $spreadsheet = new Spreadsheet(); 

        // Get active sheet
        $worksheet = $spreadsheet->getActiveSheet();  

        $worksheet->setCellValue("A2", "Report Date")
                  ->setCellValue("A3", "Account Name")
                  ->setCellValue("A4", "Sales Channel")
                  ->setCellValue("A5", "Settlement Period")
                  ->setCellValue("A6", "Deposit Amount")
                  ->setCellValue("A7", "Total Amazon Fees")
                  ->setCellValue("A8", "Total Calculated Fees");
        
        // Formatting report headings
        for ($i=2; $i < 9; $i++) { 
            // Merge cells
            $worksheet->mergeCells("A$i:B$i");

            // Heading alignment
            $worksheet->getStyle("A$i:C$i")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $worksheet->getStyle("A$i:C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $worksheet->getStyle("A$i:C$i")->getAlignment()->setWrapText(true); 
            $worksheet->getStyle("A$i:C$i")->getAlignment()->setShrinkToFit(true); 

            // Style the heading
            $worksheet->getStyle("A$i")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB("D9D9D9"); 
        }

        // Set default heading row
        $hn = 10; 

        // Set auto filter
        $worksheet->setAutoFilter("A$hn:J$hn");

        // Set heading row alignment
        $worksheet->getStyle("A$hn:J$hn")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $worksheet->getStyle("A$hn:J$hn")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle("A$hn:J$hn")->getAlignment()->setWrapText(true); 
        $worksheet->getStyle("A$hn:J$hn")->getAlignment()->setShrinkToFit(true); 

        // Set heading rows to bold
        $worksheet->getStyle("A$hn:J$hn")->getFont()->setBold(true);

        // Set auto column size
        //$worksheet->getColumnDimension('A')->setAutoSize(true);
        $worksheet->getColumnDimension('B')->setAutoSize(true);
        $worksheet->getColumnDimension('C')->setAutoSize(true);
        $worksheet->getColumnDimension('D')->setWidth(15);
        $worksheet->getColumnDimension('F')->setAutoSize(true);
        $worksheet->getColumnDimension('H')->setWidth(13);
        $worksheet->getColumnDimension('I')->setWidth(13);
        $worksheet->getColumnDimension('J')->setWidth(13);

        // Cells color array
        //$cell_colors["A$hn:J$hn"] = 'F2F2F2'; 
        $cell_colors["A$hn:J$hn"] = 'D9D9D9'; 

        // Loop through cells color array and set cells color
        foreach($cell_colors as $key => $val)
        {
            $worksheet->getStyle($key)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB($val); 
        }

        // Set worksheet title
        $worksheet->setTitle('Report');

        // Set header cells
        $worksheet->setCellValue("A$hn", "SKU") 
                ->setCellValue("B$hn", "ASIN")
                ->setCellValue("C$hn", "Amazon Order Id")
                ->setCellValue("D$hn", "Order Date (YYYY-MM-DD)")
                ->setCellValue("E$hn", "Qty Shp")
                ->setCellValue("F$hn", "Fee Type")
                ->setCellValue("G$hn", "Currency")
                ->setCellValue("H$hn", "Fee Amount - Amazon")
                ->setCellValue("I$hn", "Fee Amount - Calculated")
                ->setCellValue("J$hn", "Fee Difference"); 

        // Query to get fba fees comparison
        $result = $this->payments_model->get_fba_fees_comp($fin_event_grp_id); 

        if(!empty($result))
        {   
            $n = 11;

            $worksheet->freezePane("D$n");

            $worksheet->setCellValue("A1", "Amazon Payment - Fee Comparison Report")
                ->setCellValue("C2", date('m/d/Y'))
                ->setCellValue("C3", $result[0]->amz_acct_name)
                ->setCellValue("C4", $result[0]->sales_channel)
                ->setCellValue("C5", date('m/d/Y', strtotime($result[0]->fin_event_grp_start))." - ".date('m/d/Y', strtotime($result[0]->fin_event_grp_end)))
                ->setCellValue("C6", $result[0]->deposit_amt); 

            foreach($result as $row)
            {   
                // Fetch weight and dimentions of the product
                $result = $this->products_model->get_fba_prod($amz_acct_id, $row->seller_sku); 

                if(!empty($result)) 
                {   
                    $ls = $result[0]->longest_side; 
                    $ms = $result[0]->median_side; 
                    $ss = $result[0]->shortest_side; 
                    $wt = $result[0]->pkgd_prod_wt/453.59237; // Gram to pound
                    $dt = date('Y-m-d', strtotime($row->posted_date));

                    $FBAPerUnitFulfillmentFeeCalculated = get_fba_ful_fees($ls, $ms, $ss, $wt, $dt); 
                    $asin = $result[0]->asin; 
                }
                else {
                    $FBAPerUnitFulfillmentFeeCalculated = 0;
                    $asin = null; 
                }

                // FBA Fees Amazon vs Calculated
                $fba_fees_amz = $row->amount * -1; 
                $fba_fees_cal = $FBAPerUnitFulfillmentFeeCalculated * $row->qty_shp;

                // Highlight row if excess fees charged by Amazon
                if(($fba_fees_amz-$fba_fees_cal) > 0)
                {
                    $worksheet->getStyle("A$n:J$n")
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('ffc7ce'); 
                }

                // Set data rows heading alignment
                $worksheet->getStyle("A$n:J$n")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $worksheet->getStyle("A$n:E$n")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle("G$n")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $worksheet->getStyle("A$hn:J$hn")->getAlignment()->setWrapText(true); 
                $worksheet->getStyle("A$hn:J$hn")->getAlignment()->setShrinkToFit(true); 

                $worksheet->getStyle("H$n:J$n")->getNumberFormat()->setFormatCode('0.00');
                
                // Write data to worksheet
                $worksheet->setCellValue("A$n", $row->seller_sku)
                    ->setCellValue("B$n", $asin)
                    ->setCellValue("C$n", $row->amz_ord_id)
                    ->setCellValue("D$n", date('Y-m-d', strtotime($row->posted_date)))
                    ->setCellValue("E$n", $row->qty_shp)
                    ->setCellValue("F$n", $row->amt_desc)
                    ->setCellValue("G$n", $row->amt_curr)
                    ->setCellValue("H$n", $fba_fees_amz)
                    ->setCellValue("I$n", $fba_fees_cal)
                    ->setCellValue("J$n", "=H$n-I$n");

                $n++; 
            }

            $worksheet->setCellValue("C7", "=sum(H11:H$n)")
                      ->setCellValue("C8", "=sum(I11:I$n)");
        }
        else $worksheet->setCellValue('A2', "An error occurred");

        // Write data to excel
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="FeeCompPmtReport.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        ob_end_clean();
        $writer->save('php://output'); // download file
        exit();
    }
}
?>