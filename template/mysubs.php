<?php
//wp_enqueue_script("jquery");
?>
<link rel="stylesheet" href="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/lib/alertify.core.css" />
<script src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/lib/alertify.min.js"></script>
<script>

    function dnaAskConfimation(btn1,btn2,message,target_url)
    {
        alertify.set({ labels: { ok: btn1, cancel: btn2 } });
        alertify.confirm(message, function (e) {
                if (e) {
                        location.href=target_url;
                } else {
                        return;
                }
        });
    }
</script>
<?php

    echo stripcslashes(urldecode($_REQUEST["msg"]));
	$login_name = $_SESSION['login_name'];
	$alreadySigned=array();
        if(isset($_REQUEST["SubscribeAPI"]))
        {
            $paid_by_credit_card=1;
            $check_mo=0;
            if($_POST["payment_method"]=="1")
                $existing_credit_card="1";//$_POST["ccid"];
            else if($_POST["payment_method"]=="2")
            {
                $existing_credit_card="";
                $_REQUEST["ccid"]="";
            }
            else
            {
                $paid_by_credit_card="";
                $existing_credit_card="";
                $_REQUEST["ccid"]="";
                $check_mo=1;
            }
            list($service_id,$billing_routine_id)=explode(";",$_POST["package"]);
            $requestData = array(
                   'login_name' => $login_name,
                   'password' => $_SESSION['password'],
                   'service_id' => $service_id,
                   'billing_routine_id' => $billing_routine_id,
                   'paid_by_credit_card' => $paid_by_credit_card,
                   'existing_credit_card' => $existing_credit_card,
                   'card_id'=>$_REQUEST["ccid"],
                   'cc_name' => $_POST["cc_name"],
                   'cc_type' => $_POST["cc_type"],
                   'cc_number' => $_POST["cc_number"],
                   'cc_exp_month' => $_POST["cc_exp_month"],
                   'cc_exp_year' => $_POST["cc_exp_year"],
                   'cc_cvv' => $_POST["cc_cvv"],
                   'how_referred' => $how_referred,
                   'promo_code' => $_POST["promo_code"],
                   'check_mo' =>$check_mo,
                   'tax' => '',
                   'change'=>$_REQUEST["change"]
             );
            //print_r($requestData);die();
            $result = SubscriptionDNA_ProcessRequest($requestData,"service/subscribe",true);
            if($_REQUEST["stop_auto_bill"]=="1" && $result["sub_id"]!="")
            {
                $data=array("login_name"=>$login_name,"sub_id"=>$result["sub_id"], "status"=>"Discontinued");
                SubscriptionDNA_ProcessRequest($data,"subscription/change_status");
            }
            //print_r($result);
            $msg='<div class="alert alert-success" role="alert">'.$result["errDesc"].'</div>';

            ?>
            <script>
            location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>?msg=<?php echo(urlencode($msg)); ?>';
            </script>
            <?php
            die();

        }
	if($_REQUEST['status']){
		$data=array("login_name"=>$login_name,"sub_id"=>$_REQUEST['subId'], "status"=>$_REQUEST['status']);
		$result = SubscriptionDNA_ProcessRequest($data,"subscription/change_status");
        $msg='<div class="alert alert-success" role="alert">'.$result->errDesc.'</div>';
		?>
		<script>
        // location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>?msg=<?php echo(urlencode($result->errDesc)); ?>';
		location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>?msg=<?php echo(urlencode($msg)); ?>';
		</script>
		<?php
		die();


	}
	else if($_REQUEST['renew'])
	{
		if($_REQUEST['confirmation_page'] == 1 or $_REQUEST['confirmation_page'] == 21)
		{
                $data=array("sub_id"=>$_REQUEST['subId'], "confirmation_page"=>$_REQUEST['confirmation_page'], "login_name"=>$login_name);
			     $result = SubscriptionDNA_ProcessRequest($data,"subscription/renew");
                //print_r($result);

                $ccList = SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name),"creditcard/list");
                if(is_array($ccList) && count($ccList)>0)
                {
                    $ccinfo=true;
                }
                else
                {
                    $ccinfo=false;
                }

			?>


                <script type="text/javascript" src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/js/ccinfo.js"></script>
    


                <!--start bootstrap-->
                <div class="col-xs-12">
                <form method="post" class="form-horizontal form-border  text-left " action="?&subId=<?=$_REQUEST['subId']?>&renew=renew">
                <input type="hidden" name="card_id" value="<?php echo($_REQUEST["card_id"]); ?>">
                <input type="hidden" name="confirmation_page" value="<?php if ($_REQUEST["confirmation_page"] == "21"){echo("2");}else{echo("0");} ?>">
                <h3><?php if ($_REQUEST["confirmation_page"] == "21"){echo("Extend Your ");}else{echo("Renew Your Expired ");} ?>Subscription</h3>

                    <p><b>Name:</b>  <?=$result->first_name ?> <?=$result->last_name ?></p>
                    <p><b>Email:</b>  <?=$result->first_name ?> <?=$result->email ?></p>
                    <p><b>Login Name:</b>  <?=$result->login_name ?></p>


                <strong style="font-size:1.4em;">Subscription:</strong><br />
                        <p>
                        <?=$result->services ?>
                        <?=$result->billing_routine ?>
                        </p>

                        <p><b>Subscription Date:</b>  <?php echo($result->start_date); ?></p>
                        <p><b>Subscription Expiration:</b>  <?php echo($result->expiry_date); ?></p>
                        <?php
                        if ($_REQUEST["confirmation_page"] == "21")
                        {
                            ?>
                           <p><b>Extend To:</b>  <?php echo($result->extend_upto); ?></p>
                            <?php
                        }
                        else
                        {
                            ?>
                            <p><b>Renew Subscription To:</b>  <?php echo($result->renew_upto); ?></p>
                        <?php
                        }
                        ?>

                 <div class="form-group">
                    <label for="payment_method" class="col-md-12" >Payment Method</label>
                    <div class="col-md-12 pad-left-40" >
                        <label style="font-weight:500;"><input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="3") echo("checked"); ?> value='3' onclick="hideShowCCInfo(true);dnaPaymentMethodChanged(3);"> Check/Invoice</label><br />
                        <label style="font-weight:500;"><input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="2") echo("checked"); ?> value='2' onclick="hideShowCCInfo(false);dnaPaymentMethodChanged(2);"> Use New Credit Card</label><br />
                        <?php
                        if($ccinfo)
                        {
                        ?>
                            <label style="font-weight:500;"><input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="1" or ($ccinfo && $_POST["payment_method"]=="")) echo("checked"); ?> value='1' onclick="hideShowCCInfo(this.checked);dnaPaymentMethodChanged(1);"> Use Existing Credit Card</label><br />
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div id="trCheckInfo" class="well" style="display:none">
                   <b>Please make checks payable to:</b><br />
                   <b>My Company Name</b><br />
                   <b>123 Good Street</b><br />
                   <b>Cincinnati, OH 45248</b><br />

                    <br />
                    <small>
                    *Checks/money orders must be in US funds & drawn from a US bank.
                    <br />
                    *Returned checks are subject to a $20.00 return check fee.
                    </small>
                </div>
                    <?php
                    if($ccinfo)
                    {
                    ?>

                <div id="existingCCInfo">
                   <div class="form-group">
                        <label for="ccid" class="col-xs-12">Existing card:</label>
                        <div class="col-xs-12">
                                    <select name="ccid" class="form-control" id="ccid">
                                    <?php
                                    foreach($ccList as $ccdetail)
                                    {
                                            ?>
                                            <option value="<?php echo($ccdetail->ccid); ?>"><?=$ccdetail->card_number ?> | <?=$ccdetail->expire_date ?> | <?=$ccdetail->card_type ?></option>
                                            <?php
                                    }
                                    ?>
                                    </select>
                        </div>
                    </div>
                </div>
                    <?php
                    $display="none";
                    }
                    $result=array();
                    include 'cc_info.php';
                    ?>
                <div class="col-md-12" id="tr_no_auto_bill">
                    <div class="form-group">
                                Disable Auto-Renewal:
                                <label>
                                    <input type="checkbox" name="stop_auto_bill" id="stop_auto_bill" value="1" <?php if($_REQUEST["stop_auto_bill"]=="1")echo("checked"); ?>>
                                </label>
                    </div>
                </div>

                        <h3 id="msgProgress" style="display:none">Processing your request, Please wait..</h3>
                        <input type="submit" class="btn btn-default btn-block" onclick="this.style.display='none';document.getElementById('msgProgress').style.display='';" value="<?php if ($_REQUEST["confirmation_page"] == "21"){echo("Extend");}else{echo("Renew");} ?> Subscription" />

                </form>
                </div>
                <!--end bootstrap-->
	<?php
			exit;
		}
		else
		{
                    if(isset($_POST["confirmation_page"]))
                    {
                        if($_REQUEST["payment_method"]=="2")
                            $_REQUEST["paid_by_new_card"]=1;
                        else if($_REQUEST["payment_method"]=="3")
                        {
                            $_REQUEST["check_mo"]=1;
                            $_REQUEST["ccid"]="";
                        }
                        $data=array("sub_id"=>$_REQUEST['subId'],
                            "confirmation_page"=>$_REQUEST['confirmation_page'],
                            "login_name"=>$login_name,
                            "card_id"=>$_REQUEST["ccid"],
                            "paid_by_new_card"=>$_REQUEST["paid_by_new_card"],
                            "cc_name"=>$_REQUEST["cc_name"],
                            "cc_type"=>$_REQUEST["cc_type"],
                            "cc_number"=>$_REQUEST["cc_number"],
                            "cc_exp_month"=>$_REQUEST["cc_exp_month"],
                            "cc_exp_year"=>$_REQUEST["cc_exp_year"],
                            "cc_cvv"=>$_REQUEST["cc_cvv"],
                            "check_mo"=>$_REQUEST["check_mo"],
                            "description"=>""
                                );
			$result = SubscriptionDNA_ProcessRequest($data,"subscription/renew",true);

			if($result["errCode"]!=10){
				$msg='<div class="alert alert-danger" role="alert">'.$result["errDesc"].', Please try again</div>';
			}
                        else
                        {
                            if($_REQUEST["stop_auto_bill"]=="1")
                            {
                                $data=array("login_name"=>$login_name,"sub_id"=>$_REQUEST['subId'], "status"=>"Discontinued");
                                SubscriptionDNA_ProcessRequest($data,"subscription/change_status");
                            }
                            $msg='<div class="alert alert-success" role="alert">'.$result["errDesc"].'</div>';
			}
                        ?>
                        <script>
                        location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>?msg=<?php echo(urlencode($msg)); ?>';
                        </script>
                        <?php
                        die();
                    }

		}
	}
	else if($_REQUEST["update_cc"])
	{
                $data=array("login_name"=>$login_name,"sub_id"=>$_REQUEST['subId'], "card_id"=>$_REQUEST['update_cc']);
                $result = SubscriptionDNA_ProcessRequest($data,"subscription/update_payment_method",true);
		if($result["errCode"]!=7){
			$msg='<div class="alert alert-danger" role="alert">'.$result["errDesc"].', Please try again</div>';
		}else{
			$msg='<div class="alert alert-success" role="alert">thisone'.$result["errDesc"].'</div>';
		}
	}

	SubscriptionDNA_Update_Subscription();

	$ccList = SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name),"creditcard/list");
	if(is_array($ccList) && count($ccList)>0)
	{
		$ccinfo=true;
		$ccdetail=$ccList[0];
	}
	else
	{
		$ccinfo=false;
	}


	$subscriptions = SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name,"return_group_info"=>""),"subscription/list");;
        SubscriptionDNA_LoginCheck($subscriptions);

	if(count($subscriptions)<1)
	{
		echo '<div class="alert alert-danger" role="alert">No Subscription Found.</div>';
	}
	else
	{


	?>
    <div id="dna-subscriptions" class="col-xs-12" style="padding-left:5px;padding-right:5px;">
            <?php
            if($msg)
            {
            ?>
            <span class="text-center">
                <?php echo($msg); ?>
            </span>
            <?php
            }
            ?>
            <h3 >Subscription Details</h3>
    <?php
    $alreadySigned=array();

    foreach($subscriptions as $subscription)
    {
        if(!$subscription)
        break;
        if($subscription->status=='Active')
        $_SESSION['subscription']="1";
        if($subscription->status!="Unsubscribed" && $subscription->status!="Expired" && $subscription->status!="Pending")
        {
            $alreadySigned[]=$subscription->service_id;
            $existing_billing=$subscription->billing_id;
        }
        ?>
    <div class="well clearfix">
        <div class="visible-xs visible-sm" ><h5 style="padding-left:15px;"><u>Subscription</u></h5></div>
            <div class="col-md-6" style="padding-right:5px;">
                <b><?php echo $subscription->service_name; ?></b><br>
                <?php if ($subscription->service_description!="") { echo($subscription->service_description); echo "<br />"; } ?>
                <?php if ($subscription->billing_description!="") { echo "<i>"; echo($subscription->billing_description); echo "</i><br />"; } ?>
            </div>

            <div class="visible-xs visible-sm"><h5 style="padding-left:15px;"><u>Details</u></h5></div>
            <div class="col-md-6" style="padding-right:5px;">
                <b>Status:</b> <?php echo ($subscription->status=="Discontinued"?"Discontinued Auto-Billing":$subscription->status); ?><br />
                <b>Start / Expiration Dates:</b><br />
                <?php echo substr($subscription->subscription_date,0,10); ?> / <?php echo substr($subscription->expires,0,10); ?><br />

                <?php
                    if(trim($subscription->status)=='Discontinued' || $subscription->recurring!="1" || !$ccinfo)
                    {
                        echo "<a href='?&subId=" . $subscription->sub_id ."&card_id=".$subscription->ccid."&renew=renew&confirmation_page=21'>Extend</a>&nbsp; | &nbsp;";
                    }
                    if(trim($subscription->status)=='Active')
                    {
                        echo "<a href='?&change=" . $subscription->sub_id . "'>Change</a>&nbsp; | &nbsp;<a  onClick=\"dnaAskConfimation('Yes','No','Are you sure you want to Unsubscribe?','?&subId=" . $subscription->sub_id . "&status=Unsubscribed')\" href='#'>Unsubscribe</a>";
                        if($subscription->recurring=="1")
                        {
                            if($subscription->rebilling=="1")
                                echo("&nbsp; | &nbsp;<a onClick=\"dnaAskConfimation('Yes','No','Please confirm you want to Stop Auto-Billing.','?&subId=" . $subscription->sub_id . "&status=Discontinued')\"  href='#'>Stop Auto-Billing</a><br>");
                            else
                                echo("&nbsp; | &nbsp;<a   onClick=\"dnaAskConfimation('Yes','No','Please confirm you want to Start Auto-Billing.','?&subId=" . $subscription->sub_id . "&status=Active')\" href='#'>Start Auto-Billing</a><br>");
                        }
                    }
                    else if($subscription->status=='Discontinued')
                    {
                        echo "<a onClick=\"dnaAskConfimation('Yes','No','Are you sure you want to Unsubscribe?','?&subId=" . $subscription->sub_id . "&status=Unsubscribed')\" href='#'>Unsubscribe</a>";
                        if($subscription->recurring=="1")
                            echo("&nbsp; | &nbsp;<a   onClick=\"dnaAskConfimation('Yes','No','Please confirm you want to Start Auto-Billing.','?&subId=" . $subscription->sub_id . "&status=Active')\" href='#'>Start Auto-Billing</a><br>");
                    }
            ?>
            </div>
        </div>

<? } ?>
        </div>


            <!-- end  new -->

<?php
    /*
    ?>
    <p>
    <small><b>Please note that UNSUBSCRIBE will immediately terminate your subscription.  Therefore, please only UNSUBSCRIBE if you have decided for sure to permanently cancel the service. If you would like to cancel your subscription at the expiration of the current billing period (allowing you to finish your current subscription term), please click on DISCONTINUE. When you do so, you can change your mind until the expiration date by clicking on REACTIVATE to restore your subscription.</b></small>
    </p>
    <?php
    */
}

//old subscribe

$packages = SubscriptionDNA_ProcessRequest("","list/packages");
$login_name = $_SESSION['login_name'];
?>
<script>
function packageChanged(packob,package_id)
{
    jQuery("#package").val(package_id);
    if(jQuery(packob).hasClass("package-box"))
    {

        jQuery(".package-box-main").each(function() {

            jQuery(this).removeClass('package-box-active');
            jQuery(this).addClass('package-box');

        });
        jQuery(packob).removeClass('package-box');
        jQuery(packob).addClass("package-box-active")
    }



}
</script>
<div style="color:#990000;padding:15px;">
    <h4><?php echo($_POST["response"]); ?></h4>
</div>
<?php
if(count($packages)>0)
{
$newPackages=0;

	if(isset($_REQUEST["change"]))
	{
            $data=array("sub_id"=>$_REQUEST["change"],"login_name"=>$_SESSION['login_name']);
            $result = SubscriptionDNA_ProcessRequest($data,"subscription/get_credit_info",true);
	}

	if($result["remaining_amount"]>0)
	{
	?>
    <div class="col-xs-12">
	<fieldset>
	<legend ><h4>Existing Subscription Details</h4></legend>
	<div class="red">
	<b>Current subscription</b> to <?php echo($result["service_name"]);?> expires <?php echo($result["expires"]);?>
	<ul>
	<li><b>Service:</b> <?php echo($result["service_name"]);?><br></li>
	<?php
	if($result["routine_name"]!="")
	{
	?>
	<li><b>Billing Routine:</b> <?php echo($result["routine_name"]);?><br></li>
	<?php
	}
	?>
	</ul>
	<b>Last Invoice Payment:</b> $<?php echo($result["amount"]);?> was completed on <?php echo($result["invoice_date"]);?><br>
	<b>Total Credit:</b> = $<?php echo($result["remaining_amount"]);?> <br>
	<br>
	<b>Credit Discount</b> of $<?php echo($result["remaining_amount"]);?> will be applied toward your new subscription.<br>
	<br />
    </div>
	</fieldset>
	</div>
	<?php
	}
	else if($_REQUEST["change"]!="")
	{
		?>
			<b>Remaining Subscription Balance is $0.00
		<?php
	}
?>


<script type="text/javascript" src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/js/ccinfo.js"></script>
<script>
jQuery(document).ready(function () {


    var validatePromo = jQuery('#promo_code_lbl_error');
    jQuery('#promo_code').blur(function () {
        var t = this;
        if (this.value != this.lastValue && this.value!="") {
            if (this.timer) clearTimeout(this.timer);
            validatePromo.removeClass('error').html('<img src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna//images/loader.gif" height="16" width="16" />');
            this.timer = setTimeout(function () {
                jQuery.ajax({
                    url: '<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/?dna_validate=promo_code',
                    data: 'promo_code=' + t.value,
                    dataType: 'html',
                    type: 'post',
                    success: function (j) {
                        validatePromo.html(j);
                    }
                });
            }, 200);
            this.lastValue = this.value;
        }
    });

});
</script>


<!-- bootstrap start -->
<div id="DNAFormFields" class="col-xs-12" style="margin-bottom:50px;">
    <form name='customSubscribeForm' class='form-horizontal' action=''  method='POST'>
    <input type='hidden' name='SubscribeAPI' id="SubscribeAPI" value='Submit'>
                
    

<!-- new package list-->
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
<div id="packagesList1" >
     <input type='hidden' name='login_name' value='<?= $_SESSION['login_name']?>' />
        <input type='hidden' name='password' value='<?= $_SESSION['password']?>' />
        <input type="hidden"  value="<?php echo($_REQUEST["change"]); ?>" name="change" id="change" />
        <b>Subscription Plans:</b>
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
                    <div id="divPackageType<?php echo($key); ?>"  style="<?php if($key!=$selected_category && $nonempty>0){echo("display: none");} ?>">
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
                            <div title="Click to select your subscription plan."  id="innerDiv_<?php echo($package->uid); ?>"  class='package-box package-box-main' onclick='packageChanged(this, "<?php echo($package->service_id); ?>;<?php echo($package->billing_routine_id); ?>", "<?php echo($package->payment_info_not_required); ?>");
                                    displayTotal("<?php echo($package->cost); ?>");'>
                                <strong><?php echo($package->package_name); ?></strong>
                                <div ><?php echo($package->package_description); ?></div>
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

            <span id="package_lbl_error" class="lblErr center-block"></span>
            <input type="hidden" name="package" id='package' value="<?php echo($selected_package);  ?>" />
            <input type="hidden" name="packages[]" id='selected_package' value="<?php echo($selected_package); ?>" />
            <input type='hidden' name='selected_package_cost' id="selected_package_cost" value='<?php echo($sel_cost); ?>'>
</div>

    <!-- new package list-->
<br />
        <div class="form-group">
            <label for="promo_code" class="col-xs-12" >Promo Code:</label>
            <div class="col-xs-6">
                <input type="text" name="promo_code" id="promo_code" value="<?php echo(@$_REQUEST["promo_code"]); ?>"  size="30" class="noErr form-control" style="min-width:100px" maxlength="100" />
                <span id="promo_code_lbl_error" class="lblErr center-block text-center"><?php echo($code_msg); ?></span>
            </div>
            <div class="col-xs-6">
                <input type="submit" onclick="validate_promo();return false;" class="btn btn-default btn-block " name="ValidateCode" value="Validate Promocode" style="padding: 5px;" />
            </div>
        </div>

    <div class="form-group">
        <label for="payment_method" class="col-md-12" >Payment Method</label>
        <div class="col-md-12">
            <label style="font-weight:500;"><input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="3") echo("checked"); ?> value='3' onclick="hideShowCCInfo(true);dnaPaymentMethodChanged(3);"> Check/Invoice</label><br />
            <label style="font-weight:500;"><input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="2") echo("checked"); ?> value='2' onclick="hideShowCCInfo(false);dnaPaymentMethodChanged(2);"> Use New Credit Card</label><br />
            <?php
            if($ccinfo)
            {
            ?>
                <label style="font-weight:500;"><input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="1" or ($ccinfo && $_POST["payment_method"]=="")) echo("checked"); ?> value='1' onclick="hideShowCCInfo(this.checked);dnaPaymentMethodChanged(1);"> Use Existing Credit Card</label><br />
            <?php
            }
            ?>
        </div>
    </div>

    <div id="trCheckInfo" class="well" style="display:none">
            <b>Please make checks payable to:</b><br />
            <b>My Company Name</b><br />
            <b>123 Good Street</b><br />
            <b>Cincinnati, OH 45248</b><br />

            <br />
            <small>
                *Checks/money orders must be in US funds & drawn from a US bank.
                <br />
                *Returned checks are subject to a $20.00 return check fee.
            </small>
    </div>

        <?php
        if($ccinfo)
        {
        ?>
            <div id="existingCCInfo">
               <div class="form-group">
                    <label for="ccid" class="col-xs-12">Existing card:</label>
                    <div class="col-xs-12">

                        <select class="form-control" name="ccid" id="ccid">
                        <?php
                        foreach($ccList as $ccdetail)
                        {
                            ?>
                            <option value="<?php echo($ccdetail->ccid); ?>"><?=$ccdetail->card_number ?> | <?=$ccdetail->expire_date ?> | <?=$ccdetail->card_type ?></option>
                            <?php
                        }
                        ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php
        $display="none";
        }
        $result=array();
        include 'cc_info.php';
        ?>
    <div class="col-md-12" id="tr_no_auto_bill">
    <div class="form-group">
                Disable Auto-Renewal:
                <label>
                    <input type="checkbox" name="stop_auto_bill" id="stop_auto_bill" value="1" <?php if($_REQUEST["stop_auto_bill"]=="1")echo("checked"); ?>>
                </label>
    </div>
    </div>
        <h3 id="msgProgress" style="display:none">Processing your request, Please wait..</h3>
        <input type='submit' name='Subscribe_API' class="btn btn-default btn-block" onclick="if( frmValidate(this.form) && (current_payment_method == 2) ){this.style.display='none';document.getElementById('msgProgress').style.display='';}else if( current_payment_method == 1 || current_payment_method == 3){this.style.display='none';document.getElementById('msgProgress').style.display='';this.form.submit();} else {return false;}" id="Subscribe_API" value='Submit'>

</form>
</div>
<br />
<br />
<!-- bootstrap end -->
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
    function validate_promo(){
            var validatePromo = jQuery('#promo_code_lbl_error');
            var selected_package = jQuery('#selected_package').val();
            var selected_package_cost = jQuery('#selected_package_cost').val();
            if (selected_package == "")
            {
                validatePromo.html("Please select a package to validate promocode.");
                return false;
            }
            else
            {
                var t = jQuery('#promo_code');

                var promo_val =t.val();
                if (t.timer)
                    clearTimeout(t.timer);
                validatePromo.removeClass('error').html('<img src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna//images/loader.gif" height="16" width="16" />');
                if (jQuery('#promo_code').val() == "")
                {
                    validatePromo.html("Please enter promocode .");
                    return false;
                }
                t.timer = setTimeout(function () {
                    jQuery.ajax({
                        url: '<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/?dna_validate=promo_code',
                        data: 'promo_code=' + promo_val + "&selected_package_cost=" + selected_package_cost + "&selected_package=" + selected_package,
                        dataType: 'json',
                        type: 'post',
                        success: function (j) {
                            validatePromo.html(j.msg);
                            //document.getElementById('displayTaxInfo').innerHTML = j.newcostmsg;
                            showHidePaymentInfo(j.payment_info_not_required);
                        }
                    });
                }, 200);
                jQuery('#promo_code').lastValue = jQuery('#promo_code').value;

                return false;
            }
            
            
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
        }
    });
//-->
</script>
<script>

if("<?php echo($selected_package); ?>"!="")
packageChanged(document.getElementById("innerDiv_<?php echo($selected); ?>") ,"<?php echo($selected_package); ?>");
</script>
<script>

<?php
if($newPackages==0)
{
	echo("document.getElementById('packagesList').style.display='none';");
}
?>
</script>
<?php
}
?>