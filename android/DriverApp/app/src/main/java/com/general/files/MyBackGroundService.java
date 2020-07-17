package com.general.files;

import android.app.ActivityManager;
import android.app.AlarmManager;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.Service;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.graphics.BitmapFactory;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.os.SystemClock;
import android.support.v4.app.NotificationCompat;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.RemoteViews;

import com.fastcabtaxi.driver.R;
import com.utils.CommonUtilities;
import com.utils.Utils;

import java.util.HashMap;

/**
 * Created by Admin on 20-01-2016.
 */
public class MyBackGroundService extends Service implements UpdateFrequentTask.OnTaskRunCalled {

    ConfigPubNub configPubNub;

    GeneralFunctions generalFunc;
    BackgroundAppReceiver bgAppReceiver;

    private WindowManager windowManager;
    private ImageView chatHead;

    public static String OPEN_APP_BTN = "com.Intent.Action.OPEN_APP";
    public static String GO_OFFLINE_BTN = "com.Intent.Action.GO_OFFLINE";
    public static String KEEP_ONLINE_BTN = "com.Intent.Action.KEEP_ONLINE";
    public static int KEEP_ONLINE_REQ_CODE = 789;
    public static int GO_OFFLINE_REQ_CODE = 790;
    public static int OPEN_APP_REQ_CODE = 791;

    GcmBroadCastReceiver gcmBroadCastReceiver;

    Handler handler;

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        // TODO do something useful
        super.onStartCommand(intent, flags, startId);

        generalFunc = new GeneralFunctions(getServiceContext());
        generalFunc.sendHeartBeat();

        UpdateFrequentTask freqTask = new UpdateFrequentTask(2 * 60 * 1000);
        freqTask.setTaskRunListener(this);
        freqTask.startRepeatingTask();

        registerReceiver();
        configBackground();
        return Service.START_STICKY;

    }

    public void registerReceiver() {
        BackgroundAppReceiver bgAppReceiver = new BackgroundAppReceiver(getServiceContext());

        this.bgAppReceiver = bgAppReceiver;
        IntentFilter filter = new IntentFilter();
        filter.addAction(CommonUtilities.BACKGROUND_APP_RECEIVER_INTENT_ACTION);

        registerReceiver(bgAppReceiver, filter);
    }

    public void unRegisterReceiver() {
        if (bgAppReceiver != null) {
            try {
                unregisterReceiver(bgAppReceiver);
            } catch (Exception e) {

            }

            bgAppReceiver = null;
        }
    }

    public void configBackground() {


        if (handler == null) {
            handler = new Handler();
        } else {
            handler.removeCallbacks(myRunnable);
            handler.removeCallbacksAndMessages(null);

            handler = new Handler();
        }
        handler.postDelayed(myRunnable, 1000);
//        if(Utils.isApplicationBroughtToBackground(getServiceContext()) && !generalFunc.getMemberId().equals("")
//                        && generalFunc.retrieveValue(CommonUtilities.DRIVER_ONLINE_KEY).equals("true") ){
//                    Utils.printLog("AppBackground","Yes");
//                    if(generalFunc.retrieveValue(Utils.ENABLE_PUBNUB_KEY).equalsIgnoreCase("Yes")){
//                        initializePubNub();
//                    }
//
//                    registerGCMReceiver();
//                    Utils.generateNotification(MyBackGroundService.this,generalFunc.retrieveLangLBl("App is in background.","LBL_APP_IN_BG"));
//
//                }else{
//                    Utils.printLog("AppBackground","No");
//
//                    unRegisterGCMReceiver();
//                    releasePubNub();
//
//                }

    }

    Runnable myRunnable = new Runnable() {
        public void run() {
            //Some interesting task
            if (getApp().isMyAppInBackGround() == true && !generalFunc.getMemberId().equals("")
                    && generalFunc.retrieveValue(CommonUtilities.DRIVER_ONLINE_KEY).equals("true")) {
                Utils.printLog("AppBackground", "Yes");
                if (generalFunc.retrieveValue(Utils.ENABLE_PUBNUB_KEY).equalsIgnoreCase("Yes")) {
                    initializePubNub();
                }

                registerGCMReceiver();
                // Utils.generateNotification(MyBackGroundService.this, generalFunc.retrieveLangLBl("App is in background.", "LBL_APP_IN_BG"));
//			   fireNotification();
            } else {

                Utils.printLog("AppBackground", "No");

                unRegisterGCMReceiver();
                releasePubNub();
            }
        }
    };

    public MyApp getApp() {
        return ((MyApp) getApplication());
    }


    public Context getServiceContext() {
        return MyBackGroundService.this;
    }

    @Override
    public IBinder onBind(Intent intent) {
        // TODO for communication return IBinder implementation
        return null;
    }

    private boolean isMyServiceRunning(Class<?> serviceClass) {
        ActivityManager manager = (ActivityManager) getSystemService(Context.ACTIVITY_SERVICE);
        for (ActivityManager.RunningServiceInfo service : manager.getRunningServices(Integer.MAX_VALUE)) {
            if (serviceClass.getName().equals(service.service.getClassName())) {
                return true;
            }
        }
        return false;
    }

    @Override
    public void onDestroy() {
        // TODO Auto-generated method stub
        super.onDestroy();
        Utils.printLog("AppBackground", "OnDestroy >> Yes");

        Intent restartServiceIntent = new Intent(getApplicationContext(), this.getClass());
        restartServiceIntent.setPackage(getPackageName());

        PendingIntent restartServicePendingIntent = PendingIntent.getService(getApplicationContext(), 1,
                restartServiceIntent, PendingIntent.FLAG_ONE_SHOT);
        AlarmManager alarmService = (AlarmManager) getApplicationContext().getSystemService(Context.ALARM_SERVICE);
        alarmService.set(AlarmManager.ELAPSED_REALTIME, SystemClock.elapsedRealtime() + 1000,
                restartServicePendingIntent);

        releaseAllTask();

    }

    @Override
    public boolean onUnbind(Intent intent) {
        // TODO Auto-generated method stub

        Utils.printLog("AppBackground", "OnUnBind >> Yes");
        Intent restartServiceIntent = new Intent(getApplicationContext(), this.getClass());
        restartServiceIntent.setPackage(getPackageName());

        PendingIntent restartServicePendingIntent = PendingIntent.getService(getApplicationContext(), 1,
                restartServiceIntent, PendingIntent.FLAG_ONE_SHOT);
        AlarmManager alarmService = (AlarmManager) getApplicationContext().getSystemService(Context.ALARM_SERVICE);
        alarmService.set(AlarmManager.ELAPSED_REALTIME, SystemClock.elapsedRealtime() + 1000,
                restartServicePendingIntent);

        releaseAllTask();

        return super.onUnbind(intent);

    }

    @Override
    public void onTaskRemoved(Intent rootIntent) {
        /*
        Intent restartServiceIntent = new Intent(getApplicationContext(), this.getClass());
        restartServiceIntent.setPackage(getPackageName());

        PendingIntent restartServicePendingIntent = PendingIntent.getService(getApplicationContext(), 1,
                restartServiceIntent, PendingIntent.FLAG_ONE_SHOT);
        AlarmManager alarmService = (AlarmManager) getApplicationContext().getSystemService(Context.ALARM_SERVICE);
        alarmService.set(AlarmManager.ELAPSED_REALTIME, SystemClock.elapsedRealtime() + 1000,
                restartServicePendingIntent);*/

        releaseAllTask();

        this.stopSelf();

        super.onTaskRemoved(rootIntent);

        Utils.printLog("AppBackground", "OnTaskRemoved >> Yes");

    }



    public void releaseAllTask() {

        releasePubNub();
        unRegisterReceiver();

        unRegisterGCMReceiver();
    }

    @Override
    public void onTaskRun() {
        generalFunc.sendHeartBeat();

        configBackground();
    }

    public void releasePubNub() {
        if (configPubNub != null) {
            try {
                configPubNub.releaseInstances();
            } catch (Exception e) {

            }

            configPubNub = null;
        }
    }

    public void initializePubNub() {
        releasePubNub();
        if (configPubNub == null && generalFunc.retrieveValue(Utils.ENABLE_PUBNUB_KEY).equalsIgnoreCase("Yes")
                && !generalFunc.retrieveValue(CommonUtilities.PUBNUB_SUB_KEY).equals("")
                && generalFunc.retrieveValue(CommonUtilities.PUBNUB_SUB_KEY).trim().length() > 1) {
            configPubNub = new ConfigPubNub(getServiceContext());
            configPubNub.subscribeToCabRequestChannel();

            Utils.printLog("Intitialize", "PubNub");
        }
    }

    public void registerGCMReceiver() {
        if (gcmBroadCastReceiver == null) {
            gcmBroadCastReceiver = new GcmBroadCastReceiver();
        }
        unRegisterGCMReceiver();
        IntentFilter filter = new IntentFilter();
        filter.addAction(CommonUtilities.passenger_message_arrived_intent_action);

        registerReceiver(gcmBroadCastReceiver, filter);
    }

    public void unRegisterGCMReceiver() {
        if (gcmBroadCastReceiver != null) {
            try {
                unregisterReceiver(gcmBroadCastReceiver);
            } catch (Exception e) {

            }
        }
    }

    public void fireNotification() {
        WakeLocker.acquire(getServiceContext());


//        Intent onlineIntent = new Intent(getServiceContext(), NotificationClickReceiver.class);
//        onlineIntent.setAction(KEEP_ONLINE_BTN);
//        onlineIntent.putExtra("TYPE","ONLINE");
//        PendingIntent pendingIntentYes = PendingIntent.getBroadcast(this, KEEP_ONLINE_REQ_CODE, onlineIntent, PendingIntent.FLAG_UPDATE_CURRENT);
//        mBuilder.addAction(0, "Keep Online", pendingIntentYes);
        RemoteViews remoteViews = new RemoteViews(getServiceContext().getPackageName(),
                R.layout.background_notification_design);

        Intent offlineIntent = new Intent(getServiceContext(), NotificationClickReceiver.class);
        offlineIntent.setAction(GO_OFFLINE_BTN);
        offlineIntent.putExtra("TYPE", "OFFLINE");
        PendingIntent pendingIntentOffline = PendingIntent.getBroadcast(this, GO_OFFLINE_REQ_CODE, offlineIntent, PendingIntent.FLAG_UPDATE_CURRENT);

        Intent appIntent = new Intent(getServiceContext(), NotificationClickReceiver.class);
        appIntent.setAction(OPEN_APP_BTN);
        appIntent.putExtra("TYPE", "OPEN");
        PendingIntent appPendingIntent = PendingIntent.getBroadcast(this, OPEN_APP_REQ_CODE, appIntent, PendingIntent.FLAG_UPDATE_CURRENT);

//        remoteViews.setOnClickPendingIntent(R.id.goOfflineBtn, pendingIntentOffline);


        NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(getServiceContext());

        if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            mBuilder.setSmallIcon(R.drawable.ic_stat_driver_logo);
            mBuilder.setLargeIcon(BitmapFactory.decodeResource(getServiceContext().getResources(),
                    R.mipmap.ic_launcher));
            mBuilder.setContentTitle(getServiceContext().getString(R.string.app_name)).setContentText("App goes into background");
            mBuilder.setDefaults(Notification.DEFAULT_ALL).setAutoCancel(true);
            mBuilder.setOngoing(true);
            mBuilder.setVisibility(NotificationCompat.VISIBILITY_PUBLIC);
            mBuilder.setDefaults(Notification.DEFAULT_ALL);
            mBuilder.addAction(0, "Go Offline", pendingIntentOffline);
            mBuilder.addAction(0, "Open App", appPendingIntent);
            mBuilder.setColor(getServiceContext().getResources().getColor(R.color.appThemeColor_1));
        } else {
            mBuilder.setSmallIcon(R.drawable.ic_stat_driver_logo);
            mBuilder.setLargeIcon(BitmapFactory.decodeResource(getServiceContext().getResources(),
                    R.mipmap.ic_launcher));
            mBuilder.setContentTitle(getServiceContext().getString(R.string.app_name)).setContentText("App goes into background");
            mBuilder.setDefaults(Notification.DEFAULT_ALL).setAutoCancel(true);
            mBuilder.setOngoing(true);
            mBuilder.setVisibility(NotificationCompat.VISIBILITY_PUBLIC);
            mBuilder.setDefaults(Notification.DEFAULT_ALL);
            mBuilder.addAction(0, "Go Offline", pendingIntentOffline);
            mBuilder.addAction(0, "Open App", appPendingIntent);
            mBuilder.setColor(getServiceContext().getResources().getColor(R.color.appThemeColor_1));
        }


//        remoteViews.setOnClickPendingIntent(R.id.goOfflineBtn, pendingIntentOffline);

//        Notification foregroundNote = mBuilder.setSmallIcon(R.mipmap.ic_launcher)
//                .setTicker("Hello")
//                .setContentTitle("Hello")
//                .setContentText("Hello").setPriority(2)
//                .setDefaults(Notification.DEFAULT_ALL).setContent(remoteViews)
//                .setContentIntent(pendingIntentOffline).build();
//        foregroundNote.priority = Notification.PRIORITY_HIGH;
//
//        foregroundNote.bigContentView = remoteViews;

        NotificationManager notificationmanager = (NotificationManager) getServiceContext()
                .getSystemService(Context.NOTIFICATION_SERVICE);

        notificationmanager.notify(Utils.NOTIFICATION_BACKGROUND_ID, mBuilder.build());

        WakeLocker.release();
    }
}

