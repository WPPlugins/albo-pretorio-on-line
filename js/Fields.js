jQuery(document).ready(function($){
    $('#color').wpColorPicker();
	$('#colorp').wpColorPicker();
    $('#colord').wpColorPicker();
	$( "#Calendario1" ).datepicker({
        dateFormat : 'dd/mm/yy'
    });
	$( "#Calendario2" ).datepicker({
        dateFormat : 'dd/mm/yy'
    });
	$( "#Calendario3" ).datepicker({
        dateFormat : 'dd/mm/yy'
    });
	$( "#Calendario4" ).datepicker({
        dateFormat : 'dd/mm/yy'
    });
    $("#setta-def-data-o").click(function() {
 	 $( "#Calendario4" ).datepicker( "setDate", $(this).attr('name') );
 	});
});