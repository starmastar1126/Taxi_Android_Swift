package com.general.files;

import android.content.Context;
import android.location.Location;

import com.fastcabtaxi.passenger.R;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.model.Polyline;
import com.google.android.gms.maps.model.PolylineOptions;
import com.utils.CommonUtilities;
import com.utils.Utils;

import org.json.JSONArray;
import org.json.JSONObject;

/**
 * Created by Admin on 02-08-2017.
 */

//public class UpdateDirections implements GetLocationUpdates.LocationUpdates, UpdateFrequentTask.OnTaskRunCalled {
public class UpdateDirections implements UpdateFrequentTask.OnTaskRunCalled {

    public GoogleMap googleMap;
    public Location destinationLocation;
    public Context mcontext;
    public Location userLocation;

    GeneralFunctions generalFunctions;

    String serverKey;
    Polyline route_polyLine;

    GetLocationUpdates getLocUpdates;
    UpdateFrequentTask updateFreqTask;

    String gMapLngCode = "en";
    String userProfileJson = "";
    String eUnit = "KMs";

    public UpdateDirections(Context mcontext, GoogleMap googleMap, Location userLocation, Location destinationLocation) {
        this.googleMap = googleMap;
        this.destinationLocation = destinationLocation;
        this.mcontext = mcontext;
        this.userLocation = userLocation;

        generalFunctions = new GeneralFunctions(mcontext);

        serverKey = mcontext.getResources().getString(R.string.google_api_get_address_from_location_serverApi);

        gMapLngCode = generalFunctions.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY);

        userProfileJson = generalFunctions.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        eUnit = generalFunctions.getJsonValue("eUnit", userProfileJson);
    }

    public void scheduleDirectionUpdate() {

        String DESTINATION_UPDATE_TIME_INTERVAL = generalFunctions.retrieveValue("DESTINATION_UPDATE_TIME_INTERVAL");
        updateFreqTask = new UpdateFrequentTask((int) (generalFunctions.parseDoubleValue(2, DESTINATION_UPDATE_TIME_INTERVAL) * 60 * 1000));
        updateFreqTask.setTaskRunListener(this);
        updateFreqTask.startRepeatingTask();
    }

    public void releaseTask() {
        if (updateFreqTask != null) {
            updateFreqTask.stopRepeatingTask();
            updateFreqTask = null;
        }

        if (getLocUpdates != null) {
            getLocUpdates.stopLocationUpdates();
            getLocUpdates = null;
        }

        Utils.runGC();
    }


    public void updateDirections() {

        if (userLocation == null || destinationLocation == null) {
            return;
        }

        String originLoc = userLocation.getLatitude() + "," + userLocation.getLongitude();
        String destLoc = destinationLocation.getLatitude() + "," + destinationLocation.getLongitude();

        String directionURL = "https://maps.googleapis.com/maps/api/directions/json?origin=" + originLoc + "&destination=" + destLoc + "&sensor=true&key=" + serverKey + "&language=" + gMapLngCode + "&sensor=true";

        if (userProfileJson != null && !eUnit.equalsIgnoreCase("KMs")) {
            directionURL = directionURL + "&units=imperial";
        }

        String trip_data = generalFunctions.getJsonValue("TripDetails", userProfileJson);

        String eTollSkipped = generalFunctions.getJsonValue("eTollSkipped", trip_data);

        if (eTollSkipped == "Yes") {
            directionURL = directionURL + "&avoid=tolls";
        }

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mcontext, directionURL, true);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    String status = generalFunctions.getJsonValue("status", responseString);

                    if (status.equals("OK")) {

                        JSONArray obj_routes = generalFunctions.getJsonArray("routes", responseString);
                        if (obj_routes != null && obj_routes.length() > 0) {
                            JSONObject obj_legs = generalFunctions.getJsonObject(generalFunctions.getJsonArray("legs", generalFunctions.getJsonObject(obj_routes, 0).toString()), 0);


                            String distance = "" + generalFunctions.getJsonValue("value",
                                    generalFunctions.getJsonValue("distance", obj_legs.toString()).toString());
                            String time = "" + generalFunctions.getJsonValue("text",
                                    generalFunctions.getJsonValue("duration", obj_legs.toString()).toString());

                            double distance_final = generalFunctions.parseDoubleValue(0.0, distance);


                            if (userProfileJson != null && !generalFunctions.getJsonValue("eUnit", userProfileJson).equalsIgnoreCase("KMs")) {
                                distance_final = distance_final * 0.000621371;
                            } else {
                                distance_final = distance_final * 0.00099999969062399994;
                            }

                            distance_final = generalFunctions.round(distance_final, 2);
                        }


                        if (googleMap != null) {

                            PolylineOptions lineOptions = generalFunctions.getGoogleRouteOptions(responseString, Utils.dipToPixels(mcontext, 5), mcontext.getResources().getColor(R.color.appThemeColor_2));

                            if (lineOptions != null) {
                                if (route_polyLine != null) {
                                    route_polyLine.remove();
                                }
                                route_polyLine = googleMap.addPolyline(lineOptions);

                            }
                        }

                    }

                }
            }
        });
        exeWebServer.execute();
    }

    @Override
    public void onTaskRun() {
        Utils.runGC();
        updateDirections();
    }

    public void changeUserLocation(Location location) {
        if (location != null) {
            this.userLocation = location;
        }
    }
}
