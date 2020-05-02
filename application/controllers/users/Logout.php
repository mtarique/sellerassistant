<?php 
/**
 * Logout active user
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Logout extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->library('session');
    }

    public function index()
    {
        // Destroy all session
		$this->session->sess_destroy();

		// Redirect to Login page
		redirect('users/login');
    }
}