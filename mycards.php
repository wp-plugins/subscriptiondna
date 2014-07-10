<?php
if($_REQUEST["save_cc_info"])
{
	
	$login_name = $_SESSION['login_name'];	
	

	$cid=$_REQUEST['cid'];
	if(!empty($cid)){
                    $data=array("login_name"=>$login_name,"card_id"=>$_REQUEST["cid"]);
                    $result =SubscriptionDNA_ProcessRequest($data,"creditcard/detail",true);
	}
	if(!empty($_REQUEST['send'])){
                        
			$data=array("cc_name"=>$_REQUEST['cc_name'], "cc_type"=>$_REQUEST['cc_type'], "cc_number"=>$_REQUEST['cc_number'],"cc_exp_month"=>$_REQUEST['cc_exp_month'], "cc_exp_year"=>$_REQUEST['cc_exp_year'], "is_primary"=>$_REQUEST['isPrimary'], "login_name"=>$login_name);

			if(!empty($_REQUEST['cc_id'])){
				if($_REQUEST['isPrimary']!=1) $_REQUEST['isPrimary']=0;
                                $data["card_id"]=$_REQUEST['cc_id'];
				$result = SubscriptionDNA_ProcessRequest($data,"creditcard/update",true);
				

				$msg=$result["errDesc"];			

				?>
				<script>
				location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['payment-methods'])."?&msg=".urlencode($msg)); ?>';
				</script>
				<?php

                        exit;
			}else{
								
				$result = SubscriptionDNA_ProcessRequest($data,"creditcard/add",true);;
			}
			if($result["errCode"]==7 || $result["errCode"]==12){
				$msg=$result["errDesc"];			
				?>
				<script>
				location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['payment-methods'])."?&msg=".urlencode($msg)); ?>';
				</script>
				<?php
				die();
			}else{
				$msg='<font color="#FF0000">'.$result["errDesc"].'</font>';			
				if($_REQUEST['isPrimary']==1){
					$status='Primary';
				}			
				$result=array('cc_type'=>$_REQUEST['cc_type'],
					'cc_name'=>$_REQUEST['cc_name'],
					'cc_number'=>$_REQUEST['cc_number'],
					'status'=>$status,
					'expire_date'=>$_REQUEST['cc_exp_month'].'/'.$_REQUEST['cc_exp_year'],
				);
			}		
	}
	?>	
	
        <script type="text/javascript" src="<?php echo(WP_PLUGIN_URL); ?>/subscriptiondna/ccinfo.js"></script>
	<form name="cc_form" method="post" action="" onsubmit="return frmValidate(this);">	
		<table>
			<tr>
				<td id="avail_msg" colspan="2"><b><?=$msg; ?></b></td>
			</tr>

        <?php
         include 'cc_info.php';
         if(!empty($result["ccId"]))
             { 
            ?>			
			<tr>
				<td>Is Primary <input type="hidden" id="cc_id" name="cc_id" value="<?=$result["ccId"]; ?>" /></td>
				<td><input name="isPrimary" id="isPrimary" <? if($result["status"]=='Primary'){ echo 'checked="checked"'; } ?>  type="checkbox" value="1"></td>
			</tr>
			<?php
                        
                                } 
                                ?>	
			<tr>
				<td></td>
					<td><input name="send" value="Save Credit Card" type="submit"/></td>
				</tr>
		</table>
	</form>
	<script>
	dropdown_select('cc_type','<?=$result["card_type"]; ?>');
	var cc_date="<?=$result["expire_date"]; ?>";
	if(cc_date!=""){
		var split_date=cc_date.split('/');
		dropdown_select('cc_exp_month',split_date[0]);
		dropdown_select('cc_exp_year',split_date[1]);	
	}	
	</script>        
        <?php
}
else
{
	$wsdl =$GLOBALS['SubscriptionDNA']['WSDL_URL'];
	$client = new nusoap_client($wsdl,true);
	$session_id = $_SESSION['user_session_id'];
	$login_name = $_SESSION['login_name'];
	if($_REQUEST["del_id"])
	{
            $data=array("login_name"=>$login_name,"card_id"=>$_REQUEST["del_id"]);
            $result =SubscriptionDNA_ProcessRequest($data,"creditcard/delete",true);
	}
	
	$cards = SubscriptionDNA_ProcessRequest(array("login_name"=>$_SESSION['login_name']),"creditcard/list",true);
?>
	<br><a href='?&save_cc_info=1'>Add New Credit Card</a><br><br>
<?php	

	if(count($cards)<1){
	echo '&nbsp;&nbsp;<font color="#FF0000">No credit card found.</font><br />';
	}
	else
	{
		?>

		
<table id="dna-subscriptions" width="100%" cellpadding="3" cellspacing="0">
		<tr>
		<td colspan="6"><?=$_REQUEST['msg']; ?></td>
		</tr>
		<tr>	
		<!--				<th>CC ID</th>-->
		<th>Card Type</th>				
		<th>Name on Card</th>
		<th>Card Number</th>
		<th>Expiration Date</th>
		<th>Subscriptions</th>
		<th>Action</th>
		</tr>	
		
		<?php		
		
		foreach($cards as $card)
		{

		?>
		<tr onmouseover="this.style.backgroundColor='#ebebeb'" onmouseout="this.style.backgroundColor=''">
		
		<?php echo "
		<!--<td>" . $card["ccid"] . "</td>-->
			<td>" . $card["card_type"] . "</td>
			<td>" . $card["card_holder_name"] . "</td>
			<td>" . $card["card_number"] . "</td>					
			<td>" . $card["expire_date"] . "</td>
			<td>(<a href='/members/subscriptions.php'>" . $card["no_of_subscriptions"] . "</a>)</td>
			<td><a href='?&save_cc_info=1&cid=" . $card["ccid"] . "'>Edit</a> | <a onClick=\"if(!confirm('Are you sure you want to delete?')) return(false);\"  href='?&del_id=" . $card["ccid"] . "'>Delete</a></td>
		</tr>";
		
		}
		
		echo '</table>';
	}
}

?>