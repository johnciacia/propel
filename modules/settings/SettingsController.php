<?php

class SettingsController extends PropelController
{
    public function load ()
    {
		require_once("SettingsModel.php");
		$this->model = new SettingsModel();
    	if(empty($_GET['action'])) {
			$this->index();
		} else {	
			if(in_array($_GET['action'], $actions)) {
				$this->$_GET['action']();
			} else {
				$this->index();
			}
		}    	
    }
    
    private function index()
    {
    	if(!empty($_POST['propel_theme'])) {    
			if(get_option('propel_theme') != $_POST['propel_theme']) {
	    		update_option('propel_theme', $_POST['propel_theme']);
	    	} else {
	    		add_option('propel_theme', $_POST['propel_theme']);
	    	}
    	}
    	
    	$themes = $this->model->getTemplates();
    	require_once ("views/settings.php");	
    }


}

?>