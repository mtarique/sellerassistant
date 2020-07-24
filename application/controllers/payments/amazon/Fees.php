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

        $this->load->library(array('mws/finances', 'encryption')); 

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
}
?>