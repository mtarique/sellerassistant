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
    public function __construct()
    {
        parent::__construct(); 

        $this->load->database(); 
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

        return ($query) ? true : $this->db->error()['message'];
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

    /**
     * Get user details by user email
     *
     * @param   string  $email  User email
     * @return  void
     */
    public function get_user_by_email($email)
    {
        $query = $this->db->get_where('users', array('email' => $email));
        
        return ($query->num_rows() > 0) ? $query->result() : null; 
    }

    /**
     * Get user details by user id
     *
     * @param   integer  $userid  User id
     * @return  void
     */
    public function get_user_by_id($userid)
    {
        $query = $this->db->get_where('users', array('user_id' => $userid));
        
        return ($query->num_rows() > 0) ? $query->result() : null; 
    }
}

 ?>