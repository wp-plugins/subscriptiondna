        <div id="trCCInfo1" class="form-group" style="display:<?php echo($display); ?>">
            <label  for="first_name" class="col-md-12 " >Card Holder Name:</label>
            <div class="col-md-12">
                <div class="input-group">
                    <input type='text' name='cc_name' class="form-control" id="cc_name" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('cc_name_lbl_error'));" value="<?php echo(@$result["card_holder_name"]); ?>" size=30 maxlength=100 />
                    <div class="input-group-addon req-star ">
                        <span style='color:red'>*</span>
                    </div>
                </div>
                 <span id="cc_name_lbl_error" class="lblErr center-block text-center"></span>
            </div>
        </div>

        <div id="trCCInfo2" class="form-group" style="display:<?php echo($display); ?>">
            <label  for="first_name" class="col-md-12 " >Card Type:</label>
            <div class="col-md-12">
                <div class="input-group">
                    <select name='cc_type' class="form-control" id="cc_type" onchange="hideErrorMsg(SubscriptionDNA_GetElement('cc_type_lbl_error'));">
                        <option value=''>Select</option>
                        <option value='MasterCard' >MasterCard</option>
                        <option value='American Express' >American Express</option>
                        <option value='Visa' >Visa</option>
                        <option value='Discover' >Discover</option>
                    </select>
                    <div class="input-group-addon req-star ">
                        <span style='color:red'>*</span>
                    </div>
                </div>
                 <span id="cc_type_lbl_error" class="lblErr center-block text-center"></span>
            </div>
        </div>

        <div id="trCCInfo3" class="form-group" style="display:<?php echo($display); ?>">
            <label  for="first_name" class="col-md-12 " >Card Number:</label>
            <div class="col-md-12">
                <div class="input-group">
                    <input type='text' name='cc_number' class="form-control" id="cc_number" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('cc_number_lbl_error'));" value='<?php echo(@$result["card_number"]); ?>' size=20 maxlength=20 />
                    <div class="input-group-addon req-star ">
                            <span style='color:red'>*</span>
                    </div>
                </div>
                 <span id="cc_number_lbl_error" class="lblErr center-block text-center"></span>
            </div>
        </div>

        <div id="trCCInfo4" class="form-group" style="display:<?php echo($display); ?>">
            <div> <label   class="col-md-12 " >Card Expiry:</label></div>
                <div class="col-xs-6">
                    <div class="input-group">
                        <select name='cc_exp_month' id="cc_exp_month" class="form-control" onchange="hideErrorMsg(SubscriptionDNA_GetElement('cc_exp_lbl_error'));">
                            <option value=''>Select Month</option>
                            <option value='01'>Jan</option>
                            <option value='02'>Feb</option>
                            <option value='03'>Mar</option>
                            <option value='04'>Apr</option>
                            <option value='05'>May</option>
                            <option value='06'>Jun</option>
                            <option value='07'>Jul</option>
                            <option value='08'>Aug</option>
                            <option value='09'>Sep</option>
                            <option value='10'>Oct</option>
                            <option value='11'>Nov</option>
                            <option value='12'>Dec</option>
                        </select>
                        <div class="input-group-addon req-star ">
                                <span style='color:red'>*</span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="input-group">
                        <select name='cc_exp_year' id="cc_exp_year" class="form-control" onchange="hideErrorMsg(SubscriptionDNA_GetElement('cc_exp_lbl_error'));">
        		          <option value=''>Select Year</option>
                            <?php
                            $year=date("Y");
                            for($i=$year;$i<=$year+9;$i++)
                            {
                                ?><option value='<?php echo(substr($i,2)); ?>'><?php echo($i); ?></option><?php
                            }
                            ?>
                        </select>
                        <div class="input-group-addon req-star ">
                            <span style='color:red'>*</span>
                        </div>
                    </div>
                </div>
                <span id="cc_exp_lbl_error" class="lblErr center-block "></span>
            </div>


        <?php
        if(!$_REQUEST["save_cc_info"])
        {
        ?>
        <div id="trCCInfo5" class="form-group" style="display:<?php echo($display); ?>">
            <label  for="cc_cvv" class="col-md-12" >CVC:</label>
            <div class="col-md-12">
                    <input type='text' name='cc_cvv' id="cc_cvv" class="form-control text-center" style="max-width:100px;" value=''  size=5 maxlength=4 />
                   
    	   </div>
        </div>
        <?php 
        }
        ?>