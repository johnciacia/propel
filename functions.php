<?php
//Miscellaneous functions that don't have a better home

function propel_get_status($meta) {

		if($meta['complete'] == 100) {
			$z = "Complete";
			$color = "#0000cc";
		} 
		
		else if($meta['end'] == "0000-00-00") {
			$z = "Later";
			$color = "#008000";			
		}
		else {
			if(date("Y-m-d") == $meta['end']) {
				$z = "Today";
				$color = "#ffa500";
			} else if(date("Y-m-d") > $meta['end']) {
				$z = "Overdue";
				$color = "#ff0000";
			} else {
				$z = "Later";
				$color = "#008000";
			}
		}
		
		return array($z, $color);
		
}


?>