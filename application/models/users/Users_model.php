<?php
/**
 * Users model
 * 
 * @package 	Codeigniter
 * @version     3.1.11
 * @subpackage 	Model 
 * @author 		MD TARIQUE ANWER | mtarique@outlook.com
 */
defined('BASEPATH') or exit('No direct script access allowed'); 

class Users_model extends CI_Model 
{   
    public $db_error; 

    public function __construct()
    {
        parent::__construct(); 

        $this->load->database(); 

        //$this->db_error = $this->db->error(); 
    }

    /**
     * Add new user to database
     *
     * @param   array   $user_data  New user data
     * @return  void
     */
    public function add_user($user_data)
    {    
        $query = $this->db->insert('users', $user_data); 

        /* if(!$query)
        {
            
        }
        else return true;  */

        /* if($query) return true; 
        else {
            $db_error = $this->db->error();

            return $db_error['code'].": ".$db_error['message'];
        } */

        $this->db_error = $this->db->error(); 
        return ($query) ? true : $this->db_error['message'];
    }

    /**
     * Check user exist
     *
     * @param   string  $email  User email
     * @return  boolean
     */
    public function user_exist($email)
    {
        $query = $this->db->get_where('users', array('email' => $email)); 

        return ($query->num_rows() > 0) ? true : false; 
    }
}

 ?>