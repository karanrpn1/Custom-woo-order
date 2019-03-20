jQuery(document).ready(function($){	
	$("#datepicker1").datepicker({
		minDate: 1,	
		beforeShowDay: noWeekendsOrHolidays,
		onSelect: function(dateText, inst) {
			var date = $(this).datepicker("getDate");			
			var dayOfWeek = date.getDay();	
			if(dayOfWeek==1) {
				$("#disabledChoice").hide();
				$("#disabledChoice input[type='radio']").attr("disabled");
				$("#lunch-opt label.checkContainer:first-child").click();
			}
			else {
				$("#disabledChoice").show();
				$("#disabledChoice input[type='radio']").removeAttr("disabled");
			}
			
			if(dayOfWeek==1) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-monday-value"));				
				$("#totalPackage").val($("#totalPackageValue").attr("data-monday-value"));
			}
			else if(dayOfWeek==2) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-tuesday-value"));				
				$("#totalPackage").val($("#totalPackageValue").attr("data-tuesday-value"));
			}
			else if(dayOfWeek==3) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-wednesday-value"));
				$("#totalPackage").val($("#totalPackageValue").attr("data-wednesday-value"));
			}
			else if(dayOfWeek==4) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-thursday-value"));				
				$("#totalPackage").val($("#totalPackageValue").attr("data-thursday-value"));
			}
			else if(dayOfWeek==5) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-friday-value"));				
				$("#totalPackage").val($("#totalPackageValue").attr("data-friday-value"));
			}
			
			//mealTotalCheck();
		}
	}); 
	
	/*$("#mealPackage").keyup(function(){
		mealTotalCheck();
	});
	
	function mealTotalCheck() {
		var date = $("#datepicker1").val();
		if(date.length===0) {
			$("#error").html('<span class="errorMessage">Please select date first...</span>');	
		}
		else {
			if(($("#mealPackage").val().length)>0) {
				var currentInputValue = parseInt($("#mealPackage").val());
				var currentDayValue = parseInt($("#currentDayValue").val());
				
				if(currentInputValue>currentDayValue) {
					$("#error").html('<span class="errorMessage">Number of meals must be less than total package.</span>');
					$("form#lunch-opt button[type='submit']").attr("disabled","disabled");
				}
				else {	
					$("#error").html("");
					$("form#lunch-opt button[type='submit']").removeAttr("disabled");	 
				}
			}
		}
	}*/
	
	natDays = [];
	var j = 0;
	$(".choiceDate").each(function(){
		var chDate = $(this).attr('data-id');
		chDate = chDate.split("-");
		
		var d = new Date(chDate[0],chDate[1]-1,chDate[2],0,0,0,0);
		d.setDate((d.getDate()-d.getDay())+1);
		
		var date = d;
		const DAY = 1000 * 60 * 60 * 24;
		
		var i;
		for (i = 0; i <5; i++) {
			//alert(date);
			natDays[j]=[date.getMonth()+1,date.getDate(),date.getFullYear()];
			date.setTime(date.getTime() + DAY);
			j++;
		}
		
	});	
	
	//console.log(natDays);
	
	function nationalDays(date) {
		for (i = 0; i < natDays.length; i++) {
		  if (date.getMonth() == natDays[i][0] - 1
			  && date.getDate() == natDays[i][1]) {
			return [false, natDays[i][2] + '_day'];
		  }
		}
	  return [true, ''];
	}
	function noWeekendsOrHolidays(date) {
		var noWeekend = $.datepicker.noWeekends(date);
		if (noWeekend[0]) {			
			return nationalDays(date);
			
		} else {			
			return noWeekend;
		}
	}
	
	
	
	$("#new-order-create .totalMeal").find("input[type='number']").on('input', function() {
		
			var inputValue = $(this).val();
			var childValue = $("input#"+jQuery(this).attr('data-substract')).val();
		
			var minimumInputValue = parseInt(inputValue)+parseInt(childValue);
			
			if(inputValue==0) {
				if(childValue>0) {
					var subVal = 30-parseInt(childValue);
					$(this).next("span.error").html('Minimum Quantity '+subVal);
					$(this).next("span.error").addClass('visible');
				}
				else {
					$(this).next("span.error").removeClass('visible');
					jQuery("input#"+jQuery(this).attr('data-change')).val(minimumInputValue);
					
				}
			}
			else {
				if(inputValue>=30) {
					if(minimumInputValue>=0) {
						$(this).next("span.error").removeClass('visible');	
						jQuery("input#"+jQuery(this).attr('data-change')).val(minimumInputValue);
					}
					else {
						$(this).next("span.error").html('Value must be greater than or equals to '+childValue);
						$(this).next("span.error").addClass('visible');
					}
					
				}
				else {
					//Value must be greater than 30
					if(minimumInputValue>=30) {
						$(this).next("span.error").removeClass('visible');	
						jQuery("input#"+jQuery(this).attr('data-change')).val(minimumInputValue);
					}
					else {
						var subVal = 30-parseInt(childValue);
						$(this).next("span.error").html('Minimum Quantity '+subVal);
						$(this).next("span.error").addClass('visible');
					}
				}
			}
	});
	
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

//console.log(disableDates);

//DATE RANGE PICKER FOR CLOSE DATE FORM 	
$.datepicker._defaults.onAfterUpdate = null;
var datepicker__updateDatepicker = $.datepicker._updateDatepicker;
$.datepicker._updateDatepicker = function( inst ) {
datepicker__updateDatepicker.call( this, inst );
var onAfterUpdate = this._get(inst, 'onAfterUpdate');
if (onAfterUpdate)
  onAfterUpdate.apply((inst.input ? inst.input[0] : null),
	 [(inst.input ? inst.input.val() : ''), inst]);
}
$(function() {
  	var cur = -1, prv = -1;
  	//disableDates = ['05-24-2018','05-28-2018'];
   	$('#jrange div').datepicker({
            //numberOfMonths: 3,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
			minDate: 1,
            beforeShowDay: function ( date ) {				
					
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
               
               },
            onSelect: function ( dateText, inst ) {
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
               },
            onChangeMonthYear: function ( year, month, inst ) {
                  //prv = cur = -1;
               },
            onAfterUpdate: function ( inst ) {
                  $('<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" data-handler="hide" data-event="click">Done</button>')
                     .appendTo($('#jrange div .ui-datepicker-buttonpane'))
                     .on('click', function () { $('#jrange div').hide(); });
               }
         })
      .position({
            my: 'left top',
            at: 'left bottom',
            of: $('#jrange input')
         })
      .hide();
   	$('#jrange input').on('focus', function (e) {
         var v = this.value,
             d;
         try {
            if ( v.indexOf(' - ') > -1 ) {
               d = v.split(' - ');
               prv = $.datepicker.parseDate( 'mm/dd/yy', d[0] ).getTime();
               cur = $.datepicker.parseDate( 'mm/dd/yy', d[1] ).getTime();
            } else if ( v.length > 0 ) {
               prv = cur = $.datepicker.parseDate( 'mm/dd/yy', v ).getTime();
            }
         } catch ( e ) {
            cur = prv = -1;
         }
         if ( cur > -1 )
            $('#jrange div').datepicker('setDate', new Date(cur));
         $('#jrange div').datepicker('refresh').show();
      });
});



//DATEPICKER FOR SCHOOL REDINESS FORM

	natDays1 = [];
	var j = 0;
	$(".choiceDateRedinessSchool").each(function(){
		var chDate = $(this).attr('data-id');
		chDate = chDate.split("-");	
		var d = new Date(chDate[0],chDate[1]-1,chDate[2],0,0,0,0);	
		var date = d;
		natDays1[j]=[date.getMonth()+1,date.getDate(),date.getFullYear()];
		j++;	
	});	 

	function nationalDaysSchoolRediness(date) {
		for (i = 0; i < natDays1.length; i++) {
		  if (date.getMonth() == natDays1[i][0] - 1
			  && date.getDate() == natDays1[i][1]) {
			return [false, natDays1[i][2] + '_day'];
		  }
		}
	  return [true, ''];
	}
	function noWeekendsOrHolidaysSchoolRediness(date) {
		var noWeekend = $.datepicker.noWeekends(date);
		if (noWeekend[0]) {			
			return nationalDaysSchoolRediness(date);
			
		} else {			
			return noWeekend;
		}
	}  
	
	$("#datepicker2").datepicker({
		minDate: 1,	
		beforeShowDay: noWeekendsOrHolidaysSchoolRediness,
		onSelect: function(dateText, inst) {
			var date = $(this).datepicker("getDate");			
			var dayOfWeek = date.getDay();	
			
			if(dayOfWeek==1) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-monday-value"));				
				$("#totalPackage").val($("#totalPackageValue").attr("data-monday-value"));
			}
			else if(dayOfWeek==2) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-tuesday-value"));				
				$("#totalPackage").val($("#totalPackageValue").attr("data-tuesday-value"));
			}
			else if(dayOfWeek==3) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-wednesday-value"));
				$("#totalPackage").val($("#totalPackageValue").attr("data-wednesday-value"));
			}
			else if(dayOfWeek==4) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-thursday-value"));				
				$("#totalPackage").val($("#totalPackageValue").attr("data-thursday-value"));
			}
			else if(dayOfWeek==5) {
				$("#currentDayValue").val($("#totalPackageValue").attr("data-friday-value"));				
				$("#totalPackage").val($("#totalPackageValue").attr("data-friday-value"));
			}	
			
		}
	}); 

	$("form#school-rediness .chkChildren input[type='number']").on('input', function() {
		var sectionTotal=0;  
		$(this).parents('.chkChildren').find("input[type='number']").each(function(){	 			
			if( $(this).val() ) {  				
				sectionTotal = parseInt(sectionTotal)+parseInt($(this).val());
			}																											 		}); 
		var childTotal = $("input#totalPackage").val();
		if(sectionTotal>childTotal) {
			$(this).parents('.chkChildren').find(".error").html('<span>Note:  You have ordered more items than the total number of children scheduled to attend</span>');
		}
		else {
			$(this).parents('.chkChildren').find(".error").html('');
		}		
	}); 

});