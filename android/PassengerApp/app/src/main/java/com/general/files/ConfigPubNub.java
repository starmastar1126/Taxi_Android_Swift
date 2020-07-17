package com.general.files;

import android.app.Activity;
import android.content.Context;
import android.content.IntentFilter;
import android.location.Location;
import android.os.Handler;

import com.fastcabtaxi.passenger.MainActivity;
import com.pubnub.api.PNConfiguration;
import com.pubnub.api.PubNub;
import com.pubnub.api.callbacks.PNCallback;
import com.pubnub.api.callbacks.SubscribeCallback;
import com.pubnub.api.models.consumer.PNPublishResult;
import com.pubnub.api.models.consumer.PNStatus;
import com.pubnub.api.models.consumer.pubsub.PNMessageResult;
import com.pubnub.api.models.consumer.pubsub.PNPresenceEventResult;
import com.utils.CommonUtilities;
import com.utils.Utils;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;

/**
 * Created by Admin on 05-10-2016.
 */
public class ConfigPubNub extends SubscribeCallback implements GetLocationUpdates.LocationUpdates, UpdateFrequentTask.OnTaskRunCalled {
    private static ConfigPubNub instance = null;
    public boolean isSessionout = false;
    Context mContext;
    PubNub pubnub;
    GeneralFunctions generalFunc;
    GcmBroadCastReceiver gcmMessageBroadCastReceiver;
    private Location userLoc;
    private String iTripId = "";
    private String iDriverId = "";
    private ExecuteWebServerUrl currentExeTask;
    private UpdateFrequentTask updatepassengerStatustask;
    private InternetConnection intCheck;
    private GetLocationUpdates getLocUpdates;
    private boolean isPubnubInstKilled = false;

    public static ConfigPubNub getInstance() {
        if (instance == null) {
            instance = new ConfigPubNub();
        }
        return instance;
    }

    public static ConfigPubNub getInstance(boolean isOnlyChk) {
        return instance;
    }

    public void ConfigPubNub(Context mContext) {
        this.mContext = mContext;
        generalFunc = new GeneralFunctions(mContext);
        intCheck = new InternetConnection(mContext);
        PNConfiguration pnConfiguration = new PNConfiguration();

        pnConfiguration.setUuid(generalFunc.retrieveValue(Utils.DEVICE_SESSION_ID_KEY).equals("") ? generalFunc.getMemberId() : generalFunc.retrieveValue(Utils.DEVICE_SESSION_ID_KEY));

        pnConfiguration.setSubscribeKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_SUB_KEY));
        pnConfiguration.setPublishKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_PUB_KEY));
        pnConfiguration.setSecretKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_SEC_KEY));

        if (pubnub != null) {
            try {
                pubnub.forceDestroy();
            } catch (Exception e) {

            }
        }
        pubnub = new PubNub(pnConfiguration);

        pubnub.addListener(this);

        if (getLocUpdates != null) {
            getLocUpdates.stopLocationUpdates();
            getLocUpdates = null;
        }
        getLocUpdates = new GetLocationUpdates(mContext, Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, true, this);


        if (updatepassengerStatustask != null) {
            updatepassengerStatustask.stopRepeatingTask();
            updatepassengerStatustask = null;
        }
        updatepassengerStatustask = new UpdateFrequentTask(generalFunc.parseIntegerValue(15, generalFunc.retrieveValue(Utils.FETCH_TRIP_STATUS_TIME_INTERVAL_KEY)) * 1000);
        updatepassengerStatustask.setTaskRunListener(this);
        updatepassengerStatustask.startRepeatingTask();


        subscribeToPrivateChannel();

        connectToPubNub(10000);
        connectToPubNub(20000);
        connectToPubNub(30000);


        if (gcmMessageBroadCastReceiver != null) {
            unRegisterGcmReceiver();
            gcmMessageBroadCastReceiver = null;
        }
        gcmMessageBroadCastReceiver = new GcmBroadCastReceiver(this);

        registerGcmMsgReceiver();

    }

    public void registerGcmMsgReceiver() {
        try {
            IntentFilter filter = new IntentFilter();
            filter.addAction(CommonUtilities.driver_message_arrived_intent_action);

            mContext.registerReceiver(gcmMessageBroadCastReceiver, filter);
        } catch (Exception e) {

        }
    }


    public void unRegisterGcmReceiver() {
        try {
            if (mContext != null && gcmMessageBroadCastReceiver != null) {
                mContext.unregisterReceiver(gcmMessageBroadCastReceiver);
            }
        } catch (Exception e) {

        }
    }


    public void setTripId(String iTripId, String iDriverId) {
        this.iTripId = iTripId;
        this.iDriverId = iDriverId;
    }

    /*private void connectTopubNub() {
        isPubnubInstKilled = false;
        pubnub.reconnect();
    }*/

    public void connectToPubNub(int interval) {
//        isPubnubInstKilled = false;
        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                if (pubnub != null) {
                    pubnub.reconnect();
                }
            }
        }, interval);
    }

    public void connectToPubNub() {
//        isPubnubInstKilled = false;
        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                if (pubnub != null) {
                    pubnub.reconnect();
                }
            }
        }, 10000);
    }

    public void connectToPubNub(final PubNub pubNub) {

        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                if (pubNub != null) {
                    pubNub.reconnect();
                }
            }
        }, 10000);
    }

    public void subscribeToPrivateChannel() {
        try {
            pubnub.subscribe()
                    .channels(Arrays.asList("PASSENGER_" + generalFunc.getMemberId())) // subscribe to channels
                    .execute();
        } catch (Exception e) {

        }
    }

    public void unSubscribeToPrivateChannel() {
        try {
            pubnub.unsubscribe()
                    .channels(Arrays.asList("PASSENGER_" + generalFunc.getMemberId())) // subscribe to channels
                    .execute();
        } catch (Exception e) {

        }
    }

    public void releaseInstances() {
        try {

            if (pubnub != null) {
                pubnub.removeListener(this);
                pubnub.unsubscribeAll();
                pubnub.forceDestroy();
            }
            //isPubnubInstKilled = true;
            if (updatepassengerStatustask != null) {
                updatepassengerStatustask.stopRepeatingTask();
                updatepassengerStatustask = null;
            }


            if (getLocUpdates != null) {
                getLocUpdates.stopLocationUpdates();
                getLocUpdates = null;
            }

            unRegisterGcmReceiver();

            //instance = null;
        } catch (Exception e) {
            Utils.printELog("releaseInstances", "::" + e.toString());

        }

    }

    public void subscribeToChannels(ArrayList<String> channels) {
        pubnub.subscribe()
                .channels(channels) // subscribe to channels
                .execute();
    }

    public void unSubscribeToChannels(ArrayList<String> channels) {
        pubnub.unsubscribe()
                .channels(channels)
                .execute();
    }

    public boolean isJsonObj(String json) {

        try {
            new JSONObject(json);
        } catch (JSONException ex) {
            // edited, to include @Arthur's comment
            // e.g. in case JSONArray is valid as well...
            return false;
        }
        return true;
    }

    public void publishMsg(String channel, String message) {
//        .message(Arrays.asList("hello", "there"))
        pubnub.publish()
                .message(message)
                .channel(channel)
                .async(new PNCallback<PNPublishResult>() {
                    @Override
                    public void onResponse(PNPublishResult result, PNStatus status) {
                        // handle publish result, status always present, result if successful
                        // status.isError to see if error happened
                        Utils.printLog("Publish Res", "::::" + result.getTimetoken());
                    }
                });
    }


    private void getUpdatedPassengerStatus() {

        if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {
            return;
        }
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "configPassengerTripStatus");
        parameters.put("iTripId", iTripId);
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("UserType", Utils.userType);

        if (userLoc != null) {
            parameters.put("vLatitude", "" + userLoc.getLatitude());
            parameters.put("vLongitude", "" + userLoc.getLongitude());
        }

        if (Utils.checkText(iTripId)) {
            parameters.put("CurrentDriverIds", "" + iDriverId);
        } else if (mContext != null) {
            if (mContext instanceof MainActivity) {
                if (((MainActivity) mContext).getAvailableDriverIds() != null)
                    parameters.put("CurrentDriverIds", "" + ((MainActivity) mContext).getAvailableDriverIds());
            }


        }


        if (this.currentExeTask != null) {
            this.currentExeTask.cancel(true);
            this.currentExeTask = null;
            Utils.runGC();
        }

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        this.currentExeTask = exeWebServer;
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && Utils.checkText(responseString)) {
                    //  Utils.printLog("Api", "configPassengerTripStatus ::" + responseString);

                    boolean isDataAvail = generalFunc.checkDataAvail(CommonUtilities.action_str, responseString);

                    String message = Utils.checkText(responseString) ? generalFunc.getJsonValue(CommonUtilities.message_str, responseString) : null;

                    if (mContext != null && mContext instanceof Activity) {
                        Activity act = (Activity) mContext;
                        if (!act.isFinishing()) {
                            if (message != null && message.equals("SESSION_OUT")) {

                                isSessionout = true;

                                Utils.printLog("SessionOutRes", "=>" + isSessionout);

                                if (currentExeTask != null) {
                                    currentExeTask.cancel(true);
                                    currentExeTask = null;
                                }

                                if (updatepassengerStatustask != null) {
                                    updatepassengerStatustask.stopRepeatingTask();
                                    updatepassengerStatustask = null;
                                    releaseInstances();
                                }
                                generalFunc.notifySessionTimeOut();
                                return;
                            }
                        }
                    }


                    if (isDataAvail == true) {

                        dispatchMsg(generalFunc.getJsonValue(CommonUtilities.message_str, responseString));

                    }
                    JSONArray currentDrivers = generalFunc.getJsonArray("currentDrivers", responseString);
                    if (currentDrivers != null && currentDrivers.length() > 0 && isPubnubInstKilled == false) {

                        String PUBNUB_DISABLED = generalFunc.retrieveValue(CommonUtilities.PUBNUB_DISABLED_KEY);

                        for (int i = 0; i < currentDrivers.length(); i++) {


                            try {
                                String data_temp = currentDrivers.get(i).toString();
                                JSONObject obj = new JSONObject();
                                obj.put("MsgType", Utils.checkText(iTripId) ? "LocationUpdateOnTrip" : "LocationUpdate");
                                obj.put("iDriverId", generalFunc.getJsonValue("iDriverId", data_temp));
                                obj.put("vLatitude", generalFunc.getJsonValue("vLatitude", data_temp));
                                obj.put("vLongitude", generalFunc.getJsonValue("vLongitude", data_temp));
                                obj.put("ChannelName", Utils.pubNub_Update_Loc_Channel_Prefix + generalFunc.getJsonValue("iDriverId", data_temp));
                                obj.put("LocTime", System.currentTimeMillis() + "");

                                message = obj.toString();
                                // dispatchMsg(message);

                                if (PUBNUB_DISABLED.equalsIgnoreCase("YES")) {
                                    dispatchMsg(message);

                                }
                            } catch (Exception e) {

                            }

                        }
                    }


                }


            }
        });
        exeWebServer.execute();
    }

    @Override
    public void onLocationUpdate(Location location) {
        if (location == null) {
            return;
        }
        this.userLoc = location;
    }

    @Override
    public void onTaskRun() {

        Utils.printLog("SessionOut", "=>" + isSessionout);
        if (!isSessionout) {
            generalFunc.sendHeartBeat();

            getUpdatedPassengerStatus();
        }
    }


    public void dispatchMsg(final String message) {

        String finalMsg = message;

//        if (isPubnubInstKilled == true) {
//            return;
//        }

        try {
            finalMsg = new JSONTokener(message).nextValue().toString();
            Utils.printLog("finalMsg", ":=>" + finalMsg);
        } catch (Exception e) {

            Utils.printLog("jsonExcep", ":=>" + e.toString());
            e.printStackTrace();

        }

        Activity act = (Activity) mContext;
        act.runOnUiThread(new Runnable() {
            @Override
            public void run() {
                String finalMsg = message.replaceAll("^\"|\"$", "");


                if (!isJsonObj(finalMsg)) {
                    finalMsg = message.replaceAll("\\\\", "");

                    finalMsg = finalMsg.replaceAll("^\"|\"$", "");

                    if (!isJsonObj(finalMsg)) {
                        finalMsg = message.replace("\\\"", "\"").replaceAll("^\"|\"$", "");
                    }

                    finalMsg = finalMsg.replace("\\\\\"", "\\\"");
                }

                Utils.printLog("dispatchMsgAfter", "::" + message);
                if (generalFunc.isTripStatusMsgExist(finalMsg)) {
                    Utils.printLog("isTripMsg", "true");
                    return;
                }

                Utils.printLog("isTripMsg", "false");

                showpopup(finalMsg);

                if (mContext instanceof MainActivity) {
                    ((MainActivity) mContext).pubNubMsgArrived(finalMsg);
                }
            }
        });


    }

    public void showpopup(String Messages) {

        String msgType = generalFunc.getJsonValue("MsgType", Messages).trim();
        String eType = generalFunc.getJsonValue("eType", Messages);

        Utils.printLog("eType::", eType);
        String iTripIdmsg = generalFunc.getJsonValue("iTripId", Messages);

        if (msgType.equals("TripEnd")) {

            generalFunc.storedata(CommonUtilities.ISWALLETBALNCECHANGE, "Yes");

            Utils.printLog("showpopup", ":: TripEnd");
            if (!eType.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                generalFunc.showPubnubGeneralMessage("", generalFunc.getJsonValue("vTitle", Messages), true, false);
            } else {
                generalFunc.showPubnubGeneralMessage(iTripId, generalFunc.getJsonValue("vTitle", Messages), false, true);
            }
        } else if (msgType.equals("DriverArrived")) {
            Utils.printLog("showpopup", ":: DriverArrived");

            generalFunc.showPubnubGeneralMessage("", generalFunc.getJsonValue("vTitle", Messages), false, false);
        } else {
            String driverMsg = generalFunc.getJsonValue("Message", Messages).trim();

            Utils.printLog("Message::", driverMsg);
            if (driverMsg.equals("TripEnd")) {
                generalFunc.storedata(CommonUtilities.ISWALLETBALNCECHANGE, "Yes");
                Utils.printLog("showpopup", ":: TripEnd");

                if (!eType.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                    generalFunc.showPubnubGeneralMessage("", generalFunc.getJsonValue("vTitle", Messages), true, false);
                } else {
                    generalFunc.showPubnubGeneralMessage(iTripId, generalFunc.getJsonValue("vTitle", Messages), false, true);
                }
            } else if (driverMsg.equals("TripStarted")) {
                Utils.printLog("showpopup", ":: TripStarted");
                generalFunc.showPubnubGeneralMessage("", generalFunc.getJsonValue("vTitle", Messages), false, false);
            } else if (driverMsg.equals("TripCancelledByDriver")) {
                generalFunc.storedata(CommonUtilities.ISWALLETBALNCECHANGE, "Yes");
                Utils.printLog("showpopup", ":: TripCancelledByDriver");

                if (!eType.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                    generalFunc.showPubnubGeneralMessage("", generalFunc.getJsonValue("vTitle", Messages), true, false);
                } else {

                    if (generalFunc.getJsonValue("ShowTripFare", Messages).equalsIgnoreCase("true")) {
                        generalFunc.showPubnubGeneralMessage(iTripId, generalFunc.getJsonValue("vTitle", Messages), false, true);
                    } else {
                        generalFunc.showPubnubGeneralMessage(iTripId, generalFunc.getJsonValue("vTitle", Messages), false, false);
                    }
                }
            }
        }
    }


    @Override
    public void status(PubNub pubnub, PNStatus status) {
        if (pubnub == null || status == null || status.getCategory() == null) {
            connectToPubNub();
            return;
        }

        if (mContext instanceof MainActivity) {
            ((MainActivity) mContext).pubNubStatus(status.getCategory());
        }
        switch (status.getCategory()) {
            case PNMalformedResponseCategory:
            case PNUnexpectedDisconnectCategory:
            case PNTimeoutCategory:
            case PNNetworkIssuesCategory:
            case PNDisconnectedCategory:
                connectToPubNub(pubnub);
                break;
            case PNConnectedCategory:
                // Connect event. You can do stuff like publish, and know you'll get it.
                // Or just use the connected event to confirm you are subscribed for
                // UI / internal notifications, etc
                break;

            default:
                break;

        }
    }

    @Override
    public void message(PubNub pubnub, PNMessageResult message) {

        dispatchMsg(message.getMessage().toString());

        //dispatchMsg(message.getMessage().toString().replaceAll("^\"|\"$", ""));
    }

    @Override
    public void presence(PubNub pubnub, PNPresenceEventResult presence) {

    }

}

    /*SubscribeCallback subscribeCallback = new SubscribeCallback() {
        @Override
        public void status(final PubNub pubnub, final PNStatus status) {
            // the status object returned is always related to subscribe but could contain
            // information about subscribe, heartbeat, or errors
            // use the operationType to switch on different options
            if (status == null || status.getOperation() == null) {
//                Utils.printLog("status operation", ":::re connected::" + status.toString());
                connectTopubNub();
                return;
            }

            switch (status.getOperation()) {
                // let's combine unsubscribe and subscribe handling for ease of use
                case PNSubscribeOperation:
                case PNUnsubscribeOperation:
                    // note: subscribe statuses never have traditional
                    // errors, they just have categories to represent the
                    // different issues or successes that occur as part of subscribe
                    switch (status.getCategory()) {
                        case PNConnectedCategory:
                            // this is expected for a subscribe, this means there is no error or issue whatsoever
//                            Utils.printLog("PNConnectedCategory", ":::connected::" + status.toString());
                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_Connected);
                            }
                        case PNReconnectedCategory:
                            // this usually occurs if subscribe temporarily fails but reconnects. This means
                            // there was an error but there is no longer any issue
//                            Utils.printLog("PNReconnectedCategory", ":::re connected::" + status.toString());
                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_Connected);
                            }
                        case PNDisconnectedCategory:
                            // this is the expected category for an unsubscribe. This means there
                            // was no error in unsubscribing from everything
//                             Utils.printLog("PNDisconnectedCategory", ":::dis connected::" + status.toString());
//                            if(mContext instanceof MainActivity){
//                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_DisConnected);
//                            }
                        case PNTimeoutCategory:
                        case PNUnexpectedDisconnectCategory:
                            // this is usually an issue with the internet connection, this is an error, handle appropriately
                            // retry will be called automatically
                            new Handler().postDelayed(new Runnable() {
                                @Override
                                public void run() {
                                    connectTopubNub();
                                }
                            }, 1500);

//                            Utils.printLog("PNUnexpectedDisconnect", ":::dis unexpected::" + status.toString());
                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_DisConnected);
                            }
                        case PNAccessDeniedCategory:
                            // this means that PAM does allow this client to subscribe to this
                            // channel and channel group configuration. This is another explicit error
//                             Utils.printLog("AccessDenied", ":::denied::" + status.toString());
//                            if(mContext instanceof MainActivity){
//                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_Denied);
//                            }
                        default:
                            // More errors can be directly specified by creating explicit cases for other
                            // error categories of `PNStatusCategory` such as `PNTimeoutCategory` or `PNMalformedFilterExpressionCategory` or `PNDecryptionErrorCategory`
//                            Utils.printLog("Default", ":::default::" + status.toString());
                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_Error_Connection);
                            }
                    }

                case PNHeartbeatOperation:
                    // heartbeat operations can in fact have errors, so it is important to check first for an error.
                    // For more information on how to configure heartbeat notifications through the status
                    // PNObjectEventListener callback, consult <link to the PNCONFIGURATION heartbeart config>
                    if (status.isError()) {
                        // There was an error with the heartbeat operation, handle here
//                        Utils.printLog("PNHeartbeatOperation", ":::failed::" + status.toString());
                    } else {
                        // heartbeat operation was successful
//                        Utils.printLog("PNHeartbeatOperation", ":::success::" + status.toString());
                    }
                default: {
                    // Encountered unknown status type
//                    Utils.printLog("unknown status", ":::unknown::" + status.toString());
                }
            }
        }

        @Override
        public void message(PubNub pubnub, final PNMessageResult message) {

            // handle incoming messages

            //Do things...
            dispatchMsg(message.getMessage().toString().replaceAll("^\"|\"$", ""));


        }

        @Override
        public void presence(PubNub pubnub, PNPresenceEventResult presence) {
            // handle incoming presence data
            Utils.printLog("ON presence", ":::got::" + presence.toString());
        }
    };*/