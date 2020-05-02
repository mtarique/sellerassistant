<?php 
/**
 * System errors
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Errors extends CI_Controller 
{
    /**
     * View javascript disabled error
     *
     * @return void
     */
    public function error_js()
    {
        $page_data['title'] = 'Javascript disabled!';
        $page_data['descr'] = 'It looks like javascript not running and set to disabled in your browser. <br>
        Please enable javascript in your browser\'s settings in order to access this site.';

        $this->load->view('sys/errors_view', $page_data); 
    }

    /**
     * View access permission error
     *
     * @return void
     */
    public function error_perms()
    {
        $page_data['title'] = 'Access denied!';
        $page_data['descr'] = 'You do not have enough privileges to access this page or function.<br>
                               Kindly contact system administrator.';

        $this->load->view('sys/errors_view', $page_data); 
    }

    /**
     * View session error 
     *
     * @return void
     */
    public function error_sess()
    {
        $page_data['title'] = 'Session expired!';
        $page_data['descr'] = 'Please click <a href=\'#\'>here</a> to login again.';

        $this->load->view('sys/errors_view', $page_data); 
    }
}
?>