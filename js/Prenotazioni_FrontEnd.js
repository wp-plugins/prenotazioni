jQuery(document).ready(
function($){
 	$(document).click(function(e) { //start function when Random button is clicked
		if(e.target.className=="adminpreStyle"){
			var OraI=parseInt(e.target.id, 10);
			var Maxore=0;
			var IDNext="";
			var OI=$("#OraInizio").attr('value');
			var OF=$("#OraFine").attr('value');
			var NMO=$("#NumMaxOre").attr('value');
			var ColSel=$("#ColPrenotato").attr('value');
			var oldsel="#"+$("#OldSel").attr('value');
			$(oldsel).attr("style","background-color:#FFFFFF;");
			e.target.style="background-color:"+ColSel;
			$("#OldSel").attr('value',e.target.id);
			$("#OraInizioPrenotazione").attr('value',OraI);
			$("#VisOraInizio").text(OraI);
			$("#NumOrePren").empty();
			do{
				Maxore++;
				IDNext="#"+(OraI+Maxore);
				$("#NumOrePren").append($('<option value="'+Maxore+'">'+Maxore+'</option>'));
			}while ($(IDNext).attr('class')=="adminpreStyle" && Maxore<NMO);		
/*			alert("Inizio "+OraI+" Max ore pren "+Maxore);
			alert($("#OraInizioPrenotazione").attr("value"));*/
		}
	});	
    $('#CartellePrenotazioni').tabs();
    $( "#SpazioP" ).change(function() {
 		 $( "#SpazioP option:selected" ).each(function() {
			$("#imgSpazio").attr('src',$( this ).attr('title'));
				$.ajax({type: "post",url: $("#UrlAjax").attr("value"),data: { action: 'FEprenSpazi', 
																		      data: $('#DataPrenotazione').attr("value"), 
																	        spazio: $( "#SpazioP" ).attr("value"),
																	          sorg: "FE"}, 
							beforeSend: function() {
												$("#loading").fadeIn('fast');
										}, 
							success: function(html){
												$("#InizioPre").html(html);
												$("#NumOrePren").empty();
												$("#NumOrePren").append($('<option value="0">----</option>'));
												$("#loading").fadeOut('fast');
										},
							error: function() {
          									alert('Error occurs! action: FEprenSpazi  data: '+selectedDate+' spazio: '+ $( "#SpazioP" ).attr("value")+' sorg: "FE"');
          									$("#loading").fadeOut('fast');
          									}
					});
		});
	});
	
	$.datepicker.setDefaults( $.datepicker.regional[ "it" ] );
	$('#DataPrenotazione').datepicker({
      	dateFormat: "dd/mm/yy",
      	onClose: function(selectedDate) {
        			if(selectedDate!=""){
        				//var Spazio=${"#SpazioP"}.attr('value');
 						$.ajax({type: "post",url: $("#UrlAjax").attr("value") ,data: { action: 'FEprenSpazi', 
																			          data: selectedDate, 
																		            spazio: $( "#SpazioP" ).attr("value"),
																		              sorg: "FE"}, 
								beforeSend: function() {
													$("#loading").fadeIn('fast');
											}, 
								success: function(html){
													$("#NumOrePren").empty();
													$("#NumOrePren").append($('<option value="0">----</option>'));
													$("#InizioPre").html(html);
													$("#loading").fadeOut('fast');
											},
								error: function(xhrRequest, status, errorMessage)  {
                       alert("Errore dal server. Status: " + status);
                    }
						});
					}
         }
    });
});