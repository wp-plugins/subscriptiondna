<?php
$packages = SubscriptionDNA_ProcessRequest("", "list/packages");

$ccinfo = false;
$login_name = $_SESSION['login_name'];

if ($login_name != "") {

    $ccList = SubscriptionDNA_ProcessRequest(array("login_name" => $login_name), "creditcard/list", true);

    if (count($ccList) > 0) {
        $ccinfo = true;
        $ccdetail = $ccList[0];
    }
    if (@$_REQUEST["sender_fname"] == "") {
        $profile = SubscriptionDNA_ProcessRequest(array("login_name" => $_SESSION['login_name']), "user/profile", true);
        $_REQUEST["sender_fname"] = $profile["first_name"];
        $_REQUEST["sender_lname"] = $profile["last_name"];
        $_REQUEST["sender_email"] = $profile["email"];
    }
}
?>
<div align="center" id="DNAFormFields" class="form-border form-shadow"> <!-- Outer shadow div end-->
    <div class="required">
        <?php
        if ($_POST && $_REQUEST["dna_message"]) {
            echo($_REQUEST["dna_message"]);
        }
        ?>
    </div>
    <!--    bootstrap form start here-->

    <form method="post" class="form-horizontal form-border text-left pad-left-40 pad-right-40" name="customSubscribeForm" id="customSubscribeForm" action="" >
        <input type='hidden' name='payment_info_not_required' id="payment_info_not_required" value='<?php echo($_REQUEST["payment_info_not_required"]); ?>'>
        <input type="hidden" value="gift" name="dna_action_page" />
        <div id="x_sid_01_lbl_error" class="lblErr center-block  text-center"></div>






<!--        ////////////////////////////////////////////////////////
for show packages...........
-->


<?php
$categories=array();
$nonempty=0;
$selected_category=null;

if(is_array($packages))
{
    foreach($packages as $package)
    {
        $categories[$package->category]=$package->category;
        if($package->category!="")
        $nonempty++;
    }
}

?>

         <h3>Select a Gift Subscription Plan:</h3>

           <div class="clearfix center-block" <?php if($nonempty==0){echo("style='display:none'");} ?>>
            <?php
            $catcount = 0;
            $package_types = array();
            foreach ($categories as $category) {
                    $package_types[$catcount] = array();
                    foreach ($packages as $package) {
                        if ($package->category == $category) {
                            $package_types[$catcount][] = $package;
                        }
                    }
                    if($nonempty>0)
                    {

                    ?>
                        <div class="choice" onclick="showPackage(<?php echo($catcount); ?>);" style="float: left;">
                            <a href="javascript:;"><?php echo($category); ?></a>
                        </div>
                        <?php
                    }
                    $catcount++;
                }
                ?>
            </div>

            <div>
                    <?php
                    $count = 0;
                    foreach ($package_types as $key => $package_type) {
                        $packages = $package_type;
                        ?>
                        <div id="divPackageType<?php echo($key); ?>"  style="<?php if ($key != $selected_category  && $nonempty>0) echo("display: none"); ?>">
                        <?php
                        $count = 0;
                        foreach ($packages as $package) {
                            if (in_array($package->service_id . ";" . $package->billing_routine_id, $_POST["packages"]) || ($package->defaultval == "Yes" && !$selected)) {
                                $selected = $package->uid;
                                $selected_billing = $package->billing_routine_id;
                                $selected_package = $package->service_id . ";" . $package->billing_routine_id;
                                $sel_payment_info_not_required = $package->payment_info_not_required;
                                $sel_cost = $package->cost;
                            }
                            ?>
                                <div title="Click to select your subscription plan."  id="innerDiv_<?php echo($package->uid); ?>"  class='package-box package-box-main' onclick='packageChanged(this, "<?php echo($package->service_id); ?>;<?php echo($package->billing_routine_id); ?>", "<?php echo($package->payment_info_not_required); ?>");displayTotal("<?php echo($package->cost); ?>");'>
                                    <strong><?php echo($package->package_name); ?></strong>
                                    <div><?php echo($package->package_description); ?></div>
                                </div>
                                <?php
                                $count++;
                            }
                            ?>
                        </div>
                            <?php
                        }
                        ?>
                </div>

            <span id="package_lbl_error" class="lblErr center-block "></span>
            <input type="hidden" name="package" value="" id="package" />
            <input type="hidden" name="packages[]" id='selected_package' value="<?php echo($selected_package); ?>" />
            <input type='hidden' name='selected_package_cost' id="selected_package_cost" value='<?php echo($sel_cost); ?>'>




            <span id="error2" class="lblErr center-block "><?php echo($message); ?></span>


            <div class="form-group">
                <label id="promo_code_lbl" for="promo_code" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Enter Discount Code</label>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <input type="text" class="form-control " name="promo_code" value="<?php echo($_REQUEST["promo_code"]); ?>" id="promo_code"  size="30" MAXLENGTH="50" />
                    <span id="error2" class="lblErr center-block "></span>
                </div>
            </div>
            <span id="promo_code_lbl_error" class="lblErr center-block "><?php echo($message); ?></span>



        <h3>Gift Recipient Information</h3>




        <div class="form-group">
            <label for="first_name" class="col-md-12 " >First Name:</label>
            <div class="col-md-12">
                <input type="text" name="first_name" class="form-control noErr" id="first_name" value="<?php echo($_REQUEST["first_name"]); ?>" required="yes"  maxlength="50"/>

                <span id="first_name_lbl_error" class="lblErr center-block "></span>
            </div>
        </div>

        <div class="form-group">
            <label for="last_name" class="col-md-12 " >Last Name:</label>
            <div class="col-md-12">
                <input type="text" name="last_name" id="last_name" class="form-control noErr" value="<?php echo($_REQUEST["last_name"]); ?>" required="yes" maxlength="50"/>

                <span id="last_name_lbl_error" class="lblErr center-block "></span>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="col-md-12 " >Email:</label>
            <div class="col-md-12">
                <input type="text" name="email" id="email" value="<?php echo($_REQUEST["email"]); ?>"   class="form-control noErr" required="yes" maxlength="100"/>

                <span id="email_lbl_error" class="lblErr center-block "></span>
            </div>
        </div>

        <div class="form-group">
            <label for="email2" class="col-md-12 " >(Re-enter Email):</label>
            <div class="col-md-12">
                <input type="text" name="email2" id="email2" value="<?php echo($_REQUEST["email2"]); ?>"  class="form-control noErr" required="yes" maxlength="100"/>

                <span id="email2_lbl_error" class="lblErr center-block "></span>
            </div>
        </div>



        <div class="form-group">
            <label for="send_recipient_email_on" id="send_recipient_email_on_lbl" class="lbl col-md-12">Gift Delivery Date :</label>
            <input type="hidden" name="today" value="<?php echo(date('m/d/Y')); ?>"/>
            <div class="col-md-12">
                <input type="date" name="send_recipient_email_on" id="send_recipient_email_on"  size="20" class="form-control noErr" required="yes" maxlength="100">

                <span id="send_recipient_email_on_lbl_error" class="lblErr center-block "></span>
            </div>
        </div>





        <div class="form-group">
            <label for="custom_comment" class="col-md-12 lbl " >Type a message to be sent <br />with your gift subscription:</label>
            <div class="col-md-12">
                <textarea name="custom_comment" class="form-control"size="30" rows="5"><?php echo($_REQUEST["custom_comment"]); ?></textarea>

                <span id="custom_comment_lbl_error" class="lblErr center-block "></span>
            </div>
        </div>




        <div  id="dna-gift">
            <h3>Your Payment Information</h3>
            
            <b>Your Total Today:</b>

            <b><i><div id='displayTaxInfo' class='label label-default'><?php echo($sel_cost == "" ? "Please select a package." : "$" . $sel_cost); ?></div></i></b>
            <br /><br />
            <div  id="trCCInfo6">
            <?php
            if ($ccinfo) {
                ?>
                <script>
                    function hideShowCCInfo(chk)
                    {
                        if (chk)
                        {
                            hide = "none";
                            try
                            {
                                document.getElementById('existingCCInfo').style.display = "";
                            }
                            catch (Error) {
                            }
                        }
                        else
                        {
                            hide = "";
                            try
                            {
                                document.getElementById('existingCCInfo').style.display = "none";
                            }
                            catch (Error) {
                            }
                        }

                        for (i = 1; i < 6; i++)
                        {
                            document.getElementById('trCCInfo' + i).style.display = hide;
                        }
                    }
                </script>

            <div style="height:4em">
                <label>
                    <input style='width: 15px;height:1em;' type='checkbox' name='cc_on_file' id="cc_on_file" <?php if ($_POST["cc_on_file"] == "1" or $ccinfo) echo("checked"); ?> value='1' onclick="hideShowCCInfo(this.checked);">Use Existing Credit Card<br>
                </label>
            </div>




                <div class="form-group" id="existingCCInfo">
                    <label for="card_id" class="col-md-12" >Select Card:</label>
                    <div class="col-md-12">
                        <select name="card_id" class="form-control" id="card_id">

                            <?php
                            foreach ($ccList as $ccdetail) {
                                ?>
                                <option value="<?php echo($ccdetail["ccid"]); ?>"><?= $ccdetail["card_number"] ?> | <?= $ccdetail["expire_date"] ?> | <?= $ccdetail["card_type"] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <?php
                $display = "none";
// $display = "";
            }
            ?>

            </div> 

            <div class="form-group">
                <label for="sender_fname" class="col-md-12 " >Your First Name:</label>
                <div class="col-md-12">
                    <input type="text" name="sender_fname" id="sender_fname" value="<?php echo($_REQUEST["sender_fname"]); ?>"  size="30" maxlength="100" class="noErr form-control">

                    <span id="sender_fname_lbl_error" class="lblErr center-block "></span>
                </div>
            </div>


            <div class="form-group">
                <label for="sender_lname" class="col-md-12 sender_lname_lbl" >Your Last Name:</label>
                <div class="col-md-12">
                    <input type="text" name="sender_lname" id="sender_lname" value="<?php echo($_REQUEST["sender_lname"]); ?>"  size="30" maxlength="100" class="noErr form-control">

              <span id="sender_lname_lbl_error" class="lblErr center-block "></span>
              </div>
            </div>

            <div class="form-group">
                <label for="sender_email" class="col-md-12 lbl" >Your Email:</label>
                <div class="col-md-12">
                    <input type="text" name="sender_email" id="sender_email" value="<?php echo($_REQUEST["sender_email"]); ?>"  size="30" maxlength="100" class="noErr form-control">

                    <span id="sender_email_lbl_error" class="lblErr center-block "></span>
                </div>
            </div>



            <div id="trCCInfo1" style="display:<?php echo($display); ?>">
                <div class="form-group">
                    <label for="cc_name" class="col-md-12 lbl" id="cc_name_lbl" >Name on Card:</label>
                    <div class="col-md-12">
                        <input type="text" name="cc_name" id="sender_email" value="<?php echo($_REQUEST["cc_name"]); ?>"  size="30" maxlength="100" class="noErr form-control">

                        <span id="cc_name_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>
            </div>



            <div id="trCCInfo2" style="display:<?php echo($display); ?>">
                <div class="form-group">
                    <label for="cc_type" class="col-md-12 lbl" id="cc_type_lbl" >Credit card type:</label>
                    <div class="col-md-12">
                        <select name="cc_type" class="form-control noErr " id="cc_type">
                            <option></option>
                            <option value='MasterCard' >Master Card</option><option value='Visa' >Visa</option><option value='Discover' >Discover</option>
                        </select>
                        <span id="cc_type_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>
            </div>




            <div id="trCCInfo3" style="display:<?php echo($display); ?>">
                <div class="form-group">
                    <label for="cc_number" class="col-md-12 lbl" id="cc_number_lbl" >Credit card number:</label>
                    <div class="col-md-12">
                        <input type="text" name="cc_number" id="sender_email" id="cc_number" value="<?php echo($_REQUEST["cc_name"]); ?>"  size="30" maxlength="16" class="noErr form-control">

                        <span id="cc_number_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>
            </div>





            <div id="trCCInfo4" class="form-group" style="display:<?php echo($display); ?>">
                    <label for="cc_exp_month" class="col-md-12 lbl" id="cc_exp_month_lbl" >Card expiration:</label>
                    <div class="col-xs-6">
                        <select name="cc_exp_month" id="cc_exp_month"  class="noErr form-control">
                            <option>Month</option>
                            <option VALUE="01">January</option>
                            <option VALUE="02">February</option>
                            <option VALUE="03">March</option>
                            <option VALUE="04">April</option>
                            <option VALUE="05">May</option>
                            <option VALUE="06">June</option>
                            <option VALUE="07">July</option>
                            <option VALUE="08">August</option>
                            <option VALUE="09">September</option>
                            <option VALUE="10">October</option>
                            <option VALUE="11">November</option>
                            <option VALUE="12">December</option>
                        </select>

                    </div>
                    <div class="col-xs-6">
                        <select name="cc_exp_year" id="cc_exp_year"  class="noErr form-control">
                            <option>Year</option>
                            <?php
                            $year = date("Y");
                            for ($i = $year; $i <= $year + 9; $i++) {
                                ?><option value='<?php echo(substr($i, 2)); ?>'><?php echo($i); ?></option><?php
                            }
                            ?>
                        </select>

                </div>
            </div>

            <span id="cc_exp_month_lbl_error" class="lblErr center-block "></span>
            <span id="cc_exp_year_lbl_error" class="lblErr center-block "></span>

            <div id="trCCInfo5" style="display:<?php echo($display); ?>">
                <div class="form-group">
                    <label for="cc_cvv" class="col-md-12 lbl">CVC Code:</label>
                    <div class="col-md-12">
                        <input type="text" name="cc_cvv"  id="cc_cvv" size="5" maxlength="3" class="noErr form-control" style="max-width:100px;" />
                    </div>
                </div>
            </div>



            <div class="form-group">
                <div class="col-md-12">
                    <input name="x_submit" value="Click here to submit form" type="submit" class="btn btn-default btn-block noErr " onclick="return checkForm(this.form);" /> <!--this.form.frm_skip_validation.value = '0';-->
                </div>
            </div>
        </div>
    </form>     <!--    bootstrap form end here-->

    <br /><br />
    <div style="text-align: center;">
        <i>Processing may take a few seconds, and a paid receipt will be sent to you by e-mail.</i>
    </div>

</div> <!-- Outer shadow div end-->
<br/>
<br/>
        <script type="text/javascript">
<!--
    function showPackage(c) {

        for (i = 0; i <<?php echo(count($categories)); ?>; i++)
        {
            jQuery("#divPackageType" + i).hide();
        }

        jQuery("#divPackageType" + c).slideDown("slow");

    }
    function paymentMethodChanged(method)
    {
        if (method == "1")
        {
            for (i = 2; i <= 6; i++)
                jQuery("#paymentinfo" + i).hide();
        }
        else
        {
            for (i = 2; i <= 6; i++)
                jQuery("#paymentinfo" + i).show();
        }
    }
    function packageSelected(packob)
    {

        if (jQuery(packob).hasClass("package-box"))
        {

            jQuery(".package-box-main").each(function () {

                jQuery(this).removeClass('package-box-active');
                jQuery(this).addClass('package-box');

            });
            jQuery(packob).removeClass('package-box');
            jQuery(packob).addClass("package-box-active")
        }

    }
    function packageChanged(packob, package_id, payment_info_not_required)
    {
        jQuery("#selected_package").val(package_id);
        if (jQuery(packob).hasClass("package-box"))
        {

            jQuery(".package-box-main").each(function () {

                jQuery(this).removeClass('package-box-active');
                jQuery(this).addClass('package-box');

            });
            jQuery(packob).removeClass('package-box');
            jQuery(packob).addClass("package-box-active")
        }
        pcode = jQuery('#promo_code');
        if (pcode.val() != "")
        {
            pcode.blur();
        }
        showHidePaymentInfo(payment_info_not_required);
    }
    function showHidePaymentInfo(payment_info_not_required)
    {
        if (payment_info_not_required == "1")
        {
            for (i = 1; i <= 6; i++)
                jQuery("#trCCInfo" + i).hide();
        }
        else
        {
            for (i = 1; i <= 6; i++)
                jQuery("#trCCInfo" + i).show();
        }
        jQuery("#payment_info_not_required").val(payment_info_not_required);
    }
    function displayTotal(total)
    {
        jQuery('#selected_package_cost').val(total);
        document.getElementById('displayTaxInfo').innerHTML = "$" + total;
    }
    jQuery(document).ready(function () {
        var validateUsername = jQuery('#login_name_lbl_error');
        jQuery('#login_name').blur(function () {
            var t = this;
            if (this.value != this.lastValue && this.value != "") {
                if (this.timer)
                    clearTimeout(this.timer);

                jQuery('#username_validated').val("");
                jQuery('#x_submit').prop("disabled", true);

                validateUsername.removeClass('error').html('<img src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/images/loader.gif" height="16" width="16" />');
                this.timer = setTimeout(function () {
                    jQuery.ajax({
                        url: '<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/?dna_validate=login_name',
                        data: 'login_name=' + t.value,
                        dataType: 'html',
                        type: 'post',
                        success: function (j) {

                            validateUsername.html(j);
                            if (j.indexOf("lblErr") > 0)
                            {
                                jQuery('#username_validated').val("");
                            }
                            else
                            {
                                jQuery('#username_validated').val("1");
                            }
                            jQuery('#x_submit').prop("disabled", false);
                        }
                    });
                }, 200);
                this.lastValue = this.value;
            }
        });

        var validateEmail = jQuery('#email_lbl_error');
        jQuery('#email').blur(function () {
            var t = this;
            if (this.value != this.lastValue && this.value != "") {
                if (this.timer)
                    clearTimeout(this.timer);
                jQuery('#email_validated').val("");
                jQuery('#x_submit').prop("disabled", true);
                validateEmail.removeClass('error').html('<img src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/images/loader.gif" height="16" width="16" />');
                this.timer = setTimeout(function () {
                    jQuery.ajax({
                        url: '<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/?dna_validate=email',
                        data: 'email=' + t.value,
                        dataType: 'html',
                        type: 'post',
                        success: function (j) {
                            validateEmail.html(j);
                            if (j.indexOf("lblErr") > 0)
                            {
                                jQuery('#email_validated').val("");
                            }
                            else
                            {
                                jQuery('#email_validated').val("1");
                            }
                            jQuery('#x_submit').prop("disabled", false);
                        }
                    });
                }, 200);
                this.lastValue = this.value;
            }
        });

        var validatePromo = jQuery('#promo_code_lbl_error');
        if (selected_package == "")
        {
            validatePromo.html("Please select a package to validate promocode.");
        }
        else
        {
            jQuery('#promo_code').blur(function () {
                var t = this;

                var selected_package = jQuery('#selected_package').val();
                var selected_package_cost = jQuery('#selected_package_cost').val();

                if (this.timer)
                    clearTimeout(this.timer);
                validatePromo.removeClass('error').html('<img src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna//images/loader.gif" height="16" width="16" />');
                this.timer = setTimeout(function () {
                    jQuery.ajax({
                        url: '<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/?dna_validate=promo_code',
                        data: 'promo_code=' + t.value + "&selected_package_cost=" + selected_package_cost + "&selected_package=" + selected_package,
                        dataType: 'json',
                        type: 'post',
                        success: function (j) {
                            validatePromo.html(j.msg);
                            document.getElementById('displayTaxInfo').innerHTML = j.newcostmsg;
                            showHidePaymentInfo(j.payment_info_not_required);
                        }
                    });
                }, 200);
                this.lastValue = this.value;

            });
        }
    });

</script>


 <script type="text/javascript">
    focused = 0;
    function countryChanged(country) {
        if (country == "223") {
            document.getElementById('stateList').style.display = 'block';
            document.getElementById('state').style.display = 'none';
        } else {
            document.getElementById('stateList').style.display = 'none';
            document.getElementById('state').style.display = 'block';
            document.getElementById('state').value = "";
        }
    }
    function stateChanged(state) {
        document.getElementById('state').value = state;
    }

    /* support routines */
    function xGetElementById(e) {
        if (typeof (e) != "string")
            return e;
        if (document.getElementById)
            e = document.getElementById(e);
        else if (document.all)
            e = document.all[e];
        else if (document.layers)
            e = xLayer(e);
        else
            e = null;
        return e;
    }
    function xCollapse(e) {
        if (!(e = xGetElementById(e)))
            return;
        e.style.display = "none";
    }
    function xExpand(e) {
        if (!(e = xGetElementById(e)))
            return;
        e.style.display = "block";
    }
// set focusObj or lblObj to ZERO (0) to suppress

// at this point, the 2 pw's are NOT empty
    function validatePasswords(f) {

        var p1 = f.password.value,
                p2 = f.password2.value;

        if (p1 != p2)
            ValidateField(false, "password", "Passwords do not match.");
        else
            ValidateField(true, "password", "Passwords do not match.");
    }


// ensure the number doesn't have invalid chars
    function validateOnePhoneNumber(num)
    {
        var i = 0, ct = num.length, c;
        for (; i < ct; ++i)
        {
            c = num.charAt(i);
            if (!(c == '(' || c == ')' || c == ' ' || c == '-' || c == '+' || (c >= '0' && c <= '9')))
                return false;
        }
        return true;
    }

    function validatePhones(f)
    {
        var ph = f.phone.value;
        if (ph.length < 10)
            ValidateField(false, "phone", "Phone number is too short.");
        else if (!validateOnePhoneNumber(ph))
            ValidateField(false, "phone", "Phone number has invalid characters.");
        else
            ValidateField(true, "phone", "Phone number has invalid characters.");
    }

// see [http://www.breakingpar.com/] in the tips/regExp section
    function isEmailValid(emailAddress)
    {
        var re = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;

        //var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        return re.test(emailAddress);
        //return(true);
    }

    function validateEmails(f)
    {
        var e1 = f.email.value,
                e2 = f.email2.value;
        if (!isEmailValid(e1)) {
            ValidateField(false, "email", "Please enter a valid email.");
        } else if (e1 != e2) {
            ValidateField(false, "email", "Email fields do not match.");
        } else {
            ValidateField(true, "email", "Enter the same email in both fields.");
        }
    }

    function checkMembership(f) {
        if (!xGetElementById('x_sid_01').checked && !xGetElementById('x_sid_02').checked) {
            xGetElementById('x_sid_01').checked = true;
            ValidateField(false, "x_sid_01", "Please select membership type if unchecked.");
        } else {
            ValidateField(true, "x_sid_01", "Please select membership type if unchecked.");
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
        if (!re.test(cc_number))
            return false;
        // Checksum ("Mod 10")
        // Add even digits in even length strings or odd digits in odd length strings.
        for (i = (2 - (cc_number.length % 2)); i <= cc_number.length; i += 2) {
            checksum += parseInt(cc_number.charAt(i - 1));
        }
        // Analyze odd digits in even length strings or even digits in odd length strings.
        for (i = (cc_number.length % 2) + 1; i < cc_number.length; i += 2) {
            var digit = parseInt(cc_number.charAt(i - 1)) * 2;
            if (digit < 10) {
                checksum += digit;
            } else {
                checksum += (digit - 9);
            }
        }
        return ((checksum % 10) == 0);
    }

    function checkCreditCard(f) {
        if (isCCvalid(f.cc_type.value, f.cc_number.value))
            ValidateField(true, "cc_type", "");
        else {
            ValidateField(false, "cc_number", "Invalid credit card number.");
        }
    }

    function checkCreditCardExpiry(f) {
        var dtt = new Date();
        m1 = dtt.getMonth();
        m2 = f.cc_exp_month.value;
        y1 = dtt.getFullYear() - 2000;
        y2 = f.cc_exp_year.value;

        if ((m2 >= m1 && y2 >= y1) || y2 > y1) {
            ValidateField(true, "cc_exp_month", "");
        } else {
            ValidateField(false, "cc_exp_month", "Invalid credit card expiration date.");
        }
    }

    function checkEmpty(fid, message) {
        var obj = xGetElementById(fid);
        if (obj.value == "")
            return(ValidateField(false, fid, message));
        else
            return(ValidateField(true, fid, message));
    }
    mainValidated = true;

    function ValidateField(validated, fid, message) {
        if (!validated && mainValidated)
            mainValidated = false;

        var obj = xGetElementById(fid);
        var lbl_error = xGetElementById(fid + "_lbl_error");
        if (validated) {
            lbl_error.innerHTML = "";
            obj.className = obj.className+' noErr';
        } else {
            lbl_error.innerHTML = message;
            // hilite the error field
            if (focused == 0) {
                try {
                    obj.focus();
                }
                catch (errr) {
                }
                focused = 1;
            }
            obj.className = obj.className+' err';
        }
        return(validated);
    }

    function checkForm(f) {

        //if (f.frm_skip_validation.value == "1"){
        //    return(true);

        //}

        //alert('hellp');
        checkEmpty("first_name", "Please enter gift recipient First name.");
        checkEmpty("last_name", "Please enter gift recipient Last name.");

        checkEmpty("sender_fname", "Please enter your First name.");
        checkEmpty("sender_lname", "Please enter your Last name.");
        checkEmpty("sender_email", "Please enter your email.");

        //alert(checkEmpty("email","Please enter Email."));
        if (checkEmpty("email", "Please enter Email."))
            validateEmails(f);
        checkEmpty("email2", "Please re-enter Email.");

        checkEmpty("send_recipient_email_on", "Please select Gift Delivery Date.");
        if(xGetElementById("payment_info_not_required").value!="1")
        {
            if (f.cc_on_file && f.cc_on_file.checked)
            {
                //no cart validation
            }
            else
            {


                checkEmpty("cc_name", "Please enter Name on Card.");
                checkEmpty("cc_type", "Please select Card Type.");
                if (checkEmpty("cc_number", "Please enter Card Number."))
                    checkCreditCard(f);
                checkEmpty("cc_exp_month", "Expiry Month");
                if (checkEmpty("cc_exp_year", "Expiry Year"))
                    checkCreditCardExpiry(f);
            }
        }
        focused = 0;

        if (!mainValidated) {
            mainValidated = true;
            return(false);
        }
        else
            return true;
    }
    function changePeriodOptions(vall)
    {
        number_of_periods = document.getElementById("number_of_periods");
        for (i = 0; i <= 10; i++)
            number_of_periods.options[0] = null;
        if (vall == "y")
        {
            document.getElementById("lblPeriodOptions").innerHTML = "Please select # of Years";
            for (i = 1; i <= 5; i++)
            {
                if (i == 1)
                    label = "Year";
                else
                    label = "Years";
                amount = 99.95 * i;
                amount = amount.toFixed(2);
                number_of_periods.options[i - 1] = new Option(i + " " + label + " for $" + (amount) + "", i);
            }
            document.getElementById("trPeriodOptions").style.display = "none";
        }
        else if (vall == "6")
        {
            document.getElementById("lblPeriodOptions").innerHTML = "";
            for (i = 1; i <= 1; i++)
            {
                label = "Months";
                amount = 75;
                amount = amount.toFixed(2);
                number_of_periods.options[i - 1] = new Option("6 " + label + " for $" + (amount) + "", i);
            }
            document.getElementById("trPeriodOptions").style.display = "none";
        }
        else
        {
            document.getElementById("trPeriodOptions").style.display = "";
            document.getElementById("lblPeriodOptions").innerHTML = "Please select # of Months";
            for (i = 1; i <= 11; i++)
            {
                if (i == 1)
                    label = "Month";
                else
                    label = "Months";
                amount = 9.95 * i;
                amount = amount.toFixed(2);
                number_of_periods.options[i - 1] = new Option(i + " " + label + " for $" + (amount) + "", i);
            }
        }
    }
</script>
<?php
if ($_POST) {
    ?>
    <script>
        document.getElementById("cc_number").value = "<?php echo($_REQUEST["cc_number"]); ?>";
        document.getElementById("cc_type").value = "<?php echo($_REQUEST["cc_type"]); ?>";
        document.getElementById("cc_exp_month").value = "<?php echo($_REQUEST["cc_exp_month"]); ?>";
        document.getElementById("cc_exp_year").value = "<?php echo($_REQUEST["cc_exp_year"]); ?>";
        document.getElementById("cc_cvv").value = "<?php echo($_REQUEST["cc_cvv"]); ?>";


    </script>
    <?php
}
?>

