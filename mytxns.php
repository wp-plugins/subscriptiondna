<? 
	$login_name = $_SESSION['login_name'];
        $data=array("login_name"=>$login_name);
        $transactions = SubscriptionDNA_ProcessRequest($data,"user/transactions",true);
        SubscriptionDNA_LoginCheck($transactions);
	if(count($transactions)<1){
		echo '<div id="dna-login"><div id="failure">No Transaction Exists.</div></div>';
	}
        else
        {		
	?>
	
<table id="dna-subscriptions" width="100%" cellpadding="3" cellspacing="0">
<tr><td colspan="5"></td></tr>
			<tr>
				<th>Invoice</th>
				<th align="left">Payment Method</th>
				<th align="left">Amount</th>
				<th align="left">Txn Date</th>
				<th align="left">Status</th>
			</tr>	
				
	<?php		
		$page_file="?";
		
                //page header
                $minimumshow=20;
                $display=20;
                $upto=20;
                if(isset($_REQUEST["dispalypages"]))
                {
                        $display=$_REQUEST["dispalypages"];
                        $upto=$_REQUEST["dispalypages"];
                        $link="&dispalypages=".$_REQUEST["dispalypages"];
                }
                $limitFrom=0;
                $curentPage=1;
                if($_REQUEST["pageing"])
                {
                        $curentPage=$_REQUEST["pageing"];
                        $limitFrom=($curentPage-1)*$display;
                        $upto=$limitFrom+$display;
                }

                $total=@count($transactions);                
                //end page header
                
		for($i = $limitFrom; $i <$upto; $i++)
		{
			$resultRow = $transactions[$i];
			if($resultRow)
			{
				//print_r($resultRows["record".$i]);
				if($resultRow["response_code"] == '')	
				{
						$resultRow["response_code"] = "&nbsp;";
				}
				?>
					<tr onmouseover="this.style.backgroundColor='#ebebeb'" onmouseout="this.style.backgroundColor='';">
						<td><?php echo(str_pad($resultRow["invoice_id"],5,"0",STR_PAD_LEFT)); ?></td>
						<td align="left">
							<?php if ($resultRow["isCheckMO"]=="1") { ?>
							Paid by Cash/Check/MO
							<? } else { ?>
							<?php echo($resultRow["card_number"]); ?> | <?php echo($resultRow["expire_date"]); ?> | <?php echo($resultRow["card_type"]); ?>
							<?php
							}
							?>
						</td>
						
						<td>$<?php echo($resultRow["amount"]); ?></td>
						<td><?php echo($resultRow["invoice_date"]); ?></td>
					<td><?php echo($resultRow["payment_status"]); ?> </td>
					</tr>	
				<?php
			}
		}
	
		echo '</table>';		
                // page footer
                if($total>$minimumshow)
                {	
                        $next=$curentPage+1;
                        $previous=$curentPage-1;
                         print "<br><center><div style='font-size:10px;'>Total Record(s) <b>".$total."</b> Display on this page:";
                         ?>
                         <select name="dispaly" onChange="window.open('<?php print $page_file;?>&dispalypages='+this.value,'_self','');">
                                <?php
                                for($pageLoop=1;$pageLoop<101;$pageLoop++)
                                {	
                                        $pageLoop=$pageLoop+9;
                                        $select="";
                                        if($pageLoop==$display)
                                                $select="selected";
                                        print "<option value='$pageLoop' $select>$pageLoop</option>";
                                }
                                ?>
                        </select><br><br>
                         <?php
                        if($limitFrom>0)
                        {
                                print "<a href='".$page_file."&Previous=1&pageing=".$previous.$link."'>Previous</a>";
                        }
                        else
                                print "Previous";	


                        $pageLink=$total/$display;
                         if(!is_int($pageLink))
                                $pageLink=$pageLink+1;
                        print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        for ($pageL = 1; $pageL <= $pageLink; $pageL++) 
                        {
                                if( $pageL == $curentPage )
                                        print "<b> $pageL </b> &nbsp;";
                                else
                                        print "<a href='".$page_file."&pageing=".$pageL.$link."'>$pageL</a> &nbsp;";
                        }
                        print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

                        if($limitFrom+$display<$total)
                        {
                                print "<a href='".$page_file."&pageing=".$next.$link."'>Next</a>";
                        }
                        else
                                print "Next";	

                                print "</div></center>";

                }
        }
	
?>