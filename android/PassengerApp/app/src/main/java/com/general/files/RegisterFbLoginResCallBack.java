package com.general.files;

import android.content.Context;
import android.os.Bundle;

import com.fastcabtaxi.passenger.AppLoignRegisterActivity;
import com.facebook.CallbackManager;
import com.facebook.FacebookCallback;
import com.facebook.FacebookException;
import com.facebook.GraphRequest;
import com.facebook.GraphResponse;
import com.facebook.login.LoginResult;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MyProgressDialog;

import org.json.JSONObject;

import java.net.URL;
import java.util.HashMap;

/**
 * Created by Admin on 29-06-2016.
 */
public class RegisterFbLoginResCallBack implements FacebookCallback<LoginResult> {
    private final InternetConnection intCheck;
    Context mContext;
    GeneralFunctions generalFunc;
    MyProgressDialog myPDialog;
    AppLoignRegisterActivity appLoginAct;
    private CallbackManager callbackManager;

    public RegisterFbLoginResCallBack(Context mContext, CallbackManager callbackManager) {
        this.mContext = mContext;

        generalFunc = new GeneralFunctions(mContext);
        this.callbackManager = callbackManager;
        appLoginAct = (AppLoignRegisterActivity) mContext;
        intCheck = new InternetConnection(mContext);
    }

    @Override
    public void onSuccess(LoginResult loginResult) {

        Utils.printLog("Response", "onSuccess:");
        if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {
            closeDialog();
            return;
        }

        myPDialog = new MyProgressDialog(mContext, false, generalFunc.retrieveLangLBl("", "LBL_LOADING_TXT"));
        myPDialog.show();

        GraphRequest request = GraphRequest.newMeRequest(
                loginResult.getAccessToken(),
                new GraphRequest.GraphJSONObjectCallback() {
                    @Override
                    public void onCompleted(
                            JSONObject me,
                            GraphResponse response) {
                        // Application code
                        myPDialog.close();
                        if (response.getError() != null) {
                            // handle error
                            Utils.printLog("onError", "onError:" + response.getError());

                            generalFunc.showGeneralMessage(generalFunc.retrieveLangLBl("", "LBL_ERROR"), generalFunc.retrieveLangLBl("", "LBL_TRY_AGAIN"));
                        } else {
                            try {

                                String email_str = generalFunc.getJsonValue("email", me.toString());
                                String name_str = generalFunc.getJsonValue("name", me.toString());
                                String first_name_str = generalFunc.getJsonValue("first_name", me.toString());
                                String last_name_str = generalFunc.getJsonValue("last_name", me.toString());
                                String fb_id_str = generalFunc.getJsonValue("id", me.toString());

                                // URL imageURL = "https://graph.facebook.com/" + fb_id_str + "/picture?type=large";
                                URL imageURL = new URL("https://graph.facebook.com/" + fb_id_str + "/picture?type=large");

                                registerFbUser(email_str, first_name_str, last_name_str, fb_id_str, imageURL + "");

                                generalFunc.logOUTFrmFB();
                            } catch (Exception e) {

                            }
                        }
                    }


                });
        Bundle parameters = new Bundle();
        parameters.putString("fields", "id,name,first_name,last_name,email");
        request.setParameters(parameters);
        request.executeAsync();

        if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {
            closeDialog();
        }

    }

    @Override
    public void onCancel() {
        Utils.printLog("Response", "onCancel::");
        closeDialog();
    }

    @Override
    public void onError(FacebookException error) {
        Utils.printLog("Response", " error ::" + error);
        closeDialog();
    }


    public void registerFbUser(final String email, final String fName, final String lName, final String fbId, final String imageURL) {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "LoginWithFB");
        parameters.put("vFirstName", fName);
        parameters.put("vLastName", lName);
        parameters.put("vEmail", email);
        parameters.put("iFBId", fbId);
        parameters.put("vDeviceType", Utils.deviceType);
        parameters.put("UserType", Utils.userType);
        parameters.put("vCurrency", generalFunc.retrieveValue(CommonUtilities.DEFAULT_CURRENCY_VALUE));
        parameters.put("vLang", generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY));
        parameters.put("vImageURL", imageURL);
        parameters.put("eLoginType", "Facebook");


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        exeWebServer.setLoaderConfig(mContext, true, generalFunc);
        exeWebServer.setIsDeviceTokenGenerate(true, "vDeviceToken", generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        new SetUserData(responseString, generalFunc, mContext, true);
                        generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        new OpenMainProfile(mContext,
                                generalFunc.getJsonValue(CommonUtilities.message_str, responseString), false, generalFunc).startProcess();
                    } else {
                        if (!generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_REGISTER")) {
                            generalFunc.showGeneralMessage("",
                                    generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        } else {

                            signupUser(email, fName, lName, fbId, imageURL);

                        }

                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void signupUser(final String email, final String fName, final String lName, final String fbId, String imageURL) {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "signup");
        parameters.put("vFirstName", fName);
        parameters.put("vLastName", lName);
        parameters.put("vEmail", email);
        parameters.put("vFbId", fbId);
        parameters.put("vDeviceType", Utils.deviceType);
        parameters.put("UserType", Utils.userType);
        parameters.put("vCurrency", generalFunc.retrieveValue(CommonUtilities.DEFAULT_CURRENCY_VALUE));
        parameters.put("vLang", generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY));
        parameters.put("eSignUpType", "Facebook");
        parameters.put("vImageURL", imageURL);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        exeWebServer.setIsDeviceTokenGenerate(true, "vDeviceToken", generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        new SetUserData(responseString, generalFunc, mContext, true);
                        generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        new OpenMainProfile(mContext,
                                generalFunc.getJsonValue(CommonUtilities.message_str, responseString), false, generalFunc).startProcess();
                    } else {


                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }


    public void closeDialog() {
        if (myPDialog != null) {
            myPDialog.close();
        }
    }
}
