<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    public function index() {
        $this->load->view('user/overview');
	}
	public function login() {
		$this->load->view('user/login');
	}
    public function loginUser() {
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$result = $this->user->checkUserLogin($username, $password);
		if ($result != false){
			$logindata = array(
				'userID'	=> $result[0]->userID,
				'username'  => $result[0]->username,
				'email'     => $result[0]->userEmail,
				'userType'	=> $result[0]->admintype,
				'logged_in' => TRUE
			);
			$this->session->set_userdata($logindata);
			redirect('main/index');
		} else {
			$data = array('error_message' => 'Invalid Username or Password');
			$this->load->view('user/login', $data);
		}	
    }
    public function logout() {
		session_destroy();
		redirect('user/login');
    }
    public function register() {
        $data['corporationList'] = $this->user->corporationList();
        $this->load->view('user/registration', $data);
    }
    function registerUser(){		
		$postData = $this->input->post(NULL, TRUE);
		$resp = $this->user->registerUser($postData);
		if ($resp == TRUE){
			$data['confirm_mssge'] = 'User successfully added.';
			$this->load->view('user/registerconfirm', $data);
		} else {
			$data['confirm_mssge'] = 'User already exists.';
			$this->load->view('user/registerconfirm', $data);
		}
	}
}