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
		$can_load = false;
		if (apc_exists("fp_sdk_busy")) :
			$cur_date = new DateTime();
			$cur_date->sub(new DateInterval("PT20S"));
			$fp_sdk_busy_time = apc_fetch("fp_sdk_busy_time");
			if ($cur_date > $fp_sdk_busy_time) : // cache more then 20 seconds
				$can_load = true;
			else:
				sleep(3);
				$cur_date = new DateTime();
				$cur_date->sub(new DateInterval("PT20S"));
				$fp_sdk_busy_time = apc_fetch("fp_sdk_busy_time");
				if ($cur_date > $fp_sdk_busy_time) : // cache more then 20 seconds
					$can_load = true;
				endif;
			endif;
		else:
			$can_load = true;
		endif;
		if ($can_load) :
			try {
				$this->GrFingerX = new COM('GrFingerX.GrFingerXCtrl.1') or die ('Could not initialize object. line 24');
				if($this->GrFingerX->Initialize() == $this->GR_OK):
					$this->set_fp_sdk_busy();
					return true;
				endif;
			} catch (Exception $exception) {
				com_load_typelib('{A9995C7C-77BF-4E27-B581-A4B5BBD90E50}');
				$this->GrFingerX = new COM('GrFingerX.GrFingerXCtrl.1') or die ('Could not initialize object. line 29');
				if($this->GrFingerX->Initialize() == $this->GR_OK):
					$this->set_fp_sdk_busy();
					return true;
				endif;
			}
		endif;
		return false;
		
	}
	
	/**
	 * Finalize the SDK library
	 */
	public function finalize() {
		$this->GrFingerX->Finalize();
		$this->release_sdk_busy();
	}
	
	private function set_fp_sdk_busy() {
		apc_store("fp_sdk_busy", true);
		apc_store("fp_sdk_busy_time", new DateTime());
		
	}
	
	private function release_sdk_busy() {
		apc_delete("fp_sdk_busy");
		apc_delete("fp_sdk_busy_time");
	}
}

?>