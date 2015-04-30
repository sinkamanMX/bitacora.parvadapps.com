<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	/**
	 * Instancia estática
	 *
	 * @var Bootstrap
	 */
	protected static $_instance = null;
	
	/**
	 * Petición a partir de URL
	 *
	 * @var string
	 */
	protected $_module;
	
	/**
	 * Adaptadores de base de datos
	 *
	 * @var array
	 */
	protected $_dbAdapter;
	
	/**
	 * Adaptadores de caché
	 *
	 * @var array
	 */
	protected $_cacheAdapter;
	
	/**
	 * Configuración de la aplicación
	 *
	 * @var Zend_Config
	 */
	protected $_config = array();
	
	/**
	 * Devuelve instancia estática de esta clase, creándola si es necesario
	 *
	 * @return Bootstrap
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	
	protected function _initAutoload() {		
		$front = $this->bootstrap("frontController")->frontController;
		$modules = $front->getControllerDirectory();		
		$default = $front->getDefaultModule();
		
		foreach (array_keys($modules) as $module) {
		    if ($module === $default) {
		       // continue;
		    }
		    
		    $moduleloader = new Zend_Application_Module_Autoloader(array(						   
		        'namespace' => $module,
		        'basePath'  => $front->getModuleDirectory($module)."/controllers"));
		    $front->addControllerDirectory($front->getModuleDirectory($module)."/controllers", $module);
		    
		}

		return $moduleloader;
		
		
	}  	
	/**
	 * Configura base de datos dinamicamente
	 *
	 * @return Bootstrap
	 */
	protected function _initDatabase(){
		// get config from config/application.ini
		$config = $this->getOptions();
	
		$db = Zend_Db::factory($config['resources']['db']['adapter'], $config['resources']['db']['params']);
	
		//set default adapter
		Zend_Db_Table::setDefaultAdapter($db);
	
		//save Db in registry for later use
		Zend_Registry::set("db", $db);
	}
	
    function _initLayout(){
 		Zend_Layout::startMvc(array(
                'layoutPath' => APPLICATION_PATH.'/views/layouts'
		));
    }
    
    protected function _initSession()
    {

    }    
    
    protected function _initRequest()
    {
    	try{
    		/*
	        $config = $this->getOptions();
	    	// Ensure front controller instance is present, and fetch it
	    	$this->bootstrap('FrontController');
	    	$front = $this->getResource('FrontController');

	    	// Initialize the request object
	    	$request = new Zend_Controller_Request_Http();
	    	$request->setBaseUrl('/');
	        
	        $front->setBaseUrl($config['serviceUrl']);
	    	// Add it to the front controller
	    	$front->setRequest($request);
	    
	    	// Bootstrap will store this value in the 'request' key of its container
	    	return $request;*/
    	}catch (Zend_Controller_Dispatcher_Exception $ex){
		   $request = $controller->getRequest();
		   echo "Exception: ".$request->getControllerName()."Controller class not found. ".$request->getActionName()."Action() not found. "; 
		}
    } 
}