<?php
	$login_name = $_SESSION['login_name'];
        $data=array("login_name"=>$login_name);
        $transactions = SubscriptionDNA_ProcessRequest($data,"user/transactions",true);
        SubscriptionDNA_LoginCheck($transactions);
	if(count($transactions)<1){
		echo '<div id="dna-login"><div class="alert alert-danger" role="alert">No Transaction Exists.</div></div>';
	}
        else
        {
            
            
    if(isset($_REQUEST["detail"]))
    {
        foreach($transactions as $transaction)
        {
            if($transaction["invoice_id"] == $_REQUEST["detail"])	
            {
               $resultRow=$transaction;
               break;
            }
        }
        if($resultRow=="")
        {
            echo("Invalid Invoice ID");
        }
        else
        {
        //print_r($resultRow);
    ?>

        <div class="well">
            <div class="row">
             <div class="col-sm-4"><b>Invoice</b></div>
             <div class="col-sm-8"><?php echo(str_pad($resultRow["invoice_id"],5,"0",STR_PAD_LEFT)); ?></div>
           </div>
            <div class="row">
             <div class="col-sm-4"><b>Payment Method</b></div>
             <div class="col-sm-8">
                <?php if ($resultRow["isCheckMO"]=="1") { ?>
                Paid by Cash/Check/MO
                <? } else { ?>
                <?php echo($resultRow["card_number"]); ?> | <?php echo($resultRow["expire_date"]); ?> | <?php echo($resultRow["card_type"]); ?>
                <?php
                }
                ?>
                 
             </div>
           </div>
            <div class="row">
             <div class="col-sm-4"><b>Amount</b></div>
             <div class="col-sm-8">$<?php echo($resultRow["amount"]); ?></div>
           </div>
            <div class="row">
             <div class="col-sm-4"><b>Invoice Date</b></div>
             <div class="col-sm-8"><?php echo substr(($resultRow["invoice_date"]),0,10); ?></div>
           </div>
            <div class="row">
             <div class="col-sm-4"><b>Status</b></div>
             <div class="col-sm-8"><?php echo($resultRow["payment_status"]); ?> </div>
           </div>
           <?php
           if($resultRow["response_message"]!="")
           {
           ?>
            <div class="row">
             <div class="col-sm-4"><b>Response Message</b></div>
             <div class="col-sm-8"><?php echo($resultRow["response_message"]); ?> </div>
           </div>
           <?php
           }
           if($resultRow["description"]!="")
           {
           ?>
            <div class="row">
             <div class="col-sm-4"><b>Description</b></div>
             <div class="col-sm-8"><?php echo($resultRow["description"]); ?> </div>
           </div>
           <?php
           }
           ?>
            <br>
        <input type="button" onclick="history.go(-1);" value="Back To List" class="btn btn-default">
        <input type="button" onclick="window.print();" value="Print Invoice" class="btn btn-default">
            
        </div>     

    <?php    
        }
    }
    else
    {
            
	?>

 

        <div id="dna-subscriptions" >
        <div class="hidden-xs" style="  border-top: 1px solid #ddd;background-color: #ebebeb;border-bottom: 1px solid #ddd;min-height: 40px;padding: 10px 0;width:100%;">
        <div class="hidden-xs clearfix" >
            <div class='col-sm-2 ' style="padding-right:5px;padding-left:10px;width:15%;"><b>Invoice</b></div>
            <div class='col-sm-3 ' style="padding-right:5px;padding-left:0;width:40%;"><b>Payment Method</b></div>
            <div class='col-sm-2 ' style="padding-right:5px;padding-left:0;width:10%;"><b>Amount</b></div>
            <div class='col-sm-3 ' style="padding-right:5px;padding-left:0;width:20%;"><b >Txn Date</b></div>
            <div class='col-sm-2 ' style="padding-left:5px;padding-right:10px;width:15%;"><b>Status</b></div>
        </div>
        </div>

	<?php
		$page_file="?";

                //page header
                $minimumshow=20;
                $display=20;
                $upto=20;
                if(isset($_REQUEST["displaypages"]))
                {
                        $display=$_REQUEST["displaypages"];
                        $upto=$_REQUEST["displaypages"];
                        $link="&displaypages=".$_REQUEST["displaypages"];
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
                    $evenRow = false;
			$resultRow = $transactions[$i];
			if($resultRow)
			{
				//print_r($resultRows["record".$i]);
				if($resultRow["response_code"] == '')
				{
						$resultRow["response_code"] = "&nbsp;";
				}
                                
				?>
                                            <div class="well visible-xs clearfix" style="max-width: 500px;min-width: 325px;margin: 0 auto 10px auto;">
                                                <div class="clearfix"><div class=' visible-xs' style="float:left"><b>Invoice</b></div><div class=' tabular tabular-right' style=""><a href="?detail=<?php echo($resultRow["invoice_id"]); ?>"><?php echo(str_pad($resultRow["invoice_id"],5,"0",STR_PAD_LEFT)); ?></a></div></div>
                                                <div class="clearfix"><div class=' visible-xs' style="float:left"><b>Payment Method</b></div><div class=' tabular tabular-right' style="">
                                                    <?php if ($resultRow["isCheckMO"]=="1") { ?>
                                                    Paid by Cash/Check/MO
                                                    <? } else { ?>
                                                    <?php echo($resultRow["card_number"]); ?> | <?php echo($resultRow["expire_date"]); ?> | <?php echo($resultRow["card_type"]); ?>
                                                    <?php
                                                    }
                                                    ?>
                                                </div></div>
                                                <div class="clearfix"><div class=' visible-xs' style="float:left"><b>Amount</b></div><div class=' tabular tabular-right' style="">$<?php echo($resultRow["amount"]); ?></div></div>
                                                <div class="clearfix"><div class=' visible-xs' style="float:left"><b>Txn Date</b></div><div class=' tabular tabular-right' style=""><?php echo substr(($resultRow["invoice_date"]),0,10); ?></div></div>
                                                <div class="clearfix"><div class=' visible-xs' style="float:left"><b>Status</b></div><div class=' tabular tabular-right' style=""><?php echo($resultRow["payment_status"]); ?> </div></div>
                                            </div>
            
                                 <?php   if( $i % 2 != 0 ) { $evenRow = true; } ?>
            
                                            <div class="hidden-xs clearfix" style='min-height: 40px;padding: 5px 0;width:100%;<?php if($evenRow) { echo 'background-color:#ebebeb;'; } ?>' >
						<div class='col-sm-2' style="padding-right:5px;padding-left:10px;width:15%;"><a href="?detail=<?php echo($resultRow["invoice_id"]); ?>"><?php echo(str_pad($resultRow["invoice_id"],5,"0",STR_PAD_LEFT)); ?></a></div>
						<div class='col-sm-3' style="padding-right:5px;padding-left:0;width:40%;">
							<?php if ($resultRow["isCheckMO"]=="1") { ?>
							Paid by Cash/Check/MO
							<? } else { ?>
							<?php echo($resultRow["card_number"]); ?> | <?php echo($resultRow["expire_date"]); ?> | <?php echo($resultRow["card_type"]); ?>
							<?php
							}
							?>
						</div>
						<div class='col-sm-2' style="padding-right:5px;padding-left:0;width:10%;">$<?php echo($resultRow["amount"]); ?></div>
						<div class='col-sm-3' style="padding-right:5px;padding-left:0;width:20%;"><?php echo substr(($resultRow["invoice_date"]),0,10); ?></div>
					        <div class='col-sm-2' style="padding-left:5px;padding-right:10px;width:15%;"><?php echo($resultRow["payment_status"]); ?> </div>
                                                </div>

				<?php
			}
		}

        echo '</div>';


            


                // page footer
                if($total>$minimumshow)
                {  
            
                        $next=$curentPage+1;
                        $previous=$curentPage-1;
                         print "<br><center><div style='font-size:0.65em;'>Total Record(s) <span class='badge'>".$total."</span> Display on this page:";
                         ?>
                         <select name="display" style="width: auto;" onChange="window.open('<?php print $page_file;?>&displaypages='+this.value,'_self','');">
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
                        </select>
                        <nav>
                        <ul class="pager">
                         <?php
                        if($limitFrom>0)
                        {
                                print "<li class='previous'><a aria-label='Previous' href='".$page_file."&Previous=1&pageing=".$previous.$link."'><span aria-hidden='true'>&larr;</span> Previous</a></li>";
                        }
                        else
                                print '<li class="previous disabled"><a href="#" onClick="return false;"><span aria-hidden="true">&larr;</span> Previous</a></li>';


                        $pageLink=$total/$display;
                         if(!is_int($pageLink))
                                $pageLink=$pageLink+1;
                        if($limitFrom+$display<$total)
                        {
                                print "<li class='next'><a aria-label='Next' href='".$page_file."&pageing=".$next.$link."'>Next <span aria-hidden='true'>&rarr;</span></a>";
                        }
                        else{
                            print '<li class="next disabled"><a href="#" onClick="return false;">Next <span aria-hidden="true">&rarr;</span></a></li>';
                        }
                    print "</ul>";
                    print "</nav></center><br /><br />";
                

                }
    }
        }
                        ?>