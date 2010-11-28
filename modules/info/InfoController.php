<?php

class InfoController
{
	private $pagehook = "toplevel_page_propel";
	
	public function metabox()
	{
		add_meta_box('propel-info-contentbox-1', 'About', array(&$this, 'propel_info_contentbox_1'), "toplevel_page_propel", 'normal', 'core');
		add_meta_box('propel-info-contentbox-2', 'FAQ', array(&$this, 'propel_info_contentbox_2'), "toplevel_page_propel", 'normal', 'core');
		add_meta_box('propel-info-contentbox-3', 'Support', array(&$this, 'propel_info_contentbox_3'), "toplevel_page_propel", 'normal', 'core');
		add_meta_box('propel-info-contentbox-4', 'Contribute', array(&$this, 'propel_info_contentbox_4'), "toplevel_page_propel", 'normal', 'core');
		
		add_meta_box('propel-info-contentbox-5', 'Latest News', array(&$this, 'propel_info_contentbox_5'), "toplevel_page_propel", 'side', 'core');
		add_meta_box('propel-info-contentbox-6', 'Support Forums', array(&$this, 'propel_info_contentbox_6'), "toplevel_page_propel", 'side', 'core');
		add_meta_box('propel-info-contentbox-7', 'Revision Log', array(&$this, 'propel_info_contentbox_7'), "toplevel_page_propel", 'side', 'core');
	}
	
	public function load() {
		global $screen_layout_columns;
        require_once("views/info.php");
	}

	
	public function propel_info_contentbox_1()
	{
		require_once('views/about.php');
	}
	
	public function propel_info_contentbox_2() 
	{
		require_once("views/faq.php");
	}
	
	public function propel_info_contentbox_3()
	{
		require_once("views/support.php");
	}
	
	public function propel_info_contentbox_4()
	{
		require_once("views/contribute.php");
	}
	
	public function propel_info_contentbox_5()
	{
		$rss = fetch_feed('http://www.johnciacia.com/category/propel/feed');
		if (!is_wp_error( $rss ) ) { // Checks that the object is created correctly 
		    // Figure out how many total items there are, but limit it to 5. 
		    $maxitems = $rss->get_item_quantity(5); 
		
		    // Build an array of all the items, starting with element 0 (first element).
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
	}

	public function propel_info_contentbox_6()
	{
		$rss = fetch_feed('http://wordpress.org/support/rss/tags/propel');
		if (!is_wp_error( $rss ) ) { // Checks that the object is created correctly 
		    // Figure out how many total items there are, but limit it to 5. 
		    $maxitems = $rss->get_item_quantity(5); 
		
		    // Build an array of all the items, starting with element 0 (first element).
		    $rss_items = $rss->get_items(0, $maxitems); 
		}
		
		echo '<ul>';
			if ($maxitems == 0) echo '<li>No items.</li>';
			else {
				foreach ($rss_items as $item) { 
					echo "<li>
							<p><a href='".$item->get_permalink()."' title='Posted ". $item->get_date('j F Y | g:i a') . "'>" . $item->get_title() . "</a><br />";
					$description = $item->get_description(); 
		        	$description = substr($description, 0, -37);
		        	echo strip_tags($description);
				    echo '</p></li>';
				}
			}
					     
			echo '</ul>';		
	}
	
	public function propel_info_contentbox_7()
	{
		$rss = fetch_feed('http://plugins.trac.wordpress.org/log/propel?limit=10&mode=stop_on_copy&format=rss');
		if (!is_wp_error( $rss ) ) { // Checks that the object is created correctly 
		    // Figure out how many total items there are, but limit it to 5. 
		    $maxitems = $rss->get_item_quantity(10); 
		
		    // Build an array of all the items, starting with element 0 (first element).
		    $rss_items = $rss->get_items(0, $maxitems); 
		}
		
		echo '<ul>';
			if ($maxitems == 0) echo '<li>No items.</li>';
			else {
				foreach ($rss_items as $item) { 
					echo "<li><p><a href='".$item->get_permalink()."' title='Posted ". $item->get_date('j F Y | g:i a') . "'>" . $item->get_title() . "</a><br /></p></li>";
				}
			}
					     
			echo '</ul>';		
	}	
	
}

?>