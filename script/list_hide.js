/*
toggleHide from hide.js
*/

// hide onload
if(window.addEventListener){
	window.addEventListener("load", function(){
	console.log('load');
	// hide head edit form
	var headEdit = document.getElementById('head-edit');
	if(headEdit){
		headEdit.className += ' hide';}
	// hide edit-titles
	var hideTitles = document.getElementsByClassName('edit-title');
	for(var title of hideTitles){
		title.className += ' hide';	
	}	
	// hide edit-panels
	var hidePanels = document.getElementsByClassName('edit-panel');
	console.log(hidePanels);
		for(var panel of hidePanels){
			panel.className += ' hide';
		}
	// hide sublists
	var items = document.getElementsByTagName('li');
	for(var item of items){
		var addnew = item.getElementsByClassName('add-new-item')[0];
		var sublist = item.getElementsByTagName('ul')[0];
		if(addnew){
			addnew.className += ' hide';
		}
		if(sublist){
			sublist.className += ' hide';}
	}
	},false);
}else{
	document.attachEvent('onload', function(e){
		var hideElms = getElementsByClassName('edit-panel');	
		for(var elm of hideElms){
			toggleHide(elm);
		}
	});
}
