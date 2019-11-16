// require script: control.js
// 1) hide more info in page
// 2) edit button toggles hide (edit form) on click
// 3) delete button deletes item on click
// 4) item (block) show subitems on click
// 5) edit-form update item on submit
// 6) add-new-form add new item on submit
// 7) checkbox checkmark on click
// 8) update items' order on sort(jQuery)

// 1) hide more info in page
addEvent(window, "load", hide_more_list);

// 2) edit button toggles hide (edit form) on click
var editButts = document.getElementsByClassName('edit-button');
for(var editButt of editButts){
	addEvent(editButt, "click", edit_toggle_hide);
}//End for

// 3) delete button deletes item on click
var deleteButtons = document.getElementsByClassName('delete-button');
for(var deleteButt of deleteButtons){
	addEvent(deleteButt,"click", delete_element);
}

// 4) item (block) show subitems on click(delegate on ansector <ul>)
var subitems = document.getElementsByClassName('subitems');
for(var subitem of subitems){
	addEvent(subitem,'click',function(e){show_subelements(e,'item')});
}

// 5) edit-form update item on submit
let editForms = document.getElementsByClassName('edit-form');
for(let editForm of editForms){
	let dataBeforeEdit = getFormParams(editForm);
	addEvent(editForm,'submit',function(e){update_element(e,'item',dataBeforeEdit);});
}

// 6) add-new-form add new item on submit
var new_item_forms = document.getElementsByClassName('add-new-item');
for(var new_item_form of new_item_forms){
	addEvent(new_item_form,"submit", function(e){add_new_element(e,'item');});
}

// 7) checkbox checkmark on click
var checkboxs = document.getElementsByTagName('button');
for(var checkbox of checkboxs){
	addEvent(checkbox,"click", checkmark);
}

// 8) update items' order on sort
$(document).ready(function(){
	setSortable($('.subitems'),'item',true);// set disable by default
	$('#subitems>.subitems').sortable('enable');// enable top list
});
