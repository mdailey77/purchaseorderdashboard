<?php
		// Load javascript files from config.php
		$scripts = $this->config->item('scripts');
		$lf = $this->config->item('lf');
		foreach ($scripts as $script) {
			if (!empty($script)) {
				echo '<script src="' . base_url() . 'js/' . $script . '.js"></script>' . $lf;
			}
		} ?>
	</body>
</html>