package com.general.files;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

import com.fastcabtaxi.driver.MainActivity;
import com.utils.Utils;

/**
 * Created by Admin on 17-02-2017.
 */
public class BackgroundAppReceiver extends BroadcastReceiver {

    MyBackGroundService myBgService;
    MainActivity mainAct;
    Context mContext;

    public BackgroundAppReceiver(Context mContext) {
        this.mContext = mContext;

        if(mContext instanceof MyBackGroundService){
            myBgService = (MyBackGroundService) this.mContext;
        }
        if(mContext instanceof MainActivity){
            mainAct = (MainActivity) this.mContext;
        }
    }

    @Override
    public void onReceive(Context context, Intent intent) {

        Utils.printLog("BackgroundAppReceiver","OnReceive");
        if(myBgService != null){
            myBgService.configBackground();
        }if(mainAct != null){
            mainAct.configBackground();
        }
    }
}
