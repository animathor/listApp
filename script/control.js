/* Require: utilities.js
** // 1) Hide
** // 1-1) hide_more_collection
** // 1-2) hide_more_list
**
** // 2) Toggle hide 
** // 2-1) toggleHide
** // 2-2) edit_toggle_hide
**
** // 3) delete_element
**
** // 4) Add new element into page
** // 4-1) setLiAllControl
** // 4-2) deleteMsg
** // 4-3) addMsg
** // 4-4) getFormParams
** // 4-5) add_new_element  
** // 4-6) show_subelements
** // 4-7) utf8_byte_count
** // 4-8) update_element
**
** // 5) checkmark
**
** // 6) setSortable 
**
*/


// 1) Hide
// 1-1) hide_more_collection
// hide more in collection page
function hide_more_collection(){
	// hide edit-titles
	var hideTitles = document.getElementsByClassName('edit-title');
	for(var title of hideTitles){
		title.className += ' hide';	
	}	
	// hide addnews 
	let addnews = document.getElementsByClassName('add-new-collection')
	for(let addnew of addnews){
		if(addnew.parentNode.id !== 'subEles'){
			addnew.className += ' hide';
		}
	}
	let subcollections = document.getElementsByClassName('subcollections');
	for(let subcollection of subcollections){
		if(subcollection.parentNode.id !== 'subEles'){
			subcollection.className += ' hide';
		}
	}
	let all_lists = document.getElementsByClassName('lists');
	for(let a_lists of all_lists){
		if(a_lists.parentNode.id !== 'subEles'){
			a_lists.className += ' hide';}
	}
}

// 1-2) hide_more_list
// hide more in list page
function hide_more_list(e){
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
			sublist.className += ' hide';
		}
	}
}

// 2) Toggle hide 
// 2-1) toggleHide
function toggleHide(e){
		let className = e.className;
		if(/hide/.test(className)){
			e.className = className.replace(' hide', '');
		}else{
			e.className += ' hide';
		}
}

// 2-2) edit_toggle_hide
// edit button
function edit_toggle_hide(e){
	deleteMsg('updateMsg');// delete form massage
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

// 3) delete_element
// delete element(item, list, collection)
function delete_element(e){
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
	
			let delete_element_url = el.getAttribute('href');
			xhr.open('POST', delete_element_url, true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.send('ajax=true');
			
			stopBubbling(e);
}


// 4) Add new element into page
// 4-1) setLiAllControl
function setLiAllControl(element_type, newliNode){
	// set checkbox toggle checked
	let newCheckBox = newliNode.getElementsByTagName('button')[0];// only one button
	if(newCheckBox){
		newCheckBox.addEventListener("click", checkmark, false);
	}
	// set add-new-form add new on submit
	let newAddNew_form = find_1stDescendant_with(newliNode, 'form', 'add-new-'+element_type);
	if(newAddNew_form){
	newAddNew_form.addEventListener("submit", function(e){add_new_element(e,element_type)}, false);
	}
	// edit toggle hide
	let newEditButt = find_1stDescendant_with(newliNode, 'div', 'edit-button');
	newEditButt.addEventListener("click", edit_toggle_hide, false); 

	let newSubelementsUl =  find_1stDescendant_with(newliNode, 'ul', 'sub'+element_type+'s');
	if(newSubelementsUl){
	// item show subitems (delegate listen to <ul>)
	newSubelementsUl.addEventListener("click",function(e){show_subelements(e,element_type)}, false); 
	// set <ul> sortable(jQery)
		if(element_type == 'item'){
			setSortable($(newSubelementsUl));
		}
	}

	// set delete button delete on click
	let newDeleteButt = find_1stDescendant_with(newliNode, 'a', 'delete-button');
	newDeleteButt.addEventListener("click", delete_element, false);

	// hide edit-panel, add-new-form, edit-title
	let editTitle = find_1stDescendant_with(newliNode, 'input', 'edit-title'); 
	let editPanel = find_1stDescendant_with(newliNode, 'div', 'edit-panel');
	editTitle.className += ' hide';
	if(element_type === 'item'){
		editPanel.className += ' hide';
	}
	if(newAddNew_form){
		newAddNew_form.className += ' hide';
	}

	// update edit-form on submit
	let newEditForm = find_1stDescendant_with(newliNode, 'form', 'edit-form');
	let newFormData = getFormParams(newEditForm);
	newEditForm.addEventListener('submit',function(e){update_element(e,element_type,newFormData);}, false);

}
// 4-2) deleteMsg
function deleteMsg(targetName){
	let msg = document.getElementById(targetName);
	if(msg){
		msgParent = msg.parentNode;
		msgParent.removeChild(msg);
	}
}

// 4-3) addMsg
function addMsg(targetName,message,onElement, beforeEle = null){
	if(beforeEle == null){
		beforeEle = onElement.firstChild;
	}
	let msg = document.createElement('span');
	msg.textContent = message;
	msg.setAttribute('id',targetName);
	onElement.insertBefore(msg, beforeEle);
}
// 4-4) getFormParams
function getFormParams(formEle){
	let inputs=formEle.elements;
	let inputsData ={};
			for(let i=0; i<inputs.length;i++){
				inputsData[inputs[i].name]=inputs[i].value;
			}
	return inputsData;	
}
// 4-5) add_new_element  

// add new element(item list collection)
function add_new_element(e, element_type){
		preventLinkAction(e);
		let add_new_form = getTriggerEle(e);
		// get form elements
		var form_elements = add_new_form.elements; 
		let formParent = add_new_form.parentNode;
		element_title = form_elements[element_type+'_title'].value;
		// clear out the titleMsg create before
		deleteMsg('titleMsg');
		if(utf8_byte_count(element_title)<256){
			// set XHR
			var xhr = new XMLHttpRequest();
			xhr.onload = function(){

				var massage='';
				if(xhr.status === 200){
					// clear input
					form_elements[element_type+'_title'].value = '';
					// the sublist 'ul' is sibling of current form
					// add new item to sublist underneath
					let currentUl = formParent.getElementsByTagName('ul')[0];
					//let newliNode = document.createElement('li');
					//newliNode.innerHTML = xhr.responseText;
					//currentUl.appendChild(newliNode);
					currentUl.insertAdjacentHTML('afterbegin',xhr.responseText);// insert <li> by server side html
					let newliNode = currentUl.getElementsByTagName('li')[0];
					setLiAllControl(element_type,newliNode);

				}else if(xhr.status === 400){
					// title too long or incorrect query
					addMsg('titleMsg',xhr.responseText, formParent, add_new_form);
				}else{
					massage = " Something go wrong with the service... Try again later";
				}
				// show massage
				var messageboard = document.getElementById('message-board');
				messageboard.textContent=massage;

			}
	
			let add_new_item_url = add_new_form.getAttribute('action');
			xhr.open('POST', add_new_item_url, true);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			// encode to application/x-www-form-urlencoded format
			
			xhr.send('ajax=true&'+element_type+'_title='+encodeForPost(element_title));
	}else{
					addMsg('titleMsg','Title should be less than 255 characters', formParent, add_new_form);
	}
			stopBubbling(e);
}



// 4-6) show_subelements
// element button shows subelements(collections(and lists), items)
function show_subelements(e, element_type){
	var el = getTriggerEle(e);
			// if it is title link, element in form, don't trigger
			if(RegExp('^'+element_type).test(el.className)){
				// find li node
				var liNode = find_ancestor_tag(el,'li');
				if(el.parentNode.id != 'head'){
					if(liNode.className == 'load-more'){
						// li that doesn't display sub, may has subelements
						// set xhr for more
						let xhr = new XMLHttpRequest();
						xhr.onload = function(){
							if(xhr.status === 200){
								// add the add-new and subEles
								liNode.insertAdjacentHTML('beforeend', xhr.responseText);
								// set add-new-form add new on submit
								let newAddNew_form = find_1stDescendant_with(liNode, 'form', 'add-new-'+element_type);
								newAddNew_form.addEventListener("submit", function(e){add_new_element(e,element_type)}, false);

								let subLiNodes = liNode.getElementsByTagName('li');
								for(let subLiNode of subLiNodes){
									// set all control
									setLiAllControl(element_type, subLiNode);
								}
								// set subitems be sortable
								if(element_type=='item'){
									let subitemUl = liNode.getElementsByClassName('subitems')[0];
									setSortable($(subitemUl));
								}
								// this item is now loaded. wipe the classname
								liNode.className = liNode.className.replace('load-more','');
							}else if(xhr.status === 500){
								// show massage
								var messageboard = document.getElementById('message-board');
								messageboard.textContent="Fail to read sub"+element_type+'s';
							}
						};
						let load_more_from_url = '';
						if(element_type === 'item'){
							load_more_from_url = 'components/read_subitems.php?id='+liNode.dataset['id']+'&type='+liNode.dataset['type'];
						}else if(element_type === 'collection'){
							load_more_from_url = 'components/read_subcollections_and_lists.php?id='+liNode.dataset['id'];
						}

						xhr.open('POST',load_more_from_url,true);
						xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xhr.send();
					}else{
						// normal <li>
						var addNew = liNode.getElementsByClassName('add-new-'+element_type)[0];
						if(addNew){
							toggleHide(addNew);
						}
						
						if(element_type === 'item'){
							let sublist = liNode.getElementsByTagName('ul')[0];
							if(sublist){
								toggleHide(sublist);
								toggleSortable(sublist,el);
							}
						}else if(element_type === 'collection'){
							let subcollections = liNode.getElementsByClassName('subcollections')[0];
							if(subcollections){
								toggleHide(subcollections);
								toggleSortable(subcollections,el);
							}
							// the last is the sibling ul
							let all_lists = liNode.getElementsByClassName('lists');
							let the_lists = all_lists[all_lists.length-1];
							if(the_lists){
								toggleHide(the_lists);
								toggleSortable(the_lists,el);
							}
						}
					}//if --li no sub else --normal li
				}// not head item
			}
			// stop bubbling
			stopBubbling(e);
}

// 4-7) utf8_byte_count
function utf8_byte_count(str){
	let unicode=[];
	let totalBytes = 0;
	unicode[1] = str.match(/[\u0000-\u007F]/g);//'oneByteChars'
	unicode[2] = str.match(/[\u0080-\u07FF]/g);//'twoByteChars'
	unicode[3] = str.match(/[\u0800-\uFFFF]/g);//'threeByteChars'
	unicode[4] = str.match(/[\u10000-\u1FFFFF]/g);//'fourByteChars'
	for(let i=1; i<5;i++){
		if(unicode[i] != null){
			totalBytes += unicode[i].length*i;
		}
	}
	return totalBytes;
}

// 4-8) update_element
// update edit-form on submit
function update_element(e, element_type, dataBeforeEdit){
	preventLinkAction(e);
	let updateForm = getTriggerEle(e);
	let formData = updateForm.elements;

	//delete updatemsg
	deleteMsg('updateMsg');
	console.log("validate");
	//validate
	let utf8_strLength = utf8_byte_count(formData['title'].value);
	console.log(utf8_strLength);
	if(utf8_strLength < 256){
	

	// set xhr
	let xhr = new XMLHttpRequest();
	xhr.onload = function(){
		let message = '';
		let responseObj = JSON.parse(xhr.responseText);
		if(xhr.status === 200){
			if(responseObj.success){
				// update sanitized text
				formData['title'].value = responseObj.title;
				if(element_type === 'item'){
					formData['note'].value = responseObj.note;
				}
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
				editTitle.className += ' hide';
				if(element_type === 'item'){
					let editPanel = find_1stDescendant_with(updateForm, 'div', 'edit-panel');
					editPanel.className += ' hide';
				}
			}else{
				message = responseObj.message;
				// restore data
				for(inputName in dataBeforeEdit){
					formData[inputName].value = dataBeforeEdit[inputName];
				}
			}
		}else{
			if(xhr.status === 400){
				addUpdateMsg(responseObj.message, updateForm);
			}
			//message = "Something go wrong with the service... Try again later";
			// restore data
			for(inputname in dataBeforeEdit){
				formData[inputname].value = dataBeforeEdit[inputname];
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
	}else{
				addMsg('updateMsg',"Title should be less than 255 charaters", updateForm);
			// restore data
			for(inputname in dataBeforeEdit){
				formData[inputname].value = dataBeforeEdit[inputname];
			}
	}// end validate
}


// 5) checkmark
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

// 6) setSortable 
function toggleSortable(e,el=null){
		let className = e.className;
		// run after toggle hide, so disable when it is hided.
		if(/sortable/.test(className)){
			if(/hide/.test(className)){
				$(e).sortable('disable');
			}else{
				$(e).sortable('enable');
			}
		}else{
			console.log(e);
		}
}
// sortable (jQuery)
function setSortable(jq_obj, setDisabled = false){
	var formerParentId = null;
	var newParent = null;
	jq_obj.sortable({
		disabled: setDisabled,
		opacity:0.5,
		//axis:'y',
		cursor: "move",
		connectWith:'.subitems',
		
		forcePlaceholderSize: true,
		placeholder:'ui-state-highlight',
		handle:'.drag-handle',
		// cut from
		out: function(e, ui){
			formerParentId = ui.sender.data('id');
		},
		// paste on
		over:function(e, ui){
			$newParent = $(this);
		},
		stop: function (e,ui){
			$this = $(this);
			$newParent = ($newParent==null) ? $this : $newParent;
			// serilize the order of the items
			let item = $newParent.data();
			let order = $newParent.sortable('serialize');
			// update the change by Ajax
			console.log(formerParentId);
			if(formerParentId == $newParent.data('id')){
				$.ajax({
					method:"POST",
					url: "components/update_subitems_order.php?item_id="+item.id+"&item_type="+item.type,
					data: order,
					success: function(data){
						if(data.success == false){
							// cancel the sort and display message
							$this.sortable('cancel');
							$('#message-board').text("Fail to update the order of items");
						}
					},
					error: function(){
							$this.sortable('cancel');
							$('#message-board').text( "Sorry, somethig go wrong..., please try again later");
							
					}
				});
			}else{
			console.log(ui);
				let draggedSubItemId = ui.item.data('id');
				$.ajax({
					method:"POST",
					url: "components/move_subitem.php?item_id="+item.id+"&item_type="+item.type,
					data: order+"&draggedSubItemId="+draggedSubItemId+"&formerParentId="+formerParentId,
					success: function(data){
						if(data.success != true){
							$this.sortable('cancel');// cancel the sort and display message
							$('#message-board').text("Fail to move item");
						}
					},
					error: function(){
							$this.sortable('cancel');// cancel the sort and display message
							$('#message-board').text( "Sorry, somethig go wrong..., please try again later");
					}
				});
			}
		} // end stop
	});
}
