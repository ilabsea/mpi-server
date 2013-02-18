<?php
/**
 * Member
 * @author sokha
 */
class Members extends MpiController {
	/**
	 * Enter description here ...
	 */
    function memberlist() {
        $data = array();
		$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	
    	$this->load->model("service");
    	$data["services"] = $this->service->getServices();
    	
    	$criteria = array(
    						"cri_serv_id" => "",
    						"cri_site_code" => "",
    						"cri_member_login" => "",
    						"cur_page" => 1,
    						"orderby" => "site_code",
    						"orderdirection" => "ASC"
    					);
        
        $start = 0;
    	
    	$session_data = Isession::getCriteria("member_list");
    	$first_access = false;
    	if ($session_data != null) :
    		$criteria = array_merge($criteria, $session_data);
    	else : 
    	    $first_access = true;
    	endif;
    	
    	if (isset($_REQUEST["orderby"])) :
    		$criteria["orderby"] = $_REQUEST["orderby"];
    	endif;
    	
    	if (isset($_REQUEST["orderdirection"])) :
    		$criteria["orderdirection"] = $_REQUEST["orderdirection"];
    	endif;
    	
    	if (isset($_REQUEST["cur_page"])) :
    		$criteria["cur_page"] = $_REQUEST["cur_page"];
    	endif;
    	
    	if ($first_access) :
    	    $data = array_merge($data, $criteria);
    	    $data["member_list"] = null;
    	    $data["total_record"] = 0;
    	    $data["nb_of_page"] = 1;
    	    $this->load->template("templates/general", "members/member_list", Iconstant::MPI_APP_NAME, $data);
    	    return;
    	endif;
    	
    	$this->load->model("member");
    	$total_sites = $this->member->count_member_list($criteria);
    	$total_pages = (int)($total_sites / Iconstant::PAGINATION_ROW_PER_PAGE);
    	if ($total_pages == 0 || $total_pages * Iconstant::PAGINATION_ROW_PER_PAGE < $total_sites) :
    	    $total_pages++;
    	endif;
    	
    	if ($criteria["cur_page"] > $total_pages) :
    		$criteria["cur_page"] = $total_pages;
    	endif; 
    	
    	Isession::setCriteria("member_list", $criteria);
    	
    	
    	
    	$start = ($criteria["cur_page"] - 1) * Iconstant::PAGINATION_ROW_PER_PAGE;
    	
    	//$criteria["orderby"] = $orderby;
    	//$criteria["orderdirection"] = $orderdirection;

    	$data["member_list"] = $this->member->getMembers($criteria, $start, Iconstant::PAGINATION_ROW_PER_PAGE);
    	//$data["cur_page"] = $cur_page;
    	$data["total_record"] = $total_sites;
    	$data["nb_of_page"] = $total_pages;
    	$data = array_merge($data, $criteria);
    	$this->load->template("templates/general", "members/member_list", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Searching for member
     */
    function search() {
    	$criteria = $_POST;
    	$criteria["cri_site_code"] = trim($criteria["cri_site_code"]);

    	$session_data = Isession::getCriteria("member_list");
    	if ($session_data != null) :
    		$criteria = array_merge($session_data, $criteria);
    	endif;
    	
    	Isession::setCriteria("member_list", $criteria);
    	redirect(site_url("members/memberlist"));
    
    
    }
}
