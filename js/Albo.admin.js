jQuery.noConflict();
(function($) {
	$(function() {
            $("#Prova").click(function() {

  var post = new wp.api.models.Post( { title: 'This is a test post' } );
post.save();
} );
		$( "#errori" ).dialog({
	      autoOpen: false,
	      show: {
	        effect: "blind",
	        duration: 1000
	      },
	      hide: {
	        effect: "explode",
	        duration: 1000
	      }
	    });
		$( "#ConfermaCancellazione" ).dialog({
	      autoOpen: false,
	      show: {
	        effect: "blind",
	        duration: 1000
	      },
	      hide: {
	        effect: "explode",
	        duration: 1000
	      },
	      modal: true,
	      buttons: {
	        "Conferma": function() {
	          $( this ).dialog( "close" );
	          location.href=$("#UrlDest").val();
	          return true;
	        },
	        "Annula": function() {
	          $( this ).dialog( "close" );
	          return true;
	        }
	    	}		      
	    });
		$('#MemorizzaDati').click(function(){	
		var myList = document.getElementsByClassName("richiesto");
		var ListaErrori='';
		for(var i=0;i<myList.length;i++){
			console.log(myList[i].value + ' - '+myList[i].name+ ' - '+myList[i].tagName+ ' - '+myList[i].classList+"|");
			var Classi = myList[i].classList;
			var Condizione=true;
			for(var ic=0;ic<Classi.length;ic++){
				if(Classi[ic].slice(0, 8)=='ValValue'){
					console.log(Classi[ic].slice(0, 8));
					Condizione=eval(myList[i].value+Classi[ic].slice(9, Classi[ic].length-1));
				}				
			}
			if (!myList[i].value || !Condizione)
				ListaErrori+=myList[i].name+' '+myList[i].value+'<br />';	
		}
		if(ListaErrori){
//			alert('Lista Campi con Errori:\n'+ListaErrori+'Correggere gli errori per continuare');
			document.getElementById("ElencoCampiConErrori").innerHTML=ListaErrori;		
			$( "#errori" ).dialog( "open" );
			return false;
		}else
			return true;
		});
		$('a.ac').click(function(){
//			var answer = confirm("Confermi la cancellazione dell' Atto: `" + $(this).attr('rel') + '` ?')
//			if (answer){
//				return true;
//			}
//			else{
				document.getElementById("oggetto").innerHTML=$(this).attr('rel');
				$("#UrlDest").val($(this).attr('href'));
//				alert($("#UrlDest").val());
				$("#ConfermaCancellazione").dialog( "open" );
				return false;
//			}					
		});		
		$('a.ripubblica').click(function(){
			var answer = confirm("Confermi la ripubblicazione dei " + $(this).attr('rel') + ' atti in corso di validita?')
			if (answer){
				return true;
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
    var Pagina=$("#Pagina").val();
    $('#utility-tabs-container').tabs({ active: Pagina });
    $('#edit-atti-tabs').tabs();
    $('#repertori-tabs-container').tabs();
    $('#utility-tabs-container').tabs();	
    $('#config-tabs-container').tabs();	
 });
})(jQuery);


function Stampa(Testo){
     // Prelevo dalla pagina solo i blocchi che interessano
     // Ad esempio il titolo e il corpo di un articolo

     // Apro una finestra pop-up nella quale inserisco i blocchi
     var a = window.open('','','width=640,height=480');
     a.document.open("text/html");
     a.document.write("<html><head></head><body>");

     // Scrivo il titolo e il corpo con un pò di stile in CSS
     a.document.write("<div style='border: 1px solid #CCCCCC'>"+titolo+"</div><br/>"+corpo);
     a.document.write("</body></html>");
     a.document.close();

     // Invio il documento alla stampante
     a.print(); 
}
