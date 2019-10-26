// require script: control.js
// 1) hide more info in page
// 2) edit button toggles hide (edit form) on click
// 3) delete button deletes item on click
// 4) item (block) show subitems on click
// 5) edit-form update item on submit
// 6) add-new-form add new item on submit
// 7) checkbox checkmark on click


// 1) hide more info in page
window.addEventListener("load", hide_more_list, false);

// 2) edit button toggles hide (edit form) on click
var editButts = document.getElementsByClassName('edit-button');
for(var editButt of editButts){
		editButt.addEventListener("click", edit_toggle_hide,false);
}//End for

// 3) delete button deletes item on click
var deleteButtons = document.getElementsByClassName('delete-button');
for(var deleteButt of deleteButtons){
	deleteButt.addEventListener("click", delete_element, false);
}

// 4) item (block) show subitems on click
var items = document.getElementsByClassName('item');
for(var item of items){
		item.addEventListener('click',function(e){show_subelements(e,'item')},false);
}

// 5) edit-form update item on submit
let editForms = document.getElementsByClassName('edit-form');
for(let editForm of editForms){
	let dataBeforeEdit = getFormParams(editForm);
	editForm.addEventListener('submit',function(e){update_element(e,'item',dataBeforeEdit);}, false);
}

// 6) add-new-form add new item on submit
var new_item_forms = document.getElementsByClassName('add-new-item');
for(var new_item_form of new_item_forms){
	new_item_form.addEventListener("submit", function(e){add_new_element(e,'item');}, false);
}

// 7) checkbox checkmark on click
var checkboxs = document.getElementsByTagName('button');
for(var checkbox of checkboxs){
	checkbox.addEventListener("click", checkmark, false);
}
