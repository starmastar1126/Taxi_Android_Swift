package com.general.files;

import android.app.Activity;
import android.app.Application;
import android.content.Context;
import android.content.IntentFilter;
import android.content.pm.ActivityInfo;
import android.location.LocationManager;
import android.os.Build;
import android.os.Bundle;
import android.support.multidex.MultiDex;
import android.support.v7.app.AppCompatDelegate;
import android.view.WindowManager;

import com.fastcabtaxi.passenger.LauncherActivity;
import com.fastcabtaxi.passenger.R;
import com.splunk.mint.Mint;
import com.utils.CommonUtilities;
import com.utils.Utils;

/**
 * Created by Admin on 28-06-2016.
 */
public class MyApp extends Application {
    public static Activity currentactivity = null;
    protected static MyApp mMyApp;
    GeneralFunctions generalFun;
    boolean isAppInBackground = true;
    private Activity mCurrentActivity = new LauncherActivity();
    private GpsReceiver mGpsReceiver;

    public static Activity getCurrentAct() {
        return currentactivity;
    }

    public static synchronized MyApp getInstance() {
        return mMyApp;
    }

    @Override
    public void onCreate() {
        super.onCreate();

        setScreenOrientation();
        mMyApp = (MyApp) this.getApplicationContext();
        Mint.initAndStartSession(this, CommonUtilities.MINT_APP_ID);
        AppCompatDelegate.setCompatVectorFromResourcesEnabled(true);

        generalFun = new GeneralFunctions(this);

        if (mGpsReceiver == null)
            registerReceiver();

        if (generalFun != null && generalFun.getMemberId() != null) {
            Mint.addExtraData("iMemberId", "" + generalFun.getMemberId());
        }
    }

    public Activity getCurrentActivity() {
        return mCurrentActivity;
    }

    public void setCurrentActivity(Activity mCurrentActivity) {
        this.mCurrentActivity = mCurrentActivity;
    }

    public boolean isMyAppInBackGround() {
        return this.isAppInBackground;
    }

    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(base);
        MultiDex.install(this);
    }


    @Override
    public void onLowMemory() {
        super.onLowMemory();

        Utils.printLog("Api", "Object Destroyed >> MYAPP onLowMemory");
    }

    @Override
    public void onTrimMemory(int level) {
        super.onTrimMemory(level);

        Utils.printLog("Api", "Object Destroyed >> MYAPP onTrimMemory");

    }


    @Override
    public void onTerminate() {
        super.onTerminate();
        Utils.printLog("Api", "Object Destroyed >> MYAPP onTerminate");
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
            public void onActivityCreated(Activity activity, Bundle savedInstanceState) {
                Utils.runGC();
                // new activity created; force its orientation to portrait
                activity.setRequestedOrientation(
                        ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                activity.setTitle(getResources().getString(R.string.app_name));
                mMyApp.setCurrentActivity(activity);

                currentactivity = activity;

                activity.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            }

            @Override
            public void onActivityStarted(Activity activity) {
                Utils.runGC();
                mMyApp.setCurrentActivity(activity);

            }

            @Override
            public void onActivityResumed(Activity activity) {
                Utils.runGC();
                mMyApp.setCurrentActivity(activity);

                isAppInBackground = false;

                currentactivity = activity;
                if (!activity.getLocalClassName().equalsIgnoreCase("LauncherActivity") && generalFun.isLocationEnabled() == false && generalFun.retrieveValue("isInLauncher").equalsIgnoreCase("false")) {
                    // new GeneralFunctions(activity).restartApp();
                }
            }

            @Override
            public void onActivityPaused(Activity activity) {

                Utils.runGC();
                isAppInBackground = true;

                clearReferences();
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
                Utils.runGC();
                Utils.hideKeyboard(activity);
                clearReferences();

            }


        });
    }

    private void clearReferences() {
        Activity currActivity = mMyApp.getCurrentActivity();
        if (this.equals(currActivity))
            mMyApp.setCurrentActivity(null);
    }


}
