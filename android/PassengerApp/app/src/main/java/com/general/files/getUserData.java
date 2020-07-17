package com.general.files;

import android.app.Activity;
import android.content.Context;
import android.os.Handler;
import android.support.v4.app.ActivityCompat;

import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;

import java.util.HashMap;

/**
 * Created by Admin on 19-06-2017.
 */

public class getUserData {

    GeneralFunctions generalFunc;
    Context mContext;

    public getUserData(GeneralFunctions generalFunc, Context mContext) {
        this.generalFunc = generalFunc;
        this.mContext = mContext;

    }


    public void getData() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getDetail");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("vDeviceType", Utils.deviceType);
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("AppVersion", Utils.getAppVersion());


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        exeWebServer.setLoaderConfig(mContext, true, generalFunc);
        exeWebServer.setIsDeviceTokenGenerate(true, "vDeviceToken", generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(final String responseString) {


                if (responseString != null && !responseString.equals("")) {


                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);

                    if (Utils.checkText(responseString) && message.equals("SESSION_OUT")) {
                        if (ConfigPubNub.getInstance(true) != null) {
                            ConfigPubNub.getInstance().releaseInstances();
                        }
                        generalFunc.notifySessionTimeOut();
                        Utils.runGC();
                        generalFunc.removeValue(CommonUtilities.LANGUAGE_CODE_KEY);
                        generalFunc.removeValue(CommonUtilities.DEFAULT_CURRENCY_VALUE);
                        return;
                    }

                    if (isDataAvail == true) {
                        if (ConfigPubNub.getInstance(true) != null) {
                            ConfigPubNub.getInstance().releaseInstances();
                        }

                        generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        new OpenMainProfile(mContext,
                                generalFunc.getJsonValue(CommonUtilities.message_str, responseString), true, generalFunc).startProcess();
                        Handler handler = new Handler();
                        handler.postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                try {
                                    ActivityCompat.finishAffinity((Activity) mContext);
                                    Utils.runGC();

                                } catch (Exception e) {
                                }
                            }
                        }, 300);


                    } else {
                        if (!generalFunc.getJsonValue("isAppUpdate", responseString).trim().equals("")
                                && generalFunc.getJsonValue("isAppUpdate", responseString).equals("true")) {

                        } else {

                            if (generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equalsIgnoreCase("LBL_CONTACT_US_STATUS_NOTACTIVE_COMPANY") ||
                                    generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equalsIgnoreCase("LBL_ACC_DELETE_TXT") ||
                                    generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equalsIgnoreCase("LBL_CONTACT_US_STATUS_NOTACTIVE_DRIVER")) {

                                GenerateAlertBox alertBox = generalFunc.notifyRestartApp("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                                alertBox.setCancelable(false);
                                alertBox.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                                    @Override
                                    public void handleBtnClick(int btn_id) {

                                        if (btn_id == 1) {
//                                            generalFunc.logoutFromDevice(mContext,"getUserData",generalFunc);
                                            generalFunc.logOutUser();
                                            generalFunc.restartApp();
                                        }
                                    }
                                });
                                return;
                            }

                        }
                    }
                } else {
                }
            }
        });
        exeWebServer.execute();
    }
}
