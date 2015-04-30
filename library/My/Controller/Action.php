<?php
/**
 * Archivo de definición de clase 
 * 
 * @package library.My.Controller
 * @author andres
 */

/**
 * Definición de clase de controlador genérico
 *
 * @package library.My.Controller
 * @author andres
 */
class My_Controller_Action extends Zend_Controller_Action
{
	/**
	 * Identidad
	 * 
	 * @var mixed
	 */
	protected $_idUpdate = Array();
		
	/**
	 * Identidad
	 * 
	 * @var mixed
	 */
	protected $_dataUser= Array();
	
    /**
     * 
     * @var array 
     */
    protected $_menuCurrent;
    
    /**
     * 
     * @var array 
     */
    protected $_dataIn;  

    /**
     * 
     * @var array 
     */
    protected $_colorContenedor;  

    /**
     * 
     * @var array 
     */
    protected $_dataOp;    
    /**
     * 
     * @var array 
     */
    protected $_resultOp;  

	/**
	 * Identidad
	 * 
	 * @var mixed
	 */
	protected $_aErrors= Array();    
	
	/**
	 * Identidad
	 * 
	 * @var mixed
	 */
	protected $_baseUrl= Array();  	
    
   /**
    * Inicializa el contexto, el formato de respuesta a un action
    *
    * @return void
    */
    public function init() {		

    }
 

    /**
     * Método llamado previo a llamada de controlador
     *
     * @return void
     */
    public function preDispatch()
    {
  
            
    }

    /**
     * Procedimiento posterior a llamado de controlador
     *
     * @return void
     */
    public function postDispatch(){
		
    }
}
