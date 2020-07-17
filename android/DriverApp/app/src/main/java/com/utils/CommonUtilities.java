package com.utils;

/**
 * Created by Admin on 27-06-2016.
 */
public class CommonUtilities {
    public static final String app_name = "DriverApp";
    public static final String MINT_APP_ID = "90eb5708";
    public static final String package_name = "com.fastcabtaxi.driver";


    public static final String SERVER = "https://www.fastcab.co.za/";
    public static final String SERVER_FOLDER_PATH = "";
    public static final String SERVER_WEBSERVICE_PATH = SERVER_FOLDER_PATH + "webservice.php";

    public static final String SERVER_URL = SERVER + SERVER_FOLDER_PATH;
    public static final String SERVER_URL_WEBSERVICE = SERVER + SERVER_WEBSERVICE_PATH + "?";
    public static final String SERVER_URL_PHOTOS = SERVER_URL + "webimages/";
    public static final String PUBNUB_PUB_KEY = "PUBNUB_PUBLISH_KEY";
    public static final String PUBNUB_SUB_KEY = "PUBNUB_SUBSCRIBE_KEY";
    public static final String PUBNUB_SEC_KEY = "PUBNUB_SECRET_KEY";

    public static final String TOLLURL = "https://tce.cit.api.here.com/2/calculateroute.json?app_id=";
    public static String app_type = "Driver";
    public static String languageLabelsKey = "LanguageLabel";
    public static String APP_GCM_SENDER_ID_KEY = "APP_GCM_SENDER_ID";
    public static String MOBILE_VERIFICATION_ENABLE_KEY = "MOBILE_VERIFICATION_ENABLE";
    public static String FACEBOOK_APPID_KEY = "FACEBOOK_APPID";
    public static String LINK_FORGET_PASS_KEY = "LINK_FORGET_PASS_PAGE_DRIVER";
    public static String LINK_SIGN_UP_PAGE_KEY = "LINK_SIGN_UP_PAGE_DRIVER";
    public static String LANGUAGE_LIST_KEY = "LANGUAGELIST";
    public static String CURRENCY_LIST_KEY = "CURRENCYLIST";
    public static String LANGUAGE_IS_RTL_KEY = "LANG_IS_RTL";
    public static String GOOGLE_MAP_LANGUAGE_CODE_KEY = "GOOGLE_MAP_LANG_CODE";
    public static String REFERRAL_SCHEME_ENABLE = "REFERRAL_SCHEME_ENABLE";
    public static String WALLET_ENABLE = "WALLET_ENABLE";
    public static String SITE_TYPE_KEY = "SITE_TYPE";
    public static String ENABLE_TOLL_COST = "ENABLE_TOLL_COST";
    public static String TOLL_COST_APP_ID = "TOLL_COST_APP_ID";
    public static String TOLL_COST_APP_CODE = "TOLL_COST_APP_CODE";
    public static String DRIVER_CURRENT_REQ_OPEN_KEY = "DRIVER_REQ_OPEN";

    public static String DefaultCountry = "vDefaultCountry";
    public static String DefaultCountryCode = "vDefaultCountryCode";
    public static String DefaultPhoneCode = "vDefaultPhoneCode";

    public static String DATABASE_RTL_STR = "rtl";
    public static String LANGUAGE_CODE_KEY = "LANG_CODE";
    public static String isUserLogIn = "IsUserLoggedIn";
    public static String iMemberId_KEY = "iUserId";
    public static String APP_TYPE = "APP_TYPE";
    public static String action_str = "Action";
    public static String message_str = "message";
    public static String message_str_one = "message1";

    public static final String TRIP_REQ_CODE_PREFIX_KEY = "TRIP_STATUS_MSG_";

    public static String APP_DESTINATION_MODE = "APP_DESTINATION_MODE";

    public static String NONE_DESTINATION = "None";
    public static String STRICT_DESTINATION = "Strict";
    public static String NON_STRICT_DESTINATION = "NonStrict";


    public static String IsTripStarted = "TripStart";
    public static String DriverWaitingTime = "DriverWaitingTime";
    public static String DriverWaitingSecTime = "DriverWaitingSecTime";

    public static String GO_ONLINE_KEY = "GO_ONLINE";
    public static String LAST_FINISH_TRIP_TIME_KEY = "LAST_FINISH_TRIP_TIME";
    public static String PHOTO_UPLOAD_SERVICE_ENABLE_KEY = "PHOTO_UPLOAD_SERVICE_ENABLE";

    public static String GCM_FAILED_KEY = "GCM_FAILED";
    public static String APNS_FAILED_KEY = "APNS_FAILED";

    public static String DRIVER_ONLINE_KEY = "IS_DRIVER_ONLINE";
    public static String DRIVER_REQ_CODE_PREFIX_KEY = "REQUEST_CODE_";
    public static String DRIVER_ACTIVE_REQ_MSG_KEY = "ACTIVE_REQUEST_MSG_";
    public static String DRIVER_REQ_COMPLETED_MSG_CODE_KEY = "REQUEST_CODE_CONFIRMED_";

    public static String BACKGROUND_APP_RECEIVER_INTENT_ACTION = "BACKGROUND_CALLBACK_ACTION";

    public static String passenger_message_arrived_intent_action = package_name + ".CALLFROMUSER";
    public static String passenger_message_arrived_intent_action_trip_msg = package_name + ".CALLFROMUSER.TRIP.MSG";
    public static String passenger_message_arrived_intent_key = "message";


    public static String DEFAULT_LANGUAGE_VALUE = "DEFAULT_LANGUAGE_VALUE";
    public static String DEFAULT_CURRENCY_VALUE = "DEFAULT_CURRENCY_VALUE";

    public static final String USER_PROFILE_JSON = "User_Profile";

    public static String HANDICAP_ACCESSIBILITY_OPTION = "HANDICAP_ACCESSIBILITY_OPTION";
    public static String FEMALE_RIDE_REQ_ENABLE = "FEMALE_RIDE_REQ_ENABLE";
    public static String PREF_FEMALE = "Female_request";


    public static String FACEBOOK_LOGIN = "FACEBOOK_LOGIN";
    public static String GOOGLE_LOGIN = "GOOGLE_LOGIN";
    public static String TWITTER_LOGIN = "TWITTER_LOGIN";

    public static String WORKLOCATION = "vWorkLocation";

}
