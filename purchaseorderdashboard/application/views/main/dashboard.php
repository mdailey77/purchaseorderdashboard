<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$this->load->view('header'); ?>
			<div class="d-flex justify-content-center mt-5 mb-3">
				<div class="btn-toolbar">
					<?php $attributes = array('class' => 'form-inline', 'id'=>'filterOrders');
					echo form_open('main/index/', $attributes) . "\n" ?>
						<div class="d-flex flex-row">
							<label class="mr-2" for="fromDate">From</label>
							<input type="text" class="form-control mr-sm-2" id="fromDate" name="fromDate" value="<?= ( isset($fromDate) ? $fromDate : '' )?>">
						</div>
						<div class="d-flex flex-row">
							<label class="mr-2" for="toDate">To</label>
							<input type="text" class="form-control mr-sm-2" id="toDate" name="toDate" value="<?= ( isset($toDate) ? $toDate : '' )?>">							
						</div>
						<div class="dropdown ml-2 mr-5">
							<div class="form-group">
								<label class="mr-2" for="filterDropDown">Order Status</label>
								<select class="form-control" id="filterDropDown" name="filterDropDown">
									<option <?php if($filter=='Pending'){echo 'selected';}?>>Pending</option>
									<option <?php if($filter=='Submitted'){echo 'selected';}?>>Submitted</option>
									<option <?php if($filter=='Invoice'){echo 'selected';}?>>Invoice</option>
									<option <?php if($filter=='Billed'){echo 'selected';}?>>Billed</option>				
								</select>
							</div>
						</div>
						<button type="submit" class="btn btn-primary" id="filterSubmit">Show Orders</button>
					<?= form_close() . "\n" ?>
				</div>
            </div>
			<?php
			if (!empty($pending_orders)) { ?>
				<table id="dashboard" class="table table-striped table-bordered">
					<thead class="thead-dark">
						<tr>
							<th scope="col">Job ID</th>
							<th scope="col">PO #</th>
							<th scope="col">Date</th>
							<th scope="col">Status</th>
							<th scope="col"></th>
						</tr>
					</thead>
					<tbody>					
							<?php						
								foreach ($pending_orders as $order) {
									$datestampCnvrted = strtotime($order->datestamp);
									$formattedDate = date('m/d/Y h:i:s', $datestampCnvrted);
									echo'<tr><th scope="row"><a class="orderdetail" href="' . base_url() . 'index.php/main/orderdetails/' . $order->jobID . '/' . $order->orderStatus . '">' . $order->jobID . '</a></th>
									<td>' . $order->purchaseOrder . '</td>
									<td>' . $formattedDate . '</td>
									<td class="orderstatus">' . $filter .'</td>' .
									($order->orderStatus=='Pending' ? '<td><button data-jobid="' . $order->jobID . '" class="orderprocess btn btn-primary">Process</button></td></tr>' : '<td>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>');
								}
							?>
					</tbody>
				</table>
			<?php } else { ?>
				<div class="d-flex justify-content-center mt-5 mb-3">
					<?php echo '<p>There are no ' . $filter . ' orders. Click \'Show Orders\' to refresh.</p>'; ?>
				</div>	
			<?php } ?>
		</div>
<?php $this->load->view('footer'); ?>	