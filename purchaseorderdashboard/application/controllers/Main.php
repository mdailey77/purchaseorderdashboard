<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
	public function index()	{
		$username = $this->session->has_userdata('username');
		if ($username != false) {		// check if user is logged in	
			if ( !empty($this->input->post('filterDropDown')) ) {
				$filter = $this->input->post('filterDropDown');
			} else {
				$filter = 'Pending';
			}
			if ( empty($this->input->post('fromDate')) ) {
				$jd = gregoriantojd( date( "n" ), date( "j" ), date( "Y" ) );
				$jd -= 30;
				$fromDate = jdtogregorian( $jd );
			} else {
				$fromDate = $this->input->post('fromDate');
			}
			if ( empty($this->input->post('toDate')) ) {			
				$toDate = date('m/d/Y');
			} else {
				$toDate = $this->input->post('toDate');
			}

			$data['pending_orders'] = $this->dashboard->getOrders($filter, $fromDate, $toDate);
			$data['filter'] = $filter;
			$data['fromDate'] = $fromDate;
			$data['toDate'] = $toDate;
			$this->load->view('main/dashboard', $data);
		} else {
			redirect('user/login', 'refresh');
		}
	}
	public function orderDetails($jobID, $orderStatus){
		//$data['orderStatus'] = $orderStatus;
		$orderDetail = $this->dashboard->getOrderDetail($jobID);
		$data['orderDetail'] = $orderDetail;
		$data['invoiceStatus'] = $this->checkForInvoiceStatus($orderDetail);
		$data['orderHeader'] = $this->dashboard->getOrderHeader($jobID);
		$data['billingInfo'] = $this->dashboard->getOrderBillInfo($data['orderHeader']->billToID);
		$data['shippingInfo'] = $this->dashboard->getShippingInfo($data['orderHeader']->shipToID);
		$orderDetails = $this->dashboard->getOrderDetail($jobID);
		$data['imageLinks'] = $this->dashboard->addPODanchors($orderDetails);

		$this->load->view('main/orderDetails', $data);
	}
	public function checkForInvoiceStatus($orderDetail) {
		foreach($orderDetail as $item) {
			if ($item->cmsStatus == 'Invoice') {
				return true;
			}
		}
		return false;
	}
	// update orderStatus
	public function updateOrderStatus($jobID, $orderStatus) {
		$resp = array('updatedOrderStatus'=> '');
		switch ($orderStatus) {
			case 'Submitted':
				$updatedOrderStatus = $this->dashboard->saveOrderStatus($jobID, $orderStatus);
				$jobICNs = $this->dashboard->getJobICNs($jobID);
				foreach($jobICNs as $key => $ICN) {
					$itemResponse = $this->dashboard->saveOrderItem($jobID, $ICN->itemICN, $orderStatus);
					if ($itemResponse == false) {
						$resp['itemsUpdated'][$key] = false;
					}
				}
				$resp['updatedOrderStatus'] = $updatedOrderStatus;
				break;
			case 'Invoice':
				$updatedOrderStatus = $this->dashboard->saveOrderStatus($jobID, $orderStatus);
				$resp['updatedOrderStatus'] = $updatedOrderStatus;
				break;
			case 'Billed':
				$updatedOrderStatus = $this->dashboard->saveOrderStatus($jobID, $orderStatus);
				$resp['updatedOrderStatus'] = $updatedOrderStatus;
				break;
		}
		$resp['updatedOrderStatus'] = $updatedOrderStatus;
		echo json_encode($resp);
	}
	public function updateOrderItem($jobID, $ICN, $orderStatus) {		
		$resp = $this->dashboard->saveOrderItem($jobID, $ICN, $orderStatus);
		echo json_encode($resp);    		
	}
	// triggered from clicking 'Process' button in pop-up
	public function generateInvoice($jobID, $orderStatus) {	
		$invoiceID = $this->dashboard->getNextGlobalCounterValue($this->config->item('corporationID') , 'invoiceID');
		$itemICNs = explode("," , $this->input->post('ICNs'));
		$ICNList = $this->input->post('ICNs');
		foreach($itemICNs as $key => $ICN){ // change cmsStatus to 'Billed' for each item
			$this->dashboard->saveInvoiceData($jobID, $ICN, $invoiceID, $key + 1);
			$this->dashboard->saveOrderItem($jobID, $ICN, $orderStatus);
		}
		$updateStatus = $this->input->post('updateOrderStatus');
		if ($updateStatus == true) { // if all checkboxes were selected, change orderStatus to 'Billed'
			$this->dashboard->saveOrderStatus($jobID, $orderStatus);
		}
		$this->generateCSV($jobID, $ICNList);
		$this->generatePDF($jobID, $itemICNs);
		$comment = 'Invoice generated for ' . $jobID;
		$this->dashboard->saveUserAction($comment);
 	}
	public function generateCSV($jobID, $itemICNs) {
		// column headings
		$csv = "invoiceID, invoiceDate, isShippingInLine, agreementID, orderID, orderDate, salesOrderNumber, soldToAddressID, soldToName, invoiceLineNumber, lineReferenceNumber, supplierPartID, quantity, unitOfMeasure, unitPrice, itemDescription, lineItemSubtotal, shipToAddressID, shipToName, shipToStreet, shipToCity, shipToState, shipToPostalCode, shipToCountry, shipToEmail, invoiceTaxAmount, invoiceTaxDescription, summarySpecialHandlingAmount, summaryShippingAmount, summaryDiscountAmount \n";		
		
		$data = $this->dashboard->getCSVData($jobID, $itemICNs);
		
		foreach ($data as $item) {					
			if ($item->salesTax > 0) {
				$taxDescription = 'MA Sales Tax';
			} else {
				$taxDescription = '';
			}
			$lineSubtotal = $item->itemQuantity * $item->itemPrice;
			$csv .= $item->invoiceID . ', ' . $item->invoiceDate . ', ' . 'N, , ' . $item->purchaseOrder . ', ' . $item->datestamp . ', , , , ' . $item->invoiceLineNumber . ', ' . $item->poLineNumber . ', ' . $item->itemICN . ', ' . $item->itemQuantity . ', EA, '
			 . $item->itemPrice . ', ' . $item->itemName . ', ' . $lineSubtotal . ', ' . $item->shipToID . ', ' . $item->company . ', ' . $item->address1 . ', ' 
			. $item->city . ', ' . $item->stateName . ', ' . $item->zipCode . ', ' . $item->countryName . ', ' . $item->email . ', ' . $item->salesTax . ', ' . $taxDescription . ', 2.16, 0, 0' . "\n";
			log_message('TRACE', $csv);			
		}
		$filepath = $this->config->item('CSVpath');
		$csv_handler = fopen ($filepath . $jobID . ".csv", "w");
		fwrite ($csv_handler,$csv);
		fclose ($csv_handler);	
	}
	public function generatePDF($jobID, $itemICNs) {
		$selectedItems = array();		
		$orderHeader = $this->dashboard->getOrderHeader($jobID);
		$orderDetail = $this->dashboard->getOrderDetail($jobID);
		$invoiceID = $orderDetail{0}->invoiceID;
		foreach ($orderDetail as $order) {
			foreach ($itemICNs as $item) {
				if ($order->itemICN == $item) {
					$selectedItems[] = $order;
				}
			}			
		}
		$billingInfo = $this->dashboard->getOrderBillInfo($orderHeader->billToID);
		$shippingInfo = $this->dashboard->getShippingInfo($orderHeader->shipToID);
		$mpdfConfig = array(
			'mode' => 'utf-8',
			'default_font_size' => 0,     // font size - default 0
			'default_font' => 'Arial',    // default font family
			'margin_left' => 5,    	// 15 margin_left
			'margin_right' => 5,    	// 15 margin right
			'margin_top' => 5,     // 16 margin top
			'margin_bottom' => 5,    	// margin bottom
			'margin_header' => 3,     // 9 margin header
			'margin_footer' => 3,     // 9 margin footer
			'orientation' => 'P',  	// L - landscape, P - portrait
			'allow_output_buffering' => true
		);
		$mpdf = new \Mpdf\Mpdf($mpdfConfig);
		$sh_company = !empty($shippingInfo->company) ? $shippingInfo->company . '<br />' : '';
		$sh_address1 = !empty($shippingInfo->address1) ? $shippingInfo->address1 . '<br />' : '';
		$sh_address2 = !empty($shippingInfo->address2) ? $shippingInfo->address2 . '<br />' : '';
		$sh_address3 = !empty($shippingInfo->address3) ? $shippingInfo->address3 . '<br />' : '';
		$sh_city = !empty($shippingInfo->city) ? $shippingInfo->city . '<br />' : '';
		$sh_phone = !empty($shippingInfo->phone) ? $shippingInfo->phone . '<br />' : '';

		$b_company = !empty($billingInfo->company) ? $billingInfo->company . '<br />' : '';
		$b_address1 = !empty($billingInfo->address1) ? $billingInfo->address1 . '<br />' : '';
		$b_address2 = !empty($billingInfo->address2) ? $billingInfo->address2 . '<br />' : '';
		$b_address3 = !empty($billingInfo->address3) ? $billingInfo->address3 . '<br />' : '';
		$b_city = !empty($billingInfo->city) ? $billingInfo->city . '<br />' : '';
		$b_phone = !empty($billingInfo->phone) ? $billingInfo->phone . '<br />' : '';

		$o_emailAddr = !empty($orderHeader->emailAddr) ? $orderHeader->emailAddr . '<br />' : '';

		$datestampCnvrted = strtotime($orderHeader->datestamp);
        $formattedDate = date('m/d/Y h:i:s', $datestampCnvrted);

		$html = '<style>
				table {
					border-collapse: collapse;
				}
				th,
				td {
				  border: 1px solid #737373;
				  padding: 10px 15px;
				}
			</style>
			<h3>Invoice #' . $invoiceID . 'for Order #' . $orderHeader->purchaseOrder .'</h3>
			<table style="border: 0px;">
				<tr>
					<td style="width:50%;vertical-align:top;">
                    <strong>Ship To:</strong><br />'
					. $shippingInfo->firstName . ' ' . $shippingInfo->lastName . '<br />'					
                    . $sh_company
                    . $sh_address1
                    . $sh_address2
                    . $sh_address3
                    . $shippingInfo->city . ', ' . $shippingInfo->stateAbbr . ' ' . $shippingInfo->zipCode . '<br />'
                    . $sh_phone
				. '</td>
					<td style="width:50%;vertical-align:top;">
                    <strong>Bill To:</strong><br />'
					. $billingInfo->firstName . ' ' . $billingInfo->lastName . '<br />'
					. $b_company
					. $b_address1
					. $b_address2
					. $b_address3
                    . $billingInfo->city . ', ' . $billingInfo->stateAbbr . ' ' . $billingInfo->zipCode . '<br />'
                    . $b_phone
                    . $o_emailAddr
                . '</td>
			</table>
			<h2>Order Information</h2>
			<table style="width: 100%;">
			<thead>
			<tr>
				<th>ICN</th>
				<th>Name</th>
				<th>Quantity</th>
				<th>Unit Cost</th>
				<th>Unit Price</th>
				<th>Total</th>				
			</tr></thead><tbody>';
			$subTotal = 0;
			$totalTax = 0;
			foreach($selectedItems  as $row) {
				$lineTotal = $row->itemQuantity * $row->itemPrice;
				$subTotal += $lineTotal;
				$totalTax += $row->salesTax;
				$html .= '<tr>
					<td style="text-align:center;">' . $row->itemICN . '</td>
					<td style="text-align:center;">' . $row->itemName . '</td>
					<td style="text-align:center;">' . $row->itemQuantity . '</td>
					<td style="text-align:center;">' . number_format($row->itemCost, 2) . '</td>
					<td style="text-align:center;">' . number_format($row->itemPrice, 2) . '</td>
					<td style="text-align:center;"><span>$</span>' . number_format($lineTotal, 2) . '</td>			
				</tr>';
			}
		$html .= '</tbody></table>';
		$html .= '<table style="width: 100%;">
			<tr>
				<td colspan="7"  style="text-align:right">
					<strong>Merchandise Total: </strong>' . number_format($subTotal, 2) . '<br>
					<strong>Tax:</strong> $' . number_format($totalTax, 2) . '<br>
					<strong>Shipping:</strong> $' . number_format($orderHeader->totalShipping, 2) . '<br>
					<strong>Total Cost:</strong> $' . number_format(((float)$subTotal + (float)$totalTax + (float)$orderHeader->totalShipping - (float)$orderHeader->couponDiscount), 2) . 
				'</td>
			</tr>                        
		</table>';
		ob_end_clean();
		$mpdf->WriteHTML($html);
		$mpdf->Output();
		echo $html;		
	}
}