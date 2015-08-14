<?php
if (isset($_REQUEST["x_submit"]))
{
    if (!isset($_REQUEST["cc_on_file"]))
        $_REQUEST["card_id"] = "";
    
        list($service_id, $billing_routine_id) = explode(";", $_POST["packages"][0]);
        $data = array(
        "service_id" => $service_id,
        "billing_routine_id" => $billing_routine_id,
        "sender_email" => $_REQUEST["sender_email"],
        "sender_fname" => $_REQUEST["sender_fname"],
        "sender_lname" => $_REQUEST["sender_lname"],
        "first_name" => $_REQUEST["first_name"],
        "last_name" => $_REQUEST["last_name"],
        "email" => $_REQUEST["email"],
        "send_recipient_email_on" => $_REQUEST["send_recipient_email_on"],
        "cc_name" => $_REQUEST["cc_name"],
        "cc_type" => $_REQUEST["cc_type"],
        "cc_number" => $_REQUEST["cc_number"],
        "cc_exp_month" => $_REQUEST["cc_exp_month"],
        "cc_exp_year" => $_REQUEST["cc_exp_year"],
        "cc_cvv" => $_REQUEST["cc_cvv"],
        "country" => $_REQUEST["country"],
        "address" => $_REQUEST["address"],
        "city" => $_REQUEST["city"],
        "state" => $_REQUEST["state"],
        "zipcode" => $_REQUEST["zipcode"],
        "check_mo" => $_REQUEST["check_mo"],
        "promo_code" => $_REQUEST["promo_code"],
        "custom_comment" => $_REQUEST["custom_comment"],
        "login_name" => $_SESSION['login_name'],
        "card_id" => $_REQUEST["card_id"]
    );
    $result = SubscriptionDNA_ProcessRequest($data, "subscription/gift", true);
    if ($result["errCode"] < 0)
    {
        $_REQUEST["dna_message"]='<div class="alert alert-danger" role="alert">' . $result["errDesc"] . '</div>';
    }
    else
    {
        wp_redirect("/gift/subscription-confirmation");
        die();
    }
}
?>