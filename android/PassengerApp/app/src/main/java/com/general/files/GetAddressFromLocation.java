package com.general.files;

import android.content.Context;

import com.fastcabtaxi.passenger.R;
import com.utils.CommonUtilities;

import org.json.JSONArray;
import org.json.JSONObject;

/**
 * Created by Admin on 02-07-2016.
 */
public class GetAddressFromLocation {
    double latitude;
    double longitude;
    Context mContext;
    String serverKey;
    GeneralFunctions generalFunc;

    ExecuteWebServerUrl currentWebTask;

    AddressFound addressFound;

    boolean isLoaderEnable = false;

    public GetAddressFromLocation(Context mContext, GeneralFunctions generalFunc) {
        this.mContext = mContext;
        this.generalFunc = generalFunc;

        serverKey = mContext.getResources().getString(R.string.google_api_get_address_from_location_serverApi);
    }

    public void setLocation(double latitude, double longitude) {
        this.latitude = latitude;
        this.longitude = longitude;
    }

    public void setLoaderEnable(boolean isLoaderEnable) {

        this.isLoaderEnable = isLoaderEnable;
    }

    public void execute() {
        if (currentWebTask != null) {
            currentWebTask.cancel(true);
            currentWebTask = null;
        }
        String url_str = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + latitude + "," + longitude + "&key=" + serverKey + "&language=" + generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY) + "&sensor=true";
        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, url_str, true);

        if (isLoaderEnable == true) {
            exeWebServer.setLoaderConfig(mContext, true, generalFunc);
        }
        this.currentWebTask = exeWebServer;
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

//                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    String status = generalFunc.getJsonValue("status", responseString);

                    if (status.equals("OK")) {
                        String address_loc = "";

                        JSONArray arr = generalFunc.getJsonArray("results", responseString);

                        if (arr.length() > 0) {

                            JSONObject obj = generalFunc.getJsonObject(arr, 0);

                            String formatted_address = generalFunc.getJsonValue("formatted_address", obj.toString());

//                            Utils.printLog("formatted_address","::"+formatted_address);
                            String[] addressArr = formatted_address.split(",");

                            boolean first_input = true;
                            for (int i = 0; i < addressArr.length; i++) {
                                if (!addressArr[i].contains("Unnamed") && !addressArr[i].contains("null")) {

                                    if (i == 0 && addressArr[0].matches("^[0-9]+$")) {
                                        continue;
                                    }
                                    if (first_input == true) {
                                        address_loc = addressArr[i];
                                        first_input = false;
                                    } else {
                                        address_loc = address_loc + "," + addressArr[i];
                                    }

                                }
                            }

                            if (addressFound != null) {
                                addressFound.onAddressFound(address_loc, latitude, longitude, responseString);
                            }
                        }

                    }

                }
            }
        });
        exeWebServer.execute();
    }

    public void setAddressList(AddressFound addressFound) {
        this.addressFound = addressFound;
    }

    public interface AddressFound {
        void onAddressFound(String address, double latitude, double longitude, String geocodeobject);
    }
}
