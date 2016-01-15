jQuery(document).ready(function($){
    $('#pp-tabs-container').tabs();
});
jQuery(document).ready(function($){
    $('#fe-tabs-container').tabs();
});

jQuery(document).ready(function($){
    $('#maxminfiltro').on('click', function(event) {
		var pos=$('#maxminfiltro').attr("src");
		pos=pos.substr(0,pos.lastIndexOf("/")+1); 
    	if($('#maxminfiltro').attr('class')=="s"){
			$('#fe-tabs-container').hide();
			$('#maxminfiltro').attr('class',"h");
			$('#maxminfiltro').attr("src",pos+"maximize.png");
		}else{
			$('#fe-tabs-container').show();
			$('#maxminfiltro').attr('class',"s");		
			$('#maxminfiltro').attr("src",pos+"minimize.png");	
		}
	});
});
jQuery(document).ready(function($) {
		$('a.numero-pagina').click(function(){
			location.href=$(this).attr('href')+"&vf="+$('#maxminfiltro').attr('class')+"#dati";
			return false;	
		});
});