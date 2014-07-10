<?php
	$categories = get_categories("hide_empty=0");
	if($_POST["cmdServices"])
	{
		foreach($categories as $key=>$Term) 
		{
			update_option('SubscriptionDNA_-_Settings_-_Cat'.$Term->cat_ID, $_POST["service_id_".$Term->cat_ID]) ;
		}
	}
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
?>
<!--<div id="icon-edit" class="icon32"></div>-->
<h3>Manage Member Access to Secure Posts and Post Categories</h3>
Your members must have a valid active subscription to one of the assigned services to access that post or category of posts. It is also possible to modify this to require active subscription to all the assigned services. Click the link to quickly switch from Member Only to Public access.

<p>
<form  id="SubscriptionDNA_list_form" name="SubscriptionDNA_list_form" method="post" innerAction=""> 

    <table class="widefat post" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="manage-column" id="cat_desc" scope="col" >Category</th>
                <!--
                <th class="manage-column" id="cat_desc" scope="col" >Description</th>
                <th class="manage-column" id="cat_desc" scope="col" >Slug</th>
                -->
                <th class="manage-column" id="cat_desc" scope="col" >Category Access</th>
                <th class="manage-column" id="cat_desc" scope="col" >Post Access</th>
                <th class="manage-column" id="cat_desc" scope="col" >Subscribed Services</th>
            </tr>
        </thead>

<!--
        <tfoot>

            <tr>
                <th class="manage-column" id="cat_desc" scope="col" >Name</th>
                <th class="manage-column" id="cat_desc" scope="col" >Description</th>
                <th class="manage-column" id="cat_desc" scope="col" >Slug</th>
                <th class="manage-column" id="cat_desc" scope="col" >Category Access</th>
                <th class="manage-column" id="cat_desc" scope="col" >Posts Access</th>
                <th class="manage-column" id="cat_desc"  scope="col" >Services</th>
            </tr>

        </tfoot>
-->
        <tbody>

<?php 

if($categories) {

                foreach($categories as $key=>$Term) {
					$cats=get_option("SubscriptionDNA_cats");
					$checked="Public";
					$posts='<a href="options-general.php?page=subscriptiondna/dna.php&manage_posts=1&post_cat_id='.$Term->cat_ID.'">Posts Access</a>';
					if(is_array($cats))
					{
						if(in_array($Term->cat_ID,$cats))
						{
							$checked="Member Only";
							$posts="Member Only";
						}	
					}
					$cat='<a href="options-general.php?page=subscriptiondna/dna.php&manage_cats=1&cat_id='.$Term->cat_ID.'">'.$checked.'</a>';

                    $Class = ( 'alternate' == $Class ) ? '' : 'alternate' ;
					
					$services=get_option('SubscriptionDNA_-_Settings_-_Cat'.$Term->cat_ID);
					if(!is_array($services))
						$services=array();
					?>

            <tr  id="cat-<?php echo $Term->cat_ID ; ?>" class="<?php echo $Class ; ?>">
                <td>
        <?php echo $Term->name ; ?>
                </td>
               <!--
                <td class="manage-column">
				<?php echo $Term->category_description ; ?>                             
                </td>
                <td class="manage-column">
	         <?php echo $Term->slug ; ?>
                </td>
                -->
                <td class="manage-column">
		        <?php echo $cat ; ?>
                </td>
                <td class="manage-column">
		        <?php echo $posts ; ?>
				<?php
				if($checked=="Public")
				{
					$cc=0;
					$defaults["category"]=$Term->cat_ID;
					$c_posts = get_posts($defaults);
					foreach($c_posts as $c_post)
					{
						$memberpage=get_post_meta($c_post->ID, "_SubscriptionDNA_yes", true);
						if($memberpage)
						$cc++;
					}
					echo("($cc currently set)");
				}	
				?>
                </td>
                <td>
				<?php
				if($checked=="Member Only")
				{
				?>
					<select name="service_id_<?php echo $Term->cat_ID ; ?>[]" id="service_id_<?php echo $Term->cat_ID ; ?>" multiple="multiple" size="4" style="height:80px;">
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

</form>
<?php
if (count( $categories )<1 ) 
{
	echo '<p>There are no categories in the database.</p>' ;
}
?> 
