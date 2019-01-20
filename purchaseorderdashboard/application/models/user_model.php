<?php
class User_model extends CI_Model {

    private $erp;

    public function __construct() {
        parent::__construct();
        $this->erp = $this->load->database('erp', TRUE);
    }
    public function registerUser($postData){
        $admintype = $postData['userType'];
		$username = $postData['username'];
		$password = $postData['password'];
		$userEmail = $postData['email'];
		$firstName = $postData['firstName'];
		$lastName = $postData['lastName'];
		$active = $postData['activeflag'];
		$company1 = $postData['ddlcorporation'];
		$address1 = $postData['address1'];
		$address2 = $postData['address2'];
		$city = $postData['city'];
		$stateID = (int)$postData['stateID'];
		$zip = $postData['zip'];
		$countryID = (int)$postData['countryID'];
        //$phone = $postData['phoneNum'];
        
        $sql = "SELECT * FROM users WHERE username='$username' AND company1='$company1' AND active=1";
        $query = $this->erp->query($sql);

        if($query->num_rows() > 0){
            echo 'user already in database';            
        }else{
            $sql = "INSERT INTO users (admintype, username, password ,userEmail, firstName, lastName, active, company1, address1, address2, city, stateID, zip, countryID)" .
            "VALUES ('$admintype','$username','$password' ,'$userEmail', '$firstName', '$lastName', '$active', '$company1', '$address1', '$address2', '$city', '$stateID', '$zip', '$countryID')";
            $this->erp->query($sql);
            return ($this->erp->affected_rows() != 1) ? false : 'user successfully added to database';
        }
    }
    public function checkUserLogin($username, $password){
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' AND active=1";
       $query = $this->erp->query($sql);
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }        
    public function updateUserInformation( $postData ){
        $userID = $postData['userID'];
        $admintype = $postData['userType'];
        $username = $postData['userName'];
        $password = $postData['Password'];
        $userEmail = $postData['email'];
        $firstName = $postData['firstName'];
        $lastName = $postData['lastName'];
        $active = $postData['checkbox'];
        $company1 = $postData['ddlcorporation'];
        $address1 = $postData['address1'];
        $address2 = $postData['address2'];
        $city = $postData['city'];
        $stateID = $postData['stateID'];
        $zip = $postData['zip'];
        $countryID = $postData['countryID'];
        $phone = $postData['phoneNum'];

        $sql  = "UPDATE users SET admintype='$admintype', username='$username', password='$password', userEmail='$userEmail', firstName='$firstName', lastName='$lastName'," 
        . " active='$active', company1='$company1', address1='$address1', address2='$address2', city='$city',stateID='$stateID', zip='$zip', countryID='$countryID', phone='$phone' WHERE userID = '$userID' ";
            $query = $this->erp->query( $sql );			
    }
    public function getUserInfoByUserID($userID){
        $sql = "SELECT * FROM users WHERE userID ='$userID'";
        $query = $this->erp->query($sql);
        return $query->result();
    }
    public function corporationList(){
		$sql = "SELECT id, name, address1, address2, city, stateID, countryID, postalcode FROM corporation";
		$query = $this->erp->query($sql);
		return $query->result();
	}       
    public function getCountryName( $countryID ){
        $sql = "SELECT countryName FROM countries WHERE countryID ='$countryID'";
        $query = $this->erp->query($sql);
        return $query->row()->countryName;
    }
    public function getStateName( $stateID ){
        $sql = "SELECT stateName FROM states WHERE stateID ='$stateID'";
        $query = $this->erp->query($sql);
        return $query->row()->stateName;
    }
    public function getUserActiveFlagInfo($userID){
        $sql = "SELECT active FROM users WHERE userID ='$userID'";
        $query = $this->erp->query($sql);
        return $query->row()->active;
    }    
    public function updateUserFlagInfo($userID, $activeflag){
        $sql = "UPDATE users SET active = $activeflag WHERE userID ='$userID'";
        $query = $this->erp->query($sql);
    }
}