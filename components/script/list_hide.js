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
		toggleHide(headEdit);}
	// hide edit-titles
	var hideTitles = document.getElementsByClassName('edit-title');
	for(var title of hideTitles){
		toggleHide(title);	
	}	
	// hide edit-panels
	var hidePanels = document.getElementsByClassName('edit-panel');
	console.log(hidePanels);
		for(var panel of hidePanels){
			toggleHide(panel);
		}
	// hide sublists
	var items = document.getElementsByTagName('li');
	for(var item of items){
		var addnew = item.getElementsByClassName('add-new-item')[0];
		var sublist = item.getElementsByTagName('ul')[0];
			toggleHide(addnew);
		if(sublist){
			toggleHide(sublist);}
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

// edit button
var editButts = document.getElementsByClassName('edit-button');

for(var editButt of editButts){
	if(editButt.addEventListener){
		editButt.addEventListener("click", function(e){
			if(!e){
				e = window.event;
			}
		
			var el = e.target || e.srcElement;
			var edit_form = el.parentNode.parentNode;
			console.log(edit_form);
			// head edit form
			if(el.id === 'head-edit-button'){
				var headEdit = document.getElementById('head-edit');
				if(headEdit){
					toggleHide(headEdit);}
			}
			// edit title and link
			var title_link = edit_form.getElementsByClassName('title-link')[0];
			if(title_link){
				toggleHide(title_link);}
			var edit_title = edit_form.getElementsByClassName('edit-title')[0];
			if(edit_title){
				toggleHide(edit_title);}
			// panel
			var edit_panel = edit_form.getElementsByClassName('edit-panel')[0];
			if(edit_panel){
			toggleHide(edit_panel);}
			// stop bubbling
			if(e.stopPropogation){
				e.stopPropogation();
			}else{
				e.cancelBubble = true;
			}
			
		},false);
	}else{
		document.attachEvent("onclick", function(e){
			if(!e){
				e = window.event;
			}
			// stop bubbling
			if(e.stopPropogation){
				e.stopPropogation();
			}else{
				e.cancelBubble = true;
			}
			
			var el = e.target || e.srcElement;
			var edit_form = el.parentNode.parentNode;
			console.log(edit_form);
			// edit title and link
			var title_link = edit_form.getElementsByClassName('title_link')[0];
			console.log(title_link);
			var edit_title = edit_form.getElementsByClassName('edit_title')[0];
			console.log(edit_title);
			toggleHide(title_link);
			toggleHide(edit_title);
			// panel
			var edit_panel = edit_form.getElementsByClassName('edit-panel')[0];
			console.log(edit_panel);
			toggleHide(edit_panel);
		});
	}//End else
}//End for

// item button shows sublist
var items = document.getElementsByClassName('item');
for(var item of items){
	if(item.addEventListener){
		item.addEventListener('click', function(e){
			if(!e){
				e = window.event;
			}
			var el = e.target || e.srcElement;
			// if it is title link, don't trigger
			if(el.className != 'title-link'){
				// find li node
				var liNode = el;
				do{
					var parent = liNode.parentNode;
					liNode = parent;
					tagName = liNode.tagName.toLowerCase();
				}while(tagName != 'li');
				var addNew = liNode.getElementsByClassName('add-new-item')[0];
					toggleHide(addNew);
				var sublist = liNode.getElementsByTagName('ul')[0];
					if(sublist){
						toggleHide(sublist);
					}
				}
			// stop bubbling
			if(e.stopPropogation){
				e.stopPropogation();
			}else{
				e.cancelBubble = true;
			}
		},false);
	}else{
		document.attachEvent("onclick", function(e){
			if(!e){
				e = window.event;
			}
			var el = e.target || e.srcElement;
			// find li node
			var liNode = el;
			do{
				var parent = liNode.parentNode;
				liNode = parent;
				tagName = liNode.tagName.toLowerCase();
			}while(tagName != 'li');
			var addNew = liNode.getElementsByClassName('add-new-item')[0];
				toggleHide(addNew);
			var sublist = liNode.getElementsByTagName('ul')[0];
				if(sublist){
					toggleHide(sublist);
				}
			// stop bubbling
			if(e.stopPropogation){
				e.stopPropogation();
			}else{
				e.cancelBubble = true;
			}
		});
	}
}
