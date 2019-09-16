function toggleHide(e){
		let className = e.className;
		if(className.match('hide')){
			e.className = className.replace(' hide', '');
		}else{
			e.className += ' hide';
			console.log(e.className);
		}
}
