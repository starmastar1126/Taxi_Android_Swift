package com.general.files;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

import com.fastcabtaxi.driver.ActiveTripActivity;
import com.fastcabtaxi.driver.ChatActivity;
import com.fastcabtaxi.driver.DriverArrivedActivity;
import com.utils.CommonUtilities;
import com.utils.Utils;

/**
 * Created by Admin on 19-07-2016.
 */
public class TripMessageReceiver extends BroadcastReceiver {

    GeneralFunctions generalFunc;

    Activity activity;
    boolean isTripStartPage;
    boolean isTripCancelled=false;

    public TripMessageReceiver(Activity activity, boolean isTripStartPage) {
        this.activity = activity;
        this.isTripStartPage = isTripStartPage;
    }

    @Override
    public void onReceive(Context context, Intent intent) {
        if (generalFunc == null) {
            generalFunc = new GeneralFunctions(context);
        }

        if (intent.getAction().equals(CommonUtilities.passenger_message_arrived_intent_action_trip_msg) && intent != null) {
            String json_message = intent.getExtras().getString(CommonUtilities.passenger_message_arrived_intent_key);

            if (generalFunc.getJsonValue("Message", json_message).equals("TripCancelled")) {
                if (isTripCancelled==true)
                {
                    return;
                }


                isTripCancelled = true;


                if (activity instanceof ChatActivity) {
                    ((ChatActivity) activity).tripCancelled(generalFunc.getJsonValue("vTitle",json_message));
                } else if (activity instanceof ActiveTripActivity && isTripStartPage == true) {
                    ((ActiveTripActivity) activity).tripCancelled(generalFunc.getJsonValue("vTitle",json_message));
                } else if (activity instanceof DriverArrivedActivity) {
                    ((DriverArrivedActivity) activity).tripCancelled(generalFunc.getJsonValue("vTitle",json_message));
                }


//                if (isTripStartPage == true) {
//
//                    if (activity instanceof ChatActivity) {
//                        ((ChatActivity) activity).tripCancelled();
//                    } else {
//                        ((ActiveTripActivity) activity).tripCancelled();
//                    }
//                } else {
//                    if (activity instanceof ChatActivity) {
//                        ((ChatActivity) activity).tripCancelled();
//
//                    } else {
//                        ((DriverArrivedActivity) activity).tripCancelled();
//                    }
//                }

            } else if (generalFunc.getJsonValue("Message", json_message).equals("DestinationAdded")) {

                Utils.generateNotification(context, generalFunc.getJsonValue("vTitle",json_message));

                generalFunc.showGeneralMessage("", generalFunc.getJsonValue("vTitle",json_message));
                if (isTripStartPage == true) {
                    ((ActiveTripActivity) activity).onDestAddedByPassenger(json_message);
                }
            }
        }

    }
}
