<?php
if(!$_SESSION['is_groupowner'])
{
    ?>
    <script>
    location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['members'])); ?>';					
    </script>
    <?php
    die();
}
$login_name = $_SESSION['login_name'];

if($_REQUEST["del_id"])
{
    $result=SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name,"member_uid"=>$_REQUEST["del_id"]),"group/delete_member",true);
    $_REQUEST['msg']=$result["errDesc"];
}

$groupConfig=SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name),"group/get_configuration",true);
SubscriptionDNA_LoginCheck($groupConfig);

if($_REQUEST["members_added"]=="1")
{
    $_REQUEST['msg']="Your transaction processed successfully. You now have support for ".$groupConfig["maxLimit"]." users.";
}
if($_REQUEST["save_mem_info"])
{
        SubscriptionDNA_MemberInfoForm($groupConfig,$client);
}
else
{
    $members=SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name),"group/members_list",true);
    $added_members=count($members);
    
    if($_REQUEST['msg']=="")
        $_REQUEST['msg']="Total users limit ".$groupConfig["maxLimit"].", ".$added_members." users added, ".($groupConfig["maxLimit"]-$added_members)." remaining.<br>";

	
?>
	<div><a href='?&save_mem_info=1'>Add New Member</a></div><br>
<?php	
	if(count($members)<1){
		echo '<font color="#FF0000">No Members Exist.</font>';
	}
	else
	{
?>
        &nbsp;	
<table id="dna-subscriptions" width="100%" cellpadding="3" cellspacing="0">
			<tr>
				<td colspan="7"><font color="#009933"><?=$_REQUEST['msg']; ?></font></td>
			</tr>
			<tr>	
				<th>Login Name</th>				
				<th>First Name</th>
				<th>Last Name</th>
				<th>Email</th>
				<th>Address</th>
				<th>Join Date</th>
				<th>Action</th>
			</tr>	
				
	<?php
        if($groupConfig["isProfileEdit"])
            $editLabel="Vew/Edit Profile";
        else
            $editLabel="Vew Profile";
        
	
	foreach($members as $member)
	{
			echo "<tr onmouseover=\"this.style.backgroundColor='#ebebeb'\" onmouseout=\"this.style.backgroundColor='';\">
						<td>" . $member["login_name"] . "</td>
						<td>" . $member["first_name"] . "</td>
						<td>" . $member["last_name"] . "</td>
						<td>" . $member["email"] . "</td>
						<td>&nbsp;" . $member["address1"] . "</td>
						<td>" . $member["on_date"] . "</td>
						<td><a href='?&save_mem_info=1&uid=" . $member["uid"] . "'>".$editLabel."</a> | <a onClick=\"if(!confirm('Are you sure you want to delete?')) return(false);\"  href='?&del_id=" . $member["uid"] . "'>Delete</a></td>
					</tr>";
	}

	echo '</table>';

        
}
}

function SubscriptionDNA_MemberInfoForm($groupConfig,$client)
{
$login_name = $_SESSION['login_name'];
if($_POST)
{
    //group_owner_login, login_name, password, first_name, last_name, email, address1, address2, phone, city, state, zipcode, country_id, user_description,
    // status, card_holder_name, credit_card_type, credit_card_number, expiry_month, expiry_year, custom_fields, company_name, job_title, mobile_phone, notify_st, how_referred
    $data=array(
        "group_owner_login"=>$login_name,
        "login_name"=>$_POST['login_name'], 
        "password"=>$_POST['password'], 
        "first_name"=>$_POST['first_name'], 
        "last_name"=>$_POST['last_name'],
        "email"=>$_POST['email'], 
        "address1"=>$_POST['address1'], 
        "address2"=>$_POST['address2'], 
        "phone"=>$_POST['phone'],
        "city"=>$_POST['city'], 
        "state"=>$_POST['state'], 
        "zipcode"=>$_POST['zipcode'], 
        "country_id"=>$_POST['country_id'],
        "user_description"=>$_POST['user_description'],
        "status"=>$_POST['status'],
        "card_holder_name"=>$_REQUEST['card_holder_name'], 
        "credit_card_type"=>$_REQUEST['credit_card_type'], 
        "credit_card_number"=>$_REQUEST['credit_card_number'], 
        "expiry_month"=>$_REQUEST['expiryMonth'], 
        "expiry_year"=>$_REQUEST['expiryYear'],
        "custom_fields"=>$_REQUEST['custom_fields'],
        "company_name"=>$_REQUEST['company_name'],
        "job_title"=>$_REQUEST['job_title'],
        "mobile_phone"=>$_REQUEST['mobile_phone'],
        "notify_st"=>"Email",
        "how_referred"=>$_REQUEST['how_referred']
        );
    if($_REQUEST["uid"]=="")
    {
        $result = SubscriptionDNA_ProcessRequest($data,"group/add_member",true);
        if($result['errCode']!=1){
                $msg='<font color="#FF0000">'.$result['errDesc'].'</font>';
        }else
        {
                $msg=$result['errDesc'];
                ?>
                <script>
                location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['groups'])); ?>?msg=<?php echo($msg); ?>';					
                </script>
                <?php
        }
    }
    else
    {
        $data["member_uid"]=$_REQUEST["uid"];
        $result = SubscriptionDNA_ProcessRequest($data,"group/update_member",true);
        if($result['errCode']!=6){
                $msg='<font color="#FF0000">'.$result['errDesc'].'</font>';
        }else{
                $msg=$result['errDesc'];
                ?>
                <script>
                location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['groups'])); ?>?msg=<?php echo($msg); ?>';					
                </script>
                <?php
        }
        
    }
}


if($groupConfig["isProfileEdit"] or $_REQUEST["uid"]=="")
    $fields_readonly="";
else
    $fields_readonly="readonly disabled ";

?>
<form name="profile-form" method="post" action="">
<input name="uid" id="uid" type="hidden" value="<?php echo($_REQUEST["uid"]); ?>" />

<div id="dna-heading">
    <?php
    if($_REQUEST["uid"]!="")
    {
        echo("Edit Member Info");
        if(!$_POST)
        {
            $groupMember =SubscriptionDNA_ProcessRequest(array("login_name"=>$login_name,"member_uid"=>$_REQUEST["uid"]),"group/members_list",true);
            $groupMember=$groupMember[0];
            foreach($groupMember as $key=>$value)
            {
                $_REQUEST[$key]=$value;
            }
        }    
    }
    else
        echo("Add Member");
    ?>
    
</div>






  <fieldset>
  <legend>Account Info</legend>
  <table border="0" cellpadding="0" cellspacing="1" width="100%">
      <tbody><tr>
    <td colspan="4" id="avail_msg"><?=$msg ?></td>
</tr>    
<tr>
      <td class="field_title"><label for="login_name">User Name: <span class="require">*</span></label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?> name="login_name" id="login_name" tabindex="1" required="yes" field="User Name" control="string" value="<?php echo($_REQUEST["login_name"]); ?>" pattern="^([a-zA-Z0-9_\.\-\@])+$">
        </td>
      <td class="field_title"><label for="password">Password: <span class="require">*</span></label></td>
      <td class="field_data"><input type="password" <?php echo($fields_readonly); ?>  id="password" tabindex="2" name="password" value="<?php echo($_REQUEST["password"]); ?>" required="yes" field="Password"></td>
</tr>
  </tbody></table>
  </fieldset>
  <br>
  <fieldset>
  <legend>Personal Information</legend>
  <table border="0" cellpadding="0" cellspacing="1" width="100%">
    <tbody><tr>
      <td class="field_title"><label for="first_name">First Name: <span class="require">*</span></label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?>  name="first_name" id="first_name" tabindex="4" value="<?php echo($_REQUEST["first_name"]); ?>" required="yes" field="First Name" class="text"></td>
      <td class="field_title"><label for="address1">Address 1:</label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?>  name="address1" id="address1" tabindex="9" control="memo" class="text" value="<?php echo($_REQUEST["address1"]); ?>"></td>
    </tr>
    <tr>
      <td class="field_title"><label for="last_name">Last Name: <span class="require">*</span></label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?>  name="last_name" id="last_name" tabindex="5" value="<?php echo($_REQUEST["last_name"]); ?>" required="yes" field="Last Name" class="text"></td>
      <td class="field_title"><label for="address2">Address 2:</label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?>  name="address2" id="address2" tabindex="10" control="memo" class="text" value="<?php echo($_REQUEST["address2"]); ?>"></td>
    </tr>
    <tr>
      <td class="field_title"><label for="phone">Phone:</label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?>  name="phone" id="phone" tabindex="6" value="<?php echo($_REQUEST["phone"]); ?>" class="text"></td>
      <td class="field_title"><label for="city">City:</label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?>  name="city" id="city" tabindex="11" value="<?php echo($_REQUEST["city"]); ?>" class="text"></td>
    </tr>
    <tr>
      <td class="field_title"><label for="email">Email: <span class="require">*</span></label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?>  id="email" tabindex="7" name="email" value="<?php echo($_REQUEST["email"]); ?>" required="yes" field="Email" control="string" pattern="^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$" ajaxpath="" class="text"></td>
      <td class="field_title"><label for="state">State:</label></td>
      <td class="field_data"><select <?php echo($fields_readonly); ?> name="state" id="state" tabindex="12" field="State">
<option label="&lt;--Please Select--&gt;" value="">&lt;--Please Select--&gt;</option>
<option label="Alabama" value="AL">Alabama</option>
<option label="Alaska" value="AK">Alaska</option>
<option label="Arizona" value="AZ">Arizona</option>
<option label="Arkansas" value="AR">Arkansas</option>
<option label="California" value="CA">California</option>
<option label="Colorado" value="CO">Colorado</option>
<option label="Connecticut" value="CT">Connecticut</option>
<option label="Delaware" value="DE">Delaware</option>
<option label="Florida" value="FL">Florida</option>
<option label="Georgia" value="GA">Georgia</option>
<option label="Hawaii" value="HI">Hawaii</option>
<option label="Idaho" value="ID">Idaho</option>
<option label="Illinois" value="IL">Illinois</option>
<option label="Indiana" value="IN">Indiana</option>
<option label="Iowa" value="IA">Iowa</option>
<option label="Kansas" value="KS">Kansas</option>
<option label="Kentucky" value="KY">Kentucky</option>
<option label="Louisiana" value="LA">Louisiana</option>
<option label="Maine" value="ME">Maine</option>
<option label="Maryland" value="MD">Maryland</option>
<option label="Massachusetts" value="MA">Massachusetts</option>
<option label="Michigan" value="MI">Michigan</option>
<option label="Minnesota" value="MN">Minnesota</option>
<option label="Mississippi" value="MS">Mississippi</option>
<option label="Missouri" value="MO">Missouri</option>
<option label="Montana" value="MT">Montana</option>
<option label="Nebraska" value="NE">Nebraska</option>
<option label="Nevada" value="NV">Nevada</option>
<option label="New Hampshire" value="NH">New Hampshire</option>
<option label="New Jersey" value="NJ">New Jersey</option>
<option label="New Mexico" value="NM">New Mexico</option>
<option label="New York" value="NY">New York</option>
<option label="North Carolina" value="NC">North Carolina</option>
<option label="North Dakota" value="ND">North Dakota</option>
<option label="Ohio" value="OH">Ohio</option>
<option label="Oklahoma" value="OK">Oklahoma</option>
<option label="Oregon" value="OR">Oregon</option>
<option label="Pennsylvania" value="PA">Pennsylvania</option>
<option label="Puerto Rico" value="PR">Puerto Rico</option>
<option label="Rhode Island" value="RI">Rhode Island</option>
<option label="South Carolina" value="SC">South Carolina</option>
<option label="South Dakota" value="SD">South Dakota</option>
<option label="Tennessee" value="TN">Tennessee</option>
<option label="Texas" value="TX">Texas</option>
<option label="Utah" value="UT">Utah</option>
<option label="Vermont" value="VT">Vermont</option>
<option label="Virginia" value="VA">Virginia</option>
<option label="Washington" value="WA">Washington</option>
<option label="Washington D.C." value="DC">Washington D.C.</option>
<option label="West Virginia" value="WV">West Virginia</option>
<option label="Wisconsin" value="WI">Wisconsin</option>
<option label="Wyoming" value="WY">Wyoming</option>
<option label="Not Applicable" value="N/A">Not Applicable</option>
</select>
</td>
    </tr>
    <tr>
      <td class="field_title"><label for="status">Status: </label></td>
      <td class="field_data"><select <?php echo($fields_readonly); ?> name="status" id="status" tabindex="8">
<option label="Active" value="Active">Active</option>
<option label="Suspended" value="Suspended">Suspended</option>
</select>
</td>
      <td class="field_title"><label for="zipcode">Zip:</label></td>
      <td class="field_data"><input type="text" <?php echo($fields_readonly); ?>  name="zipcode" id="zipcode" tabindex="13" value="<?php echo($_REQUEST["zipcode"]); ?>"></td>
    </tr>
    <tr>
      <td class="field_title">Description:</td>
      <td class="field_data"><input name="user_description" style="width:60%" type="text" <?php echo($fields_readonly); ?>  id="user_description" tabindex="14" value="<?php echo($_REQUEST["user_description"]); ?>"></td>
      <td class="field_title"><label for="country">Country:</label></td>
      <td class="field_data"><select <?php echo($fields_readonly); ?> name="country_id" id="country_id" tabindex="14" field="Country">
<option label="&lt;--Please Select--&gt;" value="">&lt;--Please Select--&gt;</option>
<option label="United States" value="223">United States</option>
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
</td>
    </tr>
    
    
    
    
  </tbody></table>
  </fieldset>


  	
  
<fieldset>
  <legend>Subscription Information</legend>
  <table  cellpadding="2" cellspacing="2" border="0">
    <tbody><tr> 
      <td class="field_title" valign="top" width="50"><label>Service: </label></td>
      <td class="field_data" align="left"><?php echo($groupConfig["service"]); ?></td>
	</tr>
    <tr> 
      <td class="field_title" valign="top"><label>Billing Routine: </label></td>
      <td class="field_data"><?php echo($groupConfig["billing"]); ?></td>
	</tr>
    <tr> 
      <td class="field_title" valign="top" width="120"><label>Billing Description: </label></td>
      <td class="field_data"><?php echo($groupConfig["billing_description"]); ?></td>
	</tr>
  </tbody></table>
  </fieldset>

  <fieldset>
 <?php
 if($groupConfig["isOwnerPayee"]!="1" && $_REQUEST["uid"]=="")
 {
 ?> 
 <fieldset>
<legend>Credit Card Info</legend>
  <table width="100%" cellpadding="2" cellspacing="2" border="0">
    <tr>
      <td class="field_title" valign="top">Card Holder Name: <span class="require">*</span></td>
      <td class="field_data" valign="top"><input type="text" name="card_holder_name" id="card_holder_name" value="<?php echo($_REQUEST["card_holder_name"]); ?>" tabindex="16"   required="yes" field="Card Holder Name" />
      </td>
      <td class="field_title" valign="top">Card Type: <span class="require">*</span></td>
      <td class="field_data" valign="top"> <select name="credit_card_type" id="credit_card_type" tabindex="18">
<option label="American Express" value="American Express">American Express</option>
<option label="Discover" value="Discover">Discover</option>
<option label="MasterCard" value="MasterCard">MasterCard</option>
<option label="Visa" value="Visa">Visa</option>
</select>
 </td>
    </tr>
    <tr>
      <td class="field_title" valign="top">Card Number: <span class="require">*</span></td>
      <td class="field_data" valign="top"><input type="text" name="credit_card_number" id="credit_card_number" tabindex="17"   required="yes" field="Card Number" />
      </td>
      <td class="field_title" valign="top">Card Expiry: <span class="require">*</span></td>
      <td class="field_data" valign="top"> <select name="expiryMonth">
<option label="January" value="01">January</option>
<option label="February" value="02">February</option>
<option label="March" value="03">March</option>
<option label="April" value="04">April</option>
<option label="May" value="05">May</option>
<option label="June" value="06" selected="selected">June</option>
<option label="July" value="07">July</option>
<option label="August" value="08">August</option>
<option label="September" value="09">September</option>
<option label="October" value="10">October</option>
<option label="November" value="11">November</option>
<option label="December" value="12">December</option>
</select>

<select name="expiryYear">
    <?php
    $year=date("Y");
    for($i=$year;$i<=$year+9;$i++)
    {
        ?><option value='<?php echo($i); ?>'><?php echo($i); ?></option><?php
    }
    ?>
</select> </td>
    </tr>
  </table>
  </fieldset>
    <script>
        document.getElementById("credit_card_type").value="<?php echo($_REQUEST["credit_card_type"]); ?>";
        document.getElementById("expiryMonth").value="<?php echo($_REQUEST["expiryMonth"]); ?>";
        document.getElementById("expiryYear").value="<?php echo($_REQUEST["expiryYear"]); ?>";
    </script>
 <?php
 }
 ?>     

      <script>
          document.getElementById("country_id").value="<?php echo($_REQUEST["country_id"]); ?>";
          document.getElementById("state").value="<?php echo($_REQUEST["state"]); ?>";
          document.getElementById("status").value="<?php echo($_REQUEST["status"]); ?>";
      </script>
  
  <div class="right">
    <?php
    if($groupConfig["isProfileEdit"] or $_REQUEST["uid"]=="")
    {
    ?>  
    <input type="submit" name="bttnSubmit" value="Save">
    <?php
    }
    ?>
    &nbsp;
    <?php
    if($_REQUEST["uid"]=="")
    {
    ?> 
    <input type="button" name="back" value="Back" onclick="location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['groups'])); ?>';">
    <?php
    }
    else
    {
        ?>
        &nbsp;
        <input type="button" name="back" value="Back" onclick="location.href='<?php echo(get_permalink($GLOBALS['SubscriptionDNA']['Settings']["dna_pages"]['groups'])); ?>';">
        <?php
    }
    ?>
  </div>

    
</fieldset></form>


<?php
}
?>

