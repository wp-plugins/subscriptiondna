<?php
if(isset($_REQUEST["x_submit"]))
{
    if(!isset($_REQUEST["cc_on_file"]))
        $_REQUEST["card_id"]="";
    $data=array(
        "service_id"=>"c03f4ec2-7ca9-11e0-b9e9-001372fb8066",
        "billing_routine_id"=>$_REQUEST["billing_routine_id"],
        "sender_email"=>$_REQUEST["sender_email"],
        "sender_fname"=>$_REQUEST["sender_fname"],
        "sender_lname"=>$_REQUEST["sender_lname"],
        "first_name"=>$_REQUEST["first_name"],
        "last_name"=>$_REQUEST["last_name"],
        "email"=>$_REQUEST["email"],
        "send_recipient_email_on"=>$_REQUEST["send_recipient_email_on"],
        "cc_name"=>$_REQUEST["cc_name"],
        "cc_type"=>$_REQUEST["cc_type"],
        "cc_number"=>$_REQUEST["cc_number"],
        "cc_exp_month"=>$_REQUEST["cc_exp_month"],
        "cc_exp_year"=>$_REQUEST["cc_exp_year"],
        "cc_cvv"=>$_REQUEST["cc_cvv"],
        "country"=>$_REQUEST["country"],
        "address"=>$_REQUEST["address"],
        "city"=>$_REQUEST["city"],
        "state"=>$_REQUEST["state"],
        "zipcode"=>$_REQUEST["zipcode"],
        "check_mo"=>$_REQUEST["check_mo"],
        "custom_comment"=>$_REQUEST["custom_comment"],
        "login_name"=>$_SESSION['login_name'],
        "card_id"=>$_REQUEST["card_id"]        
    );
    $result = SubscriptionDNA_ProcessRequest($data,"subscription/gift",true);
    if($result["errCode"]<0)
    {
        $msg='<font color="#00FF00">'.$result["errDesc"].'</font>';
    }
    else
    {
        ?>
        <script>
        location.href='/gift/subscription-confirmation';
        </script>
        <?php
        die();
    }
}
$packages = SubscriptionDNA_ProcessRequest("","list/packages",true);

if (isset($_POST["ValidateCode"]))
{
    list($service, $billing) = explode(";", $_POST["packages"][0]);
    $data=array("promo_code"=>$_POST["promo_code"],"services"=>$service,"billing_routine_id"=>$billing);
    $promocode = SubscriptionDNA_ProcessRequest($data,"subscription/validate_promocode",true);

    if ($promocode["errCode"] < 0)
    {
        $message = $promocode["errDesc"] . '';
    }
    else
    {
        if ($promocode["discount_mod"] == "%")
            $message = 'Your code is valid. You save ' . $promocode["discount"] . $promocode["discount_mod"] . '';
        if ($promocode["discount_mod"] == "b")
            $message = 'Your code is valid. New Billing is :' . $promocode["billing"];
        else
            $message = 'Your code is valid. You save $' . $promocode["discount"] . '';
    }
}

$ccinfo = false;
$login_name = $_SESSION['login_name'];

if ($login_name != "")
{

    $ccList = SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name),"creditcard/list",true);;
    if (count($ccList) > 0)
    {
        $ccinfo = true;
        $ccdetail = $ccList[0];
    }
    if (@$_REQUEST["sender_fname"] == "")
    {
        $profile = SubscriptionDNA_ProcessRequest(array("login_name"=>$_SESSION['login_name']),"user/profile",true);
        $_REQUEST["sender_fname"] = $profile["first_name"];
        $_REQUEST["sender_lname"] = $profile["last_name"];
        $_REQUEST["sender_email"] = $profile["email"];
    }
}
?>



<script LANGUAGE="JavaScript">
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
            obj.className = 'noErr';
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
            obj.className = 'err';
        }
        return(validated);
    }

    function checkForm(f) {
        if (f.frm_skip_validation.value == "1")
            return(true);

        
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

<div align="center" id="DNAFormFields">
    <div class="required">
<?php
if ($_POST && $msg)
{
    echo($msg);
}
?>
    </div>

    <form method="post" name="customSubscribeForm" id="customSubscribeForm" action="" onSubmit="return checkForm(this);">
        <input type='hidden' name='frm_skip_validation' value='1'>

        <div id="x_sid_01_lbl_error" class="lblErr"></div>

        <table>
            <tr valign=top>
                <td colspan="3"><h2>Select a Gift Subscription Plan:</h2></td>
            </tr>

            <tr valign=top>            
                <td colspan="3">



                    <div style="border: solid 1px gray; padding: 4px; padding-top: 10px; padding-right: 10px;">

<?php
$count = 0;
foreach ($packages as $package)
{
    if (in_array($package["service_id"] . ";" . $package["billing_routine_id"], $_POST["packages"]))
        $package["defaultval"] = "Yes";
    if ($package["defaultval"] == "Yes")
        $current_biling = $package["billing_routine_id"];
    ?>
                            <div id="innerDiv">
                                <strong><input style="width: 15px;" type="radio" name="packages[]" id="packages_<?php echo($count); ?>"  value="<?php echo($package["billing_routine_id"]); ?>" <?php if ($package["defaultVal"] == "Yes") echo("checked"); ?>  onclick="document.getElementById('billing_routine_id').value = this.value;"><?php echo($package["package_name"]); ?></strong>
                                <div style="margin-left:20px;"><?php echo($package["package_description"]); ?></div><br>
                            </div>
    <?php
    $count++;
}
?>
                        <span id="package_lbl_error" class="lblErr"></span>

                        <input type="hidden" name="package" value="" id="package" />
                        <input type='hidden' name='billing_routine_id' id="billing_routine_id" value="<?php echo($current_biling); ?>">

                    </div>

                </td>
            </tr>

            <script>
                function validateSubscription()
                {
                    for (i = 0; i <<?php echo($count); ?>; i++)
                    {
                        if (document.getElementById('packages_' + i).checked)
                            return(true);
                    }
                    return(false);
                }
            </script>
            <tr><td colspan="3">


                    <p>
                        <span id="error2" class="lblErr"><?php echo($message); ?></span>
                </td></tr>



            <tr>
                <td valign="top">

                    <table border="0" cellpadding="3" cellspacing="2" id="dna-gift">
                        <tr>
                            <td colspan="2"><h2>Gift Recipient Information</h2></td>
                        </tr>

                        <tr>
                            <td style="text-align: right;"><span id="first_name_lbl" class="lbl">First Name</span></td>
                            <td width="200"><input TYPE="TEXT" NAME="first_name" id="first_name" value="<?php echo($_REQUEST["first_name"]); ?>" style="width:175px; padding-left: 4px;" size="30" class="noErr" MAXLENGTH="50"></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td nowrap><span id="first_name_lbl_error" class="lblErr"></span></td>
                        </tr>

                        <tr>
                            <td style="text-align: right;"><span id="last_name_lbl" class="lbl">Last Name</span></td>
                            <td><input TYPE="TEXT" NAME="last_name" id="last_name" value="<?php echo($_REQUEST["last_name"]); ?>" style="width:175px; padding-left: 4px;" size="30" class="noErr" MAXLENGTH="50"></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><span id="last_name_lbl_error" class="lblErr"></span></td>
                        </tr>

                        <tr>
                            <td style="text-align: right;"><span id="email_lbl" class="lbl">Recipient Email</span></td>
                            <td><input TYPE="TEXT" NAME="email" id="email" value="<?php echo($_REQUEST["email"]); ?>"  style="width:175px; padding-left: 4px;" size="30" class="noErr" MAXLENGTH="100"></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><span id="email_lbl_error" class="lblErr"></span></td>
                        </tr>

                        <tr>
                            <td style="text-align: right;"><nobr><span id="email2_lbl" class="lbl">(Re-enter Email)</i></span></nobr></td>
                <td><input TYPE="TEXT" NAME="email2" id="email2" value="<?php echo($_REQUEST["email2"]); ?>" style="width:175px; padding-left: 4px;" size="30" class="noErr" MAXLENGTH="100"></td>
            </tr>

            <tr>
                <td></td>
                <td><span id="email2_lbl_error" class="lblErr"></span></td>
            </tr>

            <tr> 
                <td style="text-align: right;"><span id="send_recipient_email_on_lbl" class="lbl">Gift Delivery Date</span></td> 
                <td nowrap>
                    <input type="hidden" name="today" value="<?php echo(date('m/d/Y')); ?>"/>
                    <input TYPE="TEXT" NAME="send_recipient_email_on" id="send_recipient_email_on"  style="width:140px; padding-left: 4px;" size="20" class="noErr" MAXLENGTH="100">   

                </td>
            </tr>

            <tr>
                <td></td> 
                <td><span id="send_recipient_email_on_lbl_error" class="lblErr"></span></td> 
            </tr>


            <tr>
                <td style="text-align: right; vertical-align: top;"><span class="lbl">Type a message to be sent<br />with your gift subscription.</span></td>
                <td><textarea NAME="custom_comment" style="width:175px; padding-left: 4px;" size="30" rows="5"><?php echo($_REQUEST["custom_comment"]); ?></textarea></td>
            </tr>

        </table>



        </td>
        <td valign="top" style="padding-left: 40px;">


            <table border="0" cellpadding="3" cellspacing="2" id="dna-gift">
                <tr><td colspan="3"><h2>Your Payment Information</h2></td></tr>
<?php
if ($ccinfo)
{
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
                    <tr valign=top>
                        <td colspan="3">
                            <input type='checkbox' name='cc_on_file' id="cc_on_file" <?php if ($_POST["cc_on_file"] == "1" or $ccinfo) echo("checked"); ?> value='1' onclick="hideShowCCInfo(this.checked);">Use Existing Credit Card<br>
                        </td>
                    </tr>	
                    <tr valign=top id="existingCCInfo">
                        <td>Select Card:</td>
                        <td colspan="2">
                            <select name="card_id" id="card_id">
                                <?php
                                foreach ($ccList as $ccdetail)
                                {
                                    ?>
                                    <option value="<?php echo($ccdetail["ccid"]); ?>"><?= $ccdetail["card_number"] ?> | <?= $ccdetail["expire_date"] ?> | <?= $ccdetail["card_type"] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
    <?php
    $display = "none";
}
?>
                <tr>
                    <td style="text-align: right;"><span id="sender_fname_lbl" class="lbl">Your First Name</span></td>
                    <td><input TYPE="TEXT" NAME="sender_fname" id="sender_fname" value="<?php echo($_REQUEST["sender_fname"]); ?>"  style="width:175px; padding-left: 4px;" size="30" maxlength="100" class="noErr"></td>
                </tr>

                <tr>
                    <td></td>
                    <td><span id="sender_fname_lbl_error" class="lblErr"></span></td>
                </tr>

                <tr>
                    <td style="text-align: right;"><span id="sender_lname_lbl" class="lbl">Your Last Name</span></td>
                    <td><input TYPE="TEXT" NAME="sender_lname" id="sender_lname" value="<?php echo($_REQUEST["sender_lname"]); ?>"  style="width:175px; padding-left: 4px;" size="30" maxlength="100" class="noErr"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><span id="sender_lname_lbl_error" class="lblErr"></span></td>
                </tr>

                <tr>
                    <td style="text-align: right;"><span id="sender_email_lbl" class="lbl">Your Email</span></td>
                    <td><input TYPE="TEXT" NAME="sender_email" id="sender_email" value="<?php echo($_REQUEST["sender_email"]); ?>"  style="width:175px; padding-left: 4px;" size="30" maxlength="100" class="noErr"></td>
                </tr>

                <tr>
                    <td></td>
                    <td><span id="sender_email_lbl_error" class="lblErr"></span></td>
                </tr>

                <tr id="trCCInfo1" style="display:<?php echo($display); ?>">
                    <td style="text-align: right;"><span id="cc_name_lbl" class="lbl">Name on Card</span></td>
                    <td><input TYPE="TEXT" NAME="cc_name" id="cc_name" value="<?php echo($_REQUEST["cc_name"]); ?>"  style="width:175px; padding-left: 4px;" size="30" maxlength="100" class="noErr"></td>
                </tr>

                <tr>
                    <td></td>
                    <td><span id="cc_name_lbl_error" class="lblErr"></span></td>
                </tr>

                <tr id="trCCInfo2" style="display:<?php echo($display); ?>">
                    <td style="text-align: right;"><span id="cc_type_lbl" class="lbl">Credit card type</span></td>
                    <td><select class="noErr" name="cc_type" id="cc_type">
                            <option></option>
                            <option value='MasterCard' >MasterCard</option><option value='Visa' >Visa</option><option value='Discover' >Discover</option></select></td>
                </tr>

                <tr>
                    <td></td>
                    <td><span id="cc_type_lbl_error" class="lblErr"></span></td>
                </tr>

                <tr id="trCCInfo3" style="display:<?php echo($display); ?>">
                    <td style="text-align: right;"><span id="cc_number_lbl" class="lbl">Credit card number</span></td>
                    <td><input TYPE="TEXT" NAME="cc_number" id="cc_number"  style="width:175px; padding-left: 4px;" size="30" maxlength="16" class="noErr"></td>
                </tr>

                <tr>
                    <td></td>
                    <td><span id="cc_number_lbl_error" class="lblErr"></span></td>
                </tr>

                <tr id="trCCInfo4" style="display:<?php echo($display); ?>">
                    <td style="text-align: right;"><span id="cc_exp_month_lbl" class="lbl">Card expiration month</span></td>
                    <td style="text-align: left; padding-left:0px;">

                        <table cellpadding="0" cellspacing="0" style="margin:0px;"><tr>
                                <td style="text-align: left;">
                                    <select NAME="cc_exp_month" id="cc_exp_month"  class="noErr" style="width: 80px; margin: 0px;">
                                        <option></option>
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
                                    </select></td>
                                <td style="padding-left: 12px;">
                                    <span id="cc_exp_year_lbl" class="lbl">year</span>&nbsp;</td>
                                <td><select NAME="cc_exp_year" id="cc_exp_year"  class="noErr" style="width: 50px;">
                                        <option></option>
                                        <?php
                                        $year=date("Y");
                                        for($i=$year;$i<=$year+9;$i++)
                                        {
                                            ?><option value='<?php echo(substr($i,2)); ?>'><?php echo($i); ?></option><?php
                                        }
                                        ?>
                                    </select></td>
                            </tr>
                        </table></td>
                </tr>

                <tr>
                    <td></td>
                    <td><span id="cc_exp_month_lbl_error" class="lblErr"></span> &nbsp; <span id="cc_exp_year_lbl_error" class="lblErr"></span></td>
                </tr>

                <tr id="trCCInfo5" style="display:<?php echo($display); ?>">
                    <td style="text-align: right;">CVC Code:</td>
                    <td><input name="cc_cvv" id="cc_cvv" size="5" maxlength="3" value="" type="text" style="width: 50px;"></td>
                </tr>





                <tr>
                    <td></td>
                    <td><br><br>
                        <input TYPE="submit" name="x_submit" VALUE="Click here to submit form" onclick="this.form.frm_skip_validation.value = '0';" class="noErr"></td>
                </tr>
            </table>

        </td>
        </tr>
        </table>

    </form>

    <br><br><br>
    <div style="text-align: center;">
        <i>Processing may take a few seconds, and a paid receipt will be sent to you by e-mail.</i>
    </div>

</div>

<?php
if ($_POST)
{
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

