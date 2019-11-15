/*
** DOM Navigate:
**	find_ancestor_class: find first ancestor by classname
**	find_ancestor_tag: find first ancestor by tagname
**	find_1stDescendant_with: find first descendant by tag and then class
**
** Event control (IE fallback):
**	getTriggerEle
**	preventLinkAction
**	stopBubbling
**	addEvent
**
** Other:
**	encodeForPost: encode data into utf-8 and the form data type
**
*/
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
		if(e.stopPropogation){
			e.stopPropogation();
		}else{
			e.cancelBubble = true;
		}
}

function addEvent(el, event, callback) {
  if ('addEventListener' in el) {                  
    el.addEventListener(event, callback, false);   
  } else {                                        
    el['e' + event + callback] = callback;         
    el[event + callback] = function () {
      el['e' + event + callback](window.event);
    };
    el.attachEvent('on' + event, el[event + callback]);
  }
}

// modify the code in MDN document about 'encodeURIComponent'
function encodeForPost(str) {
  return encodeURIComponent(str).replace('%20','+').replace(/[!'()*]/g, function(c) {
    return '%' + c.charCodeAt(0).toString(16);
  });
}

