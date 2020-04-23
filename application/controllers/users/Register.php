<?php 
/**
 * Register
 * 
 * @package 	CI
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Register extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function index()
    {
        $page_data['title'] = "Register";
        $page_data['descr'] = "Register for account."; 

        $this->load->view('users/register_view', $page_data);
    }
}
?>