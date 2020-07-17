package com.general.files;

import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;

import com.fastcabtaxi.driver.AccountverificationActivity;
import com.fastcabtaxi.driver.ActiveTripActivity;
import com.fastcabtaxi.driver.CollectPaymentActivity;
import com.fastcabtaxi.driver.DriverArrivedActivity;
import com.fastcabtaxi.driver.MainActivity;
import com.fastcabtaxi.driver.SuspendedDriver_Activity;
import com.fastcabtaxi.driver.TripRatingActivity;
import com.utils.AnimateMarker;
import com.utils.CommonUtilities;
import com.utils.Utils;

import org.json.JSONObject;

import java.util.HashMap;

/**
 * Created by Admin on 29-06-2016.
 */
public class OpenMainProfile {
    private final JSONObject userProfileJsonObj;
    Context mContext;
    String responseString;
    boolean isCloseOnError;
    GeneralFunctions generalFun;
    boolean isnotification = false;
    AnimateMarker animateMarker;

    public OpenMainProfile(Context mContext, String responseString, boolean isCloseOnError, GeneralFunctions generalFun) {
        this.mContext = mContext;
        //this.responseString = responseString;
        this.isCloseOnError = isCloseOnError;
        this.generalFun = generalFun;

        this.responseString = generalFun.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

        userProfileJsonObj = generalFun.getJsonObject(this.responseString);
        animateMarker = new AnimateMarker();

    }

    public OpenMainProfile(Context mContext, String responseString, boolean isCloseOnError, GeneralFunctions generalFun, boolean isnotification) {
        this.mContext = mContext;
        //this.responseString = responseString;
        this.isCloseOnError = isCloseOnError;
        this.generalFun = generalFun;
        this.isnotification = isnotification;

        this.responseString = generalFun.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

        userProfileJsonObj = generalFun.getJsonObject(this.responseString);
        animateMarker = new AnimateMarker();

        generalFun.storedata(CommonUtilities.DefaultCountry, generalFun.getJsonValueStr("vDefaultCountry", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.DefaultCountryCode, generalFun.getJsonValueStr("vDefaultCountryCode", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.DefaultPhoneCode, generalFun.getJsonValueStr("vDefaultPhoneCode", userProfileJsonObj));

    }

    public void startProcess() {
        generalFun.sendHeartBeat();

        // responseString = generalFun.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        setGeneralData();


        animateMarker.driverMarkerAnimFinished = true;

        Bundle bn = new Bundle();
        bn.putString("USER_PROFILE_JSON", responseString);
        bn.putString("IsAppReStart", "true"); // flag for retrieving data to en route trip pages

        String vTripStatus = generalFun.getJsonValueStr("vTripStatus", userProfileJsonObj);

        boolean lastTripExist = false;

        if (vTripStatus.contains("Not Active")) {


            String ratings_From_Driver_str = generalFun.getJsonValueStr("Ratings_From_Driver", userProfileJsonObj);

            if (!ratings_From_Driver_str.equals("Done")) {
                lastTripExist = true;
            }
        }
        if (generalFun.getJsonValue("vPhone", userProfileJsonObj).equals("") || generalFun.getJsonValue("vEmail", userProfileJsonObj).equals("")) {
            if (generalFun.getMemberId() != null && !generalFun.getMemberId().equals("")) {
                new StartActProcess(mContext).startActWithData(AccountverificationActivity.class, bn);
            }
        } else if (vTripStatus != null && !vTripStatus.equals("NONE") && !vTripStatus.equals("Cancelled")
                && (vTripStatus.trim().equals("Active") || vTripStatus.contains("On Going Trip")
                || vTripStatus.contains("Arrived") || lastTripExist == true)) {

            // String last_trip_data = generalFun.getJsonValue("TripDetails", userProfileJsonObj);
            JSONObject last_trip_data = generalFun.getJsonObject("TripDetails", userProfileJsonObj);
            // String passenger_data = generalFun.getJsonValue("PassengerDetails", userProfileJsonObj);
            JSONObject passenger_data = generalFun.getJsonObject("PassengerDetails", userProfileJsonObj);
            HashMap<String, String> map = new HashMap<>();

            map.put("TotalSeconds", generalFun.getJsonValueStr("TotalSeconds", userProfileJsonObj));
            map.put("TimeState", generalFun.getJsonValueStr("TimeState", userProfileJsonObj));
            map.put("iTripTimeId", generalFun.getJsonValueStr("iTripTimeId", userProfileJsonObj));

            map.put("Message", "CabRequested");
            map.put("sourceLatitude", generalFun.getJsonValueStr("tStartLat", last_trip_data));
            map.put("sourceLongitude", generalFun.getJsonValueStr("tStartLong", last_trip_data));

            map.put("tSaddress", generalFun.getJsonValueStr("tSaddress", last_trip_data));
            map.put("drivervName", generalFun.getJsonValue("vName", responseString));
            map.put("drivervLastName", generalFun.getJsonValue("vLastName", responseString));

            map.put("PassengerId", generalFun.getJsonValueStr("iUserId", last_trip_data));
            map.put("PName", generalFun.getJsonValue("vName", passenger_data) + " " + generalFun.getJsonValue("vLastName", passenger_data));
            map.put("PPicName", generalFun.getJsonValueStr("vImgName", passenger_data));
            map.put("PFId", generalFun.getJsonValueStr("vFbId", passenger_data));
            map.put("PRating", generalFun.getJsonValueStr("vAvgRating", passenger_data));
            map.put("PPhone", generalFun.getJsonValueStr("vPhone", passenger_data));
            map.put("PPhoneC", generalFun.getJsonValueStr("vPhoneCode", passenger_data));
            map.put("PAppVersion", generalFun.getJsonValueStr("iAppVersion", passenger_data));
            map.put("TripId", generalFun.getJsonValueStr("iTripId", last_trip_data));
            map.put("DestLocLatitude", generalFun.getJsonValueStr("tEndLat", last_trip_data));
            map.put("DestLocLongitude", generalFun.getJsonValueStr("tEndLong", last_trip_data));
            map.put("DestLocAddress", generalFun.getJsonValueStr("tDaddress", last_trip_data));
            map.put("REQUEST_TYPE", generalFun.getJsonValueStr("eType", last_trip_data));
            map.put("eFareType", generalFun.getJsonValueStr("eFareType", last_trip_data));
            map.put("iTripId", generalFun.getJsonValueStr("iTripId", last_trip_data));
            map.put("fVisitFee", generalFun.getJsonValueStr("fVisitFee", last_trip_data));
            map.put("eHailTrip", generalFun.getJsonValueStr("eHailTrip", last_trip_data));
            map.put("iActive", generalFun.getJsonValueStr("iActive", last_trip_data));
            map.put("eTollSkipped", generalFun.getJsonValueStr("eTollSkipped", last_trip_data));

            map.put("vVehicleType", generalFun.getJsonValueStr("vVehicleType", last_trip_data));
            map.put("vVehicleType", generalFun.getJsonValueStr("eIconType", last_trip_data));


            map.put("eAfterUpload", generalFun.getJsonValueStr("eAfterUpload", last_trip_data));
            map.put("eBeforeUpload", generalFun.getJsonValueStr("eBeforeUpload", last_trip_data));

            map.put("vDeliveryConfirmCode", generalFun.getJsonValueStr("vDeliveryConfirmCode", last_trip_data));
            map.put("SITE_TYPE", generalFun.getJsonValueStr("SITE_TYPE", userProfileJsonObj));
            if (generalFun.getJsonValueStr("APP_TYPE", userProfileJsonObj).equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                map.put("PPetName", generalFun.getJsonValue("PetName", generalFun.getJsonValueStr("PetDetails", last_trip_data)));
                map.put("PPetId", generalFun.getJsonValueStr("iUserPetId", last_trip_data));
                map.put("PPetTypeName", generalFun.getJsonValue("PetTypeName", generalFun.getJsonValueStr("PetDetails", last_trip_data)));
                map.put("tUserComment", generalFun.getJsonValueStr("tUserComment", last_trip_data));
            }
            if (generalFun.getJsonValueStr("APP_TYPE", userProfileJsonObj).equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
                map.put("PPetName", generalFun.getJsonValue("PetName", generalFun.getJsonValueStr("PetDetails", last_trip_data)));
                map.put("PPetId", generalFun.getJsonValueStr("iUserPetId", last_trip_data));
                map.put("PPetTypeName", generalFun.getJsonValue("PetTypeName", generalFun.getJsonValueStr("PetDetails", last_trip_data)));
            }

            if (vTripStatus.contains("Not Active") && lastTripExist == true) {
                // Open rating page
                bn.putSerializable("TRIP_DATA", map);

                String ePaymentCollect = generalFun.getJsonValueStr("ePaymentCollect", last_trip_data);
                if (ePaymentCollect.equals("No")) {
                    new StartActProcess(mContext).startActWithData(CollectPaymentActivity.class, bn);
                } else {
                    new StartActProcess(mContext).startActWithData(TripRatingActivity.class, bn);
                }

            } else {

                if (vTripStatus.contains("Arrived")) {

                    //                    if (!generalFun.isLocationEnabled()) {
//                        generalFun.restartApp();
//                        return;
//
//                    }
                    // Open active trip page
                    map.put("vTripStatus", "Arrived");
                    bn.putSerializable("TRIP_DATA", map);
                    bn.putBoolean("isnotification", isnotification);

                    new StartActProcess(mContext).startActWithData(ActiveTripActivity.class, bn);
                } else if (!vTripStatus.contains("Arrived") && vTripStatus.contains("On Going Trip")) {
                    // Open active trip page

//                    if (!generalFun.isLocationEnabled()) {
//                        generalFun.restartApp();
//                        return;
//
//                    }
                    map.put("vTripStatus", "EN_ROUTE");
                    bn.putSerializable("TRIP_DATA", map);
                    bn.putBoolean("isnotification", isnotification);
                    new StartActProcess(mContext).startActWithData(ActiveTripActivity.class, bn);
                } else if (!vTripStatus.contains("Arrived") && vTripStatus.contains("Active")) {
                    // Open cubetaxiplus arrived page
//                    if (!generalFun.isLocationEnabled()) {
//                        generalFun.restartApp();
//                        return;
//
//                    }
                    bn.putSerializable("TRIP_DATA", map);
                    bn.putBoolean("isnotification", isnotification);

                    new StartActProcess(mContext).startActWithData(DriverArrivedActivity.class, bn);
                }
            }

        } else {

            String eStatus = generalFun.getJsonValueStr("eStatus", userProfileJsonObj);

            if (eStatus.equalsIgnoreCase("suspend")) {
                new StartActProcess(mContext).startAct(SuspendedDriver_Activity.class);
            } else {
                new StartActProcess(mContext).startActWithData(MainActivity.class, bn);

            }
        }


        ActivityCompat.finishAffinity((Activity) mContext);
    }

    public void setGeneralData() {
        generalFun.storedata(Utils.ENABLE_PUBNUB_KEY, generalFun.getJsonValueStr("ENABLE_PUBNUB", userProfileJsonObj));
        generalFun.storedata(Utils.SESSION_ID_KEY, generalFun.getJsonValueStr("tSessionId", userProfileJsonObj));
        generalFun.storedata(Utils.DEVICE_SESSION_ID_KEY, generalFun.getJsonValueStr("tDeviceSessionId", userProfileJsonObj));

        generalFun.storedata(Utils.FETCH_TRIP_STATUS_TIME_INTERVAL_KEY, generalFun.getJsonValueStr("FETCH_TRIP_STATUS_TIME_INTERVAL", userProfileJsonObj));


        generalFun.storedata(Utils.VERIFICATION_CODE_RESEND_TIME_IN_SECONDS_KEY, generalFun.getJsonValueStr(Utils.VERIFICATION_CODE_RESEND_TIME_IN_SECONDS_KEY, userProfileJsonObj));
        generalFun.storedata(Utils.VERIFICATION_CODE_RESEND_COUNT_KEY, generalFun.getJsonValueStr(Utils.VERIFICATION_CODE_RESEND_COUNT_KEY, userProfileJsonObj));
        generalFun.storedata(Utils.VERIFICATION_CODE_RESEND_COUNT_RESTRICTION_KEY, generalFun.getJsonValueStr(Utils.VERIFICATION_CODE_RESEND_COUNT_RESTRICTION_KEY, userProfileJsonObj));

        generalFun.storedata(CommonUtilities.PUBNUB_PUB_KEY, generalFun.getJsonValueStr("PUBNUB_PUBLISH_KEY", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.PUBNUB_SUB_KEY, generalFun.getJsonValueStr("PUBNUB_SUBSCRIBE_KEY", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.PUBNUB_SEC_KEY, generalFun.getJsonValueStr("PUBNUB_SECRET_KEY", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.SITE_TYPE_KEY, generalFun.getJsonValueStr("SITE_TYPE", userProfileJsonObj));

        generalFun.storedata(CommonUtilities.MOBILE_VERIFICATION_ENABLE_KEY, generalFun.getJsonValueStr("MOBILE_VERIFICATION_ENABLE", userProfileJsonObj));
        generalFun.storedata("LOCATION_ACCURACY_METERS", generalFun.getJsonValueStr("LOCATION_ACCURACY_METERS", userProfileJsonObj));
        generalFun.storedata("DRIVER_LOC_UPDATE_TIME_INTERVAL", generalFun.getJsonValueStr("DRIVER_LOC_UPDATE_TIME_INTERVAL", userProfileJsonObj));
        generalFun.storedata("RIDER_REQUEST_ACCEPT_TIME", generalFun.getJsonValueStr("RIDER_REQUEST_ACCEPT_TIME", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.PHOTO_UPLOAD_SERVICE_ENABLE_KEY, generalFun.getJsonValueStr(CommonUtilities.PHOTO_UPLOAD_SERVICE_ENABLE_KEY, userProfileJsonObj));

        generalFun.storedata(CommonUtilities.ENABLE_TOLL_COST, generalFun.getJsonValueStr("ENABLE_TOLL_COST", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.TOLL_COST_APP_ID, generalFun.getJsonValueStr("TOLL_COST_APP_ID", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.TOLL_COST_APP_CODE, generalFun.getJsonValueStr("TOLL_COST_APP_CODE", userProfileJsonObj));
        generalFun.storedata(Utils.ENABLE_PUBNUB_KEY, generalFun.getJsonValueStr("ENABLE_PUBNUB", userProfileJsonObj));

        generalFun.storedata(CommonUtilities.WALLET_ENABLE, generalFun.getJsonValueStr("WALLET_ENABLE", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.REFERRAL_SCHEME_ENABLE, generalFun.getJsonValueStr("REFERRAL_SCHEME_ENABLE", userProfileJsonObj));

        generalFun.storedata(CommonUtilities.APP_DESTINATION_MODE, generalFun.getJsonValueStr("APP_DESTINATION_MODE", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.APP_TYPE, generalFun.getJsonValueStr("APP_TYPE", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.HANDICAP_ACCESSIBILITY_OPTION, generalFun.getJsonValueStr("HANDICAP_ACCESSIBILITY_OPTION", userProfileJsonObj));
        generalFun.storedata(CommonUtilities.FEMALE_RIDE_REQ_ENABLE, generalFun.getJsonValueStr("FEMALE_RIDE_REQ_ENABLE", userProfileJsonObj));


    }
}
