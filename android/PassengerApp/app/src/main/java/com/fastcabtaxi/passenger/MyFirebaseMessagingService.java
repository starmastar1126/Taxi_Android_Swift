package com.fastcabtaxi.passenger;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.PowerManager;

import com.general.files.GeneralFunctions;
import com.general.files.MyApp;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;
import com.utils.CommonUtilities;
import com.utils.Utils;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;


public class MyFirebaseMessagingService extends FirebaseMessagingService {

    private static final String TAG = MyFirebaseMessagingService.class.getSimpleName();


    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {


        if (remoteMessage == null || remoteMessage.getData() == null/* || remoteMessage.getNotification().getBody() == null*/)
            return;


        String message = remoteMessage.getData().get("message");
        GeneralFunctions generalFunc = new GeneralFunctions(this);
        if (generalFunc.getJsonValue("MsgType", remoteMessage.getData().get("message")).equals("CHAT")) {
            sendNotification(remoteMessage.getData().get("message"));
        } else {
            if (message != null && Utils.checkText(message)) {
                Utils.printELog("firebase", "message" + remoteMessage.getData().get("message").toString());
                if (isJSONValid(message)) {
                    Intent intent_broad = new Intent(CommonUtilities.driver_message_arrived_intent_action);
                    intent_broad.putExtra(CommonUtilities.driver_message_arrived_intent_key, message);
                    this.sendBroadcast(intent_broad);
                    if (!MyApp.getInstance().isMyAppInBackGround()) {
                        Utils.printLog("broadcast", "called");

                    } else {

                        if (generalFunc.getJsonValue("MsgType", remoteMessage.getData().get("message")).equals("TripEnd") ||
                                generalFunc.getJsonValue("Message", remoteMessage.getData().get("message")).equals("TripEnd") || (
                                generalFunc.getJsonValue("MsgType", remoteMessage.getData().get("message")).equals("TripCancelledByDriver") && generalFunc.getJsonValue("ShowTripFare", message).equalsIgnoreCase("true")) || (
                                generalFunc.getJsonValue("Message", remoteMessage.getData().get("message")).equals("TripCancelledByDriver") &&
                                        generalFunc.getJsonValue("ShowTripFare", message).equalsIgnoreCase("true"))) {
                            Utils.generateNotificationForRating(getApplicationContext(), generalFunc.getJsonValue("vTitle", message), generalFunc.getJsonValue("iTripId", message));
                        } else {
                            Utils.generateNotification(getApplicationContext(), generalFunc.getJsonValue("vTitle", message));

                        }

                    }

                } else {
                    try {

                        PowerManager powerManager = (PowerManager) MyApp.getCurrentAct().getSystemService(Context.POWER_SERVICE);
                        boolean isScreenOn = powerManager.isScreenOn();
                        if (!MyApp.getInstance().isMyAppInBackGround()) {
                            buildMessage(message);
                        } else {
                            Utils.generateNotification(getApplicationContext(), message);
                            buildMessage(message);
                        }
                    } catch (Exception e) {


                    }
                }
            }
        }
    }

    public void buildMessage(final String message) {
        MyApp.getCurrentAct().runOnUiThread(new Runnable() {
            @Override
            public void run() {
                GeneralFunctions generalFunc = new GeneralFunctions(MyApp.getCurrentAct());
                generalFunc.showGeneralMessage("", message);
            }
        });
    }


    public boolean isJSONValid(String test) {
        try {
            new JSONObject(test);
        } catch (JSONException ex) {
            try {
                new JSONArray(test);
            } catch (JSONException ex1) {
                return false;
            }
        }
        return true;
    }

    private void sendNotification(String messageBody) {

        GeneralFunctions generalFunc = new GeneralFunctions(this);
        if (generalFunc.getJsonValue("MsgType", messageBody).equals("CHAT")) {


            if (Utils.getPreviousIntent(this) != null) {


                Bundle bn = new Bundle();

                bn.putString("iFromMemberId", generalFunc.getJsonValue("iFromMemberId", messageBody));
                bn.putString("FromMemberImageName", generalFunc.getJsonValue("FromMemberImageName", messageBody));
                bn.putString("iTripId", generalFunc.getJsonValue("iTripId", messageBody));
                bn.putString("FromMemberName", generalFunc.getJsonValue("FromMemberName", messageBody));

                if (getApp().isMyAppInBackGround() == true) {
                    if (!MyApp.getCurrentAct().getClass().getSimpleName().equals("ChatActivity")) {

                        Intent show_timer = new Intent();
                        show_timer.setClass(getApplicationContext(), ChatActivity.class);
                        show_timer.putExtras(bn);
                        show_timer.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                        getApplicationContext().startActivity(show_timer);
                    }
                } else {

                    if (MyApp.getCurrentAct().getClass().getSimpleName().equals("ChatActivity")) {
                        Utils.generateNotification(this, generalFunc.getJsonValue("Msg", messageBody));
                        return;
                    }

                    Intent show_timer = new Intent();
                    show_timer.setClass(this, ChatActivity.class);
                    show_timer.putExtras(bn);

                    show_timer.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                    this.startActivity(show_timer);

                }

            } else {
                generalFunc.storedata("OPEN_CHAT", "Yes");

                Utils.generateNotification(this, generalFunc.getJsonValue("Msg", messageBody));
            }
        }


    }

    public MyApp getApp() {
        return ((MyApp) getApplication());
    }

}
