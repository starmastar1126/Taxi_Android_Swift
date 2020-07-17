package com.general.files;

import android.app.Service;
import android.content.Intent;
import android.location.Location;
import android.os.IBinder;
import android.support.annotation.Nullable;

import com.utils.Utils;

import java.util.HashMap;

/**
 * Created by Admin on 18-07-2016.
 */
public class UpdateDriverStatus extends Service implements UpdateFrequentTask.OnTaskRunCalled, GetLocationUpdates.LocationUpdates {

    UpdateFrequentTask updateDriverStatusTask;
    GetLocationUpdates getLastLocation;
    Location driverLocation;
    String iDriverId = "";
    ExecuteWebServerUrl currentExeTask;
    GeneralFunctions generalFunc;

    ConfigPubNub configPubNub;

    @Nullable
    @Override
    public IBinder onBind(Intent intent) {
        return null;
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        super.onStartCommand(intent, flags, startId);

        iDriverId = (new GeneralFunctions(this)).getMemberId();
        generalFunc = new GeneralFunctions(this);
        updateDriverStatusTask = new UpdateFrequentTask(2 * 60 * 1000);
        updateDriverStatusTask.setTaskRunListener(this);
        updateDriverStatusTask.startRepeatingTask();

        if (getLastLocation != null) {
            getLastLocation.stopLocationUpdates();
            getLastLocation = null;
        }

        getLastLocation = new GetLocationUpdates(this, Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, true, this);
        configPubNub = new ConfigPubNub(this, true);

        return Service.START_STICKY;
    }

    @Override
    public void onTaskRun() {
        updateOnlineAvailability("");
    }

    public void updateOnlineAvailability(String status) {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "updateDriverStatus");
        parameters.put("iDriverId", iDriverId);

        if (driverLocation != null) {
            parameters.put("latitude", "" + driverLocation.getLatitude());
            parameters.put("longitude", "" + driverLocation.getLongitude());
        }

        if (status.equals("Not Available")) {
            parameters.put("Status", "Not Available");
        }

        if (this.currentExeTask != null) {
            this.currentExeTask.cancel(true);
            this.currentExeTask = null;
            Utils.runGC();
        }

        parameters.put("isUpdateOnlineDate", "true");

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getApplicationContext(), parameters);
        this.currentExeTask = exeWebServer;
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

            }
        });
        exeWebServer.execute();
    }

    @Override
    public void onLocationUpdate(Location location) {
        this.driverLocation = location;

        updateLocationToPubNubBeforeTrip();
    }

    public void stopFreqTask() {



        Utils.printLog("Api", "is in stopFreqTask");
        if (updateDriverStatusTask != null) {
            updateDriverStatusTask.stopRepeatingTask();
            updateDriverStatusTask = null;
        }

        if (getLastLocation != null) {
            getLastLocation.stopLocationUpdates();
            getLastLocation = null;
        }


        Utils.runGC();
    }


    public void updateLocationToPubNubBeforeTrip() {
        if (configPubNub != null && driverLocation != null && driverLocation.getLongitude() != 0.0 && driverLocation.getLatitude() != 0.0) {
            configPubNub.publishMsg(generalFunc.getLocationUpdateChannel(), generalFunc.buildLocationJson(driverLocation));
        }
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        Utils.printLog("UpdateDriverStatus", "onDestroy >> Yes");


        stopFreqTask();
    }

    @Override
    public void onTaskRemoved(Intent rootIntent) {
        super.onTaskRemoved(rootIntent);
        Utils.printLog("UpdateDriverStatus", "OnTaskRemoved >> Yes");

        updateOnlineAvailability("Not Available");

        for (int i=0;i<100;i++)
        {

        }

        stopFreqTask();
    }
}
