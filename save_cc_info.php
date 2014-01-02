<?php
	$client = new nusoap_client($GLOBALS['SubscriptionDNA']['WSDL_URL'],true);
	$session_id = $_SESSION['user_session_id'];
	$login_name = $_SESSION['login_name'];	
	
	function dump( $txt ){
		echo "<pre>"; print_r($txt); echo "</pre>";
	}
	
	$cid=$_REQUEST['cid'];
//print_r($cid);	
	if(!empty($cid)){
			$result = $client->call("CCInfoDataByCCid", SubscriptionDNA_wrapAsSoap(array($login_name,$cid)));
			$result = SubscriptionDNA_parseResponse($result);
//echo "<pre>";			
//print_r(explode("|",$result));
//print_r($_REQUEST);
			//$result =$result[0];		
	}
	if(!empty($_REQUEST['send'])){

			if(!empty($_REQUEST['cc_id'])){
				if($_REQUEST['isPrimary']!=1) $_REQUEST['isPrimary']=0;
				
				$result = $client->call("EditCCInfo",SubscriptionDNA_wrapAsSoap(array($_REQUEST['cc_id'], $_REQUEST['cc_name'], $_REQUEST['cc_type'], $_REQUEST['cc_number'], 
															      $_REQUEST['cc_exp_month'], $_REQUEST['cc_exp_year'], $_REQUEST['isPrimary'], $login_name)));
				

			$result = SubscriptionDNA_parseResponse($result);
				$msg=$result["errdesc"];			

				?>
				<script>
				location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']['CreditCards'])."?&msg=".urlencode($msg)); ?>';
				</script>
				<?php

	exit;
			}else{
								
				$result = $client->call("AddCCInfo", SubscriptionDNA_wrapAsSoap(array($_REQUEST['cc_name'], $_REQUEST['cc_type'], $_REQUEST['cc_number'], $_REQUEST['cc_exp_month'], $_REQUEST['cc_exp_year'], $login_name)));
			$result = SubscriptionDNA_parseResponse($result);
	//	print_r($result);exit;
			}
			if($result["errcode"]==7 || $result["errcode"]==12){
				$msg=$result["errdesc"];			
				?>
				<script>
				location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']['CreditCards'])."?&msg=".urlencode($msg)); ?>';
				</script>
				<?php
				die();
			}else{
				$msg='<font color="#FF0000">'.$result["errdesc"].'</font>';			
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
			<tr>
				<td>Card Type</td>
				<td><select name="cc_type" id="cc_type">
						<option></option>
						<option label="American Express" value="American Express">American Express</option>
						<option label="Discover" value="Discover">Discover</option>
						<option label="MasterCard" value="MasterCard">MasterCard</option>
						<option label="Visa" value="Visa">Visa</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Card Holder Name</td>
				<td><input name="cc_name" id="cc_name" value="<?=$result["card_holder_name"]; ?>" size="35" maxlength="100" type="text"></td>
			</tr>
			<tr>
				<td>Card Number</td>
				<td><input name="cc_number" id="cc_number" value="" size="35" maxlength="16" type="text"> <?=$result["card_number"]; ?></td>
			</tr>
			<tr>
				<td>Card Expiry</td>
				<td><select name="cc_exp_month" id="cc_exp_month">
						<option label="January" value="01">January</option>
						<option label="February" value="02">February</option>
						<option label="March" value="03">March</option>
						<option label="April" value="04">April</option>
						<option label="May" value="05">May</option>
						<option label="June" value="06">June</option>
						<option label="July" value="07">July</option>
						<option label="August" value="08">August</option>					
						<option label="September" value="09">September</option>
						<option label="October" value="10">October</option>
						<option label="November" value="11">November</option>
						<option label="December" value="12">December</option>
					</select>
	
					<select name="cc_exp_year" id="cc_exp_year">
						<option label="2013" value="2013">2013</option>
						<option label="2014" value="2014">2014</option>
						<option label="2015" value="2015">2015</option>
						<option label="2016" value="2016">2016</option>
						<option label="2017" value="2017">2017</option>
						<option label="2018" value="2018">2018</option>
						<option label="2019" value="2019">2019</option>
						<option label="2020" value="2020">2020</option>
					</select>
				</td>
			</tr>
			<? if(!empty($result["ccid"])){ ?>			
			<tr>
				<td>Is Primary <input type="hidden" id="cc_id" name="cc_id" value="<?=$result["ccid"]; ?>" /></td>
				<td><input name="isPrimary" id="isPrimary" <? if($result["status"]=='Primary'){ echo 'checked="checked"'; } ?>  type="checkbox" value="1"></td>
			</tr>
			<? } ?>	
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
		dropdown_select('cc_exp_year','20'+split_date[1]);	
	}	
	</script>
	