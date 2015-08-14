<div align="center" class="form-border form-shadow text-left">

    <?php
    echo($_REQUEST["dna_message"]);
    if (isset($_REQUEST["reset_id"]))
    {
        ?>
        <form name="forget_form" method="post" class="form-border" onsubmit="return verifyReset();">
            <div id="dna-login">
                <div class="form-group">
                    <label class="dna-heading" for="new_password">Enter New Password:</label>
                    <div class="input-group ">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                        <input type="password" name="new_password" id="new_password" type="text" class="form-control control-input" required="yes" onkeydown="hideAllMsg();" placeholder="Enter New Password" />
                    </div>
                    <span id="new_password_lbl_error" class="lblErr center-block text-center"></span>
                </div>
                <div class="form-group">
                    <label class="dna-heading" for="c_new_password">Confirm New Password:</label>
                    <div class="input-group ">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                        <input type="password" name="c_new_password" id="c_new_password" type="text" class="form-control control-input" required="yes" onkeydown="hideAllMsg();" placeholder="Re-enter New Password" />
                    </div>
                    <span id="c_new_password_lbl_error" class="lblErr center-block text-center"></span>
                </div>
                <input name="cmdReset" value="Reset Password" type="submit" class="btn btn-default btn-block " />
            </div>
        </form>
        <?php
    }
    else
    {
        ?>
        <form name="forget_form" class="form-border" method="post" onsubmit="return verify();">
            <div id="dna-login">
                <span id="general_lbl_error" class="lblErr center-block text-center"></span>
                <div class="form-group">
                    <label class="dna-heading" for="login_name">Login Name:</label>
                    <div class="input-group ">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
                        <input name="login_name" id="login_name" class="form-control control-input" placeholder="Enter Login Name" onkeydown="hideAllMsg();" type="text" />
                    </div>
                    <span id="login_name_lbl_error" class="lblErr center-block text-center"></span>
                </div>

                <div class="center-block text-center"><i>- OR -</i></div>
                <div class="form-group">
                    <label class="dna-heading" for="email">Email Address:</label>
                    <div class="input-group ">
                        <div class="input-group-addon">@</div>
                        <input name="email" id="email" class="form-control control-input" placeholder="Enter Login Name"  onkeydown="hideAllMsg();" type="text" />
                    </div>
                    <span id="email_lbl_error" class="lblErr center-block text-center"></span>
                </div>
                <input name="send" value="Submit" type="submit" class="btn btn-default btn-block " />

            </div>
        </form>
        <?php
    }
    ?>

</div>

<script language="javascript" type="text/javascript">
    function SubscriptionDNA_GetElement(id) {
        return document.getElementById(id);
    }

    function showErrorMsg(span, message) {
        var f = span;   // Get the input span element in the document with error
        jQuery(f).html(message);
    }
    function hideErrorMsg(span) {
        var f = span;   // Get the input span element in the document with error
        jQuery(f).html('');
    }
    function hideAllMsg() {
        var f = SubscriptionDNA_GetElement('general_lbl_error');
        jQuery(f).html('');
        var f = SubscriptionDNA_GetElement('login_name_lbl_error');
        jQuery(f).html('');
        var f = SubscriptionDNA_GetElement('new_password_lbl_error');
        jQuery(f).html('');
        var f = SubscriptionDNA_GetElement('c_new_password_lbl_error');
        jQuery(f).html('');
        var f = SubscriptionDNA_GetElement('email_lbl_error');
        jQuery(f).html('');
    }
    function verify() {
        if (SubscriptionDNA_GetElement('login_name').value != "") {
            if (SubscriptionDNA_GetElement('login_name').value.indexOf(' ') != -1) {
                //alert("Space not allowed in the Login Name.");
                var span = SubscriptionDNA_GetElement('login_name_lbl_error');
                var errMsg = "Space not allowed in the Login Name";
                showErrorMsg(span, errMsg);
                SubscriptionDNA_GetElement('login_name').focus();
                return false;
            } else {
                if (check_special_chr($('login_name').value) == false) {
                    //alert ("Special characters are not allowed in Login Name.");
                    var span = SubscriptionDNA_GetElement('login_name_lbl_error');
                    var errMsg = "Special characters are not allowed in Login Name";
                    showErrorMsg(span, errMsg);
                    SubscriptionDNA_GetElement('login_name').focus();
                    return false;
                }
            }
            return(true);
        } 
        else if (SubscriptionDNA_GetElement('email').value != "") {
            if (!validate(SubscriptionDNA_GetElement('email').value)) {
                SubscriptionDNA_GetElement('email').focus();
                return false;
            }
            return(true);
        } else {
            //alert ("Please provide either login name or email address on to retrieve your credentials.");
            var span = SubscriptionDNA_GetElement('general_lbl_error');
            var errMsg = "Please provide either login name or email address on to retrieve your credentials";
            showErrorMsg(span, errMsg);
            SubscriptionDNA_GetElement('login_name').focus();
            return false;
        }
        return true;
    }
    function verifyReset() {
        if (SubscriptionDNA_GetElement('new_password').value == "") {
            //alert("Please enter new password.");
            var span = SubscriptionDNA_GetElement('new_password_lbl_error');
            var errMsg = "Please enter new password";
            showErrorMsg(span, errMsg);
            SubscriptionDNA_GetElement('new_password').focus();
            return(false);

        } else if (SubscriptionDNA_GetElement('new_password').value != SubscriptionDNA_GetElement('c_new_password').value) {
            //alert("Passowrd and confirm password are not same.");
            var span = SubscriptionDNA_GetElement('c_new_password_lbl_error');
            var errMsg = "Passowrd and confirm password are not same";
            showErrorMsg(span, errMsg);
            SubscriptionDNA_GetElement('new_password').focus();
            return(false);
        }
        return true;
    }
    function check_special_chr(fld) {

        var iChars = "~!@#$%^&*()+=-[]\\\';,./{}|\":<>?";

        for (var i = 0; i < fld.length; i++) {
            if (iChars.indexOf(fld.charAt(i)) != -1) {
                return false;
            }
        }
        return true;
    }

    function validate(id) {
        var val = id;
        var checkTLD = 1;
        var knownDomsPat = /^(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum)$/;
        var emailPat = /^(.+)@(.+)$/;
        var specialChars = "\\(\\)><@,;:\\\\\\\"\\.\\[\\]";
        var validChars = "\[^\\s" + specialChars + "\]";
        var quotedUser = "(\"[^\"]*\")";
        var ipDomainPat = /^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
        var atom = validChars + '+';
        var word = "(" + atom + "|" + quotedUser + ")";
        var userPat = new RegExp("^" + word + "(\\." + word + ")*$");
        var domainPat = new RegExp("^" + atom + "(\\." + atom + ")*$");
        var matchArray = val.match(emailPat);
        if (matchArray == null) {
            //alert("Please provide valid Email");
            var span = SubscriptionDNA_GetElement('email_lbl_error');
            var errMsg = "Please provide valid Email";
            showErrorMsg(span, errMsg);
            return false;
        }
        var user = matchArray[1];
        var domain = matchArray[2];

        for (i = 0; i < user.length; i++) {
            if (user.charCodeAt(i) > 127) {
                //alert("Please provide valid Email.");
                var span = SubscriptionDNA_GetElement('email_lbl_error');
                var errMsg = "Please provide valid Email";
                showErrorMsg(span, errMsg);
                return false;
            }
        }

        for (i = 0; i < domain.length; i++) {
            if (domain.charCodeAt(i) > 127) {
                //alert("Please provide valid Email.");
                var span = SubscriptionDNA_GetElement('email_lbl_error');
                var errMsg = "Please provide valid Email";
                showErrorMsg(span, errMsg);
                return false;
            }
        }

        if (user.match(userPat) == null) {
            //alert("Please provide valid Email.");
            var span = SubscriptionDNA_GetElement('email_lbl_error');
            var errMsg = "Please provide valid Email";
            showErrorMsg(span, errMsg);
            return false;
        }
        var IPArray = domain.match(ipDomainPat);
        if (IPArray != null) {
            for (var i = 1; i <= 4; i++) {
                if (IPArray[i] > 255) {
                    //alert("Please provide valid Email.");
                    //	alert("Destination IP address is invalid!");
                    var span = SubscriptionDNA_GetElement('email_lbl_error');
                    var errMsg = "Please provide valid Email";
                    showErrorMsg(span, errMsg);
                    return false;
                }
            }
            return true;
        }

        var atomPat = new RegExp("^" + atom + "$");
        var domArr = domain.split(".");
        var len = domArr.length;
        for (i = 0; i < len; i++) {
            if (domArr[i].search(atomPat) == -1) {
                //alert("Please provide valid Email.");
                //alert("The domain name does not seem to be valid.");
                var span = SubscriptionDNA_GetElement('email_lbl_error');
                var errMsg = "Please provide valid Email";
                showErrorMsg(span, errMsg);
                return false;
            }
        }
        if (checkTLD && domArr[domArr.length - 1].length != 2 &&
                domArr[domArr.length - 1].search(knownDomsPat) == -1) {
            //alert("Please provide valid Email.");
            //	alert("The address must end in a well-known domain or two letter " + "country.");
            var span = SubscriptionDNA_GetElement('email_lbl_error');
            var errMsg = "Please provide valid Email";
            showErrorMsg(span, errMsg);
            return false;
        }

        if (len < 2) {
            //alert("Please provide valid Email");
            //alert("This address is missing a hostname!");
            var span = SubscriptionDNA_GetElement('email_lbl_error');
            var errMsg = "Please provide valid Email";
            showErrorMsg(span, errMsg);
            return false;
        }

        /*	length_2 = val.length;
         is_last = val.lastIndexOf('.')+1;

         if(	val.lastIndexOf('@')==-1 || val.lastIndexOf('@')==0 || val.lastIndexOf('@')==val.length-1 || val.lastIndexOf('.')==-1 || length_2==is_last){
         alert("Please provide valid Email");
         return false;
         }*/

        return true;
    }

</script>