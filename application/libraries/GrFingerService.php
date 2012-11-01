<?php
class GrFingerService
{
	// Constants declation
	public $GR_OK = 0;
	public $GR_MATCH = 1;
	public $GR_DEFAULT_CONTEXT = 0;	
	public $GrFingerX;
	public $db;

	// Application startup code
	public function initialize()
	{
		// Initialize GrFingerX Library
		$this->GrFingerX = new COM('GrFingerX.GrFingerXCtrl.1') or die ('Could not initialise object.');
		com_load_typelib('{A9995C7C-77BF-4E27-B581-A4B5BBD90E50}');
		if($this->GrFingerX->Initialize() != $this->GR_OK)
			return false;
		return true;
	}
	
	// Application finalization code
	public function finalize()
	{
		$this->GrFingerX->Finalize();
	}
}

?>