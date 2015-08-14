<?php
$profile = SubscriptionDNA_ProcessRequest(array("login_name" => $_SESSION['login_name']), "user/profile");
SubscriptionDNA_LoginCheck($profile);
?>

<style>
    #dna-profile td { text-align: left; padding: 4px; font-size: 11pt; }
    #dna-profile input { font-size: 10pt; }
    #dna-profile .dna-heading { font-size: 15pt; font-weight: bold; letter-spacing: -1px; }
    #dna-profile .submit { padding-left: 5px; padding-right: 5px; }
</style>
<form name="profile-form" method="post" action="" class="form-horizontal form-border form-shadow text-left pad-left-40">
    <input name="uid" id="uid" type="hidden" value="<?= $profile->user_id; ?>" />
    <input name="login_name" id="login_name" type="hidden" value="<?= $profile->login_name; ?>" />
    <input name="oldpassword" id="oldpassword" type="hidden" value="<?= $profile->password; ?>" />

 <br />
    <div class="col-md-12"><span  id="avail_msg" class="lblErr center-block text-center"><?= $msg ?></span></div>

    <?php
    if($profile->referral_code!="")
    {
    ?>
    <div class="form-group">
        <label for="referral_code" class="col-md-12 " >Here's a referral link to give to your friends:</label>
        <div class="col-md-12">
            <div class="input-group">
               <?php 
               $signuplink=get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['subscribe'])."?ref=".$profile->referral_code;
                $data=array("promo_code"=>$profile->referral_code);
                $promocode = SubscriptionDNA_ProcessRequest($data,"subscription/validate_promocode",true);
                if(round($promocode["discount"])==$promocode["discount"])
                    $promocode["discount"]=round($promocode["discount"]);
                if($promocode["discount_mod"]=="%")
                {
                    $msg=$promocode["discount"].$promocode["discount_mod"];
                }
                elseif($promocode["discount_mod"]=="$")
                {
                    $msg='$'.$promocode["discount"].'';
                }
               
               ?>
                <a target="_blank" href="<?php echo($signuplink); ?>"> <?php echo($signuplink); ?></a>
                <?php 
                if($promocode["discount"]>0)
                {
                    echo("<br>When they join they will receive a ".$msg." discount.");
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
    <h2  class='dna-heading'>Contact Information</h2>
    <div class="form-group">
        <label for="first_name" class="col-md-12 " >First Name:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="first_name" id="first_name" type="text" class="form-control" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('first_name_lbl_error'));" value="<?= $profile->first_name; ?>" />
                <div class="input-group-addon req-star require ">*</div>
            </div>
            <span id="first_name_lbl_error" class="lblErr center-block "></span>
        </div>
    </div>
    <div class="form-group">
        <label for="last_name" class="col-md-12" >Last Name:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="last_name" id="last_name" type="text" class="form-control" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('last_name_lbl_error'));" value="<?= $profile->last_name; ?>" />
                <div class="input-group-addon req-star require">*</div>
            </div>
            <span id="last_name_lbl_error" class="lblErr center-block "></span>
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="col-md-12" >Email:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="email" id="email" type="text" class="form-control" onkeydown="hideErrorMsg(SubscriptionDNA_GetElement('email_lbl_error'));" value="<?= $profile->email; ?>" />
                <div class="input-group-addon req-star require">*</div>
            </div>
            <span id="email_lbl_error" class="lblErr center-block "></span>
        </div>
    </div>

    <div class="form-group">
        <label for="phone" class="col-md-12" >Phone:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="phone" id="phone" type="text"  class="form-control" value="<?= $profile->phone; ?>" />
                <div class="input-group-addon req-star"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="address1" class="col-md-12" >Address:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="address1" id="address1" type="text" class="form-control" value="<?= $profile->address1; ?>" />
                <div class="input-group-addon req-star"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="address2" class="col-md-12" >Address 2:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="address2" id="address2" type="text" class="form-control" value="<?= $profile->address2; ?>" />
                <div class="input-group-addon req-star"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="city" class="col-md-12" >City:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="city" id="city" type="text" class="form-control" value="<?= $profile->city; ?>" />
                <div class="input-group-addon req-star"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="state" class="col-md-12" >State:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="state" id="state" type="text" class="form-control" value="<?= $profile->state; ?>" />
                <div class="input-group-addon req-star"></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="zipcode" class="col-md-12" >Zip:</label>
        <div class="col-md-12">
            <div class="input-group">
                <input name="zipcode" id="zipcode" type="text" class="form-control" value="<?= $profile->zipcode; ?>" />
                <div class="input-group-addon req-star"></div>
            </div>
        </div>
    </div>


    <div class="form-group">
        <label for="country" class="col-md-12" >Country:</label>
        <div class="col-md-12">
            <div class="input-group">
                <select name="country" tabindex="14" class="form-control" id="country">
                    <option label="&lt;--Please Select--&gt;" value="">&lt;--Please Select--&gt;</option>
                    <option label="United States" value="223" selected="selected">United States</option>
                    <option label="Afghanistan" value="1">Afghanistan</option>
                    <option label="Albania" value="2">Albania</option>
                    <option label="Algeria" value="3">Algeria</option>
                    <option label="American Samoa" value="4">American Samoa</option>
                    <option label="Andorra" value="5">Andorra</option>
                    <option label="Angola" value="6">Angola</option>
                    <option label="Anguilla" value="7">Anguilla</option>
                    <option label="Antarctica" value="8">Antarctica</option>

                    <option label="Antigua and Barbuda" value="9">Antigua and Barbuda</option>
                    <option label="Argentina" value="10">Argentina</option>
                    <option label="Armenia" value="11">Armenia</option>
                    <option label="Aruba" value="12">Aruba</option>
                    <option label="Australia" value="13">Australia</option>
                    <option label="Austria" value="14">Austria</option>
                    <option label="Azerbaijan" value="15">Azerbaijan</option>
                    <option label="Bahamas" value="16">Bahamas</option>
                    <option label="Bahrain" value="17">Bahrain</option>

                    <option label="Bangladesh" value="18">Bangladesh</option>
                    <option label="Barbados" value="19">Barbados</option>
                    <option label="Belarus" value="20">Belarus</option>
                    <option label="Belgium" value="21">Belgium</option>
                    <option label="Belize" value="22">Belize</option>
                    <option label="Benin" value="23">Benin</option>
                    <option label="Bermuda" value="24">Bermuda</option>
                    <option label="Bhutan" value="25">Bhutan</option>
                    <option label="Bolivia" value="26">Bolivia</option>

                    <option label="Bosnia and Herzegowina" value="27">Bosnia and Herzegowina</option>
                    <option label="Botswana" value="28">Botswana</option>
                    <option label="Bouvet Island" value="29">Bouvet Island</option>
                    <option label="Brazil" value="30">Brazil</option>
                    <option label="British Indian Ocean Territory" value="31">British Indian Ocean Territory</option>
                    <option label="Brunei Darussalam" value="32">Brunei Darussalam</option>
                    <option label="Bulgaria" value="33">Bulgaria</option>
                    <option label="Burkina Faso" value="34">Burkina Faso</option>
                    <option label="Burundi" value="35">Burundi</option>

                    <option label="Cambodia" value="36">Cambodia</option>
                    <option label="Cameroon" value="37">Cameroon</option>
                    <option label="Canada" value="38">Canada</option>
                    <option label="Cape Verde" value="39">Cape Verde</option>
                    <option label="Cayman Islands" value="40">Cayman Islands</option>
                    <option label="Central African Republic" value="41">Central African Republic</option>
                    <option label="Chad" value="42">Chad</option>
                    <option label="Chile" value="43">Chile</option>
                    <option label="China" value="44">China</option>

                    <option label="Christmas Island" value="45">Christmas Island</option>
                    <option label="Cocos (Keeling) Islands" value="46">Cocos (Keeling) Islands</option>
                    <option label="Colombia" value="47">Colombia</option>
                    <option label="Comoros" value="48">Comoros</option>
                    <option label="Congo" value="49">Congo</option>
                    <option label="Cook Islands" value="50">Cook Islands</option>
                    <option label="Costa Rica" value="51">Costa Rica</option>
                    <option label="Cote D'Ivoire" value="52">Cote D'Ivoire</option>
                    <option label="Croatia" value="53">Croatia</option>

                    <option label="Cuba" value="54">Cuba</option>
                    <option label="Cyprus" value="55">Cyprus</option>
                    <option label="Czech Republic" value="56">Czech Republic</option>
                    <option label="Denmark" value="57">Denmark</option>
                    <option label="Djibouti" value="58">Djibouti</option>
                    <option label="Dominica" value="59">Dominica</option>
                    <option label="Dominican Republic" value="60">Dominican Republic</option>
                    <option label="East Timor" value="61">East Timor</option>
                    <option label="Ecuador" value="62">Ecuador</option>

                    <option label="Egypt" value="63">Egypt</option>
                    <option label="El Salvador" value="64">El Salvador</option>
                    <option label="Equatorial Guinea" value="65">Equatorial Guinea</option>
                    <option label="Eritrea" value="66">Eritrea</option>
                    <option label="Estonia" value="67">Estonia</option>
                    <option label="Ethiopia" value="68">Ethiopia</option>
                    <option label="Falkland Islands (Malvinas)" value="69">Falkland Islands (Malvinas)</option>
                    <option label="Faroe Islands" value="70">Faroe Islands</option>
                    <option label="Fiji" value="71">Fiji</option>

                    <option label="Finland" value="72">Finland</option>
                    <option label="France" value="73">France</option>
                    <option label="France, Metropolitan" value="74">France, Metropolitan</option>
                    <option label="French Guiana" value="75">French Guiana</option>
                    <option label="French Polynesia" value="76">French Polynesia</option>
                    <option label="French Southern Territories" value="77">French Southern Territories</option>
                    <option label="Gabon" value="78">Gabon</option>
                    <option label="Gambia" value="79">Gambia</option>
                    <option label="Georgia" value="80">Georgia</option>

                    <option label="Germany" value="81">Germany</option>
                    <option label="Ghana" value="82">Ghana</option>
                    <option label="Gibraltar" value="83">Gibraltar</option>
                    <option label="Greece" value="84">Greece</option>
                    <option label="Greenland" value="85">Greenland</option>
                    <option label="Grenada" value="86">Grenada</option>
                    <option label="Guadeloupe" value="87">Guadeloupe</option>
                    <option label="Guam" value="88">Guam</option>
                    <option label="Guatemala" value="89">Guatemala</option>

                    <option label="Guinea" value="90">Guinea</option>
                    <option label="Guinea-bissau" value="91">Guinea-bissau</option>
                    <option label="Guyana" value="92">Guyana</option>
                    <option label="Haiti" value="93">Haiti</option>
                    <option label="Heard and Mc Donald Islands" value="94">Heard and Mc Donald Islands</option>
                    <option label="Honduras" value="95">Honduras</option>
                    <option label="Hong Kong" value="96">Hong Kong</option>
                    <option label="Hungary" value="97">Hungary</option>
                    <option label="Iceland" value="98">Iceland</option>

                    <option label="India" value="99">India</option>
                    <option label="Indonesia" value="100">Indonesia</option>
                    <option label="Iran (Islamic Republic of)" value="101">Iran (Islamic Republic of)</option>
                    <option label="Iraq" value="102">Iraq</option>
                    <option label="Ireland" value="103">Ireland</option>
                    <option label="Israel" value="104">Israel</option>
                    <option label="Italy" value="105">Italy</option>
                    <option label="Jamaica" value="106">Jamaica</option>
                    <option label="Japan" value="107">Japan</option>

                    <option label="Jordan" value="108">Jordan</option>
                    <option label="Kazakhstan" value="109">Kazakhstan</option>
                    <option label="Kenya" value="110">Kenya</option>
                    <option label="Kiribati" value="111">Kiribati</option>
                    <option label="Korea, Democratic People's Republic of" value="112">Korea, Democratic People's Republic of</option>
                    <option label="Korea, Republic of" value="113">Korea, Republic of</option>
                    <option label="Kuwait" value="114">Kuwait</option>
                    <option label="Kyrgyzstan" value="115">Kyrgyzstan</option>
                    <option label="Lao People's Democratic Republic" value="116">Lao People's Democratic Republic</option>

                    <option label="Latvia" value="117">Latvia</option>
                    <option label="Lebanon" value="118">Lebanon</option>
                    <option label="Lesotho" value="119">Lesotho</option>
                    <option label="Liberia" value="120">Liberia</option>
                    <option label="Libyan Arab Jamahiriya" value="121">Libyan Arab Jamahiriya</option>
                    <option label="Liechtenstein" value="122">Liechtenstein</option>
                    <option label="Lithuania" value="123">Lithuania</option>
                    <option label="Luxembourg" value="124">Luxembourg</option>
                    <option label="Macau" value="125">Macau</option>

                    <option label="Macedonia, The Former Yugoslav Republic of" value="126">Macedonia, The Former Yugoslav Republic of</option>
                    <option label="Madagascar" value="127">Madagascar</option>
                    <option label="Malawi" value="128">Malawi</option>
                    <option label="Malaysia" value="129">Malaysia</option>
                    <option label="Maldives" value="130">Maldives</option>
                    <option label="Mali" value="131">Mali</option>
                    <option label="Malta" value="132">Malta</option>
                    <option label="Marshall Islands" value="133">Marshall Islands</option>
                    <option label="Martinique" value="134">Martinique</option>

                    <option label="Mauritania" value="135">Mauritania</option>
                    <option label="Mauritius" value="136">Mauritius</option>
                    <option label="Mayotte" value="137">Mayotte</option>
                    <option label="Mexico" value="138">Mexico</option>
                    <option label="Micronesia, Federated States of" value="139">Micronesia, Federated States of</option>
                    <option label="Moldova, Republic of" value="140">Moldova, Republic of</option>
                    <option label="Monaco" value="141">Monaco</option>
                    <option label="Mongolia" value="142">Mongolia</option>
                    <option label="Montserrat" value="143">Montserrat</option>

                    <option label="Morocco" value="144">Morocco</option>
                    <option label="Mozambique" value="145">Mozambique</option>
                    <option label="Myanmar" value="146">Myanmar</option>
                    <option label="Namibia" value="147">Namibia</option>
                    <option label="Nauru" value="148">Nauru</option>
                    <option label="Nepal" value="149">Nepal</option>
                    <option label="Netherlands" value="150">Netherlands</option>
                    <option label="Netherlands Antilles" value="151">Netherlands Antilles</option>
                    <option label="New Caledonia" value="152">New Caledonia</option>

                    <option label="New Zealand" value="153">New Zealand</option>
                    <option label="Nicaragua" value="154">Nicaragua</option>
                    <option label="Niger" value="155">Niger</option>
                    <option label="Nigeria" value="156">Nigeria</option>
                    <option label="Niue" value="157">Niue</option>
                    <option label="Norfolk Island" value="158">Norfolk Island</option>
                    <option label="Northern Mariana Islands" value="159">Northern Mariana Islands</option>
                    <option label="Norway" value="160">Norway</option>
                    <option label="Oman" value="161">Oman</option>

                    <option label="Pakistan" value="162">Pakistan</option>
                    <option label="Palau" value="163">Palau</option>
                    <option label="Panama" value="164">Panama</option>
                    <option label="Papua New Guinea" value="165">Papua New Guinea</option>
                    <option label="Paraguay" value="166">Paraguay</option>
                    <option label="Peru" value="167">Peru</option>
                    <option label="Philippines" value="168">Philippines</option>
                    <option label="Pitcairn" value="169">Pitcairn</option>
                    <option label="Poland" value="170">Poland</option>

                    <option label="Portugal" value="171">Portugal</option>
                    <option label="Puerto Rico" value="172">Puerto Rico</option>
                    <option label="Qatar" value="173">Qatar</option>
                    <option label="Reunion" value="174">Reunion</option>
                    <option label="Romania" value="175">Romania</option>
                    <option label="Russian Federation" value="176">Russian Federation</option>
                    <option label="Rwanda" value="177">Rwanda</option>
                    <option label="Saint Kitts and Nevis" value="178">Saint Kitts and Nevis</option>
                    <option label="Saint Lucia" value="179">Saint Lucia</option>

                    <option label="Saint Vincent and the Grenadines" value="180">Saint Vincent and the Grenadines</option>
                    <option label="Samoa" value="181">Samoa</option>
                    <option label="San Marino" value="182">San Marino</option>
                    <option label="Sao Tome and Principe" value="183">Sao Tome and Principe</option>
                    <option label="Saudi Arabia" value="184">Saudi Arabia</option>
                    <option label="Senegal" value="185">Senegal</option>
                    <option label="Seychelles" value="186">Seychelles</option>
                    <option label="Sierra Leone" value="187">Sierra Leone</option>
                    <option label="Singapore" value="188">Singapore</option>

                    <option label="Slovakia (Slovak Republic)" value="189">Slovakia (Slovak Republic)</option>
                    <option label="Slovenia" value="190">Slovenia</option>
                    <option label="Solomon Islands" value="191">Solomon Islands</option>
                    <option label="Somalia" value="192">Somalia</option>
                    <option label="South Africa" value="193">South Africa</option>
                    <option label="South Georgia and the South Sandwich Islands" value="194">South Georgia and the South Sandwich Islands</option>
                    <option label="Spain" value="195">Spain</option>
                    <option label="Sri Lanka" value="196">Sri Lanka</option>
                    <option label="St. Helena" value="197">St. Helena</option>

                    <option label="St. Pierre and Miquelon" value="198">St. Pierre and Miquelon</option>
                    <option label="Sudan" value="199">Sudan</option>
                    <option label="Suriname" value="200">Suriname</option>
                    <option label="Svalbard and Jan Mayen Islands" value="201">Svalbard and Jan Mayen Islands</option>
                    <option label="Swaziland" value="202">Swaziland</option>
                    <option label="Sweden" value="203">Sweden</option>
                    <option label="Switzerland" value="204">Switzerland</option>
                    <option label="Syrian Arab Republic" value="205">Syrian Arab Republic</option>
                    <option label="Taiwan" value="206">Taiwan</option>

                    <option label="Tajikistan" value="207">Tajikistan</option>
                    <option label="Tanzania, United Republic of" value="208">Tanzania, United Republic of</option>
                    <option label="Thailand" value="209">Thailand</option>
                    <option label="Togo" value="210">Togo</option>
                    <option label="Tokelau" value="211">Tokelau</option>
                    <option label="Tonga" value="212">Tonga</option>
                    <option label="Trinidad and Tobago" value="213">Trinidad and Tobago</option>
                    <option label="Tunisia" value="214">Tunisia</option>
                    <option label="Turkey" value="215">Turkey</option>

                    <option label="Turkmenistan" value="216">Turkmenistan</option>
                    <option label="Turks and Caicos Islands" value="217">Turks and Caicos Islands</option>
                    <option label="Tuvalu" value="218">Tuvalu</option>
                    <option label="Uganda" value="219">Uganda</option>
                    <option label="Ukraine" value="220">Ukraine</option>
                    <option label="United Arab Emirates" value="221">United Arab Emirates</option>
                    <option label="United Kingdom" value="222">United Kingdom</option>
                    <option label="United States" value="223" selected="selected">United States</option>
                    <option label="United States Minor Outlying Islands" value="224">United States Minor Outlying Islands</option>
                    <option label="Uruguay" value="225">Uruguay</option>

                    <option label="Uzbekistan" value="226">Uzbekistan</option>
                    <option label="Vanuatu" value="227">Vanuatu</option>
                    <option label="Vatican City State (Holy See)" value="228">Vatican City State (Holy See)</option>
                    <option label="Venezuela" value="229">Venezuela</option>
                    <option label="Viet Nam" value="230">Viet Nam</option>
                    <option label="Virgin Islands (British)" value="231">Virgin Islands (British)</option>
                    <option label="Virgin Islands (U.S.)" value="232">Virgin Islands (U.S.)</option>
                    <option label="Wallis and Futuna Islands" value="233">Wallis and Futuna Islands</option>
                    <option label="Western Sahara" value="234">Western Sahara</option>

                    <option label="Yemen" value="235">Yemen</option>
                    <option label="Yugoslavia" value="236">Yugoslavia</option>
                    <option label="Zaire" value="237">Zaire</option>
                    <option label="Zambia" value="238">Zambia</option>
                    <option label="Zimbabwe" value="239">Zimbabwe</option>
                    <option label="Aaland Islands" value="240">Aaland Islands</option>
                </select>
                <div class="input-group-addon req-star"></div>
            </div>
        </div>
    </div>

    <?php
    if ($GLOBALS['SubscriptionDNA']['Settings']['Extra'] == "1") {
        ?>


        <h3>Additional Fields</h3>

        <?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	Adding custom fields.
////////////////////////////////////////////////////////////////////////////////////////////////////////////


        $customFields = $profile->custom_fields;
        foreach ($customFields as $customField) {
            $caption = $customField->caption;
            $type = $customField->type;
            $name = $customField->name;
            $default_value = $customField->default_value;
            $value = $customField->value;

            if ($name) {
                 echo '<div class="form-group">
                        <label for="'. $name .'" class="col-xs-12 control-label-align" >'. $caption .':</label>
                        <div class="col-xs-12">';
                if ($type == 'text') {
                    $text_val = (empty($value)) ? $default_value : $value;
                    echo '<div class="input-group"><input type="text"class="form-control" name="'.$name.'" id="'. $name .'" value="'. htmlentities( $text_val ) .'"  size="30">';
                    echo '<div class="input-group-addon req-star "> </div></div>';
                    }


                if ($type == 'checkbox') {
                    if ($default_value) {
                        $checkbox_list = explode("\n", $default_value);
                        $selected_value_list = explode(",", $value);
                        echo("");

                        for ($j = 0; $j < count($checkbox_list); $j++) {
                            $selected_val = '';
                            for ($k = 0; $k < count($selected_value_list); $k++) {
                                if ($checkbox_list[$j] == $selected_value_list[$k]) {
                                    $selected_val = "checked";
                                    break;
                                }
                            }

                            echo "<input style='width: 15px' name='" . $name . "[" . $j . "]" . "' type='" . $type . "' id='" . $name . "' " . $selected_val . " type='" . $type . "' value='" . $checkbox_list[$j] . "' /> " . $checkbox_list[$j] . " ";
                            echo '<br/>';
                            }
                        echo("");
                    } else {
                        echo "<input style='width: 15px name='" . $name . "[" . $j . "]" . "' id='" . $name . "' type='" . $type . "' value='" . $value . "' />";
                        echo '<br/>';
                    }
                }

                if ($type == 'radio') {
                    if ($default_value) {
                        $radio_list = explode("\n", $default_value);
                        echo'';
                        for ($j = 0; $j < count($radio_list); $j++) {
                            if ($value == $radio_list[$j])
                                $sel = "checked";
                            else
                                $sel = '';

                            echo '<input style="width: 15px" name="'.$name.'" type="'. $type . '" id="'. $name . '"' . $sel . '" type="' . $type .' " value="' . $radio_list[$j] . '" />' . $radio_list[$j]. '  ';
                        echo '<br/>';

                        }
                        echo '';
                    }
                    else {
                        echo '<input style="width: 15px" name="' . $name . '" id="' . $name . '" type="' . $type . '" value="' . $value . '" />';
                        echo '<br />';
                    }
                }


                if ($type == 'textarea') {
                    echo '<div class="input-group"><textarea class="form-control control-input" name="'.$name.'"id="'.$name.'">'.htmlentities($value).'</textarea>';
                      echo' <div class="input-group-addon req-star "> </div></div>';
                }


                if ($type == 'select') {
                    if ($default_value) {
                        $value_list = explode("\n", $default_value);
                        echo '<div class="input-group"><select class="form-control control-input" name="'. $name .'"id="' . $name .'">';
                        for ($j = 0; $j < count($value_list); $j++) {
                            if ($value_list[$j] == $value)
                                echo "<option selected value=" . $value_list[$j] . ">" . $value_list[$j] . "</option>";
                            else
                                echo "<option value=" . $value_list[$j] . ">" . $value_list[$j] . "</option>";
                        }
                        echo' </select><div class="input-group-addon req-star "> </div></div>';

                    }
                }


                if ($type == 'multi_select') {
                    if ($default_value) {
                        $multiselect_list = explode("\n", $default_value);
                        $selected_value_list = explode(",", $value);

                        echo '<div class="input-group"><select class="form-control control-input" name="'. $name. '[]" multiple id="'. $name.'">';

                        for ($j = 0; $j < count($multiselect_list); $j++) {
                            $selected_val = '';
                            for ($k = 0; $k < count($selected_value_list); $k++) {
                                if ($multiselect_list[$j] == $selected_value_list[$k]) {
                                    $selected_val = "selected";
                                    break;
                                }
                            }

                            //echo 'option '.$selected_val.' value="'.$multiselect_list[$j].'">'.$multiselect_list[$j].'option' . "<br>";
                            echo '<option ' . $selected_val . ' value="' . $multiselect_list[$j] . '">' . $multiselect_list[$j] . '</option>';
                        }

                        echo '</select><div class="input-group-addon req-star "> </div></div>';
                    }
                }

             echo '</div></div>';
            }
        }

////////////////////////////////////////////////////////////////////////////////////////////////////////////

    }
    ?>
    <br/>
    <br/>
    <div class="form-group">
        <div class="col-md-12">
            <div class="input-group">
                <input name="send" value="Click here to submit form" type="submit" class="btn btn-default btn-block " onclick="return verify()" />
                <div class="input-group-addon req-star"></div>
            </div>
        </div>
    </div>
</form>




<script language="javascript" type="text/javascript">

    function SubscriptionDNA_GetElement(id) {
        return document.getElementById(id);
    }

    function IsNumeric(strString) {
        var strValidChars = "0123456789.-";
        var strChar;
        var blnResult = true;
        if (strString.length == 0)
            return false;

        //  test strString consists of valid characters listed above
        for (i = 0; i < strString.length && blnResult == true; i++) {
            strChar = strString.charAt(i);
            if (strValidChars.indexOf(strChar) == -1) {
                blnResult = false;
            }
        }
        return blnResult;
    }


    function check_special_chr(fld) {

        var iChars = "~!@#$%^&*()+=-[]\\\';,./{}|\":<>?";

        for (var i = 0; i < fld.length; i++) {
            if (iChars.indexOf(fld.charAt(i)) != -1) {
                return false;
            }
        }
        return true;
    }
     function showErrorMsg(span,message){
        var f = span;   // Get the input span element in the document with error
        jQuery(f).html(message);
    }
    function hideErrorMsg(span){
        var f = span;   // Get the input span element in the document with error
        jQuery(f).html('');
    }
    function verify() {


        if (SubscriptionDNA_GetElement('first_name').value == "") {
            //alert("Please provide First Name");
            var span = SubscriptionDNA_GetElement('first_name_lbl_error');
            var errMsg = "Please provide First Name" ;
            showErrorMsg(span,errMsg);
            SubscriptionDNA_GetElement('first_name').focus();
            return false;
        }

        if (SubscriptionDNA_GetElement('last_name').value == "") {
            //alert("Please provide Last Name");
            var span = SubscriptionDNA_GetElement('last_name_lbl_error');
            var errMsg = "Please provide Last Name" ;
            showErrorMsg(span,errMsg);
            SubscriptionDNA_GetElement('last_name').focus();
            return false;
        }
        if (!validate(SubscriptionDNA_GetElement('email').value)) {
            SubscriptionDNA_GetElement('email').focus();
            return false;
        }
        return true;
    }

    function validate(id) {
        var val=id;
	var checkTLD=1;
	var knownDomsPat=/^(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum)$/;
	var emailPat=/^(.+)@(.+)$/;
	var specialChars="\\(\\)><@,;:\\\\\\\"\\.\\[\\]";
	var validChars="\[^\\s" + specialChars + "\]";
	var quotedUser="(\"[^\"]*\")";
	var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
	var atom=validChars + '+';
	var word="(" + atom + "|" + quotedUser + ")";
	var userPat=new RegExp("^" + word + "(\\." + word + ")*$");
	var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$");
	var matchArray=val.match(emailPat);
	if (matchArray==null) {
		//alert("Please provide valid Email");
		var span = SubscriptionDNA_GetElement('email_lbl_error');
                var errMsg = "Please provide valid Email" ;
                showErrorMsg(span,errMsg);
		return false;
	}
	var user=matchArray[1];
	var domain=matchArray[2];

	for (i=0; i<user.length; i++) {
		if (user.charCodeAt(i)>127) {
                    //alert("Please provide valid Email.");
                    var span = SubscriptionDNA_GetElement('email_lbl_error');
                    var errMsg = "Please provide valid Email" ;
                    showErrorMsg(span,errMsg);
                    return false;
	   }
	}

	for (i=0; i<domain.length; i++) {
		if (domain.charCodeAt(i)>127) {
                    //alert("Please provide valid Email.");
                    var span = SubscriptionDNA_GetElement('email_lbl_error');
                    var errMsg = "Please provide valid Email" ;
                    showErrorMsg(span,errMsg);
                    return false;
		}
	}

	if (user.match(userPat)==null) {
		//alert("Please provide valid Email.");
                var span = SubscriptionDNA_GetElement('email_lbl_error');
                var errMsg = "Please provide valid Email" ;
                showErrorMsg(span,errMsg);
		return false;
	}
	var IPArray=domain.match(ipDomainPat);
	if (IPArray!=null) {
		for (var i=1;i<=4;i++) {
			if (IPArray[i]>255) {
				//alert("Please provide valid Email.");
			//	alert("Destination IP address is invalid!");
                                var span = SubscriptionDNA_GetElement('email_lbl_error');
                                var errMsg = "Please provide valid Email" ;
                                showErrorMsg(span,errMsg);
				return false;
		   	}
		}
		return true;
	}

	var atomPat=new RegExp("^" + atom + "$");
	var domArr=domain.split(".");
	var len=domArr.length;
	for (i=0;i<len;i++) {
		if (domArr[i].search(atomPat)==-1) {
			//alert("Please provide valid Email.");
			//alert("The domain name does not seem to be valid.");
                        var span = SubscriptionDNA_GetElement('email_lbl_error');
                        var errMsg = "Please provide valid Email" ;
                        showErrorMsg(span,errMsg);
			return false;
		}
	}
	if (checkTLD && domArr[domArr.length-1].length!=2 &&
		domArr[domArr.length-1].search(knownDomsPat)==-1) {
		//alert("Please provide valid Email.");
	//	alert("The address must end in a well-known domain or two letter " + "country.");
                var span = SubscriptionDNA_GetElement('email_lbl_error');
                var errMsg = "Please provide valid Email" ;
                showErrorMsg(span,errMsg);
		return false;
	}

	if (len<2) {
		//alert("Please provide valid Email");
		//alert("This address is missing a hostname!");
                var span = SubscriptionDNA_GetElement('email_lbl_error');
                var errMsg = "Please provide valid Email" ;
                showErrorMsg(span,errMsg);
		return false;
	}

/*	length_2 = val.length;
	is_last = val.lastIndexOf('.')+1;

	if(	val.lastIndexOf('@')==-1 || val.lastIndexOf('@')==0 || val.lastIndexOf('@')==val.length-1 || val.lastIndexOf('.')==-1 || length_2==is_last){
		alert("Please provide valid Email");
		return false;
	}*/

	return true;
    }

    function ajax_call() {
        var login_name = document.getElementById('login_name').value;
        get_ajax_data('<?= AJAX_URL ?>?check_available=' + login_name, 'avail_msg');
    }

    function enableSubscription(obj) {
        if (obj.checked == true) {
            SubscriptionDNA_GetElement('subscription_div').style.display = '';
        } else {
            SubscriptionDNA_GetElement('subscription_div').style.display = 'none';
        }
    }

    function dropdown_select(did, val) {
        var dropdownid = SubscriptionDNA_GetElement(did);
        try {
            for (i = dropdownid.options.length - 1; i >= 0; i--) {
                if (dropdownid.options[i].value == val) {
                    dropdownid.options[i].selected = true;
                } else {
                    dropdownid.options[i].selected = false;
                }
            }
        } catch (er) {
        }
    }

    dropdown_select('country', '<?= $profile->country_id; ?>');

</script>