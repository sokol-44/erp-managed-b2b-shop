function element_up(obj, type) {
	var prev_tr = $(obj).closest(type).prev(type + ":not(.tableBoxHeading)");
	if( prev_tr ) {
		prev_tr.before( $(obj).closest(type) );
	}
}

function element_down(obj, type) {
	var next_tr = $(obj).closest(type).next(type);
	if( next_tr ) {
		next_tr.after( $(obj).closest(type) );
	}
}

var pic_template = '';
var pic_onclick = '';

function add_picture_to_list(picture_name, list_name) {
	var pic_obj = $('#' + picture_name + ' option:selected');
	if( pic_obj.val() != '' && pic_template != '') {
		var pic = pic_template.replace('$id_picture', pic_obj.val());
		if( pic_onclick != '' ) {
			pic_oc = pic_onclick.replace('$id_picture', pic_obj.val());
			pic = pic.replace('>', ' onclick=\'' + pic_oc + '\'>');
		}
		var td_pic = '<td>' + pic + '</td>';
		var td_control = '<td>' + $('#tbl_picture_controls').html() + '</td>';
		var row = '<tr>' + td_pic + td_control + '</tr>\n';
		// $("#" + list_name + " tr:last").after(row);
		$("#" + list_name + " tbody").append(row);
	} else {
		
		alert('em' + pic_template);
	}
}

function select_picture(id_picture) {
	picObj = document.getElementById('id_picture');
	for(n=0; n<picObj.length; n++) {
		if( picObj.options[n].value == id_picture ) {
			picObj.options[n].selected = true;
		} else {
			picObj.options[n].selected = false;
		}
	}
}

function load_hotels_pictures(selObj) {
	var res = '';
	for (i=0; i<selObj.options.length; i++) {
		if (selObj.options[i].selected) {
		  res = selObj.options[i].value;
		}
	}
	
	picObj = document.getElementById('id_picture');
	var text_zero = picObj.options[0].text;
	picObj.options.length = 0;
	picObj.options[0] = new Option(text_zero,'');
	
	eval('var isSetter = (typeof all_pictures_by_hotel.id_hotel_' + res + ' != \'undefined\')');
	if( isSetter ) {
		eval('var pictures_hotel_array = all_pictures_by_hotel.id_hotel_' + res + ';');
		for(n=0; n<pictures_hotel_array.length; n++) {
			var obj_tmp = pictures_hotel_array[n];
			picObj.options[n+1] = new Option(obj_tmp.name, obj_tmp.id_picture);
			$('#id_picture').attr('disabled', false);
		}
	} else {
		$('#id_picture').attr('disabled', true);
	}
}

function set_special_attrib_parameters(selObj) {
	for (i=0; i<selObj.options.length; i++) {
		if (selObj.options[i].selected) {
		  res = selObj.options[i].value;
		}
	}
	
	if( res == 'SELECT') $('#type_options_serialize').attr('disabled', false);
	else $('#type_options_serialize').attr('disabled', true);
	
}