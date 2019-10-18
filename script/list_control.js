function find_ancestor_class(element, classname){
		let targetEle = element;
			do{
					targetEle = targetEle.parentNode;
			}while(!targetEle.className.match(classname));
		return targetEle;
}

function find_ancestor_tag(element, tagname){
		let targetEle = element;
			do{
					targetEle = targetEle.parentNode;
					var checkTagName = targetEle.tagName.toLowerCase();
			}while( checkTagName != tagname && checkTagName != 'body');
		return targetEle;
}

function find_1stDescendant_with(element, tagname, classname){
	let tagElements = element.getElementsByTagName(tagname);
	let i;
	for(i=0;i<tagElements.length;i++){
		if(tagElements[i].className.match(classname))
			break;
	}
	if(i<tagElements.length){
		return tagElements[i];
	}else{	
		return null;
	}
}

function getTriggerEle(e){
	if(!e){
			e = window.event;
		}
	element = e.target || e.srcElement;
	return	element;
}

function preventLinkAction(e){
		if(e.preventDefault){
			e.preventDefault();
		}else{
			e.returnValue = false;
		}
}

function stopBubbling(e){
		//stop bubbling
		if(e.stopPropogation){
			e.stopPropogation();
		}else{
			e.cancelBubble = true;
		}
}

// checkboxs check
function checkmark(e){
		preventLinkAction(e);
		let el = getTriggerEle(e);
		

			// figure out that it is going to check or uncheck
			var toCheck = el.getAttribute('value');
			if(toCheck === 'true'){
				toCheck = true;
			}else if(toCheck == 'false'){
				toCheck = false;
			}
			// set XHR
			var xhr = new XMLHttpRequest();
			xhr.onload = function(){
				var massage='';
				if(xhr.status === 200){
					var responseObject = JSON.parse(xhr.responseText);
					if(responseObject.success === true){
							// toggle checked class
								//find div[.item]
								var itemNode = find_ancestor_class(el, 'item');
							if(toCheck){
								// set button's value to false (in case user turn off the javascript after this response)
								el.setAttribute('value', 'false');
								// ada checkbox to class 'checked' 
								el.className += ' checked';
								// ada div[.item] to class 'checked'
								itemNode.className += ' checked';
							}else{
								// set button's value to true
								el.setAttribute('value', 'true');
								// remove checkbox from class 'checked'
								
								el.className = el.className.replace(' checked','');
								// remove div[.item] from class 'checked'
								itemNode.className = itemNode.className.replace(' checked','');
							}
					}else{
							massage = responseObject.massage;
					}
				}else{
					message = "Something go wrong with the service... Try again later";
				}
				// show massage
				var messageboard = document.getElementById('message-board');
				messageboard.textContent=massage;

			}
			// get api's url from form action
			var formNode = el.parentNode;

			let check_item_url = formNode.getAttribute('action');
			xhr.open('POST', check_item_url, true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send('ajax=true&checked='+toCheck);
			
			stopBubbling(e);
}

var checkboxs = document.getElementsByTagName('button');
for(var checkbox of checkboxs){
	checkbox.addEventListener("click", checkmark, false);
}


// delete item
function delete_item(e){
		preventLinkAction(e);
		let el = getTriggerEle(e);

			// set XHR
			var xhr = new XMLHttpRequest();
			xhr.onload = function(){
				var massage='';
				if(xhr.status === 200){
					var responseObject = JSON.parse(xhr.responseText);
					if(responseObject.success === true){
						var liNode = find_ancestor_tag(el, 'li')// find ancestor div[.item]
						liNode.parentNode.removeChild(liNode);//remove it
					}else{
							massage = responseObject.massage;
					}
				}else{
					massage = "Something go wrong with the service... Try again later";
				}
				// show massage
				var messageboard = document.getElementById('message-board');
				messageboard.textContent=massage;

			}
	
			let delete_item_url = el.getAttribute('href');
			xhr.open('POST', delete_item_url, true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send('ajax=true');
			
			stopBubbling(e);
}

var deleteButtons = document.getElementsByClassName('delete-button');
for(var deleteButt of deleteButtons){
	deleteButt.addEventListener("click", delete_item, false);
}


// add new item
// modify the code in MDN document about 'encodeURIComponent'
function encodeForPost(str) {
  return encodeURIComponent(str).replace('%20','+').replace(/[!'()*]/g, function(c) {
    return '%' + c.charCodeAt(0).toString(16);
  });
}

function add_new_item(e){
		preventLinkAction(e);
		let add_new_form = getTriggerEle(e);
		// get form elements
		var form_elements = add_new_form.elements; 
		item_title = form_elements.item_title.value;

			// set XHR
			var xhr = new XMLHttpRequest();
			xhr.onload = function(){
				var massage='';
				if(xhr.status === 200){
					// clear input
					form_elements.item_title.value = '';
					// the sublidst 'ul' is sibling of current form
					let formParent = add_new_form.parentNode;

					// add new item to sublist underneath
					let currentUl = formParent.getElementsByTagName('ul')[0];
					let newliNode = document.createElement('li');
					newliNode.innerHTML = xhr.responseText;
					currentUl.appendChild(newliNode);
					
					// set checkbox toggle checked
					let newCheckBox = newliNode.getElementsByTagName('button')[0];// only one button
					if(newCheckBox){
						newCheckBox.addEventListener("click", checkmark, false);
					}
					// set add-new-form add new on submit
					let newAddNew_form = find_1stDescendant_with(newliNode, 'form', 'add-new-item');
					newAddNew_form.addEventListener("submit", add_new_item, false);

					// edit toggle hide
					let newEditButt = find_1stDescendant_with(newliNode, 'div', 'edit-button');
					newEditButt.addEventListener("click", edit_toggle_hide, false); 

					// item show subitems
					let newItemDiv =  find_1stDescendant_with(newliNode, 'div', 'item');
					newItemDiv.addEventListener("click",show_subitems, false); 

					// set delete button delete on click
					let newDeleteButt = find_1stDescendant_with(newliNode, 'a', 'delete-button');
					newDeleteButt.addEventListener("click", delete_item, false);

					// hide edit-panel, add-new-form, edit-title
					let editTitle = find_1stDescendant_with(newliNode, 'input', 'edit-title'); 
					let editPanel = find_1stDescendant_with(newliNode, 'div', 'edit-panel');
					editTitle.className += ' hide';
					editPanel.className += ' hide';
					newAddNew_form.className += ' hide';

					// update edit-form on submit
					let newEditForm = find_1stDescendant_with(newliNode, 'form', 'edit-form');
					let newFormData = getFormParams(newEditForm);
					newEditForm.addEventListener('submit',function(e){update_item(e,newFormData);}, false);



				}else{ 
					massage = xhr.status+" Something go wrong with the service... Try again later";
				}
				// show massage
				var messageboard = document.getElementById('message-board');
				messageboard.textContent=massage;

			}
	
			let add_new_item_url = add_new_form.getAttribute('action');
			xhr.open('POST', add_new_item_url, true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			// encode to application/x-www-form-urlencoded format
			
			xhr.send('ajax=true&item_title='+encodeForPost(item_title));
			
			stopBubbling(e);
}

var new_item_forms = document.getElementsByClassName('add-new-item');
for(var new_item_form of new_item_forms){
	new_item_form.addEventListener("submit", add_new_item, false);
}

// edit button
function edit_toggle_hide(e){
	let el = getTriggerEle(e);
	let edit_form = el.parentNode.parentNode;
	// hide the edit-title and -link
	let title_link = edit_form.getElementsByClassName('title-link')[0];
	if(title_link){
		toggleHide(title_link);}
	let edit_title = edit_form.getElementsByClassName('edit-title')[0];
	if(edit_title){
		toggleHide(edit_title);}
	// hide the panel
	let edit_panel = edit_form.getElementsByClassName('edit-panel')[0];
	if(edit_panel){
	toggleHide(edit_panel);}

		stopBubbling(e);
}

var editButts = document.getElementsByClassName('edit-button');

for(var editButt of editButts){
		editButt.addEventListener("click", edit_toggle_hide,false);
}//End for


// item button shows subitems
function show_subitems(e){
	var el = getTriggerEle(e);
			// if it is title link, element in form, don't trigger
			if(el.className != 'title-link' && el.tagName.toLowerCase() != 'input' && el.tagName.toLowerCase() != 'textarea'){
				// find li node
				var liNode = find_ancestor_tag(el,'li') ;
				if(this.id != 'head'){
					var addNew = liNode.getElementsByClassName('add-new-item')[0];
					if(addNew){
						toggleHide(addNew);
					}
					var sublist = liNode.getElementsByTagName('ul')[0];
						if(sublist){
							toggleHide(sublist);
						}
					}
				}
			// stop bubbling
			stopBubbling(e);
}

var items = document.getElementsByClassName('item');
for(var item of items){
		item.addEventListener('click', show_subitems,false);
}

// update edit-form on submit
function update_item(e, dataBeforeEdit){
	preventLinkAction(e);
	let updateForm = getTriggerEle(e);
	let formData = updateForm.elements;
	// set xhr
	let xhr = new XMLHttpRequest();
	xhr.onload = function(){
		let message = '';
		let responseObj = JSON.parse(xhr.responseText);
		if(xhr.status === 200){
			if(responseObj.success){
					// update sanitized text
					formData['title'].value = responseObj.title;
					formData['note'].value = responseObj.note;
					// update title link and show it
					let title_link = updateForm.getElementsByTagName('a')[0];
					// head of list does't have a link
					if(title_link){
						title_link.textContent = responseObj.title;
						title_link.className =title_link.className.replace(' hide', '');
					}else{
						// head item trigger this, got set list-title
						document.getElementById('list-title').textContent = responseObj.title;
						// and the path end
						document.getElementById('nav-current-item').textContent = responseObj.title;
					}
					// hide edit-panel, add-new-form, edit-title
					let editTitle = find_1stDescendant_with(updateForm, 'input', 'edit-title'); 
					let editPanel = find_1stDescendant_with(updateForm, 'div', 'edit-panel');
					editTitle.className += ' hide';
					editPanel.className += ' hide';
			}else{
				message = responseObj.message;
				// restore data
				for(inputName in dataBeforeEdit){
					formData[inputName].value = dataBeforeEdit[inputName];
				}
			}
		}else{
			message = "Something go wrong with the service... Try again later";
			// restore data
			for(inputName in dataBeforeEdit){
				formData[inputName].value = dataBeforeEdit[inputName];
			}
		}
		// show massage
		var messageboard = document.getElementById('message-board');
		messageboard.textContent=message;
	};

			let update_item_url = updateForm.getAttribute('action');
			xhr.open('POST',update_item_url, true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			// encode to application/x-www-form-urlencoded format
			// get form data
			let dataString = ''
			for(let i=0; i<formData.length;i++){
				dataString += ('&'+formData[i].name +'='+ encodeForPost(formData[i].value));
			}
			xhr.send('ajax=true'+dataString);
			
			stopBubbling(e);
}
function getFormParams(formEle){
	let inputs=formEle.elements;
	let inputsData ={};
			for(let i=0; i<inputs.length;i++){
				inputsData[inputs[i].name]=inputs[i].value;
			}
	return inputsData;	
}

let editForms = document.getElementsByClassName('edit-form');
for(let editForm of editForms){
	let dataBeforeEdit = getFormParams(editForm);
	editForm.addEventListener('submit',function(e){update_item(e,dataBeforeEdit);}, false);
}
