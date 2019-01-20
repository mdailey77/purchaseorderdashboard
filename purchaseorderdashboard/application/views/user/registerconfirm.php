<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('header'); ?>
    
    <div class="row  mt-5 mb-3">
        <div class="col-md-6 offset-md-3">
            <h3 class="mt-3 mb-5">Register User</h3>
            <p><?php echo $confirm_mssge; ?></p>
        </div>       
    </div>

<?php $this->load->view('footer'); ?>