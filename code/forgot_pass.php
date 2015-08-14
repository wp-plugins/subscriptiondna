<?php
if (isset($_POST['cmdReset']))
{
    if (empty($_REQUEST["new_password"]) || $_REQUEST["c_new_password"] != $_REQUEST["new_password"])
    {
        $_REQUEST["dna_message"]="<div id='dna-login'><div id='failure'><div class='alert alert-danger' role='alert'>Please enter new password</div></div></div>";
    }
    else
    {
        $result = SubscriptionDNA_ProcessRequest(array("reset_id" => $_REQUEST["reset_id"], "new_password" => $_REQUEST["new_password"]), "user/forgot_pass", true);
        if ($result["errCode"] != 11)
        {
           $_REQUEST["dna_message"]="<div id='dna-login'><div id='failure'><div class='alert alert-danger' role='alert'>" . $result["errDesc"] . "</div></div></div>";
        }
        else
        {
            $_REQUEST["dna_message"]="<div id='dna-login'><div id='failure'><div class='alert alert-danger' role='alert'>" . $result["errDesc"] . " <a href='" . get_permalink($GLOBALS['SubscriptionDNA']['Settings']['dna_pages']["login"]) . "'>Click here to login</a></div></div></div>";
        }
    }
}
else if (isset($_POST['send']))
{
    $data = array();
    $data["login_name"] = $_POST['login_name'];
    if (!empty($_POST['email']))
        $data["email"] = $_POST['email'];
    $data["reset"] = "1";
    $data["reset_url"] = get_permalink($GLOBALS['SubscriptionDNA']['Settings']['dna_pages']["forgot-password"]);
    //reset, $reset_url, $reset_id, $new_password
    $result = SubscriptionDNA_ProcessRequest($data, "user/forgot_pass", true);

    if ($result["errCode"] != 18)
    {
        $_REQUEST["dna_message"]="<div id='dna-login'><div id='failure' class='lblErr'><div class='alert alert-danger' role='alert'>" . $result["errDesc"] . "</div></div></div>";
    }
    else
    {
        $_REQUEST["dna_message"]="<div id='dna-login'><div id='failure' class='lblErr'><div class='alert alert-danger' role='alert'>" . $result["errDesc"] . "</div></div></div>";
    }

}
?>