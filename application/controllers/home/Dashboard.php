<?php 
/**
 * Dashboard
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Dashboard extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->helper('auth_helper');
    }

    public function index()
    {
        $page_data['title'] = "Dashboard";
        $page_data['descr'] = "Welcome to ".$this->config->item('title'); 

        $this->load->view('home/dashboard_view', $page_data);
    }
}