<?php

class main_ProcesspaymentsController extends My_Controller_Action
{	
    public function init()
    {
		$this->view->layout()->setLayout('layout_blank');

    }
    
    public function receivepaymentAction(){
		try{   			
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$cTravel	= new My_Model_Viajes();
			$cLog 		= new My_Model_LogPagos();	
			$validateTravel = Array();		
			$this->_dataIn			= $this->_request->getParams();
			
			$pDataTravel = $this->_dataIn['item_number'];
			$aDataTravel = explode("_",$pDataTravel);

			$validateTravel = $cTravel->getData($aDataTravel[1]);
			if(count($validateTravel)>0){
				$update = $cTravel->updatePaymentStatus($this->_dataIn['payment_status'],$validateTravel['ID_VIAJE']);
				
				$sDataInsert = $this->_dataIn['item_number']."!".$this->_dataIn['payment_status']."!".
								 $this->_dataIn['payment_type']."!".$this->_dataIn['payment_date']."!".
								 @$this->_dataIn['txn_type']."!".@$this->_dataIn['txn_id'];				
				$cLog->insertLog($sDataInsert,$validateTravel['ID_VIAJE']);		
			}
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    
    }

    public function declinepayAction(){
		try{   		
			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }       	
    }
    
    public function okpaymentAction(){
		try{   		
			
		} catch (Zend_Exception $e) {
            echo "Caught exception: " . get_class($e) . "\n";
        	echo "Message: " . $e->getMessage() . "\n";                
        }    	
    }    
}