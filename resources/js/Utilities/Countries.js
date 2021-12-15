const countries = [
    {
        'name': 'Afghanistan',
        'code': 'AF',
        'tla': 'AFG',
    },
    {
        'name': 'Aland Islands',
        'code': 'AX',
        'tla': 'ALA',
    },
    {
        'name': 'Albania',
        'code': 'AL',
        'tla': 'ALB',
    },
    {
        'name': 'Algeria',
        'code': 'DZ',
        'tla': 'DZA',
    },
    {
        'name': 'American Samoa',
        'code': 'AS',
        'tla': 'ASM',
    },
    {
        'name': 'Andorra',
        'code': 'AD',
        'tla': 'AND',
    },
    {
        'name': 'Angola',
        'code': 'AO',
        'tla': 'AGO',
    },
    {
        'name': 'Anguilla',
        'code': 'AI',
        'tla': 'AIA',
    },
    {
        'name': 'Antarctica',
        'code': 'AQ',
        'tla': 'ATA',
    },
    {
        'name': 'Antigua and Barbuda',
        'code': 'AG',
        'tla': 'ATG',
    },
    {
        'name': 'Argentina',
        'code': 'AR',
        'tla': 'ARG',
    },
    {
        'name': 'Armenia',
        'code': 'AM',
        'tla': 'ARM',
    },
    {
        'name': 'Aruba',
        'code': 'AW',
        'tla': 'ABW',
    },
    {
        'name': 'Australia',
        'code': 'AU',
        'tla': 'AUS',
    },
    {
        'name': 'Austria',
        'code': 'AT',
        'tla': 'AUT',
    },
    {
        'name': 'Azerbaijan',
        'code': 'AZ',
        'tla': 'AZE',
    },
    {
        'name': 'Bahamas',
        'code': 'BS',
        'tla': 'BHS',
    },
    {
        'name': 'Bahrain',
        'code': 'BH',
        'tla': 'BHR',
    },
    {
        'name': 'Bangladesh',
        'code': 'BD',
        'tla': 'BGD',
    },
    {
        'name': 'Barbados',
        'code': 'BB',
        'tla': 'BRB',
    },
    {
        'name': 'Belarus',
        'code': 'BY',
        'tla': 'BLR',
    },
    {
        'name': 'Belgium',
        'code': 'BE',
        'tla': 'BEL',
    },
    {
        'name': 'Belize',
        'code': 'BZ',
        'tla': 'BLZ',
    },
    {
        'name': 'Benin',
        'code': 'BJ',
        'tla': 'BEN',
    },
    {
        'name': 'Bermuda',
        'code': 'BM',
        'tla': 'BMU',
    },
    {
        'name': 'Bhutan',
        'code': 'BT',
        'tla': 'BTN',
    },
    {
        'name': 'Bolivia',
        'code': 'BO',
        'tla': 'BOL',
    },
    {
        'name': 'Bonaire, Saint Eustatius and Saba',
        'code': 'BQ',
        'tla': 'BES',
    },
    {
        'name': 'Bosnia and Herzegovina',
        'code': 'BA',
        'tla': 'BIH',
    },
    {
        'name': 'Botswana',
        'code': 'BW',
        'tla': 'BWA',
    },
    {
        'name': 'Bouvet Island',
        'code': 'BV',
        'tla': 'BVT',
    },
    {
        'name': 'Brazil',
        'code': 'BR',
        'tla': 'BRA',
    },
    {
        'name': 'British Indian Ocean Territory',
        'code': 'IO',
        'tla': 'IOT',
    },
    {
        'name': 'British Virgin Islands',
        'code': 'VG',
        'tla': 'VGB',
    },
    {
        'name': 'Brunei',
        'code': 'BN',
        'tla': 'BRN',
    },
    {
        'name': 'Bulgaria',
        'code': 'BG',
        'tla': 'BGR',
    },
    {
        'name': 'Burkina Faso',
        'code': 'BF',
        'tla': 'BFA',
    },
    {
        'name': 'Burundi',
        'code': 'BI',
        'tla': 'BDI',
    },
    {
        'name': 'Cambodia',
        'code': 'KH',
        'tla': 'KHM',
    },
    {
        'name': 'Cameroon',
        'code': 'CM',
        'tla': 'CMR',
    },
    {
        'name': 'Canada',
        'code': 'CA',
        'tla': 'CAN',
    },
    {
        'name': 'Cape Verde',
        'code': 'CV',
        'tla': 'CPV',
    },
    {
        'name': 'Cayman Islands',
        'code': 'KY',
        'tla': 'CYM',
    },
    {
        'name': 'Central African Republic',
        'code': 'CF',
        'tla': 'CAF',
    },
    {
        'name': 'Chad',
        'code': 'TD',
        'tla': 'TCD',
    },
    {
        'name': 'Chile',
        'code': 'CL',
        'tla': 'CHL',
    },
    {
        'name': 'China',
        'code': 'CN',
        'tla': 'CHN',
    },
    {
        'name': 'Christmas Island',
        'code': 'CX',
        'tla': 'CXR',
    },
    {
        'name': 'Cocos Islands',
        'code': 'CC',
        'tla': 'CCK',
    },
    {
        'name': 'Colombia',
        'code': 'CO',
        'tla': 'COL',
    },
    {
        'name': 'Comoros',
        'code': 'KM',
        'tla': 'COM',
    },
    {
        'name': 'Cook Islands',
        'code': 'CK',
        'tla': 'COK',
    },
    {
        'name': 'Costa Rica',
        'code': 'CR',
        'tla': 'CRI',
    },
    {
        'name': 'Croatia',
        'code': 'HR',
        'tla': 'HRV',
    },
    {
        'name': 'Cuba',
        'code': 'CU',
        'tla': 'CUB',
    },
    {
        'name': 'Curacao',
        'code': 'CW',
        'tla': 'CUW',
    },
    {
        'name': 'Cyprus',
        'code': 'CY',
        'tla': 'CYP',
    },
    {
        'name': 'Czech Republic',
        'code': 'CZ',
        'tla': 'CZE',
    },
    {
        'name': 'Democratic Republic of the Congo',
        'code': 'CD',
        'tla': 'COD',
    },
    {
        'name': 'Denmark',
        'code': 'DK',
        'tla': 'DNK',
    },
    {
        'name': 'Djibouti',
        'code': 'DJ',
        'tla': 'DJI',
    },
    {
        'name': 'Dominica',
        'code': 'DM',
        'tla': 'DMA',
    },
    {
        'name': 'Dominican Republic',
        'code': 'DO',
        'tla': 'DOM',
    },
    {
        'name': 'East Timor',
        'code': 'TL',
        'tla': 'TLS',
    },
    {
        'name': 'Ecuador',
        'code': 'EC',
        'tla': 'ECU',
    },
    {
        'name': 'Egypt',
        'code': 'EG',
        'tla': 'EGY',
    },
    {
        'name': 'El Salvador',
        'code': 'SV',
        'tla': 'SLV',
    },
    {
        'name': 'Equatorial Guinea',
        'code': 'GQ',
        'tla': 'GNQ',
    },
    {
        'name': 'Eritrea',
        'code': 'ER',
        'tla': 'ERI',
    },
    {
        'name': 'Estonia',
        'code': 'EE',
        'tla': 'EST',
    },
    {
        'name': 'Ethiopia',
        'code': 'ET',
        'tla': 'ETH',
    },
    {
        'name': 'Falkland Islands',
        'code': 'FK',
        'tla': 'FLK',
    },
    {
        'name': 'Faroe Islands',
        'code': 'FO',
        'tla': 'FRO',
    },
    {
        'name': 'Fiji',
        'code': 'FJ',
        'tla': 'FJI',
    },
    {
        'name': 'Finland',
        'code': 'FI',
        'tla': 'FIN',
    },
    {
        'name': 'France',
        'code': 'FR',
        'tla': 'FRA',
    },
    {
        'name': 'French Guiana',
        'code': 'GF',
        'tla': 'GUF',
    },
    {
        'name': 'French Polynesia',
        'code': 'PF',
        'tla': 'PYF',
    },
    {
        'name': 'French Southern Territories',
        'code': 'TF',
        'tla': 'ATF',
    },
    {
        'name': 'Gabon',
        'code': 'GA',
        'tla': 'GAB',
    },
    {
        'name': 'Gambia',
        'code': 'GM',
        'tla': 'GMB',
    },
    {
        'name': 'Georgia',
        'code': 'GE',
        'tla': 'GEO',
    },
    {
        'name': 'Germany',
        'code': 'DE',
        'tla': 'DEU',
    },
    {
        'name': 'Ghana',
        'code': 'GH',
        'tla': 'GHA',
    },
    {
        'name': 'Gibraltar',
        'code': 'GI',
        'tla': 'GIB',
    },
    {
        'name': 'Greece',
        'code': 'GR',
        'tla': 'GRC',
    },
    {
        'name': 'Greenland',
        'code': 'GL',
        'tla': 'GRL',
    },
    {
        'name': 'Grenada',
        'code': 'GD',
        'tla': 'GRD',
    },
    {
        'name': 'Guadeloupe',
        'code': 'GP',
        'tla': 'GLP',
    },
    {
        'name': 'Guam',
        'code': 'GU',
        'tla': 'GUM',
    },
    {
        'name': 'Guatemala',
        'code': 'GT',
        'tla': 'GTM',
    },
    {
        'name': 'Guernsey',
        'code': 'GG',
        'tla': 'GGY',
    },
    {
        'name': 'Guinea',
        'code': 'GN',
        'tla': 'GIN',
    },
    {
        'name': 'Guinea-Bissau',
        'code': 'GW',
        'tla': 'GNB',
    },
    {
        'name': 'Guyana',
        'code': 'GY',
        'tla': 'GUY',
    },
    {
        'name': 'Haiti',
        'code': 'HT',
        'tla': 'HTI',
    },
    {
        'name': 'Heard Island and McDonald Islands',
        'code': 'HM',
        'tla': 'HMD',
    },
    {
        'name': 'Honduras',
        'code': 'HN',
        'tla': 'HND',
    },
    {
        'name': 'Hong Kong',
        'code': 'HK',
        'tla': 'HKG',
    },
    {
        'name': 'Hungary',
        'code': 'HU',
        'tla': 'HUN',
    },
    {
        'name': 'Iceland',
        'code': 'IS',
        'tla': 'ISL',
    },
    {
        'name': 'India',
        'code': 'IN',
        'tla': 'IND',
    },
    {
        'name': 'Indonesia',
        'code': 'ID',
        'tla': 'IDN',
    },
    {
        'name': 'Iran',
        'code': 'IR',
        'tla': 'IRN',
    },
    {
        'name': 'Iraq',
        'code': 'IQ',
        'tla': 'IRQ',
    },
    {
        'name': 'Ireland',
        'code': 'IE',
        'tla': 'IRL',
    },
    {
        'name': 'Isle of Man',
        'code': 'IM',
        'tla': 'IMN',
    },
    {
        'name': 'Israel',
        'code': 'IL',
        'tla': 'ISR',
    },
    {
        'name': 'Italy',
        'code': 'IT',
        'tla': 'ITA',
    },
    {
        'name': 'Ivory Coast',
        'code': 'CI',
        'tla': 'CIV',
    },
    {
        'name': 'Jamaica',
        'code': 'JM',
        'tla': 'JAM',
    },
    {
        'name': 'Japan',
        'code': 'JP',
        'tla': 'JPN',
    },
    {
        'name': 'Jersey',
        'code': 'JE',
        'tla': 'JEY',
    },
    {
        'name': 'Jordan',
        'code': 'JO',
        'tla': 'JOR',
    },
    {
        'name': 'Kazakhstan',
        'code': 'KZ',
        'tla': 'KAZ',
    },
    {
        'name': 'Kenya',
        'code': 'KE',
        'tla': 'KEN',
    },
    {
        'name': 'Kiribati',
        'code': 'KI',
        'tla': 'KIR',
    },
    {
        'name': 'Kosovo',
        'code': 'XK',
        'tla': 'XKX',
    },
    {
        'name': 'Kuwait',
        'code': 'KW',
        'tla': 'KWT',
    },
    {
        'name': 'Kyrgyzstan',
        'code': 'KG',
        'tla': 'KGZ',
    },
    {
        'name': 'Laos',
        'code': 'LA',
        'tla': 'LAO',
    },
    {
        'name': 'Latvia',
        'code': 'LV',
        'tla': 'LVA',
    },
    {
        'name': 'Lebanon',
        'code': 'LB',
        'tla': 'LBN',
    },
    {
        'name': 'Lesotho',
        'code': 'LS',
        'tla': 'LSO',
    },
    {
        'name': 'Liberia',
        'code': 'LR',
        'tla': 'LBR',
    },
    {
        'name': 'Libya',
        'code': 'LY',
        'tla': 'LBY',
    },
    {
        'name': 'Liechtenstein',
        'code': 'LI',
        'tla': 'LIE',
    },
    {
        'name': 'Lithuania',
        'code': 'LT',
        'tla': 'LTU',
    },
    {
        'name': 'Luxembourg',
        'code': 'LU',
        'tla': 'LUX',
    },
    {
        'name': 'Macao',
        'code': 'MO',
        'tla': 'MAC',
    },
    {
        'name': 'Macedonia',
        'code': 'MK',
        'tla': 'MKD',
    },
    {
        'name': 'Madagascar',
        'code': 'MG',
        'tla': 'MDG',
    },
    {
        'name': 'Malawi',
        'code': 'MW',
        'tla': 'MWI',
    },
    {
        'name': 'Malaysia',
        'code': 'MY',
        'tla': 'MYS',
    },
    {
        'name': 'Maldives',
        'code': 'MV',
        'tla': 'MDV',
    },
    {
        'name': 'Mali',
        'code': 'ML',
        'tla': 'MLI',
    },
    {
        'name': 'Malta',
        'code': 'MT',
        'tla': 'MLT',
    },
    {
        'name': 'Marshall Islands',
        'code': 'MH',
        'tla': 'MHL',
    },
    {
        'name': 'Martinique',
        'code': 'MQ',
        'tla': 'MTQ',
    },
    {
        'name': 'Mauritania',
        'code': 'MR',
        'tla': 'MRT',
    },
    {
        'name': 'Mauritius',
        'code': 'MU',
        'tla': 'MUS',
    },
    {
        'name': 'Mayotte',
        'code': 'YT',
        'tla': 'MYT',
    },
    {
        'name': 'Mexico',
        'code': 'MX',
        'tla': 'MEX',
    },
    {
        'name': 'Micronesia',
        'code': 'FM',
        'tla': 'FSM',
    },
    {
        'name': 'Moldova',
        'code': 'MD',
        'tla': 'MDA',
    },
    {
        'name': 'Monaco',
        'code': 'MC',
        'tla': 'MCO',
    },
    {
        'name': 'Mongolia',
        'code': 'MN',
        'tla': 'MNG',
    },
    {
        'name': 'Montenegro',
        'code': 'ME',
        'tla': 'MNE',
    },
    {
        'name': 'Montserrat',
        'code': 'MS',
        'tla': 'MSR',
    },
    {
        'name': 'Morocco',
        'code': 'MA',
        'tla': 'MAR',
    },
    {
        'name': 'Mozambique',
        'code': 'MZ',
        'tla': 'MOZ',
    },
    {
        'name': 'Myanmar',
        'code': 'MM',
        'tla': 'MMR',
    },
    {
        'name': 'Namibia',
        'code': 'NA',
        'tla': 'NAM',
    },
    {
        'name': 'Nauru',
        'code': 'NR',
        'tla': 'NRU',
    },
    {
        'name': 'Nepal',
        'code': 'NP',
        'tla': 'NPL',
    },
    {
        'name': 'Netherlands',
        'code': 'NL',
        'tla': 'NLD',
    },
    {
        'name': 'Netherlands Antilles',
        'code': 'AN',
        'tla': 'ANT',
    },
    {
        'name': 'New Caledonia',
        'code': 'NC',
        'tla': 'NCL',
    },
    {
        'name': 'New Zealand',
        'code': 'NZ',
        'tla': 'NZL',
    },
    {
        'name': 'Nicaragua',
        'code': 'NI',
        'tla': 'NIC',
    },
    {
        'name': 'Niger',
        'code': 'NE',
        'tla': 'NER',
    },
    {
        'name': 'Nigeria',
        'code': 'NG',
        'tla': 'NGA',
    },
    {
        'name': 'Niue',
        'code': 'NU',
        'tla': 'NIU',
    },
    {
        'name': 'Norfolk Island',
        'code': 'NF',
        'tla': 'NFK',
    },
    {
        'name': 'North Korea',
        'code': 'KP',
        'tla': 'PRK',
    },
    {
        'name': 'Northern Mariana Islands',
        'code': 'MP',
        'tla': 'MNP',
    },
    {
        'name': 'Norway',
        'code': 'NO',
        'tla': 'NOR',
    },
    {
        'name': 'Oman',
        'code': 'OM',
        'tla': 'OMN',
    },
    {
        'name': 'Pakistan',
        'code': 'PK',
        'tla': 'PAK',
    },
    {
        'name': 'Palau',
        'code': 'PW',
        'tla': 'PLW',
    },
    {
        'name': 'Palestinian Territory',
        'code': 'PS',
        'tla': 'PSE',
    },
    {
        'name': 'Panama',
        'code': 'PA',
        'tla': 'PAN',
    },
    {
        'name': 'Papua New Guinea',
        'code': 'PG',
        'tla': 'PNG',
    },
    {
        'name': 'Paraguay',
        'code': 'PY',
        'tla': 'PRY',
    },
    {
        'name': 'Peru',
        'code': 'PE',
        'tla': 'PER',
    },
    {
        'name': 'Philippines',
        'code': 'PH',
        'tla': 'PHL',
    },
    {
        'name': 'Pitcairn',
        'code': 'PN',
        'tla': 'PCN',
    },
    {
        'name': 'Poland',
        'code': 'PL',
        'tla': 'POL',
    },
    {
        'name': 'Portugal',
        'code': 'PT',
        'tla': 'PRT',
    },
    {
        'name': 'Puerto Rico',
        'code': 'PR',
        'tla': 'PRI',
    },
    {
        'name': 'Qatar',
        'code': 'QA',
        'tla': 'QAT',
    },
    {
        'name': 'Republic of the Congo',
        'code': 'CG',
        'tla': 'COG',
    },
    {
        'name': 'Reunion',
        'code': 'RE',
        'tla': 'REU',
    },
    {
        'name': 'Romania',
        'code': 'RO',
        'tla': 'ROU',
    },
    {
        'name': 'Russia',
        'code': 'RU',
        'tla': 'RUS',
    },
    {
        'name': 'Rwanda',
        'code': 'RW',
        'tla': 'RWA',
    },
    {
        'name': 'Saint Barthelemy',
        'code': 'BL',
        'tla': 'BLM',
    },
    {
        'name': 'Saint Helena',
        'code': 'SH',
        'tla': 'SHN',
    },
    {
        'name': 'Saint Kitts and Nevis',
        'code': 'KN',
        'tla': 'KNA',
    },
    {
        'name': 'Saint Lucia',
        'code': 'LC',
        'tla': 'LCA',
    },
    {
        'name': 'Saint Martin',
        'code': 'MF',
        'tla': 'MAF',
    },
    {
        'name': 'Saint Pierre and Miquelon',
        'code': 'PM',
        'tla': 'SPM',
    },
    {
        'name': 'Saint Vincent and the Grenadines',
        'code': 'VC',
        'tla': 'VCT',
    },
    {
        'name': 'Samoa',
        'code': 'WS',
        'tla': 'WSM',
    },
    {
        'name': 'San Marino',
        'code': 'SM',
        'tla': 'SMR',
    },
    {
        'name': 'Sao Tome and Principe',
        'code': 'ST',
        'tla': 'STP',
    },
    {
        'name': 'Saudi Arabia',
        'code': 'SA',
        'tla': 'SAU',
    },
    {
        'name': 'Senegal',
        'code': 'SN',
        'tla': 'SEN',
    },
    {
        'name': 'Serbia',
        'code': 'RS',
        'tla': 'SRB',
    },
    {
        'name': 'Serbia and Montenegro',
        'code': 'CS',
        'tla': 'SCG',
    },
    {
        'name': 'Seychelles',
        'code': 'SC',
        'tla': 'SYC',
    },
    {
        'name': 'Sierra Leone',
        'code': 'SL',
        'tla': 'SLE',
    },
    {
        'name': 'Singapore',
        'code': 'SG',
        'tla': 'SGP',
    },
    {
        'name': 'Sint Maarten',
        'code': 'SX',
        'tla': 'SXM',
    },
    {
        'name': 'Slovakia',
        'code': 'SK',
        'tla': 'SVK',
    },
    {
        'name': 'Slovenia',
        'code': 'SI',
        'tla': 'SVN',
    },
    {
        'name': 'Solomon Islands',
        'code': 'SB',
        'tla': 'SLB',
    },
    {
        'name': 'Somalia',
        'code': 'SO',
        'tla': 'SOM',
    },
    {
        'name': 'South Africa',
        'code': 'ZA',
        'tla': 'ZAF',
    },
    {
        'name': 'South Georgia and the South Sandwich Islands',
        'code': 'GS',
        'tla': 'SGS',
    },
    {
        'name': 'South Korea',
        'code': 'KR',
        'tla': 'KOR',
    },
    {
        'name': 'South Sudan',
        'code': 'SS',
        'tla': 'SSD',
    },
    {
        'name': 'Spain',
        'code': 'ES',
        'tla': 'ESP',
    },
    {
        'name': 'Sri Lanka',
        'code': 'LK',
        'tla': 'LKA',
    },
    {
        'name': 'Sudan',
        'code': 'SD',
        'tla': 'SDN',
    },
    {
        'name': 'Suriname',
        'code': 'SR',
        'tla': 'SUR',
    },
    {
        'name': 'Svalbard and Jan Mayen',
        'code': 'SJ',
        'tla': 'SJM',
    },
    {
        'name': 'Swaziland',
        'code': 'SZ',
        'tla': 'SWZ',
    },
    {
        'name': 'Sweden',
        'code': 'SE',
        'tla': 'SWE',
    },
    {
        'name': 'Switzerland',
        'code': 'CH',
        'tla': 'CHE',
    },
    {
        'name': 'Syria',
        'code': 'SY',
        'tla': 'SYR',
    },
    {
        'name': 'Taiwan',
        'code': 'TW',
        'tla': 'TWN',
    },
    {
        'name': 'Tajikistan',
        'code': 'TJ',
        'tla': 'TJK',
    },
    {
        'name': 'Tanzania',
        'code': 'TZ',
        'tla': 'TZA',
    },
    {
        'name': 'Thailand',
        'code': 'TH',
        'tla': 'THA',
    },
    {
        'name': 'Togo',
        'code': 'TG',
        'tla': 'TGO',
    },
    {
        'name': 'Tokelau',
        'code': 'TK',
        'tla': 'TKL',
    },
    {
        'name': 'Tonga',
        'code': 'TO',
        'tla': 'TON',
    },
    {
        'name': 'Trinidad and Tobago',
        'code': 'TT',
        'tla': 'TTO',
    },
    {
        'name': 'Tunisia',
        'code': 'TN',
        'tla': 'TUN',
    },
    {
        'name': 'Turkey',
        'code': 'TR',
        'tla': 'TUR',
    },
    {
        'name': 'Turkmenistan',
        'code': 'TM',
        'tla': 'TKM',
    },
    {
        'name': 'Turks and Caicos Islands',
        'code': 'TC',
        'tla': 'TCA',
    },
    {
        'name': 'Tuvalu',
        'code': 'TV',
        'tla': 'TUV',
    },
    {
        'name': 'U.S. Virgin Islands',
        'code': 'VI',
        'tla': 'VIR',
    },
    {
        'name': 'Uganda',
        'code': 'UG',
        'tla': 'UGA',
    },
    {
        'name': 'Ukraine',
        'code': 'UA',
        'tla': 'UKR',
    },
    {
        'name': 'United Arab Emirates',
        'code': 'AE',
        'tla': 'ARE',
    },
    {
        'name': 'United Kingdom',
        'code': 'GB',
        'tla': 'GBR',
    },
    {
        'name': 'United States',
        'code': 'US',
        'tla': 'USA',
    },
    {
        'name': 'United States Minor Outlying Islands',
        'code': 'UM',
        'tla': 'UMI',
    },
    {
        'name': 'Uruguay',
        'code': 'UY',
        'tla': 'URY',
    },
    {
        'name': 'Uzbekistan',
        'code': 'UZ',
        'tla': 'UZB',
    },
    {
        'name': 'Vanuatu',
        'code': 'VU',
        'tla': 'VUT',
    },
    {
        'name': 'Vatican',
        'code': 'VA',
        'tla': 'VAT',
    },
    {
        'name': 'Venezuela',
        'code': 'VE',
        'tla': 'VEN',
    },
    {
        'name': 'Vietnam',
        'code': 'VN',
        'tla': 'VNM',
    },
    {
        'name': 'Wallis and Futuna',
        'code': 'WF',
        'tla': 'WLF',
    },
    {
        'name': 'Western Sahara',
        'code': 'EH',
        'tla': 'ESH',
    },
    {
        'name': 'Yemen',
        'code': 'YE',
        'tla': 'YEM',
    },
    {
        'name': 'Zambia',
        'code': 'ZM',
        'tla': 'ZMB',
    },
    {
        'name': 'Zimbabwe',
        'code': 'ZW',
        'tla': 'ZWE',
    },
];

export default countries;
