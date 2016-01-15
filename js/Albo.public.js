function format ( d ) {
	return '<table cellpadding="1" cellspacing="0" border="0" style="padding-left:5px;font-size:0.9em;font-weight: bold;">'+
		'<tr>'+
			'<td style="width:5%;text-align: right;">Ente:</td>'+
			'<td>'+d[2]+'</td>'+
		'</tr>'+
		'<tr>'+
			'<td style="text-align: right;">Riferimento:</td>'+
			'<td>'+d[3]+'</td>'+
		'</tr>'+
		'<tr>'+
			'<td style="text-align: right;">Categoria:</td>'+
			'<td>'+d[6]+'</td>'+
		'</tr>'+
	'</table>';
}
jQuery.noConflict();
(function($) {
	$(function() {		 
		$('#paginazione').change(function(){
				location.href=$(this).attr('rel')+$("#paginazione option:selected" ).text();
		});
		$('#Calendario1').datepicker();
		$('#Calendario2').datepicker();
		$('a.addstatdw').click(function() {
			 var link=$(this).attr('rel');
				jQuery.ajax({type: 'get',url: $(this).attr('rel')}); //close jQuery.ajax
			return true;		 
			});
		$('#elenco-atti tbody').on('click', 'td.details-control', function () {
			var tr = $(this).closest('tr');
			var row = table.row( tr );
	//alert(row.data());
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown');
			}
			else {
				// Open this row
				row.child( format(row.data()) ).show();
				tr.addClass('shown');
			}
		} );
		var l = window.location;
		var url = l.protocol + "//" + l.host +"/"+  l.pathname.split('/')[1]+"/wp-content/plugins/albo-pretorio-on-line/swf/copy_csv_xls_pdf.swf";
		var table = $('#elenco-atti').DataTable( {
	        "dom": 'lT<f>Crtip',
			"colVis": {"label": function ( index, title, th ) {return (index+1) +'. '+ title;}},
		   	"language":{
			    "sEmptyTable":     "Nessun dato presente nella tabella",
			    "sInfo":           " _START_-_END_ di _TOTAL_ atti",
			    "sInfoEmpty":      "Vista da 0 a 0 di 0 atti",
			    "sInfoFiltered":   "(filtrati da _MAX_ atti totali)",
			    "sInfoPostFix":    "",
			    "sInfoThousands":  ",",
			    "sLengthMenu":     "Visualizza _MENU_ atti",
			    "sLoadingRecords": "Caricamento...",
			    "sProcessing":     "Elaborazione...",
			    "sSearch":         "Cerca:",
			    "sZeroRecords":    "La ricerca non ha portato alcun risultato.",
			    "oPaginate": {
			        "sFirst":      "<<",
			        "sPrevious":   "<",
			        "sNext":       ">",
			        "sLast":       ">>"
			    },
			},
			"tableTools": {
	         	"sSwfPath": url,
	          	"aButtons": [ 
					{
	                    "sExtends":    "collection",
	                    "sButtonText": "Salva",
	                    "aButtons":    [ "csv", "xls"]
	                }
				]
	        }
		});
	});
})(jQuery);