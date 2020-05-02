<?php 
/**
 * User registration
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Controller 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed.'); 

class Register extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct(); 

        $this->load->model('users/users_model'); 
    }

    public function index()
    {
        $page_data['title'] = "Register";
        $page_data['descr'] = "Register for account."; 

        $this->load->view('users/register_view', $page_data);
    }

    /**
     * Create new user account
     *
     * @return void
     */
    public function create_account()
    {   
        // Set form validation rules 
        $this->form_validation->set_rules('inputName', 'Name', 'required');     
        $this->form_validation->set_rules('inputEmail', 'Email', 'required|valid_email');     
        $this->form_validation->set_rules('inputPassword', 'Password', 'required');    
        
        // Validate user inputs
        if($this->form_validation->run() == true)
        {   
            // Users data array where keys are same as users table field name
            // So that we don't need to reference field names in where clause
            $user_data = array(
                'name'          => $this->input->post('inputName'), 
                'email'         => $this->input->post('inputEmail'), 
                'password'      => password_hash($this->input->post('inputPassword'), PASSWORD_DEFAULT), 
                'registered_on' => date('Y-m-d') 
            ); 

            // Check if user already exist
            if($this->users_model->user_exist($user_data['email']) == false)
            {   
                // Add new user to database
                $result = $this->users_model->add_user($user_data); 

                if($result == "true")
                {
                    $ajax['status']  = true; 
                    $ajax['message'] = show_alert('success', 'Account created');
                }
                else {
                    $ajax['status']  = false; 
                    $ajax['message'] = show_alert('danger', $result); 
                }
            }
            else {
                $ajax['status']  = false; 
                $ajax['message'] = show_alert('danger', 'Email already exist.');
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