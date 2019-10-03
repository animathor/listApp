// checkboxs check
function checkmark(e){
		if(!e){
			e = window.event;
		}
		var	el = e.target || e.srcElement;
		// if element is checkbox
		if(el.tagName.toLowerCase() == 'input' && el.getAttribute('type') == 'checkbox'){
			var xhr = new XMLHttpRequest();
			xhr.open('POST', )

			
		}

		//stop bubbling
		if(e.stopPropogation){
			e.stopPropogation();
		}else{
			e.cancelBubble = true;
		}

		// prevent link to other page
		if(e.preventDefault){
			e.preventDefault();
		}else{
			e.returnValue = false;
		}
}
var lists = getElementsByTagName('ul');
for(var list of lists){
	list.addEventListener('input', checkmark, false);
}
