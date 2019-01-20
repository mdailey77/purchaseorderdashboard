<?php
	$config['site_enabled'] = TRUE;
	$config['dev_site'] = TRUE;
	$config['debug'] = TRUE;
	$config['environment'] = 'Development';	
	$config['base_url'] = "http://localhost/{$config['base_dir']}/trunk";
	$config['local_root_path'] = "C:/Repositories/{$config['base_dir']}/trunk/";
	$config['composer_autoload'] = $config['local_root_path'] . '/vendor/autoload.php';
?>