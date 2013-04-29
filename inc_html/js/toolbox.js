
var toolbox_tablename = '';
function set_toolbox_table(table_ident) {
	toolbox_tablename = table_ident;
	setTimeout('clear_password()',10);
}

function clear_password() {
	$(toolbox_tablename + " input:password").each( function() {
		$(this).val('');
	});
}


function go_to_href(url) {
	location.href = url;
}

function change_form_target(target) }



}


