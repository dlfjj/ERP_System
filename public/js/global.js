$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function() {
        $('input').keyup(function() {
         //       this.value = this.value.toLocaleUpperCase();
        });
        $('textarea').keyup(function() {
                //this.value = this.value.toLocaleUpperCase();
        });
    });

	$('form').on('submit', function () {
		//$('input.btn').prop('disable', true);
		$('input.btn').hide();
		//$(this).find('input.btn').val('Wait...');
	});


	$( ".datepicker" ).datepicker({
		defaultDate: +7,
		showOtherMonths:true,
		autoSize: true,
		dateFormat: 'yy-mm-dd'
	});

	$('.select2').select2({
		minimumInputLength: 3
	});


    $("a.basic-alert").click(function(e) {
        e.preventDefault();
        var msg = $(this).attr('rel');
        bootbox.alert(msg, function() {
        });
    });


	$("body").on("click", "a.form-submit", function(e){
		e.preventDefault();
		var target_url = $(this).attr('data-target-form');
		var action     = $(this).text();


		//var btn = $(this);
		//btn.button('loading');

		if ($(this).attr('data-target-form') !== undefined){
			var form = $(this).attr('data-target-form');
		} else {
			var form = $(this).closest("form").attr('id');
		}

		if(action == 'New' || action == 'Delete' || action == 'Duplicate'){
			bootbox.confirm("Are you sure?", function(result) {
				if(result){
					$('form#'+form).submit();
				}
			});
		} else {
			$('form#'+form).submit();
		}
	});

	$("body").on("click", "input.form-submit-conf", function(e){
		e.preventDefault();
		var form = $(this).closest("form");
		bootbox.confirm("Are you sure?", function(result) {
			if(result){
				$(form).submit();
			}
		});
	});

	$("body").on("click", "a.form-submit-conf", function(e){
		e.preventDefault();
		var target_url = $(this).attr('data-target-form');

		if ($(this).attr('data-target-form') !== undefined){
			var form = $(this).attr('data-target-form');
		} else {
			var form = $(this).closest("form").attr('id');
		}

		bootbox.confirm("Are you sure?", function(result) {
			if(result){
				$('form#'+form).submit();
			}
		});
	});

	$("body").on("click", "a.conf", function(e){
		e.preventDefault();
		var target_url = $(this).attr('href');

		bootbox.confirm("Are you sure?", function(result) {
			if(result){
				window.location.replace(target_url);
			}
		});
	});


	$.extend( $.validator.defaults, {
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
				var message = errors == 1
				? 'You missed 1 field. It has been highlighted.'
				: 'You missed ' + errors + ' fields. They have been highlighted.';
				noty({
					text: message,
					type: 'error',
					timeout: 2000
				});
			}
		}
	});

	$("form.form-validate").validate();
	$("form.form-validate1").validate();


});
/*
	$("body").on("click", "a.ajax-submit", function(e){
		e.preventDefault();
		var target_url = $(this).attr('data-target-url');
		var action     = $(this).text();
		if(action == 'New' || action == 'Delete' || action == 'Duplicate'){
			bootbox.confirm("Are you sure?", function(result) {
				if(result){
					$(this).closest("form").submit();
					$.ajax({
						type: "POST",
						url: target_url,
						dataType: "json",
						data: { 
							"action" : action
						},  
						success: function(data) {
							var ret_type = data.ret_type;
							var ret_msg = data.ret_msg;
							var ret_url = data.ret_url;
							$("div.wallop").html(ret_msg);
							if(ret_type == 'error'){
								//$("div.wallop").addClass("wallop-error");
								alert('Error');
							} else {
								if(ret_url != ''){
									window.location.replace(ret_url);
								}
								$("div.wallop").addClass("wallop-success");
							}
						}   
					});
				}
			});
		} else {
			alert("OK 2");
		}
	});
});
*/
