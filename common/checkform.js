function check_field_empty(field, msg_text)
{	
	if (field.value == "")
	{
		alert(msg_text);
		return false;
	}
	else
	{
		return true;
	}
}

var submitFlag = false;
function submitForm(button, frm)
{
    if (!submitFlag) {
        submitFlag = true;
        frm.command.value = button.name;
        frm.submit();
    }

}

function checkForm(form)
{
    var emptyFields = "";
    var errors      = "";
    var elem;
    for (var i = 0; i < form.length; i++) {
        elem = form.elements[i];
        if (elem.date != null) {
              if (elem.value > elem.date.value) {
                  errors += "- " + elem.text1 + " date must be greater than " + elem.text2 + " date\n";
              }
              continue;
        }
        if ((elem.type == "password" || elem.type == "text" || elem.type == "textarea" || elem.type == "file") && elem.required) {
            if (elem.value == null || elem.value == "" || isBlank(elem.value)) {
                emptyFields += "          " + elem.text + "\n";
                continue;
            }
        }
        if (elem.required || (!elem.required && elem.value != "")) {
            if (elem.numeric || elem.min != null || elem.max != null) {
                n = parseFloat(elem.value);
                if  (isNaN(n)
                     || (elem.min != null && (n < elem.min))
                     || (elem.max != null && (n > elem.max))) {
                    errors += "- The field '" + elem.text + "' must be a number";
                    if ((elem.min != null) && (n < elem.min)) {
                        errors += " that is greater than " + (elem.min - 1);
                    } else if ((elem.max != null) && (n > elem.max)) {
                        errors += " that is less than " + (elem.max + 1);
                    }
                    errors += "\n";
                } else {
                    elem.value = n;
                }
            }
        }
    }
    if (emptyFields == "" && errors == "") {
        return true;
    } else {
        msg = "The form was not submited due to the following error(s)\n";
        if (emptyFields != "") {
            msg += "\n- The followind fields must be entered:\n" + emptyFields;
        }
        if (errors != "") {
            msg += "\n" + errors;
        }
        alert(msg);
        return false;
    }
}

function checkPasswords(password, confirm_password)
{
    var errors = "";
    if (password.value != confirm_password.value){
        errors += "- The 2 password fields do not match\n";
    }
    if (password.value.length < 6) {
        errors += "- Password must be at least 6 characters long\n";
    }
    if (errors != "") {
        var msg = "The form was not submited due to the following error(s)\n" + errors;
        alert(msg);
        return false;
    }
    return true;

}

function isBlank(s)
{
  // Returns true if string contains only whitespace
  var i;
  var c;

  for (i = 0; i < s.length; i++) {
    c = s.charAt(i);
    if (c != ' ' && c != '\n' && c != '\t')
      return false;
  }
  return true;
}


function emailCheck (emailStr)
{
    var emailPat=/^(.+)@(.+)$/;
    var specialChars="\\(\\)<>@,;:\\\\\\\"\\.\\[\\]";
    var validChars="\[^\\s" + specialChars + "\]";
    var quotedUser="(\"[^\"]*\")";
    var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
    var atom=validChars + '+';
    var word="(" + atom + "|" + quotedUser + ")";
    // The following pattern describes the structure of the user
    var userPat=new RegExp("^" + word + "(\\." + word + ")*$");
    var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$");
    var matchArray=emailStr.match(emailPat);
    if (matchArray==null)
    {
        alert("Email address seems incorrect (check @ and .'s)");
        return false;
    }
    var user=matchArray[1];
    var domain=matchArray[2];
    if (user.match(userPat)==null)
    {
    // user is not valid
        alert("The username doesn't seem to be valid.");
        return false;
    }
    var IPArray=domain.match(ipDomainPat)
    if (IPArray!=null)
    {
    // this is an IP address
        for (var i=1;i<=4;i++)
        {
            if (IPArray[i]>255)
            {
                alert("Destination IP address is invalid!");
                return false;
            }
        }
        return true;
    }
    var domainArray=domain.match(domainPat);
    if (domainArray==null)
    {
        alert("The domain name doesn't seem to be valid.");
        return false;
    }
    var atomPat=new RegExp(atom,"g");
    var domArr=domain.match(atomPat);
    var len=domArr.length;
    if (domArr[domArr.length-1].length<2 || domArr[domArr.length-1].length>3)
    {
        alert("The address must end in a three-letter domain, or two letter country.");
        return false;
    }
    if (len<2)
    {
        var errStr="This address is missing a hostname!";
        alert(errStr);
        return false;
    }
    return true;
}


function formatNum(num, prec)
{
    var k = Math.pow(10, prec);
    var intPart   = Math.floor(num);
    var fractPart = num - intPart + 1;
    var rounded   = String(Math.round(fractPart * k) / k);
    var s = rounded.substr(2);
    while (s.length < prec) {
        s += "0";
    }
    return intPart + "." + s;
}

function checkGroups()
{
    var error = 1;
    var obj = document.primary.elements['group[]'];
    if(obj != undefined)
    {
        if(obj.length != undefined)
        {
            for(var i = 0; i < obj.length ; i++)
            {
                if(obj[i].checked)
                {
                    error = 0;
                    break;
                }
            }
        }
        else
        {
            if (obj.checked)
            {
                error = 0;
            }
        } 
    }
    if(error)
    {
        var msg = "The form was not submited due to the following error(s)\n";
            msg += "\nYou must choose one group at least\n";
        alert(msg);
    }
    return !error;
}

function confirmDelete(text)
{
    return confirm("Are you sure you want to delete " + text + "?");
}

