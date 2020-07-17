package com.fastcabtaxi.passenger;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.location.Location;
import android.os.Bundle;
import android.os.Handler;
import android.provider.Settings;
import android.support.design.widget.Snackbar;
import android.support.v4.app.ActivityCompat;
import android.support.v7.app.ActionBar;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.view.WindowManager;
import android.widget.RelativeLayout;

import com.crashlytics.android.Crashlytics;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.GetLocationUpdates;
import com.general.files.InternetConnection;
import com.general.files.MyBackGroundService;
import com.general.files.OpenMainProfile;
import com.general.files.SetUserData;
import com.general.files.StartActProcess;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GoogleApiAvailability;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.google.android.gms.security.ProviderInstaller;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.anim.loader.AVLoadingIndicatorView;

import io.fabric.sdk.android.Fabric;
import java.util.Calendar;
import java.util.HashMap;

public class LauncherActivity extends AppCompatActivity implements GenerateAlertBox.HandleAlertBtnClick, ProviderInstaller.ProviderInstallListener {

    AVLoadingIndicatorView loaderView;
    InternetConnection intCheck;
    GenerateAlertBox generateAlert;
    GeneralFunctions generalFunc;

    GetLocationUpdates getLastLocation;

    String alertType = "";

    long autoLoginStartTime = 0;

    /*4.4 lower Device SSl CERTIFICATE ISSUE*/

    private static final int ERROR_DIALOG_REQUEST_CODE = 1;
    private boolean mRetryProviderInstall;
    RelativeLayout rlContentArea;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        Fabric.with(this, new Crashlytics());
        setContentView(R.layout.activity_launcher);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN);
//        ActionBar actionBar = getSupportActionBar();
//        actionBar.hide();
        generalFunc = new GeneralFunctions(getActContext());
        getLastLocation = new GetLocationUpdates(getActContext(), 2, false, null);

        intCheck = new InternetConnection(this);

        generalFunc.storedata("isInLauncher", "true");

        generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setBtnClickList(this);
        setDefaultAlertBtn();
        generateAlert.setCancelable(false);

        loaderView = (AVLoadingIndicatorView) findViewById(R.id.loaderView);
        rlContentArea = (RelativeLayout) findViewById(R.id.rlContentArea);

//        checkConfigurations(true);

        ProviderInstaller.installIfNeededAsync(this, this);


        new StartActProcess(getActContext()).startService(MyBackGroundService.class);
    }

    public void setDefaultAlertBtn() {
        generateAlert.resetBtn();
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Retry", "LBL_RETRY_TXT"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"));
    }

    public void checkConfigurations(boolean isPermissionShown) {

        int status = (GoogleApiAvailability.getInstance()).isGooglePlayServicesAvailable(getActContext());

        if (status == ConnectionResult.SERVICE_VERSION_UPDATE_REQUIRED) {
            showErrorOnPlayServiceDialog(generalFunc.retrieveLangLBl("This application requires updated google play service. " +
                    "Please install Or update it from play store", "LBL_UPDATE_PLAY_SERVICE_NOTE"));
            return;
        } else if (status != ConnectionResult.SUCCESS) {
            showErrorOnPlayServiceDialog(generalFunc.retrieveLangLBl("This application requires updated google play service. " +
                    "Please install Or update it from play store", "LBL_UPDATE_PLAY_SERVICE_NOTE"));
            return;
        }

        if (generalFunc.isAllPermissionGranted(isPermissionShown) == false) {
            showNoPermission();
            return;
        }
        if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {
            showNoInternetDialog();
        } else {
            Location mLastLocation = getLastLocation.getLastLocation();
            if (mLastLocation == null) {
                getLastLocation.startLocationUpdates(false);
            }
            continueProcess();

        }

    }

    public void continueProcess() {

        showLoader();

        Utils.setAppLocal(getActContext());

        if (generalFunc.isUserLoggedIn() == true) {
            autoLogin();
        } else {
            downloadGeneralData();
        }

    }

    public void restartappDailog() {
        GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());

        alertType = "";

        generateAlert.setContentMessage("",
                generalFunc.retrieveLangLBl("Please try again.", "LBL_TRY_AGAIN_TXT"));
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generalFunc.restartApp();
            }
        });

        generateAlert.showAlertBox();
    }


    public void downloadGeneralData() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "generalConfigData");
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("AppVersion", Utils.getAppVersion());
        parameters.put("vLang",generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY));
        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                if (isFinishing()) {
                    restartappDailog();
                    return;
                }

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {


                        generalFunc.storedata(CommonUtilities.FACEBOOK_APPID_KEY, generalFunc.getJsonValue("FACEBOOK_APP_ID", responseString));
                        generalFunc.storedata(CommonUtilities.LINK_FORGET_PASS_KEY, generalFunc.getJsonValue("LINK_FORGET_PASS_PAGE_PASSENGER", responseString));
                        generalFunc.storedata(CommonUtilities.APP_GCM_SENDER_ID_KEY, generalFunc.getJsonValue("GOOGLE_SENDER_ID", responseString));
                        generalFunc.storedata(CommonUtilities.MOBILE_VERIFICATION_ENABLE_KEY, generalFunc.getJsonValue("MOBILE_VERIFICATION_ENABLE", responseString));

                        generalFunc.storedata(CommonUtilities.CURRENCY_LIST_KEY, generalFunc.getJsonValue("LIST_CURRENCY", responseString));


                        generalFunc.storedata(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY, generalFunc.getJsonValue("vGMapLangCode", generalFunc.getJsonValue("DefaultLanguageValues", responseString)));
                        generalFunc.storedata(CommonUtilities.REFERRAL_SCHEME_ENABLE, generalFunc.getJsonValue("REFERRAL_SCHEME_ENABLE", responseString));
                        generalFunc.storedata(CommonUtilities.SITE_TYPE_KEY, generalFunc.getJsonValue("SITE_TYPE", responseString));


                      //  if (generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY).equalsIgnoreCase("")) {
                        generalFunc.storedata(CommonUtilities.languageLabelsKey, generalFunc.getJsonValue("LanguageLabels", responseString));
                        generalFunc.storedata(CommonUtilities.LANGUAGE_LIST_KEY, generalFunc.getJsonValue("LIST_LANGUAGES", responseString));
                        generalFunc.storedata(CommonUtilities.LANGUAGE_IS_RTL_KEY, generalFunc.getJsonValue("eType", generalFunc.getJsonValue("DefaultLanguageValues", responseString)));
                        generalFunc.storedata(CommonUtilities.LANGUAGE_CODE_KEY, generalFunc.getJsonValue("vCode", generalFunc.getJsonValue("DefaultLanguageValues", responseString)));
                        generalFunc.storedata(CommonUtilities.DEFAULT_LANGUAGE_VALUE, generalFunc.getJsonValue("vTitle", generalFunc.getJsonValue("DefaultLanguageValues", responseString)));
                        //  }

                        if (generalFunc.retrieveValue(CommonUtilities.DEFAULT_CURRENCY_VALUE).equalsIgnoreCase("")) {
                            generalFunc.storedata(CommonUtilities.DEFAULT_CURRENCY_VALUE, generalFunc.getJsonValue("vName", generalFunc.getJsonValue("DefaultCurrencyValues", responseString)));
                        }

                        generalFunc.storedata(CommonUtilities.FACEBOOK_LOGIN, generalFunc.getJsonValue("FACEBOOK_LOGIN", responseString));
                        generalFunc.storedata(CommonUtilities.GOOGLE_LOGIN, generalFunc.getJsonValue("GOOGLE_LOGIN", responseString));
                        generalFunc.storedata(CommonUtilities.TWITTER_LOGIN, generalFunc.getJsonValue("TWITTER_LOGIN", responseString));

                        generalFunc.storedata(CommonUtilities.DefaultCountry, generalFunc.getJsonValue("vDefaultCountry", responseString));
                        generalFunc.storedata(CommonUtilities.DefaultCountryCode, generalFunc.getJsonValue("vDefaultCountryCode", responseString));
                        generalFunc.storedata(CommonUtilities.DefaultPhoneCode, generalFunc.getJsonValue("vDefaultPhoneCode", responseString));
                        Utils.setAppLocal(getActContext());
                        closeLoader();


                        if (generalFunc.getJsonValue("SERVER_MAINTENANCE_ENABLE", responseString).equalsIgnoreCase("Yes")) {

                            new StartActProcess(getActContext()).startAct(MaintenanceActivity.class);
                            finish();
                            return;
                        }
                        new StartActProcess(getActContext()).startAct(AppLoginActivity.class);
                        try {
                            ActivityCompat.finishAffinity(LauncherActivity.this);
                        } catch (Exception e) {

                        }


                    } else {
                        if (!generalFunc.getJsonValue("isAppUpdate", responseString).trim().equals("")
                                && generalFunc.getJsonValue("isAppUpdate", responseString).equals("true")) {

                            showAppUpdateDialog(generalFunc.retrieveLangLBl("New update is available to download. " +
                                            "Downloading the latest update, you will get latest features, improvements and bug fixes.",
                                    generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        } else {
                            showError();
                        }

                    }
                } else {
                    showError();
                }

            }
        });
        exeWebServer.execute();
    }

    public void autoLogin() {
        autoLoginStartTime = Calendar.getInstance().getTimeInMillis();

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getDetail");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("vDeviceType", Utils.deviceType);
        parameters.put("AppVersion", Utils.getAppVersion());
        parameters.put("UserType", CommonUtilities.app_type);
        if (!generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY).equalsIgnoreCase("")) {
            parameters.put("vLang", generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY));
        }

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setIsDeviceTokenGenerate(true, "vDeviceToken", generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(final String responseString) {

                closeLoader();

                if (isFinishing()) {
                    return;
                }

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (generalFunc.getJsonValue("changeLangCode", responseString).equalsIgnoreCase("Yes")) {
                        //here to manage code
                        new SetUserData(responseString, generalFunc, getActContext(), false);
                    }


                    String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);


                    if (message.equals("SESSION_OUT")) {
                        autoLoginStartTime = 0;
                        generalFunc.notifySessionTimeOut();
                        Utils.runGC();
                        generalFunc.removeValue(CommonUtilities.LANGUAGE_CODE_KEY);
                        generalFunc.removeValue(CommonUtilities.DEFAULT_CURRENCY_VALUE);
                        return;
                    }

                    if (isDataAvail == true) {

                        if (generalFunc.getJsonValue("SERVER_MAINTENANCE_ENABLE", message).equalsIgnoreCase("Yes")) {
                            new StartActProcess(getActContext()).startAct(MaintenanceActivity.class);
                            finish();
                            return;
                        }

                        generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, message);

                        if (Calendar.getInstance().getTimeInMillis() - autoLoginStartTime < 2000) {
                            new Handler().postDelayed(new Runnable() {

                                @Override
                                public void run() {
                                    new OpenMainProfile(getActContext(),
                                            generalFunc.getJsonValue(CommonUtilities.message_str, responseString), true, generalFunc).startProcess();
                                }
                            }, 2000);
                        } else {
                            new OpenMainProfile(getActContext(),
                                    generalFunc.getJsonValue(CommonUtilities.message_str, responseString), true, generalFunc).startProcess();
                        }


                    } else {
                        autoLoginStartTime = 0;
                        if (!generalFunc.getJsonValue("isAppUpdate", responseString).trim().equals("")
                                && generalFunc.getJsonValue("isAppUpdate", responseString).equals("true")) {

                            showAppUpdateDialog(generalFunc.retrieveLangLBl("New update is available to download. " +
                                            "Downloading the latest update, you will get latest features, improvements and bug fixes.",
                                    generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        } else {

                            if (generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equalsIgnoreCase("LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER") ||
                                    generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equalsIgnoreCase("LBL_ACC_DELETE_TXT")) {

                                GenerateAlertBox alertBox = new GenerateAlertBox(getActContext());
                                alertBox.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                                alertBox.setCancelable(false);
                                alertBox.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
                                alertBox.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_CONTACT_US_TXT"));

                                alertBox.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                                    @Override
                                    public void handleBtnClick(int btn_id) {
                                        if (btn_id == 0) {
                                            new StartActProcess(getActContext()).startAct(ContactUsActivity.class);
                                            alertBox.showAlertBox();
                                        } else if (btn_id == 1) {
//                                            generalFunc.logoutFromDevice(getActContext(),generalFunc,"Launcher");
                                            generalFunc.logOutUser();
                                            generalFunc.restartApp();
                                        }
                                    }
                                });
                                alertBox.showAlertBox();
                                return;
                            }
                            showError("",
                                    generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        }
                    }
                } else {
                    autoLoginStartTime = 0;
                    showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void showLoader() {
        loaderView.setVisibility(View.VISIBLE);
    }

    public void closeLoader() {
        loaderView.setVisibility(View.GONE);
    }

    public void showError() {

        generateAlert.closeAlertBox();

        alertType = "ERROR";
        setDefaultAlertBtn();
        generateAlert.setContentMessage("",
                generalFunc.retrieveLangLBl("Please try again.", "LBL_TRY_AGAIN_TXT"));

        generateAlert.showAlertBox();
    }

    public void showError(String title, String contentMsg) {

        generateAlert.closeAlertBox();

        alertType = "ERROR";
        setDefaultAlertBtn();
        generateAlert.setContentMessage(title,
                contentMsg);

        generateAlert.showAlertBox();
    }

    public void showNoInternetDialog() {

        generateAlert.closeAlertBox();

        alertType = "NO_INTERNET";
        setDefaultAlertBtn();
        generateAlert.setContentMessage("",
                generalFunc.retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT"));

        generateAlert.showAlertBox();

    }


    public void showNoPermission() {
        generateAlert.closeAlertBox();

        alertType = "NO_PERMISSION";
        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("Application requires some permission to be granted to work. Please allow it.",
                "LBL_ALLOW_PERMISSIONS_APP"));

        generateAlert.resetBtn();
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Allow All", "LBL_ALLOW_ALL_TXT"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"));
        generateAlert.showAlertBox();

    }

    public void showErrorOnPlayServiceDialog(String content) {

        generateAlert.closeAlertBox();

        alertType = "NO_PLAY_SERVICE";
        generateAlert.setContentMessage("", content);

        generateAlert.resetBtn();
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Update", "LBL_UPDATE"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("Retry", "LBL_RETRY_TXT"));
        generateAlert.showAlertBox();

    }

    public void showAppUpdateDialog(String content) {
        generateAlert.closeAlertBox();

        alertType = "APP_UPDATE";
        generateAlert.setContentMessage(generalFunc.retrieveLangLBl("New update available", "LBL_NEW_UPDATE_AVAIL"), content);

        generateAlert.resetBtn();
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Update", "LBL_UPDATE"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("Retry", "LBL_RETRY_TXT"));
        generateAlert.showAlertBox();

    }


    public Context getActContext() {
        return LauncherActivity.this;
    }

    @Override
    public void handleBtnClick(int btn_id) {
        Utils.hideKeyboard(getActContext());
        if (btn_id == 0) {
            generateAlert.closeAlertBox();

            if (!alertType.equals("NO_PLAY_SERVICE") && !alertType.equals("APP_UPDATE")) {
                finish();
            } else {
                checkConfigurations(false);
            }


        } else {
            if (alertType.equals("NO_PLAY_SERVICE")) {

                boolean isSuccessfulOpen = new StartActProcess(getActContext()).openURL("market://details?id=com.google.android.gms");
                if (isSuccessfulOpen == false) {
                    new StartActProcess(getActContext()).openURL("http://play.google.com/store/apps/details?id=com.google.android.gms");
                }

                generateAlert.closeAlertBox();
                checkConfigurations(false);

            } else if (alertType.equals("NO_PERMISSION")) {
//            generalFunc.openSettings();
                if (ActivityCompat.shouldShowRequestPermissionRationale(this, android.Manifest.permission.ACCESS_FINE_LOCATION) == false ||
                        ActivityCompat.shouldShowRequestPermissionRationale(this, android.Manifest.permission.ACCESS_COARSE_LOCATION) == false ||
                        ActivityCompat.shouldShowRequestPermissionRationale(this, android.Manifest.permission.WRITE_EXTERNAL_STORAGE) == false ||
                        ActivityCompat.shouldShowRequestPermissionRationale(this, android.Manifest.permission.CAMERA) == false) {

                    generalFunc.openSettings();
                    generateAlert.closeAlertBox();
                } else if (generalFunc.isAllPermissionGranted(false) == false) {
                    generalFunc.isAllPermissionGranted(true);
                    generateAlert.closeAlertBox();
                    checkConfigurations(false);
                } else {
                    generateAlert.closeAlertBox();
                    checkConfigurations(true);
                }

            } else if (alertType.equals("APP_UPDATE")) {

                boolean isSuccessfulOpen = new StartActProcess(getActContext()).openURL("market://details?id=" + CommonUtilities.package_name);
                if (isSuccessfulOpen == false) {
                    new StartActProcess(getActContext()).openURL("http://play.google.com/store/apps/details?id=" + CommonUtilities.package_name);
                }

                generateAlert.closeAlertBox();
                checkConfigurations(false);

            } else if (!alertType.equals("NO_GPS")) {
                generateAlert.closeAlertBox();
                checkConfigurations(false);


            } else {
                new StartActProcess(getActContext()).
                        startActForResult(Settings.ACTION_LOCATION_SOURCE_SETTINGS, Utils.REQUEST_CODE_GPS_ON);
            }

        }
    }

//    @Override
//    public void onLastLocationUpdate(Location mLastLocation) {
//
//        this.mLastLocation = mLastLocation;
//        checkConfigurations(false);
//    }

    @Override
    public void onResume() {
        // TODO Auto-generated method stub
        super.onResume();
    }

    @Override
    protected void onPause() {
        // TODO Auto-generated method stub
        super.onPause();
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        generateAlert.closeAlertBox();
        switch (requestCode) {
            case Utils.REQUEST_CODE_GPS_ON:

                checkConfigurations(false);

                break;
            case GeneralFunctions.MY_SETTINGS_REQUEST:
                generateAlert.closeAlertBox();
                checkConfigurations(false);

                break;
            // SSL Certificate issue

            case ERROR_DIALOG_REQUEST_CODE:
                // Adding a fragment via GooglePlayServicesUtil.showErrorDialogFragment
                // before the instance state is restored throws an error. So instead,
                // set a flag here, which will cause the fragment to delay until
                // onPostResume.
                mRetryProviderInstall = true;
                break;

        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode,
                                           String permissions[], int[] grantResults) {
        switch (requestCode) {
            case GeneralFunctions.MY_PERMISSIONS_REQUEST: {

                generateAlert.closeAlertBox();

                checkConfigurations(false);

                return;
            }
        }
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        generalFunc.storedata("isInLauncher", "false");
        if (getLastLocation != null) {
            getLastLocation.stopLocationUpdates();
        }
    }

    @Override
    public void onProviderInstalled() {
        checkConfigurations(true);
    }

    @Override
    public void onProviderInstallFailed(int errorCode, Intent intent) {
        if (GooglePlayServicesUtil.isUserRecoverableError(errorCode)) {
            // Recoverable error. Show a dialog prompting the user to
            // install/update/enable Google Play services.
            GooglePlayServicesUtil.showErrorDialogFragment(
                    errorCode,
                    this,
                    ERROR_DIALOG_REQUEST_CODE,
                    new DialogInterface.OnCancelListener() {
                        @Override
                        public void onCancel(DialogInterface dialog) {
                            // The user chose not to take the recovery action
                            onProviderInstallerNotAvailable();
                        }
                    });
        } else {
            // Google Play services is not available.
            onProviderInstallerNotAvailable();
        }
    }

    private void onProviderInstallerNotAvailable() {
        // This is reached if the provider cannot be updated for some reason.
        // App should consider all HTTP communication to be vulnerable, and take
        // appropriate action.
        checkConfigurations(true);
        showMessageWithAction(rlContentArea, generalFunc.retrieveLangLBl("provider cannot be updated for some reason.", "LBL_PROVIDER_NOT_AVALIABLE_TXT"));
    }

    @Override
    protected void onPostResume() {
        super.onPostResume();

        if (mRetryProviderInstall) {
            // We can now safely retry installation.
            ProviderInstaller.installIfNeededAsync(this, this);
        }
        mRetryProviderInstall = false;
    }

    public void showMessageWithAction(View view, String message) {
        Snackbar snackbar = Snackbar.make(view, message, Snackbar.LENGTH_INDEFINITE);
        snackbar.setDuration(10000);
        snackbar.show();
    }

}
