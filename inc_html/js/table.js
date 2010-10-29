

function colorize_table(table_ident) {
	$(table_ident + " tr:nth-child(2n+2)").css("background-color", "#e8e8e9");
	
	$(table_ident + " tr:nth-child(n+2)").mouseover( function() {
//		if( $(this).attr("old-color") == '' ) {
//			$(this).attr("old-color", $(this).css("background-color"));
//			$(this).css("background-color", "#585859");
//		}
		$(this).find('td').css("border", "1px dotted black");
	});
		
	$(table_ident + " tr:nth-child(n+2)").mouseout( function() {
//		if( $(this).attr("old-color") != '' ) {
//			$(this).css("background-color", $(this).attr("old-color"));
//			$(this).attr("old-color", '');
//		}
		$(this).find('td').css("border", "1px solid white");
	});
	
	$(table_ident + " tr:nth-child(n+2)") .mousedown( function() {
		var res = $(this).find(":radio").attr('checked', true);
	});
	
}