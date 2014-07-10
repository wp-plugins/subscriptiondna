<?php
$subscriptions = SubscriptionDNA_ProcessRequest(array("login_name"=>$_SESSION['login_name']),"subscription/list");
SubscriptionDNA_LoginCheck($subscriptions);
if (count($subscriptions) < 1)
{
    echo '&nbsp;&nbsp;<font color="#FF0000">No Subscriptions Found.</font><br />';
}
else
{
    ?>
    <div id="dna-heading-sub"><br>You are subscribed to the following services:</div>

    <div style="border: solid 1px gray; padding: 4px; background-color: #ffffff;">
        <?php
        foreach ($subscriptions as $subscription)
        {
            if (!$subscription)
                break;
            
            
            
            
            
            // Messages for members
            
            if($_SESSION['is_groupmember']=="1")
            {
                if($subscription->status=="Active")
                {
                    ?>
                     You have an active subscription to <b><?php echo($subscription->service_name); ?></b>.<br>
                    <?php
                }
                else
                {
                    ?>
                        <div class="red">
                        Your subscription to <b><?php echo($subscription->service_name); ?></b> is no longer valid as of <?php echo(substr($subscription->expires, 0, 10)); ?>. 

                        <?php if($_SESSION['is_groupmember'] == 1){?>
                        For more information about your subscription , please contact <?php echo($_SESSION['group_first_name']." ".$_SESSION['group_last_name']); ?> at <?php echo($_SESSION['group_phone']); ?> or via email to <a href="mailto:<?php echo($_SESSION['group_email']); ?>"><?php echo($_SESSION['group_email']); ?></a>.<br />
                        <?php } ?>

                         </div>
                    <?php
                }
            }
            else  
            {
                
            // Messages for group owners are normal users
            ?>
                <!--
                Use This ID to map services with contents
            <?php echo($subscription->subid); ?>
                -->
                <strong><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>"><?php echo($subscription->service_name); ?></a></strong><br />
                <?php if ($subscription->service_description != "")
                {
                    echo($subscription->service_description);
                    echo "<br>";
                } ?>
                <?php if ($subscription->billing_description != "")
                {
                    echo "<i>";
                    echo($subscription->billing_description);
                    echo "</i><br />";
                } ?>

            <!--			<?php echo($subscription->package_description); ?><br />-->

                <br>
            <?php
            }
            
            
            
            
        
    }
    ?>
    </div>
        <?php
    }

    $notes = SubscriptionDNA_ProcessRequest(array("login_name"=>$_SESSION['login_name']),"list/notes_reminders");
    if (count($notes) > 0)
    {
    ?>

    <div id="dna-packages">
    <?php
    foreach($notes as $note)
    {
        echo("" . $note->on_date . "");
        echo("<h3>" . $note->name . "</h3>");
        echo($note->description . "");
    }
    
    ?>
    </div>

<?php 
    }
?>

