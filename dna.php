<?php
/*
    Plugin Name: SubscriptionDNA
    Plugin URI: http://SubscriptionDNA.com/wordpress/
    Description: Quickly integrate your website with your SubscriptionDNA Enterprise Subscription Billing and Members Management Platform account.
    Version: 2.0
    Author: SubscriptionDNA.com
    Author URI: http://SubscriptionDNA.com/
*/

/*
    Initialize
*/


$lifetime=25920000;
ini_set("session.cookie_lifetime",$lifetime);
session_start();
$device_id=@$_COOKIE["dna_device_id"];
if($device_id=="")
    $device_id=md5(time().session_id());
setcookie("dna_device_id",$device_id,time()+$lifetime,"/");
define("LIFETIME",360000);

error_reporting(E_ALL ^ E_NOTICE);
$GLOBALS['SubscriptionDNA'] = Array ( ) ;

remove_filter('the_content', 'wpautop',1 );
include("dna_widgets.php");
// Common Plugin Functions

/**
 * Initializes global variables used in plugin like DNA Front-End pages list,TLD, and api key
 *
 */
function SubscriptionDNA_Initialize ( )
{
        //include("type_flipbooks.php");    
	$GLOBALS['SubscriptionDNA']['DPages']=array();

        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"subscribe","title"=>"Subscribe","order"=>"1",'p'=>"");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"login","title"=>"Login","order"=>"2",'p'=>"");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"forgot-password","title"=>"Forgot Password","order"=>"3",'p'=>"login");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"members","title"=>"My Account","order"=>"4",'p'=>"");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"my-profile","title"=>"My Profile","order"=>"5",'p'=>"members");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"change-password","title"=>"Change Password","order"=>"6",'p'=>"members");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"manage-subscriptions","title"=>"Manage Subscriptions","order"=>"7",'p'=>"members");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"payment-methods","title"=>"My Payment Methods","order"=>"8",'p'=>"members");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"transactions","title"=>"My Transactions","order"=>"9",'p'=>"members");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"gift","title"=>"Gift","order"=>"10",'p'=>"");
        $GLOBALS['SubscriptionDNA']['DPages'][]=array("name"=>"groups","title"=>"My Group","order"=>"11",'p'=>"members");
        
        $GLOBALS['SubscriptionDNA']['Settings']= SubscriptionDNA_Get_Settings () ;
        $siteurl=get_option("siteurl");
        if($_SERVER["SERVER_PORT"]=="443")
        {
           $siteurl=  str_replace("http:", "https:", $siteurl);
        }
        $GLOBALS['SubscriptionDNA']["siteurl"]= $siteurl;
        $seruce_list = $GLOBALS['SubscriptionDNA']['Settings']['HTTPS'];
        if(is_array($seruce_list))
        {
            if($_SERVER["SERVER_PORT"] != "443")
            {
                $secure=false;
                foreach ($seruce_list as $secureurl)
                {
                    if (strpos($_SERVER['REQUEST_URI'], $secureurl) !== false)
                    {
                        $secure=true;
                    }
                }
                if($secure)
                {
                    $urlr = $GLOBALS['SubscriptionDNA']['Settings']["SSL"] . $_SERVER['REQUEST_URI'];
                    wp_redirect($urlr);
                    die();
                }
            }
        }

	$GLOBALS['SubscriptionDNA']['WSDL_URL']="http://".$GLOBALS['SubscriptionDNA']['Settings']["TLD"].".xsubscribe.com/soap/soapbridge/wsdl";

        if(isset($_REQUEST["dna_validate"]))
        {

            if($_REQUEST["dna_validate"]=="login_name")
            {
                $login_name = $_REQUEST['login_name'];
                $result=SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name),"user/loginname_availability",true);

                if ($result['errCode'] != 4)
                {
                    $msg = '<div class="lblErr">' . $result['errDesc'] . '</div>';
                }
                else
                {
                    $msg = '<div style="color:green">' . $result['errDesc'] . '</div>';
                }
            }
            else if($_REQUEST["dna_validate"]=="email")
            {
                $email = $_REQUEST['email'];
                $result=SubscriptionDNA_ProcessRequest(array("email"=>$email),"user/email_availability",true);
                if($result['errCode']!=5){
                        $msg='<div class="lblErr">'.$result['errDesc'].'</div>';
                }else{
                        $msg='<div style="color:green">'.$result['errDesc'].'</div>';
                }
            }
            else if($_REQUEST["dna_validate"]=="promo_code")
            {

                $newcost=$_REQUEST["selected_package_cost"];
                $newcostmsg="$".$_REQUEST["selected_package_cost"];
                $payment_info_not_required=0;
                $blocked_codes=array("blk1","blk2");
                if(!in_array($_REQUEST["promo_code"],$blocked_codes))
                {
                    list($service_id,$billing_routine)=split(";",$_REQUEST["selected_package"]);
                    $data=array("promo_code"=>$_REQUEST["promo_code"],"services"=>$service_id,"billing_routine_id"=>$billing_routine);
                     $promocode = SubscriptionDNA_ProcessRequest($data,"subscription/validate_promocode",true);
                     if($promocode["errCode"]<0)
                     {
                             $msg=$promocode["errDesc"].'';
                             $validCode="f";
                     }
                     else
                     {
                        if($promocode["discount_mod"]=="%")
                        {
                            $msg='You save '.$promocode["discount"].$promocode["discount_mod"].'';
                            $discount=$_REQUEST["selected_package_cost"]*($promocode["discount"]/100);
                            $newcost=$_REQUEST["selected_package_cost"]-$discount;
                            $newcost=number_format($newcost, 2);
                            $discount=number_format($discount, 2);
                            if($newcost<0)
                                $newcost=0;
                            $newcostmsg="<strike style='color:#b90000;'>$".$_REQUEST["selected_package_cost"]."</strike> - $".$discount." (".$promocode["discount"]."% discount) = $".$newcost;
                        }
                        elseif($promocode["discount_mod"]=="$")
                        {
                            $msg='You save $'.$promocode["discount"].'';
                            $newcost=$_REQUEST["selected_package_cost"]-$promocode["discount"];
                            if($newcost<0)
                                $newcost=0;
                            $promocode["discount"]=number_format($promocode["discount"], 2);
                            $newcostmsg="<strike style='color:#b90000;'>$".$_REQUEST["selected_package_cost"]."</strike> - $".$promocode["discount"]." ($".$promocode["discount"]." discount) = $".$newcost;
                        }
                        elseif($promocode["discount_mod"]=="b")
                        {
                            $msg=$promocode["billing"];
                            $newcost=$promocode["first_period_cost"];
                            $discount=$_REQUEST["selected_package_cost"]-$newcost;
                            $discount=number_format($discount, 2);
                            $newcostmsg="<strike style='color:#b90000;'>$".$_REQUEST["selected_package_cost"]."</strike> - $".$discount." ($".$discount." discount) = $".$newcost;
                            $payment_info_not_required=$promocode["payment_info_not_required"];
                        }
                         $validCode="t";
                     }

                     if($validCode=="t")
                     {
                        $msg="<span class='dna-success' style='color:green' >Discount Code Validated: ".$_REQUEST["promo_code"] ." - ".$msg."</span>";
                     }
                     else if($_REQUEST["promo_code"]!="")
                     {
                        $msg="Sorry, you've entered an invalid discount code: ".$_REQUEST["promo_code"];
                     }
                }
                else
                {
                     $msg=" Invalid discount code: ".$_REQUEST["promo_code"];
                }
                $msg=  json_encode(array("msg"=>$msg,"newcost"=>$newcost,"newcostmsg"=>$newcostmsg,"payment_info_not_required"=>$payment_info_not_required));
            }
            die($msg);
        }

        if($_REQUEST["DNA_Services"]=="1")
        {
            $serviceArray = SubscriptionDNA_ProcessRequest("","list/services",true);
            if($serviceArray["errCode"]=="-51")
            {
                $data=array();
                $data[0]=array("sId"=>"0","service_name"=>$serviceArray["errDesc"]);
            }
            else
            {
               $data=$serviceArray;
            }

            die(json_encode($data));
        }

        $current_page_id=url_to_postid($_SERVER["REQUEST_URI"]);  
        if($current_page_id>0 && in_array($current_page_id, $GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]))
        {
            SubscriptionDNA_GetFiles($current_page_id,"code");
        }
        else if(isset($_POST["dna_action_page"]))
        {
            include("code/".$_POST["dna_action_page"].".php");
        }
	return TRUE ;
}

function SubscriptionDNA_GetFiles($current_page_id,$f_type="code")
{
    $dna_pages=$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"];
    $page_vs_filename=array(
        $dna_pages["subscribe"]=>"register",
        $dna_pages["login"]=>"login",
        $dna_pages["forgot-password"]=>"forgot_pass",
        $dna_pages["members"]=>"home",
        $dna_pages["my-profile"]=>"myprofile",
        $dna_pages["change-password"]=>"mypass",
        $dna_pages["manage-subscriptions"]=>"mysubs",
        $dna_pages["payment-methods"]=>"mycards",
        $dna_pages["transactions"]=>"mytxns",
        $dna_pages["gift"]=>"gift",
        $dna_pages["groups"]=>"mygroup",
   );
   $file_name=$page_vs_filename[$current_page_id];
   if($file_name!="")
   {
        $base_path=dirname(__FILE__);
        if(file_exists($base_path."/custom/".$f_type."/".$file_name.".php"))
            include($base_path."/custom/".$f_type."/".$file_name.".php");
        else
            include($base_path."/".$f_type."/".$file_name.".php");
   }
}
function SubscriptionDNA_LoginValidate()
{
    if($_SESSION['login_name']=="")
    {
        wp_redirect(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])."?redirect_to=".$current_page_id);
        die();
    }
}


/**
 * Creates soap style xml using array of parameters
 *
 */
function SubscriptionDNA_ProcessRequest($data,$url="user/register",$assoc=false)
{
    if($GLOBALS['SubscriptionDNA']['Settings']['offline']=="1")
    {
        switch ($url)
        {
            case "user/login":
                $response='{
                "errCode": "1",
                "user_session_id": "e6vjcb8svfl86dc19mkh92sib6",
                "login_name":"'.$data["login_name"].'"
                }';
                break;
            case "subscription/check":
                $response='
                [
                    {
                    "service_id": "0fa23b22-7cb5-11e0-b9e9-001372fb8066",
                    "status": "Active"
                    }
                ]';
                break;
            case "login/check":
                $response='{
                    "errCode": 60,
                    "errDesc": "Logged in."
                    }';
                break;
            default :
                $response='{
                "errCode": "-100",
                "errDesc": "Data is temporarily unavailable."
                }';

        }

    }
    else
    {
        if($_SERVER['HTTP_HOST']=="localetech.com")
            $apihost="lahore";
        else    
            $apihost="xsubscribe";

        $endpoint="https://".$GLOBALS['SubscriptionDNA']['Settings']["TLD"].".".$apihost.".com/dna-rest/".$url;

        $session = curl_init($endpoint);
        $data=json_encode($data);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $data);
        curl_setopt($session, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'apikey:'.$GLOBALS['SubscriptionDNA']['Settings']['API_KEY'],
            'sessionid:'.@$_SESSION['user_session_id'],
            'userip:'.$_SERVER["REMOTE_ADDR"]
        );
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($session);
        curl_close($session);
    }
    $response = json_decode($response,$assoc);
    return($response);

}
/**
 * returns DNA settings from wordpress db like page menu,TLD and API Key
 *
 */
function SubscriptionDNA_Get_Settings()
{

	$Settings = get_option ('SubscriptionDNA_Settings') ;
        $Settings["dna_pages"]=get_option('SubscriptionDNA_Settings_DNAPages');
        if(isset($_POST["btnCreatePages"]))
        {
            $dna_pages=array();

            foreach ($GLOBALS['SubscriptionDNA']['DPages'] as $page)
            {
                $ID=SubscriptionDNA_CreatePage($page);
                $dna_pages[$page["name"]]=$ID;

                $dna_options=array("menu"=>"1","login"=>"1");
                if($page["name"]=="subscribe" || $page["name"]=="login" || $page["name"]=="forgot-password" || $page["name"]=="gift")
                {
                    $dna_options["menu"]="";
                    $dna_options["login"]="";
                }
                update_post_meta($ID, "_SubscriptionDNA", $dna_options);
            }
            update_option ('SubscriptionDNA_Settings_DNAPages', $dna_pages);
            $Settings["dna_pages"]=$dna_pages;
        }

	return $Settings ;

}




//DNA Front-End Functions

/**
 * Used to authenticate pages and posts using dna subscribed services and settings
 *
 */
function SubscriptionDNA_Get_Page_Content($Content)
{
	global $wpdb;

        $dna_options=get_post_meta($GLOBALS['post']->ID, "_SubscriptionDNA",true);
        if(is_page())
        {
            $memberpage=@$dna_options["login"];
            $menu=@$dna_options["menu"];
            if(@$dna_options["sub"]=="1")
                $memberpage="1";
        }
        else
        {
            $memberpage=get_post_meta($GLOBALS['post']->ID, "_SubscriptionDNA_yes", true);
            $menu=get_post_meta($GLOBALS['post']->ID, "_SubscriptionDNA_menu", true);
        }
        ob_start();
	$restrictedCats=get_option("SubscriptionDNA_cats");
	if(($memberpage or in_category($restrictedCats)) && (is_single() or is_page()) && $_SESSION['login_name']=="" && $GLOBALS['post']->ID!=$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'] && !is_home())
	{
		?>
		<style>
		body{display:none;}
		</style>
		<script>
		location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>?&redirect_to=<?php echo($GLOBALS['post']->ID);?>';
		</script>
		<?php
		exit;
	}
	if(is_single() && $GLOBALS['post']->post_type=="post" && $_SESSION['login_name']!="" and ($memberpage or in_category($restrictedCats)))
	{
		$allowed=true;
		if($memberpage)
		{
			$services=get_option('SubscriptionDNA_-_Settings_-_Post'.$GLOBALS['post']->ID);
			if(is_array($services))
			{
				$allowed=false;
				foreach($services as $val)
				{
					if(in_array($val,$_SESSION['subscribed_services']))
						$allowed=true;
				}
			}
		}
		else
		{
			if(!in_category($_SESSION['subscribed_categories']))
			$allowed=false;
		}
		if(!$allowed)
		{
			?>
			<style>
			body{display:none;}
			</style>
			<script>
			location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>';
			</script>
			<?php
			exit;
		}

	}
	if(is_page() && $GLOBALS['post']->post_type=="page" && @$dna_options["sub"]=="1")
	{
		$allowed=true;
                $services=$dna_options["services"];
                if(is_array($services))
                {
                    $allowed=false;
                    foreach($services as $val)
                    {
                        if($val=="all" && count($_SESSION['subscribed_services'])>0)
                        {
                            $allowed=true;
                            break;
                        }
                        else if(in_array($val,$_SESSION['subscribed_services']))
                        {
                           $allowed=true;
                           break;
                        }
                    }
                }
		if(!$allowed)
		{
			?>
			<style>
			body{display:none;}
			</style>
			<script>
			location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions'])); ?>';
			</script>
			<?php
			exit;
		}
	}

	if(!is_single() and $GLOBALS['post']->post_type=="post")
	{
		if($GLOBALS['SubscriptionDNA']['Settings']['Limit']>0)
		{
			if($GLOBALS['SubscriptionDNA']['Settings']['LimitOnly']=="1")
			{
				$allowed=true;
				if($memberpage)
				{
					$services=get_option('SubscriptionDNA_-_Settings_-_Post'.$GLOBALS['post']->ID);
					if(is_array($services))
					{
						$allowed=false;
						foreach($services as $val)
						{
							if(in_array($val,$_SESSION['subscribed_services']))
								$allowed=true;
						}
					}
				}
				else
				{
					if(in_category($restrictedCats))
					$allowed=false;
				}
				if(!$allowed)
				$Content=SubscriptionDNA_limit_text($Content,$GLOBALS['SubscriptionDNA']['Settings']['Limit']);
			}
			else
			{
				$Content=SubscriptionDNA_limit_text($Content,$GLOBALS['SubscriptionDNA']['Settings']['Limit']);
			}
		}
	}
	$Content.=ob_get_contents();
	ob_end_clean();
	return($Content);
}

/**
 * overwrides wordpres's the_time filter to display "member only" text for protected posts
 *
 */
function SubscriptionDNA_Get_Time ( $Content )
{
	if($GLOBALS['SubscriptionDNA']['Settings']['MemOnly']=="1")
	{
		$memberpage=get_post_meta($GLOBALS['post']->ID, "_SubscriptionDNA_yes", true);
		$restrictedCats=get_option("SubscriptionDNA_cats");
		if($memberpage or in_category($restrictedCats))
		return($Content." | Member Only");
	}
	return($Content);
}

/**
 * adds missing tags to the trimmed data , used to display limited text on posts
 *
 */
function SubscriptionDNA_closetags ( $html )
{
    #put all opened tags into an array
    preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
    $openedtags = $result[1];

    #put all closed tags into an array
    preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
    $closedtags = $result[1];
    $len_opened = count ( $openedtags );
    # all tags are closed
    if( count ( $closedtags ) == $len_opened )
    {
        return $html;
    }
    $openedtags = array_reverse ( $openedtags );
    # close tags
    for( $i = 0; $i < $len_opened; $i++ )
    {
        if ( !in_array ( $openedtags[$i], $closedtags ) )
        {
            $html .= "</" . $openedtags[$i] . ">";
        }
        else
        {
            unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
        }
    }
    return $html;
}

/**
 * used to limit text on posts
 *
 */
function SubscriptionDNA_limit_text($text, $limit)
{
	$text_in = strip_tags($text);
	$words = str_word_count($text_in, 2);
	$pos = array_keys($words);
	if(count($words) > $limit)
	{
			$one=$words[$pos[$limit-3]];
			$two=$words[$pos[$limit-2]];
			$three=$words[$pos[$limit-1]];
			$p=strpos($text,$one,$pos[$limit-3]);
			$p=strpos($text,$two,$p);
			$p=strpos($text,$three,$p)+strlen($three);

		if($_SESSION['login_name']!="")
		{
			if(!in_category($_SESSION['subscribed_categories']))
			{
				$text = SubscriptionDNA_closetags(substr($text, 0, $p)."..."). ' <a href="'.get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions']).'?&redirect_to='.$GLOBALS['post']->ID.'">Subscribe to Read More...</a><br><br>';
			}
		}
		else
		{
			$text = SubscriptionDNA_closetags(substr($text, 0, $p)."...") . ' <a href="'.get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login']).'?&redirect_to='.$GLOBALS['post']->ID.'">Login to Read More...</a><br><br>';
		}
	}
	else
	{
		if($_SESSION['login_name']!="")
		{
			if(!in_category($_SESSION['subscribed_categories']))
			{
				$text = $text."...".' <a href="'.get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['manage-subscriptions']).'?&redirect_to='.$GLOBALS['post']->ID.'">Subscribe to Read More...</a><br><br>';
			}
		}
		else
		{
			$text = $text."..." . ' <a href="'.get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login']).'?&redirect_to='.$GLOBALS['post']->ID.'">Login to Read More...</a><br><br>';
		}
	}
	return $text;
}

/**
 * displays a php file using short codes
 *
 */
function SubscriptionDNA_ShortCode_Page($file_name,$page_name)
{
    ob_start();
    $page_id=$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"][$page_name];
    $dna_options=get_post_meta($page_id, "_SubscriptionDNA",true);
   $base_path=dirname(__FILE__);
    
    if($dna_options["menu"]=="1")
    {
        if(file_exists($base_path."/custom/template/menu.php"))
             include($base_path."/custom/template/menu.php");
        else
             include($base_path."/template/menu.php");
    }
    
   if(file_exists($base_path."/custom/template/".$file_name.".php"))
        include($base_path."/custom/template/".$file_name.".php");
   else
        include($base_path."/template/".$file_name.".php");

    $contents=  ob_get_contents();
    ob_end_clean();
    return($contents);
}


/*
 Generic functions used to display shortcode page
*/
function SubscriptionDNA_Login($args="")
{
    return(SubscriptionDNA_ShortCode_Page("login","login"));
}
function SubscriptionDNA_Register($args="")
{
    return(SubscriptionDNA_ShortCode_Page("register","subscribe"));
}
function SubscriptionDNA_ForgotPassword($args="")
{
    return(SubscriptionDNA_ShortCode_Page("forgot_pass","forgot-password"));
}
function SubscriptionDNA_Members($args="")
{
    return(SubscriptionDNA_ShortCode_Page("home","members"));
}
function SubscriptionDNA_MyProfile($args="")
{
    return(SubscriptionDNA_ShortCode_Page("myprofile","my-profile"));
}
function SubscriptionDNA_ChangePassword($args="")
{
    return(SubscriptionDNA_ShortCode_Page("mypass","change-password"));
}
function SubscriptionDNA_ManageSub($args="")
{
    return(SubscriptionDNA_ShortCode_Page("mysubs","manage-subscriptions"));
}
function SubscriptionDNA_PaymentMethods($args="")
{
    return(SubscriptionDNA_ShortCode_Page("mycards","payment-methods"));
}
function SubscriptionDNA_Transactions($args="")
{
    return(SubscriptionDNA_ShortCode_Page("mytxns","transactions"));
}
function SubscriptionDNA_Gift($args="")
{
    return(SubscriptionDNA_ShortCode_Page("gift","gift"));
}
function SubscriptionDNA_Groups($args="")
{
    return(SubscriptionDNA_ShortCode_Page("mygroup","groups"));
}


/**
 * gets list of subscriptions for currently logged in user and saves it into session
 *
 */
function SubscriptionDNA_Update_Subscription()
{
	$categories = get_categories("hide_empty=0");
	$services=array();
	$servicesToCategories=array();
	foreach($categories as $key=>$Term)
	{
		$service_ids=get_option('SubscriptionDNA_-_Settings_-_Cat'.$Term->cat_ID);
		if(is_array($service_ids))
		{
			foreach($service_ids as $service_id)
			{
				if(!in_array($service_id,$services))
				$services[]=$service_id;
				if(!is_array($servicesToCategories[$service_id]))
				$servicesToCategories[$service_id]=array();
				if(!in_array($Term->cat_ID,$servicesToCategories[$service_id]))
				$servicesToCategories[$service_id][]=$Term->cat_ID;
			}
		}
	}
	$serviceArray=array();
	$serviceArray = SubscriptionDNA_ProcessRequest(array("login_name"=>$_SESSION['login_name']),"subscription/check");
	$activeCategories=array();
	$activeServices=array();
	foreach($serviceArray as $service)
	{
            if($service->status=="Active")
            {
                if(is_array($servicesToCategories[$service->service_id]))
                foreach($servicesToCategories[$service->service_id] as $key=>$val)
                $activeCategories[]=$val;

                $activeServices[]=$service->service_id;
                $_SESSION['subscription']="1";
            }
	}
	$_SESSION['subscribed_categories']=$activeCategories;
	$_SESSION['subscribed_services']=$activeServices;
}

/**
 * this function verifies if user's session is still valid on DNA side, and if not valid it redirects user to login page
 *
 */
function SubscriptionDNA_LoginCheck($result)
{
    if(is_object($result))
        $result=  get_object_vars ($result);
    if($result["errCode"]=="-61")
    {
        $_SESSION['user_session_id'] = "";
        $_SESSION["login_name"] = "";
        $_SESSION['password'] = "";
        $_SESSION['subscription']="";
        unset($_SESSION['user_session_id']);
        unset($_SESSION["login_name"]);
        unset($_SESSION["password"]);
        unset($_SESSION["subscription"]);
        ?>
        <script>
        location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>';
        </script>
        <?php
        exit;
    }
    else if($result["errCode"]=="-100")
    {
        echo($result["errDesc"]);
    }
}
/**
 * Overwrides wp's header filter to display any common css
 *
 */
function SubscriptionDNA_wp_head ( )
{

    $css_file="/template/styles.css";
    if(file_exists(dirname(__FILE__)."/custom/template/styles.css"))
        $css_file="/custom/template/styles.css";
            
?>
        <link rel="stylesheet" href='<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/css/bootstrap.min.css' /> <!-- minified Bootstrap  -->
	<link rel='stylesheet' href='<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna<?php echo($css_file); ?>' type='text/css'/>
        <?php
        if($GLOBALS['SubscriptionDNA']['Settings']['hidelinks']!="1")
        {
        ?>
        <div id="headerSection">      
            <div id="headerNav">
                <div id="topNav">
                    <ul>
                        <?php
                        if($_SESSION['login_name']!="")
                        {
                        ?>
                        <li>Welcome, <?php echo(stripcslashes($_SESSION['first_name'])); ?>!</li>
                        <li><div class="tnDiv">|</div></li>
                        <?php
                        }
                        ?>
                        <li><?php if($_SESSION['login_name']!=""){ ?><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>?&action=logout">Logout</a><?php }else{ ?><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>">Login</a><?php } ?></li>
                        <li><div class="tnDiv">|</div></li>
                        <li><a href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['members'])); ?>">My Account</a></li>
                    </ul>
                </div>
            </div>   
        </div>  
        <?php
        }
        if($_SESSION['login_name']!="")
        {
            ?>
            <style>
            #menu-item-125 {
            display: none;
            }
            <?php
            if($_SESSION['is_groupowner']!="1")
            {
                ?>
                #menu-item-128 {
                display: none;
                }
                <?php
            }
            ?>
            </style>
            <?php
        }
        else
        {
            ?>
            <style>
            #menu-item-124 {
            display: none;
            }
            </style>
            <?php
        }
	return TRUE ;

}
/**
 * Overwrides wp's footer filter to display any common footer code
 *
 */
function SubscriptionDNA_wp_footer ( )
{
    ?>
    
    <!-- Latest compiled and minified Bootstrap JavaScript -->
    <script src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/js/bootstrap.min.js"></script>
<?php
}



//DNA Admin Functions

/**
 * Overwrides wp's admin initialize function to dna meta boxes in pages/posts sidebars
 *
 */
function SubscriptionDNA_Admin_Initialize() {
	global $wp_version;
	add_meta_box('SubscriptionDNA_admin_meta_box','SubscriptionDNA Options', 'SubscriptionDNA_admin_sidebar', 'page', 'side', 'high');
	add_action('save_post', 'SubscriptionDNA_admin_sidebar_save');
	add_action ('edit_category_form', 'SubscriptionDNA_admin_category_add');


	if($_REQUEST["post_id"]!="")
	{
		//die($_REQUEST["status"]);
		update_post_meta($_REQUEST["post_id"], "_SubscriptionDNA_yes", $_REQUEST["status"]);
	}
	else if($_REQUEST["cat_id"]!="")
	{
		$cats=get_option("SubscriptionDNA_cats");
		if(is_array($cats))
		{
			if(!in_array($_REQUEST["cat_id"],$cats))
				$cats[]=$_REQUEST["cat_id"];
			else
			{
				foreach($cats as $key=>$val)
				{
					if($val==$_REQUEST["cat_id"])
					unset($cats[$key]);
				}
			}

		}
		else
		{
			$cats=array();
			$cats[]=$_REQUEST["cat_id"];
		}
		update_option("SubscriptionDNA_cats",$cats);


		wp_redirect("options-general.php?page=subscriptiondna/dna.php&manage_cats=1");
	}
}

/**
 *  Displays SubscriptionDNA modify category access link on add/edit categories page
 *
 */
function SubscriptionDNA_admin_category_add()
{
	?>
	<div id="memberOnly"><strong>After adding a new category, you can modify the access level using <a href="options-general.php?page=subscriptiondna/dna.php&manage_cats=1">SubscriptionDNA</a></strong></div>
	<?php
}

/**
 *  DNA's admin sidebar to display page access options
 *
 */
function SubscriptionDNA_admin_sidebar($post)
{
        $dna_secure_pages=array($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]["members"],$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]["my-profile"],$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]["change-password"],$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]["manage-subscriptions"],$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]["payment-methods"],$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]["transactions"],$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]["groups"]);
        $dna_options=get_post_meta($post->ID, "_SubscriptionDNA",true);
	if(in_array($post->ID, $dna_secure_pages))
        {
            ?>
            <div class="wrap">
                <table width="100%" class="form-table1" cellpadding="0" cellspacing="5" style="font-size: 11px;">

                <tr>
                <td><input type="checkbox" id="SubscriptionDNA_login" name="_SubscriptionDNA[login]" value="1"  checked onclick="this.checked=true;" /></td>
                <td align="left">Authenticate Login?</td>
                </tr>
                <tr>
                <td>
                <input type="checkbox" name="_SubscriptionDNA[menu]" value="1" <?php if($dna_options["menu"]) echo("checked"); ?> />
                <input type="hidden" name="SubscriptionDNA_form" value="1" />
                </td>
                <td align="left" style="font-size: 11px;">Show Member Menu?</td>
                </tr>
                </table>
            </div>
            <?php
        }
        else
        {
            if($dna_options["sub"]=="1")
            {
                $selected_services=$dna_options["services"];
                $serviceArray = SubscriptionDNA_ProcessRequest("","list/services",true);
                if($serviceArray["errCode"]!="-51")
                {
                   $data=$serviceArray;
                }

            }
            ?>
            <div class="wrap">
            <table width="100%" class="form-table1" cellpadding="0" cellspacing="5" style="font-size: 11px;">

            <tr>
            <td><input type="checkbox" id="SubscriptionDNA_login" name="_SubscriptionDNA[login]" value="1"  <?php if($dna_options["sub"]) echo("checked disabled"); ?>  <?php if($dna_options["login"]) echo("checked"); ?> /></td>
            <td align="left">Authenticate Login?</td>
            </tr>

            <tr>
            <td><input type="checkbox" name="_SubscriptionDNA[sub]" value="1" <?php if($dna_options["sub"]) echo("checked"); ?> onclick="DNA_LoadServices(this.checked);" /></td>
            <td align="left" style="font-size: 11px;">Authenticate Subscription?</td>
            </tr>
            <tr id="SubscriptionDNA_services" style="<?php if($dna_options["sub"]!="1"){ ?>display:none<?php } ?>">
                <td colspan="2">
                    <div style="margin-left: 10px;">Authorized Services:</div>
                    <div style="margin-left: 30px;" id="SubscriptionDNA_included_services"><?php
                    if(count($data)>0)
                    {
                        ?><input type="checkbox" name="_SubscriptionDNA[services][]" value="all" style="margin-left:-20px;" <?php if(in_array("all",$selected_services))echo("checked"); ?>>All Services<br /><?php
                        foreach($data as $service)
                        {
                            ?>
                            <input type="checkbox"  name="_SubscriptionDNA[services][]" value="<?php echo($service["sId"]); ?>" style="margin-left:-20px;" <?php if(in_array($service["sId"],$selected_services))echo("checked"); ?>><?php echo($service["service_name"]); ?><br />
                            <?php
                        }
                    }
                    ?></div>
                </td>
            </tr>
            <tr>
            <td>
            <input type="checkbox" name="_SubscriptionDNA[menu]" value="1" <?php if($dna_options["menu"]) echo("checked"); ?> />
            <input type="hidden" name="SubscriptionDNA_form" value="1" />
            </td>
            <td align="left" style="font-size: 11px;">Show Member Menu?</td>
            </tr>
            </table>
            </div>
            <script>
            window.onload=test;
            function test()
            {
                    try{
                            document.getElementById('category-ajax-response').innerHTML='<div id="memberOnly"><strong>After adding category you can make is "Member Only" from category listing page</strong></div>';
                    }catch(ERROR){}
            }
            function DNA_LoadServices(chk)
            {
                if(chk)
                {
                    document.getElementById("SubscriptionDNA_services").style.display="";
                    document.getElementById("SubscriptionDNA_login").checked=true;
                    document.getElementById("SubscriptionDNA_login").disabled=true;
                    if(document.getElementById("SubscriptionDNA_included_services").innerHTML=="")
                    {
                        document.getElementById("SubscriptionDNA_included_services").innerHTML="Loading..";
                        jQuery.ajax({
                        type: "GET",
                        url: "<?php echo(get_option("siteurl"));?>?DNA_Services=1",
                        }).done(function( msg ) {
                            data = jQuery.parseJSON(msg);
                            services='<input type="checkbox" name="_SubscriptionDNA[services][]" value="all" style="margin-left:-20px;">All Services<br />';
                            jQuery.each( data, function( key, val )
                            {
                                services+='<input type="checkbox"  name="_SubscriptionDNA[services][]" value="'+val.sId+'" style="margin-left:-20px;">'+val.service_name+"<br />";
                            });
                            document.getElementById("SubscriptionDNA_included_services").innerHTML=services;
                        });
                    }
                }
                else
                {
                    document.getElementById("SubscriptionDNA_login").disabled=false;
                    document.getElementById("SubscriptionDNA_services").style.display="none";
                }

            }
            </script>
            <?php
        }
}

/**
 *  Saves DNA sidebar settings for pages/posts
 *
 */
function SubscriptionDNA_admin_sidebar_save($post_id)
{
	if(@$_REQUEST["SubscriptionDNA_form"]=="1")
	{
		update_post_meta($post_id, "_SubscriptionDNA", $_REQUEST["_SubscriptionDNA"]);
	}
}

/**
 *  Display SubscriptionDNA link in wp menu
 *
 */
function SubscriptionDNA_admin_menu ( )
{

    add_menu_page( 'SubscriptionDNA', 'SubscriptionDNA', 0, __FILE__,'SubscriptionDNA_Options_Edit');
    add_submenu_page(__FILE__,__('Settings','SubscriptionDNA'),__('Configuration','SubscriptionDNA'), 10, __FILE__,'SubscriptionDNA_Options_Edit');
    //add_submenu_page(__FILE__,__("FlipBook",'SubscriptionDNA'),__("Manage FlipBooks",'SubscriptionDNA'),10,"edit.php?post_type=dna-flipbooks");
    return TRUE ;

}


/**
 *  Auto creates dna related member pages
 *
 */
function SubscriptionDNA_CreatePage($page)
{
    global $wpdb;
    $existing=$wpdb->get_row("select ID,post_status from $wpdb->posts where `post_title`='".$page["title"]."'");
        if($existing->ID>0)
        {
            return($existing->ID);
        }
        else
        {
            if($page["p"]=="")
            {
                $parent=0;
            }
            else
            {
                $p_post=$wpdb->get_row("select ID from $wpdb->posts where `post_title`='".$page["p"]."'");
                $parent= $p_post->ID;
            }
            $Array = Array
            (
                'post_date'             => date   ( 'Y-m-d H:i:s' ) ,
                'post_date_gmt'         => gmdate ( 'Y-m-d H:i:s' ) ,
                'post_content'          => '[subscriptiondna-'.$page["name"].']',
                'post_title'            => $page["title"]           ,
                'post_excerpt'          => ''                       ,
                'post_status'           => 'publish'                ,
                'comment_status'        => 'closed'                 ,
                'ping_status'           => 'closed'                 ,
                'post_password'         => ''                       ,
                'post_name'             => $page["name"]   ,
                'to_ping'               => ''                       ,
                'pinged'                => ''                       ,
                'post_modified'         => date   ( 'Y-m-d H:i:s' ) ,
                'post_modified_gmt'     => gmdate ( 'Y-m-d H:i:s' ) ,
                'post_content_filtered' => ''                       ,
                'post_parent'           => (int)$parent                        ,
                'guid'                  => ''                       ,
                'menu_order'            => $page["order"]                   ,
                'post_type'             => 'page'                   ,
                'post_mime_type'        => ''                       ,
                'comment_count'         => 0                        ,
            ) ;
            $ID=wp_insert_post($Array);
            return($ID);
        }
}
/**
 *  Displays form to edit DNA settings like API Key, TLD and page SSL settings
 *
 */
function SubscriptionDNA_Options_Edit ( )
{
	if ( !empty ( $_POST['action'] ) AND 'update' == $_POST['action'] )
	{
		$Aarzi = SubscriptionDNA_Options_Save ( ) ;
	}

?>
<div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
	<h2><?php echo __ ('SubscriptionDNA' ) ; ?></h2>

	<br>
	<div style="font-size:12px" align="center">
		<a href="http://www.SubscriptionDNA.com" target="_blank"><img width="200" src="http://www.subscriptiondna.com/wp-content/uploads/2014/10/SubscriptionDNA-512.png" border="0"></a><br>
		<br>
		<a href="options-general.php?page=subscriptiondna/dna.php">View/Edit Settings</a>&nbsp;|
		<a href="options-general.php?page=subscriptiondna/dna.php&view_short=1">Short Codes</a>&nbsp;|
		<a href="options-general.php?page=subscriptiondna/dna.php&manage_cats=1">Category/Post Access Settings</a>&nbsp;|
		<a href="http://www.subscriptiondna.com/wordpress/" target="_blank">Plugin Information</a>&nbsp;|
		<a href="http://www.subscriptiondna.com/contact/" target="_blank">Contact Us</a>
	</div>
	<br>
	<br>
	<?php

	if($_REQUEST["manage_cats"]=="1")
	{
		include dirname(__FILE__).'/categories.php';
	}
	else if($_REQUEST["view_short"]=="1")
	{

            ?>
            <fieldset class="options">
                <legend></legend>
                <div id="poststuff" class="metabox-holder has-right-sidebar">
                    <div id="tagsdiv-post_tag" class="postbox">
                        <h3 class='hndle'><span>SubscriptionDNA Possible short codes</span></h3>
                        <link rel='stylesheet' href='styles.css' type='text/css' media='all' />
                        <div class="inside">
            [subscriptiondna-login]  SubscriptionDNA Login Page<br><br>
            [subscriptiondna-subscribe]  SubscriptionDNA  Registration page<br><br>
            [subscriptiondna-forgot-password]  SubscriptionDNA  forgot password page<br><br>
            [subscriptiondna-members]  SubscriptionDNA  members home page<br><br>
            [subscriptiondna-my-profile]  SubscriptionDNA  user profile management page<br><br>
            [subscriptiondna-change-password]  SubscriptionDNA  change password page<br><br>
            [subscriptiondna-manage-subscriptions]  SubscriptionDNA  manage subscriptions page<br><br>
            [subscriptiondna-payment-methods]  SubscriptionDNA  manage credit cards list<br><br>
            [subscriptiondna-transactions]                      SubscriptionDNA  transactions listing page<br><br>
            [subscriptiondna-groups]                      SubscriptionDNA  groups management page<br><br>
            [subscriptiondna-gift]                      SubscriptionDNA  gift subscription page<br><br>

                        </div>
                    </div>
                </div>
            </fieldset>
            <?php
	}
	else if($_REQUEST["manage_posts"]=="1")
	{
		include dirname(__FILE__).'/posts.php';
	}
	else
	{
	?>
		<form action="options-general.php?page=<?php echo $_GET['page'] ; ?>" method="post">
		<fieldset class="options">
		<legend></legend>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div id="tagsdiv-post_tag" class="postbox">
		<h3 class='hndle'><span>Subscription DNA Basic Plugin Settings</span></h3>

                <div style="padding: 25px;">
                <p>
                <input type="checkbox" name="SubscriptionDNA_Settings[offline]" value="1" <?php if($GLOBALS['SubscriptionDNA']['Settings']['offline']=="1") echo("checked") ; ?>  /><b>Work Offline:</b>
                <br />
                (if checked login and subscription API will return true without communicating to SubscriptionDNA)
                </p>
                <p>
                <input type="checkbox" name="SubscriptionDNA_Settings[hidelinks]" value="1" <?php if($GLOBALS['SubscriptionDNA']['Settings']['hidelinks']=="1") echo("checked") ; ?>  /><b>Hide Top Member Links:</b>
                <br />
                (if checked Login and MyAccount Links will not show on top right corner)
                </p><br>

                <b>DNA Account TLD:</b><br />
                <input type="text" name="SubscriptionDNA_Settings[TLD]" value="<?php echo($GLOBALS['SubscriptionDNA']['Settings']['TLD']) ; ?>" style="width:300px;" /><br />
                (ex: If your account URL is https://demo.xsubscribe.com, then "demo" is your TLD)

                <p>
                <b>API KEY:</b><br />
                <input  type="text" name="SubscriptionDNA_Settings[API_KEY]" value="<?php echo($GLOBALS['SubscriptionDNA']['Settings']['API_KEY']) ; ?>" style="width:300px;" /><br />
                (ex: API KEY is found on Configurations page in DNA portal )

                <!--
                                <?php
                                if(!is_array($GLOBALS['SubscriptionDNA']['Settings']['HTTPS']))
                                    $GLOBALS['SubscriptionDNA']['Settings']['HTTPS']=array();
                                foreach ($GLOBALS['SubscriptionDNA']['DPages'] as $page)
                                {
                                ?>
                                <tr>
                                <th scope="row"><div align="right"><?php echo($page["title"]);?> Page:</div></th>
                                <td class="dna-small">
                                &nbsp; &nbsp; &nbsp; <input type="checkbox" name="SubscriptionDNA_Settings[HTTPS][]" value="<?php echo($page["name"]); ?>" <?php if(in_array($page["name"],$GLOBALS['SubscriptionDNA']['Settings']['HTTPS']))echo("checked"); ?>> Use SSL?
                                </td>
                                </tr>
                                <?php
                                }
                                ?>


                                <tr>
                                <th scope="row"><div align="right">SSL URL:</div></th>
                                <td class="dna-small"><input type="text" name="SubscriptionDNA_Settings[SSL]" value="<?php echo($GLOBALS['SubscriptionDNA']['Settings']['SSL']) ; ?>" style="width:300px;" />
                                </tr>
                -->

                <p>
                <hr />

                <p>
                <input type="checkbox" name="SubscriptionDNA_Settings[Extra]" value="1" <?php if($GLOBALS['SubscriptionDNA']['Settings']['Extra']=="1")echo("checked") ; ?> /> <b>Display Custom Fields?</b><br>
                (Includes Custom Fields on Registration Signup and My Profile)

                <p>
                <b>Member Home Redirect:</b><br />
                <input type="text" name="SubscriptionDNA_Settings[mem_url]"   style="width:300px;" value="<?php echo($GLOBALS['SubscriptionDNA']['Settings']['mem_url']) ; ?>"  /><br />
                (Optionally redirect active subscription logins to this URL)

                </div>

		<h3 class='hndle'><span>Display Limits on Secure Posts</span></h3>

<div style="padding: 25px;">

<input type="checkbox" name="SubscriptionDNA_Settings[LimitOnly]" value="1" <?php if($GLOBALS['SubscriptionDNA']['Settings']['LimitOnly']=="1")echo("checked") ; ?> /> <b>Limit Access to "Member Only" Posts?</b><br />
(Applies limit to secure posts only)

<p>
<input type="text" name="SubscriptionDNA_Settings[Limit]" value="<?php echo($GLOBALS['SubscriptionDNA']['Settings']['Limit']) ; ?>" style="width:40px;" /> <b>Limit length of secure post summary on listing page?</b><br />
(Enter number of words before truncating the post.  0 = Unlimited)

<p>
<input type="checkbox" name="SubscriptionDNA_Settings[MemOnly]" value="1" <?php if($GLOBALS['SubscriptionDNA']['Settings']['MemOnly']=="1")echo("checked") ; ?> /> <b>Display "Member Only" Label?</b>


</div>

		</div>
		</div>
		</fieldset>

		<p class="submit">
		<input type="hidden" name="butSwitchValue" id="butSwitchValue" value="text"            />
		<input type="hidden" name="action" value="update"                                 />
		<input type="submit" name="submit" value="<?php echo __ ( 'Update Settings &raquo;' ) ; ?>"/>
		<input type="submit" name="btnCreatePages" value="<?php echo __ ( 'Generate Default DNA Member Pages &raquo;' ) ; ?>"/>
        </p>
		</form>
	<?php
	}
	?>
</div>
<?php

return TRUE ;

}

/**
 *  Saves DNA settings like API Key, TLD and page SSL settings
 *
 */
function SubscriptionDNA_Options_Save ( )
{


	update_option ( 'SubscriptionDNA_Settings', $_POST['SubscriptionDNA_Settings']) ;

	$GLOBALS['SubscriptionDNA']['Settings'] = SubscriptionDNA_Get_Settings () ;


?>

    <div id="message" class="updated fade">
        <p><strong><?php echo __ ( 'The options were saved successfully.' ) ; ?></strong></p>
    </div>

<?php

return TRUE ;

}

/*
Misc. Plugin Setup Code
*/

function SubscriptionDNA_GetProvinces()
{
    $canada_provinces=array("Alberta"=>"AB","British Columbia"=>"BC","Manitoba"=>"MB","New Brunswick"=>"NB","Newfoundland and Labrador"=>"NL","Northwest Territories"=>"NT","Nova Scotia"=>"NS","Nunavut"=>"NU","Ontario"=>"ON","Prince Edward Island"=>"PE","Quebec"=>"QC","Saskatchewan"=>"SK","Yukon"=>"YT");
    return($canada_provinces);
}


if ( function_exists ( 'add_action' ) )
{
	$Aarzi = add_action ( 'init' , 'SubscriptionDNA_Initialize' ) ;
	add_action('admin_init', 'SubscriptionDNA_Admin_Initialize');
}


if ( function_exists ( 'add_action' ) )
{

	$Aarzi = add_action ( 'admin_menu' , 'SubscriptionDNA_admin_menu' ) ;
	$Aarzi = add_action ( 'wp_head' , 'SubscriptionDNA_wp_head' ) ;
	$Aarzi = add_action(  'wp_footer', 'SubscriptionDNA_wp_footer');

        add_action ( 'the_excerpt' , 'SubscriptionDNA_Get_Page_Content' , 10 ) ;
	add_action ( 'the_content' , 'SubscriptionDNA_Get_Page_Content' , 10 ) ;

	add_action ( 'the_time' , 'SubscriptionDNA_Get_Time' , 10 ) ;
}
add_shortcode("subscriptiondna-login" , "SubscriptionDNA_Login");
add_shortcode("subscriptiondna-subscribe" , "SubscriptionDNA_Register");
add_shortcode("subscriptiondna-forgot-password" , "SubscriptionDNA_ForgotPassword");
add_shortcode("subscriptiondna-members" , "SubscriptionDNA_Members");
add_shortcode("subscriptiondna-my-profile" , "SubscriptionDNA_MyProfile");
add_shortcode("subscriptiondna-change-password" , "SubscriptionDNA_ChangePassword");
add_shortcode("subscriptiondna-manage-subscriptions" , "SubscriptionDNA_ManageSub");
add_shortcode("subscriptiondna-payment-methods" , "SubscriptionDNA_PaymentMethods");
add_shortcode("subscriptiondna-transactions" , "SubscriptionDNA_Transactions");
add_shortcode("subscriptiondna-gift" , "SubscriptionDNA_Gift");
add_shortcode("subscriptiondna-groups" , "SubscriptionDNA_Groups");

?>