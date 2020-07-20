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

date_default_timezone_set('UTC');

class Amazon_payments extends CI_Controller 
{
    private $my_var; 

    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper(array('auth_helper', 'fba_fees_calc_helper'));

        $this->load->library(array('mws/finances', 'encryption')); 

        $this->load->model(array('payments/amazon/payments_model', 'settings/channels/amazon_model', 'products/amazon/products_model')); 

        $this->my_var = "HelloWorld"; 
    }

    /**
     * View Amazon payment analyzer page
     *
     * @return void
     */
    public function index()
    {
        $page_data['title'] = "Amazon Payments";
        $page_data['descr'] = "View and do analysis for your Amazon payments.".$this->my_var; 

        $this->load->view('payments/amazon/statements', $page_data);
    }

    /**
     * View payments or settlement reports
     */
    public function view_payments()
    {
        $this->form_validation->set_rules('inputAmzAcctId', 'Amazon Account ID', 'required'); 
        $this->form_validation->set_rules('inputPmtDateFm', 'From Date', 'required');
        
        if($this->form_validation->run() == true)
        {   
            $amz_acct_id = $this->input->post('inputAmzAcctId'); 
            $pmt_date_fm = date("Y-m-d", strtotime($this->input->post('inputPmtDateFm')));
            $pmt_date_to = ($this->input->post('inputPmtDateTo') != '') ? date("Y-m-d", strtotime($this->input->post('inputPmtDateTo'))) : null;

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

            // MWS request to ListFinancialEventGroups
            $response = $this->finances->ListFinancialEventGroups($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, $pmt_date_fm, $pmt_date_to);

            $xml = new SimpleXMLElement($response); 

            if(isset($xml->ListFinancialEventGroupsResult->FinancialEventGroupList->FinancialEventGroup))
            {   
                // Html payment rows
                $pmt_rows = ''; 

                foreach($xml->ListFinancialEventGroupsResult->FinancialEventGroupList->FinancialEventGroup as $event_group)
                {
                    if($event_group->ProcessingStatus == "Open")
                    {
                        $settlement_period = date('M d, Y', strtotime($event_group->FinancialEventGroupStart)).' - '.date('M d, Y'); 
                    } 
                    else $settlement_period = date('M d, Y', strtotime($event_group->FinancialEventGroupStart)).' - '.date('M d, Y', strtotime($event_group->FinancialEventGroupEnd)); 

                    $fund_transfer_date = (isset($event_group->FundTransferDate)) ? date('M d, Y', strtotime($event_group->FundTransferDate)) : ""; 
                    $pmt_rows .= '
                        <tr>
                            <td class="align-middle text-left">'.$settlement_period.'</td>
                            <td class="align-middle text-right">'.$event_group->OriginalTotal->CurrencyCode.' '.$event_group->OriginalTotal->CurrencyAmount.'</td>
                            <td class="align-middle text-center">'.$fund_transfer_date.'</td>
                            <td class="align-middle text-left">'.$event_group->ProcessingStatus.'</td>
                            <td class="align-middle text-center">
                                <a href="'.base_url('payments/amazon_payments/view_transactions?fineventgrpid='.$event_group->FinancialEventGroupId.'&amzacctid='.$amz_acct_id).'" target="_blank" class="btn btn-xs btn-warning shadow-sm">View Transactions</a>
                                <a href="#" 
                                    class="btn btn-xs btn-warning shadow-sm btn-comp-fba-fees" 
                                    fin-event-grp-start="'.date('M d, Y', strtotime($event_group->FinancialEventGroupStart)).'" 
                                    fin-event-grp-end="'.date('M d, Y', strtotime($event_group->FinancialEventGroupEnd)).'" 
                                    fin-event-grp-id="'.$event_group->FinancialEventGroupId.'" 
                                    amz-acct-id="'.$amz_acct_id.'">
                                Compare FBA Fees</a>
                            </td>
                        </tr>
                    ';
                }

                $ajax['status'] = true; 
                $ajax['report_list'] = $pmt_rows; 
            }
            else {
                $ajax['status'] = false; 
                $ajax['message'] = '<tr><th colspan="5" class="text-center text-red">'.$xml->Error->Message.'</th></tr>';
            } 
        }
        else {
            $ajax['status'] = false;
            $ajax['message'] = '<tr><th colspan="5" class="text-center text-red">'.validation_errors().'</th></tr>';
        }

        echo json_encode($ajax);
    }

    /**
     * View payment transactions page
     *
     * @return void
     */
    public function view_transactions()
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
     * Get and save FBA Fees data 
     * 
     * The saved fees data wiill be used later to compare fba fees
     *
     * @return void
     */
    public function fetch_fba_fees()
    {   
        // Financial event group id
        $fin_event_grp_id    = $this->input->get('fineventgrpid');
        $fin_event_grp_start = $this->input->get('fineventgrpstart');  
        $fin_event_grp_end   = $this->input->get('fineventgrpend');  
        $amz_acct_id         = $this->input->get('amzacctid'); 

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

        // MWS request to ListFinancialEvents
        $response = $this->finances->ListFinancialEvents($seller_id, $mws_auth_token, $aws_access_key_id, $secret_key, null, null, $fin_event_grp_id, null, null);

        $xml = new SimpleXMLElement($response); 

        if(isset($xml->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent))
        {  
            // Query to insert comp header
            $result = $this->payments_model->insert_fba_fees_comp_header($fin_event_grp_id, $fin_event_grp_start, $fin_event_grp_end, $amz_acct_id); 

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
                                    // Fetch weight and dimentions of the product
                                    $result = $this->products_model->get_fba_prod($amz_acct_id, $shipment_item->SellerSKU); 

                                    if(!empty($result)) 
                                    {
                                        $row = $result[0]; 

                                        $ls = $row->pkgd_prod_ls; 
                                        $ms = $row->pkgd_prod_ms; 
                                        $ss = $row->pkgd_prod_ss; 
                                        $wt = $row->pkgd_prod_wt/453.59237; // Gram to pound
                                        $dt = date('Y-m-d', strtotime($shipment_event->PostedDate));

                                        $FBAPerUnitFulfillmentFeeCalculated = get_fba_ful_fees($ls, $ms, $ss, $wt, $dt); 
                                        $calc_remarks = null; 
                                    }
                                    else {
                                        $FBAPerUnitFulfillmentFeeCalculated = 0;
                                        $calc_remarks = "MISSING_PRODUCT_DIMENSIONS"; 
                                    } 

                                    // Add fees data to array rows
                                    $fees_data[$i]['fin_event_grp_id']    = $fin_event_grp_id; 
                                    $fees_data[$i]['amz_ord_id']          = $shipment_event->AmazonOrderId; 
                                    $fees_data[$i]['posted_date']         = date('Y-m-d H: m: i', strtotime($shipment_event->PostedDate)); 
                                    $fees_data[$i]['mp_name']             = $shipment_event->MarketplaceName; 
                                    $fees_data[$i]['ord_item_id']         = $shipment_item->OrderItemId; 
                                    $fees_data[$i]['seller_sku']          = $shipment_item->SellerSKU; 
                                    $fees_data[$i]['qty_shp']             = $shipment_item->QuantityShipped; 
                                    $fees_data[$i]['fee_type']            = $fee_component->FeeType; 
                                    $fees_data[$i]['fee_curr']            = $fee_component->FeeAmount->CurrencyCode; 
                                    $fees_data[$i]['fee_amt']             = $fee_component->FeeAmount->CurrencyAmount; 
                                    $fees_data[$i]['calc_fee_amt']        = $FBAPerUnitFulfillmentFeeCalculated*$shipment_item->QuantityShipped;   
                                    $fees_data[$i]['calc_remarks']        = $calc_remarks; 

                                    $i++; // Increment the loop counter
                                }
                            }
                        }
                    }
                }

                // Query to insert FBA fees data
                $result = $this->payments_model->insert_fba_fees_comp_details($fees_data);
                
                // Validate the query response
                if($result == 1)
                {
                    $ajax['status'] = true; 

                    // If response has next token
                    if(isset($xml->ListFinancialEventsResult->NextToken)) 
                    {   
                        $ajax['next_token']= $xml->ListFinancialEventsResult->NextToken;
                    }                
                    
                    $ajax['message'] = show_alert('success', "Comparison completed! Your report is ready to download."); 
                }
                else {
                    $ajax['status'] = false;  
                    $ajax['message'] = show_alert('danger', "FBA Fees data could not be saved.");
                }
            }
            else {
                $ajax['status'] = false;  
                $ajax['message'] = show_alert('danger', "FBA fees comparison data could not be saved.");
            }
        }
        else {
            $ajax['status'] = false; 
            $ajax['message'] = show_alert('danger', $xml->Error->Message);
        }

        echo json_encode($ajax); 
    }

    /**
     * Get and save FBA Fees data by Next Token
     * 
     * The saved fees data wiill be used later to compare fba fees
     * 
     * @return void
     */
    public function fetch_fba_fees_by_next_token()
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
                                // Fetch weight and dimentions of the product
                                $result = $this->products_model->get_fba_prod($amz_acct_id, $shipment_item->SellerSKU); 

                                if(!empty($result)) 
                                {
                                    $row = $result[0]; 

                                    $ls = $row->pkgd_prod_ls; 
                                    $ms = $row->pkgd_prod_ms; 
                                    $ss = $row->pkgd_prod_ss; 
                                    $wt = $row->pkgd_prod_wt/453.59237; // Gram to pound
                                    $dt = date('Y-m-d', strtotime($shipment_event->PostedDate));

                                    $FBAPerUnitFulfillmentFeeCalculated = get_fba_ful_fees($ls, $ms, $ss, $wt, $dt); 
                                    $calc_remarks = null; 
                                }
                                else {
                                    $FBAPerUnitFulfillmentFeeCalculated = 0;
                                    $calc_remarks = "MISSING_PRODUCT_DIMENSIONS"; 
                                } 

                                // Add fees data to array rows
                                $fees_data[$i]['fin_event_grp_id']    = $fin_event_grp_id; 
                                $fees_data[$i]['amz_ord_id']          = $shipment_event->AmazonOrderId; 
                                $fees_data[$i]['posted_date']         = date('Y-m-d H: m: i', strtotime($shipment_event->PostedDate)); 
                                $fees_data[$i]['mp_name']             = $shipment_event->MarketplaceName; 
                                $fees_data[$i]['ord_item_id']         = $shipment_item->OrderItemId; 
                                $fees_data[$i]['seller_sku']          = $shipment_item->SellerSKU; 
                                $fees_data[$i]['qty_shp']             = $shipment_item->QuantityShipped; 
                                $fees_data[$i]['fee_type']            = $fee_component->FeeType; 
                                $fees_data[$i]['fee_curr']            = $fee_component->FeeAmount->CurrencyCode; 
                                $fees_data[$i]['fee_amt']             = $fee_component->FeeAmount->CurrencyAmount; 
                                $fees_data[$i]['calc_fee_amt']        = $FBAPerUnitFulfillmentFeeCalculated*$shipment_item->QuantityShipped;   
                                $fees_data[$i]['calc_remarks']        = $calc_remarks;   

                                $i++; // Increment the loop counter
                            }
                        }
                    }
                }
            }

            // Query to insert FBA fees data
            $result = $this->payments_model->insert_fba_fees_comp_details($fees_data);
            
            // Validate the query response
            if($result == 1)
            {
                $ajax['status'] = true; 
                
                // Show load more button if it has next token 
                if(isset($xml->ListFinancialEventsByNextTokenResult->NextToken)) 
                {   
                    $ajax['next_token']= $xml->ListFinancialEventsByNextTokenResult->NextToken; 
                }

                $ajax['message'] = show_alert('success', "Comparison completed! Your report is ready to download."); 
            }
            else {
                $ajax['status'] = false;  
                $ajax['message'] = show_alert('danger', "FBA Fees data by Next Token could not be saved.");
            }
        }
        else {
            $ajax['status'] = false; 
            //$ajax['message'] = show_alert('danger', $xml->Error->Message);
            $ajax['message'] = show_alert('danger', "XML Response error");
        }

        echo json_encode($ajax); 
    }
}
?>