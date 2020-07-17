package com.general.files;

import android.Manifest;
import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.Signature;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Matrix;
import android.graphics.Paint;
import android.graphics.Rect;
import android.graphics.Typeface;
import android.location.LocationManager;
import android.media.ExifInterface;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.preference.PreferenceManager;
import android.provider.Settings;
import android.support.design.widget.Snackbar;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.Fragment;
import android.support.v4.content.ContextCompat;
import android.text.SpannableStringBuilder;
import android.text.TextUtils;
import android.text.method.LinkMovementMethod;
import android.util.Base64;
import android.view.View;
import android.view.ViewTreeObserver;
import android.widget.ImageView;
import android.widget.TextView;

import com.fastcabtaxi.passenger.LauncherActivity;
import com.fastcabtaxi.passenger.MainActivity;
import com.fastcabtaxi.passenger.R;
import com.fastcabtaxi.passenger.RatingActivity;
import com.fastcabtaxi.passenger.VerifyInfoActivity;
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
import com.view.MyProgressDialog;
import com.view.SelectableRoundedImageView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.text.DecimalFormat;
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
    public static final int MY_PERMISSIONS_REQUEST = 51;
    public static final int MY_SETTINGS_REQUEST = 52;
    Context mContext;
    long autoLoginStartTime = 0;
    GenerateAlertBox generateAlert;
    String alertType = "";
    InternetConnection intCheck;
    String languageLabels_str = "";
    Map<String, Object> languageData = null;

    GenerateAlertBox generateSessionAlert;

    public GeneralFunctions(Context context) {
        this.mContext = context;
        checkForRTL();
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

    public static boolean checkDataAvail(String key, String response) {
        try {
            JSONObject obj_temp = new JSONObject(response);

            String action_str = obj_temp.getString(key);

            if (!action_str.equals("") && !action_str.equals("0") && action_str.equals("1")) {
                return true;
            }

        } catch (JSONException e) {


            return false;
        }

        return false;
    }

    public static boolean isJSONValid(String test) {
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
            double value = Double.parseDouble(strValue);
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

    public String getDateFormatedType(String date, String originalformate, String targateformate, Locale locale) {
        String convertdate = "";

        SimpleDateFormat original_formate = new SimpleDateFormat(originalformate, locale);
        SimpleDateFormat date_format = new SimpleDateFormat(targateformate, locale);

        try {
            Date datedata = original_formate.parse(date);
            convertdate = date_format.format(datedata);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return convertdate;

    }

    public Typeface getDefaultFont(Context context) {
        return Typeface.createFromAsset(context.getAssets(), "fonts/roboto_light.ttf");
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

    public Object getValueFromJsonArr(JSONArray arr, int position) {
        try {

            return arr.get(position);

        } catch (JSONException e) {


            return "";
        }

    }

    public String getTimezone() {
        TimeZone tz = TimeZone.getDefault();
        return tz.getID() + "";
    }

    public GenerateAlertBox notifyRestartApp(String message) {
        GenerateAlertBox generateAlert = new GenerateAlertBox(mContext);
        generateAlert.setContentMessage(retrieveLangLBl("", "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"),
                "");
        generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateAlert.showAlertBox();

        return generateAlert;
    }

    public Bitmap fastblur(Bitmap sentBitmap, float scale, int radius) {

        int width = Math.round(sentBitmap.getWidth() * scale);
        int height = Math.round(sentBitmap.getHeight() * scale);
        sentBitmap = Bitmap.createScaledBitmap(sentBitmap, width, height, false);

        Bitmap bitmap = sentBitmap.copy(sentBitmap.getConfig(), true);

        if (radius < 1) {
            return (null);
        }
        int w = bitmap.getWidth();
        int h = bitmap.getHeight();
        int[] pix = new int[w * h];
        Utils.printLog("pix", w + " " + h + " " + pix.length);
        bitmap.getPixels(pix, 0, w, 0, 0, w, h);
        int wm = w - 1;
        int hm = h - 1;
        int wh = w * h;
        int div = radius + radius + 1;
        int r[] = new int[wh];
        int g[] = new int[wh];
        int b[] = new int[wh];
        int rsum, gsum, bsum, x, y, i, p, yp, yi, yw;
        int vmin[] = new int[Math.max(w, h)];
        int divsum = (div + 1) >> 1;
        divsum *= divsum;
        int dv[] = new int[256 * divsum];
        for (i = 0; i < 256 * divsum; i++) {
            dv[i] = (i / divsum);
        }

        yw = yi = 0;

        int[][] stack = new int[div][3];
        int stackpointer;
        int stackstart;
        int[] sir;
        int rbs;
        int r1 = radius + 1;
        int routsum, goutsum, boutsum;
        int rinsum, ginsum, binsum;

        for (y = 0; y < h; y++) {
            rinsum = ginsum = binsum = routsum = goutsum = boutsum = rsum = gsum = bsum = 0;
            for (i = -radius; i <= radius; i++) {
                p = pix[yi + Math.min(wm, Math.max(i, 0))];
                sir = stack[i + radius];
                sir[0] = (p & 0xff0000) >> 16;
                sir[1] = (p & 0x00ff00) >> 8;
                sir[2] = (p & 0x0000ff);
                rbs = r1 - Math.abs(i);
                rsum += sir[0] * rbs;
                gsum += sir[1] * rbs;
                bsum += sir[2] * rbs;
                if (i > 0) {
                    rinsum += sir[0];
                    ginsum += sir[1];
                    binsum += sir[2];
                } else {
                    routsum += sir[0];
                    goutsum += sir[1];
                    boutsum += sir[2];
                }
            }
            stackpointer = radius;

            for (x = 0; x < w; x++) {
                r[yi] = dv[rsum];
                g[yi] = dv[gsum];
                b[yi] = dv[bsum];
                rsum -= routsum;
                gsum -= goutsum;
                bsum -= boutsum;
                stackstart = stackpointer - radius + div;
                sir = stack[stackstart % div];
                routsum -= sir[0];
                goutsum -= sir[1];
                boutsum -= sir[2];
                if (y == 0) {
                    vmin[x] = Math.min(x + radius + 1, wm);
                }
                p = pix[yw + vmin[x]];
                sir[0] = (p & 0xff0000) >> 16;
                sir[1] = (p & 0x00ff00) >> 8;
                sir[2] = (p & 0x0000ff);

                rinsum += sir[0];
                ginsum += sir[1];
                binsum += sir[2];

                rsum += rinsum;
                gsum += ginsum;
                bsum += binsum;

                stackpointer = (stackpointer + 1) % div;
                sir = stack[(stackpointer) % div];

                routsum += sir[0];
                goutsum += sir[1];
                boutsum += sir[2];

                rinsum -= sir[0];
                ginsum -= sir[1];
                binsum -= sir[2];

                yi++;
            }
            yw += w;
        }
        for (x = 0; x < w; x++) {
            rinsum = ginsum = binsum = routsum = goutsum = boutsum = rsum = gsum = bsum = 0;
            yp = -radius * w;
            for (i = -radius; i <= radius; i++) {
                yi = Math.max(0, yp) + x;
                sir = stack[i + radius];
                sir[0] = r[yi];
                sir[1] = g[yi];
                sir[2] = b[yi];
                rbs = r1 - Math.abs(i);
                rsum += r[yi] * rbs;
                gsum += g[yi] * rbs;
                bsum += b[yi] * rbs;
                if (i > 0) {
                    rinsum += sir[0];
                    ginsum += sir[1];
                    binsum += sir[2];
                } else {
                    routsum += sir[0];
                    goutsum += sir[1];
                    boutsum += sir[2];
                }

                if (i < hm) {
                    yp += w;
                }
            }
            yi = x;
            stackpointer = radius;
            for (y = 0; y < h; y++) {
                // Preserve alpha channel: ( 0xff000000 & pix[yi] )
                pix[yi] = (0xff000000 & pix[yi]) | (dv[rsum] << 16) | (dv[gsum] << 8) | dv[bsum];

                rsum -= routsum;
                gsum -= goutsum;
                bsum -= boutsum;

                stackstart = stackpointer - radius + div;
                sir = stack[stackstart % div];

                routsum -= sir[0];
                goutsum -= sir[1];
                boutsum -= sir[2];

                if (x == 0) {
                    vmin[y] = Math.min(y + r1, hm) * w;
                }
                p = x + vmin[y];

                sir[0] = r[p];
                sir[1] = g[p];
                sir[2] = b[p];

                rinsum += sir[0];
                ginsum += sir[1];
                binsum += sir[2];

                rsum += rinsum;
                gsum += ginsum;
                bsum += binsum;

                stackpointer = (stackpointer + 1) % div;
                sir = stack[stackpointer];

                routsum += sir[0];
                goutsum += sir[1];
                boutsum += sir[2];

                rinsum -= sir[0];
                ginsum -= sir[1];
                binsum -= sir[2];

                yi += w;
            }
        }

        Utils.printLog("pix", w + " " + h + " " + pix.length);
        bitmap.setPixels(pix, 0, w, 0, 0, w, h);

        return (bitmap);
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

    public void forceRTLIfSupported(GenerateAlertBox generateAlert) {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN_MR1) {
            generateAlert.alertDialog.getWindow().getDecorView().setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        }
    }

    public String wrapHtml(Context context, String html) {
        return context.getString(isRTLmode() ? R.string.html_rtl : R.string.html, html);
    }

    public String getDateFormatedType(String date, String originalformate, String targateformate) {
        String convertdate = "";

        SimpleDateFormat original_formate = new SimpleDateFormat(originalformate);
        SimpleDateFormat date_format = new SimpleDateFormat(targateformate);

        try {
            Date datedata = original_formate.parse(date);
            convertdate = date_format.format(datedata);
        } catch (ParseException e) {
            e.printStackTrace();
        }

        return convertdate;

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

    public String generateDeviceToken() {
        if (checkPlayServices() == false) {
            return "";
        }


        String GCMregistrationId = "";
        try {
            GCMregistrationId = FirebaseInstanceId.getInstance().getToken(retrieveValue(CommonUtilities.APP_GCM_SENDER_ID_KEY), "FCM");
            Utils.printLog("GcmId", GCMregistrationId);

        } catch (Exception e) {
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

    public void removeValue(String key) {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        SharedPreferences.Editor editor = mPrefs.edit();
        editor.remove(key);
        editor.commit();
    }

    public void storedata(String key, String data) {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        SharedPreferences.Editor editor = mPrefs.edit();
        editor.putString(key, data);
        editor.commit();
    }

    public String addSemiColonToPrice(String value) {
        return NumberFormat.getNumberInstance(Locale.US).format(parseIntegerValue(0, value));
    }

    public void storeUserData(String memberId) {
        storedata(CommonUtilities.iMemberId_KEY, memberId);
        storedata(CommonUtilities.isUserLogIn, "1");
    }

    public String getMemberId() {
        if (isUserLoggedIn() == true) {
            return retrieveValue(CommonUtilities.iMemberId_KEY);
        } else {
            return "";
        }
    }

    public boolean isReferralSchemeEnable() {
        if (!retrieveValue(CommonUtilities.REFERRAL_SCHEME_ENABLE).equals("") && retrieveValue(CommonUtilities.REFERRAL_SCHEME_ENABLE).equalsIgnoreCase("Yes")) {
            return true;
        }
        return false;
    }

    public void logOutUser() {
        removeValue(CommonUtilities.iMemberId_KEY);
        removeValue(CommonUtilities.isUserLogIn);
        // removeValue(CommonUtilities.languageLabelsKey);
        //removeValue(CommonUtilities.LANGUAGE_CODE_KEY);
        // removeValue(CommonUtilities.DEFAULT_CURRENCY_VALUE);
        removeValue(CommonUtilities.USER_PROFILE_JSON);
        removeValue(CommonUtilities.DELIVERY_DETAILS_KEY);

        removeValue("userHomeLocationLatitude");
        removeValue("userHomeLocationLongitude");
        removeValue("userHomeLocationAddress");

        removeValue("userWorkLocationLatitude");
        removeValue("userWorkLocationLongitude");
        removeValue("userWorkLocationAddress");


    }

    public boolean isUserLoggedIn() {
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(mContext);
        String isUserLoggedIn_str = mPrefs.getString(CommonUtilities.isUserLogIn, "");

        if (!isUserLoggedIn_str.equals("") && isUserLoggedIn_str.equals("1")) {
            return true;
        }

        return false;
    }

    public String getJsonValue(String key, String response) {

        try {
            JSONObject obj_temp = new JSONObject(response);


            if (!obj_temp.isNull(key)) {

                String value_str = obj_temp.getString(key);

                if (value_str != null && !value_str.equals("null") && !value_str.equals("")) {
                    return value_str;
                }
            }

        } catch (JSONException e) {
//

            return "";
        }

        return "";
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
//

            return "";
        }

        return "";
    }

    public String getJsonValueStr(String key, JSONObject response) {

        try {

            if (response != null) {
                String value_str = response.getString(key);

                if (value_str != null && !value_str.equals("null") && !value_str.equals("")) {
                    return value_str;
                }
            }

        } catch (JSONException e) {
//

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
//

            return null;
        }

    }

    public JSONArray getJsonArray(String response) {
        try {
            JSONArray obj_temp_arr = new JSONArray(response);

            return obj_temp_arr;

        } catch (JSONException e) {
//

            return null;
        }

    }

    public JSONObject getJsonObject(JSONArray arr, int position) {
        try {
            JSONObject obj_temp = arr.getJSONObject(position);

            return obj_temp;

        } catch (JSONException e) {
//

            return null;
        }

    }

    public JSONObject getJsonObject(String key, JSONObject obj) {
        try {
            JSONObject obj_temp = obj.getJSONObject(key);

            return obj_temp;

        } catch (JSONException e) {
//

            return null;
        }

    }

    public JSONObject getJsonObject(String data) {
        try {
            JSONObject obj_temp = new JSONObject(data);

            return obj_temp;

        } catch (JSONException e) {
//

            return null;
        }

    }

    public JSONObject getJsonObject(String key, String response) {

        try {
            JSONObject obj_temp = new JSONObject(response);

            JSONObject value_str = obj_temp.getJSONObject(key);

            if (value_str != null && !value_str.equals("null") && !value_str.equals("")) {
                return value_str;
            }

        } catch (JSONException e) {
//

            return null;
        }

        return null;
    }

    public boolean isJSONkeyAvail(String key, String response) {
        try {
            JSONObject json_obj = new JSONObject(response);

            if (json_obj.has(key) && !json_obj.isNull(key)) {
                return true;
            }
        } catch (JSONException e) {
//
            return false;
        }
        return false;
    }

    public boolean isJSONArrKeyAvail(String key, String response) {
        try {
            JSONObject json_obj = new JSONObject(response);

            if (json_obj.optJSONArray(key) != null) {
                return true;
            }
        } catch (JSONException e) {
//
            return false;
        }
        return false;
    }

    public void sendHeartBeat() {
        mContext.sendBroadcast(new Intent("com.google.android.intent.action.GTALK_HEARTBEAT"));
        mContext.sendBroadcast(new Intent("com.google.android.intent.action.MCS_HEARTBEAT"));
    }

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


        GenerateAlertBox generateAlert = new GenerateAlertBox(mContext);
        //  generateAlert.setContentMessage("", retrieveLangLBl("Please try again.", "LBL_TRY_AGAIN_TXT"));

        InternetConnection intCheck = new InternetConnection(mContext);
        String lable = (!intCheck.isNetworkConnected() && !intCheck.check_int()) ? retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT") : retrieveLangLBl("Please try again.", "LBL_TRY_AGAIN_TXT");


        generateAlert.setContentMessage("", lable);
        generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateAlert.showAlertBox();
    }

    public void showGeneralMessage(String title, String message) {
        try {

            if (message != null && message.equals("SESSION_OUT")) {
                notifySessionTimeOut();
                Utils.runGC();
                return;
            }

            Utils.printLog("showGeneralMessage", "::" + message);

            GenerateAlertBox generateAlert = new GenerateAlertBox(MyApp.getCurrentAct());
            generateAlert.setContentMessage(title, message);
            generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));


            generateAlert.showAlertBox();

        } catch (Exception e) {
            Utils.printLog("AlertEx", e.toString());
        }
    }

    public void showPubnubGeneralMessage(final String iTripId, final String message, final boolean isrestart, final boolean isufxrate) {
        try {

            if (message != null && message.equals("SESSION_OUT")) {
                notifySessionTimeOut();
                Utils.runGC();
                return;
            }

            final GenerateAlertBox generateAlert = new GenerateAlertBox(MyApp.getCurrentAct());
            generateAlert.setContentMessage("", message);
            generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
            generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                @Override
                public void handleBtnClick(int btn_id) {
                    generateAlert.closeAlertBox();

                    if (isrestart) {
                        restartwithGetDataApp();
                    }

                    if (isufxrate) {
                        Utils.ClerAllNotification();
                        Bundle bn = new Bundle();
                        bn.putBoolean("isUfx", true);
                        bn.putString("iTripId", getJsonValue("iTripId", iTripId));
                        new StartActProcess(mContext).startActWithData(RatingActivity.class, bn);
                    }


                }
            });


            generateAlert.showAlertBox();


        } catch (Exception e) {
            Utils.printLog("AlertEx", e.toString());
        }
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
                    ActivityCompat.requestPermissions((Activity) mContext, new String[]{Manifest.permission.WRITE_EXTERNAL_STORAGE}, MY_PERMISSIONS_REQUEST);
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

    public void logOUTFrmFB() {
        LoginManager.getInstance().logOut();
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

    public void notifySessionTimeOut() {

        if (generateSessionAlert != null) {
//            generateSessionAlert.closeAlertBox();
            return;
        }
//        GenerateAlertBox generateAlert = new GenerateAlertBox(mContext);
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
        generateSessionAlert.showAlertBox();


    }

    public void restartApp() {
        if (ConfigPubNub.getInstance(true) != null) {
            ConfigPubNub.getInstance().releaseInstances();
        }
        new StartActProcess(mContext).startAct(LauncherActivity.class);
        ((Activity) mContext).setResult(Activity.RESULT_CANCELED);
        try {
            ActivityCompat.finishAffinity((Activity) mContext);
        } catch (Exception e) {
        }
        Utils.runGC();
    }

    public void restartwithGetDataApp() {
        getUserData objrefresh = new getUserData(this, mContext);
        objrefresh.getData();
    }

    public void refreshMainActivity() {

        new StartActProcess(mContext).startAct(MainActivity.class);
        ((Activity) mContext).setResult(Activity.RESULT_OK);


    }


    public View getCurrentView(Activity act) {
        View view = act.findViewById(android.R.id.content);
        return view;
    }

    public void showMessage(View view, String message) {
        Snackbar snackbar = Snackbar
                .make(view, message, Snackbar.LENGTH_LONG);
        snackbar.show();
    }


    public double CalculationByLocationKm(double lat1, double lon1, double lat2, double lon2) {
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

        return kmInDec;
    }

    public String getSelectedCarTypeData(String selectedCarTypeId, ArrayList<HashMap<String, String>> dataList, String dataKey) {

        for (int i = 0; i < dataList.size(); i++) {
            HashMap<String, String> data_temp = dataList.get(i);

            String iVehicleTypeId = data_temp.get("iVehicleTypeId");

            if (iVehicleTypeId.equals(selectedCarTypeId)) {
                String dataValue = data_temp.get(dataKey);

                return dataValue;
            }
        }

        return "";
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

    public boolean isTripStatusMsgExist(String msg) {

        Utils.printLog("isTripStatusMsgExist", ":0:" + mContext.getClass().getSimpleName());

        if (getJsonValue("iTripId", msg) != "") {

            String key = CommonUtilities.TRIP_REQ_CODE_PREFIX_KEY + getJsonValue("iTripId", msg) + getJsonValue("Message", msg);
            String data = retrieveValue(key);

            if (data == null || data.equals("")) {
                if (MyApp.getInstance().isMyAppInBackGround()) {


                    if (!getJsonValue("MsgType", msg).equalsIgnoreCase("TripRequestCancel")) {
                        Utils.generateNotification(MyApp.getCurrentAct(), getJsonValue("vTitle", msg));
                    }

                }
                storedata(key, System.currentTimeMillis() + "");
                return false;
            } else {
                return true;
            }
        }
        return false;
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
                    CommonUtilities.SERVER_URL_PHOTOS + "upload/Passenger/" + getMemberId() + "/" + vImgName_str,
                    vImgName_str).execute();
        }
    }


    public void checkProfileImage(SelectableRoundedImageView userProfileImgView, String userProfileJson, String imageKey, ImageView profilebackimage) {
        String vImgName_str = getJsonValue(imageKey, userProfileJson);

        if (vImgName_str == null || vImgName_str.equals("") || vImgName_str.equals("NONE")) {
            userProfileImgView.setImageResource(R.mipmap.ic_no_pic_user);
        } else {
            new DownloadProfileImg(mContext, userProfileImgView,
                    CommonUtilities.SERVER_URL_PHOTOS + "upload/Passenger/" + getMemberId() + "/" + vImgName_str,
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

    public Bitmap writeTextOnDrawable(Context mContext, int drawableId, String text, boolean iswhite) {

        Bitmap bm = BitmapFactory.decodeResource(mContext.getResources(), drawableId).copy(Bitmap.Config.ARGB_8888, true);

        Typeface tf = Typeface.createFromAsset(mContext.getAssets(), mContext.getResources().getString(R.string.defaultFont));

        Paint paint = new Paint();
        paint.setStyle(Paint.Style.FILL);
        if (iswhite) {
            paint.setColor(Color.WHITE);
        } else {
            paint.setColor(Color.BLACK);
        }
        paint.setTypeface(tf);
        paint.setTextAlign(Paint.Align.CENTER);
        paint.setTextSize(Utils.dipToPixels(mContext, 14));

        Rect textRect = new Rect();
        paint.getTextBounds(text, 0, text.length(), textRect);

        Canvas canvas = new Canvas(bm);

        // If the text is bigger than the canvas , reduce the font size
        if (textRect.width() >= (canvas.getWidth() - 4))
            paint.setTextSize(Utils.dipToPixels(mContext, 14));

        int xPos = (canvas.getWidth() / 2) - 2; // -2 is for regulating the x
        // position offset

        // baseline to the center.
        int yPos = (int) ((canvas.getHeight() / 4) - ((paint.descent() + paint.ascent()) / 2));

        // canvas.save();

        for (String line : text.split("\n")) {
            canvas.drawText(line, xPos, yPos, paint);
            paint.setTextSize(Utils.dipToPixels(mContext, 14));
            yPos += paint.descent() - paint.ascent();
        }

        return bm;
    }


    public void autoLogin(final Activity mContext, final String tripId) {

        if (Utils.myPDialog == null) {
            Utils.myPDialog = new MyProgressDialog(mContext, true, retrieveLangLBl("Loading", "LBL_LOADING_TXT"));
            Utils.myPDialog.show();
        }
        generateAlert = new GenerateAlertBox(mContext);

        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                if (btn_id == 0) {
                    generateAlert.closeAlertBox();

                    if (!alertType.equals("NO_PLAY_SERVICE") && !alertType.equals("APP_UPDATE")) {
                        mContext.finish();
                    } else {
                        checkConfigurations(mContext, tripId);
                    }


                } else {
                    if (alertType.equals("NO_PLAY_SERVICE")) {

                        boolean isSuccessfulOpen = new StartActProcess(mContext).openURL("market://details?id=com.google.android.gms");
                        if (isSuccessfulOpen == false) {
                            new StartActProcess(mContext).openURL("http://play.google.com/store/apps/details?id=com.google.android.gms");
                        }

                        generateAlert.closeAlertBox();
                        checkConfigurations(mContext, tripId);

                    } else if (alertType.equals("APP_UPDATE")) {

                        boolean isSuccessfulOpen = new StartActProcess(mContext).openURL("market://details?id=" + CommonUtilities.package_name);
                        if (isSuccessfulOpen == false) {
                            new StartActProcess(mContext).openURL("http://play.google.com/store/apps/details?id=" + CommonUtilities.package_name);
                        }

                        generateAlert.closeAlertBox();
                        checkConfigurations(mContext, tripId);

                    } else if (!alertType.equals("NO_GPS")) {
                        generateAlert.closeAlertBox();
                        checkConfigurations(mContext, tripId);
                    } else {
                        new StartActProcess(mContext).
                                startActForResult(Settings.ACTION_LOCATION_SOURCE_SETTINGS, Utils.REQUEST_CODE_GPS_ON);
                    }

                }
            }
        });
        setDefaultAlertBtn();
        generateAlert.setCancelable(false);

        autoLoginStartTime = Calendar.getInstance().getTimeInMillis();

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getDetail");
        parameters.put("iUserId", getMemberId());
        parameters.put("vDeviceType", Utils.deviceType);
        parameters.put("AppVersion", Utils.getAppVersion());
        parameters.put("iTripId", tripId);
        parameters.put("UserType", CommonUtilities.app_type);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        exeWebServer.setIsDeviceTokenGenerate(true, "vDeviceToken", new GeneralFunctions(mContext));
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(final String responseString) {

                Utils.printLog("responseString", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    String message = getJsonValue(CommonUtilities.message_str, responseString);

                    if (Utils.checkText(responseString) && message.equals("SESSION_OUT")) {
                        autoLoginStartTime = 0;
                        notifySessionTimeOut();
                        Utils.runGC();
                        return;
                    }


                    if (isDataAvail == true) {
                        /*new SetUserData(generalFunc.getJsonValue(CommonUtilities.message_str, responseString), generalFunc);*/

                        if (Calendar.getInstance().getTimeInMillis() - autoLoginStartTime < 2000) {
                            new Handler().postDelayed(new Runnable() {

                                @Override
                                public void run() {
                                    new OpenMainProfile(mContext,
                                            getJsonValue(CommonUtilities.message_str, responseString), true, new GeneralFunctions(mContext), tripId).startProcess();
                                }
                            }, 2000);
                        } else {
                            new OpenMainProfile(mContext,
                                    getJsonValue(CommonUtilities.message_str, responseString), true, new GeneralFunctions(mContext), tripId).startProcess();
                        }


                    } else {
                        autoLoginStartTime = 0;
                        if (!getJsonValue("isAppUpdate", responseString).trim().equals("")
                                && getJsonValue("isAppUpdate", responseString).equals("true")) {

                            showAppUpdateDialog(retrieveLangLBl("New update is available to download. " +
                                            "Downloading the latest update, you will get latest features, improvements and bug fixes.",
                                    getJsonValue(CommonUtilities.message_str, responseString)));
                        } else {
                            showError("", retrieveLangLBl("", getJsonValue(CommonUtilities.message_str, responseString)));
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

    public void showError(String title, String contentMsg) {
        alertType = "ERROR";
        setDefaultAlertBtn();
        generateAlert.setContentMessage(title,
                contentMsg);

        generateAlert.showAlertBox();
    }


    public void checkConfigurations(Activity mContext, String tripId) {
        intCheck = new InternetConnection(mContext);

        int status = (GoogleApiAvailability.getInstance()).isGooglePlayServicesAvailable(mContext);

        if (status == ConnectionResult.SERVICE_VERSION_UPDATE_REQUIRED) {
            showErrorOnPlayServiceDialog(retrieveLangLBl("This application requires updated google play service. " +
                    "Please install Or update it from play store", "LBL_UPDATE_PLAY_SERVICE_NOTE"));
            return;
        } else if (status != ConnectionResult.SUCCESS) {
            showErrorOnPlayServiceDialog(retrieveLangLBl("This application requires updated google play service. " +
                    "Please install Or update it from play store", "LBL_UPDATE_PLAY_SERVICE_NOTE"));
            return;
        }

        if (isAllPermissionGranted() == false) {
            showError("", retrieveLangLBl("Application requires some permission to be granted to work. Please allow it.",
                    "LBL_ALLOW_PERMISSIONS_APP"));
            return;
        }
        if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {

            showNoInternetDialog();
        } else if (isLocationEnabled() == false) {
            showNoGPSDialog();
        } else {
            autoLogin(mContext, tripId);
        }

    }

    public boolean isAllPermissionGranted() {
        int permissionCheck_fine = ContextCompat.checkSelfPermission(mContext,
                Manifest.permission.ACCESS_FINE_LOCATION);
        int permissionCheck_coarse = ContextCompat.checkSelfPermission(mContext,
                Manifest.permission.ACCESS_COARSE_LOCATION);
        int permissionCheck_storage = ContextCompat.checkSelfPermission(mContext, Manifest.permission.WRITE_EXTERNAL_STORAGE);
        int permissionCheck_camera = ContextCompat.checkSelfPermission(mContext, Manifest.permission.CAMERA);

        if (permissionCheck_fine != PackageManager.PERMISSION_GRANTED || permissionCheck_coarse != PackageManager.PERMISSION_GRANTED
                || permissionCheck_storage != PackageManager.PERMISSION_GRANTED || permissionCheck_camera != PackageManager.PERMISSION_GRANTED) {

            ActivityCompat.requestPermissions((Activity) mContext,
                    new String[]{Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION,
                            Manifest.permission.WRITE_EXTERNAL_STORAGE, Manifest.permission.CAMERA},
                    MY_PERMISSIONS_REQUEST);


            // MY_PERMISSIONS_REQUEST_ACCESS_FINE_LOCATION is an
            // app-defined int constant. The callback method gets the
            // result of the request.
            return false;
        }

        return true;
    }

    public void showNoInternetDialog() {
        alertType = "NO_INTERNET";
        setDefaultAlertBtn();
        generateAlert.setContentMessage("",
                retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT"));

        generateAlert.showAlertBox();

    }

    public void showNoGPSDialog() {

        if (generateAlert == null) {
            generateAlert = new GenerateAlertBox(mContext);

        }
        alertType = "NO_GPS";
        generateAlert.setContentMessage("", retrieveLangLBl("Your GPS seems to be disabled, do you want to enable it?", "LBL_ENABLE_GPS"));

        generateAlert.resetBtn();
        generateAlert.setPositiveBtn(retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateAlert.setNegativeBtn(retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"));
        generateAlert.showAlertBox();

    }

    public void showErrorOnPlayServiceDialog(String content) {
        alertType = "NO_PLAY_SERVICE";
        generateAlert.setContentMessage("", content);

        generateAlert.resetBtn();
        generateAlert.setPositiveBtn(retrieveLangLBl("Update", "LBL_UPDATE"));
        generateAlert.setNegativeBtn(retrieveLangLBl("Retry", "LBL_RETRY_TXT"));
        generateAlert.showAlertBox();

    }

    public void setDefaultAlertBtn() {
        generateAlert.resetBtn();
        generateAlert.setPositiveBtn(retrieveLangLBl("Retry", "LBL_RETRY_TXT"));
        generateAlert.setNegativeBtn(retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"));
    }

    public void showAppUpdateDialog(String content) {
        alertType = "APP_UPDATE";
        generateAlert.setContentMessage(retrieveLangLBl("New update available", "LBL_NEW_UPDATE_AVAIL"), content);

        generateAlert.resetBtn();
        generateAlert.setPositiveBtn(retrieveLangLBl("Update", "LBL_UPDATE"));
        generateAlert.setNegativeBtn(retrieveLangLBl("Retry", "LBL_RETRY_TXT"));
        generateAlert.showAlertBox();

    }

    public void makeTextViewResizable(final TextView tv,
                                      final int maxLine, final String expandText, final boolean viewMore) {

        if (tv.getTag() == null) {
            tv.setTag(tv.getText());
        }
        ViewTreeObserver vto = tv.getViewTreeObserver();
        vto.addOnGlobalLayoutListener(new ViewTreeObserver.OnGlobalLayoutListener() {

            @SuppressWarnings("deprecation")
            @Override
            public void onGlobalLayout() {

                ViewTreeObserver obs = tv.getViewTreeObserver();
                obs.removeGlobalOnLayoutListener(this);
                if (maxLine == 0) {
                    int lineEndIndex = tv.getLayout().getLineEnd(0);
                    String text = tv.getText().subSequence(0,
                            lineEndIndex - expandText.length() + 1)
                            + " " + expandText;
                    tv.setText(text);
                    tv.setMovementMethod(LinkMovementMethod.getInstance());
                    tv.setText(addClickablePartTextViewResizable(tv.getText()
                                    .toString(), tv, maxLine, expandText,
                            viewMore), TextView.BufferType.SPANNABLE);
                } else if (maxLine > 0 && tv.getLineCount() >= maxLine) {
                    int lineEndIndex = tv.getLayout().getLineEnd(maxLine - 1);
                    String text = tv.getText().subSequence(0,
                            lineEndIndex - expandText.length() + 1)
                            + " " + expandText;
                    tv.setText(text);
                    tv.setMovementMethod(LinkMovementMethod.getInstance());
                    tv.setText(
                            addClickablePartTextViewResizable(tv.getText()
                                            .toString(), tv, maxLine, expandText,
                                    viewMore), TextView.BufferType.SPANNABLE);
                } else {
                    int lineEndIndex = tv.getLayout().getLineEnd(tv.getLayout().getLineCount() - 1);
                    String text = tv.getText().subSequence(0, lineEndIndex) + " " + expandText;
                    tv.setText(text);
                    tv.setMovementMethod(LinkMovementMethod.getInstance());
                    tv.setText(addClickablePartTextViewResizable(tv.getText().toString(), tv, lineEndIndex, expandText,
                            viewMore), TextView.BufferType.SPANNABLE);
                }
            }
        });

    }

    private SpannableStringBuilder addClickablePartTextViewResizable(
            final String strSpanned, final TextView tv, final int maxLine,
            final String spanableText, final boolean viewMore) {
        SpannableStringBuilder ssb = new SpannableStringBuilder(strSpanned);

        if (strSpanned.contains(spanableText)) {
            ssb.setSpan(
                    new MyClickableSpan(mContext) {

                        @Override
                        public void onClick(View widget) {

                            if (viewMore) {
                                tv.setLayoutParams(tv.getLayoutParams());
                                tv.setText(tv.getTag().toString(), TextView.BufferType.SPANNABLE);
                                tv.invalidate();
                                makeTextViewResizable(tv, -5, "\n- " + retrieveLangLBl("Less", "LBL_LESS_TXT"), false);
//                                tv.setTextColor(Color.BLACK);
                            } else {
                                tv.setLayoutParams(tv.getLayoutParams());
                                tv.setText(tv.getTag().toString(), TextView.BufferType.SPANNABLE);
                                tv.invalidate();
                                makeTextViewResizable(tv, 5, "...\n+ " + retrieveLangLBl("View More", "LBL_VIEW_MORE_TXT"), true);
//                                tv.setTextColor(Color.BLACK);
                            }

                        }
                    }, strSpanned.indexOf(spanableText),
                    strSpanned.indexOf(spanableText) + spanableText.length(), 0);

        }
        return ssb;

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
                    } else {
                        result = result + c;

                    }

                }
            }

            return result;


        } catch (Exception e) {
            Utils.printLog("Exception umber ", e.toString());
        }
        return result;

    }

    public double round(double value, int places) {
        if (places < 0) throw new IllegalArgumentException();

        long factor = (long) Math.pow(10, places);
        value = value * factor;
        long tmp = Math.round(value);
        return (double) tmp / factor;
    }


    public String getCurrentdate() {
        Calendar c = Calendar.getInstance();
        SimpleDateFormat df = new SimpleDateFormat(Utils.dateFormateForBooking);
        String formattedDate = df.format(c.getTime());

        return formattedDate;
    }


    public void logoutFromDevice(final Context mContext, final String from, final GeneralFunctions generalFunc) {
        final HashMap<String, String> parameters = new HashMap<String, String>();

        parameters.put("type", "callOnLogout");
        parameters.put("iMemberId", getMemberId());
        parameters.put("UserType", Utils.userType);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        if (generalFunc != null) {
            exeWebServer.setLoaderConfig(mContext, true, generalFunc);

        }
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        if (from.equals("AddDrawer")) {

                            if (mContext instanceof MainActivity) {
                                ((MainActivity) mContext).releaseScheduleNotificationTask();
                            }

                        }

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


}
