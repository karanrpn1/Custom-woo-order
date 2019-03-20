jQuery(document).ready(function($) {
    
    // Perform AJAX on form submit
    $('#submitOrder').on('click', function(e){
		var action = $('input[name="action"]').val();	
		var type = $('input[name="productType"]:checked').val();
		var monday = $('#new-order-create input#monday_regular').val();
		var tuesday = $('#new-order-create input#tuesday_regular').val();
		var wednesday = $('#new-order-create input#wednesday_regular').val();
		var thursday = $('#new-order-create input#thursday_regular').val();
		var friday = $('#new-order-create input#friday_regular').val();
		var childList = $("#childIds").val();
		var postId = $('input[name="postId"]').val();
		
		var error = false;
		$("#new-order-create .totalMeal input[type='number']").each(function(){
			
			var inputValue = $(this).val();
			var childValue = $("input#"+jQuery(this).attr('data-substract')).val();
		
			var minimumInputValue = parseInt(inputValue)+parseInt(childValue);
			
			if(inputValue==0) {
				if(childValue>0) {
					var subVal = 30-parseInt(childValue);
					$(this).next("span.error").html('Minimum Quantity '+subVal);
					$(this).next("span.error").addClass('visible');
					error = true;
				}
				else {
					$(this).next("span.error").removeClass('visible');					
				}
			}
			else {
				if(inputValue>=30) {
					if(minimumInputValue>=0) {
						$(this).next("span.error").removeClass('visible');						
					}
					else {
						$(this).next("span.error").html('Value must be greater than or equals to '+childValue);
						$(this).next("span.error").addClass('visible');
						error = true;
					}
					
				}
				else {
					//Value must be greater than 30
					if(minimumInputValue>=30) {
						$(this).next("span.error").removeClass('visible');	
						
					}
					else {
						var subVal = 30-parseInt(childValue);						
						$(this).next("span.error").html('Minimum Quantity 30');
						$(this).next("span.error").html('Minimum Quantity '+subVal);
						$(this).next("span.error").addClass('visible');
						error = true;
					}
					
				}
			}
			
		});	
		
		/*var mondayValue = $('#new-order-create .totalMeal input[name="mondayPackage"]').val();
		var tuesdayValue = $('#new-order-create .totalMeal input[name="tuesdayPackage"]').val();
		var wednesdayValue = $('#new-order-create .totalMeal input[name="wednesdayPackage"]').val();		
		var thursdayValue = $('#new-order-create .totalMeal input[name="thursdayPackage"]').val();		
		var fridayValue = $('#new-order-create .totalMeal input[name="fridayPackage"]').val();*/
		
		var appendedLink = "&mondayVal="+monday+"&tuesdayVal="+tuesday+"&wednesdayVal="+wednesday+"&thursdayVal="+thursday+"&fridayVal="+friday;	
		var link1 = $('input[name="redirect-url"]').val();
		var redirectLink = "?redUrl="+link1+appendedLink;
		
		if(error==false) {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_order_object.ajaxurl,
				data: { 
					'action': action,
					'type': type,
					'monday': monday,
					'tuesday': tuesday,
					'wednesday': wednesday,
					'thursday': thursday,
					'friday': friday,
					'childList': childList,
					'postId': postId
				},
				success: function(data){
					if(data.status=="success") {
						$(window).off("beforeunload");
						if(data.redirectUrl=="cartPage") {
							document.location.href = ajax_order_object.redirecturl+redirectLink;
						}
						else if(data.redirectUrl=="myaccount") {
							document.location.href = ajax_order_object.myaccountUrl;
						}		
						else if(data.redirectUrl=="samePage") {
							window.location.reload();
						}					
					}
					else {
						alert("Something went wrong! Please try after some time");
					}
				}
       		});
		}
      
        e.preventDefault();
    });
		
	$('body').on('click', '.deleteChild', function(e) {	
		 e.preventDefault(); 										   
		 if (confirm('Are you sure you want to delete this?')) {
			var childId = $(this).attr('data-id');
			var list = $("#childIds").val(); 
			var ref = $(this);
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_order_object.ajaxurl,
				data: { 
					'action': 'ajaxDeleteChild',
					'id': childId		 		
				},
				success: function(data){	
					var newList = removeValue(list, childId, ",");
					$("#childIds").val(newList); 					
				   	ref.parents('tr').fadeOut(1000,function(){ ref.remove(); });
				}
			});
		 }
	}); 
	
	$('body').on('click', '.deleteOptIn', function(e) {	
		 e.preventDefault(); 										   
		 if (confirm('Are you sure you want to delete this?')) {
			var id = $(this).attr('data-id');
			
			var ref = $(this);
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_order_object.ajaxurl,
				data: { 
					'action': 'ajaxDeleteOptIn',
					'id': id		 		
				},
				success: function(data){						
				   	ref.parents('tr').fadeOut(1000,function(){ ref.remove(); });
				}
			});
		 }
	}); 
	
	$('body').on('click', '.deleteCloseDateForm', function(e) {	
		 e.preventDefault(); 										   
		 if (confirm('Are you sure you want to delete this?')) {
			var id = $(this).attr('data-id');
			
			var ref = $(this);
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_order_object.ajaxurl,
				data: { 
					'action': 'ajaxDeleteCloseForm',
					'id': id		 		
				},
				success: function(data){						
				   	ref.parents('tr').fadeOut(1000,function(){ ref.remove(); });
				}
			});
		 }
	});
	
	$('body').on('click', '.deleteSchoolRediness', function(e) {	
		 e.preventDefault(); 										   
		 if (confirm('Are you sure you want to delete this?')) {
			var id = $(this).attr('data-id');
			
			var ref = $(this); 
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_order_object.ajaxurl,
				data: { 
					'action': 'ajaxDeleteSchoolRediness',
					'id': id		 		
				},
				success: function(data){						
				   	ref.parents('tr').fadeOut(1000,function(){ ref.remove(); });
				}
			});
		 }
	}); 
	
	/* CLOSE DATE FORM SUBMIT ON ORDER PAGE */
	$('body').on('click', '#submitCloseDateOrderForm', function(e) {
		e.preventDefault();
		var closeDate = $("#closeDate").val();
		var closeReason = $("#closeReason").val();
		if(closeDate) {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_order_object.ajaxurl,
				data: { 
					'action': 'ajaxSubmitCloseDateForm',
					'date': closeDate,
					'reason': closeReason	
				},
				success: function(data){	
					if(data.status=="success") {
						//console.log(data);
						var tblTotal = $('#closeDateTable tr').length;						
						$('#closeDateTable tr:last').after('<tr><td>'+tblTotal+'</td><td class="choiceDateCloseForm" data-startdate="'+data.startDate+'" data-enddate="'+data.endDate+'">'+data.date+'</td><td>'+data.reason+'</td><td> <a href="#" class="deleteCloseDateForm" data-id="'+data.postId+'">Cancel</a></td></tr>');
						$("#closeReason").val("");
						$("#closeDate").val("");						
						reinitalizeRangePicker();
					}					
				} 
			});		
		}
		else {
			alert("Date is empty !!");
		}		
	});
	
	
	function reinitalizeRangePicker() {
		
		var disableDates = [];
		$(".choiceDateCloseForm").each(function(){
			var startDate = $(this).attr('data-startdate');
			var endDate = $(this).attr('data-enddate');
			
			if(endDate) {
				startDate = new Date(startDate);
				endDate = new Date(endDate);
				
				var dates = [],
					  currentDate = startDate,
					  addDays = function(days) {
						var date = new Date(this.valueOf());
						date.setDate(date.getDate() + days);
						return date;
					  };
				  while (currentDate <= endDate) {
					//disableDates.push(currentDate);
					disableDates.push( ('0' + (currentDate.getMonth()+1)).slice(-2) + '-' + ('0' + currentDate.getDate()).slice(-2) + '-' + currentDate.getFullYear());
					
					currentDate = addDays.call(currentDate, 1);
				  }
			}
			else {
				startDate = new Date(startDate);
				
				disableDates.push( ('0' + (startDate.getMonth()+1)).slice(-2) + '-' + ('0' + startDate.getDate()).slice(-2) + '-' + startDate.getFullYear());
			} 
		  
		});	
		
		var cur = -1, prv = -1;
		
		$('#jrange div').datepicker('option','beforeShowDay',function ( date ) {				
						
						var dmy = (date.getMonth()+1); 
						if(date.getMonth()<9) 
							dmy="0"+dmy; 
						dmy+= "-"; 
						
						if(date.getDate()<10) dmy+="0"; 
							dmy+=date.getDate() + "-" + date.getFullYear(); 
						
						//console.log(dmy+' : '+($.inArray(dmy, disableDates)));
						
						if (($.inArray(dmy, disableDates) != -1) || date.getDay()==0 || date.getDay()==6) {
							return [false, "","disable"]; 
						} else{
							if(date.getTime() >= Math.min(prv, cur) && date.getTime() <= Math.max(prv, cur)) {	
								return [true,'date-range-selected'];
							}
							else {
								return [true,''];
							}
						}
				   
				   });
		
		$('#jrange div').datepicker('option','onSelect',function ( dateText, inst ) {
			  var d1, d2;
			  prv = cur;
			  cur = (new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay)).getTime();
			  if ( prv == -1 || prv == cur ) {
				 prv = cur;
				 $('#jrange input').val( dateText );
			  } else {
				 d1 = $.datepicker.formatDate( 'mm/dd/yy', new Date(Math.min(prv,cur)), {} );
				 d2 = $.datepicker.formatDate( 'mm/dd/yy', new Date(Math.max(prv,cur)), {} );
				 $('#jrange input').val( d1+' - '+d2 );
			  }
		   });
	
		}
		
	$("#childID").on('input', function() {
		var val = $(this).val();
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_order_object.ajaxurl,
			data: { 
				'action': 'ajaxChildIdCheck',
				'val': val
			},
			success: function(data){	
				if(data.status=="success") {
					$("#childIdError").html(data.message);	
					$('button[name="saveChildInfo"]').removeClass("disableBtn");
					$('button[name="saveChildInfo"]').prop("disabled",false);
				}
				else if(data.status=="error") {
					$("#childIdError").html(data.message);	
					$('button[name="saveChildInfo"]').addClass("disableBtn");
					$('button[name="saveChildInfo"]').prop("disabled",true);
				}
				else {
					$("#childIdError").html(data.message);	
					$('button[name="saveChildInfo"]').addClass("disableBtn");
					$('button[name="saveChildInfo"]').prop("disabled",true);
				}
			} 
		});									   	
								   	
   	});
 
});  

function removeValue(list, value, separator) {
  separator = separator || ",";
  var values = list.split(separator);
  for(var i = 0 ; i < values.length ; i++) {
    if(values[i] == value) {
      values.splice(i, 1);
      return values.join(separator);
    }
  }
  return list;
}

function cTrig(clickedid) { 
  if (document.getElementById(clickedid).checked == true) {
	
	var box= confirm("Are you sure you want to activate this order?");
	if (box==true) {
		var id = document.getElementById(clickedid).value; 
		
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_order_object.ajaxurl,
			data: { 
				'action': 'updateOrderStatus',
				'id': id,
				'status': 1 
			},
			success: function(data){					
				jQuery("#msg").html("<div class='successMsg'>Your order status has been updated it will take 2 days to reflect changes for your order.</div>");
				//window.location.reload();
			}
		});
		return true;		
	}
	else {
	   document.getElementById(clickedid).checked = false;
	}	
  } else {
   	var box= confirm("Are you sure you want to deactivate this order?");
	if (box==true) {
		var id = document.getElementById(clickedid).value; 
		
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_order_object.ajaxurl,
			data: { 
				'action': 'updateOrderStatus',
				'id': id,
				'status': 0
			},
			success: function(data){	
				jQuery("#msg").html("<div class='successMsg'>Your order status has been updated it will take 2 days to reflect changes for your order.</div>");
				//window.location.reload();
			}
		});
		return true;
	}
	else {
	   document.getElementById(clickedid).checked = true;
	}		
  }
}


/* ADMIN ON OFF ORDER */
function recuringTrigger(clickedid) {		
	if (document.getElementById(clickedid).checked == true) {
	 
		var box= confirm("Are you sure you want to activate this order?");
		if (box==true) {
			var id = document.getElementById(clickedid).value; 
			
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_order_object.ajaxurl,
				data: { 
					'action': 'adminOrderUpdateStatus',
					'id': id,
					'status': 1 
				},
				success: function(data){	
					console.log(data);
					//jQuery("#msg").html("<div class='successMsg'>Your order status has been updated it will take 2 days to reflect changes for your order.</div>");
				}
			});
			return true;		
		}
		else {
		   document.getElementById(clickedid).checked = false;
		}	
	} 
	else {
		var box= confirm("Are you sure you want to deactivate this order?");
		if (box==true) {
			var id = document.getElementById(clickedid).value; 
			
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajax_order_object.ajaxurl,
				data: { 
					'action': 'adminOrderUpdateStatus',
					'id': id,
					'status': 0
				},
				success: function(data){	
					console.log(data);
					//jQuery("#msg").html("<div class='successMsg'>Your order status has been updated it will take 2 days to reflect changes for your order.</div>");
				}
			});
			return true;
		}
		else {
		   document.getElementById(clickedid).checked = true;
		}		
	}
}

/* ADMIN DELETE STATE */
function deleteState(clickedid) {		

	var box= confirm("Are you sure you want to delete this ?");
	if (box==true) {
		
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_order_object.ajaxurl,
			data: { 
				'action': 'adminDeleteState',
				'id': clickedid
			},
			success: function(data){	
				$("#delState_"+clickedid).parents('tr').fadeOut(1000,function(){ $(this).remove(); });
			}
		});			 
	}
			
}


