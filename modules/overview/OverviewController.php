<?php

class OverviewController extends PropelController
{

    public function OverviewController ()
    {
		require_once("OverviewModel.php");    
        $this->model = new OverviewModel();
        
		require_once(dirname(__FILE__) . "/../projects/ProjectsModel.php");
		$projectModel = new ProjectsModel();
		require_once(dirname(__FILE__) . "/../tasks/TasksModel.php");
		$tasksModel = new TasksModel();

    	$projects = $projectModel->getProjects();
        foreach($projects as $project)
    		$tasks[$project->title] = $tasksModel->getTasks(6, $project->id);
    			
    	$id = 0;
    	$script = "";
    	
        require_once("views/overview.php");
    }

}	

?>