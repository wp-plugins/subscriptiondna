<?php
SubscriptionDNA_LoginValidate();

$login_name = $_SESSION['login_name'];
if ($_POST['send'])
{
    if (empty($_POST['password']))
        $_POST['password'] = $_POST['oldpassword'];

    $data = array(
        "login_name" => $login_name,
        "old_password" => $_POST['oldpassword'],
        "new_password" => $_POST['password']
    );
    $result = SubscriptionDNA_ProcessRequest($data, "user/change_pass");
    SubscriptionDNA_LoginCheck($result);

    if ($result->errCode != 11)
    {
        $msg = '<div class="alert alert-danger" role="alert">' . $result->errDesc . '</div>';
    }
    else
    {
        $_SESSION['password'] = $_POST['password'];
        $msg = '<div class="alert alert-success" role="alert">' . $result->errDesc . '</div>';
    }
}
?>