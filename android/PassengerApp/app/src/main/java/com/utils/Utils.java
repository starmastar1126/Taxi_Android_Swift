package com.utils;

import android.annotation.TargetApi;
import android.app.Activity;
import android.app.ActivityManager;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.res.Configuration;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.media.ExifInterface;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.NotificationCompat;
import android.telephony.TelephonyManager;
import android.text.InputType;
import android.text.SpannableString;
import android.text.TextUtils;
import android.text.style.ForegroundColorSpan;
import android.util.DisplayMetrics;
import android.util.Log;
import android.util.TypedValue;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.view.View;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.ImageView;

import com.fastcabtaxi.passenger.BuildConfig;
import com.fastcabtaxi.passenger.R;
import com.fastcabtaxi.passenger.RatingActivity;
import com.general.files.GeneralFunctions;
import com.view.MyProgressDialog;
import com.view.editBox.MaterialEditText;

import java.io.File;
import java.io.IOException;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import java.util.concurrent.atomic.AtomicInteger;

/**
 * Created by Admin on 29-02-2016.
 */
public class Utils {
    public static final int NOTIFICATION_ID = 11;
    public static final String pubNub_Update_Loc_Channel_Prefix = "ONLINE_DRIVER_LOC_";
    public static final String deviceType = "Android";
    public static final String userType = "Passenger";

    public static final String FireBaseDataBase = "ufxv4app";
    public static final float defaultZomLevel = (float) 16.5;
    public static final int minPasswordLength = 6;
    public static final int SELECT_COUNTRY_REQ_CODE = 124;
    public static final int SEARCH_PICKUP_LOC_REQ_CODE = 125;
    public static final int UBER_X_SEARCH_PICKUP_LOC_REQ_CODE = 123;
    public static final int SEARCH_DEST_LOC_REQ_CODE = 126;
    public static final int MY_PROFILE_REQ_CODE = 127;
    public static final int VERIFY_MOBILE_REQ_CODE = 128;
    public static final int ADD_HOME_LOC_REQ_CODE = 129;
    public static final int ADD_WORK_LOC_REQ_CODE = 130;
    public static final int CARD_PAYMENT_REQ_CODE = 131;
    public static final int VERIFY_INFO_REQ_CODE = 135;
    public static final int ADD_VEHICLE_REQ_CODE = 136;
    public static final int ADD_RECIPIENT_REQ_CODE = 137;
    public static final int SELECT_RECIPIENT_REQ_CODE = 138;
    public static final int ASSIGN_DRIVER_CODE = 140;
    public static final int PLACE_CUSTOME_LOC_REQUEST_CODE = 141;
    public static final int RIDE_DELIVERY_DETAILS_REQ_CODE = 1224;
    public static final int DELIVERY_DETAILS_REQ_CODE = 132;
    public static final int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
    public static final int REQUEST_CODE_GPS_ON = 2425;
    public static final int REQUEST_CODE_NETWOEK_ON = 2430;
    public static final int PLACE_AUTOCOMPLETE_REQUEST_CODE = 1;
    public static final int PICKUP_PET_REQ_CODE = 134;
    public static final int ADD_EDIT_PET_REQ_CODE = 133;
    public static final int MENU_PROFILE = 0;
    public static final int MENU_VEHICLE = 0100;
    public static final int GET_ONLINE_DRIVER_LIST = 1333;
    public static final int MENU_RIDE_HISTORY = 1;
    public static final int MENU_BOOKINGS = 2;
    public static final int MENU_ABOUT_US = 3;
    public static final int MENU_PAYMENT = 4;
    public static final int MENU_CONTACT_US = 5;
    public static final int MENU_HELP = 6;
    public static final int MENU_EMERGENCY_CONTACT = 7;
    public static final int MENU_SIGN_OUT = 8;
    public static final int MENU_WALLET = 9;
    public static final int MENU_INVITE_FRIEND = 10;
    public static final int MENU_MY_PETS = 11;
    public static final int MENU_POLICY = 12;
    public static final int MENU_ONGOING_TRIPS = 13;
    public static final int MENU_SUPPORT = 14;
    public static final int MENU_YOUR_TRIPS = 15;
    public static final int MENU_ACCOUNT_VERIFY = 16;
    public static final int ImageUpload_DESIREDWIDTH = 1024;
    public static final int ImageUpload_DESIREDHEIGHT = 1024;
    public static final int ImageUpload_MINIMUM_WIDTH = 256;
    public static final int ImageUpload_MINIMUM_HEIGHT = 256;
    public static final String TempImageFolderPath = "TempImages";
    public static final String TempProfileImageName = "temp_pic_img.png";
    public static final String CabReqType_Now = "Now";
    public static final String CabReqType_Later = "Later";
    public static final String CabGeneralType_Ride = "Ride";
    public static final String CabGeneralType_Deliver = "Delivery";
    public static final String CabGeneralType_UberX = "UberX";
    public static final String CabGeneralTypeRide_Delivery_UberX = "Ride-Delivery-UberX";
    public static final String Cab_UberX_Type_List = "List";
    public static final String Cab_UberX_Type_Map = "Map";
    public static final String Wallet_all = "All";
    public static final String Wallet_credit = "CREDIT";
    public static final String Wallet_debit = "DEBIT";
    public static final String Past = "getRideHistory";
    public static final String Upcoming = "checkBookings";
    public static final String CabFaretypeRegular = "Regular";
    public static final String CabFaretypeFixed = "Fixed";
    public static final String CabFaretypeHourly = "Hourly";
    public static final int LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS = 2;
    private static final AtomicInteger sNextGeneratedId = new AtomicInteger(1);
    public static int ADD_MAP_LOC_REQ_CODE = 142;
    public static MyProgressDialog myPDialog = null;
    public static int LIVE_TRACK_REQUEST_CODE = 141;
    public static String SESSION_ID_KEY = "APP_SESSION_ID";
    public static String DEVICE_SESSION_ID_KEY = "DEVICE_SESSION_ID";
    public static String FETCH_TRIP_STATUS_TIME_INTERVAL_KEY = "FETCH_TRIP_STATUS_TIME_INTERVAL";
    public static String RIDER_REQUEST_ACCEPT_TIME_KEY = "RIDER_REQUEST_ACCEPT_TIME";
    public static int UFX_REQUEST_CODE = 142;
    public static int SCHEDULE_REQUEST_CODE = 143;
    public static String dateFormateInHeaderBar = "EEE, MMM d, yyyy";
    public static String dateFormateInList = "dd-MMM-yyyy";
    public static String DefaultDatefromate = "yyyy-MM-dd";
    public static String WalletApiFormate = "dd-MMM-yyyy";
    public static String OriginalDateFormate = "yyyy-MM-dd HH:mm:ss";
    public static String DateFormatewithTime = "EEE, MMM dd, yyyy hh:mm aa";

    public static String dateFormateForBooking = "dd MMM yyyy";

    public static NotificationManager notificationmanager;

    public static String VERIFICATION_CODE_RESEND_TIME_IN_SECONDS_KEY = "VERIFICATION_CODE_RESEND_TIME_IN_SECONDS";
    public static String VERIFICATION_CODE_RESEND_COUNT_KEY = "VERIFICATION_CODE_RESEND_COUNT";
    public static String VERIFICATION_CODE_RESEND_COUNT_RESTRICTION_KEY = "VERIFICATION_CODE_RESEND_COUNT_RESTRICTION";


    public static String storedImageFolderName = "/" + CommonUtilities.app_name + "/ProfileImage";

    public static void printLog(String title, String content) {
        Log.d(title, content);
    }

    public static void printELog(String title, String content) {
        // Log.e(title, content);
    }

    public static int dipToPixels(Context context, float dipValue) {
        DisplayMetrics metrics = context.getResources().getDisplayMetrics();
        return ((int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP, dipValue, metrics));
    }


    public static int getExifRotation(String path) {
        ExifInterface exif = null;
        try {
            exif = new ExifInterface(path);
        } catch (IOException e) {
            e.printStackTrace();
        }
        int orientation = exif.getAttributeInt(ExifInterface.TAG_ORIENTATION,
                ExifInterface.ORIENTATION_UNDEFINED);

        return orientation;

    }

    public static void setMenuTextColor(MenuItem item, int color) {
        SpannableString s = new SpannableString(item.getTitle());
        s.setSpan(new ForegroundColorSpan(color), 0, s.length(), 0);
        item.setTitle(s);
    }

    public static String removeWithSpace(String data) {

        //  return data.replace("\n", "").replace(" \t", " ");
        return data.replaceAll("\\s+", " ");
    }

    public static int generateViewId() {
        /**
         * Generate a value suitable for use in {@link #setId(int)}.
         * This value will not collide with ID values generated at build time by aapt for R.id.
         *
         * @return a generated ID value
         */

        if (Build.VERSION.SDK_INT < Build.VERSION_CODES.JELLY_BEAN_MR1) {
            for (; ; ) {
                final int result = sNextGeneratedId.get();
                // aapt-generated IDs have the high byte nonzero; clamp to the range under that.
                int newValue = result + 1;
                if (newValue > 0x00FFFFFF) newValue = 1; // Roll over to 1, not 0.
                if (sNextGeneratedId.compareAndSet(result, newValue)) {
                    return result;
                }
            }

        } else {
            return View.generateViewId();
        }

    }

    public static void runGC() {
        //System.gc();


//        System.runFinalization();
//        Runtime.getRuntime().gc();
//        System.gc();
    }


    public static void removeInput(EditText editBox) {
        editBox.setInputType(InputType.TYPE_NULL);
        editBox.setFocusableInTouchMode(false);
        editBox.setFocusable(false);

        editBox.setOnTouchListener(new View.OnTouchListener() {

            @Override
            public boolean onTouch(View v, MotionEvent event) {
                // TODO Auto-generated method stub
                return true;
            }
        });
    }

    public static boolean checkText(MaterialEditText editBox) {
        if (getText(editBox).trim().equals("") || TextUtils.isEmpty(editBox.getText())) {
            return false;
        }
        return true;
    }

    public static boolean checkText(String text) {
        if (text.trim().equals("") || TextUtils.isEmpty(text)) {
            return false;
        }
        return true;
    }

    public static boolean checkText(EditText editBox) {
        if (getText(editBox).trim().equals("")) {
            return false;
        }
        return true;
    }

    public static String getText(MaterialEditText editBox) {
        return editBox.getText().toString().trim();
    }

    public static String getText(EditText editBox) {
        return editBox.getText().toString().trim();
    }

    public static boolean setErrorFields(MaterialEditText editBox, String error) {
        editBox.setError(error);
        return false;
    }


    public static String maskCardNumber(String cardNumber) {

        int i = 0;
        StringBuffer temp = new StringBuffer();
        while (i < (cardNumber.length())) {
            if (i > cardNumber.length() - 5) {
                temp.append(cardNumber.charAt(i));
            } else {
                temp.append("X");
            }
            i++;
        }

        return temp.toString();
    }

    public static String convertDateToFormat(String format, Date date) {
        SimpleDateFormat dateFormat = new SimpleDateFormat(format, Locale.US);
        return dateFormat.format(date);
    }

    public static boolean isValidTimeSelect(Date selectedDate, long timeOffset) {
        /*Calendar now = Calendar.getInstance();
        now.add(Calendar.HOUR,timeOffset);

        Calendar selectedCal = Calendar.getInstance();
        selectedCal.setTime(selectedDate);*/
        if (selectedDate.getTime() >= (System.currentTimeMillis() + timeOffset)) {
            return true;
        }
        return false;
    }

    public static boolean isValidTimeSelectForLater(Date selectedDate, long timeOffset) {
        /*Calendar now = Calendar.getInstance();
        now.add(Calendar.HOUR,timeOffset);

        Calendar selectedCal = Calendar.getInstance();
        selectedCal.setTime(selectedDate);*/
        if (selectedDate.getTime() <= (System.currentTimeMillis() + timeOffset)) {
            return true;
        }
        return false;
    }


    public static void hideKeyboard(Context context) {
//            Utils.printLog("Key board","hide");
//            InputMethodManager imm = (InputMethodManager) context.getSystemService(Activity.INPUT_METHOD_SERVICE);
//            imm.toggleSoftInput(InputMethodManager.RESULT_HIDDEN, 0);

        if (context != null && context instanceof Activity) {
            hideKeyboard(((Activity) context));
        }
    }

    public static void hideKeyboard(Activity act) {
        if (act != null && act instanceof Activity) {
            act.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            act.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            View view = act.getCurrentFocus();
            if (view != null) {
                InputMethodManager imm = (InputMethodManager) act.getSystemService(Context.INPUT_METHOD_SERVICE);
                imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
            }
        }
    }

    public static void setAppLocal(Context mContext) {
        GeneralFunctions generalFunc = new GeneralFunctions(mContext);

        String googleMapLangCode = generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY);
        String languageToLoad = googleMapLangCode.trim().equals("") ? "en" : googleMapLangCode;
        Locale locale = new Locale(languageToLoad, mContext.getResources().getConfiguration().locale.getCountry());
        Locale.setDefault(locale);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            updateResourcesLocale(mContext, locale);
            return;
        }

        updateResourcesLocaleLegacy(mContext, locale);
//        Configuration config = new Configuration();
//        config.locale = locale;
//        mContext.getResources().updateConfiguration(config,
//                mContext.getResources().getDisplayMetrics());
    }

    @TargetApi(Build.VERSION_CODES.N)
    private static Context updateResourcesLocale(Context context, Locale locale) {
        Configuration configuration = context.getResources().getConfiguration();
        configuration.setLocale(locale);
        return context.createConfigurationContext(configuration);
    }

    @SuppressWarnings("deprecation")
    private static Context updateResourcesLocaleLegacy(Context context, Locale locale) {
        Resources resources = context.getResources();
        Configuration configuration = resources.getConfiguration();
        configuration.locale = locale;
        resources.updateConfiguration(configuration, resources.getDisplayMetrics());
        return context;
    }

    public static void clearApplicationData(Context mContext) {
        File cache = mContext.getCacheDir();
        File appDir = new File(cache.getParent());
        if (appDir.exists()) {
            String[] children = appDir.list();
            for (String s : children) {
                if (!s.equals("lib")) {
                    deleteDir(new File(appDir, s));
                    Utils.printLog("TAG", "**************** File /data/data/APP_PACKAGE/" + s + " DELETED *******************");
                }
            }
        }
    }

    public static boolean deleteDir(File dir) {
        if (dir != null && dir.isDirectory()) {
            String[] children = dir.list();
            for (int i = 0; i < children.length; i++) {
                boolean success = deleteDir(new File(dir, children[i]));
                if (!success) {
                    return false;
                }
            }
        }
        return dir.delete();
    }

    public static double CalculationByLocation(double lat1, double lon1, double lat2, double lon2, String returnType) {
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
//        printLog("Api"," Radius Value Meter " + meter +" Radius Value in meterInDec"+meterInDec);
        if (returnType.equals("METER")) {
            return meterInDec;
        } else {
            return Radius * c;
        }
    }


    public static int dpToPx(Context context, float dpValue) {
        DisplayMetrics displayMetrics = context.getResources().getDisplayMetrics();
        int px = Math.round(dpValue * (displayMetrics.xdpi / DisplayMetrics.DENSITY_DEFAULT));
        return px;
    }

    public static int pxToDp(Context context, float pxValue) {
        DisplayMetrics displayMetrics = context.getResources().getDisplayMetrics();
        int dp = Math.round(pxValue / (displayMetrics.xdpi / DisplayMetrics.DENSITY_DEFAULT));
        return dp;
    }

    public static String getAppVersion() {
        return BuildConfig.VERSION_NAME;
    }

    public static void generateNotification(Context context, String message) {
        Intent intent = null;

        if (getPreviousIntent(context) != null) {
            intent = getPreviousIntent(context);
        } else {
            intent = context
                    .getPackageManager()
                    .getLaunchIntentForPackage(context.getPackageName());

            intent.setFlags(Intent.FLAG_ACTIVITY_REORDER_TO_FRONT |
                    Intent.FLAG_ACTIVITY_NEW_TASK |
                    Intent.FLAG_ACTIVITY_RESET_TASK_IF_NEEDED);
        }

        PendingIntent contentIntent = PendingIntent.getActivity(context, 0, intent, PendingIntent.FLAG_UPDATE_CURRENT);

        int icon = R.mipmap.ic_launcher;
        String title = context.getString(R.string.app_name);


        NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(context);

//                .setSound(Uri.parse("android.resource://" + context.getPackageName() + "/" + R.raw.notification))


        if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            mBuilder.setSmallIcon(R.drawable.ic_stat_rider_logo);
            mBuilder.setLargeIcon(BitmapFactory.decodeResource(context.getResources(),
                    R.mipmap.ic_launcher));
            mBuilder.setContentTitle(title).setContentText(message).setContentIntent(contentIntent);
            mBuilder.setDefaults(Notification.DEFAULT_ALL).setAutoCancel(true);
            mBuilder.setColor(context.getResources().getColor(R.color.appThemeColor_1));
        } else {
            mBuilder.setSmallIcon(R.drawable.ic_stat_rider_logo);
            mBuilder.setLargeIcon(BitmapFactory.decodeResource(context.getResources(),
                    R.mipmap.ic_launcher));
            mBuilder.setContentTitle(title).setContentText(message).setContentIntent(contentIntent);
            mBuilder.setDefaults(Notification.DEFAULT_ALL).setAutoCancel(true);
            mBuilder.setColor(context.getResources().getColor(R.color.appThemeColor_1));
        }

        notificationmanager = (NotificationManager) context
                .getSystemService(Context.NOTIFICATION_SERVICE);

        notificationmanager.notify(Utils.NOTIFICATION_ID, mBuilder.build());

        //WakeLocker.release();
    }

    public static void ClerAllNotification() {

        if (notificationmanager != null) {
            notificationmanager.cancelAll();
        }
    }

    public static void generateNotificationForRating(Context context, String message, String iTripId) {
        // WakeLocker.acquire(context);
        Intent notificationIntent = new Intent(context, RatingActivity.class);


//**add this line**
        notificationIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);

        Bundle bn = new Bundle();
        GeneralFunctions generalFunctions = new GeneralFunctions(context);
        String eType = generalFunctions.getJsonValue("eType", message);
        if (eType.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
            bn.putBoolean("isUfx", true);
        } else {
            bn.putBoolean("isUfx", false);
        }

        bn.putString("iTripId", iTripId);
        bn.putBoolean("isFirst", true);
        notificationIntent.putExtras(bn);

//**edit this line to put requestID as requestCode**
        PendingIntent contentIntent = PendingIntent.getActivity(context, 0, notificationIntent, PendingIntent.FLAG_UPDATE_CURRENT);


        int icon = R.mipmap.ic_launcher;
        String title = context.getString(R.string.app_name);


        NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(context);
        if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            mBuilder.setSmallIcon(R.drawable.ic_stat_rider_logo);
            mBuilder.setLargeIcon(BitmapFactory.decodeResource(context.getResources(),
                    R.mipmap.ic_launcher));
            mBuilder.setContentTitle(title).setContentText(message).setContentIntent(contentIntent);
            mBuilder.setDefaults(Notification.DEFAULT_ALL).setAutoCancel(true);
            mBuilder.setColor(context.getResources().getColor(R.color.appThemeColor_1));
        } else {
            mBuilder.setSmallIcon(R.drawable.ic_stat_rider_logo);
            mBuilder.setLargeIcon(BitmapFactory.decodeResource(context.getResources(),
                    R.mipmap.ic_launcher));
            mBuilder.setContentTitle(title).setContentText(message).setContentIntent(contentIntent);
            mBuilder.setDefaults(Notification.DEFAULT_ALL).setAutoCancel(true);
            mBuilder.setColor(context.getResources().getColor(R.color.appThemeColor_1));
        }

        notificationmanager = (NotificationManager) context
                .getSystemService(Context.NOTIFICATION_SERVICE);

        notificationmanager.notify(Utils.NOTIFICATION_ID, mBuilder.build());

        //WakeLocker.release();
    }

    public static Intent getPreviousIntent(Context context) {
        Intent newIntent = null;
        final ActivityManager am = (ActivityManager) context.getSystemService(Context.ACTIVITY_SERVICE);
        final List<ActivityManager.RecentTaskInfo> recentTaskInfos = am.getRecentTasks(1024, 0);
        String myPkgNm = context.getPackageName();

        if (!recentTaskInfos.isEmpty()) {
            ActivityManager.RecentTaskInfo recentTaskInfo;
            for (int i = 0; i < recentTaskInfos.size(); i++) {
                recentTaskInfo = recentTaskInfos.get(i);
                if (recentTaskInfo.baseIntent.getComponent().getPackageName().equals(myPkgNm)) {
                    newIntent = recentTaskInfo.baseIntent;
                    newIntent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                }
            }
        }
        return newIntent;
    }

    public static String getUserDeviceCountryCode(Context context) {
        if (context == null) {
            return "";
        }
        try {
            final TelephonyManager tm = (TelephonyManager) context.getSystemService(Context.TELEPHONY_SERVICE);
            final String simCountry = tm.getSimCountryIso();
            if (simCountry != null && simCountry.length() == 2) { // SIM country code is available
                return simCountry.toLowerCase(Locale.US);
            } else if (tm.getPhoneType() != TelephonyManager.PHONE_TYPE_CDMA) { // device is not 3G (would be unreliable)
                String networkCountry = tm.getNetworkCountryIso();
                if (networkCountry != null && networkCountry.length() == 2) { // network country code is available
                    return networkCountry.toLowerCase(Locale.US);
                }
            }
        } catch (Exception e) {
            Utils.printELog("TelephonyError", ":Details:" + e.getMessage());
        }

        String countryCode = "";
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                countryCode = context.getResources().getConfiguration().getLocales().get(0).getCountry();
            } else {
                countryCode = context.getResources().getConfiguration().locale.getCountry();
            }
        } catch (Exception e) {
            Utils.printELog("LocalizedCountryCodeError", ":Details:" + e.getMessage());
        }


        return countryCode;
    }

    public static void setBlurImage(Bitmap bitmap_profile_icon, ImageView profileimageback) {
        Bitmap blurred1 = fastblur(bitmap_profile_icon,  95);//second parametre is radius
        profileimageback.setImageBitmap(blurred1);
        profileimageback.invalidate();

    }
    public static Bitmap fastblur(Bitmap sentBitmap, int radius) {
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
                pix[yi] = ( 0xff000000 & pix[yi] ) | ( dv[rsum] << 16 ) | ( dv[gsum] << 8 ) | dv[bsum];

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
}
