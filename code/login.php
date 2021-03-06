<?php
if($_POST["cmdLogin"])
{

        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $data=array(
            "login_name"=>$_POST['login_name'],
            "password"=>$_POST['password'],
            "check_subscription"=>0,
            "return_group_info"=>"1",
            "device_id"=>$_COOKIE["dna_device_id"],
            "user_agent"=>$_SERVER["HTTP_USER_AGENT"],
            "reset"=>$_REQUEST["reset_devices"]);
        $result = SubscriptionDNA_ProcessRequest($data, "user/login");

        if($result->errCode == 1)
        {
                $_SESSION['user_session_id'] = $result->user_session_id;
                $_SESSION['login_name'] = $result->login_name;
                $_SESSION['password'] = $_POST['password'];

                SubscriptionDNA_Update_Subscription();

                $profile = SubscriptionDNA_ProcessRequest(array("login_name"=>$_SESSION['login_name']),"user/profile");
                $_SESSION['first_name']=$profile->first_name;
                $_SESSION['last_name']=$profile->last_name;

                $_SESSION['is_groupowner']=$profile->is_groupowner;
                $_SESSION['is_groupmember']=$profile->is_groupmember;
                $_SESSION['group_id']=$profile->uid;
                $_SESSION['paid_by_owner']=$profile->paid_by_owner;

                $_SESSION['group_first_name']=$profile->group_first_name;
                $_SESSION['group_last_name']=$profile->group_last_name;
                $_SESSION['group_email']=$profile->group_email;
                $_SESSION['group_phone']=$profile->group_phone;
                if($_REQUEST["redirect_to"]!="")
                        $url=get_permalink($_REQUEST["redirect_to"]);
                else if($_SESSION['subscription']=="1" && $GLOBALS['SubscriptionDNA']['Settings']['mem_url']!="")
                        $url=$GLOBALS['SubscriptionDNA']['Settings']['mem_url'];
                else
                        $url=get_permalink($GLOBALS['SubscriptionDNA']['Settings']['dna_pages']["members"]);

                wp_redirect($url);
                die("redirecting..");
        }
        else
        {
            $GLOBALS["dna_result"]=$result;
            $_REQUEST["dna_message"]="<div id='dna-login'><div id='failure'><div class='alert alert-danger' role='alert'>" . $result->errDesc . " Please try again.</div></div></div>";
        }

}
else if($_REQUEST["action"]=="logout")
{
		$session_id = $_SESSION['user_session_id'];

		$result = SubscriptionDNA_ProcessRequest("","user/logout");

		//Destory Session
		$_SESSION['user_session_id'] = "";
		$_SESSION["login_name"] = "";
		$_SESSION['password'] = "";
		$_SESSION['subscribed_categories']="";
		$_SESSION['subscribed_services']="";
		unset($_SESSION['user_session_id']);
		unset($_SESSION["login_name"]);
		unset($_SESSION["password"]);
		unset($_SESSION['subscribed_categories']);
		unset($_SESSION['subscribed_services']);
		?>
		<script>
		location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']['dna_pages']["members"])); ?>';
		</script>
		<?php
		die();

}
?>