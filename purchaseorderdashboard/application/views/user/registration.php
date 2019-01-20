<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('header'); ?>
    
    <div class="row  mt-5 mb-3">
        <div class="col-md-9 offset-md-3">
            <h3 class="mt-3 mb-5">Register User</h3>               
            <?php
            $formattributes = array('id'=>'registerForm');
            $usertypedata = array(
                'name'          => 'userType',
                'id'            => 'userType',
                //'value'         => $userDetail->firstName,
                'class'         => 'form-control'
            );
            $firstnamedata = array(
                'name'          => 'firstName',
                'id'            => 'firstName',
                //'value'         => $userDetail->firstName,
                'class'         => 'form-control'
            );
            $lastnamedata = array(
                'name'          => 'lastName',
                'id'            => 'lastName',
                //'value'         => $userDetail->lastName,
                'class'         => 'form-control',
            );
            $emaildata = array(
                'name'          => 'email',
                'id'            => 'email',
                //'value'         => $userDetail->userEmail,
                'class'         => 'form-control',
            );
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
            $companydata = array(
                'id'            => 'company1',
                //'value'         => $userDetail->company1,
                'class'         => 'form-control',
            );
            $address1data = array(
                'name'          => 'address1',
                'id'            => 'address1',
                //'value'         => $userDetail->address1,
                'class'         => 'form-control',
            );
            $address2data = array(
                'name'          => 'address2',
                'id'            => 'address2',
                //'value'         => $userDetail->address1,
                'class'         => 'form-control',
            );
            $citydata = array(
                'name'          => 'city',
                'id'            => 'city',
                //'value'         => $userDetail->city,
                'class'         => 'form-control',
            );
            $stateIDdata = array(
                'name'          => 'stateID',
                'id'            => 'stateID',
                //'value'         => $userDetail->stateID,
                'class'         => 'form-control',
            );
            $countryIDdata = array(
                'name'          => 'countryID',
                'id'            => 'countryID',
                //'value'         => $userDetail->countryID,
                'class'         => 'form-control',
            );
            $zipdata = array(
                'name'          => 'zip',
                'id'            => 'zip',
                //'value'         => $userDetail->zip,
                'class'         => 'form-control',
            );
            $activedata = array(
                'name'          => 'activeflag',
                'id'            => 'activeflag',
                'value'         => TRUE,
                'checked'       => TRUE,
                'class'         => 'form-check'
        );
            $submitdata = array('name'=>'register','class'=> 'btn btn-primary','content'=>'Register','type'=>'submit','id'=>'register');
            $labeldata = array(
                'class' => 'col-md-3'
            );
            $ckeckboxlabeldata = array(
                'class' => 'form-check-label'
            );
            echo form_open('user/registerUser', $formattributes) .
            '<div class="form-group row">' .
                form_label('User Type', 'userType', $labeldata) .
                '<div class="col-md-9">' .
                form_input($usertypedata) .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('First Name', 'firstName', $labeldata) .
                '<div class="col-md-9">' .
                form_input($firstnamedata) .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('Last Name', 'lastName', $labeldata) .
                '<div class="col-md-9">' .
                form_input($lastnamedata) .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('Email', 'email', $labeldata) .
                '<div class="col-md-9">' .
                form_input($emaildata) .
                '</div>' .
            '</div>' .
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
            '</div>' .
            '<div class="form-group row">' .
                '<label class="col-md-3">Company</label>' .
                '<div class="col-md-9">' .
                    '<select id="ddlcorporation" class="form-control" name="ddlcorporation">' .
                    '<option value="0">Select Company</option>';
                    foreach($corporationList as $rowCorp) {
                        echo '<option value="'.$rowCorp->name.'">'. $rowCorp->name .'</option>';
                    }
                    echo '</select>' .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('Address 1', 'address1', $labeldata) .
                '<div class="col-md-9">' .
                form_input($address1data) .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('Address 2', 'address2', $labeldata) .
                '<div class="col-md-9">' .
                form_input($address2data) .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('City', 'city', $labeldata) .
                '<div class="col-md-9">' .
                form_input($citydata) .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('State', 'stateID', $labeldata) .
                '<div class="col-md-9">' .
                form_input($stateIDdata) .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('Country', 'countryID', $labeldata) .
                '<div class="col-md-9">' .
                form_input($countryIDdata) .
                '</div>' .
            '</div>' .
            '<div class="form-group row">' .
                form_label('ZipCode', 'zip', $labeldata) .
                '<div class="col-md-9">' .
                form_input($zipdata) .
                '</div>' .
            '</div>' .
            '<div class="form-check row">' .
                form_label('Active', 'activeFlag', $labeldata) .
                '<div class="col-md-9">' .
                form_checkbox($activedata) .
                '</div>' .
            '</div>' .          
            form_button($submitdata) .
            form_close();?>
        </div>       
    </div>

<?php $this->load->view('footer'); ?>