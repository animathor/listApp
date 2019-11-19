// pop up if ajax fail in session timeout
function sign_in_again(){
	// pop up the sign in form
	let cover = document.getElementById('sign_in_again');
	toggleHide(cover);
	let sign_in_form = $('#sign_in_again>form');
	setCenter(sign_in_form);
	$(window).on('resize',function(){setCenter(sign_in_form)});
}
function setCenter(jqobj) {
	$window = $(window);
  var top = Math.max($window.height() - jqobj.outerHeight(), 0) / 2;
  var left = Math.max($window.width() - jqobj.outerWidth(), 0) / 2;
  jqobj.css({
    top:top + $window.scrollTop(),
    left:left + $window.scrollLeft()
  });
}

function setSignInAgain(){
	let sign_in_form = $('#sign_in_again>form');
	// quit button
	$('#quit-app').on('click', function(){ window.location="index.php";});
	// sign in by ajax
	sign_in_form.on('submit',function(e){
		$('#signInMsg').remove();// clear message
		e.preventDefault();
		let form = $(this).get(0);
		let password = form.elements.password;
		let addSignInMsg = function(str){
			let msg = $('<span id="signInMsg" style="color:#fd008d;">'+str+'<span>');
			$('#sign_in_again h3').after(msg);
			msg.delay(3000).fadeOut();
		}
		let cover = document.getElementById('sign_in_again');
		if(/[a-zA-Z0-9]{10,20}/.test(password.value)){
			$.ajax({
				method:"POST",
				url:"sign_in.php",
				data:$.param(getFormParams(form))+"&ajax=true",
				success:function(data){
									if(data.success){
										password.value = '';// clear password input
										toggleHide(cover);// hide all sign in stuff
									}else{
										addSignInMsg('Incorrect password');
									}
								},
				error:function(){
								addSignInMsg('Something go wrong');
							}
			});
		}else{
			addSignInMsg('Incorrect password');
		}
	});
	
}

$(document).ready(function(){setSignInAgain();});
