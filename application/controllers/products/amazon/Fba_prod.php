<?php 
/**
 * Amazon FBA products
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Fba_prod extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper('auth_helper');

        $this->load->library('encryption');

        $this->load->model('settings/channels/amazon_model'); 
    }
    
    /**
     * View manage FBA products page
     *
     * @return void
     */
    public function index()
    {
        $page_data['title'] = "Manage FBA Products";
        $page_data['descr'] = "Manage Amazon FBA products for your selected Amazon account."; 

        $this->load->view('products/amazon/fba_prod', $page_data);
    }

}

?>