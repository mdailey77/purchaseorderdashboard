<?php
class Dashboard_model extends CI_Model {

    private $erp;

    public function __construct() {
        parent::__construct();
        $this->erp = $this->load->database('erp', TRUE);
    }
    public function convertOrderStatus($fromDateTime, $toDateTime){
        $sql = "UPDATE orders SET orderStatus='Pending' WHERE datestamp BETWEEN '" . date_format($fromDateTime, 'Y-m-d H:i:s') . "' AND '" . date_format($toDateTime, 'Y-m-d H:i:s') . "' AND orderStatus='bquest Complete' AND deleted=0 AND orderSource='punchOut'";
        $query = $this->db->query($sql);
		return $query;
    }
    public function getOrders($filter, $fromDate, $toDate) {
        $format = 'Y-m-d H:i:s';
        $fromDateTime = date_create_from_format('m/d/Y', $fromDate);
        $toDateTime = date_create_from_format('m/d/Y', $toDate);
        $sql = "SELECT * FROM orders WHERE datestamp BETWEEN '" . date_format($fromDateTime, 'Y-m-d H:i:s') . "' AND '" . date_format($toDateTime, 'Y-m-d H:i:s') . "' AND orderSource='punchOut' AND deleted=0" .
        " AND jobID IN (SELECT DISTINCT(jobID) FROM ordersdetail WHERE cmsStatus='$filter' and deleted=0)";
        $query = $this->db->query($sql);
		return $query->result();
    }
    public function getShippingInfo($shipToID) {
        $sql = "SELECT sh.active, sh.shipToID, sh.firstName, sh.lastName, sh.company, sh.address1, sh.address2, sh.address3, sh.address4,"
        . " sh.address5, sh.phone, sh.city, st.stateName, c.countryName, sh.zipCode, st.abbr AS stateAbbr FROM shipTo AS sh JOIN states AS st ON sh.stateID=st.stateID"
         . " JOIN countries AS c ON sh.countryID=c.countryID WHERE shipToID=$shipToID";
		$query = $this->db->query($sql);
		return $query->row();
    }
    public function getOrderHeader($jobID) {
        $sql = "SELECT *, CONVERT( char(10), datestamp, 101 ) AS datestr, CONVERT( char(8), datestamp, 108 ) AS timestr, CONVERT( char(10), approvalDate, 101 ) "
                . "AS approvedatestr, CONVERT( char(8), approvalDate, 108 ) AS approvetimestr FROM orders WHERE jobID=$jobID AND deleted=0 AND orderSource='punchOut'";
        $query = $this->db->query($sql);
        return $query->row();
    }
    public function getOrderDetail($jobID) {
		$sql = "SELECT od.datestamp, od.invoiceID, od.jobID, od.itemICN, od.itemCost, od.itemPrice, od.itemQuantity, od.itemName, od.pieceCost, od.cmsStatus, od.orderSource, od.salesTax, p.productName, p.VID, v.company1"
        . " FROM ordersdetail AS od LEFT OUTER JOIN products AS p ON od.itemICN = p.ICN LEFT OUTER JOIN vendorNames AS v ON p.VID = v.VID WHERE od.jobID=$jobID AND od.deleted=0";
		$query = $this->db->query($sql);
		return $query->result();
    }
    public function getOrderBillInfo($billToID) {
        $sql = "SELECT *, s.abbr AS stateAbbr FROM billto AS st JOIN states AS s ON st.stateID=s.stateID "
		     . "JOIN countries AS c ON st.countryID=c.countryID "
				 . "WHERE billToID=$billToID";
		$query = $this->db->query($sql);
		return $query->row();
    }
    public function saveOrderStatus($jobID, $orderStatus) {        
        $sql = "UPDATE orders SET orderStatus='$orderStatus' WHERE jobID=$jobID AND deleted=0 AND orderSource='punchOut'";       
        $query = $this->db->query($sql);
		return $query;
    }
    public function saveOrderItem($jobID, $ICN, $orderStatus) {
        $sql = "UPDATE ordersdetail SET cmsStatus='$orderStatus' WHERE itemICN=$ICN AND jobID=$jobID AND deleted=0 AND orderSource='punchOut'";       
        $query = $this->db->query($sql);
		return $query;
    }
    public function getJobICNs($jobID) {
        $sql = "SELECT itemICN FROM ordersdetail WHERE jobID=$jobID AND deleted=0 AND orderSource='punchOut'";
        $query = $this->db->query($sql);
        $response = $query->result();
        return $response;
    }
    public function addPODanchors($orderDetails) {
        $link = array('ICN' => '', 'anchor' => '');
		$linkArray = array();
		foreach ($orderDetails as $key=>$row) { // iterate through ordersDetail
			$sql1 = "SELECT active, ICN, documentType, isPrintReady, downloadable FROM podConfig WHERE ICN={$row->itemICN} AND active=1";
			$query1 = $this->db->query( $sql1 );
			$thisPODconfig = $query1->row();
            $link['ICN'] = $row->itemICN;
			if (sizeof($thisPODconfig ) > 0) {
				if ( $thisPODconfig->isPrintReady == 1 && $thisPODconfig->downloadable == 0 ) {
					$row->documentType = $thisPODconfig->documentType;
                    $link['anchor'] = 'https://www.mattdailey.net/purchaseorderdashboard/PDF/' . $row->itemICN . '_p1.' . $thisPODconfig->documentType;
				}
				$sql2 = "SELECT COUNT( id ) AS configCount FROM customizeablePDF WHERE icn={$row->itemICN} AND active=1";
				$query2 = $this->db->query( $sql2 );
				$configCount = $query2->row()->configCount;
				if ( $configCount > 0 ) {
                    $link['anchor'] = 'https://www.mattdailey.net/purchaseorderdashboard/userFiles/' . $row->jobID . '_' . $row->itemICN . '_' . $row->templateID . '_final.' . $thisPODconfig->documentType;
                }                
            }
            $linkArray[$key] = $link;		
		}
		return $linkArray;
    }
    public function getNextGlobalCounterValue($corporationID, $type) {
        $sql = "{call getCounterNumber ( '$corporationID', '$type', 0 )}";
        $query = $this->erp->query($sql);
        $return = $query->row();
        return $return->id;
    }
    public function saveUserAction($comment) {
        $userID = $this->session->userdata('userID');
        $corporationID = $this->config->item('corporationID');
        $sql = "INSERT INTO changeLog (userID, corporationID, Description) VALUES ($userID, $corporationID, '$comment')";
        $query = $this->erp->query($sql);
        return $query; 
    }
    public function saveInvoiceData($jobID, $ICN, $invoiceID, $invoiceLineNmbr) {
        $invoiceDate = date("Y-m-d H:i:s");
        $sql = "UPDATE ordersdetail SET invoiceID=$invoiceID, invoiceDate='$invoiceDate', invoiceLineNumber=$invoiceLineNmbr WHERE jobID=$jobID AND itemICN=$ICN AND deleted=0 AND orderSource='punchOut'";       
        $query = $this->db->query($sql);
		return $query;
    }
    public function getCSVData($jobID, $itemICNs) {
        $sql = "SELECT od.invoiceID, od.jobID, od.invoiceDate, o.datestamp, o.purchaseOrder, od.invoiceLineNumber, od.poLineNumber, od.itemICN, od.itemQuantity, od.itemPrice, od.itemName, od.salesTax, sh.shipToID, sh.company, sh.address1, "
        . "sh.city, st.stateName, c.countryName, sh.zipCode, sh.email, od.salesTax FROM ordersdetail AS od INNER JOIN orders AS o ON od.jobID = o.jobID INNER JOIN shipTo AS sh ON o.shipToID = sh.shipToID"
        .  " INNER JOIN states AS st ON sh.stateID=st.stateID INNER JOIN countries AS c ON sh.countryID=c.countryID WHERE od.jobID=$jobID AND od.itemICN IN ($itemICNs) AND od.deleted=0 AND o.deleted=0";
		$query = $this->db->query($sql);
		return $query->result();
    }
}