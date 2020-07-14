<?php 
/**
 * MWS Credentials and Information Helper
 * 
 * Authenticates active session, role, permissions etc.
 * 
 * Call this helper inside contructor function only if required as shown below: 
 * $this->load->helper('auth_helper'); 
 * 
 * NOTE: Don't included any helper or library that has already been
 * called in autoload. 
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Helper 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

// Get instance, access CI superobject
$CI = & get_instance(); 

$CI->load->library(array('session', 'user_agent')); 

$CI->load->helper('url');

//$CI->load->model('mws_model'); 

// Check active session 
if(!$CI->session->userdata('_username'))
{
    redirect('sys/errors/error_sess'); 
}