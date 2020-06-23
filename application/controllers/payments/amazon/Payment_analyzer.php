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

        $this->load->library(array('mws/finances')); 
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
     * Get list of Amazon payments
     *
     * @return void
     */
    public function get_payments()
    {   
        // MWS request to ListFinancialEventGroups that contains all payments 
        // ORG
        //$response = $this->finances->ListFinancialEventGroups('QTFYV0dRQ1VTOTVEOTY=', 'YW16bi5td3MuOTZhNjljMDUtZWQyNy0xMjkzLTllZTktMmY0NjBmMzdhNmIy', 'QUtJQUpCTU1UVlVQVlJGTVVPNUE=', 'WUpiV2xSZEVFeW8xaHFYVmMxU0NSbVdVZHFQVmpKeDF0bTJ6L250dg==', '2020-06-01');
        // AL
        $response = $this->finances->ListFinancialEventGroups('QTFQSkswUkFJNzBVUTM=', 'YW16bi5td3MuZmNiOTNjNjEtMTgzNC05MTNlLTVjNjEtNDk2NTA2Zjk5N2Yw', 'QUtJQUlPNE1aSkhDTkNJNUdCWkE=', 'ZTI1MFh1RnhsNTBQcG5LRDl5czN4ei9TSEJGMzN6NGsvTmtCQ0piZQ==', '2020-06-01');

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
                            <a href="'.base_url('payments/amazon/payment_analyzer/view_transactions?fineventgrpid='.$event_group->FinancialEventGroupId).'" target="_blank" class="btn btn-xs btn-warning shadow-sm">View Transactions</a>
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
        
        echo json_encode($ajax); 
    }

    /**
     * View payment transactions page
     *
     * @return void
     */
    public function view_transactions()
    {
        $page_data['title'] = "Payment Transactions";
        $page_data['descr'] = "Transactional details for your Amazon payment."; 
        $page_data['fin_event_grp_id'] = $this->input->get('fineventgrpid'); 

        $this->load->view('payments/amazon/payment_transactions', $page_data);
    }

    /**
     * Get payment transactions using MWS API
     *
     * @return void
     */
    public function get_pmts_trans()
    {   
        // Financial event group id
        $fin_event_grp_id = $this->input->get('fineventgrpid');

        // MWS request to ListFinancialEvents
        $response = $this->finances->ListFinancialEvents('QTFQSkswUkFJNzBVUTM=', 'YW16bi5td3MuZmNiOTNjNjEtMTgzNC05MTNlLTVjNjEtNDk2NTA2Zjk5N2Yw', 'QUtJQUlPNE1aSkhDTkNJNUdCWkE=', 'ZTI1MFh1RnhsNTBQcG5LRDl5czN4ei9TSEJGMzN6NGsvTmtCQ0piZQ==', null, null, $fin_event_grp_id, null, null);

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
                $ajax['load_more']= '<button id="btnLoadMore" next-token="'.$xml->ListFinancialEventsResult->NextToken.'" class="btn btn-sm btn-dark">Load more...</button>'; 
            }
            else $ajax['load_more']; 
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
    public function get_pmts_trans_by_next_token()
    {   
        // Next token 
        $next_token = $this->input->get('nexttoken'); 

        // MWS request to ListFinancialEventsByNextToken
        $response = $this->finances->ListFinancialEventsByNextToken('QTFQSkswUkFJNzBVUTM=', 'YW16bi5td3MuZmNiOTNjNjEtMTgzNC05MTNlLTVjNjEtNDk2NTA2Zjk5N2Yw', 'QUtJQUlPNE1aSkhDTkNJNUdCWkE=', 'ZTI1MFh1RnhsNTBQcG5LRDl5czN4ei9TSEJGMzN6NGsvTmtCQ0piZQ==', $next_token);

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
                $ajax['load_more']= '<button id="btnLoadMore" next-token="'.$xml->ListFinancialEventsByNextTokenResult->NextToken.'" class="btn btn-sm btn-dark">Load more...</button>'; 
            }
            else $ajax['load_more'] = null; 
        }
        else {
            $ajax['status'] = false; 
            $ajax['message'] = '<tr><th colspan="7" class="text-center text-red">'.$xml->Error->Message.'</th></tr>';
        }

        echo json_encode($ajax); 
    }
}
?>