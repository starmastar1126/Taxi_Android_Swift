package com.general.files;

import android.Manifest;
import android.app.Activity;
import android.app.ActivityManager;
import android.app.AppOpsManager;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.Signature;
import android.graphics.Bitmap;
import android.graphics.Matrix;
import android.graphics.Typeface;
import android.location.Location;
import android.location.LocationManager;
import android.media.ExifInterface;
import android.net.Uri;
import android.os.Binder;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.preference.PreferenceManager;
import android.provider.Settings;
import android.support.design.widget.Snackbar;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.Fragment;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AlertDialog;
import android.text.TextUtils;
import android.util.Base64;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ImageView;

import com.fastcabtaxi.driver.ContactUsActivity;
import com.fastcabtaxi.driver.LauncherActivity;
import com.fastcabtaxi.driver.MyWalletActivity;
import com.fastcabtaxi.driver.R;
import com.fastcabtaxi.driver.VerifyInfoActivity;
import com.drawRoute.DirectionsJSONParser;
import com.facebook.login.LoginManager;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GoogleApiAvailability;
import com.google.android.gms.iid.InstanceID;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.PolylineOptions;
import com.google.firebase.iid.FirebaseInstanceId;
import com.utils.CommonUtilities;
import com.utils.ScalingUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.GenerateAlertBox;
import com.view.MTextView;
import com.view.SelectableRoundedImageView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.lang.reflect.Method;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.text.DecimalFormat;
import java.text.DecimalFormatSymbols;
import java.text.NumberFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.TimeZone;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Created by Admin on 27-06-2016.
 */
public class GeneralFunctions {
    Context mContext;
    public static final int MY_PERMISSIONS_REQUEST = 51;
    public static final int MY_SETTINGS_REQUEST = 52;
    IntentFilter mIntentFilter;
    AlertDialog cashBalAlertDialog;
    String languageLabels_str = "";
    Map<String, Object> languageData = null;
    GenerateAlertBox generateSessionAlert;

    public GeneralFunctions(Context context) {
        this.mContext = context;
        checkForRTL();
    }

    public double round(double value, int places) {
        if (places < 0) throw new IllegalArgumentException();

        long factor = (long) Math.pow(10, places);
        value = value * factor;
        long tmp = Math.round(value);
        return (double) tmp / factor;
    }


    public String getTimezone() {
        TimeZone tz = TimeZone.getDefault();
        return tz.getID() + "";
    }

    public String wrapHtml(Context context, String html) {
        return context.getString(isRTLmode() ? R.string.html_rtl : R.string.html, html);
    }
//    public void setAppLocal() {
//        String languageToLoad = "ar";
//        Locale locale = new Locale(languageToLoad);
//        Locale.setDefault(locale);
//        Configuration config = new Configuration();
//        config.locale = locale;
//        this.mContext.getResources().updateConfiguration(config,
//                this.mContext.getResources().getDisplayMetrics());
//    }


    public boolean isReferralSchemeEnable() {
        if (!retrieveValue(CommonUtilities.REFERRAL_SCHEME_ENABLE).equals("") && retrieveValue(CommonUtilities.REFERRAL_SCHEME_ENABLE).equalsIgnoreCase("Yes")) {
            return true;
        }
        return false;
    }

    public boolean isRTLmode() {
        if (!retrieveValue(CommonUtilities.LANGUAGE_IS_RTL_KEY).equals("") && retrieveValue(CommonUtilities.LANGUAGE_IS_RTL_KEY).equals(CommonUtilities.DATABASE_RTL_STR)) {
            return true;
        }
        return false;
    }

    public String retrieveValue(String key) {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        String value_str = mPrefs.getString(key, "");

        return value_str;
    }

    public void logOUTFrmFB() {
        LoginManager.getInstance().logOut();
    }

    public static Bitmap rotateBitmap(Bitmap bitmap, int orientation) {

        Matrix matrix = new Matrix();
        switch (orientation) {
            case ExifInterface.ORIENTATION_NORMAL:
                return bitmap;
            case ExifInterface.ORIENTATION_FLIP_HORIZONTAL:
                matrix.setScale(-1, 1);
                break;
            case ExifInterface.ORIENTATION_ROTATE_180:
                matrix.setRotate(180);
                break;
            case ExifInterface.ORIENTATION_FLIP_VERTICAL:
                matrix.setRotate(180);
                matrix.postScale(-1, 1);
                break;
            case ExifInterface.ORIENTATION_TRANSPOSE:
                matrix.setRotate(90);
                matrix.postScale(-1, 1);
                break;
            case ExifInterface.ORIENTATION_ROTATE_90:
                matrix.setRotate(90);
                break;
            case ExifInterface.ORIENTATION_TRANSVERSE:
                matrix.setRotate(-90);
                matrix.postScale(-1, 1);
                break;
            case ExifInterface.ORIENTATION_ROTATE_270:
                matrix.setRotate(-90);
                break;
            default:
                return bitmap;
        }
        try {
            Bitmap bmRotated = Bitmap.createBitmap(bitmap, 0, 0, bitmap.getWidth(), bitmap.getHeight(), matrix, true);
            bitmap.recycle();
            return bmRotated;
        } catch (OutOfMemoryError e) {
            e.printStackTrace();
            return bitmap;
        }
    }

    public void checkForRTL() {

        if (mContext instanceof Activity) {
            if (!retrieveValue(CommonUtilities.LANGUAGE_IS_RTL_KEY).equals("") && retrieveValue(CommonUtilities.LANGUAGE_IS_RTL_KEY).equals(CommonUtilities.DATABASE_RTL_STR)) {
                forceRTLIfSupported((Activity) mContext);
            } else {
                forceLTRIfSupported((Activity) mContext);
                Utils.printLog("checkForRTL", "call000" + " " + retrieveValue(CommonUtilities.LANGUAGE_IS_RTL_KEY));
            }

        }
    }

    public void forceRTLIfSupported(Activity act) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
            act.getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        }
    }

    public void forceLTRIfSupported(Activity act) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
            act.getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_LTR);
        }
    }

    public void forceRTLIfSupported(android.support.v7.app.AlertDialog alertDialog) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
            alertDialog.getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        }
    }

    public void forceRTLIfSupported(Dialog alertDialog) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
            alertDialog.getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        }
    }

    public void forceLTRIfSupported(Dialog alertDialog) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
            alertDialog.getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_LTR);
        }
    }

    public JSONObject getJsonObject(String data) {
        try {
            JSONObject obj_temp = new JSONObject(data);

            return obj_temp;

        } catch (JSONException e) {
            e.printStackTrace();

            return null;
        }

    }

    public JSONObject getJsonObject(String key, JSONObject obj) {
        try {
            JSONObject obj_temp = obj.getJSONObject(key);

            return obj_temp;

        } catch (JSONException e) {
//            e.printStackTrace();

            return null;
        }

    }


    public String retrieveLangLBl(String orig, String label) {

        if (isLanguageLabelsAvail() == true) {
            if (languageLabels_str.equals("")) {
                SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
                String languageLabels_str = mPrefs.getString(CommonUtilities.languageLabelsKey, "");
                this.languageLabels_str = languageLabels_str;
            }

            if (languageData == null && !languageLabels_str.equals("")) {
                JSONObject object = getJsonObject(languageLabels_str);
                Map<String, Object> map = new HashMap<String, Object>();

                Iterator<String> keysItr = object.keys();
                while (keysItr.hasNext()) {
                    String key = keysItr.next();
                    Object value = getJsonValue(key, object);

                    if (value instanceof JSONArray) {
                        value = toList((JSONArray) value);
                    } else if (value instanceof JSONObject) {
                        value = toMap((JSONObject) value);
                    }
                    map.put(key, value);
                }

                this.languageData = map;
            }


            if (languageData != null) {
                if (languageData.get(label) != null) {
                    return ((String) languageData.get(label));
                } else {

                    return (orig.equals("") ? (label.startsWith("LBL_") ? orig : label) : orig);
                }
            }
            if (getJsonValue(label, languageLabels_str).equals("")) {
                return (orig.equals("") ? (label.startsWith("LBL_") ? orig : label) : orig);
            }

            return getJsonValue(label, languageLabels_str);
        }

        return (orig.equals("") ? (label.startsWith("LBL_") ? orig : label) : orig);
    }

    public List<Object> toList(JSONArray array) {
        List<Object> list = new ArrayList<Object>();
        for (int i = 0; i < array.length(); i++) {
            Object value = null;
            try {
                value = array.get(i);
            } catch (JSONException e) {
                e.printStackTrace();
            }
            if (value != null) {
                if (value instanceof JSONArray) {
                    value = toList((JSONArray) value);
                } else if (value instanceof JSONObject) {
                    value = toMap((JSONObject) value);
                }
                list.add(value);
            }
        }
        return list;
    }

    public Map<String, Object> toMap(JSONObject object) {
        Map<String, Object> map = new HashMap<String, Object>();

        Iterator<String> keysItr = object.keys();
        while (keysItr.hasNext()) {
            String key = keysItr.next();
            Object value = getJsonValue(key, object);

            if (value instanceof JSONArray) {
                value = toList((JSONArray) value);
            } else if (value instanceof JSONObject) {
                value = toMap((JSONObject) value);
            }
            map.put(key, value);
        }
        return map;
    }

    public Object getJsonValue(String key, JSONObject response) {

        try {
            if (response != null) {
                Object value_str = response.get(key);

                if (value_str != null && !value_str.equals("null") && !value_str.equals("")) {
                    return value_str;
                }
            }

        } catch (JSONException e) {
            e.printStackTrace();

            return "";
        }

        return "";
    }


    public String getJsonValueStr(String key, JSONObject response) {

        try {

            if (response != null) {
                String value_str = "";
                if (response.has(key)) {
                    value_str = response.getString(key);
                }

                if (value_str != null && !value_str.equals("null") && !value_str.equals("")) {
                    return value_str;
                }
            }

        } catch (JSONException e) {
//            e.printStackTrace();

            return "";
        }

        return "";
    }

    public String generateDeviceToken() {
        if (checkPlayServices() == false) {
            return "";
        }
        InstanceID instanceID = InstanceID.getInstance(mContext);
        String GCMregistrationId = "";
        try {
//            GCMregistrationId = instanceID.getToken(retrieveValue(CommonUtilities.APP_GCM_SENDER_ID_KEY), GoogleCloudMessaging.INSTANCE_ID_SCOPE,
//                    null);
            GCMregistrationId = FirebaseInstanceId.getInstance().getToken(retrieveValue(CommonUtilities.APP_GCM_SENDER_ID_KEY), "FCM");
        } catch (IOException e) {
            e.printStackTrace();
            GCMregistrationId = "";
        }

        return GCMregistrationId;
    }

    public boolean checkPlayServices() {
        final GoogleApiAvailability googleAPI = GoogleApiAvailability.getInstance();
        final int result = googleAPI.isGooglePlayServicesAvailable(mContext);
        if (result != ConnectionResult.SUCCESS) {
            if (googleAPI.isUserResolvableError(result)) {

                ((Activity) mContext).runOnUiThread(new Runnable() {
                    public void run() {
                        googleAPI.getErrorDialog(((Activity) mContext), result,
                                Utils.PLAY_SERVICES_RESOLUTION_REQUEST).show();
                    }
                });

            }

            return false;
        }

        return true;
    }

    public static boolean checkDataAvail(String key, String response) {
        try {
            JSONObject obj_temp = new JSONObject(response);

            String action_str = obj_temp.getString(key);

            if (!action_str.equals("") && !action_str.equals("0") && action_str.equals("1")) {
                return true;
            }

        } catch (JSONException e) {
            e.printStackTrace();

            return false;
        }

        return false;
    }

    public void removeValue(String key) {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        SharedPreferences.Editor editor = mPrefs.edit();
        editor.remove(key);
        editor.commit();
    }

    public boolean containsKey(String key) {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        SharedPreferences.Editor editor = mPrefs.edit();
        String strPref = mPrefs.getString(key, null);

        if (strPref != null) {
            return true;
        } else {
            return false;
        }

    }

    public void storedata(String key, String data) {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        SharedPreferences.Editor editor = mPrefs.edit();
        editor.putString(key, data);
        editor.commit();
    }

    public void storeUserData(String memberId) {
        storedata(CommonUtilities.iMemberId_KEY, memberId);
        storedata(CommonUtilities.isUserLogIn, "1");
    }


    public String addSemiColonToPrice(String value) {
        return NumberFormat.getNumberInstance(Locale.US).format(parseIntegerValue(0, value));
    }


    public String getMemberId() {
        if (isUserLoggedIn() == true) {
            return retrieveValue(CommonUtilities.iMemberId_KEY);
        } else {
            return "";
        }
    }

    public void logOutUser() {
        removeValue(CommonUtilities.iMemberId_KEY);
        removeValue(CommonUtilities.isUserLogIn);
        // removeValue(CommonUtilities.languageLabelsKey);
        //removeValue(CommonUtilities.LANGUAGE_CODE_KEY);
        removeValue(CommonUtilities.DEFAULT_CURRENCY_VALUE);
        removeValue(CommonUtilities.USER_PROFILE_JSON);
        removeValue(CommonUtilities.WORKLOCATION);
    }

    public boolean isUserLoggedIn() {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        String isUserLoggedIn_str = mPrefs.getString(CommonUtilities.isUserLogIn, "");

        if (!isUserLoggedIn_str.equals("") && isUserLoggedIn_str.equals("1")) {
            return true;
        }

        return false;
    }

    public Object getValueFromJsonArr(JSONArray arr, int position) {
        try {

            return arr.get(position);

        } catch (JSONException e) {
            e.printStackTrace();

            return "";
        }

    }

    public String getJsonValue(String key, String response) {

        try {
            if (response != null) {
                JSONObject obj_temp = new JSONObject(response);

                if (!obj_temp.isNull(key)) {

                    String value_str = obj_temp.getString(key);

                    if (value_str != null && !value_str.equals("null") && !value_str.equals("")) {
                        return value_str;
                    }
                }
            }

        } catch (JSONException e) {
            e.printStackTrace();

            return "";
        }

        return "";
    }


    public boolean isLanguageLabelsAvail() {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        String languageLabels_str = mPrefs.getString(CommonUtilities.languageLabelsKey, null);

        if (languageLabels_str != null && !languageLabels_str.equals("")) {
            return true;
        }

        return false;
    }

    public JSONArray getJsonArray(String key, String response) {
        try {
            JSONObject obj_temp = new JSONObject(response);
            JSONArray obj_temp_arr = obj_temp.getJSONArray(key);

            return obj_temp_arr;

        } catch (JSONException e) {
//            e.printStackTrace();

            return null;
        }

    }

    public JSONArray getJsonArray(String response) {
        try {
            JSONArray obj_temp_arr = new JSONArray(response);

            return obj_temp_arr;

        } catch (JSONException e) {
//            e.printStackTrace();

            return null;
        }

    }

    public JSONObject getJsonObject(JSONArray arr, int position) {
        try {
            JSONObject obj_temp = arr.getJSONObject(position);

            return obj_temp;

        } catch (JSONException e) {
//            e.printStackTrace();

            return null;
        }

    }

    public boolean isJSONkeyAvail(String key, String response) {
        try {
            JSONObject json_obj = new JSONObject(response);

            if (json_obj.has(key) && !json_obj.isNull(key)) {
                return true;
            }
        } catch (JSONException e) {
//            e.printStackTrace();
            return false;
        }
        return false;
    }

    public JSONObject getJsonObject(String key, String response) {

        try {
            JSONObject obj_temp = new JSONObject(response);

            JSONObject value_str = obj_temp.getJSONObject(key);

            if (value_str != null && !value_str.equals("null") && !value_str.equals("")) {
                return value_str;
            }

        } catch (JSONException e) {
//            e.printStackTrace();

            return null;
        }

        return null;
    }


    public boolean isJSONArrKeyAvail(String key, String response) {
        try {
            JSONObject json_obj = new JSONObject(response);

            if (json_obj.optJSONArray(key) != null) {
                return true;
            }
        } catch (JSONException e) {
//            e.printStackTrace();
            return false;
        }
        return false;
    }

    public static Float parseFloatValue(float defaultValue, String strValue) {

        try {
            float value = Float.parseFloat(strValue);
            return value;
        } catch (Exception e) {
            return defaultValue;
        }
    }

    public static Double parseDoubleValue(double defaultValue, String strValue) {

        try {
            double value = Double.parseDouble(strValue.replace(",", ""));
            return value;
        } catch (Exception e) {
            return defaultValue;
        }
    }

    public static int parseIntegerValue(int defaultValue, String strValue) {

        try {
            int value = Integer.parseInt(strValue);
            return value;
        } catch (Exception e) {
            return defaultValue;
        }
    }

    public static long parseLongValue(long defaultValue, String strValue) {

        try {
            long value = Long.parseLong(strValue);
            return value;
        } catch (Exception e) {
            return defaultValue;
        }
    }

    public void sendHeartBeat() {
        mContext.sendBroadcast(new Intent("com.google.android.intent.action.GTALK_HEARTBEAT"));
        mContext.sendBroadcast(new Intent("com.google.android.intent.action.MCS_HEARTBEAT"));
    }

    /*public boolean isEmailValid(String email) {
        boolean isValid = false;

        //  String expression = "^[\\w\\.-]+@([\\w\\-]+\\.)+[A-Z]{2,4}$";
        String expression = "^[_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$";
        CharSequence inputStr = email.trim();

        Pattern pattern = Pattern.compile(expression, Pattern.CASE_INSENSITIVE);
        Matcher matcher = pattern.matcher(inputStr);
        if (matcher.matches()) {
            isValid = true;
        }
        return isValid;
    }*/

    public boolean isEmailValid(String email) {
        boolean isValid = false;

        String expression = "[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,20}";
        //String expression = "^[_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$";
        CharSequence inputStr = email.trim();

        Pattern pattern = Pattern.compile(expression, Pattern.CASE_INSENSITIVE);
        Matcher matcher = pattern.matcher(inputStr);
        if (matcher.matches()) {
            isValid = true;
        }
        return isValid;
    }

    public void generateErrorView(ErrorView errorView, String title, String subTitle) {
        errorView.setConfig(ErrorView.Config.create()
                .title("")
                .titleColor(mContext.getResources().getColor(android.R.color.black))
                .subtitle(retrieveLangLBl("", subTitle))
                .retryText(retrieveLangLBl("Retry", "LBL_RETRY_TXT"))
                .retryTextColor(mContext.getResources().getColor(R.color.error_view_retry_btn_txt_color))
                .build());
    }

    public void showError() {
        InternetConnection intCheck = new InternetConnection(mContext);
        String lable = (!intCheck.isNetworkConnected() && !intCheck.check_int()) ? retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT") : retrieveLangLBl("Please try again.", "LBL_TRY_AGAIN_TXT");

        GenerateAlertBox generateAlert = new GenerateAlertBox(mContext);
        generateAlert.setContentMessage("", lable);
        generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateAlert.showAlertBox();
    }

    public Typeface getDefaultFont(Context context) {
        return Typeface.createFromAsset(context.getAssets(), "fonts/roboto_light.ttf");
    }

    public void showGeneralMessage(String title, String message) {
        try {

            if (message != null && message.equals("SESSION_OUT")) {
                notifySessionTimeOut();
                Utils.runGC();
                return;
            }
            GenerateAlertBox generateAlert = new GenerateAlertBox(mContext);

            generateAlert.setContentMessage(title, message);
            generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
            generateAlert.showAlertBox();
        } catch (Exception e) {

        }
    }

    public void buildLowBalanceMessage(final Context context, String message, final Bundle bn) {

        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(context);

        LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.design_cash_balance_dialoge, null);
        builder.setView(dialogView);

        final MTextView addNowTxtArea = (MTextView) dialogView.findViewById(R.id.addNowTxtArea);
        final MTextView msgTxt = (MTextView) dialogView.findViewById(R.id.msgTxt);
        final MTextView skipTxtArea = (MTextView) dialogView.findViewById(R.id.skipTxtArea);
        final MTextView titileTxt = (MTextView) dialogView.findViewById(R.id.titileTxt);
        titileTxt.setText(retrieveLangLBl("", "LBL_LOW_BALANCE"));


        if (getJsonValue("APP_PAYMENT_MODE", bn.getString("UserProfileJson")).equalsIgnoreCase("Cash")) {
            addNowTxtArea.setText(retrieveLangLBl("", "LBL_CONTACT_US_TXT"));
        } else {
            addNowTxtArea.setText(retrieveLangLBl("", "LBL_ADD_NOW"));
        }


        skipTxtArea.setText(retrieveLangLBl("", "LBL_BTN_OK_TXT"));
        msgTxt.setText(message);


        skipTxtArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                cashBalAlertDialog.dismiss();

            }
        });

        addNowTxtArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                cashBalAlertDialog.dismiss();
                if (getJsonValue("APP_PAYMENT_MODE", bn.getString("UserProfileJson")).equalsIgnoreCase("Cash")) {
                    new StartActProcess(context).startAct(ContactUsActivity.class);

                } else {
                    new StartActProcess(context).startActWithData(MyWalletActivity.class, bn);
                }

            }
        });
        cashBalAlertDialog = builder.create();
        cashBalAlertDialog.setCancelable(false);
        if (isRTLmode() == true) {
            forceRTLIfSupported(cashBalAlertDialog);
        }
        cashBalAlertDialog.show();
    }

    public static DecimalFormat decimalFormat() {
        DecimalFormat df = new DecimalFormat("#.00");
        DecimalFormatSymbols sym = DecimalFormatSymbols.getInstance();
        sym.setDecimalSeparator('.');
        df.setDecimalFormatSymbols(sym);
        return df;
    }


    public boolean isLocationEnabled() {
        int locationMode = 0;
        String locationProviders;

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
            try {
                locationMode = Settings.Secure.getInt(mContext.getContentResolver(), Settings.Secure.LOCATION_MODE);

            } catch (Settings.SettingNotFoundException e) {
                e.printStackTrace();
            }

            final LocationManager manager = (LocationManager) mContext.getSystemService(Context.LOCATION_SERVICE);

            boolean statusOfGPS = manager.isProviderEnabled(LocationManager.GPS_PROVIDER);

            if (locationMode != Settings.Secure.LOCATION_MODE_OFF && statusOfGPS == true) {
                return true;
            }

            return false;

        } else {
            locationProviders = Settings.Secure.getString(mContext.getContentResolver(),
                    Settings.Secure.LOCATION_PROVIDERS_ALLOWED);

            return !TextUtils.isEmpty(locationProviders);
        }

    }


    public boolean checkLocationPermission(boolean isPermissionDialogShown) {
        int permissionCheck_fine = ContextCompat.checkSelfPermission(mContext,
                Manifest.permission.ACCESS_FINE_LOCATION);
        int permissionCheck_coarse = ContextCompat.checkSelfPermission(mContext,
                Manifest.permission.ACCESS_COARSE_LOCATION);

        if (permissionCheck_fine != PackageManager.PERMISSION_GRANTED || permissionCheck_coarse != PackageManager.PERMISSION_GRANTED) {

            if (isPermissionDialogShown == false) {
                ActivityCompat.requestPermissions((Activity) mContext,
                        new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION},
                        MY_PERMISSIONS_REQUEST);
            }


            // MY_PERMISSIONS_REQUEST_ACCESS_FINE_LOCATION is an
            // app-defined int constant. The callback method gets the
            // result of the request.
            return false;
        }

        return true;
    }

    public boolean isStoragePermissionGranted() {
        if (Build.VERSION.SDK_INT >= 23) {
            if (ContextCompat.checkSelfPermission(mContext, Manifest.permission.WRITE_EXTERNAL_STORAGE)
                    == PackageManager.PERMISSION_GRANTED) {

                return true;
            } else {

                if (mContext instanceof Activity) {
                    ActivityCompat.requestPermissions((Activity) mContext, new String[]{Manifest.permission.WRITE_EXTERNAL_STORAGE},
                            MY_PERMISSIONS_REQUEST);
                }

                return false;
            }
        } else {
            return true;
        }
    }

    public boolean isCameraPermissionGranted() {
        if (Build.VERSION.SDK_INT >= 23) {
            if (ContextCompat.checkSelfPermission(mContext, Manifest.permission.CAMERA)
                    == PackageManager.PERMISSION_GRANTED) {

                return true;
            } else {

                if (mContext instanceof Activity) {
                    ActivityCompat.requestPermissions((Activity) mContext, new String[]{Manifest.permission.CAMERA},
                            MY_PERMISSIONS_REQUEST);
                }

                return false;
            }
        } else {
            return true;
        }
    }

    public String[] generateImageParams(String key, String content) {
        String[] tempArr = new String[2];
        tempArr[0] = key;
        tempArr[1] = content;

        return tempArr;
    }

    public String getApp_Type() {

        return retrieveValue(CommonUtilities.APP_TYPE);

    }

    public boolean isAllPermissionGranted(boolean openDialog) {
        int permissionCheck_fine = ContextCompat.checkSelfPermission(mContext,
                Manifest.permission.ACCESS_FINE_LOCATION);
        int permissionCheck_coarse = ContextCompat.checkSelfPermission(mContext,
                Manifest.permission.ACCESS_COARSE_LOCATION);
        int permissionCheck_storage = ContextCompat.checkSelfPermission(mContext, Manifest.permission.WRITE_EXTERNAL_STORAGE);
        int permissionCheck_camera = ContextCompat.checkSelfPermission(mContext, Manifest.permission.CAMERA);

//        if (permissionCheck_fine != PackageManager.PERMISSION_GRANTED || permissionCheck_coarse != PackageManager.PERMISSION_GRANTED
//                || permissionCheck_storage != PackageManager.PERMISSION_GRANTED || permissionCheck_camera != PackageManager.PERMISSION_GRANTED) {

        if (permissionCheck_fine != PackageManager.PERMISSION_GRANTED || permissionCheck_coarse != PackageManager.PERMISSION_GRANTED) {

            if (openDialog) {
//
//                ActivityCompat.requestPermissions((Activity) mContext,
//                        new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION,
//                                Manifest.permission.WRITE_EXTERNAL_STORAGE, Manifest.permission.CAMERA},
//                        MY_PERMISSIONS_REQUEST);

                ActivityCompat.requestPermissions((Activity) mContext,
                        new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION
                        },
                        MY_PERMISSIONS_REQUEST);

            }

            // MY_PERMISSIONS_REQUEST_ACCESS_FINE_LOCATION is an
            // app-defined int constant. The callback method gets the
            // result of the request.
            return false;
        }

        return true;
    }

    public boolean isPermisionGranted() {
        int permissionCheck_storage = ContextCompat.checkSelfPermission(mContext, Manifest.permission.WRITE_EXTERNAL_STORAGE);
        int permissionCheck_camera = ContextCompat.checkSelfPermission(mContext, Manifest.permission.CAMERA);

        if (permissionCheck_storage == PackageManager.PERMISSION_GRANTED && permissionCheck_camera == PackageManager.PERMISSION_GRANTED) {
            return true;

        } else {
            return false;
        }
    }

    public boolean isCameraStoragePermissionGranted() {

        int permissionCheck_storage = ContextCompat.checkSelfPermission(mContext, Manifest.permission.WRITE_EXTERNAL_STORAGE);
        int permissionCheck_camera = ContextCompat.checkSelfPermission(mContext, Manifest.permission.CAMERA);

        if (permissionCheck_storage != PackageManager.PERMISSION_GRANTED || permissionCheck_camera != PackageManager.PERMISSION_GRANTED) {

//
//                ActivityCompat.requestPermissions((Activity) mContext,
//                        new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION,
//                                Manifest.permission.WRITE_EXTERNAL_STORAGE, Manifest.permission.CAMERA},
//                        MY_PERMISSIONS_REQUEST);

            ActivityCompat.requestPermissions((Activity) mContext,
                    new String[]{Manifest.permission.CAMERA, Manifest.permission.WRITE_EXTERNAL_STORAGE
                    },
                    MY_PERMISSIONS_REQUEST);


            // MY_PERMISSIONS_REQUEST_ACCESS_FINE_LOCATION is an
            // app-defined int constant. The callback method gets the
            // result of the request.
            return false;
        }

        return true;
    }

    public void openSettings() {
        if (mContext instanceof Activity) {
            Utils.hideKeyboard((Activity) mContext);
            Intent intent = new Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS);
            Uri uri = Uri.fromParts("package", CommonUtilities.package_name, null);
            intent.setData(uri);
            ((Activity) mContext).startActivityForResult(intent, MY_SETTINGS_REQUEST);
        }
    }


    public void notifySessionTimeOut() {
        generateSessionAlert = new GenerateAlertBox(mContext);


        generateSessionAlert.setContentMessage(retrieveLangLBl("", "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"),
                retrieveLangLBl("Your session is expired. Please login again.", "LBL_SESSION_TIME_OUT"));
        generateSessionAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateSessionAlert.setCancelable(false);
        generateSessionAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {

                if (btn_id == 1) {
                    logOutUser();
                    restartApp();
                }
            }
        });

        generateSessionAlert.showSessionOutAlertBox();


    }

    public GenerateAlertBox notifyRestartApp() {
        GenerateAlertBox generateAlert = new GenerateAlertBox(mContext);
        generateAlert.setContentMessage(retrieveLangLBl("", "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"),
                retrieveLangLBl("In order to apply changes restarting app is required. Please wait.", "LBL_NOTIFY_RESTART_APP_TO_CHANGE"));
        generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateAlert.showAlertBox();

        return generateAlert;
    }

    public GenerateAlertBox notifyRestartApp(String title, String contentMsg) {
        GenerateAlertBox generateAlert = new GenerateAlertBox(mContext);
        generateAlert.setContentMessage(title, contentMsg);
        generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateAlert.showAlertBox();

        return generateAlert;
    }

    public void getHasKey(Context act) {
        PackageInfo info;
        try {
            info = act.getPackageManager().getPackageInfo(act.getPackageName(), PackageManager.GET_SIGNATURES);
            for (Signature signature : info.signatures) {
                MessageDigest md;
                md = MessageDigest.getInstance("SHA");
                md.update(signature.toByteArray());
                String something = new String(Base64.encode(md.digest(), 0));
                Utils.printLog("hash key", something);
            }
        } catch (PackageManager.NameNotFoundException e1) {
            Utils.printLog("name not found", e1.toString());
        } catch (NoSuchAlgorithmException e) {
            Utils.printLog("no such an algorithm", e.toString());
        } catch (Exception e) {
            Utils.printLog("exception", e.toString());
        }
    }


    public void restartwithGetDataApp() {
        getUserData objrefresh = new getUserData(this, mContext);
        objrefresh.getData();
    }

    public void restartApp() {

        new StartActProcess(mContext).startAct(LauncherActivity.class);
        ((Activity) mContext).setResult(Activity.RESULT_CANCELED);
        try {
            ActivityCompat.finishAffinity((Activity) mContext);
        } catch (Exception e) {
        }
        freeMemory();

        //  System.gc();
    }

    public void freeMemory() {
        Utils.runGC();
    }


    public String getDateFormatedType(String date, String originalformate, String targateformate) {
        String convertdate = "";
        SimpleDateFormat original_formate = new SimpleDateFormat(originalformate);
        SimpleDateFormat date_format = new SimpleDateFormat(targateformate);

        try {
            Date datedata = original_formate.parse(date);
            convertdate = date_format.format(datedata);
            Utils.printLog("ConvertDate:", convertdate);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return convertdate;

    }

    public View getCurrentView(Activity act) {
        View view = act.findViewById(android.R.id.content);
        return view;
    }

    public void showMessage(View view, String message) {
        Snackbar snackbar = Snackbar.make(view, message, Snackbar.LENGTH_LONG);
        snackbar.show();
    }


    public double CalculationByLocation(double lat1, double lon1, double lat2, double lon2) {
        int Radius = 6371;// radius of earth in Km
        double lat1_s = lat1;
        double lat2_d = lat2;
        double lon1_s = lon1;
        double lon2_d = lon2;
        double dLat = Math.toRadians(lat2_d - lat1_s);
        double dLon = Math.toRadians(lon2_d - lon1_s);
        double a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(Math.toRadians(lat1_s))
                * Math.cos(Math.toRadians(lat2_d)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        double c = 2 * Math.asin(Math.sqrt(a));
        double valueResult = Radius * c;
        double km = valueResult / 1;
        DecimalFormat newFormat = new DecimalFormat("####");
        int kmInDec = Integer.valueOf(newFormat.format(km));
        double meter = valueResult % 1000;
        int meterInDec = Integer.valueOf(newFormat.format(meter));
        // Log.i("Radius Value", "" + valueResult + " KM " + kmInDec
        // + " Meter " + meterInDec);

        return Radius * c;
    }

    public String getSelectedCarTypeData(String selectedCarTypeId, String jsonArrKey, String dataKey, String json) {
        JSONArray arr = getJsonArray(jsonArrKey, json);

        for (int i = 0; i < arr.length(); i++) {
            JSONObject tempObj = getJsonObject(arr, i);

            String iVehicleTypeId = getJsonValue("iVehicleTypeId", tempObj.toString());

            if (iVehicleTypeId.equals(selectedCarTypeId)) {
                String dataValue = getJsonValue(dataKey, tempObj.toString());

                return dataValue;
            }
        }

        return "";
    }

    public PolylineOptions getGoogleRouteOptions(String directionJson, int width, int color) {
        PolylineOptions lineOptions = new PolylineOptions();

        try {
            DirectionsJSONParser parser = new DirectionsJSONParser();
            List<List<HashMap<String, String>>> routes_list = parser.parse(new JSONObject(directionJson));

            ArrayList<LatLng> points = new ArrayList<LatLng>();

            if (routes_list.size() > 0) {
                // Fetching i-th route
                List<HashMap<String, String>> path = routes_list.get(0);

                // Fetching all the points in i-th route
                for (int j = 0; j < path.size(); j++) {
                    HashMap<String, String> point = path.get(j);

                    double lat = Double.parseDouble(point.get("lat"));
                    double lng = Double.parseDouble(point.get("lng"));
                    LatLng position = new LatLng(lat, lng);

                    points.add(position);

                }

                lineOptions.addAll(points);
                lineOptions.width(width);
                lineOptions.color(color);

                return lineOptions;
            } else {
                return null;
            }
        } catch (Exception e) {
            return null;
        }
    }


    public void checkProfileImage(SelectableRoundedImageView userProfileImgView, String userProfileJson, String imageKey) {
        String vImgName_str = getJsonValue(imageKey, userProfileJson);

        if (vImgName_str == null || vImgName_str.equals("") || vImgName_str.equals("NONE")) {
            userProfileImgView.setImageResource(R.mipmap.ic_no_pic_user);
        } else {
            new DownloadProfileImg(mContext, userProfileImgView,
                    CommonUtilities.SERVER_URL_PHOTOS + "upload/Driver/" + getMemberId() + "/" + vImgName_str,
                    vImgName_str).execute();
        }
    }

    public void checkProfileImage(SelectableRoundedImageView userProfileImgView, String userProfileJson, String imageKey, ImageView profilebackimage) {
        String vImgName_str = getJsonValue(imageKey, userProfileJson);

        if (vImgName_str == null || vImgName_str.equals("") || vImgName_str.equals("NONE")) {
            userProfileImgView.setImageResource(R.mipmap.ic_no_pic_user);
        } else {
            new DownloadProfileImg(mContext, userProfileImgView,
                    CommonUtilities.SERVER_URL_PHOTOS + "upload/Driver/" + getMemberId() + "/" + vImgName_str,
                    vImgName_str, profilebackimage).execute();
        }
    }


    public void verifyMobile(final Bundle bn, final Fragment fragment) {
        final GenerateAlertBox generateAlert = new GenerateAlertBox(mContext);
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();

                if (btn_id == 0) {
                    return;
                }

                if (fragment == null) {
                    new StartActProcess(mContext).startActForResult(VerifyInfoActivity.class, bn, Utils.VERIFY_MOBILE_REQ_CODE);
                } else {
                    new StartActProcess(mContext).startActForResult(fragment, VerifyInfoActivity.class, Utils.VERIFY_MOBILE_REQ_CODE, bn);
                }

            }
        });
        generateAlert.setContentMessage("", retrieveLangLBl("", "LBL_VERIFY_MOBILE_CONFIRM_MSG"));
        generateAlert.setPositiveBtn(retrieveLangLBl("", "LBL_BTN_OK_TXT"));
        generateAlert.setNegativeBtn(retrieveLangLBl("", "LBL_CANCEL_TXT"));
        generateAlert.showAlertBox();
    }

    public String decodeFile(String path, int DESIREDWIDTH, int DESIREDHEIGHT, String tempImgName) {
        String strMyImagePath = null;
        Bitmap scaledBitmap = null;

        try {
            // Part 1: Decode image
//            scaledBitmap = ScalingUtilities.decodeFile(path, DESIREDWIDTH, DESIREDHEIGHT);
            int rotation = Utils.getExifRotation(path);
            Bitmap unscaledBitmap = ScalingUtilities.decodeFile(path, DESIREDWIDTH, DESIREDHEIGHT, ScalingUtilities.ScalingLogic.CROP);

            if (!(unscaledBitmap.getWidth() <= DESIREDWIDTH && unscaledBitmap.getHeight() <= DESIREDHEIGHT)) {
                // Part 2: Scale image
                scaledBitmap = ScalingUtilities.createScaledBitmap(unscaledBitmap, DESIREDWIDTH, DESIREDHEIGHT, ScalingUtilities.ScalingLogic.CROP);
            } else {
//                unscaledBitmap.recycle();
//                return path;

                if (unscaledBitmap.getWidth() > unscaledBitmap.getHeight()) {
                    scaledBitmap = ScalingUtilities.createScaledBitmap(unscaledBitmap, unscaledBitmap.getHeight(), unscaledBitmap.getHeight(), ScalingUtilities.ScalingLogic.CROP);
                } else {
                    scaledBitmap = ScalingUtilities.createScaledBitmap(unscaledBitmap, unscaledBitmap.getWidth(), unscaledBitmap.getWidth(), ScalingUtilities.ScalingLogic.CROP);
                }
            }

            // Store to tmp file
            scaledBitmap = rotateBitmap(scaledBitmap, rotation);
            String extr = Environment.getExternalStorageDirectory().toString();
            File mFolder = new File(extr + "/" + Utils.TempImageFolderPath);
            if (!mFolder.exists()) {
                mFolder.mkdir();
            }

//            String s = "tmp.png";

            File f = new File(mFolder.getAbsolutePath(), tempImgName);

            strMyImagePath = f.getAbsolutePath();
            FileOutputStream fos = null;
            try {
                fos = new FileOutputStream(f);
                scaledBitmap.compress(Bitmap.CompressFormat.JPEG, 60, fos);
                fos.flush();
                fos.close();
            } catch (FileNotFoundException e) {

                e.printStackTrace();
            } catch (Exception e) {

                e.printStackTrace();
            }

            scaledBitmap.recycle();
        } catch (Throwable e) {
        }

        if (strMyImagePath == null) {
            return path;
        }
        return strMyImagePath;

    }

    public boolean isServiceRunning(Class<?> serviceClass) {
        ActivityManager manager = (ActivityManager) mContext.getSystemService(Context.ACTIVITY_SERVICE);
        for (ActivityManager.RunningServiceInfo service : manager.getRunningServices(Integer.MAX_VALUE)) {
            if (serviceClass.getName().equals(service.service.getClassName())) {
                return true;
            }
        }
        return false;
    }

    public void saveGoOnlineInfo() {

        storedata(CommonUtilities.GO_ONLINE_KEY, "Yes");
        storedata(CommonUtilities.LAST_FINISH_TRIP_TIME_KEY, "" + Calendar.getInstance().getTimeInMillis());

    }

    public String getLocationUpdateChannel() {
        return Utils.pubNub_Update_Loc_Channel_Prefix + getMemberId();
    }

    public String buildLocationJson(Location location) {

        if (location != null) {
            try {
                JSONObject obj = new JSONObject();
                obj.put("MsgType", "LocationUpdate");
                obj.put("iDriverId", getMemberId());
                obj.put("vLatitude", location.getLatitude());
                obj.put("vLongitude", location.getLongitude());
                obj.put("ChannelName", getLocationUpdateChannel());
                obj.put("LocTime", System.currentTimeMillis() + "");

                return obj.toString();
            } catch (Exception e) {
                return "";
            }
        } else {
            return "";
        }

    }

    public String buildLocationJson(Location location, String msgType) {

        if (location != null) {
            try {
                JSONObject obj = new JSONObject();
                obj.put("MsgType", msgType);
                obj.put("iDriverId", getMemberId());
                obj.put("vLatitude", location.getLatitude());
                obj.put("vLongitude", location.getLongitude());
                obj.put("ChannelName", getLocationUpdateChannel());
                obj.put("LocTime", System.currentTimeMillis() + "");

                return obj.toString();
            } catch (Exception e) {
                return "";
            }
        } else {
            return "";
        }

    }

    public String buildRequestCancelJson(String iUserId, String vMsgCode) {

        try {
            JSONObject obj = new JSONObject();
            obj.put("MsgType", "TripRequestCancel");
            obj.put("Message", "TripRequestCancel");
            obj.put("iDriverId", getMemberId());
            obj.put("iUserId", iUserId);
            obj.put("iTripId", vMsgCode);

            return obj.toString();
        } catch (Exception e) {
            return "";
        }


    }

    public String convertNumberWithRTL(String data) {
        String result = "";
        NumberFormat nf = null;
        try {

            Locale locale = new Locale(retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY));
            nf = NumberFormat.getInstance(locale);

            if (data != null && !data.equals("")) {
                for (int i = 0; i < data.length(); i++) {

                    char c = data.charAt(i);

                    if (Character.isDigit(c)) {
                        result = result + nf.format(Integer.parseInt(String.valueOf(c)));
                        Utils.printLog("result", result);
                    } else {
                        result = result + c;

                    }

                }
            }

            Utils.printLog("result", result);
            return result;


        } catch (Exception e) {
            Utils.printLog("Exception umber ", e.toString());
        }
        return result;

    }

    public void deleteTripStatusMessages() {

        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        Map<String, ?> keys = mPrefs.getAll();

        for (Map.Entry<String, ?> entry : keys.entrySet()) {
            Utils.printLog("map values", entry.getKey() + ": " + entry.getValue().toString());

            if (entry.getKey().contains(CommonUtilities.TRIP_REQ_CODE_PREFIX_KEY)) {
                //generalFunc.removeValue(entry.getKey());
                Long CURRENTmILLI = System.currentTimeMillis() - (1000 * 60 * 60 * 24 * 1);
                long value = parseLongValue(0, entry.getValue().toString());
                if (CURRENTmILLI >= value) {
                    removeValue(entry.getKey());
                }
            }
        }
    }


    public boolean isTripStatusMsgExist(final String msg) {
        String iTripId = getJsonValue("iTripId", msg);
        String message = getJsonValue("Message", msg);
        Utils.printLog("isTripStatusMsgExist", ":0:" + mContext.getClass().getSimpleName());

        if (!iTripId.equals("")) {


            String key = CommonUtilities.TRIP_REQ_CODE_PREFIX_KEY + iTripId + message;

            String data = retrieveValue(key);

            if (data == null || data.equals("")) {
                if (getJsonValue("Message", msg).equalsIgnoreCase("DestinationAdded")) {
                    storedata(key, getJsonValue("time", msg) + "");
                } else {
                    storedata(key, System.currentTimeMillis() + "");
                }
                return false;
            } else {
                if (key.contains("DestinationAdded")) {
                    long oldTime = GeneralFunctions.parseLongValue(0, data);
                    long newTime = GeneralFunctions.parseLongValue(0, getJsonValue("time", msg));
                    if (newTime > oldTime) {
                        removeValue(key);
                        return isTripStatusMsgExist(msg);
                    }
                }
                return true;
            }
        } else if (!getJsonValue("tSessionId", msg).equals("")) {
            if (!getJsonValue("tSessionId", msg).equals(retrieveValue(Utils.SESSION_ID_KEY))) {
                return true;
            }
        }

        return false;
    }


    public String convertStringWithRTL(String data) {
        String result = "";

        try {
            result = data;

            //    Locale locale = mContext.getResources().getConfiguration().locale;

//
//            if (data != null && !data.equals("")) {
//                for (int i = 0; i < data.length(); i++) {
//
//                    char c = data.charAt(i);
//
//
//                    result = result + nf.format(Integer.parseInt(String.valueOf(c)));
//                    Utils.printLog("result", result);
//
//
//                }
//            }

            Utils.printLog("result", result);
            return result;


        } catch (Exception e) {
            Utils.printLog("Exception umber ", e.toString());
        }
        return result;

    }

    public boolean canDrawOverlayViews(Context con) {
        if (Build.VERSION.SDK_INT < Build.VERSION_CODES.LOLLIPOP)
            return true;
        Utils.printLog("SDK_VERSION", "::" + Build.VERSION.SDK_INT);

        try {
            return Settings.canDrawOverlays(con);
        } catch (NoSuchMethodError e) {
            Utils.printLog("DrawOverlayException", e.toString());
            return canDrawOverlaysUsingReflection(con);
        }

    }

    public static boolean canDrawOverlaysUsingReflection(Context context) {

        try {

            AppOpsManager manager = (AppOpsManager) context.getSystemService(Context.APP_OPS_SERVICE);
            Class clazz = AppOpsManager.class;
            Method dispatchMethod = clazz.getMethod("checkOp", new Class[]{int.class, int.class, String.class});
            //AppOpsManager.OP_SYSTEM_ALERT_WINDOW = 24
            int mode = (Integer) dispatchMethod.invoke(manager, new Object[]{24, Binder.getCallingUid(), context.getApplicationContext().getPackageName()});

            return AppOpsManager.MODE_ALLOWED == mode;

        } catch (Exception e) {
            return false;
        }

    }

    public JSONObject getJsonObjectFromString(String json) {
        JSONObject jsonObject = null;
        try {

            jsonObject = new JSONObject(json);

            Utils.printLog("Api", jsonObject.toString());

        } catch (Throwable tx) {
            Utils.printLog("Api", "Could not parse malformed JSON: \"" + json + "\"");
        }
        return jsonObject;
    }

    //

    public void logoutFromDevice(Context context, GeneralFunctions generalFunc, String from) {
        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "callOnLogout");
        parameters.put("iMemberId", getMemberId());
        parameters.put("UserType", Utils.userType);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(context, parameters);
        exeWebServer.setLoaderConfig(context, true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        logOutUser();
                        restartApp();

                    } else {
                        showGeneralMessage("",
                                retrieveLangLBl("", getJsonValue(CommonUtilities.message_str, responseString)));
                    }
                } else {
                    showError();
                }
            }
        });
        exeWebServer.execute();
    }


    public String formatUpto2Digit(float discount) {
        return "" + Math.round(discount * 100.0) / 100.0;
//        return String.format("%.2f", discount);
    }

    public String formatUpto2Digit(double discount) {
        //return String.format("%.2f", discount);
        return "" + Math.round(discount * 100.0) / 100.0;
    }
}
