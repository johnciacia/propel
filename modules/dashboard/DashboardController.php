<?php

class DashboardController extends PropelController
{
	private $pagehook = "propel_page_propel-dashboard";

	
	public function metabox()
	{
		add_meta_box('propel-dashboard-contentbox-1', 'About', array(&$this, 'propel_dashboard_contentbox_1'), "propel_page_propel-dashboard", 'normal', 'core');
		
		add_meta_box('propel-dashboard-contentbox-7', 'Revision Log', array(&$this, 'propel_dashboard_contentbox_7'), "propel_page_propel-dashboard", 'side', 'core');
	}
	
	public function load() {
		global $screen_layout_columns;
        require_once('views/dashboard.php');
	}

	
	public function propel_dashboard_contentbox_1()
	{
		echo "Hello, World";
	}
	
	
	public function propel_dashboard_contentbox_7()
	{
		echo "Hello, World";
	}	
	
}

?>