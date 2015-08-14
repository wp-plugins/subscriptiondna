<?php
if ($_REQUEST["save_cc_info"])
{

    $login_name = $_SESSION['login_name'];


    $cid = $_REQUEST['cid'];
    if (!empty($cid))
    {
        $data = array("login_name" => $login_name, "card_id" => $_REQUEST["cid"]);
        $result = SubscriptionDNA_ProcessRequest($data, "creditcard/detail", true);
    }
    else
    {
        $result=$GLOBALS["dna_result"];
    }
    ?>

    <script type="text/javascript" src="<?php echo($GLOBALS['SubscriptionDNA']["siteurl"]); ?>/wp-content/plugins/subscriptiondna/js/ccinfo.js"></script>
    <form name="cc_form" method="post" class="form-horizontal form-border form-shadow text-left pad-left-40" action="" onsubmit="return frmValidate(this);">
        <input type="hidden" value="mycards" name="dna_action_page" />
        <div style="padding-top:20px;">
            <span id="avail_msg" class="lblErr center-block text-center"><b><?= $msg.$_REQUEST["dna_message"]; ?></b></span>
            <?php
            include 'cc_info.php';
            if (!empty($result["ccId"]))
            {
                ?>
                <div class="form-group">
                    <div class="checkbox  col-md-12 control-label-align">
                        <input type="hidden" id="cc_id" name="cc_id" value="<?= $result["ccId"]; ?>" />
                    </div>
                </div>
                <?php
            }
            ?>
            <hr />
            <div class="form-group">
                <div class="col-md-12">
                    <input name="send" value="Save Credit Card" type="submit" class="btn btn-default btn-block "/>
                </div>
            </div>
        </div>
    </form>
    <script>
        dropdown_select('cc_type', '<?= $result["card_type"]; ?>');
        var cc_date = "<?= $result["expire_date"]; ?>";
        if (cc_date != "") {
            var split_date = cc_date.split('/');
            dropdown_select('cc_exp_month', split_date[0]);
            dropdown_select('cc_exp_year', split_date[1]);
        }
    </script>
    <?php
}
else
{
    $login_name = $_SESSION['login_name'];
    if ($_REQUEST["del_id"])
    {
        $data = array("login_name" => $login_name, "card_id" => $_REQUEST["del_id"]);
        $result = SubscriptionDNA_ProcessRequest($data, "creditcard/delete", true);
    }

    $cards = SubscriptionDNA_ProcessRequest(array("login_name" => $_SESSION['login_name']), "creditcard/list", true);
    SubscriptionDNA_LoginCheck($cards);
    ?>
    <div style="padding:20px;margin-bottom:50px;">
        <a href='?&save_cc_info=1' class='btn btn-default'>Add New Credit Card</a><br><br>
        <?php
        if (count($cards) < 1)
        {
            echo '<div class="alert alert-danger" role="alert">No credit card found.</div><br />';
        }
        else
        {
            ?>


        <div id="dna-subscriptions" >
            <div class="alert alert-info" style="<?php if( ! isset($_REQUEST['msg']) || ! isset($_REQUEST['msg']) ) echo 'display:none'; ?>" > <?= $_REQUEST['msg'].$_REQUEST["dna_message"]; ?></div>

                <div class="hidden-xs" style="  border-top: 1px solid #ddd;background-color: #ebebeb;border-bottom: 1px solid #ddd;min-height: 40px;padding: 10px 0;">
                    <div class="hidden-xs clearfix">    
                        <div class='col-sm-3 ' style='padding-right:5px;'><b>Type</b></div>
                        <div class='col-sm-2 ' style='padding-right:5px;padding-left:0;'><b>Name</b></div>
                        <div class='col-sm-3 ' style='padding-right:5px;padding-left:0;'><b>Card #</b></div>
                        <div class='col-sm-2 ' style='padding-right:5px;padding-left:0;'><b >Exp. Date</b></div>
                        <div class='col-sm-2 ' style='padding-left:5px;padding-right:10px;'><b>Action</b></div>
                    </div>
                </div>
                <?php
                $deface=false;
                foreach ($cards as $card)
                {
                    ?>
                    <div class="well visible-xs clearfix" style="max-width: 500px;min-width: 285px;margin: 0 auto 10px auto;">
                        <?php
                            echo "
                            <div class='clearfix'><div class='col-xs-6 visible-xs' ><b>Type</b></div>  <div class='col-xs-6  tabular-right'>" . $card["card_type"] . "</div></div>
                            <div class='clearfix'><div class='col-xs-6 visible-xs' ><b>Name</b></div>  <div class='col-xs-6   tabular-right'>" . $card["card_holder_name"] . "</div></div>
                            <div class='clearfix'><div class='col-xs-6 visible-xs' ><b>Card #</b></div>    <div class='col-xs-6  tabular-right'>" . $card["card_number"] . "</div></div>
                            <div class='clearfix'><div class='col-xs-6 visible-xs' ><b>Exp. Date</b></div> <div class='col-xs-6  tabular-right'>" . $card["expire_date"] . "</div></div>
                            <div class='col-xs-12  tabular-center'>
                                <a class='' data-toggle='tooltip' data-placement='top' title='Edit!' href='?&save_cc_info=1&cid=" . $card["ccid"] . "'>  <span  class='glyphicon glyphicon-wrench' aria-hidden='true'></span>    </a> | <a class='' onClick=\"if(!confirm('Are you sure you want to delete?')) return(false);\"  data-toggle='tooltip' data-placement='top' title='Delete!' href='?&del_id=" . $card["ccid"] . "'>  <span  class='glyphicon glyphicon-remove-circle' aria-hidden='true'></span>   </a>
                            </div>";
                            ?>
                    </div>
            <div class="hidden-xs clearfix"  style="min-height: 40px;padding: 10px 0;<?php if($deface) { echo 'color: silver;'; }  if($card["status"]=="Primary") { echo 'background-color:#F5F5F5';$deface=true; } ?>" >
                        <?php
                        echo "
                          <div class='col-sm-3 ' style='padding-right:5px;'>" . $card["card_type"] . "</div>
                          <div class='col-sm-2 ' style='padding-right:5px;padding-left:0;'>" . $card["card_holder_name"] . "</div>
                          <div class='col-sm-3 ' style='padding-right:5px;padding-left:0;'>" . $card["card_number"] . "</div>
                          <div class='col-sm-2 ' style='padding-right:5px;padding-left:0;'>" . $card["expire_date"] . "</div>
                          <div class='col-sm-2 tabular-center' style='padding-right:10px;padding-left:5px;'>
                            <a class='' data-toggle='tooltip' data-placement='bottom' title='Edit!' href='?&save_cc_info=1&cid=" . $card["ccid"] . "'>  <span  class='glyphicon glyphicon-wrench' aria-hidden='true'></span>    </a> | <a class='' onClick=\"if(!confirm('Are you sure you want to delete?')) return(false);\"  data-toggle='tooltip' data-placement='bottom' title='Delete!' href='?&del_id=" . $card["ccid"] . "'>  <span  class='glyphicon glyphicon-remove-circle' aria-hidden='true'></span>   </a>
                          </div>"
                          ?>
                    </div>

                    <?php
                    
                } 
                ?>
        </div> 
            <?php
    }
            ?>
   </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();//turn-on bootstrap tooltips
    });
    </script>
<?php
}
?>