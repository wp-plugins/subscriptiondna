<?php
SubscriptionDNA_LoginValidate();
if ($_REQUEST["save_cc_info"])
{
    $login_name = $_SESSION['login_name'];

    if (!empty($_REQUEST['send']))
    {

        $data = array("cc_name" => $_REQUEST['cc_name'], "cc_type" => $_REQUEST['cc_type'], "cc_number" => $_REQUEST['cc_number'], "cc_exp_month" => $_REQUEST['cc_exp_month'], "cc_exp_year" => $_REQUEST['cc_exp_year'], "is_primary" => "1", "login_name" => $login_name);

        if (!empty($_REQUEST['cc_id']))
        {
            $data["card_id"] = $_REQUEST['cc_id'];
            $result = SubscriptionDNA_ProcessRequest($data, "creditcard/update", true);
            $msg = $result["errDesc"];
            wp_redirect(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['payment-methods']) . "?&msg=" . urlencode($msg));
            exit;
        }
        else
        {
            $result = SubscriptionDNA_ProcessRequest($data, "creditcard/add", true);
        }
        if ($result["errCode"] == 7 || $result["errCode"] == 12)
        {
            $msg = $result["errDesc"];
            wp_redirect(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['payment-methods']) . "?&msg=" . urlencode($msg));
            exit;
        }
        else
        {
            $_REQUEST["dna_message"]='<div class="alert alert-danger" role="alert">' . $result["errDesc"] . '</div>';
            $status = 'Primary';
            $GLOBALS["dna_result"] = array('card_type' => $_REQUEST['cc_type'],
                'card_holder_name' => $_REQUEST['cc_name'],
                'card_number' => $_REQUEST['cc_number'],
                'status' => $status,
                'expire_date' => $_REQUEST['cc_exp_month'] . '/' . $_REQUEST['cc_exp_year'],
            );
        }
    }
    
}
?>