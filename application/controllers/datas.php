<?php
ini_set("display_errrors", 2);
class Datas extends MpiController {
	function csvexport() {
		$data = array();
		$data["mpi_patient"] = 1;
		$data["mpi_visit"] = 1;
		$this->load->template("templates/general", "datas/csv_export", Iconstant::MPI_APP_NAME, $data);
	}

	function downloadcsv() {
		$this->load->model("data");
		$zip_file = FCPATH.APPPATH."tmp/csv_export.zip";
		$zip = new ZipArchive();
		$code = $zip->open($zip_file, ZipArchive::CREATE);
		if ($code === TRUE) {
		   //
		} else {
		    die("Failed while creating zip file");
		    
		}
		
		if (isset($_POST["mpi_patient"])) :
			$data = $this->data->get_data_from_table("mpi_patient");
			$csv_file = FCPATH.APPPATH."tmp/csv_export_patient.csv";
			$fp = fopen($csv_file, "w");
			$insert_header = false;
			foreach($data->result_array() as $row) :
			 	if (!$insert_header) :
			 		fputcsv($fp, array_keys($row));
			 		$insert_header = true;
			 	endif;
				fputcsv($fp, $row);
			endforeach;
			fclose($fp);
			$zip->addFile($csv_file, "mpi_patient.csv");
		endif;
		
		if (isset($_POST["mpi_visit"])) :
			$data = $this->data->get_data_from_table("mpi_visit");
			$csv_file = FCPATH.APPPATH."tmp/csv_export_visit.csv";
			$fp = fopen($csv_file, "w");
			$insert_header = false;
			foreach($data->result_array() as $row) :
			 	if (!$insert_header) :
			 		fputcsv($fp, array_keys($row));
			 		$insert_header = true;
			 	endif;
				fputcsv($fp, $row);
			endforeach;
			fclose($fp);
			$zip->addFile($csv_file, "mpi_visit.csv");
		endif;
		$zip->close();
		
		$file_name = "data_".date("YmdHis").".zip";
    	header("Content-disposition: attachment; filename=".$file_name);
		header("Content-type: text/plain");
		$content = file_get_contents($zip_file);
		
		unlink($zip_file);
		//unlink($csv_file);
		echo $content;
	}
}