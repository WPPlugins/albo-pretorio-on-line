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
				
		$('#elenco-atti').dataTable( {
          dom: 'T<"clear">lfrtip',
          order: [[ColOrder, OrdOrder ]],
          tableTools: {
         	"sSwfPath": url,
          	"aButtons": [ 
          		{
					"sExtends": "copy",
					"sButtonText": "Copia negli Appunti"
				},
          		{
					"sExtends": "print",
					"sButtonText": "Stampa"
				},
				{
                    "sExtends":    "collection",
                    "sButtonText": "Salva",
                    "aButtons":    [ "csv", "xls",                 
                    {
                    	"sExtends": "pdf",
                    	"sPdfOrientation": "landscape",
                    	"sPdfMessage": "Tabella generata con il plugin Gestione Circolari."
                	},]
                }
			]
         },
        language:{
		    "sEmptyTable":     "Nessun dato presente nella tabella",
		    "sInfo":           "Vista da _START_ a _END_ di _TOTAL_ elementi",
		    "sInfoEmpty":      "Vista da 0 a 0 di 0 elementi",
		    "sInfoFiltered":   "(filtrati da _MAX_ elementi totali)",
		    "sInfoPostFix":    "",
		    "sInfoThousands":  ",",
		    "sLengthMenu":     "Visualizza _MENU_ elementi",
		    "sLoadingRecords": "Caricamento...",
		    "sProcessing":     "Elaborazione...",
		    "sSearch":         "Cerca:",
		    "sZeroRecords":    "La ricerca non ha portato alcun risultato.",
		    "oPaginate": {
		        "sFirst":      "Inizio",
		        "sPrevious":   "Precedente",
		        "sNext":       "Successivo",
		        "sLast":       "Fine"
		    },
		    "oAria": {
		        "sSortAscending":  ": attiva per ordinare la colonna in ordine crescente",
		        "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
		    }
		}
    } );
	});
})(jQuery);