package com.fastcabtaxi.driver;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

import com.general.files.MyBackGroundService;
import com.general.files.StartActProcess;
import com.utils.Utils;

/**
 * Created by Admin on 27-01-2016.
 */
public class StartServiceReceiver extends BroadcastReceiver {

    @Override
    public void onReceive(Context context, Intent intent) {
        // TODO Auto-generated method stub
            new StartActProcess(context).startService(MyBackGroundService.class);

    }
}