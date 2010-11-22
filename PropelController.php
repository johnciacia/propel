<?php

class PropelController
{
	/**
	 * @since 1.5.4
	 * @version 1.0
	 */
	public function on_load_info() 
	{
        $this->loadMetabox('info');
	}
	
	/**
	 * @since 1.5.4
	 * @version 1.0
	 */	
	public function on_load_dashboard() 
	{
		$this->loadMetabox('dashboard');
	}
	
	/**
	 * @since 1.3
	 * @version 1.0
	 */
    public function propel ()
    {
       $this->loadModule('info');  
    }

	/**
	 * @since 1.5
	 * @version 1.0
	 */
    public function dashboard ()
    {   
       $this->loadModule('dashboard');
    }

    /**
     * Controller for propel-projects
     * @since 1.0
     * @version 1.1
     */
    public function projects ()
    {
        $this->loadModule('projects');
    }

	/**
	 * @since 1.0
	 * @version 1.1
	 */
    public function tasks ()
    {
        $this->loadModule('tasks');
    }

	/**
	 * @since 1.3
	 * @version 1.0
	 */
    public function files()
    {
       	$this->loadModule('files');
    }

    /**
     * @since 1.3
     * @version 1.0
     */
    public function settings ()
    {   
		$this->loadModule('settings');
    }

    /**
     * @since 1.3
     * @version 1.0
     */
    private function loadModule($module)
    {
    	$title = ucfirst(strtolower($module));
    	$controller = $title . "Controller";
    		
        echo '<div class="wrap">';
        echo '<h2>' . $title . '</h2>';
        
    	require_once ("modules/" . strtolower($module) . "/" . $controller . ".php");
        $c = new $controller();
        $c->load();
        
        echo '</div>';    
    }
    
	/**
	 * @since 1.5.4
	 * @version 1.0
	 */
	private function loadMetabox($module)
	{
    	$title = ucfirst(strtolower($module));
    	$controller = $title . "Controller";
    	require_once ("modules/" . strtolower($module) . "/" . $controller . ".php");
        $c = new $controller();
        $c->metabox();		
	}
	
	/**
	 * @TODO: decouple and abstract the following method
	 */
    public function shortcode ($shortcode, $atts)
    {
    	switch ($shortcode) {
    		case 'project':
    			require_once("modules/projects/ProjectsModel.php");
    			$projectModel = new ProjectsModel();
    			require_once("modules/tasks/TasksModel.php");
    			$tasksModel = new TasksModel();
    			ob_start();
    			$script = "";
		    	extract(shortcode_atts(array('id' => 0), $atts));
		    	
		    	/**
		    	 * If $id = 0 then we will display all the projects.
		    	 */
		    	if($id == 0) {
		    		$projects = $projectModel->getProjects();
    		    	foreach($projects as $project)
		    			$tasks[$project->title] = $tasksModel->getTasks(6, $project->id);
		    	} 
		    	
		    	/**
		    	 * If the $id is set to a value, then we will display only that project.
		    	 */
		    	else {
		    		$project = $projectModel->getProject($id);
		    		if($project == NULL) {
		    			echo "<span class='alert'>The project you are looking for does not exist.</span>";
		    			return ob_get_clean();
		    		}
		    		$projects[$project->title] = $project;
		    		$tasks[$project->title] = $tasksModel->getTasks(6, $project->id);
		    	}

		    	require_once ("shortcode-projects.php");  
		    	return ob_get_clean();
    			break;
    			
    		case 'feedback':
    			extract(shortcode_atts(array('id' => 0), $atts));
    		
    			require_once("modules/projects/ProjectsModel.php");
    			$projectModel = new ProjectsModel();
    			$projects = $projectModel->getProjects();
    			
 		    	require_once ("shortcode-feedback.php");  
//		    	return $this->model->buildBugReport($atts['id']);   
    			break;
    	
    	}

    }
}

?>