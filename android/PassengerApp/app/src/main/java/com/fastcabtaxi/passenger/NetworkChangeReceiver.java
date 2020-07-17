package com.fastcabtaxi.passenger;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

import com.general.files.InternetConnection;
import com.general.files.MyApp;

/**
 * Created by Admin on 31-08-2017.
 */

public class NetworkChangeReceiver extends BroadcastReceiver {


    private MyApp mApplication;

    @Override
    public void onReceive(Context context, Intent intent) {

        boolean status = new InternetConnection(context).isNetworkConnected();

        mApplication = ((MyApp) context.getApplicationContext());


        if (mApplication == null) {
            return;
        }

        try {
            Activity currentActivity = mApplication.getCurrentActivity();

            if (currentActivity != null && currentActivity.getLocalClassName().equalsIgnoreCase("MainActivity")) {
                if (!status) {

                    if (((MainActivity) currentActivity).noloactionview != null) {
                        ((MainActivity) currentActivity).setNoLocViewEnableOrDisabled(true);
                    }
                } else {
                    if (((MainActivity) currentActivity).noloactionview != null) {
                        ((MainActivity) currentActivity).setNoLocViewEnableOrDisabled(false);
                    }
                }

            }

        } catch (Exception e) {

        }

    }
}