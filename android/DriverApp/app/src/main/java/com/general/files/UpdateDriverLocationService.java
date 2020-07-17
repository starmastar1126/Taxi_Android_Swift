package com.general.files;

import android.app.Service;
import android.content.Intent;
import android.location.Location;
import android.os.IBinder;
import android.support.annotation.Nullable;

import com.utils.Utils;

import java.util.HashMap;

/**
 * Created by Admin on 20-07-2016.
 */
public class UpdateDriverLocationService extends Service implements UpdateFrequentTask.OnTaskRunCalled, GetLocationUpdates.LocationUpdates {

    UpdateFrequentTask updateDriverLocationsTask;
    GetLocationUpdates getLocationUpdates;
    Location driverLocation;
    Location lastPublishedLocation;
    String iDriverId = "";
    ExecuteWebServerUrl currentExeTask;

    int DRIVER_LOC_UPDATE_TIME_INTERVAL = 1 * 8 * 1000;

    ConfigPubNub configPubNub;
    GeneralFunctions generalFunc;

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

        String ENABLE_PUBNUB = generalFunc.retrieveValue(Utils.ENABLE_PUBNUB_KEY);
        String PAppVersion = "";
        if (intent != null) {
            PAppVersion = intent.getStringExtra("PAppVersion");
        }

        if (!ENABLE_PUBNUB.equalsIgnoreCase("Yes") || PAppVersion == null || !PAppVersion.equals("2")) {

            DRIVER_LOC_UPDATE_TIME_INTERVAL = 1 * (generalFunc.parseIntegerValue(8, generalFunc.retrieveValue("DRIVER_LOC_UPDATE_TIME_INTERVAL"))) * 1000;

            updateDriverLocationsTask = new UpdateFrequentTask(DRIVER_LOC_UPDATE_TIME_INTERVAL);
            updateDriverLocationsTask.setTaskRunListener(this);
            updateDriverLocationsTask.startRepeatingTask();
        } else {
            configPubNub = new ConfigPubNub(this, true);
        }

        if (getLocationUpdates != null) {
            getLocationUpdates.stopLocationUpdates();
            getLocationUpdates = null;
        }

        getLocationUpdates = new GetLocationUpdates(this, Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, true, this);

        return Service.START_STICKY;
    }

    @Override
    public void onTaskRun() {
        updateDriverLocations();
    }

    public void updateDriverLocations() {
        if (driverLocation == null) {
            return;
        }

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "updateDriverLocations");
        parameters.put("iDriverId", iDriverId);
        parameters.put("latitude", "" + driverLocation.getLatitude());
        parameters.put("longitude", "" + driverLocation.getLongitude());

        if (this.currentExeTask != null) {
            this.currentExeTask.cancel(true);
            this.currentExeTask = null;
            Utils.runGC();
        }

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getApplicationContext(), parameters);
        this.currentExeTask = exeWebServer;
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Api", "Update Locations Response ::" + responseString);
            }
        });
        exeWebServer.execute();
    }

    @Override
    public void onLocationUpdate(Location location) {
        this.driverLocation = location;

        if (configPubNub != null && generalFunc != null && driverLocation != null) {

            if (lastPublishedLocation == null || (lastPublishedLocation.distanceTo(driverLocation) > 2)) {
                lastPublishedLocation = driverLocation;
                configPubNub.publishMsg(generalFunc.getLocationUpdateChannel(), generalFunc.buildLocationJson(driverLocation, "LocationUpdateOnTrip"));
            }
        }
    }


    public void stopFreqTask() {
        if (updateDriverLocationsTask != null) {
            updateDriverLocationsTask.stopRepeatingTask();
            updateDriverLocationsTask = null;
        }

        if (configPubNub != null && generalFunc != null) {
            configPubNub.releaseInstances();
            configPubNub = null;
            Utils.runGC();
        }

        if (getLocationUpdates != null) {
            getLocationUpdates.stopLocationUpdates();
            getLocationUpdates = null;
        }
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        stopFreqTask();
    }

    @Override
    public void onTaskRemoved(Intent rootIntent) {
        super.onTaskRemoved(rootIntent);
        stopFreqTask();
    }
}
