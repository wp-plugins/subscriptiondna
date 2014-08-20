<?php
        
        $msg='<font color="#009933">'.$_REQUEST["msg"].'</font>';        
	$login_name = $_SESSION['login_name'];
	$alreadySigned=array();
        if(isset($_REQUEST["Subscribe_API"]))
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
            //print_r($result);
            $msg='<font color="#00FF00">'.$result["errDesc"].'</font>';
        }	
	if($_REQUEST['status']){
		$data=array("login_name"=>$login_name,"sub_id"=>$_REQUEST['subId'], "status"=>$_REQUEST['status']);
		$result = SubscriptionDNA_ProcessRequest($data,"subscription/change_status");
		?>
		<script>
		location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>?msg=<?php echo($result->errDesc); ?>';
		</script>
		<?php
		die();

		
	}
	else if($_REQUEST['renew'])
	{
		if($_REQUEST['confirmation_page'] == 1)
		{
                        $data=array("sub_id"=>$_REQUEST['subId'], "confirmation_page"=>$_REQUEST['confirmation_page'], "login_name"=>$login_name);
			$result = SubscriptionDNA_ProcessRequest($data,"subscription/renew");
			?>
			

                        <script type="text/javascript" src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/ccinfo.js"></script>
				<form method="post" action="?&subId=<?=$_REQUEST['subId']?>&renew=renew&confirmation_page=0">
                                    <input type="hidden" name="card_id" value="<?php echo($_REQUEST["card_id"]); ?>">
                <h2>Renew Your Expired Subscription</h2>
                
                <p>
					<table align="center">
					<tr>
					<td valign="top" style="padding-right: 15px;">
						<table cellpadding="4">
						<tr><td>
						<b>Name:</b><br>
						<?=$result->first_name ?> <?=$result->last_name ?><br>
					
						<br>
						<b>Email:</b><br>
						<?=$result->email ?><br>
						
						<br>
						<b>Login Name:</b><br>
						<?=$result->login_name ?><br>
						
						</td></tr>
						</table>
					</td>
					<td valign="top" style="padding-left: 15px;">
						<table cellpadding="4">
						<tr><td valign="top">
                        <b>Subscription:</b><br />
                        	<?=$result->services ?><br />
							<?=$result->billing_routine ?>
                        </td>
						</tr>
						</table>
					</td>
					
					<td valign="top" style="padding-left: 15px;">
						<table cellpadding="4">
						<tr><td>
						<b>Payment Method:</b><br>
						<?=$result->card_holder_name ?><br>
						<?=$result->card_type ?> <?=$result->card_number ?> | <?=$result->expMonth ?>/<?=$result->expYear ?><br>
						<input type="checkbox" name="paid_by_new_card" value="1" onclick="hideShowCCInfo(!this.checked);"> Use alternate payment method?
						</td></tr>
						
						</table>
			
					</td>
					</tr>						
					<tr><td colspan="4">
						<table width="100%">
						<?php
						$display="none";
                                                $result=array();
						include 'cc_info.php';
						?>
						</table>
					
						</td></tr>
						<tr><td><br>
						<input type="submit" value="Renew Subscription" /></td></tr>
					</table>
				</form>
			<?	
			exit;
		}
		else
		{
                        $data=array("sub_id"=>$_REQUEST['subId'], 
                            "confirmation_page"=>$_REQUEST['confirmation_page'], 
                            "login_name"=>$login_name,
                            "card_id"=>$_REQUEST["card_id"],
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
				$msg='<font color="#FF0000">'.$result["errDesc"].', Please try again</font>';
			}else{
				$msg='<font color="#009933">'.$result["errDesc"].'</font>';
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
		$alreadySigned[]=$subscription->service_id;	
		?>
			
			<tr>
			<td valign="top">
			<b><?php echo $subscription->service_name; ?></b><br>
			<?php if ($subscription->service_description!="") { echo($subscription->service_description); echo "<br>"; } ?>
			<?php if ($subscription->billing_description!="") { echo "<i>"; echo($subscription->billing_description); echo "</i><br />"; } ?>
			</td>
			
			<td valign="top" style="padding-left: 15px;">
			<b>Status:</b> <?php echo ($subscription->status); ?><br>

			<br>
			<b>Start / Expiration Dates:</b><br>
			<?php echo substr($subscription->subscription_date,0,10); ?> / <?php echo substr($subscription->expires,0,10); ?><br>

		<?php
		if(trim($subscription->status)=='Active'){
			echo "<a href='?&change=" . $subscription->sub_id . "'>Change</a>&nbsp;|&nbsp;<a onClick=\"if(!confirm('Are you sure you want to Unsubscribe?')) return(false);\" href='?&subId=" . $subscription->sub_id . "&status=Unsubscribed'>Unsubscribe</a>&nbsp;| <a onClick=\"if(!confirm('Are you sure you want to Discontinue?')) return(false);\"  href='?&subId=" . $subscription->sub_id . "&status=Discontinued'>Discontinue</a><br>";
		}else if($subscription->status=='Discontinued'){
			echo "<a onClick=\"if(!confirm('Are you sure you want to Unsubscribe?')) return(false);\"  href='?&subId=" . $subscription->sub_id . "&status=Unsubscribed'>Unsubscribe</a>&nbsp;| <a onClick=\"if(!confirm('Are you sure you want to Re-activate?')) return(false);\"  href='?&subId=" . $subscription->sub_id . "&status=Active'>Re-activate</a><br>";
		}
?>
			<br>
			<b>Preferred Payment Method:</b><br>
			<select name="card_id" id="card_id" onchange="if(confirm('Are you sure you want to change selected card for this subscription?')){location.href='?&subId=<?php  echo($subscription->sub_id); ?>&update_cc='+this.value;}">
			<option value="">No Card Selected</option>

			<?php foreach($ccList as $ccdetail) { ?>
			<option value="<?php echo($ccdetail->ccid); ?>" <?php if($ccdetail->ccid==$subscription->ccid) echo("selected"); ?>><?=$ccdetail->card_number ?> | <?=$ccdetail->expire_date ?> | <?=$ccdetail->card_type ?></option>
			<?php }	?>
			</select>
<? } ?>
			</td>
			</tr></table>

<p>
<small><b>Please note that UNSUBSCRIBE will immediately terminate your subscription.  Therefore, please only UNSUBSCRIBE if you have decided for sure to permanently cancel the service. If you would like to cancel your subscription at the expiration of the current billing period (allowing you to finish your current subscription term), please click on DISCONTINUE. When you do so, you can change your mind until the expiration date by clicking on REACTIVATE to restore your subscription.</b></small>
</p>
<?php
}

//old subscribe
if(isset($_POST["ValidateCode"]))
{
        list($service,$billing)=explode(";",$_POST["packages"]);
        $data=array("promo_code"=>$_POST["promo_code"],"services"=>$service,"billing_routine_id"=>$billing);
        $promocode = SubscriptionDNA_ProcessRequest($data,"subscription/validate_promocode");
	if($promocode->errCode<0)
	{
		$code_msg=$promocode->errDesc.'';
		$code_msg='<label style="color:red">'.$code_msg.'</label>';
	}
	else
	{
            if($promocode->discount_mod=="%")
                $code_msg='Your code is valid. You save '.$promocode->discount.$promocode->discount_mod;
               else if($promocode->discount_mod=="b")
                $code_msg='Your code is valid. '.$promocode->billing;
               else
                $code_msg='Your code is valid. You save $'.$promocode->discount.'';
               $code_msg='<label style="color:green">'.$code_msg.'</label>';
	}
}

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
	<legend style="color:#0000FF"><b>Change Subscription</b></legend>
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
	<b>Total Subscription Hours:</b> <?php echo($result["total_hours"]);?> (difference of expiry date and last invoice date)<br>
	<b>Amount Per Hour:</b> Last Payment ($<?php echo($result["amount"]);?>) / Total Subscription Hours (<?php echo($result["total_hours"]);?>) = $<?php echo($result["amount_per_hour"]);?><br>
	<br>
	<b>Remaining Subscription Hours:</b> <?php echo($result["remaining_hours"]);?> <br>
	<b>Total Credit:</b> Remaining Hours (<?php echo($result["remaining_hours"]);?>) x Amount Per Hour ($<?php echo($result["amount_per_hour"]);?>) = $<?php echo($result["remaining_amount"]);?>; <br>
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
			<b>Remaining Subscription Balance is $0.0.
		<?php
	}
?>


<script type="text/javascript" src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/ccinfo.js"></script>
<div id="DNAFormFields">
<form name='customSubscribeForm' action='' method='POST'>
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
                            if(!in_array($package->service_id,$alreadySigned))
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
        <td align="left"><span id="promo_code_lbl" class="lbl">Enter Valid Promo Code</span>&nbsp;</td> 
        <td colspan="2"><input TYPE="TEXT" NAME="promo_code" id="promo_code" value="<?php echo(@$_REQUEST["promo_code"]); ?>"  style="width:175px; padding-left: 4px;" size="30" class="noErr" MAXLENGTH="100">		<input type="submit" onclick="this.form.action='';" name="ValidateCode" value="Validate Promocode" /> 
        <span id="promo_code_lbl_error" class="lblErr"><?php echo($code_msg); ?></span></td> 
        </tr>         
        <tr valign=top>
            <td>Payment Method</td>
            <td colspan="2">
        <?php
        if($ccinfo)
        {
        ?>
            <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="1" or ($ccinfo && $_POST["payment_method"]=="")) echo("checked"); ?> value='1' onclick="hideShowCCInfo(this.checked);">Use Existing Credit Card<br>
        <?php 
        }
        ?>        
            <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="3") echo("checked"); ?> value='3' onclick="hideShowCCInfo(true);">Check/Mo<br>
            <input type='radio' name='payment_method' <?php if($_POST["payment_method"]=="2") echo("checked"); ?> value='2' onclick="hideShowCCInfo(false);">Use New Credit Card<br>
            </td>
        </tr>	
        <?php
        if($ccinfo)
        {
        ?>
        <tr valign=top id="existingCCInfo">
            <td>
			<b>Existing card:</b>
			<td>
			<td>
			<select name="ccid" id="ccid" style="width:250px;">
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
		<tr>
           <td colspan="3"><input type='submit' name='Subscribe_API' id="Subscribe_API" value='Submit'>&nbsp;</td>
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

