<?php 

class FilesController
{
	public function __construct()
	{
		echo "Comming soon...";
		//require_once("FilesModel.php");
		//$this->model = new FilesModel();
	
		//$actions = array("_create");
		
        /**
         * @todo only "/a-z0-9_/"i are acceptable characters
         */
		//if(empty($_GET['action'])) {
		//	$this->index();
		//} else {	
		//	if(in_array($_GET['action'], $actions)) {
		//		$this->$_GET['action']();
		//	} else {
		//		$this->index();
		//	}
		//}
	}
	
	private function index()
	{
		require_once("views/files-create.php");
	}
	
	private function _create ()
	{
		if(!$this->model->checkFileConfig()) {
			echo $this->model->error . " Upload failed.<br />";
		} else {
			if(!$this->model->insertFile()) {
				echo $this->model->error . " Upload failed.<br />";
			} else {
				echo "Upload succesful!<br />";
			}
		}
		
		$this->index();
	}
}

?>