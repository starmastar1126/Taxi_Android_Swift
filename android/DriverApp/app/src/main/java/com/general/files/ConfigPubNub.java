package com.general.files;

import android.app.Activity;
import android.content.Context;
import android.location.Location;
import android.os.Handler;

import com.fastcabtaxi.driver.MainActivity;
import com.pubnub.api.PNConfiguration;
import com.pubnub.api.PubNub;
import com.pubnub.api.callbacks.PNCallback;
import com.pubnub.api.callbacks.SubscribeCallback;
import com.pubnub.api.enums.PNReconnectionPolicy;
import com.pubnub.api.models.consumer.PNPublishResult;
import com.pubnub.api.models.consumer.PNStatus;
import com.pubnub.api.models.consumer.pubsub.PNMessageResult;
import com.pubnub.api.models.consumer.pubsub.PNPresenceEventResult;
import com.utils.CommonUtilities;
import com.utils.Utils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;

/**
 * Created by Admin on 05-10-2016.
 */
public class ConfigPubNub extends SubscribeCallback implements GetLocationUpdates.LocationUpdates, UpdateFrequentTask.OnTaskRunCalled {
    public boolean isSubsToCabReq = false;
    public boolean isPubnubInstKilled = false;
    public Location driverLoc = null;
    Context mContext;
    PubNub pubnub;
    GeneralFunctions generalFunc;

    ArrayList<String[]> listOfPublishMsg = new ArrayList<>();
    boolean isCurrentMsgPublished = true;

    private InternetConnection intCheck;
    private GetLocationUpdates getLocUpdates;
    private UpdateFrequentTask updatedriverStatustask;
    private String iTripId = "";
    private String PassengerId = "";
    private ExecuteWebServerUrl currentExeTask;
    public boolean isSessionout = false;


    public ConfigPubNub(Context mContext) {
        this.mContext = mContext;

        generalFunc = new GeneralFunctions(mContext);
        intCheck = new InternetConnection(mContext);

        PNConfiguration pnConfiguration = new PNConfiguration();
        pnConfiguration.setUuid((generalFunc.retrieveValue(Utils.DEVICE_SESSION_ID_KEY).equals("") ? generalFunc.getMemberId() : generalFunc.retrieveValue(Utils.DEVICE_SESSION_ID_KEY)));

        pnConfiguration.setSubscribeKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_SUB_KEY));
        pnConfiguration.setPublishKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_PUB_KEY));
        pnConfiguration.setSecretKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_SEC_KEY));
        pnConfiguration.setReconnectionPolicy(PNReconnectionPolicy.LINEAR);
//        pnConfiguration.setLogVerbosity(PNLogVerbosity.BODY);

        pubnub = new PubNub(pnConfiguration);

        addListener();
        getPassenegerMsgPubNubOff();
        subscribeToPrivateChannel();
        reConnectPubNub(10000);
        reConnectPubNub(20000);
        reConnectPubNub(30000);
    }

    public ConfigPubNub(Context mContext, boolean isOnlyPublish) {
        this.mContext = mContext;
        generalFunc = new GeneralFunctions(mContext);
        intCheck = new InternetConnection(mContext);

        PNConfiguration pnConfiguration = new PNConfiguration();
        pnConfiguration.setUuid(generalFunc.retrieveValue(Utils.DEVICE_SESSION_ID_KEY).equals("") ? generalFunc.getMemberId() : generalFunc.retrieveValue(Utils.DEVICE_SESSION_ID_KEY));
        pnConfiguration.setSubscribeKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_SUB_KEY));
        pnConfiguration.setPublishKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_PUB_KEY));
        pnConfiguration.setSecretKey(generalFunc.retrieveValue(CommonUtilities.PUBNUB_SEC_KEY));
        pnConfiguration.setReconnectionPolicy(PNReconnectionPolicy.LINEAR);
//        pnConfiguration.setLogVerbosity(PNLogVerbosity.BODY);


        pubnub = new PubNub(pnConfiguration);
        connectToPubNub(10000);
        connectToPubNub(20000);
        connectToPubNub(30000);
    }

    public void reConnectPubNub(int duration) {
        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                connectToPubNub();
            }
        }, duration);
    }

    public void getPassenegerMsgPubNubOff() {

        if (getLocUpdates != null) {
            getLocUpdates.stopLocationUpdates();
            getLocUpdates = null;
        }
        getLocUpdates = new GetLocationUpdates(mContext, Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, true, this);

        if (updatedriverStatustask == null) {

            updatedriverStatustask = new UpdateFrequentTask(generalFunc.parseIntegerValue(15, generalFunc.retrieveValue(Utils.FETCH_TRIP_STATUS_TIME_INTERVAL_KEY)) * 1000);
            updatedriverStatustask.setTaskRunListener(this);
            updatedriverStatustask.startRepeatingTask();
        }

    }

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

    public void setTripId(String iTripId, String PassengerId) {
        this.iTripId = iTripId;
        this.PassengerId = PassengerId;

    }

    public void subscribeToPrivateChannel() {
        pubnub.subscribe()
                .channels(Arrays.asList("DRIVER_" + generalFunc.getMemberId())) // subscribe to channels
                .execute();
    }

    public void unSubscribeToPrivateChannel() {
        pubnub.unsubscribe()
                .channels(Arrays.asList("DRIVER_" + generalFunc.getMemberId())) // subscribe to channels
                .execute();
    }

    public void releaseInstances() {

        isPubnubInstKilled = true;

        try {
            pubnub.removeListener(this);
            pubnub.forceDestroy();

            removeRunningInstance();
            Utils.runGC();
        } catch (Exception e) {

        }
    }

    private void removeRunningInstance() {
        try {

            if (updatedriverStatustask != null) {
                updatedriverStatustask.stopRepeatingTask();
                updatedriverStatustask = null;
            }


            if (getLocUpdates != null) {
                getLocUpdates.stopLocationUpdates();
                getLocUpdates = null;
            }
        } catch (Exception e) {

        }

    }

    public void addListener() {

        pubnub.removeListener(this);
        pubnub.addListener(this);

        pubnub.reconnect();

        connectToPubNub();

    }

    public boolean isJsonObj(String json) {

        try {
            JSONObject obj_check = new JSONObject(json);

            return true;
        } catch (Exception e) {
            return false;
        }
    }




    private void dispatchMsg(String jsonMsg) {


        String finalMsg = jsonMsg.replaceAll("^\"|\"$", "");

        if (!isJsonObj(finalMsg)) {
            finalMsg = jsonMsg.replaceAll("\\\\", "");

            finalMsg = finalMsg.replaceAll("^\"|\"$", "");

            if (!isJsonObj(finalMsg)) {
                finalMsg = jsonMsg.replace("\\\"", "\"").replaceAll("^\"|\"$", "");
            }

            finalMsg = finalMsg.replace("\\\\\"", "\\\"");
        }

        Utils.printLog("dispatchMsgAfter", "::" + jsonMsg);
        if (generalFunc.isTripStatusMsgExist(finalMsg)) {

            return;
        }


        Utils.printLog("finalMsg", "::" + finalMsg);

        String codeKey = CommonUtilities.DRIVER_REQ_CODE_PREFIX_KEY + generalFunc.getJsonValue("MsgCode", finalMsg);

        if (generalFunc.retrieveValue(codeKey).equals("") && !generalFunc.containsKey(CommonUtilities.DRIVER_REQ_COMPLETED_MSG_CODE_KEY + generalFunc.getJsonValue("MsgCode", finalMsg))) {
            Utils.sendBroadCast(mContext, CommonUtilities.passenger_message_arrived_intent_action, finalMsg);
        }

        Utils.sendBroadCast(mContext, CommonUtilities.passenger_message_arrived_intent_action_trip_msg, finalMsg);

    }



    public void subscribeToCabRequestChannel() {
        isSubsToCabReq = true;

        pubnub.subscribe()
                .channels(Arrays.asList("CAB_REQUEST_DRIVER_" + generalFunc.getMemberId())) // subscribe to channels
                .execute();
    }


    public void unSubscribeToCabRequestChannel() {
        isSubsToCabReq = false;

        pubnub.unsubscribe()
                .channels(Arrays.asList("CAB_REQUEST_DRIVER_" + generalFunc.getMemberId())) // subscribe to channels
                .execute();
    }


    public void publishMsg(String channel, String message) {
        if (message == null) {
            return;
        }
        if (!isCurrentMsgPublished) {

            String[] arr = {channel, message};
            listOfPublishMsg.add(arr);
            return;
        }

        continuePublish(channel, message);

    }

    private void continuePublish(String channel, String message) {
        isCurrentMsgPublished = false;
        pubnub.publish()
                .message(message)
                .channel(channel)
                .async(new PNCallback<PNPublishResult>() {
                    @Override
                    public void onResponse(PNPublishResult result, PNStatus status) {
                        isCurrentMsgPublished = true;

                        if (listOfPublishMsg.size() > 0) {
                            String[] arr = listOfPublishMsg.get(0);
                            listOfPublishMsg.remove(0);
                            continuePublish(arr[0], arr[1]);
                        }
                    }
                });
    }


    private void getUpdatedDriverStatus() {

        if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {
            return;
        }

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "configDriverTripStatus");
        parameters.put("iTripId", iTripId);
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("UserType", Utils.userType);

        if (driverLoc != null) {
            parameters.put("vLatitude", "" + driverLoc.getLatitude());
            parameters.put("vLongitude", "" + driverLoc.getLongitude());
        }

        parameters.put("isSubsToCabReq", "" + isSubsToCabReq);

        /*if (currentExeTask != null) {
            currentExeTask.cancel(true);
            currentExeTask = null;
        }*/

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
                    //  Utils.printLog("Api", "configDriverTripStatus ::" + responseString);

                    boolean isDataAvail = generalFunc.checkDataAvail(CommonUtilities.action_str, responseString);
                    if (generalFunc.getJsonValue(CommonUtilities.message_str, responseString) == null) {
                        return;
                    }

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

                                if (updatedriverStatustask != null) {
                                    updatedriverStatustask.stopRepeatingTask();
                                    updatedriverStatustask = null;
                                    releaseInstances();
                                }
                                generalFunc.notifySessionTimeOut();
                                return;
                            }

                        }
                    }


                    if (isDataAvail == true && isPubnubInstKilled == false) {

                        if (!iTripId.isEmpty()) {
                            Utils.printLog("dispatchMsg", "!iTripId.isEmpty()");
                            dispatchMsg(generalFunc.getJsonValue(CommonUtilities.message_str, responseString));

                        } else {

                            JSONArray msgArr = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

                            if (msgArr != null) {
                                for (int i = 0; i < msgArr.length(); i++) {

                                    String tempStr = ((String) generalFunc.getValueFromJsonArr(msgArr, i)).replaceAll("^\"|\"$", "");

                                    Utils.printLog("Orig", "::" + tempStr);
//                                    JSONObject obj_temp1 = generalFunc.getJsonObjectFromString(obj_temp.toString());

//                                    if (obj_temp1 != null) {
//                                        Utils.printLog("Api", "obj_temp::" + obj_temp1.toString().replace("[\"", "").replace("\"]", "").replace("\\\"", "\""));


                                    Utils.printLog("dispatchMsg", "else");
                                    dispatchMsg(tempStr);
//                                    }

                                }
                            }


                        }
                    }
                }
            }
        });
        exeWebServer.execute();
    }


    public String getMessageFromStringObj(String message) {
        String msg = message.toString().replace("\\\"", "\"");
        String finalMsg = "";
        if (isJsonObj(message.toString())) {
            finalMsg = message.toString();
        } else {
            finalMsg = msg.substring(1, msg.length() - 1);
        }

        return finalMsg;
    }

    @Override
    public void onLocationUpdate(Location location) {
        if (location == null) {
            return;
        }
        this.driverLoc = location;

    }

    @Override
    public void onTaskRun() {
        Utils.runGC();

        if (!isSessionout) {
            generalFunc.sendHeartBeat();


            if (mContext.getClass().getSimpleName().equals(MyBackGroundService.class.getSimpleName())) {
                if (MyApp.getInstance().isMyAppInBackGround() == true) {
                    getUpdatedDriverStatus();
                }
            } else {
                if (MyApp.getInstance().isMyAppInBackGround() == false) {
                    getUpdatedDriverStatus();
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

        Utils.printLog("dispatchMsg", "message");
        dispatchMsg(message.getMessage().toString());
    }

    @Override
    public void presence(PubNub pubnub, PNPresenceEventResult presence) {

    }
}



    /*SubscribeCallback subscribeCallback = new SubscribeCallback() {
        @Override
        public void status(final PubNub pubnub, final PNStatus status) {
            if (status == null || status.getOperation() == null) {
                connectTopubNub();
                return;
            }

            switch (status.getOperation()) {
                case PNSubscribeOperation:
                case PNUnsubscribeOperation:
                    switch (status.getCategory()) {
                        case PNConnectedCategory:
                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_Connected);
                            }
                        case PNReconnectedCategory:
                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_Connected);
                            }
                        case PNDisconnectedCategory:
                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_DisConnected);
                            }
                        case PNTimeoutCategory:

                            new Handler().postDelayed(new Runnable() {
                                @Override
                                public void run() {
                                    connectTopubNub();
                                }
                            }, 25000);

                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_DisConnected);
                            }

                        case PNUnexpectedDisconnectCategory:

                            new Handler().postDelayed(new Runnable() {
                                @Override
                                public void run() {
                                    connectTopubNub();
                                }
                            }, 25000);

                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_DisConnected);
                            }
                        case PNAccessDeniedCategory:

                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).pubNubStatus(Utils.pubNubStatus_Denied);
                            }
                        case PNDecryptionErrorCategory:
//                            Utils.printLog("Default", ":::PNDecryptionErrorCategory::" + status.toString());
                        default:

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
//                         Utils.printLog("PNHeartbeatOperation", ":::failed::" + status.toString());
                    } else {
                        // heartbeat operation was successful
//                         Utils.printLog("PNHeartbeatOperation", ":::success::" + status.toString());
                    }
                default: {
                    // Encountered unknown status type
//                    Utils.printLog("unknown status", ":::unknown::" + status.toString());
                }
            }
        }

        @Override
        public void message(PubNub pubnub, PNMessageResult message) {



            *//*String msg = message.getMessage().toString().replace("\\\"", "\"");
            String finalMsg = "";
            if (isJsonObj(message.getMessage().toString())) {
                finalMsg = message.getMessage().toString();
            } else {
                finalMsg = msg.substring(1, msg.length() - 1);
            }

            dispatchMsg(finalMsg);*//*
            dispatchMsg(message.getMessage().toString());

        }

        @Override
        public void presence(PubNub pubnub, PNPresenceEventResult presence) {
            // handle incoming presence data
//            Utils.printLog("ON presence", ":::got::" + presence.toString());
        }
    };*/