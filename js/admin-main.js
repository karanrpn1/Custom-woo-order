jQuery(document).ready(function($){
	$(".alergyViewMore").click(function(e){
		e.preventDefault();
		var id = $(this).attr('data-id');
		console.log(id);
		$("#"+id).slideToggle(); 
	});		
	
});
