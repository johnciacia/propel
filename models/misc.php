<?php

class Helper {
    public function getTemplates()
    {
		$path = WP_PLUGIN_DIR . '/propel/themes';
		if ($handle = opendir($path)) {
		    while (false !== ($file = readdir($handle))) {
		    	if($file == "." || $file == "..")
		    		continue;
				if(($style = $this->verifyTemplate($path . DIRECTORY_SEPARATOR . $file)))
					$r[$file] = WP_PLUGIN_URL . '/propel/themes/' . $file . '/' . $style;
		    }
		
		    closedir($handle);
		} 

		$path = TEMPLATEPATH . '/propel';
		if(!file_exists($path))
			return $r;
		
		if ($handle = @opendir($path)) {
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
		if ($handle = @opendir($path)) {
		    while (false !== ($file = @readdir($handle))) {
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

	public function rss()
	{
		$rss = fetch_feed($_POST['feed']);
		if (!is_wp_error( $rss ) ) { 
		    $maxitems = $rss->get_item_quantity(5); 
		    $rss_items = $rss->get_items(0, $maxitems); 
		}
		
		echo '<ul>';
		if ($maxitems == 0) echo '<li>No items.</li>';
		else {
			foreach ($rss_items as $item) { 
				echo "<li>
						<p><a href='".$item->get_permalink()."' title='Posted ". $item->get_date('j F Y | g:i a') . "'>" . $item->get_title() . "</a><br />";
				$description = strip_tags($item->get_description()); 
		       	$description = substr($description, 0, -37);
		       	echo $description;
			    echo '</p></li>';
			}
		}
				     
		echo '</ul>';	
		die();		
		
	}
}
?>