<div class="panel panal-default center-block" style="padding-left:20px;">
            <?php if (isset($_SESSION["user_session_id"]))
            { ?>
                <b>You are logged in as: <?= $_SESSION['login_name'] ?></b>
            <?php
            }
            ?>
</div>

<div class="account-menu" id="dna-menu">
    <div class="container_inner">
        <div align="center">
            <div id="dna-nav">
                <?php
                foreach ($GLOBALS['SubscriptionDNA']['DPages'] as $page)
                {
                    if($page["name"]=="login")
                        continue;
                    $page_id=$GLOBALS['SubscriptionDNA']['Settings']["dna_pages"][$page["name"]];
                    $dna_options=get_post_meta($page_id, "_SubscriptionDNA",true);
                    //echo($page_id);
                    //print_r($dna_options);
                    if (($dna_options["login"] and $_SESSION["user_session_id"] != "") or (!$dna_options["login"] and $_SESSION["user_session_id"] == ""))
                    {
                        if($page["name"]=="groups" && $_SESSION['is_groupowner']!="1")
                            continue;
                        $home = get_option("home");
                        $url = get_permalink($page_id);
                        if ($GLOBALS['SubscriptionDNA']['Settings'][$page . "_HTTPS"])
                            $url = str_replace($home, $GLOBALS['SubscriptionDNA']['Settings']['SSL'], $url);
                        ?>
                        <a class="hyper navbar-link" href="<?php echo($url); ?>"><?php echo get_the_title($page_id); ?></a>
                        <?php
                        //echo(" | ");
                    }
                }
                if ($_SESSION["user_session_id"] != "")
                {
                    ?>
                    <a class="hyper navbar-link" href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>?&action=logout">Logout</a>  
                    <?php
                }
                else
                {
                    ?>
                    <a class="hyper navbar-link" href="<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['login'])); ?>">Login</a>  
                    <?php
                }
                ?>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
