package com.general.files;

import android.os.Handler;

import com.utils.Utils;

/**
 * Created by Admin on 06-07-2016.
 */
public class UpdateFrequentTask implements Runnable {
    int mInterval;
    OnTaskRunCalled onTaskRunCalled;
    boolean isAvoidFirstRun = false;
    boolean isTaskKilled = false;
    private Handler mHandler_UpdateTask;

    public UpdateFrequentTask(int mInterval) {
        this.mInterval = mInterval;
        mHandler_UpdateTask = new Handler();
    }

    public void avoidFirstRun() {
        this.isAvoidFirstRun = true;
    }

    public void changeInterval(int mInterval) {
        this.mInterval = mInterval;
    }


    public boolean isuPDATEtASK=false;

    @Override
    public void run() {
        Utils.printLog("updateFrequentTask", "Run"+"::"+isuPDATEtASK);

        if (isTaskKilled == true) {
            return;
        }
        if (onTaskRunCalled != null && isAvoidFirstRun == false) {
            onTaskRunCalled.onTaskRun();
        }

        if (isAvoidFirstRun == true) {
            isAvoidFirstRun = false;
        }

        mHandler_UpdateTask.postDelayed(this, mInterval);

    }

    public void startRepeatingTask() {
        isTaskKilled = false;
        this.run();
    }

    public void stopRepeatingTask() {

        Utils.printLog("Api", "Object Destroyed >> UpDateFreqTask  stopRepeatingTask updateDriverLocTask");

        Utils.printLog("Stopp", "yaaaa");
        isTaskKilled = true;
        mHandler_UpdateTask.removeCallbacks(this);
    }

    public void setTaskRunListener(OnTaskRunCalled onTaskRunCalled) {
        this.onTaskRunCalled = onTaskRunCalled;
    }

    public interface OnTaskRunCalled {
        void onTaskRun();
    }
}
