$(document).ready(function() {

    $('#select_order_status').on('click', function() {
        var msg_body = "";
        if(this.value == '1'){
            msg_body += "Dear <<MAIN_CONTACT>>,\n\n";
            msg_body += "Thank you very much for your request. Please check on our quotation attached to this mail. If you have any further questions please don’t hesitate to contact me again.";
        } else if(this.value == 2){
            msg_body += "Dear <<MAIN_CONTACT>>,\n\n";
            msg_body += "Thank you for the new order <<ORDER_ID>>. Please check on our confirmation attached.\n As soon we have the estimated finish date from our production department, we will send the sales contract to you.";
        } else if(this.value == 3){
        } else if(this.value == 4){
        } else if(this.value == 5){
            msg_body += "Dear <<MAIN_CONTACT>>,\n\n";
            msg_body += "Thank you again for your order <<CUSTOMER_ORDER_ID>>.  The estimated finish date is <<ESTIMATED_FINISH_DATE>>.\n\nPlease check the attached sales contract, sign it and send it back to us for confirmation. As soon we receive your confirmation we will issue the pro-forma invoice (PI) to you.";
        } else if(this.value == 6){
            msg_body += "Dear <<MAIN_CONTACT>>,\n\n";
            msg_body += "thank you for the confirmation of your order <<CUSTOMER_ORDER_NUMBER>>\n\nPlease check the pro-forma invoice <<ORDER_ID>> attached. If you have any further questions please don't hesitate to contact me again.";
        } else if(this.value == 7){
            msg_body += "Dear <<MAIN_CONTACT>>,\n\n";
            msg_body += "Your shipment has left our factory. The cargo ships ETD is: <<VESSEL_ETD>>  and it should arrive at your designated port on <<VESSEL_ETA>>.\n\nPlease find our commercial invoice for your further handling attached to this mail.";
        } else if(this.value == 8){
        } else if(this.value == 9){
            msg_body += "Dear <<MAIN_CONTACT>>,\n\n";
            msg_body += "it seems like it passed your attention, that our invoice is already <<DAYS_OVERDUE>> days overdue.\nWe know that this can happen in a busy working schedule and like to remind you gently on payment with attached file.\nIf you already have processed the payment, please provide us with your payment information and take our apology for this reminder.";
        }
        $("#history_comment").val(msg_body);
    });

	$("select.searchable").select2({
		'minimumInputLength' : 2 
	});


    $("input[type=text]").click(function() {
           $(this).select();
    });

    $('.choose_delivery_address').change(function() {
    	var address_id = $(this).val();
    	if(address_id == 0){
			return false;	
		}

        $.ajax({
            type: "POST",
            url: "/customers/addresses/showjson",
            dataType: "json",
            data: { 
                "address_id" : address_id
            },  
            success: function(data) {
                //$("input#vendor_contact").val(data.contact_name);
                //$("input#vendor_email").val(data.username);
                //$("input#vendor_phone").val(data.contact_phone);
                //$("input#vendor_deposit").val(data.default_deposit);
                //$("input#vendor_terms").val(data.payment_terms);
                //$("textarea#vendor_bank_details").val(data.bank_details);

                var address = "";
                address = data.description;
                address += "\n";
                address += data.address1;
                address += "\n";
                if(data.address2 != ""){
                    address += data.address2;
                    address += "\n";
                }
                address += data.postal_code + ", " + data.city;
                address += "\n";
                address += data.province + ", " + data.country;
                $("#delivery_address").html(address);
            }   
        });
	});

    $('.select_vendor').change(function() {
        var vendor_id = $(this).val();
        if(vendor_id == 0){
            return false;    
        }
        $.ajax({
            type: "POST",
            url: "/vendors/showjson",
            dataType: "json",
            data: { 
                "vendor_id" : vendor_id
            },  
            success: function(data) {
                $("input#vendor_contact").val(data.contact_name);
                $("input#vendor_email").val(data.username);
                $("input#vendor_phone").val(data.contact_phone);
                $("input#vendor_deposit").val(data.default_deposit);
                $("input#vendor_terms").val(data.payment_terms);
                $("textarea#vendor_bank_details").val(data.bank_details);

                var address = "";
                address = data.address1;
                address += "\n";
                if(data.address2 != ""){
                    address += data.address2;
                    address += "\n";
                }
                address += data.postal_code + ", " + data.city;
                address += "\n";
                address += data.province + ", " + data.country;
                $("#vendor_address").html(address);
            }   
        });
    });

    $('.select_customer').change(function() {
        var customer_id = $(this).val();
        if(customer_id == 0){
            return false;    
        }
        $.ajax({
            type: "POST",
            url: "/customers/showjson",
            dataType: "json",
            data: { 
                "customer_id" : customer_id
            },  
            success: function(data) {
                $("input#customer_contact").val(data.contact_name);
                $("input#customer_email").val(data.username);
                $("input#customer_phone").val(data.contact_phone);
                $("input#customer_deposit").val(data.default_deposit);
                $("input#customer_terms").val(data.payment_terms);
                $("input#ff_name").val(data.ff_name);
                $("input#ff_contact").val(data.ff_contact);
                $("input#ff_phone").val(data.ff_phone);
                $("input#ff_email").val(data.ff_email);
                $("input#ff_fax").val(data.ff_fax);

                var address = "";
                address = data.address1;
                address += "\n";
                if(data.address2 != ""){
                    address += data.address2;
                    address += "\n";
                }
                address += data.postal_code + ", " + data.city;
                address += "\n";
                address += data.province + ", " + data.country;
                $("#customer_address").html(address);

                update_items_available_view();
             //   oTable = $('.ajaxtable5').dataTable();
             //   oTable.fnReloadAjax();
            }   
        });
    });

 $(document).on("click", ".alert_bb", function(e) {
		bootbox.confirm("Hello world!", function(result) {
			alert("Perform"+result);
		});
});


    $("body").on("click", ".undelete", function(e){
    	e.preventDefault();
    	var target = $(this).attr('href');
        $(this).hide();
        $.ajax({
            type: "POST",
            url: target,
            dataType: "post",
            data: { 
                "delete" : 1
            },  
            success: function(data) {
            }   
        });

	});

    $("body").on("click", "a.select_customer", function(e){
		var customer_id  = $(this).closest("td").parent().find('td:eq(0)').html();
		var container = $("select[name=container]").val();

        $.ajax({
            type: "POST",
            url: "/orders/add_save",
            dataType: "html",
            data: { 
                "customer_id" : customer_id,
                "container" : container
            },
            success: function(data) {
            	window.location.replace("/orders/"+data);
            }   
        });
	});

    $("body").on("click", ".add_order_item", function(e){
        e.preventDefault();
        var product_id = $(this).attr('rel');
        var order_id = $("input#id").val();
        $(this).hide();

        $.ajax({
            type: "POST",
            url: "/orders/add_order_item",
            dataType: "json",
            data: { 
                "product_id" : product_id,
                "order_id" : order_id
            },  
            success: function(data) {
                update_order_items_view();
            }   
        });
    });

    function update_order_items_view(){
        var order_id = $("input#id").val();
        $.ajax({
            type: "POST",
            url: "/orders/ajax",
            dataType: "html",
            data: { 
                "order_id" : order_id
            },  
            success: function(data) {
                $("#tab_on_order").html(data);
            }   
        });
    }

    function update_items_available_view(){
        var customer_id = $("select#customer_id").val();
        $.ajax({
            type: "POST",
            url: "/orders/itemsavailable",
            dataType: "html",
            data: { 
                "customer_id" : customer_id
            },  
            success: function(data) {
                $("#tab_available").html(data);
            }   
        });
    }


    $("body").on("click", ".delete_order_item", function(e){
        e.preventDefault();
        var order_item_id = $(this).attr('id');
        $.ajax({
            type: "POST",
            url: "/orders/delete_order_item",
            dataType: "json",
            data: { 
                "order_item_id" : order_item_id
            },  
            success: function(data) {
                //$(".order_item_row_"+order_item_id).remove();
                update_order_items_view();
            }   
        });
    });

    $("body").on("change", ".order_item_row", function(e){
        var total_qty = new Number;
        var total_buy = new Number;
        var total_sell = new Number;
        var total_cbm = new Number;

        var vendor_deposit = parseInt($("#vendor_deposit").val());
        var customer_deposit = parseInt($("#customer_deposit").val());
        var customer_paid = parseFloat($(".customer_paid").text());
        var vendor_paid = parseFloat($(".customer_paid").text());

        $(".r1.order_item_row").each(function( index ) {
            var quantity = parseInt($(this).find("span.quantity input").val());
            var cbm = parseFloat($(this).find("span.cbm input").val());
            var buy_price = parseFloat($(this).find("span.buy_price input").val());
            var sell_price = parseFloat($(this).find("span.sell_price input").val());

            var line_cbm = quantity * cbm;
            var line_buy = quantity * buy_price;
            var line_sell = quantity * sell_price;

            total_qty += quantity;
            total_buy += line_buy;
            total_sell += line_sell;
            total_cbm += line_cbm;

            //$(this).find("span.calc_cbm").text(line_cbm.toFixed(3));
            $(this).find("span.calc_sell").text(line_sell.toFixed(2));
            //$(this).find("span.calc_buy").text(line_buy.toFixed(2));
        });

       //$('.order_calc_quantity').text(total_qty);
       //$('.order_calc_buy').text(total_buy.toFixed(2));
       $('.order_calc_sell').text(total_sell.toFixed(2));
       //$('.order_calc_cbm').text(total_cbm.toFixed(3));

        //var vendor_deposit_due = 0;
        //vendor_deposit_due = total_buy / 100 * vendor_deposit;

        //var customer_deposit_due = 0;
        //customer_deposit_due = total_sell / 100 * customer_deposit;

        //var vendor_balance = total_buy - vendor_paid;
        //var customer_balance = total_sell - customer_paid;

        //$('.vendor_deposit_due').text(vendor_deposit_due.toFixed(2));
        //$('.customer_deposit_due').text(customer_deposit_due.toFixed(2));
        //$('.vendor_balance').text(vendor_balance.toFixed(2));
        //$('.customer_balance').text(customer_balance.toFixed(2));

    });


$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw )
{
    if ( sNewSource !== undefined && sNewSource !== null ) {
        oSettings.sAjaxSource = sNewSource;
    }
 
    // Server-side processing should just call fnDraw
    if ( oSettings.oFeatures.bServerSide ) {
        this.fnDraw();
        return;
    }
 
    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;
    var iStart = oSettings._iDisplayStart;
    var aData = [];
 
    this.oApi._fnServerParams( oSettings, aData );
 
    oSettings.fnServerData.call( oSettings.oInstance, oSettings.sAjaxSource, aData, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );
 
        /* Got the data - add it to the table */
        var aData =  (oSettings.sAjaxDataProp !== "") ?
            that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;
 
        for ( var i=0 ; i<aData.length ; i++ )
        {
            that.oApi._fnAddData( oSettings, aData[i] );
        }
         
        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
 
        that.fnDraw();
 
        if ( bStandingRedraw === true )
        {
            oSettings._iDisplayStart = iStart;
            that.oApi._fnCalculateEnd( oSettings );
            that.fnDraw( false );
        }
 
        that.oApi._fnProcessingDisplay( oSettings, false );
 
        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' && fnCallback !== null )
        {
            fnCallback( oSettings );
        }
    }, oSettings );
};

});
