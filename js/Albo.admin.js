jQuery.noConflict();
(function($) {
	$(function() {
		$('a.ripubblica').click(function(){
			var answer = confirm("Confermi la ripubblicazione dei " + $(this).attr('rel') + ' atti in corso di validita?')
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});
		
		$('a.annullaattox').click(function(){
			var answer = confirm("Confermi l'annullamento dell'atto `" + $(this).attr('rel') + '` ?\nATTENZIONE L\'OPERAZIONE E\' IRREVERSIBILE!!!!!')
			if (answer){
				var Testoannullamento;
				Testoannullamento=prompt("Motivo Annullamento Atto "+ $(this).attr('rel'),"Atto Annullato per");				
				location.href=$(this).attr('href')+"&motivo="+Testoannullamento;
				return false;
			}
			else{
				return false;
			}					
		});
		$('a.eliminaatto').click(function(){
			var answer = confirm("Confermi l'eliminazione dell'atto `" + $(this).attr('rel') + '` ?\nATTENZIONE L\'OPERAZIONE E\' IRREVERSIBILE!!!!!')
			if (answer){
				var answer = confirm("Prima di procedere ti ricordo che l'ELIMINAZIONE degli atti dall'Albo sono regolati dalla normativa\nTranne che in casi particolari gli atti devono rimanere nell'Albo Storico almeno CINQUE ANNI")
				if (answer){
					location.href=$(this).attr('href')+"&sgs=ok";
					return false;
				}else{
					return false;
				}
			}else{
				return false;
			}					
		});
		$('a.dc').click(function(){
			var answer = confirm("Confermi la cancellazione della Categoria `" + $(this).attr('rel') + '` ?')
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});

		$('a.dr').click(function(){
			var answer = confirm("Confermi la cancellazione del Responsabile del Trattamento `" + $(this).attr('rel') + '` ?')
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});

		$('a.da').click(function(){
			var answer = confirm("Confermi la cancellazione del\'Allegato `" + $(this).attr('rel') + '` ?\n\nATTENZIONE questa operazione cancellera\' anche il file sul server!\n\nSei sicuro di voler CANCELLARE l\'allegato?')
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});
		$('a.ac').click(function(){
			var answer = confirm("Confermi la cancellazione dell' Atto: `" + $(this).attr('rel') + '` ?')
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});
		$('a.ap').click(function(){
			var answer = confirm("approvazione Atto: `" + $(this).attr('rel') + '`\nAttenzione la Data Pubblicazione verra` impostata ad oggi ?')
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});
		$('input.update').click(function(){
			var answer = confirm("confermi la modifica della Categoria " + $(this).attr('rel') + '?')
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});
		$('a.addstatdw').click(function() {
		 var link=$(this).attr('rel');
		 $.get(link,function(data){
		$('#DatiLog').html(data);
			}, "json");
		});
		var l = window.location;
		var url = l.protocol + "//" + l.host +"/"+  l.pathname.split('/')[1]+"/wp-content/plugins/albo-pretorio-on-line/swf/copy_csv_xls_pdf.swf";
		
		$('#elenco-atti').dataTable( {
          dom: 'T<"clear">lfrtip',
          order: [],
          tableTools: {
         	"sSwfPath": url,
          	"aButtons": [ 
 				{
                    "sExtends":    "collection",
                    "sButtonText": "Salva",
                    "aButtons":    [ "csv",]
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
    	$('#elenco-atti-daapp').dataTable( {
          dom: 'T<"clear">lfrtip',
          order: [],
          tableTools: {
         	"sSwfPath": url,
          	"aButtons": [ 
 				{
                    "sExtends":    "collection",
                    "sButtonText": "Salva",
                    "aButtons":    [ "csv",]
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

     	$('#elenco-atti-correnti').dataTable( {
          dom: 'T<"clear">lfrtip',
          order: [],
          tableTools: {
         	"sSwfPath": url,
          	"aButtons": [ 
 				{
                    "sExtends":    "collection",
                    "sButtonText": "Salva",
                    "aButtons":    [ "csv",]
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

     	$('#elenco-atti-scaduti').dataTable( {
          dom: 'T<"clear">lfrtip',
          order: [],
          tableTools: {
         	"sSwfPath": url,
          	"aButtons": [ 
 				{
                    "sExtends":    "collection",
                    "sButtonText": "Salva",
                    "aButtons":    [ "csv",]
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

      $('#elenco-atti-da-cancellare').dataTable( {
          dom: 'T<"clear">lfrtip',
          order: [],
          tableTools: {
         	"sSwfPath": url,
          	"aButtons": [ 
 				{
                    "sExtends":    "collection",
                    "sButtonText": "Salva",
                    "aButtons":    [ "csv",]
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
    var Pagina=$("#Pagina").val();
    $('#atti-tabs-container').tabs({ active: Pagina });
    $('#utility-tabs-container').tabs();	
 });
})(jQuery);