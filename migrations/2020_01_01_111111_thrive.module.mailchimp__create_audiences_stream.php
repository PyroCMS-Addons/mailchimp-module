<?php

use Anomaly\Streams\Platform\Database\Migration\Migration;

class ThriveModuleMailchimpCreateAudiencesStream extends Migration
{

    /**
     * This migration creates the stream.
     * It should be deleted on rollback.
     *
     * @var bool
     */
    protected $delete = true;

    /**
     * The stream definition.
     *
     * @var array
     */
    protected $stream = [
        'slug' => 'audiences',
        'title_column' => 'name',
        'translatable'  => false,
        'versionable'   => false,
        'trashable'     => true,
        'searchable'    => true,
        'sortable'      => true,
    ];

    /**
     * The addon fields.
     *
     * @var array
     */
    protected $fields = [
        "name" => [
            "type"   => "anomaly.field_type.text",
        ],
        'str_id' => [
            "type"   => "anomaly.field_type.text",
            "config" => [
                "default_value" => null,
            ]
        ],
        'permission_reminder' => [
            "type"   => "anomaly.field_type.textarea",
        ],
        'email_type_option' => [
            "type"   => "anomaly.field_type.boolean",
        ],
        'contact_company_name' => [
            "type"   => "anomaly.field_type.text",
        ],
        'contact_address1' => [
            "type"   => "anomaly.field_type.text",
        ],
        'contact_city' => [
            "type"   => "anomaly.field_type.text",
        ],
        'contact_state' => [
            "type"   => "anomaly.field_type.text",
        ],
        'contact_zip' => [
            "type"   => "anomaly.field_type.text",
        ],
        'contact_country' => [
            "type"   => "anomaly.field_type.select",
            "config" => [
                "options"       =>
                [
                    "AU"    => "Australia",
                    "US"    => "United States of Americs",
                    "CA"    => "Canada",
                    "NZ"    => "New Zealand",
                    "AT"    => "Austria",
                    "CN"    => "China",
                    "GE"    => "Germany",
                    "GL"    => "Greenland",
                    "IS"    => "Iceland",
                    "IE"    => "Ireland",
                    "MT"    => "Malta",
                    "MX"    => "Mexico",
                    "ES"    => "Spain",
                    "GB"    => "United Kingdom",
                    "FR"    => "France",
                    "DK"    => "Denmark",
                    "BR"    => "Brazil",
                    "BE"    => "Belgium",
                ],
                "separator"     => ":",
                "default_value" => 'AUS',
                "button_type"   => "info",
                "handler"       => "options",
                "mode"          => "dropdown",
            ]
        ],
        'campaign_from_name' => [
            "type"   => "anomaly.field_type.text",
        ],
        'campaign_from_email' => [
            "type"   => "anomaly.field_type.email",
        ],
        'campaign_subject' => [
            "type"   => "anomaly.field_type.text",
        ],
        'campaign_language' => [
            "type"   => "anomaly.field_type.select",
            "config" => [
                "options"       =>
                [
                    "module::message.common" => [
                        "EN_US"     => "English US",
                        "EN_AU"     => "English Australia",
                        "EN_CA"     => "English (Canada)",
                        "DE"        => "German (Standard)",
                        "IT"        => "Italian (Standard)",
                    ],
                    "module::message.all" => [
                        "AF"        => "Afrikaans",
                        "SQ"        => "Albanian",
                        "AR_DZ"     => "Arabic",
                        "AR_BH"     => "Arabic",
                        "AR_EG"     => "Arabic",
                        "AR_IQ"     => "Arabic",
                        "AR_JO"     => "Arabic (Jordan)",
                        "AR_KW"     => "Arabic (Kuwait)",
                        "AR_LB"     => "Arabic (Lebanon)",
                        "AR_LY"     => "Arabic (Libya)",
                        "AR_MA"     => "Arabic (Morocco)",
                        "AR_OM"     => "Arabic (Oman)",
                        "AR_QA"     => "Arabic (Qatar)",
                        "AR_SA"     => "Arabic (Saudi Arabia)",
                        "AR_SY"     => "Arabic (Syria)",
                        "AR_TN"     => "Arabic (Tunisia)",
                        "AR_AE"     => "Arabic (U.A.E.)",
                        "AR_YE"     => "Arabic (Yemen)",
                        "EU"        => "Basque",
                        "BE"        => "Belarusian",
                        "BG"        => "Bulgarian",
                        "CA"        => "Catalan",
                        "ZH_HK"     => "Chinese (Hong Kong)",
                        "ZH_CN"     => "Chinese (PRC)",
                        "ZH_SG"     => "Chinese (Singapore)",
                        "ZH_TW"     => "Chinese (Taiwan)",
                        "HR"        => "Croatian",
                        "CS"        => "Czech",
                        "DA"        => "Danish",
                        "NL_BE"     => "Dutch (Belgium)",
                        "NL"        => "Dutch (Standard)",
                        "EN"        => "English",
                        "EN_AU"     => "English (Australia)",
                        "EN_BZ"     => "English (Belize)",
                        "EN_CA"     => "English (Canada)",
                        "EN_IE"     => "English (Ireland)",
                        "EN_JM"     => "English (Jamaica)",
                        "EN_NZ"     => "English (New Zealand)",
                        "EN_ZA"     => "English (South Africa)",
                        "EN_TT"     => "English (Trinidad)",
                        "EN_GB"     => "English (United Kingdom)",
                        "EN_US"     => "English (United States)",
                        "ET"        => "Estonian",
                        "FO"        => "Faeroese",
                        "FA"        => "Farsi",
                        "FI"        => "Finnish",
                        "FR_BE"     => "French (Belgium)",
                        "FR_CA"     => "French (Canada)",
                        "FR_LU"     => "French (Luxembourg)",
                        "FR"        => "French (Standard)",
                        "FR_CH"     => "French (Switzerland)",
                        "GD"        => "Gaelic (Scotland)",
                        "DE_AT"     => "German (Austria)",
                        "DE_LI"     => "German (Liechtenstein)",
                        "DE_LU"     => "German (Luxembourg)",
                        "DE"        => "German (Standard)",
                        "DE_CH"     => "German (Switzerland)",
                        "EL"        => "Greek",
                        "HE"        => "Hebrew",
                        "HI"        => "Hindi",
                        "HU"        => "Hungarian",
                        "IS"        => "Icelandic",
                        "ID"        => "Indonesian",
                        "GA"        => "Irish",
                        "IT"        => "Italian (Standard)",
                        "IT_CH"     => "Italian (Switzerland)",
                        "JA"        => "Japanese",
                        "KO"        => "Korean",
                        "KO"        => "Korean (Johab)",
                        "KU"        => "Kurdish",
                        "LV"        => "Latvian",
                        "LT"        => "Lithuanian",
                        "MK"        => "Macedonian (FYROM)",
                        "ML"        => "Malayalam",
                        "MS"        => "Malaysian",
                        "MT"        => "Maltese",
                        "NO"        => "Norwegian",
                        "NB"        => "Norwegian (Bokmål)",
                        "NN"        => "Norwegian (Nynorsk)",
                        "PL"        => "Polish",
                        "PT_BR"     => "Portuguese (Brazil)",
                        "PT"        => "Portuguese (Portugal)",
                        "PA"        => "Punjabi",
                        "RM"        => "Rhaeto-Romanic",
                        "RO"        => "Romanian",
                        "RU"        => "Russian",
                        "SR"        => "Serbian",
                        "SK"        => "Slovak",
                        "SL"        => "Slovenian",
                        "SB"        => "Sorbian",
                        "ES_AR"     => "Spanish (Argentina)",
                        "ES_BO"     => "Spanish (Bolivia)",
                        "ES_CL"     => "Spanish (Chile)",
                        "ES_CO"     => "Spanish (Colombia)",
                        "ES_CR"     => "Spanish (Costa Rica)",
                        "ES_EC"     => "Spanish (Ecuador)",
                        "ES_SV"     => "Spanish (El Salvador)",
                        "ES_GT"     => "Spanish (Guatemala)",
                        "ES_HN"     => "Spanish (Honduras)",
                        "ES_MX"     => "Spanish (Mexico)",
                        "ES_NI"     => "Spanish (Nicaragua)",
                        "ES_PA"     => "Spanish (Panama)",
                        "ES_PY"     => "Spanish (Paraguay)",
                        "ES_PE"     => "Spanish (Peru)",
                        "ES_PR"     => "Spanish (Puerto Rico)",
                        "ES"        => "Spanish (Spain)",
                        "ES_UY"     => "Spanish (Uruguay)",
                        "ES_VE"     => "Spanish (Venezuela)",
                        "SV"        => "Swedish",
                        "SV_FI"     => "Swedish (Finland)",
                        "TH"        => "Thai",
                        "TS"        => "Tsonga",
                        "TN"        => "Tswana",
                        "TR"        => "Turkish",
                        "UK"        => "Ukrainian",
                        "UR"        => "Urdu",
                        "VE"        => "Venda",
                        "VI"        => "Vietnamese",
                        "CY"        => "Welsh",
                        "XH"        => "Xhosa",
                        "JI"        => "Yiddish",
                        "ZU"        => "Zulu",
                    ],
                ],
                "separator"     => ":",
                "default_value" => 'EN_AU',
                "button_type"   => "info",
                "handler"       => "options",
                "mode"          => "dropdown",
            ]
        ],
        'thrive_sync_status' => [
            "type"   => "anomaly.field_type.text",
            "config"  =>[
                "default_value" => ""
            ]
        ],
    ];

    /**
     * The stream assignments.
     *
     * @var array
     */
    protected $assignments = [

        "thrive_sync_status" => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        "name" => [
            'translatable'      => false,
            'unique'            => true,
            'required'          => true,
        ],
        'str_id' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'permission_reminder' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'email_type_option' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'contact_company_name' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'contact_address1' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => true,
        ],
        'contact_city' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'contact_state' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'contact_zip' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'contact_country' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'campaign_from_name' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'campaign_from_email' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'campaign_subject' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ],
        'campaign_language' => [
            'translatable'      => false,
            'unique'            => false,
            'required'          => false,
        ]
    ];

}