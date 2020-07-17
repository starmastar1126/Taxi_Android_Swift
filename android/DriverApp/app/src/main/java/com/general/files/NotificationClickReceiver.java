package com.general.files;

import android.app.NotificationManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;

import com.utils.CommonUtilities;
import com.utils.Utils;

import java.util.HashMap;

/**
 * Created by Admin on 17-02-2017.
 */
public class NotificationClickReceiver extends BroadcastReceiver {
    GeneralFunctions generalFunc;

    @Override
    public void onReceive(Context context, Intent intent) {
        generalFunc = new GeneralFunctions(context);

        Utils.printLog("Notification", "clicked");

        if (intent.getAction().equals(MyBackGroundService.KEEP_ONLINE_BTN)) {

        } else if (intent.getAction().equals(MyBackGroundService.GO_OFFLINE_BTN)) {
            goOffline(context);
        } else if (intent.getAction().equals(MyBackGroundService.OPEN_APP_BTN)) {

            if(Utils.getPreviousIntent(context) == null){
                Intent startIntent = context
                        .getPackageManager()
                        .getLaunchIntentForPackage(context.getPackageName());

                startIntent.setFlags(
                        Intent.FLAG_ACTIVITY_REORDER_TO_FRONT |
                                Intent.FLAG_ACTIVITY_NEW_TASK |
                                Intent.FLAG_ACTIVITY_RESET_TASK_IF_NEEDED
                );
                context.startActivity(startIntent);
            }else{
                context.startActivity(Utils.getPreviousIntent(context));
            }

        }
    }

    public void goOffline(final Context context) {
        Utils.printLog("Going","Offline");

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "updateDriverStatus");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("Status", "Not Available");

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(context,parameters);
        exeWebServer.setIsDeviceTokenGenerate(true, "vDeviceToken", generalFunc);

        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);
                    String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);

                    if (message.equals("SESSION_OUT")) {
                        generalFunc.notifySessionTimeOut();
                        Utils.runGC();
                        return;
                    }

                    if (isDataAvail == true) {


                        Utils.dismissBackGroundNotification(context);
                        generalFunc.storedata(CommonUtilities.DRIVER_ONLINE_KEY,"false");
                    }
                } else {

                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }
}
