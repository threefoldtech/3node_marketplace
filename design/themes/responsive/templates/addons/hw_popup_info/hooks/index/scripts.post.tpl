{assign var="countries" value=""|fn_get_simple_countries}

<script>
$(function(){$ldelim}
	var countryListAlpha2 = {
	  "AF": "Afghanistan",
	  "AX": "Aland Islands",
	  "AL": "Albania",
	  "DZ": "Algeria",
	  "AS": "American Samoa",
	  "AD": "Andorra",
	  "AO": "Angola",
	  "AI": "Anguilla",
	  "AQ": "Antarctica",
	  "AG": "Antigua and Barbuda",
	  "AR": "Argentina",
	  "AM": "Armenia",
	  "AW": "Aruba",
	  "AP": "Asia-Pacific",
	  "AU": "Australia",
	  "AT": "Austria",
	  "AZ": "Azerbaijan",
	  "BS": "Bahamas",
	  "BH": "Bahrain",
	  "BD": "Bangladesh",
	  "BB": "Barbados",
	  "BY": "Belarus",
	  "BE": "Belgium",
	  "BZ": "Belize",
	  "BJ": "Benin",
	  "BM": "Bermuda",
	  "BT": "Bhutan",
	  "BO": "Bolivia",
	  "BA": "Bosnia and Herzegovina",
	  "BW": "Botswana",
	  "BV": "Bouvet Island",
	  "BR": "Brazil",
	  "IO": "British Indian Ocean Territory",
	  "VG": "British Virgin Islands",
	  "BN": "Brunei Darussalam",
	  "BG": "Bulgaria",
	  "BF": "Burkina Faso",
	  "BI": "Burundi",
	  "KH": "Cambodia",
	  "CM": "Cameroon",
	  "CA": "Canada",
	  "CV": "Cape Verde",
	  "KY": "Cayman Islands",
	  "CF": "Central African Republic",
	  "TD": "Chad",
	  "CL": "Chile",
	  "CN": "China",
	  "CX": "Christmas Island",
	  "CC": "Cocos (Keeling) Islands",
	  "CO": "Colombia",
	  "KM": "Comoros",
	  "CG": "Congo",
	  "CK": "Cook Islands",
	  "CR": "Costa Rica",
	  "CI": "Cote D'ivoire",
	  "HR": "Croatia",
	  "CU": "Cuba",
	  "CW": "Curaçao",
	  "CY": "Cyprus",
	  "CZ": "Czech Republic",
	  "DK": "Denmark",
	  "DJ": "Djibouti",
	  "DM": "Dominica",
	  "DO": "Dominican Republic",
	  "TL": "East Timor",
	  "EC": "Ecuador",
	  "EG": "Egypt",
	  "SV": "El Salvador",
	  "GQ": "Equatorial Guinea",
	  "ER": "Eritrea",
	  "EE": "Estonia",
	  "ET": "Ethiopia",
	  "EU": "Europe",
	  "FK": "Falkland Islands (Malvinas)",
	  "FO": "Faroe Islands",
	  "FJ": "Fiji",
	  "FI": "Finland",
	  "FR": "France",
	  "FX": "France, Metropolitan",
	  "GF": "French Guiana",
	  "PF": "French Polynesia",
	  "TF": "French Southern Territories",
	  "GA": "Gabon",
	  "GM": "Gambia",
	  "GE": "Georgia",
	  "DE": "Germany",
	  "GH": "Ghana",
	  "GI": "Gibraltar",
	  "GR": "Greece",
	  "GL": "Greenland",
	  "GD": "Grenada",
	  "GP": "Guadeloupe",
	  "GU": "Guam",
	  "GT": "Guatemala",
	  "GG": "Guernsey",
	  "GN": "Guinea",
	  "GW": "Guinea-Bissau",
	  "GY": "Guyana",
	  "HT": "Haiti",
	  "HM": "Heard and McDonald Islands",
	  "HN": "Honduras",
	  "HK": "Hong Kong",
	  "HU": "Hungary",
	  "IS": "Iceland",
	  "IN": "India",
	  "ID": "Indonesia",
	  "IQ": "Iraq",
	  "IE": "Ireland",
	  "IR": "Islamic Republic of Iran",
	  "IM": "Isle of Man",
	  "IL": "Israel",
	  "IT": "Italy",
	  "JM": "Jamaica",
	  "JP": "Japan",
	  "JE": "Jersey",
	  "JO": "Jordan",
	  "KZ": "Kazakhstan",
	  "KE": "Kenya",
	  "KI": "Kiribati",
	  "KP": "Korea",
	  "KR": "Korea, Republic of",
	  "KW": "Kuwait",
	  "KG": "Kyrgyzstan",
	  "LA": "Laos",
	  "LV": "Latvia",
	  "LB": "Lebanon",
	  "LS": "Lesotho",
	  "LR": "Liberia",
	  "LY": "Libyan Arab Jamahiriya",
	  "LI": "Liechtenstein",
	  "LT": "Lithuania",
	  "LU": "Luxembourg",
	  "MO": "Macau",
	  "MK": "Macedonia",
	  "MG": "Madagascar",
	  "MW": "Malawi",
	  "MY": "Malaysia",
	  "MV": "Maldives",
	  "ML": "Mali",
	  "MT": "Malta",
	  "MH": "Marshall Islands",
	  "MQ": "Martinique",
	  "MR": "Mauritania",
	  "MU": "Mauritius",
	  "YT": "Mayotte",
	  "MX": "Mexico",
	  "FM": "Micronesia",
	  "MD": "Moldova, Republic of",
	  "MC": "Monaco",
	  "MN": "Mongolia",
	  "ME": "Montenegro",
	  "MS": "Montserrat",
	  "MA": "Morocco",
	  "MZ": "Mozambique",
	  "MM": "Myanmar",
	  "NA": "Namibia",
	  "NR": "Nauru",
	  "NP": "Nepal",
	  "NL": "Netherlands",
	  "NC": "New Caledonia",
	  "NZ": "New Zealand",
	  "NI": "Nicaragua",
	  "NE": "Niger",
	  "NG": "Nigeria",
	  "NU": "Niue",
	  "NF": "Norfolk Island",
	  "MP": "Northern Mariana Islands",
	  "NO": "Norway",
	  "OM": "Oman",
	  "PK": "Pakistan",
	  "PW": "Palau",
	  "PS": "Palestine Authority",
	  "PA": "Panama",
	  "PG": "Papua New Guinea",
	  "PY": "Paraguay",
	  "PE": "Peru",
	  "PH": "Philippines",
	  "PN": "Pitcairn",
	  "PL": "Poland",
	  "PT": "Portugal",
	  "PR": "Puerto Rico",
	  "QA": "Qatar",
	  "RS": "Republic of Serbia",
	  "RE": "Reunion",
	  "RO": "Romania",
	  "RU": "Russian Federation",
	  "RW": "Rwanda",
	  "LC": "Saint Lucia",
	  "WS": "Samoa",
	  "SM": "San Marino",
	  "ST": "Sao Tome and Principe",
	  "SA": "Saudi Arabia",
	  "SN": "Senegal",
	  "CS": "Serbia",
	  "SC": "Seychelles",
	  "SL": "Sierra Leone",
	  "SG": "Singapore",
	  "SX": "Sint Maarten",
	  "SK": "Slovakia",
	  "SI": "Slovenia",
	  "SB": "Solomon Islands",
	  "SO": "Somalia",
	  "ZA": "South Africa",
	  "ES": "Spain",
	  "LK": "Sri Lanka",
	  "SH": "St. Helena",
	  "KN": "St. Kitts and Nevis",
	  "PM": "St. Pierre and Miquelon",
	  "VC": "St. Vincent and the Grenadines",
	  "SD": "Sudan",
	  "SR": "Suriname",
	  "SJ": "Svalbard and Jan Mayen Islands",
	  "SZ": "Swaziland",
	  "SE": "Sweden",
	  "CH": "Switzerland",
	  "SY": "Syrian Arab Republic",
	  "TW": "Taiwan",
	  "TJ": "Tajikistan",
	  "TZ": "Tanzania, United Republic of",
	  "TH": "Thailand",
	  "TG": "Togo",
	  "TK": "Tokelau",
	  "TO": "Tonga",
	  "TT": "Trinidad and Tobago",
	  "TN": "Tunisia",
	  "TR": "Turkey",
	  "TM": "Turkmenistan",
	  "TC": "Turks and Caicos Islands",
	  "TV": "Tuvalu",
	  "UG": "Uganda",
	  "UA": "Ukraine",
	  "AE": "United Arab Emirates",
	  "GB": "United Kingdom (Great Britain)",
	  "US": "United States",
	  "VI": "United States Virgin Islands",
	  "UY": "Uruguay",
	  "UZ": "Uzbekistan",
	  "VU": "Vanuatu",
	  "VA": "Vatican City State",
	  "VE": "Venezuela",
	  "VN": "Viet Nam",
	  "WF": "Wallis And Futuna Islands",
	  "EH": "Western Sahara",
	  "YE": "Yemen",
	  "ZR": "Zaire",
	  "ZM": "Zambia",
	  "ZW": "Zimbabwe"
	};
	$('.flags li').click(function(e){$ldelim}
		let __class = ($(this).text()).trim();
		if(__class != ''){$ldelim}
			let c_code = Object.keys(countryListAlpha2).find(key => (countryListAlpha2[key]).toLowerCase() === __class.toLowerCase());
			$.cookie.set('hw_popup_info', c_code, '', '/');
			insertParam('location-change',c_code)
		{$rdelim}
		
	{$rdelim});

	var loc = $.cookie.get('hw_popup_info');
	if(loc != ''){
		$('#c_location').text(countryListAlpha2[loc]);
		$('#c_location').css('visibility', 'visible');
	}
	

{$rdelim});
$(window).on('load',function(){$ldelim}
	$('#c_location').click(function(){$ldelim}
		let _width = {$addons.hw_popup_info.width|default:'auto'}
		if ($(window).width() < 960) {$ldelim}
			_width = 380;
		{$rdelim}else if ($(window).width() < 1367) {$ldelim}
			_width = 680;
		{$rdelim}
	    $( "#info_popup_modal" ).dialog({$ldelim}
			width: _width,
			modal: true, 
			title: "Select your country"
		{$rdelim});
	{$rdelim}, {$addons.hw_popup_info.delay|default:0});
{$rdelim});
function insertParam(key, value) {$ldelim}
        key = encodeURIComponent(key);
        value = encodeURIComponent(value);
    
        // kvp looks like ['key1=value1', 'key2=value2', ...]
        var kvp = document.location.search.substr(1).split('&');
        let i=0;
    
        for(; i<kvp.length; i++){$ldelim}
            if (kvp[i].startsWith(key + '=')) {$ldelim}
                let pair = kvp[i].split('=');
                pair[1] = value;
                kvp[i] = pair.join('=');
                break;
            {$rdelim}
        {$rdelim}
    
        if(i >= kvp.length){$ldelim}
            kvp[kvp.length] = [key,value].join('=');
        {$rdelim}
    
        // can return this or...
        let params = kvp.join('&');
    
        // reload page with new params
        document.location.search = params;
    {$rdelim}
</script>
