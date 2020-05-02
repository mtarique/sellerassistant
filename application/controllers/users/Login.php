<?php 
/**
 * User login
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Login extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->model('users/users_model'); 

        $this->load->library('session');
    }

    public function index()
    {
        $page_data['title'] = "Login";
        $page_data['descr'] = "Login to your ".$this->config->item('title')." account."; 

        $this->load->view('users/login_view', $page_data);
    }

    /**
     * Authenticate user and login
     *
     * @return void
     */
    public function authenticate()
    {
        // Set form validation rules 
        $this->form_validation->set_rules('inputEmail', 'Email', 'required|valid_email');     
        $this->form_validation->set_rules('inputPassword', 'Password', 'required');

        // Validate user inputs
        if($this->form_validation->run() == true)
        { 
            // Get user details by email and verify password
            $result = $this->users_model->get_user_by_email($this->input->post('inputEmail')); 

            if(!empty($result))
            {
                // Verify password
                if(password_verify($this->input->post('inputPassword'), $result[0]->password))
                {
                    // Set user session and redirect to home page
                    $sess_user = array(
                        '_userid'    => $result[0]->user_id, 
                        '_username'  => $result[0]->name,  
                        '_useremail' => $result[0]->email
                    ); 

                    $this->session->set_userdata($sess_user); 

                    $ajax['status']  = true; 
                    $ajax['message'] = '<script>window.location.href="'.base_url('home/dashboard').'"</script>';
                }
                else {
                    $ajax['status']  = false; 
                    $ajax['message'] = show_alert('danger', 'Incorrect password.'); 
                }
            }
            else {
                $ajax['status']  = false; 
                $ajax['message'] = show_alert('danger', 'Account does not exist.'); 
            }
        }
        else {
            $ajax['status']  = false; 
            $ajax['message'] = show_alert('danger', validation_errors());
        }

        echo json_encode($ajax);
    }
}
?>