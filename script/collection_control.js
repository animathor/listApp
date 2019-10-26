// require script: control.js
// 1) hide more info in collection page on load
// 2) edit button toggles hide (edit form) on click
// 3) delete button deletes collections and lists on click
// 4) collection (block) show subcollections and lists on click
// 5) edit-form update item on submit
// 6) add-new-form add new item on submit

// 1) hide more info in collection page on load
window.addEventListener("load",hide_more_collection, false);
// 2) edit button toggles hide (edit form) on click
var editButts = document.getElementsByClassName('edit-button');
for(var editButt of editButts){
		editButt.addEventListener("click", edit_toggle_hide ,false);
}//End for

// 3) delete button deletes collections and lists on click
var deleteButtons = document.getElementsByClassName('delete-button');
for(var deleteButt of deleteButtons){
	deleteButt.addEventListener("click", delete_element, false);
}//End for

// 4) collection (block) show subcollections and lists on click
var collections = document.getElementsByClassName('collection');
for(var collection of collections){
		collection.addEventListener('click',function(e){show_subelements(e,'collection')} ,false);
}//End for

// 5) edit-form update item on submit
let update_collection_forms = document.getElementsByClassName('edit-form');
for(let update_form of update_collection_forms){
	let dataBeforeEdit = getFormParams(update_form);
	update_form.addEventListener('submit', function(e){update_element(e,'collection', dataBeforeEdit);}, false);
}
// 6) add-new-form add new item on submit
var new_collection_forms = document.getElementsByClassName('add-new-collection');
for(var new_collection_form of new_collection_forms){
	new_collection_form.addEventListener("submit", function(e){add_new_element(e,'collection');}, false);
}
