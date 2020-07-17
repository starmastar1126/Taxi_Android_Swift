package com.general.files;

import android.app.ActivityManager;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.graphics.BitmapFactory;
import android.os.Build;
import android.os.PowerManager;
import android.support.v4.app.NotificationCompat;

import com.fastcabtaxi.driver.CabRequestedActivity;
import com.fastcabtaxi.driver.R;
import com.utils.CommonUtilities;
import com.utils.Utils;

import java.util.List;

/**
 * Created by Admin on 12-07-2016.
 */
public class GcmBroadCastReceiver extends BroadcastReceiver {
    GeneralFunctions generalFunc;


    @Override
    public void onReceive(Context context, Intent intent) {
        if (generalFunc == null) {
            generalFunc = new GeneralFunctions(context);
        }

        if (intent.getAction().equals(CommonUtilities.passenger_message_arrived_intent_action) && intent != null) {
            String json_message = intent.getExtras().getString(CommonUtilities.passenger_message_arrived_intent_key);
//            mainAct.onGcmMessageArrived(message);

            ActivityManager am = (ActivityManager) context.getSystemService(context.ACTIVITY_SERVICE);

            List<ActivityManager.RunningTaskInfo> taskInfo = am.getRunningTasks(1);

            ComponentName componentInfo = taskInfo.get(0).topActivity;
            String packageName = componentInfo.getPackageName();

            PowerManager powerManager = (PowerManager) context.getSystemService(Context.POWER_SERVICE);
            boolean isScreenOn = powerManager.isScreenOn();

            String codeKey = CommonUtilities.DRIVER_REQ_CODE_PREFIX_KEY + generalFunc.getJsonValue("MsgCode", json_message);

//            if (generalFunc.isTripStatusMsgExist(json_message)) {
//                return;
//            }


            if (generalFunc.retrieveValue(codeKey).equals("")) {

                String MessageData = generalFunc.getJsonValue("Message", json_message);

                if (MessageData.equals("CabRequested")) {


                    if (!generalFunc.getJsonValue("tSessionId", json_message).equals("")) {
                        if (!generalFunc.getJsonValue("tSessionId", json_message).equals(generalFunc.retrieveValue(Utils.SESSION_ID_KEY))) {

                            return;

                        }
                    }


                    if (packageName.equals("com.fastcabtaxi.driver")) {


                        Utils.printLog("isScreenOn", "isScreenOn::" + isScreenOn);
                        if (isScreenOn == false) {

                            Utils.printLog("canDrawOverlayViews", generalFunc.canDrawOverlayViews(context) + "");
                            if (generalFunc.canDrawOverlayViews(context) == true) {
                                Utils.printLog("permission", "" + generalFunc.canDrawOverlayViews(context));
                                OpenWindowDialScreen(context, json_message);
                            } else {
                                generateNotification_callingFromUser(context, json_message);
                            }


                        } else {

                            Intent show_timer = new Intent();
                            show_timer.setClass(context, CabRequestedActivity.class);
                            show_timer.putExtra("Message", json_message);

                            show_timer.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                            context.startActivity(show_timer);

//                            Bundle bn = new Bundle();
//                            bn.putString("Message",json_message);
//                            Utils.printLog("Dismiss","Start");
//                            (new StartActProcess(context)).startActWithData(CabRequestedActivity.class,bn);
                        }

                    } else {
                        Utils.printLog("isScreenOn", "" + isScreenOn);
                        if (generalFunc.canDrawOverlayViews(context) == true) {
                            Utils.printLog("permission", "" + generalFunc.canDrawOverlayViews(context));
                            OpenWindowDialScreen(context, json_message);
                        } else {
                            generateNotification_callingFromUser(context, json_message);
                        }
                    }
                }

                generalFunc.storedata(codeKey, "true");

            }

        }
    }

    public void OpenWindowDialScreen(final Context context, final String message) {
//        context.startService(new Intent(context, ChatHeadService.class));
        Utils.generateNotification(context, generalFunc.retrieveLangLBl("", "LBL_TRIP_USER_WAITING"));
        MyApp.getInstance().stopAlertService();

        Utils.printLog("Window", "OPEN");
        generalFunc.storedata(CommonUtilities.DRIVER_ACTIVE_REQ_MSG_KEY /*+ msgCode*/, message);

        Intent it = new Intent(context, ChatHeadService.class);
        it.putExtra("Message", message);
        context.startService(it);

//        WindowManager.LayoutParams params = new WindowManager.LayoutParams(
//                WindowManager.LayoutParams.WRAP_CONTENT,
//                WindowManager.LayoutParams.WRAP_CONTENT,
//                WindowManager.LayoutParams.TYPE_PHONE,
//                WindowManager.LayoutParams.FLAG_NOT_FOCUSABLE | WindowManager.LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH | WindowManager.LayoutParams.FLAG_LAYOUT_NO_LIMITS,
//                PixelFormat.TRANSLUCENT);
//        params.gravity = Gravity.TOP | Gravity.LEFT;
//        params.x = 15;
//        params.y = 100;
//
//        final LayoutInflater mInflater = (LayoutInflater)
//                context.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);
//        final View notificationView = mInflater.inflate(R.layout.design_window_notification, null);
//        notificationView.setVisibility(View.VISIBLE);
//
//        notificationView.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View view) {
//                Bundle bundle = new Bundle();
//                bundle.putString("Message", "" + message);
//                new StartActProcess(context).startActWithData(CabRequestedActivity.class, bundle);
////                view.setVisibility(View.GONE);
////                windowManager.removeView(notificationView);
//            }
//        });
//
//        final WindowManager  windowManager = (WindowManager) context.getSystemService(context.WINDOW_SERVICE);
//        windowManager.addView(notificationView, params);
//        WindowManager.LayoutParams paramRemove = new WindowManager.LayoutParams(
//                WindowManager.LayoutParams.WRAP_CONTENT,
//                WindowManager.LayoutParams.WRAP_CONTENT,
//                WindowManager.LayoutParams.TYPE_SYSTEM_ALERT,
//                WindowManager.LayoutParams.FLAG_NOT_FOCUSABLE | WindowManager.LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH | WindowManager.LayoutParams.FLAG_LAYOUT_NO_LIMITS
//                |WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED
//                        | WindowManager.LayoutParams.FLAG_TURN_SCREEN_ON
//                        | WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON,
//                PixelFormat.TRANSLUCENT);
//
////        windowManager = (WindowManager) context.getSystemService(WINDOW_SERVICE);
////        //here is all the science of params
////        final LayoutParams myParams = new LayoutParams(
////                WindowManager.LayoutParams.WRAP_CONTENT,
////                WindowManager.LayoutParams.WRAP_CONTENT,
////                LayoutParams.TYPE_SYSTEM_ERROR,
////                WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED
////                        | WindowManager.LayoutParams.FLAG_TURN_SCREEN_ON
////                        | WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON,
////                PixelFormat.TRANSLUCENT
////        );
//        paramRemove.gravity = Gravity.TOP | Gravity.LEFT;
//        final LayoutInflater mInflater = (LayoutInflater)
//                context.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);
//        final View view = mInflater.inflate(R.layout.design_window_notification, null);
//        WindowManager wm = (WindowManager) context.getSystemService(context.WINDOW_SERVICE);
//        wm.addView(view, paramRemove);

//        WindowManager.LayoutParams windowManagerParams = new WindowManager.LayoutParams(WindowManager.LayoutParams.TYPE_SYSTEM_OVERLAY ,
//                WindowManager.LayoutParams. FLAG_DIM_BEHIND, PixelFormat.TRANSLUCENT);
//
//
//
//        WindowManager wm = (WindowManager) context.getSystemService(context.WINDOW_SERVICE);
//
//        LayoutInflater inflater = (LayoutInflater) context.getSystemService(context.LAYOUT_INFLATER_SERVICE);
//        View myView = inflater.inflate(R.layout.activity_cab_requested, null);
//
//        wm.addView(myView, windowManagerParams);
//        windowManager = (WindowManager) context.getSystemService(context.WINDOW_SERVICE);
//        final LayoutInflater mInflater = (LayoutInflater)
//                context.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);
//
//        WindowManager.LayoutParams params = new WindowManager.LayoutParams(
//                WindowManager.LayoutParams.MATCH_PARENT,
//                WindowManager.LayoutParams.WRAP_CONTENT,
//                WindowManager.LayoutParams.TYPE_PHONE,
//                WindowManager.LayoutParams.FLAG_NOT_FOCUSABLE,
//                PixelFormat.TRANSLUCENT);
//
//        params.gravity = Gravity.TOP | Gravity.CENTER;
//        params.x = 5;
//        params.y = 100;
//
//        final View view = mInflater.inflate(R.layout.design_window_notification, null);
//
//        final LinearLayout linearLayout = (LinearLayout) view.findViewById(R.id.windowLayout);
//        final RelativeLayout popupView = (RelativeLayout) view.findViewById(R.id.popupView);
//
//        MTextView waitingTxtView = (MTextView) view.findViewById(R.id.waitingTxtView);
//        waitingTxtView.setText(generalFunc.retrieveLangLBl("", "LBL_TRIP_USER_WAITING"));
//

//
//        windowManager.addView(view, params);
    }

    private void generateNotification_callingFromUser(Context context, String message) {
      //  WakeLocker.acquire(context);

        int icon = R.mipmap.ic_launcher;
        String title = context.getString(R.string.app_name);
        Intent notificationIntent = new Intent(context, CabRequestedActivity.class);

        notificationIntent.putExtra("Message", "" + message);
        notificationIntent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
        PendingIntent intent = PendingIntent.getActivity(context, 0, notificationIntent,
                PendingIntent.FLAG_UPDATE_CURRENT);


        NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(context);

        if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            mBuilder.setSmallIcon(R.drawable.ic_stat_driver_logo);
            mBuilder.setLargeIcon(BitmapFactory.decodeResource(context.getResources(),
                    R.mipmap.ic_launcher));
            mBuilder.setContentTitle(title).setContentText(generalFunc.retrieveLangLBl("Passenger is waiting For you", "LBL_TRIP_USER_WAITING")).setContentIntent(intent);
            mBuilder.setDefaults(Notification.DEFAULT_ALL).setAutoCancel(true);
            mBuilder.setColor(context.getResources().getColor(R.color.appThemeColor_1));
        } else {
            mBuilder.setSmallIcon(R.drawable.ic_stat_driver_logo);
            mBuilder.setLargeIcon(BitmapFactory.decodeResource(context.getResources(),
                    R.mipmap.ic_launcher));
            mBuilder.setContentTitle(title).setContentText(generalFunc.retrieveLangLBl("Passenger is waiting For you", "LBL_TRIP_USER_WAITING")).setContentIntent(intent);
            mBuilder.setDefaults(Notification.DEFAULT_ALL).setAutoCancel(true);
            mBuilder.setColor(context.getResources().getColor(R.color.appThemeColor_1));
        }
        NotificationManager notificationmanager = (NotificationManager) context
                .getSystemService(Context.NOTIFICATION_SERVICE);

        notificationmanager.notify(Utils.NOTIFICATION_ID, mBuilder.build());
//        notificationmanager.notify(Utils.randInt(1,9), mBuilder.build());

        Utils.printLog("Data", "Store");
        String msgCode = generalFunc.getJsonValue("MsgCode", message);
        generalFunc.storedata(CommonUtilities.DRIVER_ACTIVE_REQ_MSG_KEY/* + msgCode*/, message);


       // WakeLocker.release();
    }

}
