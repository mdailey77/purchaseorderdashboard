<?php
// prevent caching (php)
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Expires: ' . gmdate(DATE_RFC1123, time()-1));
?>
<div id="magnific-order-popup" class="popup-section">
    <button class="mfp-close closebtn" type="button" title="Close (Esc)">x</button>
    <?php
        if ($invoiceStatus == true) {
            echo '<form target="_blank" action="'. base_url() . index_page()  . '/main/generateInvoice/' . $orderHeader->jobID . '/Billed" id="invoiceProcess" method="post" accept-charset="utf-8">';
            echo '<input type="hidden" name="ICNs" value=""/>';
            echo '<input type="hidden" name="updateOrderStatus" value=""/>';
        };
    ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 border-bottom">
                <h5>Details for Order #<span id="jobid"><?php echo $orderHeader->jobID; ?></span></h5>
                <h5>Purchase Order:<?php echo $orderHeader->purchaseOrder; ?></h5>
            </div>            
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <address>
                    <strong>Ship To:</strong>
                    <?= $shippingInfo->firstName . ' ' . $shippingInfo->lastName ?><br />
                    <?= $shippingInfo->company != '' ? $shippingInfo->company . "<br />" : '' ?>
                    <?= $shippingInfo->address1 != '' ? $shippingInfo->address1 . "<br />" : '' ?>
                    <?= $shippingInfo->address2 != '' ? $shippingInfo->address2 . "<br />" : '' ?>
                    <?= $shippingInfo->address3 != '' ? $shippingInfo->address3 . "<br />" : '' ?>
                    <?= $shippingInfo->city . ', ' . $shippingInfo->stateAbbr . ' ' . $shippingInfo->zipCode ?><br />
                    <?= $shippingInfo->phone != '' ? $shippingInfo->phone . "<br />" : '' ?>
                </address>
            </div>
            <div class="col-md-6">
                <address>
                    <strong>Bill To:</strong>
                    <?= $billingInfo->firstName . ' ' . $billingInfo->lastName ?><br />
                    <?= $billingInfo->company != '' ? $billingInfo->company . "<br />" : '' ?>
                    <?= $billingInfo->address1 != '' ? $billingInfo->address1 . "<br />" : '' ?>
                    <?= $billingInfo->address2 != '' ? $billingInfo->address2 . "<br />" : '' ?>
                    <?= $billingInfo->address3 != '' ? $billingInfo->address3 . "<br />" : '' ?>
                    <?= $billingInfo->city . ', ' . $billingInfo->stateAbbr . ' ' . $billingInfo->zipCode ?><br />
                    <?= $billingInfo->phone != '' ? $billingInfo->phone . "<br />" : '' ?>
                    <?= $orderHeader->emailAddr != '' ? $orderHeader->emailAddr . "<br />" : '' ?>
                </address>
            </div>
        </div>
        <div class="row">            
            <div class="table-responsive">            
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">ICN</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Unit Cost</th>
                            <th scope="col">Unit Price</th>
                            <th scope="col">Vendor</th>
                            <th scope="col">Image Link</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                            $itemICN = '';
                            $totalPrice = 0;
                            $totalCost = 0;
                            $totalTax = 0;
                            foreach ($orderDetail as $order) {
                                $itemICN = $order->itemICN;
                                $datestampCnvrted = strtotime($order->datestamp);
                                $formattedDate = date('m/d/Y h:i:s', $datestampCnvrted);
                                echo '<tr>';
                                switch ($order->cmsStatus) {
                                    case "Open":
                                        echo '<td>Pending</td>';
                                        break;
                                    case "Submitted":
                                        echo '<td><button class="shipitem btn btn-primary">Ship</button></td>';
                                        break;
                                    case "Invoice":
                                        echo '<td><input class="invoicecheckbox" type="checkbox" id="' . $itemICN . '"></td>';
                                        break;
                                    case "Billed":
                                        echo '<td>Billed</td>';
                                        break;
                                    default:
                                        echo '<td></td>';
                                }
                                echo '<th scope="row" class="itemICN">' . $itemICN . '</th>
                                <td>' . html_entity_decode($order->itemName) . '</td>
                                <td>' . $order->itemQuantity . '</td>
                                <td>' . number_format($order->itemCost, 2) . '</td>
                                <td>' . number_format($order->itemPrice, 2) . '</td>';
                                if (empty($order->company1)) {
                                    echo '<td>NULL</td>';
                                } else {
                                    echo '<td>' . $order->company1 . '</td>';
                                }                                
                                foreach ($imageLinks as $link) {
                                    if ($link['ICN'] == $itemICN) {
                                        if (!empty($link['anchor'])) {
                                            echo '<td><a target="_blank" href="' . $link['anchor'] . '">View Image</a></td>';
                                        } else {
                                            echo '<td>No Image</td>';
                                        }
                                    }
                                }                               
                                echo '</tr>';
                                
                                $totalPrice += $order->itemQuantity * $order->itemPrice;
                                //$totalCost += $order->itemQuantity * $order->itemCost;
                                $totalTax += $order->salesTax;
                            }?>                            
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" class="text-right">
                                <?php  ?>
                                <strong>Merchandise Total: </strong> $<?= number_format($totalPrice, 2) ?><br />
                                <strong>Tax:</strong> $<?= number_format($totalTax, 2) ?><br />
                                <strong>Shipping:</strong> $<?= number_format($orderHeader->totalShipping, 2) ?><br />
                                <strong>Total Cost:</strong> $<?= number_format(((float)$totalPrice + (float)$totalTax + (float)$orderHeader->totalShipping - (float)$orderHeader->couponDiscount), 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php if ($invoiceStatus == true) { ?>
            <div class="row">           
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="invoiceBttn">Process</button>
                </div>
            </div>     
        <?php } ?>
    </div>
    <?php
        if ($invoiceStatus == true) {
            echo '</form>';
        } 
    ?>
</div>
<script>
    $(function(){
        var getUrl = window.location;
        var baseurl = getUrl .protocol + "//" + getUrl.host;
        // 'Ship' button appears when orderStatus is 'Submitted'
        $(".shipitem").click(function(){
            selecteditem = $(this);
            selecteditem.text('Shipping').attr("disabled","disabled");
            selecteditem.removeClass('btn-primary').addClass('btn-secondary');
            itemICN = selecteditem.closest('tr').children('.itemICN').text();
            $.ajax({
                method: "POST",
                url: baseurl + '/purchaseorderdashboard/trunk/index.php/main/updateOrderItem/' + <?=$orderHeader->jobID?> + '/' + itemICN +  '/Invoice',
                contentType: "application/json",
                dataType: 'json'
            })
            .done(function(response) {
                selecteditem.text('Shipped');
            })
            .fail(function(jqxhr, settings, ex) {
                alert('An error occurred, ' + ex);
            })
        });
        // when 'Process' button is clicked, changes status from 'Invoice' to 'Billed'
        $("#invoiceProcess").submit(function(event){
            var form = $(this);
            var url = form.attr('action');
            var checkboxes = [];
            var ICNarray = [];
            var ICNs;
            var i = 0;
            $('#invoiceBttn').text('Processed').attr("disabled","disabled");
            $('#invoiceBttn').removeClass('btn-primary').addClass('btn-secondary');
            $('.invoicecheckbox').each(function(index, value){
                if($(this).is(':checked')){
                    var itemICN = $(this).closest('tr').children('.itemICN').text();
                    console.log('checked ICN[' + index + '] ' + itemICN);
                    ICNarray[index] = itemICN;                                             
                } else {
                    i++;
                }
            });
            ICNs = ICNarray;
            $('input[name=ICNs]').val(ICNs); 
            if (i == 0){ // if all checkboxes are selected, change orderStatus to true             
                $('input[name=updateOrderStatus]').val('true');                               
            } else {
                $('input[name=updateOrderStatus]').val('false'); 
            }            
        });
    });
</script>
