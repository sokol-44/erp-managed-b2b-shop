
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

function form_submit(form_name) {
	// var form = $("form[name='" + form_name + "']");
	// form.submit();
	// $("form[name='" + form_name + "'] :submit").trigger('click');
	// alert($("form[name='" + form_name + "']").attr('name'));
	// alert($("form[name='" + form_name + "'] input:submit").attr('name'));
	// alert($("input[name='#SAVE']:submit").attr('name'));
	$("form#" + form_name + " input:submit").trigger('click');
	// $("input:submit").trigger('click');
}


function go_to_row_remove() {
	var found = false;
	var str = '';
	$(toolbox_tablename + " tr:nth-child(n+2)").each( function() {
		if( $(this).find(":radio").attr('checked') ) {
			$(this).find("a[title=" + TEXT_LINK_TITLE_REMOVE + "]").each( function() {
				var answer = confirm(TEXT_ARE_YOU_SURE_REMOVE);
				// if( answer )  location.href = $(this).attr('href');
				//alert( $(this).attr('href') );
				found = true;
			});
		}
	});	
	if( !found ) {
		alert(TEXT_NO_SELECTED_ROW);
	}
}

function go_to_row_folders() {
	var found = false;
	$(toolbox_tablename + " tr:nth-child(n+2)").each( function() {
		if( $(this).find(":radio").attr('checked') ) {
			$(this).find("a[title=" + TEXT_LINK_TITLE_FOLDERS + "]").each( function() {
				//location.href = $(this).attr('href');
				// alert( $(this).attr('href') );
				found = true;
			});
		}
	});
	
	if( !found ) {
		alert(TEXT_NO_SELECTED_ROW);
	}
}

function go_to_row_edit() {
	var found = false;
	$(toolbox_tablename + " tr:nth-child(n+2)").each( function() {
		if( $(this).find(":radio").attr('checked') ) {
			$(this).find("a[title=" + TEXT_LINK_TITLE_EDIT + "]").each( function() {
				location.href = $(this).attr('href');
				//alert( $(this).attr('href') );
				found = true;
			});
		}
	});
	
	if( !found ) {
		alert(TEXT_NO_SELECTED_ROW);
	}
}
