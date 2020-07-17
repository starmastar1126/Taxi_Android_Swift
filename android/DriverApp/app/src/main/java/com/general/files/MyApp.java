package com.general.files;

import android.app.Activity;
import android.app.Application;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.pm.ActivityInfo;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.location.LocationManager;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.support.multidex.MultiDex;
import android.support.v7.app.AppCompatDelegate;
import android.view.WindowManager;

import com.fastcabtaxi.driver.LauncherActivity;
import com.fastcabtaxi.driver.R;
import com.splunk.mint.Mint;
import com.utils.CommonUtilities;
import com.utils.Utils;

import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.logging.Logger;

/**
 * Created by Admin on 28-06-2016.
 */
public class MyApp extends Application {
    GeneralFunctions generalFun;
    private Activity mCurrentActivity = new LauncherActivity();
    private GpsReceiver mGpsReceiver;
    protected static MyApp mMyApp;
    Logger mLog;
    boolean isAppInBackground = true;

    public static Activity currentactivity = null;


    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(base);
        MultiDex.install(this);
    }
    @Override
    public void onCreate() {
        super.onCreate();
        setScreenOrientation();
        Mint.initAndStartSession(this, CommonUtilities.MINT_APP_ID);
        mMyApp = (MyApp) this.getApplicationContext();
        generalFun = new GeneralFunctions(this);
        mMyApp = (MyApp) this.getApplicationContext();
        Mint.initAndStartSession(this, CommonUtilities.MINT_APP_ID);
        AppCompatDelegate.setCompatVectorFromResourcesEnabled(true);

        if (mGpsReceiver == null)
            registerReceiver();


        if (generalFun != null && generalFun.getMemberId() != null) {
            Mint.addExtraData("iMemberId", "" + generalFun.getMemberId());
        }

//        // Setup handler for uncaught exceptions.
//        Thread.setDefaultUncaughtExceptionHandler (new Thread.UncaughtExceptionHandler()
//        {
//            @Override
//            public void uncaughtException (Thread thread, Throwable e)
//            {
//                Utils.printLog("Api","in handleUncaughtException");
//                handleUncaughtException (thread, e);
//            }
//        });

    }

    public void handleUncaughtException (Thread thread, Throwable e)
    {
        e.printStackTrace(); // not all Android versions will print the stack trace automatically
        try {
            extractLogToFile();

        }
        catch (Exception e1)
        {
            e1.printStackTrace();
        }
    }


    public static synchronized MyApp getInstance() {
        return mMyApp;
    }

    public void stopAlertService() {
        stopService(new Intent(getBaseContext(), ChatHeadService.class));
    }


    public boolean isMyAppInBackGround(){
        return this.isAppInBackground;
    }

    public Activity getCurrentActivity() {
        return mCurrentActivity;
    }

    public void setCurrentActivity(Activity mCurrentActivity) {
        this.mCurrentActivity = mCurrentActivity;

    }

    @Override
    public void onLowMemory() {
        super.onLowMemory();

        Utils.printLog("Api","Object Destroyed >> MYAPP onLowMemory");
    }

    @Override
    public void onTrimMemory(int level) {
        super.onTrimMemory(level);

        Utils.printLog("Api","Object Destroyed >> MYAPP onTrimMemory");
    }


    @Override
    public void onTerminate() {
        super.onTerminate();
        Utils.printLog("Api","Object Destroyed >> MYAPP onTerminate");
       releaseGpsReceiver();
    }

    private void releaseGpsReceiver() {
        if (mGpsReceiver != null)
            this.unregisterReceiver(mGpsReceiver);
        this.mGpsReceiver = null;
    }

    private void registerReceiver() {
        if (Build.VERSION.SDK_INT > Build.VERSION_CODES.FROYO) {

            IntentFilter mIntentFilter = new IntentFilter();
            mIntentFilter.addAction(LocationManager.PROVIDERS_CHANGED_ACTION);

            this.mGpsReceiver = new GpsReceiver();
            this.registerReceiver(this.mGpsReceiver, mIntentFilter);
        }
    }

    public void setScreenOrientation() {
        registerActivityLifecycleCallbacks(new ActivityLifecycleCallbacks() {

            @Override
            public void onActivityCreated(Activity activity,
                                          Bundle savedInstanceState) {
                activity.setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                activity.setTitle(getResources().getString(R.string.app_name));
                mMyApp.setCurrentActivity(activity);

                currentactivity = activity;

                Utils.runGC();

                activity.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_HIDDEN);
                activity.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

            }

            @Override
            public void onActivityStarted(Activity activity) {
                mMyApp.setCurrentActivity(activity);
                Utils.runGC();
            }

            @Override
            public void onActivityResumed(Activity activity) {
                mMyApp.setCurrentActivity(activity);
                if (!activity.getLocalClassName().equalsIgnoreCase("LauncherActivity") && generalFun.isLocationEnabled() == false && generalFun.retrieveValue("isInLauncher").equalsIgnoreCase("false")) {
                //    new GeneralFunctions(activity).restartApp();
                }
                currentactivity = activity;
                isAppInBackground = false;
                Utils.runGC();
                Utils.printLog("AppBackground","FromResume");
                Utils.sendBroadCast(getApplicationContext(), CommonUtilities.BACKGROUND_APP_RECEIVER_INTENT_ACTION);
               Utils.dismissBackGroundNotification(getApplicationContext());

            }

            @Override
            public void onActivityPaused(Activity activity) {
                clearReferences();

                isAppInBackground = true;
                Utils.runGC();
                Utils.printLog("AppBackground","FromPause");
                Utils.sendBroadCast(getApplicationContext(), CommonUtilities.BACKGROUND_APP_RECEIVER_INTENT_ACTION);
            }

            @Override
            public void onActivityStopped(Activity activity) {
                Utils.runGC();
            }

            @Override
            public void onActivitySaveInstanceState(Activity activity, Bundle bundle) {
            }

            @Override
            public void onActivityDestroyed(Activity activity) {
                Utils.hideKeyboard(activity);
                Utils.runGC();
                clearReferences();

            }


        });
    }


    private void clearReferences() {
        Activity currActivity = mMyApp.getCurrentActivity();
        if (this.equals(currActivity))
            mMyApp.setCurrentActivity(null);
    }


    public static Activity getCurrentAct() {
        return currentactivity;
    }


    private String extractLogToFile()
    {
        PackageManager manager = this.getPackageManager();
        PackageInfo info = null;
        try {
            info = manager.getPackageInfo (this.getPackageName(), 0);
        } catch (PackageManager.NameNotFoundException e2) {
        }
        String model = Build.MODEL;
        if (!model.startsWith(Build.MANUFACTURER))
            model = Build.MANUFACTURER + " " + model;

        // Make file name - file must be saved to external storage or it wont be readable by
        // the email app.
        String path = Environment.getExternalStorageDirectory() + "/" + "MyApp/";
        String fullName = path + "Log";
        Utils.printLog("Api","fullName"+fullName);
        // Extract to file.
        File file = new File (fullName);
        InputStreamReader reader = null;
        FileWriter writer = null;
        try
        {
            // For Android 4.0 and earlier, you will get all app's log output, so filter it to
            // mostly limit it to your app's output.  In later versions, the filtering isn't needed.
            String cmd = (Build.VERSION.SDK_INT <= Build.VERSION_CODES.ICE_CREAM_SANDWICH_MR1) ?
                    "logcat -d -v time MyApp:v dalvikvm:v System.err:v *:s" :
                    "logcat -d -v time";

            // get input stream
            Process process = Runtime.getRuntime().exec(cmd);
            reader = new InputStreamReader (process.getInputStream());

            // write output stream
            writer = new FileWriter (file);
            writer.write ("Android version: " +  Build.VERSION.SDK_INT + "\n");
            writer.write ("Device: " + model + "\n");
            writer.write ("App version: " + (info == null ? "(null)" : info.versionCode) + "\n");

            char[] buffer = new char[10000];
            do
            {
                int n = reader.read (buffer, 0, buffer.length);
                if (n == -1)
                    break;
                writer.write (buffer, 0, n);
            } while (true);

            reader.close();
            writer.close();
        }
        catch (IOException e)
        {
            if (writer != null)
                try {
                    writer.close();
                } catch (IOException e1) {
                }
            if (reader != null)
                try {
                    reader.close();
                } catch (IOException e1) {
                }

            // You might want to write a failure message to the log here.
            return null;
        }

        return fullName;
    }

}
