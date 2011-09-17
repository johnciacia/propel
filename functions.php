<?php
//Miscellaneous functions that don't have a better home

/**
 * Get the status of a project or task
 * @since 1.7.2
 * @param $meta is an assocative array which indexes 'complete'
 * and 'end'
 * @return an array of a status label and the color of the label
 */
function propel_get_status($meta) {

		if($meta['complete'] == 100) {
			$z = "Complete";
			$color = "#0000cc";
		} 
		
		//If the end date is not set, assume it is an ongoing item
		else if($meta['end'] == "0000-00-00" || 
				//Fix for older versions of Propel
				$meta['end'] == "") {
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