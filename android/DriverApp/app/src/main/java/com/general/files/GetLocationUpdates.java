package com.general.files;

import android.content.Context;
import android.location.Location;
import android.os.Bundle;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.location.LocationListener;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationServices;

/**
 * Created by Admin on 27-06-2016.
 */

public class GetLocationUpdates implements GoogleApiClient.ConnectionCallbacks, GoogleApiClient.OnConnectionFailedListener, LocationListener {

    Context mContext;

    GeneralFunctions generalFunc;
    private int UPDATE_INTERVAL = 1000; // 1 sec
    private int FATEST_INTERVAL = 1000; // 0.5 sec
    private int DISPLACEMENT = 8; // 8 meters
    private GoogleApiClient mGoogleApiClient;
    private LocationRequest mLocationRequest;

    private LocationUpdates locationsUpdates;

    boolean isPermissionDialogShown = false;

//    private LastLocationListener LastLocationListener;

    Location mLastLocation;

    boolean isApiConnected = false;
    boolean isContinuousLocUpdates = false;

    /*public GetLocationUpdates(Context context, int displacement) {
        // TODO Auto-generated constructor stub
        this.mContext = context;
        this.DISPLACEMENT = displacement;
        buildGoogleApiClient();
        createLocationRequest();
    }*/

    public GetLocationUpdates(Context context, int displacement, boolean isContinuousLocUpdates, LocationUpdates locationsUpdates) {
        // TODO Auto-generated constructor stub
        this.mContext = context;
        this.DISPLACEMENT = displacement;
        this.locationsUpdates = locationsUpdates;
        this.isContinuousLocUpdates = isContinuousLocUpdates;

        generalFunc = new GeneralFunctions(mContext);

        createLocationRequest();
        buildGoogleApiClient();
    }

    protected synchronized void buildGoogleApiClient() {
        mGoogleApiClient = new GoogleApiClient.Builder(mContext).addConnectionCallbacks(this)
                .addOnConnectionFailedListener(this).addApi(LocationServices.API).build();
        mGoogleApiClient.connect();
    }


    protected void createLocationRequest() {
        mLocationRequest = new LocationRequest();
        mLocationRequest.setInterval(UPDATE_INTERVAL);
        mLocationRequest.setFastestInterval(FATEST_INTERVAL);
        mLocationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
        mLocationRequest.setSmallestDisplacement(DISPLACEMENT);
    }

    /*
  Starting the location updates
 */
    public void startLocationUpdates(boolean isContinuousLocUpdates) {

        this.isContinuousLocUpdates = isContinuousLocUpdates;

        boolean isLocationPermissionGranted = new GeneralFunctions(mContext).checkLocationPermission(isPermissionDialogShown);

        if (isLocationPermissionGranted == true) {
            try {
                LocationServices.FusedLocationApi.requestLocationUpdates(mGoogleApiClient, mLocationRequest, this);
            } catch (Exception e) {
                e.printStackTrace();
                if (mGoogleApiClient != null) {
                    mGoogleApiClient.connect();
                }
            }
        } else {
            isPermissionDialogShown = true;
        }

    }

    /**
     * Stopping location updates
     */
    public void stopLocationUpdates() {
        try {
            LocationServices.FusedLocationApi.removeLocationUpdates(mGoogleApiClient, this);
        } catch (Exception e) {

        }
    }

    public Location getLastLocation() {
        try {

            if (generalFunc.checkLocationPermission(true) && mGoogleApiClient != null) {
                try {
                    Location mLastLocation = LocationServices.FusedLocationApi.getLastLocation(mGoogleApiClient);
                    return mLastLocation;
                } catch (Exception e) {

                    return this.mLastLocation;
                }
            }
        } catch (Exception e) {

            return this.mLastLocation;
        }

        return this.mLastLocation;

    }

    @Override
    public void onLocationChanged(Location location) {
        // TODO Auto-generated method stub
//		Utils.printLog("Location changed", "changed");
        if (location == null) {
            return;
        }

        if (locationsUpdates != null) {
            locationsUpdates.onLocationUpdate(location);
        }

        this.mLastLocation = location;

        if (!isContinuousLocUpdates) {
            stopLocationUpdates();
        }
    }

    public Location getLocation() {
        return this.mLastLocation;
    }

    @Override
    public void onConnectionFailed(ConnectionResult arg0) {
        // TODO Auto-generated method stub

        if (mGoogleApiClient != null) {
            mGoogleApiClient.connect();
        }

    }

    @Override
    public void onConnected(Bundle arg0) {
        // TODO Auto-generated method stub
        try {

            isApiConnected = true;
            boolean isLocationPermissionGranted = new GeneralFunctions(mContext).checkLocationPermission(isPermissionDialogShown);

            if (isLocationPermissionGranted == true) {
                Location mLastLocation = LocationServices.FusedLocationApi.getLastLocation(mGoogleApiClient);
                if (locationsUpdates != null) {
                    locationsUpdates.onLocationUpdate(mLastLocation);
                }
                /*else if (LastLocationListener != null) {
                    LastLocationListener.onLastLocationUpdate(mLastLocation);
                }*/
            } else {
                isPermissionDialogShown = true;
            }

            startLocationUpdates(isContinuousLocUpdates);

        } catch (Exception e) {

        }

    }

    public boolean isApiConnected() {
        return this.isApiConnected;
    }

    @Override
    public void onConnectionSuspended(int arg0) {
        // TODO Auto-generated method stub
        if (mGoogleApiClient != null) {
            mGoogleApiClient.connect();
        }
    }

    public interface LocationUpdates {
        void onLocationUpdate(Location location);
    }

    public void setLocationUpdatesListener(LocationUpdates locationsUpdates) {
        this.locationsUpdates = locationsUpdates;
    }

    /*public LocationUpdates getLocationUpdatesListener() {
        return locationsUpdates;
    }

    public interface LastLocationListener {
        void onLastLocationUpdate(Location mLastLocation);
    }

    public void setLastLocationListener(LastLocationListener LastLocationListener) {
        this.LastLocationListener = LastLocationListener;
    }

    public void removeLocUpdateListener() {
        locationsUpdates = null;
    }*/
}

/*public class GetLocationUpdates implements android.location.LocationListener {

    private static final int TWO_MINUTES = 1000 * 60 * 2;
    Context mContext;
    boolean isPermissionDialogShown = false;
    Location mLastLocation;
    LocationManager locationManager;
    GeneralFunctions generalFunc;
    private int DISPLACEMENT = 8; // 10 meters
    private LocationUpdates locationsUpdates;
    private LastLocationListener lastLocationListener;
    public int UPDATE_INTERVAL = 400;

    private String provider = LocationManager.GPS_PROVIDER;
    private boolean isLocationUpdatesOn = true;

    public GetLocationUpdates(Context context, int displacement) {
        // TODO Auto-generated constructor stub
        this.mContext = context;
        this.DISPLACEMENT = displacement;
        *//*buildGoogleApiClient();
        createLocationRequest();*//*

        generalFunc = new GeneralFunctions(mContext);

        buildLocationManager();
    }

    public GetLocationUpdates(Context context, int displacement, boolean isLocationUpdatesOn, LocationUpdates locationsUpdates) {
        // TODO Auto-generated constructor stub
        this.mContext = context;
        this.DISPLACEMENT = displacement;
        this.isLocationUpdatesOn = isLocationUpdatesOn;
        this.locationsUpdates = locationsUpdates;
        *//*buildGoogleApiClient();
        createLocationRequest();*//*

        generalFunc = new GeneralFunctions(mContext);

        buildLocationManager();
    }

    public void buildLocationManager() {
        locationManager = (LocationManager) mContext.getSystemService(Context.LOCATION_SERVICE);

        startLocationUpdates(isLocationUpdatesOn);
    }

    public void startLocationUpdates(boolean isLocationUpdatesOn) {
        boolean isLocationPermissionGranted = generalFunc.checkLocationPermission(true);

        if (isLocationPermissionGranted && isLocationUpdatesOn) {
            locationManager.requestLocationUpdates(provider, UPDATE_INTERVAL, DISPLACEMENT, this);

            Location lastLoc = getLastLocation();
            if (lastLoc != null) {
                provider = LocationManager.GPS_PROVIDER;
                onLocationChanged(lastLoc);
            }

            Handler handler = new Handler();
            handler.postDelayed(new Runnable() {
                @Override
                public void run() {
                    Utils.printLog("LatLocation", "::" + getLastLocation());
                    Location lastLoc = getLastLocation();
                    if (lastLoc != null) {
                        provider = LocationManager.GPS_PROVIDER;
                        onLocationChanged(lastLoc);
                    }

                }
            }, 5000);
        }
    }

    public void stopLocationUpdates() {
        boolean isLocationPermissionGranted = generalFunc.checkLocationPermission(true);

        if (isLocationPermissionGranted) {
            locationManager.removeUpdates(this);
        }
    }

    public void removeLocUpdateListener() {
        this.locationsUpdates = null;
        this.lastLocationListener = null;
    }

    *//**
 * Determines whether one Location reading is better than the current Location fix
 *
 * @param location            The new Location that you want to evaluate
 * @param currentBestLocation The current Location fix, to which you want to compare the new one
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 * <p>
 * Checks whether two providers are the same
 *//*
    protected boolean isBetterLocation(Location location, Location currentBestLocation) {
        Utils.printLog("locationaccuracy", "::NewLocTime:" + location.getTime());
        if (currentBestLocation == null) {
            // A new location is always better than no location
            return true;
        }


        // Check whether the new location fix is newer or older
        long timeDelta = location.getTime() - currentBestLocation.getTime();
        boolean isSignificantlyNewer = timeDelta > TWO_MINUTES;
        boolean isSignificantlyOlder = timeDelta < -TWO_MINUTES;
        boolean isNewer = timeDelta > 0;

        // If it's been more than two minutes since the current location, use the new location
        // because the user has likely moved
        if (isSignificantlyNewer) {
            return true;
            // If the new location is more than two minutes older, it must be worse
        } else if (isSignificantlyOlder) {
            return false;
        }

        // Check whether the new location fix is more or less accurate
        int accuracyDelta = (int) (location.getAccuracy() - currentBestLocation.getAccuracy());
        boolean isLessAccurate = accuracyDelta > 0;
        boolean isMoreAccurate = accuracyDelta < 0;
        boolean isSignificantlyLessAccurate = accuracyDelta > 200;

        // Check if the old and new location are from the same driver
        boolean isFromSameProvider = isSameProvider(location.getProvider(),
                currentBestLocation.getProvider());

        // Determine location quality using a combination of timeliness and accuracy
        if (isMoreAccurate) {
            return true;
        } else if (isNewer && !isLessAccurate) {
            return true;
        } else if (isNewer && !isSignificantlyLessAccurate && isFromSameProvider) {
            return true;
        }
        return false;
    }

    *//**
 * Checks whether two providers are the same
 *//*
    private boolean isSameProvider(String provider1, String provider2) {
        if (provider1 == null) {
            return provider2 == null;
        }
        return provider1.equals(provider2);
    }

    public void setLocationUpdatesListener(LocationUpdates locationsUpdates) {
        this.locationsUpdates = locationsUpdates;

        if (this.mLastLocation != null) {
            onLocationChanged(this.mLastLocation);
        }
    }

    public void setLastLocationListener(LastLocationListener lastLocationListener) {
        this.lastLocationListener = lastLocationListener;


        if (this.mLastLocation != null) {
            onLocationChanged(this.mLastLocation);
        }
    }

    public Location getLastLocation() {
        if (locationManager == null) {
            buildLocationManager();
        }

        if (generalFunc.checkLocationPermission(true)) {

            Location lastKnownLocation = locationManager.getLastKnownLocation(provider);

            return lastKnownLocation;
        }

        return null;

    }

    @Override
    public void onLocationChanged(Location location) {

        if (isBetterLocation(location, this.mLastLocation)) {
            this.mLastLocation = location;

            Utils.printLog("Locationaccuracy", "::" + location.getAccuracy());

            Utils.printLog("lastKnownLocation::::", ":1:" + mLastLocation.getLatitude() + "::" + mLastLocation.getLongitude());
            if (locationsUpdates != null) {
                locationsUpdates.onLocationUpdate(location);
            }

            if (lastLocationListener != null) {
                lastLocationListener.onLastLocationUpdate(location);
            }

            if (isLocationUpdatesOn == false) {
                this.stopLocationUpdates();
            }
        }
    }

    @Override
    public void onStatusChanged(String s, int i, Bundle bundle) {

    }

    @Override
    public void onProviderEnabled(String s) {

    }

    @Override
    public void onProviderDisabled(String s) {

    }

    public interface LocationUpdates {
        void onLocationUpdate(Location location);
    }

    public interface LastLocationListener {
        //        void handleLastLocationListnerCallback(Location mLastLocation);
        void onLastLocationUpdate(Location mLastLocation);
//        void handleLastLocationListnerNOVALUECallback(int id);
    }
}*/