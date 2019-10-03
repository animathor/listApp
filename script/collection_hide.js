/*
toggleHide from hide.js
*/




// hide onload
if(window.addEventListener){
	window.addEventListener("load", function(){
	console.log('load');
	// hide edit-titles
	var hideTitles = document.getElementsByClassName('edit-title');
	for(var title of hideTitles){
		toggleHide(title);	
	}	
	// hide sublists
	var items = document.getElementsByTagName('li');
	for(var item of items){
		var addnew = item.getElementsByClassName('add-new-collection')[0];
		var sublist = item.getElementsByTagName('ul')[0];
		if(addnew){
		toggleHide(addnew);}
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
			// edit title and link
			var title_link = edit_form.getElementsByClassName('title-link')[0];
			if(title_link){
				toggleHide(title_link);}
			var edit_title = edit_form.getElementsByClassName('edit-title')[0];
			if(edit_title){
				toggleHide(edit_title);}

			// stop bubbling
			if(e.stopPropogation){
				e.stopPropogation();
			}else{
				e.cancelBubble = true;
			}
			
		},false);
	}else{
		document.attachEvent("onclick", function(e){

		});
	}//End else
}//End for

function hasAncestorInClass(elementNode, classNameWant){
	// get parent node
	var wantedEle = elementNode;
	// fetch the class name
	do{
					var parent = wantedEle.parentNode;
					wantedEle = parent;
					className ='';
					if(wantedEle != null){
					 className += wantedEle.className;}
	}while(wantedEle != null && !className.match(classNameWant));
	if(wantedEle != null){
		return true;
	}else{
		return false;
	}
}

// item button shows sublist
var collections = document.getElementsByClassName('collection');
for(var collection of collections){
	if(collection.addEventListener){
		collection.addEventListener('click', function(e){
			if(!e){
				e = window.event;
			}
			var el = e.target || e.srcElement;
			// if it is title link, don't trigger
			if(el.className != 'title-link' && !hasAncestorInClass(el, 'collection-control') && el.tagName.toLowerCase() != 'input' && el.tagName.toLowerCase() != 'textarea'){
				// find li node
				var liNode = el;
				do{
					var parent = liNode.parentNode;
					liNode = parent;
					tagName = liNode.tagName.toLowerCase();
				}while(tagName != 'li');
				var addNew = liNode.getElementsByClassName('add-new-collection')[0];
					if(addNew){
					toggleHide(addNew);}
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
