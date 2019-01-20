<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Purchase Order Dashboard</title>
		<link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto" rel="stylesheet">
		<!-- Load CSS files from config.php -->
		<?php
		$styles = $this->config->item('styles');
		$lf = $this->config->item('lf');
		$loggedin = $this->session->userdata('logged_in');
		$usertype = $this->session->userdata('userType');
		$username = $this->session->userdata('username');
		foreach($styles as $css) { 
			if (!empty($css)) { 
				echo link_tag('css/' . $css . '.css') . $lf; 
			} 
		} ?>
	</head>
	<body>
		<div class="container-fluid">
			<nav class="navbar navbar-expand-lg navbar-light bg-light">
				<div class="navbar-brand font-weight-bold"><a href="<?=base_url()?>index.php/main/index" class="nav-link">Purchase Order Admin</a></div>
				<div id="navbarNav">
					<ul class="navbar-nav justify-content-end">					
						<?php if(!empty($username)) { ?>
						<li class="nav-item pr-4">
						<span class="navbar-text font-weight-bold"><?php echo $username; ?></span>
						</li>
						<?php } ?>
						<?php if($loggedin == TRUE) { ?>
						<li class="nav-item">
							<a href="<?=base_url()?>index.php/user/logout" class="nav-link">Logout</a>
						</li>
						<?php } ?>
						<?php if($usertype == 'Admin') { ?>
						<li class="nav-item">
							<a href="<?=base_url()?>index.php/user/register" class="nav-link">Register User</a>
						</li>
						<?php } ?>
					</ul>
				</div>
			</nav>