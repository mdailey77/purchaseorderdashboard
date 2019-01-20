$(function(){
    var getUrl = window.location;
    var baseurl = getUrl .protocol + "//" + getUrl.host;
    function updateStatus(jobid, orderStatus){
        $.ajax({
            method: "POST",
            url: baseurl + '/purchaseorderdashboard/trunk/index.php/main/updateOrderStatus/' + jobid + '/' + orderStatus,
            contentType: "application/json",
            dataType: 'json'
        })
        .done(function(response) {
            location.reload();
        })
        .fail(function(jqxhr, settings, ex) {
            alert('An error occurred, ' + ex);
        })
    }
    // Filter orders by order status
    $("#filterSubmit").click(function(){ 
        $(this).text('Submitting').attr("disabled","disabled");
        $(this).removeClass('btn-primary').addClass('btn-secondary');
        $('#filterOrders').submit();      
    });
    $(".orderdetail").magnificPopup({
		type: 'ajax',
        modal:true,
        callbacks: {
            close: function() {
                var magnificPopup = $.magnificPopup.instance;
                var popupEl = magnificPopup.content;
                var orderStatus = '';
                var jobid = popupEl.find('#jobid').text();
                if (popupEl.find('button').hasClass('shipitem')){
                    orderStatus = 'Invoice';
                    i = 0;
                   
                    $('.shipitem').each(function(){                    
                        if(!$(this).prop('disabled')) {
                            i++;                                                  
                        }                 
                    });
                    if (i == 0) {
                        updateStatus(jobid, orderStatus); // change orderStatus if all 'Shipped' buttons have been clicked   
                    }                    
                }
            },
            afterClose: function() {
                location.reload();
            }          
        }
    }); 
    // 'Process' button click changes orderStatus to 'Submitted'  
    $(".orderprocess").click(function(){
        $(this).text('Processing').attr("disabled","disabled");
        $(this).removeClass('btn-primary').addClass('btn-secondary');
        var selectedRow = $(this);
        var jobid = selectedRow.data('jobid');  
        $.ajax({
            method: "POST",
            url: baseurl + '/purchaseorderdashboard/trunk/index.php/main/updateOrderStatus/' + jobid + '/Submitted',
            contentType: "application/json",
            dataType: 'json'
        })
        .done(function(response) {
            location.reload();
        })
        .fail(function(jqxhr, settings, ex) {
            alert('An error occurred, ' + ex);
        })    
    });
    // Date filter for orders
    var dateFormat = "mm/dd/yy",
    from = $("#fromDate")
    .datepicker({
        defaultDate: "-1m",
        changeMonth: true,
        numberOfMonths: 1
    })
    .on("change", function() {
        to.datepicker("option", "minDate", getDate( this ));
    }),
    to = $("#toDate").datepicker({
        changeMonth: true,
        numberOfMonths: 1
    })
    .on("change", function() {
        from.datepicker("option", "maxDate", getDate( this ));
    });

    function getDate(element) {
        var date;
        try {
            date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
            date = null;
        }    
        return date;
    };
});