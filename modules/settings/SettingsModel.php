<?php

class SettingsModel
{
	
    public function getTemplates()
    {
		$path = WP_PLUGIN_DIR . '\propel\themes';
		if ($handle = opendir($path)) {
		    while (false !== ($file = readdir($handle))) {
		    	if($file == "." || $file == "..")
		    		continue;
				if(($style = $this->verifyTemplate($path . DIRECTORY_SEPARATOR . $file)))
					$r[$file] = WP_PLUGIN_URL . '/propel/themes/' . $file . '/' . $style;
		    }
		
		    closedir($handle);
		} 

		$path = TEMPLATEPATH . '\propel';
		if(!file_exists($path))
			return $r;
		
		if ($handle = opendir($path)) {
		    while (false !== ($file = readdir($handle))) {
		    	if($file == "." || $file == "..")
		    		continue;
		    		
				if(($style = $this->verifyTemplate($path . DIRECTORY_SEPARATOR . $file)))
					$r[$file] = get_bloginfo('template_directory') . '/propel/' . $file . '/' . $style;
		    }
		
		    closedir($handle);
		}   
		
		return $r; 	
    }
    
    private function verifyTemplate($path)
    {
		if ($handle = opendir($path)) {
		    while (false !== ($file = readdir($handle))) {
	        	$p = pathinfo($file);
	        	if(isset($p['extension'])) {
		        	if($p['extension'] == "css") {
		        		closedir($handle);
		        		return $file;
		        	}
	        	}
		    }
		
		    closedir($handle);
		    
		}
		return false;
    }
}
?>