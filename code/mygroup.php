<?php
SubscriptionDNA_LoginValidate();

if(!$_SESSION['is_groupowner'])
{
    wp_redirect(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['members']));
    die();
}
?>