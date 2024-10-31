<?php include "head.tpl.php"; ?>
<?php if ($cert == 'create') { ?>
<h3>Create Your Certificate</h3>
<?php if ($fp->hasError('Fatal')) { ?><div class="error"><?php echo nl2br($fp->getError('Fatal')) ?></div><?php } ?>
<form method="post" action="<?php echo $url.htmlentities('&sub='.$sub) ?>" enctype="application/x-www-form-urlencoded; charset=utf-8">
  <input type="hidden" name="pn_action" value="create_cert" />
  <table border="0" cellpadding="0" cellspacing="0" class="form-table">
	<tr>
	  <th><label for="commonName"><?php _e('Your Name') ?></label></th>
	  <td>
		<code><?php echo $fp->commonName ?></code>
		<span class="setting-description">(<?php _e('You can change this by changing your WP display name in your Profile section.') ?>)</span>
		<input type="hidden" name="commonName" value="<?php echo $fp->commonName ?>" />
	  </td>
	</tr>
	<tr>
	  <th><label for="organizationName"><?php _e('WP URL') ?></label></th>
	  <td>
		<input id="organizationName" class="regular-text code" type="text" name="organizationName" value="<?php echo $fp->organizationName ?>" size="50" />
		<?php if ($fp->hasError('organizationName')) { ?><span class="error"><?php echo $fp->getError('organizationName') ?></span><?php } ?>
	  </td>
	</tr>
	<tr>
	  <th><label for="subjectAltName"><?php _e('Email') ?></label></th>
	  <td>
		<input id="subjectAltName" class="regular-text code" type="text" name="subjectAltName" value="<?php echo $fp->subjectAltName ?>" size="30" />
		<?php if ($fp->hasError('subjectAltName')) { ?><span class="error"><?php echo $fp->getError('subjectAltName') ?></span><?php } ?>
	  </td>
	</tr>
	<tr>
	  <th><label for="countryName"><?php _e('Country') ?></label></th>
	  <td>
		<select name="countryName" id="countryName">
 		  <option value="" selected="selected">Select Country</option> 
		  <optgroup label="&nbsp;">
  			<option value="AF"><?php _e("Afghanistan") ?> [AF]</option> 
  			<option value="AL"><?php _e("Albania") ?> [AL]</option> 
  			<option value="DZ"><?php _e("Algeria") ?> [DZ]</option> 
  			<option value="AS"><?php _e("American Samoa") ?> [AS]</option> 
 			<option value="AD"><?php _e("Andorra") ?> [AD]</option> 
  			<option value="AO"><?php _e("Angola") ?> [AO]</option> 
  			<option value="AI"><?php _e("Anguilla") ?> [AI]</option> 
  			<option value="AQ"><?php _e("Antarctica") ?> [AQ]</option> 
  			<option value="AG"><?php _e("Antigua and Barbuda") ?> [AG]</option> 
  			<option value="AR"><?php _e("Argentina") ?> [AR]</option> 
  			<option value="AM"><?php _e("Armenia") ?> [AM]</option> 
  			<option value="AW"><?php _e("Aruba") ?> [AW]</option> 
  			<option value="AU"><?php _e("Australia") ?> [AU]</option> 
  			<option value="AT"><?php _e("Austria") ?> [AT]</option> 
  			<option value="AZ"><?php _e("Azerbaijan") ?> [AZ]</option> 
  			<option value="BS"><?php _e("Bahamas") ?> [BS]</option> 
  			<option value="BH"><?php _e("Bahrain") ?> [BH]</option> 
  			<option value="BD"><?php _e("Bangladesh") ?> [BD]</option> 
  			<option value="BB"><?php _e("Barbados") ?> [BB]</option> 
  			<option value="BY"><?php _e("Belarus") ?> [BY]</option> 
  			<option value="BE"><?php _e("Belgium") ?> [BE]</option> 
  			<option value="BZ"><?php _e("Belize") ?> [BZ]</option> 
  			<option value="BJ"><?php _e("Benin") ?> [BJ]</option> 
  			<option value="BM"><?php _e("Bermuda") ?> [BM]</option> 
  			<option value="BT"><?php _e("Bhutan") ?> [BT]</option> 
  			<option value="BO"><?php _e("Bolivia") ?> [BO]</option> 
  			<option value="BA"><?php _e("Bosnia and Herzegovina") ?> [BA]</option> 
  			<option value="BW"><?php _e("Botswana") ?> [BW]</option> 
  			<option value="BV"><?php _e("Bouvet Island") ?> [BV]</option> 
  			<option value="BR"><?php _e("Brazil") ?> [BR]</option> 
  			<option value="IO"><?php _e("British Indian Ocean Territory") ?> [IO]</option> 
  			<option value="BN"><?php _e("Brunei Darussalam") ?> [BN]</option> 
  			<option value="BG"><?php _e("Bulgaria") ?> [BG]</option> 
  			<option value="BF"><?php _e("Burkina Faso") ?> [BF]</option> 
  			<option value="BI"><?php _e("Burundi") ?> [BI]</option> 
  			<option value="KH"><?php _e("Cambodia") ?> [KH]</option> 
  			<option value="CM"><?php _e("Cameroon") ?> [CM]</option> 
  			<option value="CA"><?php _e("Canada") ?> [CA]</option> 
  			<option value="CV"><?php _e("Cape Verde") ?> [CV]</option> 
  			<option value="KY"><?php _e("Cayman Islands") ?> [KY]</option> 
  			<option value="CF"><?php _e("Central African Republic") ?> [CF]</option> 
  			<option value="TD"><?php _e("Chad") ?> [TD]</option> 
  			<option value="CL"><?php _e("Chile") ?> [CL]</option> 
  			<option value="CN"><?php _e("China") ?> [CN]</option> 
  			<option value="CX"><?php _e("Christmas Island") ?> [CX]</option> 
  			<option value="CC"><?php _e("Cocos (Keeling) Islands") ?> [CC]</option> 
  			<option value="CO"><?php _e("Colombia") ?> [CO]</option> 
  			<option value="KM"><?php _e("Comoros") ?> [KM]</option> 
  			<option value="CG"><?php _e("Congo") ?> []</option> 
  			<option value="CD"><?php _e("Congo, The Democratic Republic of The") ?> [CD]</option> 
  			<option value="CK"><?php _e("Cook Islands") ?> [CK]</option> 
  			<option value="CR"><?php _e("Costa Rica") ?> [CR]</option> 
  			<option value="CI"><?php _e("Cote D'ivoire") ?> [CI]</option> 
  			<option value="HR"><?php _e("Croatia") ?> [HR]</option> 
  			<option value="CU"><?php _e("Cuba") ?> [CU]</option> 
  			<option value="CY"><?php _e("Cyprus") ?> [CY]</option> 
  			<option value="CZ"><?php _e("Czech Republic") ?> [CZ]</option> 
  			<option value="DK"><?php _e("Denmark") ?> [DK]</option> 
  			<option value="DJ"><?php _e("Djibouti") ?> [DJ]</option> 
  			<option value="DM"><?php _e("Dominica") ?> [DM]</option> 
  			<option value="DO"><?php _e("Dominican Republic") ?> [DO]</option> 
  			<option value="EC"><?php _e("Ecuador") ?> [EC]</option> 
  			<option value="EG"><?php _e("Egypt") ?> [EG]</option> 
  			<option value="SV"><?php _e("El Salvador") ?> [SV]</option> 
  			<option value="GQ"><?php _e("Equatorial Guinea") ?> [GQ]</option> 
  			<option value="ER"><?php _e("Eritrea") ?> [ER]</option> 
  			<option value="EE"><?php _e("Estonia") ?> [EE]</option> 
  			<option value="ET"><?php _e("Ethiopia") ?> [ET]</option> 
  			<option value="FK"><?php _e("Falkland Islands (Malvinas)") ?> [FK]</option> 
  			<option value="FO"><?php _e("Faroe Islands") ?> [FO]</option> 
  			<option value="FJ"><?php _e("Fiji") ?> [FJ]</option> 
  			<option value="FI"><?php _e("Finland") ?> [FI]</option> 
  			<option value="FR"><?php _e("France") ?> [FR]</option> 
  			<option value="GF"><?php _e("French Guiana") ?> [GF]</option> 
  			<option value="PF"><?php _e("French Polynesia") ?> [PF]</option> 
  			<option value="TF"><?php _e("French Southern Territories") ?> [TF]</option> 
  			<option value="GA"><?php _e("Gabon") ?> [GA]</option> 
  			<option value="GM"><?php _e("Gambia") ?> [GM]</option> 
  			<option value="GE"><?php _e("Georgia") ?> [GE]</option> 
  			<option value="DE"><?php _e("Germany") ?> [DE]</option> 
  			<option value="GH"><?php _e("Ghana") ?> [GH]</option> 
  			<option value="GI"><?php _e("Gibraltar") ?> [GI]</option> 
  			<option value="GR"><?php _e("Greece") ?> [GR]</option> 
  			<option value="GL"><?php _e("Greenland") ?> [GL]</option> 
  			<option value="GD"><?php _e("Grenada") ?> [GD]</option> 
  			<option value="GP"><?php _e("Guadeloupe") ?> [GP]</option> 
  			<option value="GU"><?php _e("Guam") ?> [GU]</option> 
  			<option value="GT"><?php _e("Guatemala") ?> [GT]</option> 
  			<option value="GN"><?php _e("Guinea") ?> [GN]</option> 
  			<option value="GW"><?php _e("Guinea-bissau") ?> [GW]</option> 
  			<option value="GY"><?php _e("Guyana") ?> [GY]</option> 
  			<option value="HT"><?php _e("Haiti") ?> [HT]</option> 
  			<option value="HM"><?php _e("Heard Island and Mcdonald Islands") ?> [HM]</option> 
  			<option value="VA"><?php _e("Holy See (Vatican City State)") ?> [VA]</option> 
  			<option value="HN"><?php _e("Honduras") ?> [HN]</option> 
  			<option value="HK"><?php _e("Hong Kong") ?> [HK]</option> 
  			<option value="HU"><?php _e("Hungary") ?> [HU]</option> 
  			<option value="IS"><?php _e("Iceland") ?> [IS]</option> 
  			<option value="IN"><?php _e("India") ?> [IN]</option> 
  			<option value="ID"><?php _e("Indonesia") ?> [ID]</option> 
  			<option value="IR"><?php _e("Iran, Islamic Republic of") ?> [IR]</option> 
  			<option value="IQ"><?php _e("Iraq") ?> [IQ]</option> 
  			<option value="IE"><?php _e("Ireland") ?> [IE]</option> 
  			<option value="IL"><?php _e("Israel") ?> [IL]</option> 
  			<option value="IT"><?php _e("Italy") ?> [IT]</option> 
  			<option value="JM"><?php _e("Jamaica") ?> [JM]</option> 
  			<option value="JP"><?php _e("Japan") ?> [JP]</option> 
  			<option value="JO"><?php _e("Jordan") ?> [JO]</option> 
  			<option value="KZ"><?php _e("Kazakhstan") ?> [KZ]</option> 
  			<option value="KE"><?php _e("Kenya") ?> [KE]</option> 
  			<option value="KI"><?php _e("Kiribati") ?> [KI]</option> 
  			<option value="KP"><?php _e("Korea, Democratic People's Republic of") ?> [KP]</option> 
  			<option value="KR"><?php _e("Korea, Republic of") ?> [KR]</option> 
  			<option value="KW"><?php _e("Kuwait") ?> [KW]</option> 
  			<option value="KG"><?php _e("Kyrgyzstan") ?> [KG]</option> 
  			<option value="LA"><?php _e("Lao People's Democratic Republic") ?> [LA]</option> 
  			<option value="LV"><?php _e("Latvia") ?> [LV]</option> 
  			<option value="LB"><?php _e("Lebanon") ?> [LB]</option> 
  			<option value="LS"><?php _e("Lesotho") ?> [LS]</option> 
  			<option value="LR"><?php _e("Liberia") ?> [LR]</option> 
  			<option value="LY"><?php _e("Libyan Arab Jamahiriya") ?> [LY]</option> 
  			<option value="LI"><?php _e("Liechtenstein") ?> [LI]</option> 
  			<option value="LT"><?php _e("Lithuania") ?> [LT]</option> 
  			<option value="LU"><?php _e("Luxembourg") ?> [LU]</option> 
  			<option value="MO"><?php _e("Macao") ?> [MO]</option> 
  			<option value="MK"><?php _e("Macedonia, The Former Yugoslav Republic of") ?> [MK]</option> 
  			<option value="MG"><?php _e("Madagascar") ?> [MG]</option> 
  			<option value="MW"><?php _e("Malawi") ?> [MW]</option> 
  			<option value="MY"><?php _e("Malaysia") ?> [MY]</option> 
  			<option value="MV"><?php _e("Maldives") ?> [MV]</option> 
  			<option value="ML"><?php _e("Mali") ?> [ML]</option> 
  			<option value="MT"><?php _e("Malta") ?> [MT]</option> 
  			<option value="MH"><?php _e("Marshall Islands") ?> [MH]</option> 
  			<option value="MQ"><?php _e("Martinique") ?> [MQ]</option> 
  			<option value="MR"><?php _e("Mauritania") ?> [MR]</option> 
  			<option value="MU"><?php _e("Mauritius") ?> [MU]</option> 
  			<option value="YT"><?php _e("Mayotte") ?> [YT]</option> 
  			<option value="MX"><?php _e("Mexico") ?> [MX]</option> 
  			<option value="FM"><?php _e("Micronesia, Federated States of") ?> [FM]</option> 
  			<option value="MD"><?php _e("Moldova, Republic of") ?> [MD]</option> 
  			<option value="MC"><?php _e("Monaco") ?> [MC]</option> 
  			<option value="MN"><?php _e("Mongolia") ?> [MN]</option> 
  			<option value="MS"><?php _e("Montserrat") ?> [MS]</option> 
  			<option value="MA"><?php _e("Morocco") ?> [MA]</option> 
  			<option value="MZ"><?php _e("Mozambique") ?> [MZ]</option> 
  			<option value="MM"><?php _e("Myanmar") ?> [MM]</option> 
  			<option value="NA"><?php _e("Namibia") ?> [NA]</option> 
  			<option value="NR"><?php _e("Nauru") ?> [NR]</option> 
  			<option value="NP"><?php _e("Nepal") ?> [NP]</option> 
  			<option value="NL"><?php _e("Netherlands") ?> [NL]</option> 
  			<option value="AN"><?php _e("Netherlands Antilles") ?> [AN]</option> 
  			<option value="NC"><?php _e("New Caledonia") ?> [NC]</option> 
  			<option value="NZ"><?php _e("New Zealand") ?> [NZ]</option> 
  			<option value="NI"><?php _e("Nicaragua") ?> [NI]</option> 
  			<option value="NE"><?php _e("Niger") ?> [NE]</option> 
  			<option value="NG"><?php _e("Nigeria") ?> [NG]</option> 
  			<option value="NU"><?php _e("Niue") ?> [NU]</option> 
  			<option value="NF"><?php _e("Norfolk Island") ?> [NF]</option> 
  			<option value="MP"><?php _e("Northern Mariana Islands") ?> [MP]</option> 
  			<option value="NO"><?php _e("Norway") ?> [NO]</option> 
  			<option value="OM"><?php _e("Oman") ?> [OM]</option> 
  			<option value="PK"><?php _e("Pakistan") ?> [PK]</option> 
  			<option value="PW"><?php _e("Palau") ?> [PW]</option> 
  			<option value="PS"><?php _e("Palestinian Territory, Occupied") ?> [PS]</option> 
  			<option value="PA"><?php _e("Panama") ?> [PA]</option> 
  			<option value="PG"><?php _e("Papua New Guinea") ?> [PG]</option> 
  			<option value="PY"><?php _e("Paraguay") ?> [PY]</option> 
  			<option value="PE"><?php _e("Peru") ?> [PE]</option> 
  			<option value="PH"><?php _e("Philippines") ?> [PH]</option> 
  			<option value="PN"><?php _e("Pitcairn") ?> [PN]</option> 
  			<option value="PL"><?php _e("Poland") ?> [PL]</option> 
  			<option value="PT"><?php _e("Portugal") ?> [PT]</option> 
  			<option value="PR"><?php _e("Puerto Rico") ?> [PR]</option> 
  			<option value="QA"><?php _e("Qatar") ?> [QA]</option> 
  			<option value="RE"><?php _e("Reunion") ?> [RE]</option> 
  			<option value="RO"><?php _e("Romania") ?> [RO]</option> 
  			<option value="RU"><?php _e("Russian Federation") ?> [RU]</option> 
  			<option value="RW"><?php _e("Rwanda") ?> [RW]</option> 
  			<option value="SH"><?php _e("Saint Helena") ?> [SH]</option> 
  			<option value="KN"><?php _e("Saint Kitts and Nevis") ?> [KN]</option> 
  			<option value="LC"><?php _e("Saint Lucia") ?> [LC]</option> 
  			<option value="PM"><?php _e("Saint Pierre and Miquelon") ?> [PM]</option> 
  			<option value="VC"><?php _e("Saint Vincent and The Grenadines") ?> [VC]</option> 
  			<option value="WS"><?php _e("Samoa") ?> [WS]</option> 
  			<option value="SM"><?php _e("San Marino") ?> [SM]</option> 
  			<option value="ST"><?php _e("Sao Tome and Principe") ?> [ST]</option> 
  			<option value="SA"><?php _e("Saudi Arabia") ?> [SA]</option> 
  			<option value="SN"><?php _e("Senegal") ?> [SN]</option> 
  			<option value="CS"><?php _e("Serbia and Montenegro") ?> [CS]</option> 
  			<option value="SC"><?php _e("Seychelles") ?> [SC]</option> 
  			<option value="SL"><?php _e("Sierra Leone") ?> [SL]</option> 
  			<option value="SG"><?php _e("Singapore") ?> [SG]</option> 
  			<option value="SK"><?php _e("Slovakia") ?> [SK]</option> 
  			<option value="SI"><?php _e("Slovenia") ?> [SI]</option> 
  			<option value="SB"><?php _e("Solomon Islands") ?> [SB]</option> 
  			<option value="SO"><?php _e("Somalia") ?> [SO]</option> 
  			<option value="ZA"><?php _e("South Africa") ?> [ZA]</option> 
  			<option value="GS"><?php _e("South Georgia and The South Sandwich Islands") ?> [GS]</option> 
  			<option value="ES"><?php _e("Spain") ?> [ES]</option> 
  			<option value="LK"><?php _e("Sri Lanka") ?> [LK]</option> 
  			<option value="SD"><?php _e("Sudan") ?> [SD]</option> 
  			<option value="SR"><?php _e("Suriname") ?> [SR]</option> 
  			<option value="SJ"><?php _e("Svalbard and Jan Mayen") ?> [SJ]</option> 
  			<option value="SZ"><?php _e("Swaziland") ?> [SZ]</option> 
  			<option value="SE"><?php _e("Sweden") ?> [SE]</option> 
  			<option value="CH"><?php _e("Switzerland") ?> [CH]</option> 
  			<option value="SY"><?php _e("Syrian Arab Republic") ?> [SY]</option> 
  			<option value="TW"><?php _e("Taiwan, Province of China") ?> [TW]</option> 
  			<option value="TJ"><?php _e("Tajikistan") ?> [TJ]</option> 
  			<option value="TZ"><?php _e("Tanzania, United Republic of") ?> [TZ]</option> 
  			<option value="TH"><?php _e("Thailand") ?> [TH]</option> 
  			<option value="TL"><?php _e("Timor-leste") ?> [TL]</option> 
  			<option value="TG"><?php _e("Togo") ?> [TG]</option> 
  			<option value="TK"><?php _e("Tokelau") ?> [TK]</option> 
  			<option value="TO"><?php _e("Tonga") ?> [TO]</option> 
  			<option value="TT"><?php _e("Trinidad and Tobago") ?> [TT]</option> 
  			<option value="TN"><?php _e("Tunisia") ?> [TN]</option> 
  			<option value="TR"><?php _e("Turkey") ?> [TR]</option> 
  			<option value="TM"><?php _e("Turkmenistan") ?> [TM]</option> 
  			<option value="TC"><?php _e("Turks and Caicos Islands") ?> [TC]</option> 
  			<option value="TV"><?php _e("Tuvalu") ?> [TV]</option> 
  			<option value="UG"><?php _e("Uganda") ?> [UG]</option> 
  			<option value="UA"><?php _e("Ukraine") ?> [UA]</option> 
  			<option value="AE"><?php _e("United Arab Emirates") ?> [AE]</option> 
  			<option value="GB"><?php _e("United Kingdom") ?> [GB]</option> 
  			<option value="US"><?php _e("United States") ?> [US]</option> 
  			<option value="UM"><?php _e("United States Minor Outlying Islands") ?> [UM]</option> 
  			<option value="UY"><?php _e("Uruguay") ?> [UY]</option> 
  			<option value="UZ"><?php _e("Uzbekistan") ?> [UZ]</option> 
  			<option value="VU"><?php _e("Vanuatu") ?> [VU]</option> 
  			<option value="VE"><?php _e("Venezuela") ?> [VE]</option> 
  			<option value="VN"><?php _e("Viet Nam") ?> [VN]</option> 
  			<option value="VG"><?php _e("Virgin Islands, British") ?> [VG]</option> 
  			<option value="VI"><?php _e("Virgin Islands, U.S.") ?> [VI]</option> 
  			<option value="WF"><?php _e("Wallis and Futuna") ?> [WF]</option> 
  			<option value="EH"><?php _e("Western Sahara") ?> [EH]</option> 
  			<option value="YE"><?php _e("Yemen") ?> [YE]</option> 
  			<option value="ZM"><?php _e("Zambia") ?> [ZM]</option> 
  			<option value="ZW"><?php _e("Zimbabwe") ?> [ZW]</option>
		  </optgroup>
		</select>
 	    <?php if ($fp->hasError('countryName')) { ?><span class="error"><?php echo $fp->getError('countryName') ?></span><?php } ?>
	  </td>
	</tr>
	<tr>
	  <th><label for="stateOrProvinceName"><?php _e('State or Province') ?></label></th>
	  <td><input id="stateOrProvinceName" class="regular-text code" type="text" name="stateOrProvinceName" value="<?php echo $fp->stateOrProvinceName ?>" size="30" />
		<span class="setting-description">(<?php _e('Optional') ?>)</span>
	  </td>
	</tr>
	<tr>
	  <th><label for="localityName"><?php _e('City') ?></label></th>
	  <td>
		<input id="localityName" class="regular-text code" type="text" name="localityName" value="<?php echo $fp->localityName ?>" size="30" />
        <?php if ($fp->hasError('localityName')) { ?><span class="error"><?php echo $fp->getError('localityName') ?></span><?php } ?>
	  </td>
	</tr>
  </table>
  <input type="hidden" name="name" value="<?php echo $fp->name ?>" />
  <p class="submit"><input class="button-primary" type="submit" value="<?php _e('Create') ?>" /></p>
</form>
<?php } else { ?>			
<h3>Your Certificate</h3>
<form method="post" action="<?php echo $url.htmlentities('&sub='.$sub) ?>" enctype="application/x-www-form-urlencoded; charset=utf-8">
  <input type="hidden" name="pn_action" value="delete_cert" />
  <table border="0" cellpadding="0" cellspacing="0" class="form-table">
	<tr>
	  <th><?php _e('Identity Key') ?></th>
	  <td><?php echo $fp->identity_key ?></td>
	</tr>
	<tr>
	  <th><?php _e('Name') ?></th>
	  <td><code><?php echo $fp->subject['commonName'] ?></code></td>
	</tr>
	<tr>
	  <th><?php _e('URL') ?></th>
	  <td><code><?php echo $fp->subject['organizationName'] ?></code></td>
	</tr>
	<tr>
	  <th><?php _e('Email') ?></th>
	  <td><code><?php echo $fp->subject['X509v3 Subject Alternative Name'] ?></code></td>
	</tr>
	<tr>
	  <th><?php _e('Country') ?></th>
	  <td><code><?php echo $fp->subject['countryName'] ?></code></td>
	</tr>
	<?php if ($fp->subject['stateOrProvinceName'] && ($fp->subject['stateOrProvinceName'] != $fp->subject['countryName'])) { ?>
	<tr>
	  <th><?php _e('State') ?></th>
	  <td><code><?php echo $fp->subject['stateOrProvinceName'] ?></code></td>
	</tr>
	<?php } ?>
	<tr>
	  <th><?php _e('City') ?></th>
	  <td><code><?php echo $fp->subject['localityName'] ?></code></td>
	</tr>
	<tr>
	  <th><?php _e('Expire Date') ?></th>
	  <td><code><?php echo date("r", $fp->validTo) ?></code></td>
	</tr>
	<tr>
	  <th scope="row"><label><?php _e('Check to delete') ?></label></th>
	  <td>
		<fieldset>
		  <label><input type="checkbox" name="dodelete" value="yes" /></label>
		  <?php if ($fp->hasError('dodelete')) { ?><span class="error" style="font-weight:normal;"><?php echo $fp->getError('dodelete') ?></span><?php } ?>
		</fieldset>
	  </td>
	</tr>
  </table>
  <p class="submit"><input class="button-primary" type="submit" value="<?php _e('Delete') ?>" /></p>
</form>
<?php } ?>
<?php include "foot.tpl.php"; ?>
