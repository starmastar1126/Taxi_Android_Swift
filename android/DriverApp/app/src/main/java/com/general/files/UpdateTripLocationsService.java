package com.general.files;

import android.app.Service;
import android.content.Intent;
import android.location.Location;
import android.os.Binder;
import android.os.IBinder;

import com.google.android.gms.maps.model.LatLng;
import com.utils.CommonUtilities;
import com.utils.Utils;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 20-07-2016.
 */
public class UpdateTripLocationsService extends Service implements UpdateFrequentTask.OnTaskRunCalled, GetLocationUpdates.LocationUpdates {
    private static String LOG_TAG = "UpdateTripLocationsService";

    UpdateFrequentTask updateDriverLocationsTask;
    GetLocationUpdates getLocationUpdates;
    Location driverLocation;
    String iDriverId = "";
    ExecuteWebServerUrl currentExeTask;

    int UPDATE_TIME_INTERVAL = 4 * 60 * 1000;
    int LOCATION_ACCURACY_METERS = 200;

    public ArrayList<LatLng> store_locations;

    public boolean tripIsEnd = false;

    public boolean IsdataUploading = false;

    String tripId = "";

    GeneralFunctions generalFunc;

    private IBinder mBinder = new MyBinder();

    long lastLocationUpdateTimeInMill = 0;

    private static final long MIN_TIME_BW_UPDATES = 1000 * 15 * 1;

    long waitingTime = 0;

    public class MyBinder extends Binder {
        public UpdateTripLocationsService getService() {
            return UpdateTripLocationsService.this;
        }
    }

    @Override
    public IBinder onBind(Intent intent) {
        Utils.printLog(LOG_TAG, "in onBind");
        return mBinder;
    }

    @Override
    public void onRebind(Intent intent) {
        Utils.printLog(LOG_TAG, "in onRebind");
        super.onRebind(intent);
    }

    @Override
    public boolean onUnbind(Intent intent) {
        Utils.printLog(LOG_TAG, "in onUnbind");
        return true;
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        super.onStartCommand(intent, flags, startId);
        generalFunc = new GeneralFunctions(this);

        return Service.START_STICKY;
    }

    public void startUpdate(String generatedTripId) {
        if (store_locations != null) {
            Utils.printLog("Obj", "Exist");
            return;
        }

        waitingTime = generalFunc.parseLongValue(0, generalFunc.retrieveValue(CommonUtilities.DriverWaitingTime));

        generalFunc.storedata(CommonUtilities.IsTripStarted, "Yes");

        this.tripId = generatedTripId;

        Utils.printLog("tripId", "::" + this.tripId);

        store_locations = new ArrayList<LatLng>();

        iDriverId = (new GeneralFunctions(this)).getMemberId();

        LOCATION_ACCURACY_METERS = generalFunc.parseIntegerValue(200, generalFunc.retrieveValue("LOCATION_ACCURACY_METERS"));

        updateDriverLocationsTask = new UpdateFrequentTask(UPDATE_TIME_INTERVAL);
        updateDriverLocationsTask.setTaskRunListener(this);


        if (getLocationUpdates != null) {
            getLocationUpdates.stopLocationUpdates();
            getLocationUpdates = null;
        }

        getLocationUpdates = new GetLocationUpdates(this, Utils.LOCATION_POST_MIN_DISTANCE_IN_MITERS, true, this);

        updateDriverLocationsTask.startRepeatingTask();

    }

    @Override
    public void onTaskRun() {
        updateDriverLocations();
    }

    public void updateDriverLocations() {

        if (store_locations.size() > 0 && IsdataUploading == false) {
            IsdataUploading = true;

            final ArrayList<LatLng> tempList = new ArrayList<>();
            tempList.addAll(store_locations);

            String store_locations_latitude_str = "";
            String store_locations_longitude_str = "";

            for (int i = 0; i < tempList.size(); i++) {

                double location_latitude = tempList.get(i).latitude;
                double location_longitude = tempList.get(i).longitude;

                if (i != store_locations.size() - 1) {
                    store_locations_latitude_str = store_locations_latitude_str + location_latitude + ",";
                    store_locations_longitude_str = store_locations_longitude_str + location_longitude + ",";
                } else {
                    store_locations_latitude_str = store_locations_latitude_str + location_latitude;
                    store_locations_longitude_str = store_locations_longitude_str + location_longitude;
                }

            }
            HashMap<String, String> parameters = new HashMap<String, String>();
            parameters.put("type", "updateTripLocations");
            parameters.put("TripId", tripId);
            parameters.put("latList", store_locations_latitude_str);
            parameters.put("lonList", store_locations_longitude_str);

           /* if (this.currentExeTask != null) {
                this.currentExeTask.cancel(true);
            }*/

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

                    Utils.printLog("Update Locations Response", "::" + responseString);
                    boolean isDataAvail = generalFunc.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        for (int i = 0; i < tempList.size(); i++) {
                            store_locations.remove(0);
                        }
                    }

                    IsdataUploading = false;
                }
            });
            exeWebServer.execute();
        }


    }

    @Override
    public void onLocationUpdate(Location location) {


        if (location != null /*&& location.getAccuracy() < LOCATION_ACCURACY_METERS*/ ) {
            double location_latitude = location.getLatitude();
            double location_longitude = location.getLongitude();

            store_locations.add(new LatLng(location_latitude, location_longitude));

        }
        this.driverLocation = location;


        if (lastLocationUpdateTimeInMill == 0) {
            lastLocationUpdateTimeInMill = System.currentTimeMillis();
        } else {
            long currentTimeInMill = System.currentTimeMillis();

            if ((currentTimeInMill - lastLocationUpdateTimeInMill) > MIN_TIME_BW_UPDATES) {
                waitingTime = waitingTime + (currentTimeInMill - lastLocationUpdateTimeInMill);
                lastLocationUpdateTimeInMill = currentTimeInMill;
            }

            generalFunc.storedata(CommonUtilities.DriverWaitingTime, "" + waitingTime);
        }
    }

    public void stopFreqTask() {
        if (updateDriverLocationsTask != null) {
            updateDriverLocationsTask.stopRepeatingTask();
        }

        if (getLocationUpdates != null) {
            getLocationUpdates.stopLocationUpdates();
            getLocationUpdates = null;
        }
    }

    void startFreqTask() {
        if (updateDriverLocationsTask != null) {
            updateDriverLocationsTask.startRepeatingTask();
        }

        if (getLocationUpdates != null) {
            getLocationUpdates.stopLocationUpdates();
            getLocationUpdates = null;
        }

        getLocationUpdates = new GetLocationUpdates(this, Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, true, this);

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

    public void endTrip() {
        tripIsEnd = true;
        generalFunc.storedata(CommonUtilities.IsTripStarted, "No");
        stopFreqTask();
    }

    public void tripEndRevoked() {
        tripIsEnd = false;

        startFreqTask();
    }

    public ArrayList<LatLng> getListOfLocations() {

        while (IsdataUploading == true) {
            // Utils.printLog("Waiting", "yes");
        }
        return store_locations;
    }
}
