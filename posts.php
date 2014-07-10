<?php
$serviceArray=array();
$serviceArray = SubscriptionDNA_ProcessRequest("","list/services",true);
if($serviceArray["errCode"]=="-51")
{
	echo("Error getting services list: ".$serviceArray["errDesc"]."<br>");	
	$serviceArray=array();
}

$defaults = array(
	'numberposts' => 1115, 'offset' => 0,
	'category' => $_REQUEST["post_cat_id"], 'orderby' => 'post_date',
	'order' => 'DESC', 'include' => '',
	'exclude' => '', 'meta_key' => '',
	'meta_value' =>'', 'post_type' => 'post',
	'suppress_filters' => true
);
$posts = get_posts($defaults);

if($_POST["cmdServices"])
{
	foreach($posts as $key=>$Term) 
	{
		update_option('SubscriptionDNA_-_Settings_-_Post'.$Term->ID, $_POST["service_id_".$Term->ID]) ;
	}
}

?>
<!--<div id="icon-edit" class="icon32"><br /></div>-->

<h3>Manage Member Access to Secure Posts and Post Categories</h3>
Your members must have a valid active subscription to one of the assigned services to access that post or category of posts. It is also possible to modify this to require active subscription to all the assigned services. Click the link to quickly switch from Member Only to Public access.

<p>
<form  id="SubscriptionDNA_list_form" name="SubscriptionDNA_list_form" method="post" innerAction=""> 
    <table class="widefat post fixed" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="manage-column" id="cat_desc" scope="col" >Post Title</th>
                <th class="manage-column" id="cat_desc" scope="col" >Posts Access Level</th>
                <th class="manage-column" id="cat_desc" scope="col" >Subscribed Services</th>
            </tr>
        </thead>

        <!--
        <tfoot>

            <tr>
                <th class="manage-column" id="cat_desc" scope="col" >Title</th>
                <th class="manage-column" id="cat_desc" scope="col" >Posts Access Level</th>
                <th class="manage-column" id="cat_desc" scope="col" >Services</th>
            </tr>

        </tfoot>
        -->

        <tbody>

<?php 

if($posts) {

                foreach($posts as $key=>$Term) {
					$memberpost=get_post_meta($Term->ID, "_SubscriptionDNA_yes", true);
					$checked="Public";
					if($memberpost)
					{
						$checked="Member Only";
						$status="";
					}
					else
						$status="yes";

					$cat='<a href="options-general.php?page=subscriptiondna/dna.php&manage_posts=1&post_cat_id='.$_REQUEST["post_cat_id"].'&post_id='.$Term->ID.'&status='.$status.'">'.$checked.'</a>';

                    $Class = ( 'alternate' == $Class ) ? '' : 'alternate' ;
					
					$services=get_option('SubscriptionDNA_-_Settings_-_Post'.$Term->ID);
					if(!is_array($services))
						$services=array();
					
					?>

            <tr  id="cat-<?php echo $Term->ID ; ?>" class="<?php echo $Class ; ?>">
                <td>
      			 <?php echo $Term->post_title ; ?>
                </td>
                <td class="manage-column">
		        <?php echo $cat ; ?>
                </td>
                <td>
				<?php
				if($checked=="Member Only")
				{
				?>
					<select name="service_id_<?php echo $Term->ID ; ?>[]" id="service_id_<?php echo $Term->ID ; ?>" multiple="multiple" size="4" style="height:80px;">
					<option value=""></option>
					<? 
					foreach($serviceArray as $v)
					{
						?>
						<option label="<?=$v["service_name"]; ?>" value="<?=$v["sId"] ?>"  <?php if(in_array($v["sId"],$services))echo("selected"); ?>><?=$v["service_name"]; ?></option>
						<? 
					}
					?>
					</select>
				<?php
				}
				else
				echo("None");
				?>	
					
                </td>

            </tr>
        <?php
    }
}

            //end loop here
            ?>

        </tbody>
    </table>
<br />
<input type="submit" value="Save Service Access Settings" name="cmdServices" id="cmdServices" class="button-secondary action" />
<input type="button" value="Back" onclick="location.href='options-general.php?page=subscriptiondna/dna.php&manage_cats=1';" name="cmdServices" id="cmdServices" class="button-secondary action" />
</form>

<?php
if (count( $posts )<1 ) 
{
	echo '<p>There are no categories in the database.</p>' ;
}
?> 
