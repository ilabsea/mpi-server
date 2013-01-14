<?php
/**
 * User model
 * @author Sokha RUM
 */
class Usermodel extends Imodel {
	/**
	 * Authentication
	 * @param string $uesr_login
	 * @param string $user_pwd
	 * @return Array of user information
	 * @author Sokha RUM
	 */
    function authentication ($uesr_login, $user_pwd) {
        $sql = "SELECT u.user_id,
                       u.user_login,
                       u.user_lname,
                       u.user_fname,
                       g.grp_id,
                       g.grp_name,
                       u.user_email
                  FROM users u
                       LEFT JOIN user_group g ON (u.grp_id = g.grp_id)
                 WHERE u.user_login = '".mysql_real_escape_string($uesr_login)."' AND
                       u.user_pwd = SHA1('".mysql_real_escape_string($user_pwd)."')";
        $query = $this->db->query($sql);
        if ($query->num_rows() <= 0) :
            return null;
        else: 
            return $query->row_array();
        endif;
    }
    
    /**
     * Change password of a user
     * @param int $user_id
     * @param string $user_pwd
     * @author Sokha RUM
     */
    function update_pwd($user_id, $user_pwd) {
        $sql = "UPDATE users SET user_pwd = SHA1('".mysql_real_escape_string($user_pwd)."'),
                                 user_update = ".$user_id.",
                                 date_update = CURRENT_TIMESTAMP()
                           WHERE user_id = ".$user_id;
        $this->db->query($sql);
    }
    
    /**
     * user list query
     * @return List of users
     * @author Sokha RUM
     */
    function user_list() {
    	$sql = "SELECT u.user_id,
    	               u.user_login,
    	               u.user_lname,
    	               u.user_fname,
    	               u.user_email,
    	               g.grp_id,
    	               g.grp_name
    	          FROM users u
					   LEFT JOIN user_group g ON (u.grp_id = g.grp_id)";
    	return $this->db->query($sql);
    }
    
    /**
     * Getting user group
     * @author Sokha RUM
     */
    function group_list() {
        $sql = "SELECT grp_id,
                       grp_name,
                       grp_desc
                  FROM user_group";
        return $this->db->query($sql);
    }
    
    /**
     * get user with the specific id
     * @param int $user_id
     * @return Array of user information
     */
    function user_by_id($user_id) {
        $sql = "SELECT u.user_id,
    	               u.user_login,
    	               u.user_lname,
    	               u.user_fname,
    	               u.user_email,
    	               g.grp_id,
    	               g.grp_name
    	          FROM users u
					   LEFT JOIN user_group g ON (u.grp_id = g.grp_id)
			     WHERE u.user_id = ".$user_id;
    	$query = $this->db->query($sql);
    	if ($query->num_rows() <= 0) :
            return null;
        else: 
            return $query->row_array();
        endif;
    }
    
    /**
     * Get user with the specific login
     * @param string $user_login
     * @return Array of user information
     */
    function user_by_login($user_login) {
        $sql = "SELECT u.user_id,
    	               u.user_login,
    	               u.user_lname,
    	               u.user_fname,
    	               u.user_email,
    	               g.grp_id,
    	               g.grp_name
    	          FROM users u
					   LEFT JOIN user_group g ON (u.grp_id = g.grp_id)
			     WHERE u.user_login = '".mysql_real_escape_string($user_login)."'";
    	$query = $this->db->query($sql);
    	if ($query->num_rows() <= 0) :
            return null;
        else: 
            return $query->row_array();
        endif;
    }
    
    /**
     * Create new patient
     * @param array $data the information of patient
     */
    function user_new($data) {
        $sql = "INSERT INTO users(user_login,
                                  user_lname,
                                  user_fname,
                                  user_email,
                                  user_pwd,
                                  grp_id,
                                  user_create,
                                  date_create)
                           VALUES('".mysql_real_escape_string($data["user_login"])."',
                                  '".mysql_real_escape_string($data["user_lname"])."',
                                  '".mysql_real_escape_string($data["user_fname"])."',
                                  '".mysql_real_escape_string($data["user_email"])."',
                                  SHA1('".mysql_real_escape_string($data["user_pwd"])."'),
                                  ".$data["grp_id"].",
                                  ".$data["user_create"].",
                                  CURRENT_TIMESTAMP()
                           )";
        $this->db->query($sql);
    }
    
    /**
     * Update user
     * @param array $data
     */
    function user_update($data) {
        $sql = "UPDATE users SET user_login = '".mysql_real_escape_string($data["user_login"])."',
                                 user_lname = '".mysql_real_escape_string($data["user_lname"])."',
                                 user_fname = '".mysql_real_escape_string($data["user_fname"])."',
                                 user_email = '".mysql_real_escape_string($data["user_email"])."',
                                 grp_id = ".$data["grp_id"].",
                                 user_update = ".$data["user_update"].",
                                 date_update = CURRENT_TIMESTAMP()
                           WHERE user_id = ".$data["user_id"];
        $this->db->query($sql);
    }
    
    /**
     * remove a user with the specific id from database
     * @param int $user_id
     */
    function delete_user ($user_id) {
        $sql = "DELETE FROM users WHERE user_id = ".$user_id;
        $this->db->query($sql);
    }
}