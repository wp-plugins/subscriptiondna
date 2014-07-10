<div align="center">

	<?php	
                if (isset($_POST['cmdReset']))
                {
                    if (empty($_REQUEST["new_password"]) || $_REQUEST["c_new_password"]!=$_REQUEST["new_password"])
                    {
                            print_r("<div id='dna-login'><div id='failure'>Please enter new password</div></div>");
                    }
                    else
                    {
                        $result=SubscriptionDNA_ProcessRequest(array("reset_id"=>$_REQUEST["reset_id"],"new_password"=>$_REQUEST["new_password"]),"user/forgot_pass",true);
                        if ($result["errCode"] != 11)
                        {
                            print_r("<div id='dna-login'><div id='failure'>" . $result["errDesc"] . "</div></div>");
                        }
                        else
                        {
                            print_r("<div id='dna-login'><div id='failure'>" . $result["errDesc"] . " <a href='".get_permalink($GLOBALS['SubscriptionDNA']['Settings']['dna_pages']["login"])."'>Click here to login</a></div></div>");
                        }
                    }
                }
		else if(isset($_POST['send']))
                {					
			$data=array();
			$data["login_name"]=$_POST['login_name'];
			if(!empty($_POST['email']))
                            $data["email"]=$_POST['email'];
                        $data["reset"]="1";
                        $data["reset_url"]=get_permalink($GLOBALS['SubscriptionDNA']['Settings']['dna_pages']["forgot-password"]);
                        //reset, $reset_url, $reset_id, $new_password
                        $result = SubscriptionDNA_ProcessRequest($data,"user/forgot_pass",true);

			if($result["errCode"]!=18)
			{
				print_r ("<div id='dna-login'><div id='failure'>" . $result["errDesc"] . "</div></div>");
			}
			else
			{
				print_r ("<div id='dna-login'><div id='failure'>" . $result["errDesc"] . "</div></div>");
			}	

			?>
	<?php		 
		}
                if(isset($_REQUEST["reset_id"]))
                {
                    ?>
                    <form name="forget_form" method="post" onsubmit="return verifyReset();">
                        <table id="dna-login">
                            <tr>
                                <td class="dna-heading">Enter New Password:</td>
                                <td><input type="password" name="new_password" id="new_password" type="text" /></td>
                            </tr>
                            <tr>
                                <td class="dna-heading">Confirm New Password:</td>
                                <td><input type="password" name="c_new_password" id="c_new_password" type="text" /></td>
                            </tr>				
                            <tr>
                                <td></td>
                                <td><input name="cmdReset" value="Reset Password" type="submit" class="submit" /></td>
                            </tr>
                        </table>
                    </form>                    
                   <?php
                }
                else
                {
                    ?>
                    <form name="forget_form" method="post" onsubmit="return verify();">
                            <table id="dna-login">
                                    <tr>
                                            <td class="dna-heading">Login Name:</td>
                                            <td><input name="login_name" id="login_name" type="text" /></td>
                                    </tr>
                                    <tr>
                                            <td style="text-align: right;">OR</td>
                        <td></td>
                                    </tr>
                                    <tr>
                                            <td class="dna-heading">Email Address:</td>
                                            <td><input name="email" id="email" type="text" /></td>
                                    </tr>				
                                    <tr>
                                            <td></td>
                                            <td><input name="send" value="Submit" type="submit" /></td>
                                    </tr>
                            </table>
                    </form>	
                    <?php
                }
	?>
	
</div>

<script language="javascript" type="text/javascript">

function $(id){	
	return document.getElementById(id);
}

function verify(){
	if($('login_name').value != ""){
		if ($('login_name').value.indexOf(' ') != -1) {			
			alert("Space not allowed in the Login Name.");
			$('login_name').focus();
			return false;			
		}else{		 
			 if(check_special_chr($('login_name').value)==false){
				alert ("Special characters are not allowed in Login Name.");
				$('login_name').focus();
				return false;
			}
		}
	}else if($('email').value != ""){	
		if(!validate($('email').value)){		
			$('email').focus();
			return false; 
		}
	}else{
		alert ("Please provide either login name or email address on to retrieve your credentials.");
		$('login_name').focus();
		return false; 
	}
	return true;
}
function verifyReset() {
    if ($('new_password').value == "") {
        alert("Please enter new password.");
        $('new_password').focus();
        return(false);

    } else if ($('new_password').value != $('c_new_password').value) {
        alert("Passowrd and confirm password are not same.");
        $('new_password').focus();
        return(false);
    }
    return true;
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

function validate(id){
	var val=id;
	var checkTLD=1;
	var knownDomsPat=/^(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum)$/;
	var emailPat=/^(.+)@(.+)$/;
	var specialChars="\\(\\)><@,;:\\\\\\\"\\.\\[\\]";
	var validChars="\[^\\s" + specialChars + "\]";
	var quotedUser="(\"[^\"]*\")";
	var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
	var atom=validChars + '+';
	var word="(" + atom + "|" + quotedUser + ")";
	var userPat=new RegExp("^" + word + "(\\." + word + ")*$");
	var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$");
	var matchArray=val.match(emailPat);
	if (matchArray==null) {
		alert("Please provide valid Email");
		//alert("Email address seems incorrect (check @ and .'s)");
		return false;
	}
	var user=matchArray[1];
	var domain=matchArray[2];
	
	for (i=0; i<user.length; i++) {
		if (user.charCodeAt(i)>127) {
			alert("Please provide valid Email.");
		//	alert("The login_name contains invalid characters.");
		return false;
	   }
	}
	
	for (i=0; i<domain.length; i++) {
		if (domain.charCodeAt(i)>127) {
			alert("Please provide valid Email.");
		//	alert("Ths domain name contains invalid characters.");
			return false;
		}
	}
	
	if (user.match(userPat)==null) {
		alert("Please provide valid Email.");
	//	alert("The login_name doesn't seem to be valid.");
		return false;
	}
	var IPArray=domain.match(ipDomainPat);
	if (IPArray!=null) {
		for (var i=1;i<=4;i++) {
			if (IPArray[i]>255) {
				alert("Please provide valid Email.");
			//	alert("Destination IP address is invalid!");
				return false;
		   	}
		}
		return true;
	}
	
	var atomPat=new RegExp("^" + atom + "$");
	var domArr=domain.split(".");
	var len=domArr.length;
	for (i=0;i<len;i++) {
		if (domArr[i].search(atomPat)==-1) {
			alert("Please provide valid Email.");
			//alert("The domain name does not seem to be valid.");
			return false;
		}
	}
	if (checkTLD && domArr[domArr.length-1].length!=2 && 
		domArr[domArr.length-1].search(knownDomsPat)==-1) {
		alert("Please provide valid Email.");
	//	alert("The address must end in a well-known domain or two letter " + "country.");
		return false;
	}
	
	if (len<2) {
		alert("Please provide valid Email");
		alert("This address is missing a hostname!");
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