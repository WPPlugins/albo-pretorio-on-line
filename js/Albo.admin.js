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
		
		$('a.ripubblica').click(function(){
			var answer = confirm("Confermi la ripubblicazione dei " + $(this).attr('rel') + ' atti in corso di validita?')
			if (answer){
				return true;
			}
			else{
				return false;
			}					
		});
		$('a.annullaatto').click(function(){
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
 });
})(jQuery);