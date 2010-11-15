<?php

class InfoController
{

	/**
	 * @since 1.0
	 * @version 1.0
	 */
    public function InfoController ()
    {
		include_once(ABSPATH . WPINC . '/feed.php');
		
		// Get a SimplePie feed object from the specified feed source.
		$rss = fetch_feed('http://www.johnciacia.com/category/propel/feed');
		if (!is_wp_error( $rss ) ) { // Checks that the object is created correctly 
		    // Figure out how many total items there are, but limit it to 5. 
		    $maxitems = $rss->get_item_quantity(5); 
		
		    // Build an array of all the items, starting with element 0 (first element).
		    $rss_items = $rss->get_items(0, $maxitems); 
		}
		
		$rss2 = fetch_feed('http://www.johnciacia.com/propel/feed/');
    	if (!is_wp_error( $rss2 ) ) { // Checks that the object is created correctly 
		    // Figure out how many total items there are, but limit it to 5. 
		    $maxitems2 = $rss2->get_item_quantity(5); 
		
		    // Build an array of all the items, starting with element 0 (first element).
		    $rss_items2 = $rss2->get_items(0, $maxitems2); 
		}
							
					
        require_once("views/info.php");
    }
    
}

?>