        <tr valign=top id="trCCInfo1" style="display:<?php echo($display); ?>">
            <td><b>Card Holder Name:</b></td>
            <td><span style='color:red'>*</span></td>
            <td><input type='text' name='cc_name' id="cc_name" value="<?php echo(@$result["card_holder_name"]); ?>" size=30 maxlength=100></td>
        </tr>
        <tr valign=top id="trCCInfo2" style="display:<?php echo($display); ?>">
            <td><b>Card Type:</b></td>
            <td><span style='color:red'>*</span></td>
            <td>
                <select name='cc_type' id="cc_type" size=1>
					<option></option>
                    <option value='MasterCard' >MasterCard</option>
                    <option value='American Express' >American Express</option>
                    <option value='Visa' >Visa</option>
                    <option value='Discover' >Discover</option>
                </select>
            </td>
        </tr>
        <tr valign=top  id="trCCInfo3" style="display:<?php echo($display); ?>">
            <td><b>Card Number:</b></td>
            <td><span style='color:red'>*</span></td>
            <td><input type='text' name='cc_number' id="cc_number" value='' size=20 maxlength=20> <?php echo(@$result["card_number"]); ?></td>
        </tr>
        <tr valign=top  id="trCCInfo4" style="display:<?php echo($display); ?>">
            <td><b>Card Expiry:</b></td>
            <td><span style='color:red'>*</span></td>
            <td>
                <select name='cc_exp_month' id="cc_exp_month" size=1>
					<option></option>
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
                <select name='cc_exp_year' id="cc_exp_year" size=1>
		    <option></option>
                    <?php
                    $year=date("Y");
                    for($i=$year;$i<=$year+9;$i++)
                    {
                        ?><option value='<?php echo(substr($i,2)); ?>'><?php echo($i); ?></option><?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <?php
        if(!$_REQUEST["save_cc_info"])
        {
        ?>
        <tr valign=top>
		<td><b>CVC:</b></td>
		<td>&nbsp;</td>
		<td><input type='text' name='cc_cvv' id="cc_cvv" value='' size=5 maxlength=4></td>
	</tr>
        <?php 
        }
        ?>