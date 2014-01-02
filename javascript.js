focused=0;
function countryChanged(country) {
    if(country=="223") {
        document.getElementById('stateList').style.display='block';
        document.getElementById('state').style.display='none';
    } else {
        document.getElementById('stateList').style.display='none';
        document.getElementById('state').style.display='block';
        document.getElementById('state').value="";
    }
}
function stateChanged(state) {
    document.getElementById('state').value=state;
}
 
/* support routines */
function xGetElementById(e) {
    if(typeof(e)!="string") return e;
    if(document.getElementById) e=document.getElementById(e);
    else if(document.all) e=document.all[e];
    else if(document.layers) e=xLayer(e);
    else e=null;
    return e;
}
function xCollapse(e) {
    if(!(e=xGetElementById(e))) return;
    e.style.display = "none";
}
function xExpand(e) {
    if(!(e=xGetElementById(e))) return;
    e.style.display = "block";
}
// set focusObj or lblObj to ZERO (0) to suppress
 
// at this point, the 2 pw's are NOT empty
function validatePasswords(f) {

    var p1 = f.password.value,
        p2 = f.password2.value;
 
    if ( p1 != p2 )
        ValidateField(false,"password","Passwords do not match.");
    else
        ValidateField(true,"password","Passwords do not match.");
}
 
 
// ensure the number doesn't have invalid chars
function validateOnePhoneNumber(num)
{
    var i = 0, ct = num.length, c;
    for ( ; i < ct; ++i)
    {
        c = num.charAt(i);
        if (!( c == '(' || c == ')' || c == ' ' || c == '-' || c == '+' || (c >= '0' && c <= '9')))
            return false;
    }
    return true;
}
 
function validatePhones(f)
{
    var ph = f.phone.value;
    if (ph.length<10)
        ValidateField(false,"phone","Phone number is too short.");
    else if ( !validateOnePhoneNumber(ph))
        ValidateField(false,"phone","Phone number has invalid characters.");
    else
        ValidateField(true,"phone","Phone number has invalid characters.");
}
 
// see [http://www.breakingpar.com/] in the tips/regExp section
function isEmailValid(emailAddress)
{
    var re=/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
 
    //var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(emailAddress);
    //return(true);
}
 
function validateEmails(f)
{
    var e1 = f.email.value,
        e2 = f.email2.value;
    if ( !isEmailValid(e1)) {
        ValidateField(false,"email","Please enter a valid email.");
    } else if ( e1 != e2) {
        ValidateField(false,"email","Email fields do not match.");
    } else {
        ValidateField(true,"email","Enter the same email in both fields.");
    }
}
 
function checkLicenseAgreement(f) {
    if ( !f.agree.checked) {
        ValidateField(false,"agree","Please see terms & conditions");
    } else {
        ValidateField(true,"agree","Please see terms & conditions");
    }
}
 
function checkMembership(f) {
    if (validateSubscription()) {
        ValidateField(true,"package","");
    } else {
        ValidateField(false,"package","Please select at least one subscription plan.");
    }
}
 
// see [http://www.breakingpar.com/] in the tips/regExp section
function isCCvalid(cc_type, cc_number) {
    var re, checksum = 0, i;
    if (cc_type == "Visa")
        re = /^4\d{3}-?\d{4}-?\d{4}-?\d{4}$/;        // Visa: length 16, prefix 4, dashes optional.
    else if (cc_type == "MasterCard")
        re = /^5[1-5]\d{2}-?\d{4}-?\d{4}-?\d{4}$/;    // MC: length 16, prefix 51-55, dashes optional.
    else if (cc_type == "Discover")
        re = /^6011-?\d{4}-?\d{4}-?\d{4}$/;            // Disc: length 16, prefix 6011, dashes optional.
    else if (cc_type == "American Express")
        re = /^3[4,7]\d{13}$/;                        // Amex: length 15, prefix 34 or 37.
    else if (cc_type == "diners")
        re = /^3[0,6,8]\d{12}$/;                    // Diners: length 14, prefix 30, 36, or 38.
    else
        return false;
    if (!re.test(cc_number)) return false;
    // Checksum ("Mod 10")
    // Add even digits in even length strings or odd digits in odd length strings.
    for (i=(2-(cc_number.length % 2)); i<=cc_number.length; i+=2) {
        checksum += parseInt(cc_number.charAt(i-1));
    }
    // Analyze odd digits in even length strings or even digits in odd length strings.
    for (i=(cc_number.length % 2) + 1; i<cc_number.length; i+=2) {
        var digit = parseInt(cc_number.charAt(i-1)) * 2;
        if (digit < 10) { checksum += digit; } else { checksum += (digit-9); }
    }
    return ((checksum % 10) == 0);
}
 
function checkCreditCard(f) {
   if ( isCCvalid(f.cc_type.value,f.cc_number.value))
        ValidateField(true,"cc_type","");
   else {
        ValidateField(false,"cc_number","Invalid credit card number.");
   }
}

function checkCreditCardExpiry(f) {
    var dtt = new Date();
    m1=dtt.getMonth();
    m2=f.cc_exp_month.value;
    y1=dtt.getFullYear()-2000;
    y2=f.cc_exp_year.value;
 
   if ((m2>=m1 && y2>=y1) || y2>y1) {
        ValidateField(true,"cc_exp_month","");
   } else {
        ValidateField(false,"cc_exp_month","Invalid credit card expiration date.");
   }
}
 
function checkEmpty(fid,message) {
    var obj = xGetElementById(fid);
    if(obj.value=="")
        return(ValidateField(false,fid,message));
    else
        return(ValidateField(true,fid,message));
}
mainValidated=true;
 
function ValidateField(validated,fid,message) {
    if(!validated && mainValidated)
        mainValidated=false;
 
    var obj = xGetElementById(fid);
    var lbl_error = xGetElementById(fid+"_lbl_error");
    if(validated) {
        lbl_error.innerHTML = "";
        obj.className = 'noErr';
    } else {
        lbl_error.innerHTML = message;
    // hilite the error field
        if(focused==0) {
            try    {
                obj.focus();
            }
            catch(errr){}
            focused=1;
        }
        obj.className = 'err';
    }
    return(validated);
}
 
function checkForm(f) {
    checkMembership(f);
    checkEmpty("first_name","Please enter First name.");
    checkEmpty("last_name","Please enter Last name.");
    checkEmpty("login_name","Please enter Login name.");

    //alert(checkEmpty("email","Please enter Email."));
    if(checkEmpty("email","Please enter Email."))
        validateEmails(f);
    checkEmpty("email2","Please re-enter Email.");
 
    if(checkEmpty("password","Please enter Password."))
        validatePasswords(f);
    checkEmpty("password2","Please re-enter Password.");
     
    checkEmpty("cc_name","Please enter Name on Card.");
    checkEmpty("cc_type","Please select Card Type.");
    if(checkEmpty("cc_number","Please enter Card Number."))
    checkCreditCard(f);
    checkEmpty("cc_exp_month","Expiry Month");
    if(checkEmpty("cc_exp_year","Expiry Year"))
    checkCreditCardExpiry(f);
     
	checkEmpty("country","Please select Country.");
	checkEmpty("address1","Please enter Address.");
	checkEmpty("city","Please enter City.");
    checkEmpty("state","Please select State.");
    checkEmpty("zipcode","Please enter Zip.");
    
    if(checkEmpty("phone","Please enter Phone."))
    validatePhones(f);
        checkLicenseAgreement(f);
 
    focused=0;

    if(!mainValidated) {
        mainValidated=true;
        return(false);
    }
    else
    return true;
}
