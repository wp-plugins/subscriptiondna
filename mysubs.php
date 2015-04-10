<?php 
wp_enqueue_script("jquery");
?>
<link rel="stylesheet" href="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/lib/alertify.core.css" />
<script src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/lib/alertify.min.js"></script>
<script>
    
    function dnaAskConfimation(btn1,btn2,message,target_url)
    {
        alertify.set({ labels: { ok: btn1, cancel: btn2 } });
        alertify.confirm(message, function (e) {
                if (e) {
                        location.href=target_url
                } else {
                        return;
                }
        });
    }
</script>
<?php
        
        echo(urldecode($_REQUEST["msg"]));       
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
            $msg='<font color="#00FF00">'.$result["errDesc"].'</font>';
            
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
		?>
		<script>
		location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>?msg=<?php echo(urlencode($result->errDesc)); ?>';
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
			

                        <script type="text/javascript" src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/ccinfo.js"></script>
				<form method="post" action="?&subId=<?=$_REQUEST['subId']?>&renew=renew">
                                    <input type="hidden" name="card_id" value="<?php echo($_REQUEST["card_id"]); ?>">
                                    <input type="hidden" name="confirmation_page" value="<?php if ($_REQUEST["confirmation_page"] == "21"){echo("2");}else{echo("0");} ?>">
                <h2><?php if ($_REQUEST["confirmation_page"] == "21"){echo("Extend Your ");}else{echo("Renew Your Expired ");} ?>Subscription</h2>
                
                <p>
					<table align="center">
					<tr>
					<td valign="top" >
						<table cellpadding="4">
						<tr><td>
						<b>Name:</b><br>
						<?=$result->first_name ?> <?=$result->last_name ?><br><br>
						<b>Email:</b><br>
						<?=$result->email ?><br><br>
						<b>Login Name:</b><br>
						<?=$result->login_name ?><br>
						
						</td></tr>
						</table>
					</td>
                                        </tr>
					<tr>
					<td valign="top" >
						<table cellpadding="4">
						<tr>
                                                <td valign="top">
                                                <b>Subscription:</b><br />
                                                        <?=$result->services ?>
                                                        <?=$result->billing_routine ?><br>
                                                        <br>
                                                        Subscription Date: <?php echo($result->start_date); ?><br />
                                                        Subscription Expiration: <?php echo($result->expiry_date); ?><br />
                                                        <?php
                                                        if ($_REQUEST["confirmation_page"] == "21")
                                                        {
                                                            ?>
                                                           Extend To: <?php echo($result->extend_upto); ?>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            Renew Subscription To: <?php echo($result->renew_upto); ?>
                                                            <?php
                                                        }
                                                        ?>                                        
                                                        
                                                        
                                                </td>
						</tr>
						</table>
					</td>
                                        </tr>
					<tr>
					<td valign="top" >
<table>
                                            <tr valign=top>
                                                <td  width="160">Payment Method</td>
                                                <td  width="10">&nbsp;</td>
                                                <td >
                                                <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="3") echo("checked"); ?> value='3' onclick="hideShowCCInfo(true);dnaPaymentMethodChanged(3);"> Check/Invoice<br>
                                                <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="2") echo("checked"); ?> value='2' onclick="hideShowCCInfo(false);dnaPaymentMethodChanged(2);"> Use New Credit Card<br>
                                            <?php
                                            if($ccinfo)
                                            {
                                            ?>
                                                <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="1" or ($ccinfo && $_POST["payment_method"]=="")) echo("checked"); ?> value='1' onclick="hideShowCCInfo(this.checked);dnaPaymentMethodChanged(1);"> Use Existing Credit Card<br>
                                            <?php 
                                            }
                                            ?>        
                                                </td>
                                            </tr>
                                            <tr valign=top id="trCheckInfo" style="display:none">
                                                <td colspan="3">
                                               <b>Please make checks payable to:</b><br />
                                               <b>My Company Name</b><br />
                                               <b>123 Good Street</b><br />
                                               <b>Cincinnati, OH 45248</b><br />

                                                <br />
                                                <small>*Checks/money orders must be in US funds & drawn from a US bank.<br />
                                                *Returned checks are subject to a $20.00 return check fee.</small>
                                                </td>
                                            </tr>
                                            <?php
                                            if($ccinfo)
                                            {
                                            ?>
                                            <tr valign=top id="existingCCInfo">
                                                <td>
                                                            <b>Existing card:</b>
                                                            </td>
                                                            <td>&nbsp;</td>    
                                                            <td>
                                                            <select name="ccid" id="ccid">
                                                            <?php
                                                            foreach($ccList as $ccdetail)
                                                            {
                                                                    ?>
                                                                    <option value="<?php echo($ccdetail->ccid); ?>"><?=$ccdetail->card_number ?> | <?=$ccdetail->expire_date ?> | <?=$ccdetail->card_type ?></option>
                                                                    <?php
                                                            }
                                                            ?>
                                                            </select>
                                                            </td>
                                            </tr>
                                            <?php
                                                    $display="none";
                                                    }
                                                    $result=array();
                                                    include 'cc_info.php';
                                                    ?>    
                                        <tr valign=top id="tr_no_auto_bill" >
                                            <td nowrap>Disable Auto-Renewal: </td>
                                            <td>&nbsp;</td> 
                                            <td >
                                                <input type="checkbox" name="stop_auto_bill" id="stop_auto_bill" value="1" <?php if($_REQUEST["stop_auto_bill"]=="1")echo("checked"); ?>>
                                            </td>
                                        </tr>
                                            
</table>
					</td>
					
                                        </tr>
                                        
						<tr><td><br>
                                                <h3 id="msgProgress" style="display:none">Processing your request, Please wait..</h3>       
						<input type="submit" onclick="this.style.display='none';document.getElementById('msgProgress').style.display='';" value="<?php if ($_REQUEST["confirmation_page"] == "21"){echo("Extend");}else{echo("Renew");} ?> Subscription" /></td></tr>
					</table>
				</form>
			<?	
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
				$msg='<font color="#063">'.$result["errDesc"].', Please try again</font>';
			}
                        else
                        {
                            if($_REQUEST["stop_auto_bill"]=="1")
                            {
                                $data=array("login_name"=>$login_name,"sub_id"=>$_REQUEST['subId'], "status"=>"Discontinued");
                                SubscriptionDNA_ProcessRequest($data,"subscription/change_status");
                            }
                            $msg='<font color="#009933">'.$result["errDesc"].'</font>';
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
			$msg='<font color="#FF0000">'.$result["errDesc"].', Please try again</font>';
		}else{
			$msg='<font color="#009933">'.$result["errDesc"].'</font>';
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
		echo '&nbsp;&nbsp;<font color="#FF0000">No Subscription Found.</font><br />';
	}
	else
	{
            
            
	?>


		
        <table id="dna-subscriptions" width="100%" cellpadding="3" cellspacing="0">
			<tr>
				<td colspan="2"><?php echo($msg); ?></td>
			</tr>
			<tr>	
				<th>Subscription</th>
				<th style="padding-left: 15px;">Details</th>				
			</tr>	
				
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
			
			<tr>
			<td valign="top">
			<b><?php echo $subscription->service_name; ?></b><br>
			<?php if ($subscription->service_description!="") { echo($subscription->service_description); echo "<br>"; } ?>
			<?php if ($subscription->billing_description!="") { echo "<i>"; echo($subscription->billing_description); echo "</i><br />"; } ?>
			</td>
			
			<td valign="top" style="padding-left: 15px;">
                            
			<b>Status:</b> <?php echo ($subscription->status=="Discontinued"?"Discontinued Auto-Billing":$subscription->status); ?><br>

			
                        <b>Start / Expiration Dates:</b><br> 
			<?php echo substr($subscription->subscription_date,0,10); ?> / <?php echo substr($subscription->expires,0,10); ?><br>

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
			
<? } ?>
			</td>
			</tr></table>
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
<div style="color:#990000">
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
	
	<fieldset>
	<legend ><b>Existing Subscription Details</b></legend>
	<br>
	<div class="red">
	<b>Current subscription</b> to <?php echo($result["service_name"]);?> expires <?php echo($result["expires"]);?>
	<ul>
	<li><b>Service:</b> <?php echo($result["service_name"]);?><br>
	<?php
	if($result["routine_name"]!="")
	{
	?>
	<li><b>Billing Routine:</b> <?php echo($result["routine_name"]);?><br>
	<?php
	}
	?>
	</ul>
	
	<br>
	<b>Last Invoice Payment:</b> $<?php echo($result["amount"]);?> was completed on <?php echo($result["invoice_date"]);?><br>
	<b>Total Credit:</b> = $<?php echo($result["remaining_amount"]);?> <br>
	<br>
	<b>Credit Discount</b> of $<?php echo($result["remaining_amount"]);?> will be applied toward your new subscription.<br>
	</div>
	</fieldset>
	<br>
	<?php
	}
	else if($_REQUEST["change"]!="")
	{
		?>
			<b>Remaining Subscription Balance is $0.00
		<?php
	}
?>


<script type="text/javascript" src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/ccinfo.js"></script>
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
<div id="DNAFormFields">
<form name='customSubscribeForm' action='' method='POST'>
    <input type='hidden' name='SubscribeAPI' id="SubscribeAPI" value='Submit'>
    <table id="packagesList" cellpadding="3" width="100%">
        
        <input type='hidden' name='login_name' value='<?= $_SESSION['login_name']?>'>
        <input type='hidden' name='password' value='<?= $_SESSION['password']?>'>
        <input type="hidden"  value="<?php echo($_REQUEST["change"]); ?>" name="change" id="change" />
        <tr valign=top>
            <td colspan="3"><b>Subscription Plans:</b></td>
		</tr>
		<tr valign=top>			
            <td colspan="3">
                    <div style="border: 1px solid gray; padding: 5px;">
                    <!-- height: 150px; overflow: auto; -->
                    <?php 
                    foreach($packages as $package)
                    {
                            if(!in_array($package->service_id,$alreadySigned) || ($_REQUEST["change"]!="" && $package->billing_routine_id!=$existing_billing))
                            {
                                $newPackages++;
                                if($package->service_id.";".$package->billing_routine_id==$_POST["package"] || ($package->defaultval=="Yes" && !$selected))
                                {
                                    $selected=$package->uid;
                                    $selected_billing=$package->billing_routine_id;
                                    $selected_package=$package->service_id.";".$package->billing_routine_id;
                                }
                                ?>
                                <div title="Click to select your subscription plan."  id="innerDiv_<?php echo($package->uid); ?>"  class='package-box package-box-main' onclick='packageChanged(this,"<?php echo($package->service_id); ?>;<?php echo($package->billing_routine_id); ?>");'>
                                <strong><?php echo($package->package_name);  ?></strong>
                                <div ><?php echo($package->package_description); ?></div>
                                </div>
                                <?php
                            } 
                    }
                    ?>
                    <input type="hidden" name="package" id='package' value="<?php echo($selected_package);  ?>" />                                
                    </div>
                <br>
            </td>
        </tr>
        <tr> 
            <td align="left"  width="160"><b>Promo Code:</b></td>
        <td width="10">&nbsp;</td>
        <td ><input TYPE="TEXT" NAME="promo_code" id="promo_code" value="<?php echo(@$_REQUEST["promo_code"]); ?>"  style="width:175px; padding-left: 4px;" size="30" class="noErr" MAXLENGTH="100"> <input type="submit" onclick="return(false);" name="ValidateCode" value="Validate Promocode" /> 
        </td> 
        </tr> 
        <tr>
            <td colspan="3"><span id="promo_code_lbl_error" class="lblErr"><?php echo($code_msg); ?></span></td>
        </tr>
        <tr valign=top>
            <td>Payment Method</td>
            <td>&nbsp;</td>
            <td >
            <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="3") echo("checked"); ?> value='3' onclick="hideShowCCInfo(true);dnaPaymentMethodChanged(3);"> Check/Invoice<br>
            <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="2") echo("checked"); ?> value='2' onclick="hideShowCCInfo(false);dnaPaymentMethodChanged(2);"> Use New Credit Card<br>
        <?php
        if($ccinfo)
        {
        ?>
            <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="1" or ($ccinfo && $_POST["payment_method"]=="")) echo("checked"); ?> value='1' onclick="hideShowCCInfo(this.checked);dnaPaymentMethodChanged(1);"> Use Existing Credit Card<br>
        <?php 
        }
        ?>        
            </td>
        </tr>	
        <tr valign=top id="trCheckInfo" style="display:none">
            <td colspan="3">
            <b>Please make checks payable to:</b><br />
            <b>My Company Name</b><br />
            <b>123 Good Street</b><br />
            <b>Cincinnati, OH 45248</b><br />

            <br />
            <small>*Checks/money orders must be in US funds & drawn from a US bank.<br />
            *Returned checks are subject to a $20.00 return check fee.</small>

            </td>
        </tr>
        
        <?php
        if($ccinfo)
        {
        ?>
        <tr valign=top id="existingCCInfo">
            <td>
			<b>Existing card:</b>
			</td>
                        <td>&nbsp;</td>    
			<td>
			<select name="ccid" id="ccid">
			<?php
			foreach($ccList as $ccdetail)
			{
				?>
				<option value="<?php echo($ccdetail->ccid); ?>"><?=$ccdetail->card_number ?> | <?=$ccdetail->expire_date ?> | <?=$ccdetail->card_type ?></option>
				<?php
			}
			?>
			</select>
			</td>
        </tr>
        <?php
		$display="none";
		}
		$result=array();
		include 'cc_info.php';
		?>
                <tr valign=top id="tr_no_auto_bill">
                    <td nowrap>Disable Auto-Renewal: </td>
                    <td>&nbsp;</td>
                    <td >
                        <input type="checkbox" name="stop_auto_bill" id="stop_auto_bill" value="1" <?php if($_REQUEST["stop_auto_bill"]=="1")echo("checked"); ?>> 
                    </td>
                </tr>        
		<tr>
           <td colspan="3"><h3 id="msgProgress" style="display:none">Processing your request, Please wait..</h3><input type='submit' name='Subscribe_API' onclick="this.style.display='none';document.getElementById('msgProgress').style.display='';" id="Subscribe_API" value='Submit'>&nbsp;</td>
        </tr>
    </table>
</form>
</div>
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

