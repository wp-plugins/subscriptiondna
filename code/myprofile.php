<?php
SubscriptionDNA_LoginValidate();

if ($_POST['send'])
{
    $cf = array();
    foreach ($_POST as $key => $val)
    {
        if (substr($key, 0, 3) == "cf_")
        {
            if (is_array($val))
            {
                $val = implode(",", $val);
            }

            $cf[substr($key, 3)] = $val;
        }
    }
    $data = array(
        "login_name" => $_POST['login_name'],
        "password" => $_POST['oldpassword'],
        "first_name" => $_POST['first_name'],
        "last_name" => $_POST['last_name'],
        "email" => $_POST['email'],
        "address1" => $_POST['address1'],
        "address2" => $_POST['address2'],
        "phone" => $_POST['phone'],
        "city" => $_POST['city'],
        "state" => $_POST['state'],
        "zipcode" => $_POST['zipcode'],
        "country" => $_POST['country'],
        "custom_fields" => $cf,
        "new_login_name" => $_POST["new_login_name"],
        "user_description" => $_POST["user_description"],
        "company_name" => $_POST["company_name"],
        "job_title" => $_POST["job_title"],
        "mobile_phone" => $_POST["mobile_phone"],
        "notification" => "Email"); //Email,SMS or Both

    $result = SubscriptionDNA_ProcessRequest($data, "user/update_profile");
    if ($result->errCode < 0)
    {
        $_REQUEST["dna_message"]='<div class="alert alert-danger" role="alert">' . $result->errDesc . '</div>';
    }
    else
    {

        $_REQUEST["dna_message"] = '<div class="alert alert-success" role="alert">' . $result->errDesc . '</div>';
    }
}
?>