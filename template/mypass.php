
<form name="login_form" method="post" action="" class="form-horizontal form-border form-shadow text-left pad-left-40" onsubmit="return verify();" >
   
    <div id="dna-profile" align="center" style="padding-right:28px;">
       <span id="avail_msg"><?=$msg ?></span>
    </div>

    <div id="dna-heading" style="padding-bottom:20px;"><h2>Change Password</h2></div>
    <div class="form-group">
        <label for="oldpassword" class="col-md-12" >Old Password:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="oldpassword" id="oldpassword" type="password" required="yes" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('oldpassword_lbl_error'));" class="form-control">
                <div class="input-group-addon req-star">&nbsp;</div>
            </div>
            <span id="oldpassword_lbl_error" class="lblErr center-block text-center"></span>
        </div>
    </div>

    <div class="form-group">
        <label for="password" class="col-md-12" >New Password:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="password" id="password" type="password" required="yes" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('password_lbl_error'));" class="form-control" />
                <div class="input-group-addon req-star">*</div>
            </div>
            <span id="password_lbl_error" class="lblErr center-block text-center"></span>
        </div>
    </div>

    <div class="form-group">
        <label for="password" class="col-md-12" >Re-enter Password:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="password2" id="password2" type="password" required="yes" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('password2_lbl_error'));" class="form-control" />
                <div class="input-group-addon req-star">*</div>
            </div>
            <span id="password2_lbl_error" class="lblErr center-block text-center"></span>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <div class="input-group">
                <input name="send" value="Submit" type="submit" class="btn btn-default btn-block "  />
                <div class="input-group-addon req-star">&nbsp;</div>
            </div>
        </div>
    </div>
</form>


<script language="javascript" type="text/javascript">

	function SubscriptionDNA_GetElement(id){
		return document.getElementById(id);
	}

	function IsNumeric(strString){
		var strValidChars = "0123456789.-";
		var strChar;
		var blnResult = true;
		if (strString.length == 0) return false;

		//  test strString consists of valid characters listed above
		for (i = 0; i < strString.length && blnResult == true; i++){
			strChar = strString.charAt(i);
			if (strValidChars.indexOf(strChar) == -1){
				blnResult = false;
			}
		}
		return blnResult;
	}


	function check_special_chr(fld){

		var iChars = "~!@#$%^&*()+=-[]\\\';,./{}|\":<>?";

		for (var i = 0; i < fld.length; i++) {
			if (iChars.indexOf(fld.charAt(i)) != -1) {
				return false;
			}
		}
		return true;
	}
        function showErrorMsg(span,message){
            var f = span;   // Get the input span element in the document with error
            jQuery(f).html(message);
        }
        function hideErrorMsg(span){
            var f = span;   // Get the input span element in the document with error
            jQuery(f).html('');
        }
	function verify(){
		if(SubscriptionDNA_GetElement('oldpassword').value!="")
		{
			if(SubscriptionDNA_GetElement('password').value==""){
				//alert("Please provide Re-type Passowrd.");
                                var span = SubscriptionDNA_GetElement('password_lbl_error');
                                var errMsg = "Please provide Password" ;
                                showErrorMsg(span,errMsg);
				SubscriptionDNA_GetElement('password').focus();
				return false;
			}
                        else if(check_special_chr(SubscriptionDNA_GetElement('password').value)==false){
				//alert ("Your Password has special characters. \nThese are not allowed.\n Please remove them and try again.");
                                var span = SubscriptionDNA_GetElement('password_lbl_error');
                                var errMsg = "Special characters are not allowed. Please remove & try again" ;
                                showErrorMsg(span,errMsg);
				SubscriptionDNA_GetElement('password').focus();
				return false;
			}
			else if(SubscriptionDNA_GetElement('password2').value==""){
				//alert("Please provide Re-type Passowrd.");
                                var span = SubscriptionDNA_GetElement('password2_lbl_error');
                                var errMsg = "Please provide Re-type Password" ;
                                showErrorMsg(span,errMsg);
				SubscriptionDNA_GetElement('password2').focus();
				return false;
			}
                        else if( SubscriptionDNA_GetElement('password').value != SubscriptionDNA_GetElement('password2').value )
                        {
				//alert("Password and Re Type Password fields do not match");
                                var span = SubscriptionDNA_GetElement('password2_lbl_error');
                                var errMsg = "Password and Re-Type Password fields do not match" ;
                                showErrorMsg(span,errMsg);
				SubscriptionDNA_GetElement('password').value='';
				SubscriptionDNA_GetElement('password2').value='';
				SubscriptionDNA_GetElement('password').focus();
				return false;
			}
		}
		else
		{
				//alert("Please enter old password");
                                var span = SubscriptionDNA_GetElement('oldpassword_lbl_error');
                                var errMsg = "Please enter old password" ;
                                showErrorMsg(span,errMsg);
				SubscriptionDNA_GetElement('oldpassword').focus();
				return false;
		}
		return true;
	}
	</script>