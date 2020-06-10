<?php 
/**
 * Settings home page
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Home extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper('auth_helper');
    }

    public function index()
    {
        $page_data['title'] = "Settings";
        $page_data['descr'] = "Manage system settings."; 

        $this->load->view('settings/home_view', $page_data);
    }
}

?>