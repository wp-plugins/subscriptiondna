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
    if($_SESSION['is_groupowner']=="1")
    {
        echo("<hr>");
        $members=SubscriptionDNA_ProcessRequest(array("login_name"=>$_SESSION['login_name']),"group/members_list",true);
        $added_members=count($members);
        if ($added_members < 1)
        {
            
            $groups_link=get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['groups']);
            ?>
                    <div><a title="Add Members" href="<?php echo($groups_link); ?>?&save_mem_info=1" class="green-button" style="font-size: 20px; ">Click here to start adding your group members now!</a></div><br>
            <?php
        }
        $signup_link=get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['subscribe'])."?sub_group=".$_SESSION['group_id'];
        ?>
        
        Use this link to invite your group members to join your group.<br>
        <a target="_blank" href="<?php echo($signup_link); ?>"><?php echo($signup_link); ?></a>
        <?php
    }
?>

