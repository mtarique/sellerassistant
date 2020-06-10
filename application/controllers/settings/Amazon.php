<?php 
/**
 * Amazon settings
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Amazon extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper('auth_helper');
    }

    public function index()
    {
        $page_data['title'] = "Amazon Settings";
        $page_data['descr'] = "Manage your amazon seller central account information."; 

        $this->load->view('settings/amazon_view', $page_data);
    }

    /**
     * View Amazon MWS developers settings page
     *
     * @return void
     */
    public function mws_developers()
    {
        $page_data['title'] = "Amazon MWS Developers";
        $page_data['descr'] = "Manage your Amazon MWS developer credentials."; 

        $this->load->view('settings/mws_developers', $page_data);
    }
}

?>