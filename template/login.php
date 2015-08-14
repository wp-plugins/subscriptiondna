<div align="center" class="form-border form-shadow">
<?php
if($_POST["status"])
{
?>
	<div align="center">
	<h1>Access Confirmation</h1>
	<b>You've successfully registered.  A confirmation email has been sent for your records.</b>

	<p>

	</div>
	Please Login here!<br>

<?php
}
echo(@$_REQUEST["dna_message"]);
?>
        <form id="login_form" class="form-horizontal form-border" name='login' action='' onsubmit="return verify();" method='POST'>
        <div id="dna-login" >
            <input type="hidden" value="<?php echo($_REQUEST["redirect_to"]); ?>" name="redirect_to" />
            <input type="hidden" value="login" name="dna_action_page" />
            
        <div class="form-group">
            <label for="login_name" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Username:</label>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="input-group ">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
                    <input type="text" class="form-control" name="login_name" id="login_name" value="" size="10" maxlength="50" required="yes" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('login_name_lbl_error'));" placeholder="Enter Login Name">
                </div>
                <span id="login_name_lbl_error" class="lblErr center-block text-center"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-md-12 col-sm-12 col-xs-12 control-label-align ">Password:</label>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="input-group ">
                    <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                    <input type="password" class="form-control" name="password" id="password" size="10" maxlength="20" required="yes" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('password_lbl_error'));" placeholder="Enter Password">
                </div>
                <span id="password_lbl_error" class="lblErr center-block text-center"></span>
            </div>
        </div>

       <?php
        if($GLOBALS["dna_result"]->errCode==-31)
        {
       ?>
        <div class="form-group">
            <div class="checkbox  col-md-12 control-label-align">
                <label>
                  <input type='checkbox' name='reset_devices' value="1" class="control-label"> Reset Devices
                </label>
            </div>
        </div>
       <?php
        }
       ?>

        <div class="form-group">
            <div class="col-md-12">
                <input name="cmdLogin" value="Login" type="submit" class="btn btn-default btn-block " />
            </div>
        </div>
    </div>
</form>
</div>
<script language="javascript" type="text/javascript">

	function SubscriptionDNA_GetElement(id){
		return document.getElementById(id);
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
		
			if(SubscriptionDNA_GetElement('login_name').value==""){
				//alert("Please provide Re-type Passowrd.");
                                var span = SubscriptionDNA_GetElement('login_name_lbl_error');
                                var errMsg = "Please provide Username" ;
                                showErrorMsg(span,errMsg);
				SubscriptionDNA_GetElement('login_name').focus();
				return false;
			}
			else if(SubscriptionDNA_GetElement('password').value==""){
				//alert("Please provide Re-type Passowrd.");
                                var span = SubscriptionDNA_GetElement('password_lbl_error');
                                var errMsg = "Please provide Password" ;
                                showErrorMsg(span,errMsg);
				SubscriptionDNA_GetElement('password').focus();
				return false;
			}
	
		return true;
	}
	</script>