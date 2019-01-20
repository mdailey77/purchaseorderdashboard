<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('header'); ?>    
    <div class="row  mt-5 mb-3">
        <div class="col-md-5 offset-md-3">
            <h3 class="mt-3 mb-5">Login</h3> 
            <?php                         
            $formattributes = array('id'=>'loginForm');
            $usernamedata = array(
                'name'          => 'username',
                'id'            => 'username',
                //'value'         => $userDetail->userEmail,
                'class'         => 'form-control',
            );
            $passworddata = array(
                'name'          => 'password',
                'id'            => 'password',
                //'value'         => $userDetail->userPassword,
                'class'         => 'form-control',
            );
            
            $submitdata = array('name'=>'login','class'=> 'btn btn-primary','content'=>'Login','type'=>'submit','id'=>'login');
            $labeldata = array(
                'class' => 'col-md-3'
            );
            
            echo form_open('user/loginUser', $formattributes) .
            '<div class="form-group row">' .
                form_label('Username', 'userName', $labeldata) .
                '<div class="col-md-9">' .
                form_input($usernamedata) .
                '</div>' .
            '</div>' .            
            '<div class="form-group row">' .
                form_label('Password', 'password', $labeldata) .
                '<div class="col-md-9">' .
                form_password($passworddata) .
                '</div>' .
            '</div>';
            if (isset($error_message)) {
                echo '<div class="form-group flex-row "><div class="justify-content-center error-message">' .
                $error_message .
                '</div></div>';
            }         
            echo form_button($submitdata) .
            form_close();?> 
        </div>       
    </div>

<?php $this->load->view('footer'); ?>