<?php
        
        $msg='<font color="#009933">'.$_REQUEST["msg"].'</font>';        
	$login_name = $_SESSION['login_name'];
	$alreadySigned=array();
        if(isset($_REQUEST["Subscribe_API"]))
        {
            if(isset($_POST["cc_on_file"]))
                $existing_credit_card="1";//$_POST["ccid"];
            list($service_id,$billing_routine_id)=explode(";",$_POST["package"]);
            $requestData = array(
                   'login_name' => $login_name,
                   'password' => $_SESSION['password'],
                   'service_id' => $service_id,
                   'billing_routine_id' => $billing_routine_id,
                   'paid_by_credit_card' => 1,
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
                   'check_mo' => '',
                   'tax' => ''
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
			

                        <script type="text/javascript" src="/wp-content/plugins/subscriptiondna/ccinfo.js"></script>
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

	foreach($subscriptions as $subscription)
	{
		if(!$subscription)
		break;
		if($subscription->status=='Active')
		$_SESSION['subscription']="1";
		if($subscription->status!="Unsubscribed")
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
			echo "<a onClick=\"if(!confirm('Are you sure you want to Unsubscribe?')) return(false);\" href='?&subId=" . $subscription->sub_id . "&status=Unsubscribed'>Unsubscribe</a>&nbsp;| <a onClick=\"if(!confirm('Are you sure you want to Discontinue?')) return(false);\"  href='?&subId=" . $subscription->sub_id . "&status=Discontinued'>Discontinue</a><br>";
		}else if($subscription->status=='Discontinued'){
			echo "<a onClick=\"if(!confirm('Are you sure you want to Unsubscribe?')) return(false);\"  href='?&subId=" . $subscription->sub_id . "&status=Unsubscribed'>Unsubscribe</a>&nbsp;| <a onClick=\"if(!confirm('Are you sure you want to Re-activate?')) return(false);\"  href='?&subId=" . $subscription->sub_id . "&status=Active'>Re-activate</a><br>";
		}else if($subscription->status=='Expired'){
			echo "<a onClick=\"if(!confirm('Are you sure you want to Renew?')) return(false);\" href='?&subId=" . $subscription->sub_id . "&card_id=" . $subscription->ccid . "&renew=renew&confirmation_page=1'>Renew</a><br>";
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

<div style="color:#990000">
<h4><?php echo($_POST["response"]); ?></h4>
</div>
<?php
if(count($packages)>0)
{
$newPackages=0;
?>

<script type="text/javascript" src="/wp-content/plugins/subscriptiondna/ccinfo.js"></script>
<form name='customSubscribeForm' action='' method='POST'>
    <table id="packagesList" cellpadding="3" width="100%">
        
		<input type='hidden' name='login_name' value='<?= $_SESSION['login_name']?>'>
		<input type='hidden' name='password' value='<?= $_SESSION['password']?>'>
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
					?>
					<div id="innerDiv">
					<strong><input type="radio" name="package" id="packages_<?php echo($package->id); ?>"  value="<?php echo($package->service_id); ?>;<?php echo($package->billing_routine_id); ?>" <?php if($package->defaultval=="Yes") echo("checked");  ?>  ><?php echo($package->package_name);  ?></strong>
						<div style="margin-left:20px;"><?php echo($package->package_description); ?></div><br>
					</div>
					<?php
					} 
				}
				?>
				</div>
				<br>
			</td>
        </tr>
<tr> 
        <td align="left"><span id="promo_code_lbl" class="lbl">Enter Valid Promo Code</span>&nbsp;</td> 
        <td colspan="2"><input TYPE="TEXT" NAME="promo_code" id="promo_code" value="<?php echo(@$_REQUEST["promo_code"]); ?>"  style="width:175px; padding-left: 4px;" size="30" class="noErr" MAXLENGTH="100">		<input type="submit" onclick="this.form.action='';" name="ValidateCode" value="Validate Promocode" /> 
        <span id="promo_code_lbl_error" class="lblErr"><?php echo($code_msg); ?></span></td> 
        </tr>         
		<?php
		if($ccinfo)
		{
		?>
        <tr valign=top>
            <td colspan="3">
			<input type='checkbox' name='cc_on_file' <?php if($_POST["cc_on_file"]=="1" or $ccinfo) echo("checked"); ?> value='1' onclick="hideShowCCInfo(this.checked);">Use Existing Credit Card<br>
		</td>
		</tr>	
        <tr valign=top id="existingCCInfo">
            <td>
			<b>Payment Method:</b>
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

