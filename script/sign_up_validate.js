// A)
// 		Registration validate
// A-1) Prepare a check list for valid input
// A-2) Check each input is valid or not
// 		-show the message
//		Functions: validateRequired, validateUsername, validateEmail, validatePassword
// A-3) After all inputs has checked, submit the form if all valid
//----------------------------------------------------------------
// B)
// 		Is username Registered?



// Validate the form on submit
var sign_up_form = document.getElementsByTagName('form')[0];// only one form
sign_up_form.setAttribute('noValidate','noValidate');// Disable HTML5 validation
sign_up_form.addEventListener('submit', function(e){

	// 1) Prepare a check list for valid input
	var valid ={};// check list
	let isFormValid;
	let result;// validate result

	var inputs = this.elements;
	// 2) Check each input is valid or not
	for(let i=0;i<inputs.length;i++){
		if(inputs[i].tagName.toLowerCase() != 'input' || inputs[i].type === 'submit'){
			continue;
		}
		result = validateRequired(inputs[i]);
		valid[inputs[i].id] = result.isValid;// note the result on check list
		if(!result.isValid){
			showErrorMessage(inputs[i].id, result.message);
		}else{
			removeErrorMessage(inputs[i].id);
			switch(inputs[i].id){
				case 'username':
					// Check username
					result = validateUsername();
					if(!result.isValid){
						showErrorMessage('username', result.message);
						valid.username = false;
					}else{
						removeErrorMessage('username');
					}
					break;
				case 'email':
					// Check email
					result = validateEmail();
					if(!result.isValid){
						showErrorMessage('email', result.message);
						valid.email= false;
					}else{
						removeErrorMessage('email');
					}
				case 'password':
					// Check password
					result = validatePassword();
					if(!result.isValid){
						showErrorMessage('password', result.message);
						valid.password= false;
					}else if(!result.isConfirm){
						showErrorMessage('password', result.message);
						valid.password= false;
					}else{
						removeErrorMessage('password');
					}
			}// End switch
		}// End if else
	}// End for
	
	// 3) After all inputs has checked, find the form is valid or not
	for(feild in valid){
		if(!valid[feild]){
			isFormValid = false;
			break;
		}
		isFormValid = true;
	}
	// don't submit the form if it invalid
	if(!isFormValid){
		preventLinkAction(e);// preventDefault(e) form not submit
	}
}, true);

function showErrorMessage(elementId, message){
	let messageBoard = document.getElementById(elementId+'_msg');
	messageBoard.textContent = message;
}

function removeErrorMessage(elementId){
	let messageBoard = document.getElementById(elementId+'_msg');
	messageBoard.textContent ='';
}


// validation function

// --required
	function validateRequired(el){
		let result={};
		if(isRequired(el)){
			if(isEmpty(el)){
				result['isValid'] = false;
				result['message'] = "Require input";
			}else{
				result['isValid'] = true;
			}
		}
		return result;
	}

	function isRequired(el){
		return ((typeof el.required === 'boolean') && el.required) ||
     (typeof el.required === 'string');
	}

	function isEmpty(el) {
    return !el.value || el.value === el.placeholder;
  }

// --alpha and numbers ,6-30 chars. 
	function validateUsername(){
		let usernameEle = document.getElementById('username');
		let result ={};
		let usernameStr = usernameEle.value;
		usernameRag = /^[\w]{6,40}$/;
		if(!usernameRag.test(usernameStr)){
			result['isValid'] = false;
			result['message'] = "6-40 characters of the alphabet and numbers";
		}else{
			result['isValid'] = true;
		}
		return result;
	}

// --emeil form
	function validateEmail(){
		let emailEle = document.getElementById('email');
		let result ={};
		let emailStr = emailEle.value;
		let emailReg = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
		if(!emailReg.test(emailStr)){
			result['isValid'] = false;
			result['message'] = "Please enter a valid email!";
		}else{
			result['isValid'] = true;
		}

		return result;
	}

// --alpha and numbers, 10-20 chars. --at least one number --at least one letter 
function validatePassword(){
		let passwordEle = document.getElementById('password');
		let confirmEle = document.getElementById('confirm');
		let result ={};
		let passwordStr = passwordEle.value;
		let confirmStr = confirmEle.value;
		if(passwordStr.length < 10 || passwordStr.length>20){
			result['isValid'] = false;
			result['message'] = "10-20 characters of the alphabet and numbers";
		}else if(!(/[a-zA-Z]+/.test(passwordStr))){
			result['isValid'] = false;
			result['message'] = "At least one letter of the alphabet!";
		}else if(!(/\d+/.test(passwordStr))){
			result['isValid'] = false;
			result['message'] = "At least one digit!";
		}else{
			result['isValid'] = true;
			// confirm password
			if(confirmStr !== passwordStr){
				result['isConfirm'] = false;
				result['message'] = "Passwords do not match!";
			}else{
				result['isConfirm'] = true;
			}
		}
		return result;
}

// B)
// 		Is username Registered?

// get username input element
let usernameInput = document.getElementById('username');
// set event on blur
usernameInput.addEventListener('blur',checkRegisteration, true);

function checkRegisteration(e){
	let usernameEle;
	if(this.id === 'username'){
		 usernameEle = this;
	}else{
	// if trigger by try again button
		 usernameEle =  document.getElementById('username');
	}
	if(!isEmpty(usernameEle)){
		let xhr = new XMLHttpRequest();
		xhr.onload = function(){
			// select message board for display
			let messageBoard = document.getElementById('username_msg');
			// remove old message if there are any
			messageBoard.textContent = '';
			let oldMessage = messageBoard.getElementsByTagName('span')[0];
			if(oldMessage){
				messageBoard.removeChild(oldMessage);
			}
			// display response
			let newMessage = document.createElement('span');
			if(xhr.status === 200){
				responseObj = JSON.parse(xhr.responseText);
				if(responseObj.success === true){
					if(responseObj.isRegistered){
						newMessage.textContent = "Please try another one";
					}else{
						newMessage.className ="pass";
						newMessage.textContent = "The name is available";
					}
				}else{
					let system_msgBorad = document.getElementById('system_msg');
					system_msgBorad.textContent = responseObj.message;
				}
			}else if(xhr.status ===500){
					newMessage.className ="try-again";
				// display message with a try again button on message board
				newMessage.textContent = "Server error! Please click here to try again";
				newMessage.addEventListener('click', checkRegisteration,true);// try again button on click
			}
			messageBoard.appendChild(newMessage);
		}
		xhr.open('POST',"components/check_user_name_is_registered.php", true);
		xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xhr.send("ajax=true&username="+encodeForPost(usernameEle.value));

		stopBubbling(e);
	}
}
