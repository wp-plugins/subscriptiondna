<?php
if (isset($_REQUEST["x_submit"]))
{
    $packages = SubscriptionDNA_ProcessRequest("", "list/packages");
    if (!isset($_REQUEST["cc_on_file"]))
        $_REQUEST["card_id"] = "";

    if ($_REQUEST["check_mo"] == "1" || $_REQUEST["payment_info_not_required"] == "1")
        $_REQUEST["paid_by_credit_card"] = "";
    else
        $_REQUEST["paid_by_credit_card"] = "1";
    if ($_REQUEST["group_owner_id"] == "")
        list($service_id, $billing_routine_id) = explode(";", $_POST["packages"][0]);
    $selected_package = new stdClass();
    foreach ($packages as $package)
    {
        if ($package->service_id == $service_id && $package->billing_routine_id == $billing_routine_id)
        {
            $selected_package = $package;
        }
    }

    $data = array(
        "login_name" => $_REQUEST["login_name"],
        "password" => $_REQUEST["password"],
        "first_name" => $_REQUEST["first_name"],
        "last_name" => $_REQUEST["last_name"],
        "email" => $_REQUEST["email"],
        "address1" => $_REQUEST["address1"],
        "address2" => $_REQUEST["address2"],
        "phone" => $_REQUEST["phone"],
        "city" => $_REQUEST["city"],
        "state" => $_REQUEST["state"],
        "zipcode" => $_REQUEST["zipcode"],
        "country" => $_REQUEST["country"],
        "subscribe_to_service" => "1",
        "service_id" => $service_id,
        "billing_routine_id" => $billing_routine_id,
        "paid_by_credit_card" => $_REQUEST["paid_by_credit_card"],
        "cc_name" => $_REQUEST["cc_name"],
        "cc_type" => $_REQUEST["cc_type"],
        "cc_number" => $_REQUEST["cc_number"],
        "cc_exp_month" => $_REQUEST["cc_exp_month"],
        "cc_exp_year" => $_REQUEST["cc_exp_year"],
        "cc_cvv" => $_REQUEST["cc_cvv"],
        "custom_fields" => $_REQUEST["custom_fields"],
        "how_referred" => $_REQUEST["how_referred"],
        "promo_code" => $_REQUEST["promo_code"],
        "group_owner_id" => $_REQUEST["group_owner_id"],
        "user_description" => $_REQUEST["user_description"],
        "auto_login_name" => "",
        "auto_password" => "",
        "is_groupowner" => $selected_package->group_package,
        "max_subscribers" => $selected_package->max_subscribers,
        "group_service" => $selected_package->group_billing,
        "group_billing" => $selected_package->group_service,
        "send_welcome_email" => "",
        "setup_amount" => "",
        "check_mo" => $_REQUEST["check_mo"],
        "tax" => "",
        "email_confirmurl" => "",
        "company_name" => $_REQUEST["company_name"],
        "job_title" => $_REQUEST["job_title"],
        "mobile_phone" => $_REQUEST["mobile_phone"],
        "notify_st" => "Email"
    );

    $result = SubscriptionDNA_ProcessRequest($data, "user/register", true);
    if($result["errCode"] < 0)
    {
        $_REQUEST["dna_message"]="<div id='dna-login'><div id='failure'><div class='alert alert-danger' role='alert'>Error: " . $result["errDesc"] . "</div></div></div>";
        $_POST['response_type'] = "Failed";
    }
    else
    {
        wp_redirect(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login']));
        die();
    }
}
?>