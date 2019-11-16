// require script: control.js
// 1) hide more info in collection page on load
// 2) edit button toggles hide (edit form) on click
// 3) delete button deletes collections and lists on click
// 4) collection (block) show subcollections and lists on click
// 5) edit-form update item on submit
// 6) add-new-form add new item on submit
// 7) move collections' and lists' on sort

// 1) hide more info in collection page on load
addEvent(window,"load",hide_more_collection);

// 2) edit button toggles hide (edit form) on click
var editButts = document.getElementsByClassName('edit-button');
for(var editButt of editButts){
		addEvent(editButt,"click", edit_toggle_hide);
}

// 3) delete button deletes collections and lists on click
var deleteButtons = document.getElementsByClassName('delete-button');
for(var deleteButt of deleteButtons){
	addEvent(deleteButt,"click", delete_element);
}

// 4) collection (block) show subcollections and lists on click (delegate on ansector <ul>)
var subcollections = document.getElementsByClassName('subcollections');
for(var subcollection of subcollections){
		addEvent(subcollection, 'click',function(e){show_subelements(e,'collection')});
}

// 5) edit-form update item on submit
let update_collection_forms = document.getElementsByClassName('edit-form');
for(let update_form of update_collection_forms){
	let dataBeforeEdit = getFormParams(update_form);
	addEvent(update_form, 'submit', function(e){update_element(e,'collection', dataBeforeEdit);});
}

// 6) add-new-form add new item on submit
var new_collection_forms = document.getElementsByClassName('add-new-collection');
for(var new_collection_form of new_collection_forms){
	addEvent(new_collection_form, "submit", function(e){add_new_element(e,'collection');});
}

// 7) move collections' and lists' on sort
$(document).ready(function(){
	setSortable($('.subcollections'),'collection',true);// set disable by default
	$('#subEles>.subcollections').sortable('enable');// enable top list
	setSortable($('.lists'),'list',true);// set disable by default
	$('#subEles>.lists').sortable('enable');// enable top list
});
