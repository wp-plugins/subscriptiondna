<?php
if (isset($_GET["sub_group"])) {
    $_SESSION["sub_group"] = $_REQUEST["sub_group"];
    $data = array("group_id" => $_SESSION["sub_group"]);
    $result = SubscriptionDNA_ProcessRequest($data, "group/get_configuration", true);
    if ($result["errCode"] < 0) {
        die("Invalid Group");
    }
}
if($_REQUEST["ref"]!="" && $_REQUEST["promo_code"]=="")
{
    $msg="";
    $data=array("promo_code"=>$_REQUEST["ref"]);
    $promocode = SubscriptionDNA_ProcessRequest($data,"subscription/validate_promocode",true);
    if($promocode["discount_mod"]=="%")
    {
        $msg='Referral code is valid. You save '.$promocode["discount"].$promocode["discount_mod"].'';
    }
    elseif($promocode["discount_mod"]=="$")
    {
        $msg='Referral code is valid. You save $'.$promocode["discount"].'';
    }
    if($promocode["discount"]>0)
    {
        $message="<span style='color:green;'>".$msg."</span>";
        $_REQUEST["promo_code"]=$_REQUEST["ref"];
    }
}

$canada_provinces = SubscriptionDNA_GetProvinces();
$packages = SubscriptionDNA_ProcessRequest("", "list/packages");

$customFields = SubscriptionDNA_ProcessRequest("", "list/custom_fields");
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
            for (i = 2; i <= 7; i++)
                jQuery("#paymentinfo" + i).hide();
        }
        else
        {
            for (i = 2; i <= 7; i++)
                jQuery("#paymentinfo" + i).show();
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
            jQuery('#promo_code').blur(function () {
                var t = this;

                var selected_package = jQuery('#selected_package').val();
                var selected_package_cost = jQuery('#selected_package_cost').val();

                if (this.timer)
                    clearTimeout(this.timer);
                validatePromo.removeClass('error').html('<img src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/images/loader.gif" height="16" width="16" />');
                if (jQuery('#promo_code').val() == "")
                {
                    validatePromo.html("Please enter promocode.");
                    return false;
                }
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
            jQuery('#how_referred').blur(function () {
                if (jQuery('#promo_code').val() == "")
                {
                    var t = this;
                    howReferred=jQuery('#how_referred_lbl_error');
                    var selected_package = jQuery('#selected_package').val();
                    var selected_package_cost = jQuery('#selected_package_cost').val();

                    if (this.timer)
                        clearTimeout(this.timer);
                    howReferred.removeClass('error').html('<img src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/images/loader.gif" height="16" width="16" />');
                    this.timer = setTimeout(function () {
                        jQuery.ajax({
                            url: '<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/?dna_validate=promo_code',
                            data: 'promo_code=' + t.value + "&selected_package_cost=" + selected_package_cost + "&selected_package=" + selected_package,
                            dataType: 'json',
                            type: 'post',
                            success: function (j) {
                                if(selected_package_cost!=j.newcost) 
                                {
                                    jQuery('#promo_code').val(t.value);
                                    howReferred.html(j.msg);
                                    validatePromo.html(j.msg);
                                    document.getElementById('displayTaxInfo').innerHTML = j.newcostmsg;
                                }
                                else
                                {
                                    howReferred.html("");
                                    validatePromo.html("");
                                }
                            }
                        });
                    }, 200);
                    this.lastValue = this.value;
                }

            });        
                
        
        
    });
-->
</script>
<script type="text/javascript" src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/js/dna.js?cache=1"></script>

<div  class="form-border form-shadow center-block" style="min-width:200px" id="DNAFormFields"> <!-- Outer shadow div start -->
<?php
    echo($_REQUEST["dna_message"]);
?>

    <!--Bootstrap form start-->
    <form method="post" class="form-horizontal form-border text-left pad-left-40 pad-right-40" id="subscription_form" name="customSubscribeForm" action="">
        <input type="hidden" value="register" name="dna_action_page" />
        <input type='hidden' name='payment_info_not_required' id="payment_info_not_required" value='<?php echo($_REQUEST["payment_info_not_required"]); ?>'>
        <input type='hidden' name='x_confirmurl' value='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>'>

        <input type='hidden' name='email_validated' id="email_validated" value='<?php echo($_REQUEST["email_validated"]); ?>'>
        <input type='hidden' name='username_validated' id="username_validated" value='<?php echo($_REQUEST["username_validated"]); ?>'>
        <input type='hidden' name='group_owner_id' id="group_owner_id" value='<?php echo($_SESSION["sub_group"]); ?>'>



        <span id="x_sid_01_lbl_error" class="lblErr center-block "></span><br>


<?php
if ($_SESSION["sub_group"] == "") {
    ?>

            <h3 >Please select a subscription plan:</h3>

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
            <hr />
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
                            if (@in_array($package->service_id . ";" . $package->billing_routine_id, $_POST["packages"]) || ($package->defaultval == "Yes" && !$selected)) {
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

                <div class="form-group">
                    <label id="promo_code_lbl" for="promo_code" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Enter Discount Code</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control " name="promo_code" value="<?php echo($_REQUEST["promo_code"]); ?>" id="promo_code"  size="30" MAXLENGTH="50" />
                        <span id="error2" class="lblErr center-block "></span>
                    </div>
                </div>
                <span id="promo_code_lbl_error" class="lblErr center-block "><?php echo($message); ?></span>

    <?php
}
    ?>
        <hr />
        <h3>Member Information</h3>
                <div class="form-group">
                    <label id="first_name_lbl" for="first_name" class="col-md-12 col-sm-12 col-xs-12  control-label-align">First Name</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input text="text" class="form-control " name="first_name" value="<?php echo($_REQUEST["first_name"]); ?>" id="first_name" size="30" maxlength="50" />
                        <span id="first_name_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="last_name_lbl" for="last_name" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Last Name</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control " name="last_name" value="<?php echo($_REQUEST["last_name"]); ?>"  id="last_name" size="30" maxlength="50">
                        <span id="last_name_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>





                <div class="form-group">
                    <label id="login_name_lbl" for="login_name" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Login Username</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="login_name" class="form-control " value="<?php echo($_REQUEST["login_name"]); ?>"  id="login_name" size="30"  maxlength="100" >

                        <span id="login_name_lbl_error" class="lblErr center-block "><?php echo($msgu); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="password_lbl" for="password" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Login Password</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input name="password" value="<?php echo($_REQUEST["password"]); ?>"  class="form-control " id="password" type="password" size="30" maxlength="20">

                        <span id="password_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="password2_lbl" for="password2" class="col-md-12 col-sm-12 col-xs-12  control-label-align">(Re-enter Password)</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input name="password2" value="<?php echo($_REQUEST["password2"]); ?>" id="password2" class="form-control " type="password" size="30" maxlength="20">

                        <span id="password2_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="email_lbl" for="email" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Account Email</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="email" class="form-control " value="<?php echo($_REQUEST["email"]); ?>" id="email" size="30"  maxlength="100">

                        <span id="email_lbl_error" class="lblErr center-block "><?php echo($msge); ?></span>
                    </div>
                </div>


                <div class="form-group">
                    <label id="email2_lbl" for="email2" class="col-md-12 col-sm-12 col-xs-12  control-label-align">(Re-enter Email)</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="email2" class="form-control " value="<?php echo($_REQUEST["email2"]); ?>" id="email2" size="30"  maxlength="100">

                        <span id="email2_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>


                <div class="form-group">
                    <label id="company_name_lbl" for="company_name" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Company Name</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="company_name" class="form-control " value="<?php echo($_REQUEST["company_name"]); ?>" id="company_name" size="30"  maxlength="25">
                        <span id="company_name_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="job_title_lbl" for="company_name" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Job Title</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="job_title" class="form-control " value="<?php echo($_REQUEST["job_title"]); ?>" id="job_title" size="30"  maxlength="25">
                        <span id="job_title_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

<?php
if ($_SESSION["sub_group"] == "") {
    ?>
            <hr/>

            <div id='paymentinfo1'>
                <h3>Payment Information</h3>
            </div>


                <b>Your Total Today:</b>

                    <b><i><div id='displayTaxInfo' class='label label-default'><?php echo($sel_cost == "" ? "Please select a package." : "$" . $sel_cost); ?></div></i></b>
                    <br /><br />

                <div id='paymentinfo7 radio'>
                    <span id="check_mo_lbl"  class="control-label-align"><b>Payment Method</b></span>

                        <br />
                        <label >
                            <input type='radio' style='width: 15px;height:1em;' name='check_mo' id='check_mo_1' value='0' onclick='paymentMethodChanged("0");'> Credit Card
                        </label>
                        <br />
                        <label >
                            <input type='radio' style='width: 15px;height:1em;' name='check_mo' id='check_mo' value='1' onclick='paymentMethodChanged("1");'> Check/Mo
                        </label>
                        <br/>
                        <span id="check_mo_lbl_error" class="lblErr center-block "></span>

                </div>

                <div id='paymentinfo2'>
                    <div class="form-group">
                        <label id="cc_name_lbl" for="cc_name" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Cardholder Name</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="cc_name" class="form-control " value="<?php echo($_REQUEST["cc_name"]); ?>" id="cc_name" size="30" maxlength="100" >

                            <span id="cc_name_lbl_error" class="lblErr center-block "></span>
                        </div>
                    </div>
                </div>

                <div id='paymentinfo3'>
                    <div class="form-group">
                        <label id="cc_type_lbl" for="cc_type" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Credit Card Type</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">

                            <select name="cc_type" id="cc_type" class="form-control  text-left">
                                <option>Select Card Type</option>
                                <option value='MasterCard' >MasterCard</option>
                                <option value='Visa' >Visa</option>
                                <option value='Discover' >Discover</option>
                                <option value='American Express' >American Express</option>
                            </select>

                            <span id="cc_type_lbl_error" class="lblErr center-block "></span>
                        </div>
                    </div>
                </div>

                <div id='paymentinfo4'>
                    <div class="form-group">
                        <label id="cc_number_lbl" for="cc_number" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Credit Card Number</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="cc_number" class="form-control " value="<?php echo($_REQUEST["cc_number"]); ?>" id="cc_number" size="30" maxlength="16" >
                            <span id="cc_number_lbl_error" class="lblErr center-block "></span>
                        </div>
                    </div>
                </div>

                <div id='paymentinfo5'>
                    <div class="form-group">
                        <label id="cc_exp_month_lbl" for="cc_exp_month" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Card Expiration</label>
                        <!-- <span id="cc_exp_month_lbl" class="lbl">Card Expiration</span> -->
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div id="month-select-list" class="col-md-6 col-sm-6 col-xs-7">
                                <select name="cc_exp_month" id="cc_exp_month" class="form-control  text-left">
                                    <option value=''>Month</option>
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
                            <div id="year-select-list" class="col-md-6 col-sm-6 col-xs-5">
                                <select name="cc_exp_year" id="cc_exp_year" class="form-control  text-left">
                                    <option value=''>Year</option>
                                    <?php
                                    $year = date("Y");
                                    for ($i = $year; $i <= $year + 9; $i++) {
                                        ?><option value='<?php echo(substr($i, 2)); ?>'><?php echo($i); ?></option><?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <span id="cc_exp_month_lbl_error" class="lblErr center-block "></span>
                            <span id="cc_exp_year_lbl_error" class="lblErr center-block "></span>
                        </div>
                    </div>
                </div>

                <div id='paymentinfo6'>
                    <div class="form-group">
                        <label for="cc_cvv" class="col-md-12 col-sm-12 col-xs-12  control-label-align ">CVC Code:</label>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" name="cc_cvv" class="form-control  text-center" style="max-width:100px;" value="<?php echo($_REQUEST["cc_cvv"]); ?>" id="cc_cvv"  maxlength="4" >
                        </div>
                    </div>
                </div>
                <hr />
    <?php
}
?>

                <h3>Contact Information</h3>

                <div class="form-group">
                    <label id="country_lbl" for="country" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Country</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <select name="country" id="country"  class="form-control  text-left"  onchange="countryChanged(this.value);">
                            <option label="Please Select" value="">Please Select</option><option value='223'>United States</option><option value='1'>Afghanistan</option><option value='2'>Albania</option><option value='3'>Algeria</option><option value='4'>American Samoa</option><option value='5'>Andorra</option><option value='6'>Angola</option><option value='7'>Anguilla</option><option value='8'>Antarctica</option><option value='9'>Antigua and Barbuda</option><option value='10'>Argentina</option><option value='11'>Armenia</option><option value='12'>Aruba</option><option value='13'>Australia</option><option value='14'>Austria</option><option value='15'>Azerbaijan</option><option value='16'>Bahamas</option><option value='17'>Bahrain</option><option value='18'>Bangladesh</option><option value='19'>Barbados</option><option value='20'>Belarus</option><option value='21'>Belgium</option><option value='22'>Belize</option><option value='23'>Benin</option><option value='24'>Bermuda</option><option value='25'>Bhutan</option><option value='26'>Bolivia</option><option value='27'>Bosnia and Herzegowina</option><option value='28'>Botswana</option><option value='29'>Bouvet Island</option><option value='30'>Brazil</option><option value='31'>British Indian Ocean Territory</option><option value='32'>Brunei Darussalam</option><option value='33'>Bulgaria</option><option value='34'>Burkina Faso</option><option value='35'>Burundi</option><option value='36'>Cambodia</option><option value='37'>Cameroon</option><option value='38'>Canada</option><option value='39'>Cape Verde</option><option value='40'>Cayman Islands</option><option value='41'>Central African Republic</option><option value='42'>Chad</option><option value='43'>Chile</option><option value='44'>China</option><option value='45'>Christmas Island</option><option value='46'>Cocos (Keeling) Islands</option><option value='47'>Colombia</option><option value='48'>Comoros</option><option value='49'>Congo</option><option value='50'>Cook Islands</option><option value='51'>Costa Rica</option><option value='52'>Cote D'Ivoire</option><option value='53'>Croatia</option><option value='54'>Cuba</option><option value='55'>Cyprus</option><option value='56'>Czech Republic</option><option value='57'>Denmark</option><option value='58'>Djibouti</option><option value='59'>Dominica</option><option value='60'>Dominican Republic</option><option value='61'>East Timor</option><option value='62'>Ecuador</option><option value='63'>Egypt</option><option value='64'>El Salvador</option><option value='65'>Equatorial Guinea</option><option value='66'>Eritrea</option><option value='67'>Estonia</option><option value='68'>Ethiopia</option><option value='69'>Falkland Islands (Malvinas)</option><option value='70'>Faroe Islands</option><option value='71'>Fiji</option><option value='72'>Finland</option><option value='73'>France</option><option value='74'>France, Metropolitan</option><option value='75'>French Guiana</option><option value='76'>French Polynesia</option><option value='77'>French Southern Territories</option><option value='78'>Gabon</option><option value='79'>Gambia</option><option value='80'>Georgia</option><option value='81'>Germany</option><option value='82'>Ghana</option><option value='83'>Gibraltar</option><option value='84'>Greece</option><option value='85'>Greenland</option><option value='86'>Grenada</option><option value='87'>Guadeloupe</option><option value='88'>Guam</option><option value='89'>Guatemala</option><option value='90'>Guinea</option><option value='91'>Guinea-bissau</option><option value='92'>Guyana</option><option value='93'>Haiti</option><option value='94'>Heard and Mc Donald Islands</option><option value='95'>Honduras</option><option value='96'>Hong Kong</option><option value='97'>Hungary</option><option value='98'>Iceland</option><option value='99'>India</option><option value='100'>Indonesia</option><option value='101'>Iran (Islamic Republic of)</option><option value='102'>Iraq</option><option value='103'>Ireland</option><option value='104'>Israel</option><option value='105'>Italy</option><option value='106'>Jamaica</option><option value='107'>Japan</option><option value='108'>Jordan</option><option value='109'>Kazakhstan</option><option value='110'>Kenya</option><option value='111'>Kiribati</option><option value='112'>Korea, Democratic People's Republic of</option><option value='113'>Korea, Republic of</option><option value='114'>Kuwait</option><option value='115'>Kyrgyzstan</option><option value='116'>Lao People's Democratic Republic</option><option value='117'>Latvia</option><option value='118'>Lebanon</option><option value='119'>Lesotho</option><option value='120'>Liberia</option><option value='121'>Libyan Arab Jamahiriya</option><option value='122'>Liechtenstein</option><option value='123'>Lithuania</option><option value='124'>Luxembourg</option><option value='125'>Macau</option><option value='126'>Macedonia, The Former Yugoslav Republic of</option><option value='127'>Madagascar</option><option value='128'>Malawi</option><option value='129'>Malaysia</option><option value='130'>Maldives</option><option value='131'>Mali</option><option value='132'>Malta</option><option value='133'>Marshall Islands</option><option value='134'>Martinique</option><option value='135'>Mauritania</option><option value='136'>Mauritius</option><option value='137'>Mayotte</option><option value='138'>Mexico</option><option value='139'>Micronesia, Federated States of</option><option value='140'>Moldova, Republic of</option><option value='141'>Monaco</option><option value='142'>Mongolia</option><option value='143'>Montserrat</option><option value='144'>Morocco</option><option value='145'>Mozambique</option><option value='146'>Myanmar</option><option value='147'>Namibia</option><option value='148'>Nauru</option><option value='149'>Nepal</option><option value='150'>Netherlands</option><option value='151'>Netherlands Antilles</option><option value='152'>New Caledonia</option><option value='153'>New Zealand</option><option value='154'>Nicaragua</option><option value='155'>Niger</option><option value='156'>Nigeria</option><option value='157'>Niue</option><option value='158'>Norfolk Island</option><option value='159'>Northern Mariana Islands</option><option value='160'>Norway</option><option value='161'>Oman</option><option value='162'>Pakistan</option><option value='163'>Palau</option><option value='164'>Panama</option><option value='165'>Papua New Guinea</option><option value='166'>Paraguay</option><option value='167'>Peru</option><option value='168'>Philippines</option><option value='169'>Pitcairn</option><option value='170'>Poland</option><option value='171'>Portugal</option><option value='172'>Puerto Rico</option><option value='173'>Qatar</option><option value='174'>Reunion</option><option value='175'>Romania</option><option value='176'>Russian Federation</option><option value='177'>Rwanda</option><option value='178'>Saint Kitts and Nevis</option><option value='179'>Saint Lucia</option><option value='180'>Saint Vincent and the Grenadines</option><option value='181'>Samoa</option><option value='182'>San Marino</option><option value='183'>Sao Tome and Principe</option><option value='184'>Saudi Arabia</option><option value='185'>Senegal</option><option value='186'>Seychelles</option><option value='187'>Sierra Leone</option><option value='188'>Singapore</option><option value='189'>Slovakia (Slovak Republic)</option><option value='190'>Slovenia</option><option value='191'>Solomon Islands</option><option value='192'>Somalia</option><option value='193'>South Africa</option><option value='194'>South Georgia and the South Sandwich Islands</option><option value='195'>Spain</option><option value='196'>Sri Lanka</option><option value='197'>St. Helena</option><option value='198'>St. Pierre and Miquelon</option><option value='199'>Sudan</option><option value='200'>Suriname</option><option value='201'>Svalbard and Jan Mayen Islands</option><option value='202'>Swaziland</option><option value='203'>Sweden</option><option value='204'>Switzerland</option><option value='205'>Syrian Arab Republic</option><option value='206'>Taiwan</option><option value='207'>Tajikistan</option><option value='208'>Tanzania, United Republic of</option><option value='209'>Thailand</option><option value='210'>Togo</option><option value='211'>Tokelau</option><option value='212'>Tonga</option><option value='213'>Trinidad and Tobago</option><option value='214'>Tunisia</option><option value='215'>Turkey</option><option value='216'>Turkmenistan</option><option value='217'>Turks and Caicos Islands</option><option value='218'>Tuvalu</option><option value='219'>Uganda</option><option value='220'>Ukraine</option><option value='221'>United Arab Emirates</option><option value='222'>United Kingdom</option><option value='223'>United States</option><option value='224'>United States Minor Outlying Islands</option><option value='225'>Uruguay</option><option value='226'>Uzbekistan</option><option value='227'>Vanuatu</option><option value='228'>Vatican City State (Holy See)</option><option value='229'>Venezuela</option><option value='230'>Viet Nam</option><option value='231'>Virgin Islands (British)</option><option value='232'>Virgin Islands (U.S.)</option><option value='233'>Wallis and Futuna Islands</option><option value='234'>Western Sahara</option><option value='235'>Yemen</option><option value='236'>Yugoslavia</option><option value='237'>Zaire</option><option value='238'>Zambia</option><option value='239'>Zimbabwe</option><option value='240'>Aaland Islands</option>
                        </select>

                        <span id="country_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="address1_lbl" for="address1" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Address</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="address1" class="form-control " value="<?php echo($_REQUEST["address1"]); ?>" id="address1" size="30" maxlength="50">

                        <span id="address1_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="city_lbl" for="city" class="col-md-12 col-sm-12 col-xs-12  control-label-align">City</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="city" class="form-control " value="<?php echo($_REQUEST["city"]); ?>" id="city" size="30"  maxlength="20">

                        <span id="city_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="state_lbl" for="stateList" class="col-md-12 col-sm-12 col-xs-12  control-label-align">State or Province</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <select name="stateList"  class="form-control  text-left" id="stateList"  onchange="stateChanged(this.value);">
                            <option>Select State</option>
                            <option  value="XX">Other...</option>
                            <option  value="AK">Alaska</option>
                            <option  value="AL">Alabama</option>
                            <option  value="AR">Arkansas</option>
                            <option  value="AZ">Arizona</option>
                            <option  value="CA">California</option>
                            <option  value="CO">Colorado</option>
                            <option  value="CT">Connecticut</option>
                            <option  value="DE">Delaware</option>
                            <option  value="FL">Florida</option>
                            <option  value="GA">Georgia</option>
                            <option  value="GU">Guam</option>
                            <option  value="HI">Hawaii</option>
                            <option  value="IA">Iowa</option>
                            <option  value="ID">Idaho</option>
                            <option  value="IL">Illinois</option>
                            <option  value="IN">Indiana</option>
                            <option  value="KS">Kansas</option>
                            <option  value="KY">Kentucky</option>
                            <option  value="LA">Louisiana</option>
                            <option  value="MA">Massachusetts</option>
                            <option  value="MD">Maryland</option>
                            <option  value="ME">Maine</option>
                            <option  value="MI">Michigan</option>
                            <option  value="MN">Minnesota</option>
                            <option  value="MO">Missouri</option>
                            <option  value="MS">Mississippi</option>
                            <option  value="MT">Montana</option>
                            <option  value="NC">North Carolina</option>
                            <option  value="ND">North Dakota</option>
                            <option  value="NE">Nebraska</option>
                            <option  value="NH">New Hampshire</option>
                            <option  value="NJ">New Jersey</option>
                            <option  value="NM">New Mexico</option>
                            <option  value="NV">Nevada</option>
                            <option  value="NY">New York</option>
                            <option  value="OH">Ohio</option>
                            <option  value="OK">Oklahoma</option>
                            <option  value="OR">Oregon</option>
                            <option  value="PA">Pennsylvania</option>
                            <option  value="PR">Puerto Rico</option>
                            <option  value="RI">Rhode Island</option>
                            <option  value="SC">South Carolina</option>
                            <option  value="SD">South Dakota</option>
                            <option  value="TN">Tennessee</option>
                            <option  value="TX">Texas</option>
                            <option  value="UT">Utah</option>
                            <option  value="VI">Virgin Islands</option>
                            <option  value="VT">Vermont</option>
                            <option  value="VA">Virginia</option>
                            <option  value="WA">Washington</option>
                            <option  value="DC">Washington D.C.</option>
                            <option  value="WI">Wisconsin</option>
                            <option  value="WV">West Virginia</option>
                            <option  value="WY">Wyoming</option>
                            <option  value="XX">Other</option>
                        </select>
                        <input name="state" class="form-control " value="<?php echo($_REQUEST["state"]); ?>" style="display:none" size="30" type="text" id="state" />
                        <select name="stateListCa" class="form-control  text-left" id="stateListCa" style="display:none" onchange="stateChanged(this.value);" >
                            <option value="">Select province</option>
                            <?php
                            foreach ($canada_provinces as $key => $province) {
                                ?>
                                    <option value="<?php echo($province); ?>"><?php echo($key); ?></option>
                                <?php
                            }
                            ?>
                        </select>

                        <span id="state_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="zipcode_lbl" for="zipcode" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Zip/Postal Code</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="zipcode" class="form-control " value="<?php echo($_REQUEST["zipcode"]); ?>" id="zipcode" size="30"  maxlength="10">

                        <span id="zipcode_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="phone_lbl" for="phone" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Phone</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="phone" class="form-control " value="<?php echo($_REQUEST["phone"]); ?>" id="phone" size="30"  maxlength="25">
                        <span id="phone_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>

                <div class="form-group">
                    <label id="mobile_phone_lbl" for="mobile_phone" class="col-md-12 col-sm-12 col-xs-12  control-label-align">Mobile Phone</label>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" name="mobile_phone" class="form-control " value="<?php echo($_REQUEST["mobile_phone"]); ?>" id="mobile_phone" size="30"  maxlength="25">

                        <span id="mobile_phone_lbl_error" class="lblErr center-block "></span>
                    </div>
                </div>
                 <hr/>

<?php
if ($GLOBALS['SubscriptionDNA']['Settings']['Extra'] == "1") {
    ?>



                    <h3>Additional Information</h3>

    <?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	Adding custom fields.
////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $required_fields = array();
    foreach ($customFields as $customField) {
        $caption = $customField->caption;
        $type = $customField->type;
        $name = $customField->name;
        $ac_name = substr($customField->name, 3);
        $default_value = $customField->default_value;
        $required = $customField->required;
        if ($required == "1") {
            $required_fields[] = $name;
        }
        $value = $_POST[$name];
        if ($name) {
            echo '<div class="form-group"><label id="' . $name . '_lbl" for="' . $name . '" class="col-md-12 col-sm-12 col-xs-12  control-label-align">' . ucfirst($caption) . '</label><div class="col-md-12 col-sm-12 col-xs-12">';
            if ($type == 'text') {
                $text_val = (empty($value)) ? $default_value : $value;
                echo '<input type="text" class="form-control " name="custom_fields[' . $ac_name . ']" field="' . $caption . '" id="' . $name . '" value="' . htmlentities($text_val) . '" >';

            }

            if ($type == 'checkbox') {

                if ($default_value) {
                    $checkbox_list = explode("\n", $default_value);
                    $selected_value_list = $value;


                    for ($j = 0; $j < count($checkbox_list); $j++) {
                        $selected_val = '';
                        for ($k = 0; $k < count($selected_value_list); $k++) {
                            if ($checkbox_list[$j] == $selected_value_list[$k]) {
                                $selected_val = "checked";
                                break;
                            }
                        }

                        echo "<input style='width: 15px' name='custom_fields[" . $ac_name . "][]" . "'  field='" . $caption . "'  type='" . $type . "' id='" . $name . "' " . $selected_val . " value='" . $checkbox_list[$j] . "' /> " . $checkbox_list[$j] . " ";

                    }

                } else {
                    echo "<input style='width: 15px' name='custom_fields[" . $ac_name . "][]" . "' field='" . $caption . "' id='" . $name . "' type='" . $type . "' value='" . $value . "' />";

                }
            }

            if ($type == 'radio') {
                if ($default_value) {
                    $radio_list = explode("\n", $default_value);

                    for ($j = 0; $j < count($radio_list); $j++) {
                        if ($value == $radio_list[$j])
                            $sel = "checked";
                        else
                            $sel = '';

                        echo "<input style='width: 15px' name='custom_fields[" . $ac_name . "]' field='" . $caption . "' type='" . $type . "' id='" . $name . "' " . $sel . " type='" . $type . "' value='" . $radio_list[$j] . "' /> " . $radio_list[$j] . "  ";

                    }

                }
                else {
                    echo "<input style='width: 15px' name='custom_fields[" . $ac_name . "]' field='" . $caption . "' id='" . $name . "' type='" . $type . "' value='" . $value . "' />";

                }
            }

            if ($type == 'textarea') {
                echo '<textarea name="custom_fields[' . $ac_name . ']" field="' . $caption . '"  id="' . $name . '" class="form-control " >' . htmlentities($value) . '</textarea>';
            }

            if ($type == 'select') {
                if ($default_value) {
                    $value_list = explode("\n", $default_value);

                    echo "<select name='custom_fields[" . $ac_name . "]' field='" . $caption . "' id='" . $name . "' class='form-control '>";
                    for ($j = 0; $j < count($value_list); $j++) {
                        if ($value_list[$j] == $value)
                            echo "<option selected value='" . $value_list[$j] . "'>" . $value_list[$j] . "</option>";
                        else
                            echo "<option value='" . $value_list[$j] . "'>" . $value_list[$j] . "</option>";
                    }
                    echo "</select>";
                }
            }

            if ($type == 'multi_select') {
                if ($default_value) {
                    $multiselect_list = explode("\n", $default_value);
                    $selected_value_list = explode(",", $value);

                    echo "<select name='custom_fields[" . $ac_name . "][]' field='" . $caption . "' multiple id='" . $name . "' class='form-control '>";

                    for ($j = 0; $j < count($multiselect_list); $j++) {
                        $selected_val = '';
                        for ($k = 0; $k < count($selected_value_list); $k++) {
                            if ($multiselect_list[$j] == $selected_value_list[$k]) {
                                $selected_val = "selected";
                                break;
                            }
                        }

                        //echo 'option '.$selected_val.' value="'.$multiselect_list[$j].'">'.$multiselect_list[$j].'option' . "<br>";
                        echo '<option ' . $selected_val . ' value="' . $multiselect_list[$j] . '">' . $multiselect_list[$j] . '</option>';
                    }

                    echo "</select>";
                }
            }

            echo "<span id='" . $name . "_lbl_error' class='lblErr center-block '></span></div></div>";
        }
    }
    ?>
    <hr/>
<?php
    }
?>

         <input type="hidden" name="required_fields" id="required_fields" value="<?php echo(implode(",", $required_fields)); ?>" />


        <h3>Referred By</h3>

                <div id="left-select-list" class="col-sm-6">
                    <div class="form-group">
                        <label id="how_referred_member" for="how_referred" class="col-md-12 control-label-align">Current member</label>
                        <div  class="col-md-12">
                            <input type="text" name="how_referred" id="how_referred" value="<?php echo($_REQUEST["ref"]); ?>" class="form-control " maxlength="22" onkeydown="if (this.value.length > 1) {
                                        document.getElementById('how_referred_list').value = '';
                                        document.getElementById('how_referred_list').disabled = true;
                                        document.getElementById('how_referred_other').className = 'col-md-12 control-label-align how_referred_other';
                                    } else {
                                        document.getElementById('how_referred_list').disabled = false;
                                        document.getElementById('how_referred_other').className = 'col-md-12 control-label-align';
                                    }"
                                    onchange="if (this.value == '') {
                                                document.getElementById('how_referred_list').disabled = false;
                                                document.getElementById('how_referred_other').className = 'col-md-12 control-label-align';
                                            } else {
                                                document.getElementById('how_referred_list').value = '';
                                                document.getElementById('how_referred_list').disabled = true;
                                                document.getElementById('how_referred_other').className = 'col-md-12 control-label-align how_referred_other';
                                            }" size="30" />
                            <span id="how_referred_lbl_error" class="lblErr center-block "></span>
                        </div>
                    </div>
                </div> <!--left-select-list div end-->

                <div  id="right-select-list" class="col-sm-6">
                    <div class="form-group">
                        <label id="how_referred_other" for="how_referred" class="col-md-12 control-label-align ">Or select from dropdown</label>
                        <div id="right-select-list" class="col-md-12">
                            <select name="how_referred" id="how_referred_list"  class="form-control  text-left"
                             onchange="if (this.value == '') {
                                        document.getElementById('how_referred_member').className = 'col-md-12 control-label-align';
                                    } else {
                                        document.getElementById('how_referred_member').className = 'col-md-12 control-label-align how_referred_other';
                                    }" >
                                <option >Select Other</option>
                                <option value="Magazine">Magazine</option>
                                <option value="TV">Television</option>
                                <option value="Google">Google search</option>
                                <option value="Yahoo">Yahoo search</option>
                                <option value="Youtube">YouTube</option>
                            </select>
                        </div>
                    </div>
                </div> <!--right-select-list div end-->

                <label id="agree_lbl" for="agree" class="col-md-12 control-label-align" style="padding-bottom: 20px;padding-left: 0;">
                        <input style="width: 15px;" type="checkbox" name="agree" id="agree" value="0" <?php if ($_REQUEST["agree"] == "1") echo("checked"); ?> />
                        I have read and agree to all the Terms and Conditions
                    </label>

                    <span id="agree_lbl_error" class="lblErr center-block "></span>
                    <br />
                    <br />

                <div id="msgProcessing" style="display:none;" ><h4>Processing your request please wait...</h4></div>
                <div class="form-group">
                    <div class="col-md-12">
                        <input name="x_submit" value="Click here to submit form" type="submit" class="btn btn-default btn-block " onclick="return checkForm(this.form);"/>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="center-block text-center">
                        <i>Processing may take a few seconds, afterwards you will be able to login instantly.<br/>You will receive a confirmation email.</i><br/><br/>
                    </div>
                </div>
                <br/><br/>
        </form> <!--Bootstrap form end-->
</div> <!-- outer shadow div end-->


                <script>
                    function validateSubscription()
                    {
                        return(document.getElementById("selected_package").value);
                    }
                    if ("<?php echo($selected_package); ?>" != "")
                        packageChanged(document.getElementById("innerDiv_<?php echo($selected); ?>"), "<?php echo($selected_package); ?>", "<?php echo($sel_payment_info_not_required); ?>");
                </script>
<?php
if ($_POST) {
    ?>
                    <script>
                        document.getElementById("country").value = "<?php echo($_REQUEST["country"]); ?>";
                        document.getElementById("stateList").value = "<?php echo($_REQUEST["stateList"]); ?>";
                        document.getElementById("how_referred_list").value = "<?php echo($_REQUEST["how_referred_list"]); ?>";
                        document.getElementById("cc_type").value = "<?php echo($_REQUEST["cc_type"]); ?>";
                        document.getElementById("cc_exp_month").value = "<?php echo($_REQUEST["cc_exp_month"]); ?>";
                        document.getElementById("cc_exp_year").value = "<?php echo($_REQUEST["cc_exp_year"]); ?>";
                    </script>

    <?php
}
?>

