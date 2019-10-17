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
			console.log(targetEle);
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

function getTriggerEle(event){
	if(!event){
			event = window.event;
		}
	element = event.target || event.srcElement;
	return	element;
}

function preventLinkAction(event){
		if(event.preventDefault){
			event.preventDefault();
		}else{
			event.returnValue = false;
		}
}

function stopBubbling(event){
		//stop bubbling
		if(event.stopPropogation){
			event.stopPropogation();
		}else{
			event.cancelBubble = true;
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
			console.log("toCheck ="+toCheck);
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
					massage = "Something go wrong with the service... Try again later";
				}
				// show massage
				var messageboard = document.getElementById('message-board');
				messageboard.textContent=massage;

			}
			// get api's url from form action
			var formNode = el.parentNode;
			console.log(formNode);

			let check_item_url = formNode.getAttribute('action');
			xhr.open('POST', check_item_url, true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send('ajax=true&checked='+toCheck);
			
			stopBubbling(e);
}

var checkboxs = document.getElementsByTagName('button');
for(var checkbox of checkboxs){
	console.log(checkbox);
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
	console.log(deleteButt);
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
		console.log(item_title);

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

					// set delete button delete on click
					let newDeleteButt = find_1stDescendant_with(newliNode, 'a', 'delete-button');
					newDeleteButt.addEventListener("click", delete_item, false);

					// hide edit-panel, add-new-form, edit-title
					let editTitle = find_1stDescendant_with(newliNode, 'input', 'edit-title'); 
					let editForm = find_1stDescendant_with(newliNode, 'div', 'edit-panel');
					editTitle.className += ' hide';
					editForm.className += ' hide';
					newAddNew_form.className += ' hide';



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
	console.log(deleteButt);
	new_item_form.addEventListener("submit", add_new_item, false);
}
