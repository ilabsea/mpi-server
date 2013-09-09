<?php
/**
 * The PHP library to connect to SDK library
 * @author Sokha RUM
 */
class GrFingerService
{
	// Constants declation
	public $GR_OK = 0;
	public $GR_MATCH = 1;
	public $GR_DEFAULT_CONTEXT = 0;	
	public $GrFingerX;
	public $db;

	/**
	 * Initiolize the SDK library
	 */
	public function initialize($reload=false)
	{
		if ($reload) :
			com_load_typelib('{A9995C7C-77BF-4E27-B581-A4B5BBD90E50}');
		endif;
		try {
			$this->GrFingerX = new COM('GrFingerX.GrFingerXCtrl.1') or die ('Could not initialise object.');
			if($this->GrFingerX->Initialize() != $this->GR_OK)
				return false;
		} catch (Exception $exception) {
			com_load_typelib('{A9995C7C-77BF-4E27-B581-A4B5BBD90E50}');
			$this->GrFingerX = new COM('GrFingerX.GrFingerXCtrl.1') or die ('Could not initialise object.');
			if($this->GrFingerX->Initialize() != $this->GR_OK)
				return false;
		}
		return true;
	}
	
	/**
	 * Finalize the SDK library
	 */
	public function finalize() {
		$this->GrFingerX->Finalize();
	}
}

?>