package com.fragments;


import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.location.Location;
import android.os.Bundle;
import android.os.Handler;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.fastcabtaxi.passenger.MainActivity;
import com.fastcabtaxi.passenger.R;
import com.fastcabtaxi.passenger.SearchLocationActivity;
import com.general.files.AddDrawer;
import com.general.files.ConfigPubNub;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.general.files.UpdateFrequentTask;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.maps.model.Polyline;
import com.google.android.gms.maps.model.PolylineOptions;
import com.utils.AnimateMarker;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import org.json.JSONArray;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * A simple {@link Fragment} subclass.
 */
public class DriverAssignedHeaderFragment extends Fragment implements UpdateFrequentTask.OnTaskRunCalled {

    public boolean isDriverArrived = false;
    public boolean isDriverArrivedNotGenerated = false;
    public MTextView destLocSelectTxt;
    public boolean isBackVisible = false;
    public LatLng driverLocation;
    public LatLng pickUpLocation;
    public LatLng destLocation;
    public boolean isTripStart = false;
    MainActivity mainAct;
    GeneralFunctions generalFunc;
    String userProfileJson;
    View view;
    ImageView backImgView, menuImgView;
    MTextView titleTxt;
    DriverAssignedHeaderFragment driverAssignedHFrag;
    GoogleMap gMap;
    boolean isGooglemapSet = false;
    UpdateFrequentTask updateDriverLocTask;
    public UpdateFrequentTask updateDestMarkerTask;
    int DRIVER_LOC_FETCH_TIME_INTERVAL;
    int DESTINATION_UPDATE_TIME_INTERVAL;
    boolean isTaskKilled = false;
    String iDriverId = "";
    int notificationCount = 0;
    public HashMap<String, String> driverData;
    long currentNotificationTime = 0;
    public Polyline route_polyLine;
    MTextView pickUpLocTxt;
    public MTextView sourceLocSelectTxt;
    MTextView destLocTxt;
    View area_source;
    View area2;

    ImageView addDestLocImgView1, editDestLocImgView;
    ImageView imgAddDestbtn, imgEditDestbtn;

    AnimateMarker animDriverMarker;

    public Marker destinationPointMarker_temp;
    public Marker driverMarker;
    public Marker time_marker;
    public Marker sourceMarker;

    String eType;
    int DRIVER_ARRIVED_MIN_TIME_PER_MINUTE = 3;
    String driverAppVersion = "1";
    AddDrawer addDrawer;

    boolean isMapMoveToDriverLoc = false;

    boolean isload = true;

    LinearLayout destarea;

    public String eConfirmByUser = "No";
    android.support.v7.app.AlertDialog alertDialog_surgeConfirm;
    double tollamount = 0.0;
    String tollcurrancy = "";
    boolean istollIgnore = false;
    android.support.v7.app.AlertDialog tolltax_dialog;


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        if (view != null) {
            return view;
        }
        view = inflater.inflate(R.layout.fragment_driver_assigned_header, container, false);

        animDriverMarker = new AnimateMarker();
        mainAct = (MainActivity) getActivity();
        generalFunc = mainAct.generalFunc;
        userProfileJson = mainAct.userProfileJson;
        driverAssignedHFrag = mainAct.getDriverAssignedHeaderFrag();
        gMap = mainAct.getMap();

        addDrawer = new AddDrawer(getActContext(), userProfileJson);
        addDrawer.setIsDriverAssigned(true);
        pickUpLocTxt = (MTextView) view.findViewById(R.id.pickUpLocTxt);
        backImgView = (ImageView) view.findViewById(R.id.backImgView);
        menuImgView = (ImageView) view.findViewById(R.id.menuImgView);
        destarea = (LinearLayout) view.findViewById(R.id.destarea);
        menuImgView.setVisibility(View.VISIBLE);
        menuImgView.setOnClickListener(new setOnClickList());
        titleTxt = (MTextView) view.findViewById(R.id.titleTxt);
        pickUpLocTxt.setOnClickListener(new setOnClickList());

        pickUpLocTxt = (MTextView) view.findViewById(R.id.pickUpLocTxt);
        sourceLocSelectTxt = (MTextView) view.findViewById(R.id.sourceLocSelectTxt);
        destLocSelectTxt = (MTextView) view.findViewById(R.id.destLocSelectTxt);
        destLocTxt = (MTextView) view.findViewById(R.id.destLocTxt);
        addDestLocImgView1 = (ImageView) view.findViewById(R.id.addDestLocImgView1);
        editDestLocImgView = (ImageView) view.findViewById(R.id.editDestLocImgView);
        imgAddDestbtn = (ImageView) view.findViewById(R.id.imgAddDestbtn);
        imgEditDestbtn = (ImageView) view.findViewById(R.id.imgEditDestbtn);


        area_source = view.findViewById(R.id.area_source);
        area2 = view.findViewById(R.id.area2);

        backImgView.setImageResource(R.mipmap.ic_drawer_menu);

        backImgView.setOnClickListener(new setOnClickList());

        sourceLocSelectTxt.setOnClickListener(new setOnClickList());
        destLocSelectTxt.setOnClickListener(new setOnClickList());

        new CreateRoundedView(getResources().getColor(R.color.pickup_req_now_btn), Utils.dipToPixels(mainAct, 25), 0,
                Color.parseColor("#00000000"), view.findViewById(R.id.imgsourcearea1));
        new CreateRoundedView(getResources().getColor(R.color.pickup_req_later_btn), Utils.dipToPixels(mainAct, 25), 0,
                Color.parseColor("#00000000"), view.findViewById(R.id.imagemarkerdest1));
        new CreateRoundedView(getResources().getColor(R.color.pickup_req_now_btn), Utils.dipToPixels(mainAct, 25), 0,
                Color.parseColor("#00000000"), view.findViewById(R.id.imgsourcearea2));
        new CreateRoundedView(getResources().getColor(R.color.pickup_req_later_btn), Utils.dipToPixels(mainAct, 25), 0,
                Color.parseColor("#00000000"), view.findViewById(R.id.imagemarkerdest2));


        setDriverStatusTitle(generalFunc.retrieveLangLBl("", "LBL_EN_ROUTE_TXT"));
        setData();

        if (generalFunc.getJsonValue("vTripStatus", userProfileJson).equals("On Going Trip")) {

            setTripStartValue(true);
        } else {
            configDestinationView();
        }

        if (driverData != null && driverData.containsKey("DriverTripStatus") && driverData.get("DriverTripStatus").equalsIgnoreCase("Arrived")) {
            setDriverStatusTitle(generalFunc.retrieveLangLBl("", "LBL_DRIVER_ARRIVED_TXT"));
            isDriverArrived = true;
            isDriverArrivedNotGenerated = true;
            Utils.generateNotification(getActContext(), generalFunc.retrieveLangLBl("", "LBL_DRIVER_ARRIVED_TXT"));
        }


        DRIVER_ARRIVED_MIN_TIME_PER_MINUTE = generalFunc.parseIntegerValue(3, generalFunc.getJsonValue("DRIVER_ARRIVED_MIN_TIME_PER_MINUTE", userProfileJson));


        if (generalFunc.retrieveValue(CommonUtilities.APP_TYPE).equalsIgnoreCase("UberX")) {
            destLocSelectTxt.setVisibility(View.GONE);
            if (generalFunc.retrieveValue(CommonUtilities.APP_DESTINATION_MODE).equalsIgnoreCase(CommonUtilities.STRICT_DESTINATION) || generalFunc.retrieveValue(CommonUtilities.APP_DESTINATION_MODE).equalsIgnoreCase(CommonUtilities.NON_STRICT_DESTINATION)) {
                if (destLocSelectTxt.getVisibility() == View.GONE) {
                    destLocSelectTxt.setVisibility(View.VISIBLE);
                }
            }
        } else {
            destLocSelectTxt.setVisibility(View.VISIBLE);
        }

        if (mainAct != null) {
            mainAct.addDrawer.setMenuImgClick(view, true);
        }

        handleEditDest();

        mainAct.userLocBtnImgView.performClick();
        return view;
    }


    public void addPickupMarker() {

        if (time_marker != null) {
            time_marker.remove();
            time_marker = null;
        }
        removeSurceMarker();
        Utils.printLog("sourceMarker", ":: addPickupMarker()");
        sourceMarker = gMap.addMarker(new MarkerOptions().position(pickUpLocation)
                .icon(BitmapDescriptorFactory.fromResource(R.mipmap.ic_source_marker)));
    }

    public void setData() {


        HashMap<String, String> driverData = (HashMap<String, String>) getArguments().getSerializable("TripData");
        this.driverData = driverData;

        iDriverId = driverData.get("iDriverId");
        driverAppVersion = driverData.get("DriverAppVersion");
        pickUpLocTxt.setText(driverData.get("PickUpAddress"));
        sourceLocSelectTxt.setText(driverData.get("PickUpAddress"));

        driverLocation = new LatLng(generalFunc.parseDoubleValue(0.0, driverData.get("DriverLatitude")),
                generalFunc.parseDoubleValue(0.0, driverData.get("DriverLongitude")));
        pickUpLocation = new LatLng(generalFunc.parseDoubleValue(0.0, driverData.get("PickUpLatitude")),
                generalFunc.parseDoubleValue(0.0, driverData.get("PickUpLongitude")));


        if (driverData.get("destLatitude") != null && !driverData.get("destLatitude").equalsIgnoreCase("")) {
            destLocation = new LatLng(generalFunc.parseDoubleValue(0.0, driverData.get("destLatitude")),
                    generalFunc.parseDoubleValue(0.0, driverData.get("destLongitude")));


        }

        if (mainAct.isDeliver(mainAct.getCurrentCabGeneralType())) {
            isBackVisible = true;
            backImgView.setImageResource(R.mipmap.ic_back_arrow);
        } else {
            isBackVisible = false;
            backImgView.setImageResource(R.mipmap.ic_drawer_menu);
        }

        if (mainAct.isDeliver(mainAct.getCurrentCabGeneralType())) {
            destLocSelectTxt.setVisibility(View.GONE);

        } else if (mainAct.getDestinationStatus()) {
            destLocTxt.setText(mainAct.getDestAddress());
            destLocSelectTxt.setText(mainAct.getDestAddress());
            addDestLocImgView1.setVisibility(View.GONE);
            editDestLocImgView.setVisibility(View.VISIBLE);
            imgAddDestbtn.setVisibility(View.GONE);
            imgEditDestbtn.setVisibility(View.VISIBLE);

            if (driverData.get("eFlatTrip") != null && driverData.get("eFlatTrip").equalsIgnoreCase("Yes")) {
                editDestLocImgView.setVisibility(View.GONE);
                imgEditDestbtn.setVisibility(View.GONE);
            }

            destarea.setOnClickListener(new setOnClickList());
        } else {
            destLocTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
            destLocSelectTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
            addDestLocImgView1.setVisibility(View.VISIBLE);
            editDestLocImgView.setVisibility(View.GONE);

            imgAddDestbtn.setVisibility(View.VISIBLE);
            imgEditDestbtn.setVisibility(View.GONE);
            destarea.setOnClickListener(new setOnClickList());
        }

        if (gMap != null && isGooglemapSet == false) {
            isGooglemapSet = true;

            gMap.clear();
            configDriverLoc();
        }


        eType = driverData.get("eType");


        if (!generalFunc.getJsonValue("IS_DEST_ANYTIME_CHANGE", userProfileJson).equalsIgnoreCase("yes") || !eType.equalsIgnoreCase(Utils.CabGeneralType_Ride)) {
            editDestLocImgView.setVisibility(View.GONE);
            imgEditDestbtn.setVisibility(View.GONE);
            if (mainAct.getDestinationStatus()) {
                destarea.setOnClickListener(null);
            }
        }
        //"eFlatTrip" -> "Yes"
        if (driverData.get("eFlatTrip") != null && driverData.get("eFlatTrip").equalsIgnoreCase("Yes")) {
            editDestLocImgView.setVisibility(View.GONE);
            imgEditDestbtn.setVisibility(View.GONE);
        }


    }

    public void setGoogleMap(GoogleMap map) {
        this.gMap = map;
        if (isGooglemapSet == false) {
            gMap.clear();
        }
    }

    public void configDriverLoc() {
        if (driverLocation == null) {
            setData();
            return;
        }
        rotateMarkerBasedonDistance(driverLocation, "");


        if (mainAct != null && (mainAct.isPubNubEnabled() == false || driverAppVersion.equals("1"))) {
            scheduleDriverLocUpdate();
        } else if (mainAct != null && mainAct.isPubNubEnabled()) {
            subscribeToDriverLocChannel();
        }

        notifyDriverArrivedTime("" + driverLocation.latitude, "" + driverLocation.longitude);


    }

    public void subscribeToDriverLocChannel() {

        if (mainAct != null && ConfigPubNub.getInstance() != null) {
            ArrayList<String> channelName = new ArrayList<>();
            channelName.add(Utils.pubNub_Update_Loc_Channel_Prefix + iDriverId);
            ConfigPubNub.getInstance().subscribeToChannels(channelName);
            HashMap<String, String> driverData = (HashMap<String, String>) getArguments().getSerializable("TripData");
            ConfigPubNub.getInstance().setTripId(driverData.get("iTripId"), iDriverId);
        }

    }

    public void unSubscribeToDriverLocChannel() {
        if (mainAct != null && ConfigPubNub.getInstance() != null) {
            ArrayList<String> channelName = new ArrayList<>();
            channelName.add(Utils.pubNub_Update_Loc_Channel_Prefix + iDriverId);
            ConfigPubNub.getInstance().unSubscribeToChannels(channelName);
        }
    }

    public void scheduleDriverLocUpdate() {

        DRIVER_LOC_FETCH_TIME_INTERVAL = (generalFunc.parseIntegerValue(1, generalFunc.getJsonValue("DRIVER_LOC_FETCH_TIME_INTERVAL", userProfileJson))) * 1 * 1000;

        if (updateDriverLocTask == null) {
            updateDriverLocTask = new UpdateFrequentTask(DRIVER_LOC_FETCH_TIME_INTERVAL);
            updateDriverLocTask.setTaskRunListener(this);
            onResumeCalled();
        }
    }

    public void setTaskKilledValue(boolean isTaskKilled) {
        this.isTaskKilled = isTaskKilled;

        if (isTaskKilled == true) {
            onPauseCalled();
        }

    }


    public void setTripStartValue(boolean isTripStart) {

        this.isTripStart = isTripStart;
        if (mainAct != null) {
            mainAct.isTripStarted = isTripStart;
        }
        if (generalFunc == null) {
            generalFunc = new GeneralFunctions(getActContext());
        }

        if (isTripStart == true) {
            setDriverStatusTitle(generalFunc.retrieveLangLBl("", "LBL_EN_ROUTE_TXT"));


            mainAct.emeTapImgView.setVisibility(View.VISIBLE);
        }

        if (time_marker != null) {
            time_marker.remove();
        }
        configDestinationView();
    }

    public void setDriverStatusTitle(String title) {
        ((MTextView) view.findViewById(R.id.titleTxt)).setText(title);
        backImgView.setVisibility(View.GONE);
    }

    @Override
    public void onTaskRun() {
        updateDriverLocations();
    }

    public void updateDriverLocations() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getDriverLocations");
        parameters.put("iDriverId", iDriverId);
        parameters.put("UserType", CommonUtilities.app_type);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "getDriverLocations ::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        String vLatitude = generalFunc.getJsonValue("vLatitude", responseString);
                        String vLongitude = generalFunc.getJsonValue("vLongitude", responseString);
                        String vTripStatus = generalFunc.getJsonValue("vTripStatus", responseString);

                        if (vTripStatus.equals("Arrived")) {
                            setDriverStatusTitle(generalFunc.retrieveLangLBl("", "LBL_DRIVER_ARRIVED_TXT"));
                            if (isDriverArrivedNotGenerated == false) {
                                isDriverArrivedNotGenerated = true;
                                addDrawer.configDrawer(false);
                                Utils.generateNotification(getActContext(), generalFunc.retrieveLangLBl("", "LBL_DRIVER_ARRIVED_NOTIFICATION"));

                                generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_DRIVER_ARRIVED_NOTIFICATION"));
                            }
                            isDriverArrived = true;

                        }

                        LatLng driverLocation_update = new LatLng(generalFunc.parseDoubleValue(0.0, vLatitude),
                                generalFunc.parseDoubleValue(0.0, vLongitude));

                        rotateMarkerBasedonDistance(driverLocation_update, "");


                        if (vTripStatus.equals("Active")) {
                            updateDriverArrivedTime();
                        }
                    }
                }
            }
        });
        exeWebServer.execute();
    }

    public void rotateMarkerBasedonDistance(LatLng driverLocation_update, String message_json) {

        if (driverLocation == null) {
            driverLocation = driverLocation_update;
        }

        float rotation = driverMarker == null ? 0 : driverMarker.getRotation();


        if (animDriverMarker.currentLng != null) {
            rotation = (float) animDriverMarker.bearingBetweenLocations(animDriverMarker.currentLng, driverLocation_update);
        } else {
            rotation = (float) animDriverMarker.bearingBetweenLocations(driverLocation, driverLocation_update);
        }

        driverLocation = driverLocation_update;

        if (isMapMoveToDriverLoc == false) {
            CameraPosition cameraPosition = new CameraPosition.Builder().target(driverLocation_update)
                    .zoom(Utils.defaultZomLevel).build();
            mainAct.getMap().moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
            isMapMoveToDriverLoc = true;
        }

        Location driver_loc = new Location("gps");
        driver_loc.setLatitude(driverLocation_update.latitude);
        driver_loc.setLongitude(driverLocation_update.longitude);
        if (driverMarker == null) {
            MarkerOptions markerOptions_driver = new MarkerOptions();
            markerOptions_driver.position(driverLocation);

            String eIconType = driverData.get("eIconType");

            int iconId = R.mipmap.car_driver;
            if (eIconType.equalsIgnoreCase("Bike")) {
                iconId = R.mipmap.car_driver_1;
            } else if (eIconType.equalsIgnoreCase("Cycle")) {
                iconId = R.mipmap.car_driver_2;
            } else if (eIconType.equalsIgnoreCase("Truck")) {
//                iconId = R.mipmap.car_driver_5;
                iconId = R.mipmap.car_driver_4;
            }

            markerOptions_driver.icon(BitmapDescriptorFactory.fromResource(iconId)).anchor(0.5f,
                    0.5f).flat(true);
            driverMarker = gMap.addMarker(markerOptions_driver);
            driverMarker.setTitle(iDriverId);
        }


        if (message_json != null && message_json != "" && GeneralFunctions.isJSONValid(message_json) == true) {

            HashMap<String, String> previousItemOfMarker = animDriverMarker.getLastLocationDataOfMarker(driverMarker);

            HashMap<String, String> data_map = new HashMap<>();
            data_map.put("vLatitude", "" + driverLocation_update.latitude);
            data_map.put("vLongitude", "" + driverLocation_update.longitude);
            data_map.put("iDriverId", "" + this.iDriverId);
            data_map.put("RotationAngle", "" + rotation);
            data_map.put("LocTime", "" + generalFunc.getJsonValue("LocTime", message_json));

            if (animDriverMarker.toPositionLat.get("" + driverLocation_update.latitude) == null && animDriverMarker.toPositionLat.get("" + driverLocation_update.longitude) == null) {
                if (previousItemOfMarker.get("LocTime") != null && !previousItemOfMarker.get("LocTime").equals("")) {

                    long previousLocTime = generalFunc.parseLongValue(0, previousItemOfMarker.get("LocTime"));
                    long newLocTime = generalFunc.parseLongValue(0, data_map.get("LocTime"));

                    if (previousLocTime != 0 && newLocTime != 0) {

                        if ((newLocTime - previousLocTime) > 0 && animDriverMarker.driverMarkerAnimFinished == false) {
                            animDriverMarker.addToListAndStartNext(driverMarker, this.gMap, driver_loc, rotation, 1200, iDriverId, data_map.get("LocTime"));
                        } else if ((newLocTime - previousLocTime) > 0) {
                            animDriverMarker.animateMarker(driverMarker, this.gMap, driver_loc, rotation, 1200, iDriverId, data_map.get("LocTime"));
                        }

                    } else if ((previousLocTime == 0 || newLocTime == 0) && animDriverMarker.driverMarkerAnimFinished == false) {
                        animDriverMarker.addToListAndStartNext(driverMarker, this.gMap, driver_loc, rotation, 1200, iDriverId, data_map.get("LocTime"));
                    } else {
                        animDriverMarker.animateMarker(driverMarker, this.gMap, driver_loc, rotation, 1200, iDriverId, data_map.get("LocTime"));
                    }
                } else if (animDriverMarker.driverMarkerAnimFinished == false) {
                    animDriverMarker.addToListAndStartNext(driverMarker, this.gMap, driver_loc, rotation, 1200, iDriverId, data_map.get("LocTime"));
                } else {
                    animDriverMarker.animateMarker(driverMarker, this.gMap, driver_loc, rotation, 1200, iDriverId, data_map.get("LocTime"));
                }
            }

        } else {
            animDriverMarker.animateMarker(driverMarker, this.gMap, driver_loc, rotation, 1200, iDriverId, "");
        }
    }

    public void updateDriverLocation(String message) {
        if (message == null || !Utils.checkText(message)) {
            return;
        }
        String vLatitude = generalFunc.getJsonValue("vLatitude", message);
        String vLongitude = generalFunc.getJsonValue("vLongitude", message);

        LatLng driverLocation_update = new LatLng(generalFunc.parseDoubleValue(0.0, vLatitude),
                generalFunc.parseDoubleValue(0.0, vLongitude));

        rotateMarkerBasedonDistance(driverLocation_update, message);

        if (isTripStart == false) {

            notifyDriverArrivedTime(vLatitude, vLongitude);
        }

    }

    public void notifyDriverArrivedTime(String vLatitude, String vLongitude) {
        double distance = Utils.CalculationByLocation(pickUpLocation.latitude, pickUpLocation.longitude,
                generalFunc.parseDoubleValue(0.0, vLatitude), generalFunc.parseDoubleValue(0.0, vLongitude), "");
        int totalTimeInMinParKM = ((int) (distance * DRIVER_ARRIVED_MIN_TIME_PER_MINUTE));

        if (totalTimeInMinParKM == 0) {
            totalTimeInMinParKM = 0;
        } else if (totalTimeInMinParKM < 1) {
            totalTimeInMinParKM = 1;
        }

        if (totalTimeInMinParKM < 3 && isDriverArrived == false) {
            setDriverStatusTitle(generalFunc.retrieveLangLBl("", "LBL_ARRIVING_TXT"));
            addDrawer.configDrawer(false);

        }

        if ((totalTimeInMinParKM == 1 || totalTimeInMinParKM == 3) && notificationCount < 3 && isDriverArrived == false) {

            if (currentNotificationTime < 1 || (System.currentTimeMillis() - currentNotificationTime) > 1 * 60 * 1000) {

                currentNotificationTime = System.currentTimeMillis();

                notificationCount = notificationCount + 1;

                Utils.generateNotification(getActContext(), generalFunc.retrieveLangLBl("", "LBL_DRIVER_ARRIVING_NOTIFICATION"));
            }
        }


        Bitmap marker_time_ic = generalFunc.writeTextOnDrawable(getActContext(), R.drawable.driver_time_marker,
                getTimeTxt(totalTimeInMinParKM), true);

        setMarkerBasedOnTripStatus(marker_time_ic);

    }

    public void setMarkerBasedOnTripStatus(Bitmap marker_time_ic) {
        try {
            Utils.printLog("isTripStart", "::setMarkerBasedOnTripStatus" + isTripStart + "::" + isDriverArrived + " " + destLocation);

            if ((isDriverArrived || driverData.get("DriverTripStatus").equalsIgnoreCase("Arrived")) && !isTripStart) {

                if (time_marker != null) {
                    time_marker.remove();
                }
                return;
            }

            if (time_marker != null) {
                time_marker.remove();
                time_marker = null;
            }


            if (isDriverArrived) {

                if (isTripStart) {
                    time_marker = gMap.addMarker(
                            new MarkerOptions().position(destLocation)
                                    .icon(BitmapDescriptorFactory.fromBitmap(marker_time_ic)));
                } else

                {
                    time_marker = gMap.addMarker(
                            new MarkerOptions().position(pickUpLocation)
                                    .icon(BitmapDescriptorFactory.fromBitmap(marker_time_ic)));
                }
            } else {
                if (isTripStart) {

                    time_marker = gMap.addMarker(
                            new MarkerOptions().position(destLocation)
                                    .icon(BitmapDescriptorFactory.fromBitmap(marker_time_ic)));
                } else

                {
                    time_marker = gMap.addMarker(
                            new MarkerOptions().position(pickUpLocation)
                                    .icon(BitmapDescriptorFactory.fromBitmap(marker_time_ic)));
                }


            }
        } catch (Exception e) {

        }


        //mainAct.userLocBtnImgView.performClick();


    }

    public void updateDriverArrivedTime() {
        if (mainAct == null) {
            return;
        }
        String originLoc = pickUpLocation.latitude + "," + pickUpLocation.longitude;
        String destLoc = driverLocation.latitude + "," + driverLocation.longitude;
        String serverKey = mainAct.getResources().getString(R.string.google_api_get_address_from_location_serverApi);
        String url = "https://maps.googleapis.com/maps/api/directions/json?origin=" + originLoc + "&destination=" + destLoc + "&sensor=true&key=" + serverKey + "&language=" + generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY) + "&sensor=true";


        final String userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);


        String trip_data = generalFunc.getJsonValue("TripDetails", userProfileJson);

        String eTollSkipped = generalFunc.getJsonValue("eTollSkipped", trip_data);

        if (eTollSkipped == "Yes") {
            url = url + "&avoid=tolls";
        }
        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), url, true);

        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    String status = generalFunc.getJsonValue("status", responseString);

                    if (status.equals("OK")) {

                        JSONArray obj_routes = generalFunc.getJsonArray("routes", responseString);
                        if (obj_routes != null && obj_routes.length() > 0) {


                            int duration = (int) Math.round((generalFunc.parseDoubleValue(0.0,
                                    generalFunc.getJsonValue("value", generalFunc.getJsonValue("duration",
                                            generalFunc.getJsonObject(generalFunc.getJsonArray("legs", generalFunc.getJsonObject(obj_routes, 0).toString()), 0).toString())))) / 60);

                            Utils.printLog("duration_str", "::" + formatHoursAndMinutes(duration));

                            if (duration < 3 && isDriverArrived == false) {
                                setDriverStatusTitle(generalFunc.retrieveLangLBl("", "LBL_ARRIVING_TXT"));
                                addDrawer.configDrawer(false);
                            }

                            if (duration < 1) {
                                duration = 1;
                            }

                            if ((duration == 1 || duration == 3) && notificationCount < 3) {


                                if ((System.currentTimeMillis() - currentNotificationTime) < 60000) {
                                    currentNotificationTime = System.currentTimeMillis();
                                    notificationCount = notificationCount + 1;
                                    Utils.generateNotification(getActContext(), generalFunc.retrieveLangLBl("", "LBL_DRIVER_ARRIVING_NOTIFICATION"));
                                }

                            }

                            Bitmap marker_time_ic = generalFunc.writeTextOnDrawable(getActContext(), R.drawable.driver_time_marker,
                                    getTimeTxt(duration), true);

                            setMarkerBasedOnTripStatus(marker_time_ic);

                        }

                    }

                }
            }
        });
        exeWebServer.execute();
    }

    public String getTimeTxt(int duration) {

        if (duration < 1) {
            duration = 1;
        }
        String durationTxt = "";
        String timeToreach = duration == 0 ? "--" : "" + duration;

        timeToreach = duration > 60 ? formatHoursAndMinutes(duration) : timeToreach;


        durationTxt = (duration < 60 ? generalFunc.retrieveLangLBl("", "LBL_MINS_SMALL") : generalFunc.retrieveLangLBl("", "LBL_HOUR_TXT"));

        durationTxt = duration == 1 ? generalFunc.retrieveLangLBl("", "LBL_MIN_SMALL") : durationTxt;
        durationTxt = duration > 120 ? generalFunc.retrieveLangLBl("", "LBL_HOURS_TXT") : durationTxt;

        Utils.printLog("durationTxt", "::" + durationTxt);
        return timeToreach + "\n" + durationTxt;
    }


    public void addDestination(final String latitude, final String longitude, final String address) {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "addDestination");
        parameters.put("Latitude", latitude);
        parameters.put("Longitude", longitude);
        parameters.put("Address", address);
        parameters.put("TripId", driverData.get("iTripId"));
        parameters.put("eConfirmByUser", eConfirmByUser);
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("UserType", Utils.userType);

        parameters.put("fTollPrice", tollamount + "");
        parameters.put("vTollPriceCurrencyCode", tollcurrancy);
        String tollskiptxt = "";

        if (istollIgnore) {
            tollamount = 0;
            tollskiptxt = "Yes";

        } else {
            tollskiptxt = "No";
        }
        parameters.put("eTollSkipped", tollskiptxt);


        destLocation = new LatLng(GeneralFunctions.parseDoubleValue(0.0, latitude),
                GeneralFunctions.parseDoubleValue(0.0, longitude));


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(mainAct.getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        if (route_polyLine != null) {
                            route_polyLine.remove();
                        }

                        if (time_marker != null) {
                            time_marker.remove();
                            time_marker = null;
                        }

                        mainAct.setDestinationPoint(latitude, longitude, address, true);


                        setDestinationAddress("");
                        configDestinationView();

                        //destLocSelectTxt.performClick();

                        if (mainAct.isTripStarted) {

                            mainAct.userLocBtnImgView.performClick();
                        } else {
                            destLocSelectTxt.performClick();
                        }

                        if (eConfirmByUser.equalsIgnoreCase("Yes")) {
                            editDestLocImgView.setVisibility(View.GONE);
                            imgEditDestbtn.setVisibility(View.GONE);
                            destarea.setOnClickListener(null);
                        }


                    } else {
                        String msg_str = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
                        if (msg_str.equalsIgnoreCase("Yes")) {

                            if (generalFunc.getJsonValue("SurgePrice", responseString).equalsIgnoreCase("")) {
                                openFixChargeDialog(responseString, false);
                            } else {
                                openFixChargeDialog(responseString, true);
                            }

                            return;
                        }



                        if (msg_str.equals(CommonUtilities.GCM_FAILED_KEY) || msg_str.equals(CommonUtilities.APNS_FAILED_KEY)) {
                            generalFunc.restartApp();
                        } else {
                            generalFunc.showGeneralMessage("",
                                    generalFunc.retrieveLangLBl("", msg_str));
                        }

                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }


    public void getTollcostValue() {

        if (generalFunc.retrieveValue(CommonUtilities.ENABLE_TOLL_COST).equalsIgnoreCase("Yes")) {

            String url = CommonUtilities.TOLLURL + generalFunc.retrieveValue(CommonUtilities.TOLL_COST_APP_ID)
                    + "&app_code=" + generalFunc.retrieveValue(CommonUtilities.TOLL_COST_APP_CODE) + "&waypoint0=" + pickUpLocation.latitude
                    + "," + pickUpLocation.longitude + "&waypoint1=" + latitude + "," + longitirude + "&mode=fastest;car";

            Utils.printLog("Toll URL", "url" + url);
            ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), url, true);
            exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
            exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
                @Override
                public void setResponse(String responseString) {


                    if (responseString != null && !responseString.equals("")) {

                        if (generalFunc.getJsonValue("onError", responseString).equalsIgnoreCase("FALSE")) {
                            try {

                                String costs = generalFunc.getJsonValue("costs", responseString);

                                //  String details=generalFunc.getJsonValue("details",c)

                                String currency = generalFunc.getJsonValue("currency", costs);
                                String details = generalFunc.getJsonValue("details", costs);
                                String tollCost = generalFunc.getJsonValue("tollCost", details);
                                if (!currency.equals("") && currency != null) {
                                    tollcurrancy = currency;
                                }
                                if (!tollCost.equals("") && tollCost != null && !tollCost.equals("0.0")) {
                                    tollamount = generalFunc.parseDoubleValue(0.0, tollCost);
                                }


                            } catch (Exception e) {
                                tollcurrancy = "";
                            }
                        } else {
                            tollcurrancy = "";
                        }
                    } else {
                        tollcurrancy = "";
                    }

                }

            });
            exeWebServer.execute();


        } else {
            addDestination(latitude, longitirude, address);
        }

    }


    public void TollTaxDialog() {

        if (tollamount != 0.0 && tollamount != 0 && tollamount != 0.00) {
            android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());

            LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            View dialogView = inflater.inflate(R.layout.dialog_tolltax, null);

            final MTextView tolltaxTitle = (MTextView) dialogView.findViewById(R.id.tolltaxTitle);
            final MTextView tollTaxMsg = (MTextView) dialogView.findViewById(R.id.tollTaxMsg);
            final MTextView tollTaxpriceTxt = (MTextView) dialogView.findViewById(R.id.tollTaxpriceTxt);
            final MTextView cancelTxt = (MTextView) dialogView.findViewById(R.id.cancelTxt);

            final CheckBox checkboxTolltax = (CheckBox) dialogView.findViewById(R.id.checkboxTolltax);

            checkboxTolltax.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {

                    if (checkboxTolltax.isChecked()) {
                        istollIgnore = true;
                    } else {
                        istollIgnore = false;
                    }

                }
            });


            MButton btn_type2 = ((MaterialRippleLayout) dialogView.findViewById(R.id.btn_type2)).getChildView();
            int submitBtnId = Utils.generateViewId();
            btn_type2.setId(submitBtnId);
            btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_CONTINUE_BTN"));
            btn_type2.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    tolltax_dialog.dismiss();

                    addDestination(latitude, longitirude, address);


                }
            });


            builder.setView(dialogView);
            tolltaxTitle.setText(generalFunc.retrieveLangLBl("", "LBL_TOLL_ROUTE"));
            tollTaxMsg.setText(generalFunc.retrieveLangLBl("", "LBL_TOLL_PRICE_DESC"));

            tollTaxMsg.setText(generalFunc.retrieveLangLBl("", "LBL_TOLL_PRICE_DESC"));

            tollTaxpriceTxt.setText(generalFunc.retrieveLangLBl(
                    "Current Fare", "LBL_CURRENT_FARE") + ": " + payableAmount + "\n" + "+" + "\n" +
                    generalFunc.retrieveLangLBl("Total toll price", "LBL_TOLL_PRICE_TOTAL") + ": " + tollcurrancy + " " + tollamount);

            checkboxTolltax.setText(generalFunc.retrieveLangLBl("", "LBL_IGNORE_TOLL_ROUTE"));
            cancelTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));

            cancelTxt.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    tolltax_dialog.dismiss();


                }
            });


            tolltax_dialog = builder.create();
            if (generalFunc.isRTLmode() == true) {
                generalFunc.forceRTLIfSupported(tolltax_dialog);
            }
            tolltax_dialog.show();
        } else {

            addDestination(latitude, longitirude, address);

        }

    }

    public void handleEditDest() {
        try {

            if (eConfirmByUser.equalsIgnoreCase("Yes")) {
                eConfirmByUser = "yes";
                editDestLocImgView.setVisibility(View.GONE);
                imgEditDestbtn.setVisibility(View.GONE);
                destarea.setOnClickListener(null);
            }
        } catch (Exception e) {

        }
    }

    String payableAmount = "";

    public void openFixChargeDialog(String responseString, boolean isSurCharge) {

        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle("");
        builder.setCancelable(false);
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.surge_confirm_design, null);
        builder.setView(dialogView);

        MTextView payableAmountTxt;
        MTextView payableTxt;

        ((MTextView) dialogView.findViewById(R.id.headerMsgTxt)).setText(generalFunc.retrieveLangLBl("", generalFunc.retrieveLangLBl("", "LBL_FIX_FARE_HEADER")));


        ((MTextView) dialogView.findViewById(R.id.tryLaterTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_TRY_LATER"));

        payableTxt = (MTextView) dialogView.findViewById(R.id.payableTxt);
        payableAmountTxt = (MTextView) dialogView.findViewById(R.id.payableAmountTxt);
        if (!generalFunc.getJsonValue("fFlatTripPricewithsymbol", responseString).equalsIgnoreCase("")) {
            payableAmountTxt.setVisibility(View.VISIBLE);
            payableTxt.setVisibility(View.GONE);

            if (isSurCharge) {

                payableAmount = generalFunc.getJsonValue("fFlatTripPricewithsymbol", responseString) + " " + generalFunc.retrieveLangLBl("", "LBL_AT_TXT") + " " + "(" +
                        generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("SurgePrice", responseString)) + ")";
                ((MTextView) dialogView.findViewById(R.id.surgePriceTxt)).setText(generalFunc.convertNumberWithRTL(payableAmount));
            } else {
                payableAmount = generalFunc.getJsonValue("fFlatTripPricewithsymbol", responseString);
                ((MTextView) dialogView.findViewById(R.id.surgePriceTxt)).setText(generalFunc.convertNumberWithRTL(payableAmount));

            }
        } else {
            payableAmountTxt.setVisibility(View.GONE);
            payableTxt.setVisibility(View.VISIBLE);

        }

        MButton btn_type2 = ((MaterialRippleLayout) dialogView.findViewById(R.id.btn_type2)).getChildView();
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_ACCEPT_TXT"));
        btn_type2.setId(Utils.generateViewId());

        btn_type2.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                alertDialog_surgeConfirm.dismiss();
                eConfirmByUser = "Yes";

                addDestination(latitude, longitirude, address);


            }
        });
        (dialogView.findViewById(R.id.tryLaterTxt)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                alertDialog_surgeConfirm.dismiss();

            }
        });


        alertDialog_surgeConfirm = builder.create();
        alertDialog_surgeConfirm.setCancelable(false);
        alertDialog_surgeConfirm.setCanceledOnTouchOutside(false);
        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(alertDialog_surgeConfirm);
        }

        alertDialog_surgeConfirm.show();
    }

    public void setDestinationAddress(String eFlatTrip) {
        destLocTxt.setText(mainAct.getDestAddress());
        destLocSelectTxt.setText(mainAct.getDestAddress());


        if (!eFlatTrip.equalsIgnoreCase("") && eFlatTrip.equalsIgnoreCase("Yes")) {
            imgEditDestbtn.setVisibility(View.GONE);
            editDestLocImgView.setVisibility(View.GONE);
            addDestLocImgView1.setVisibility(View.GONE);
            imgAddDestbtn.setVisibility(View.GONE);
            destarea.setOnClickListener(null);
            return;
        }

        //destLocTxt.setOnClickListener(null);

        addDestLocImgView1.setVisibility(View.GONE);
        imgAddDestbtn.setVisibility(View.GONE);
        if (generalFunc.getJsonValue("IS_DEST_ANYTIME_CHANGE", userProfileJson).equalsIgnoreCase("yes")
                && eType.equalsIgnoreCase(Utils.CabGeneralType_Ride)) {
            imgEditDestbtn.setVisibility(View.VISIBLE);
            editDestLocImgView.setVisibility(View.VISIBLE);

            if (driverData.get("eFlatTrip") != null && driverData.get("eFlatTrip").equalsIgnoreCase("Yes")) {
                imgEditDestbtn.setVisibility(View.GONE);
                editDestLocImgView.setVisibility(View.GONE);
                destarea.setOnClickListener(null);
            }
        } else {
            destarea.setOnClickListener(null);
        }
    }

    public void configDestinationView() {


        if (driverData.get("DriverTripStatus").equalsIgnoreCase("Arrived") && !isTripStart) {
            removeSurceMarker();

            Utils.printLog("sourceMarker", ":: configDestinationView()");

            sourceMarker = gMap.addMarker(new MarkerOptions().position(pickUpLocation)
                    .icon(BitmapDescriptorFactory.fromResource(R.mipmap.ic_source_marker)));

            return;
        }

        if (mainAct == null) {
            return;
        }
        final String destLocLatitude = mainAct.getDestLocLatitude();
        final String destLocLongitude = mainAct.getDestLocLongitude();

        DESTINATION_UPDATE_TIME_INTERVAL = (generalFunc.parseIntegerValue(2, generalFunc.getJsonValue("DESTINATION_UPDATE_TIME_INTERVAL", userProfileJson))) * 60 * 1000;

        if (mainAct.getDestinationStatus()) {
            setDestinationAddress("");
        }

        if (updateDestMarkerTask != null) {
            updateDestMarkerTask.stopRepeatingTask();
            updateDestMarkerTask = null;
        }

        if (updateDestMarkerTask == null) {
            updateDestMarkerTask = new UpdateFrequentTask(DESTINATION_UPDATE_TIME_INTERVAL);
            updateDestMarkerTask.isuPDATEtASK = true;
            //  updateDestMarkerTask.startRepeatingTask();
            updateDestMarkerTask.setTaskRunListener(new UpdateFrequentTask.OnTaskRunCalled() {
                @Override
                public void onTaskRun() {
                    Utils.printLog("updateDestMarkerTask", "called");
                    //ha Toast.makeText(mainAct, "updateDestMarkerTask"+"::"+"called", Toast.LENGTH_LONG).show();
                    if (gMap != null) {
/*
                        if (destMarker == null) {
                            MarkerOptions markerOptions_destLocation = new MarkerOptions();
                            markerOptions_destLocation.position(new LatLng(generalFunc.parseDoubleValue(0.0, destLocLatitude),
                                    generalFunc.parseDoubleValue(0.0, destLocLongitude)));
                            markerOptions_destLocation.icon(BitmapDescriptorFactory.fromResource(R.mipmap.ic_dest_marker)).anchor(0.5f,
                                    0.5f);
                            destMarker = gMap.addMarker(markerOptions_destLocation);
                        }*/

                        scheduleDestRoute(destLocLatitude, destLocLongitude);
                    }
                }
            });
            onResumeCalled();
        }
    }

    public void scheduleDestRoute(final String destLocLatitude, final String destLocLongitude) {

        Utils.printLog("updateDestMarkerTask", "scheduleDestRoute called");
        // Toast.makeText(mainAct, "updateDestMarkerTask"+"::"+"scheduleDestRoute called", Toast.LENGTH_LONG).show();

        if (mainAct == null) {
            return;
        }
        String originLoc = "";
        String destLoc = "";
        LatLng destLatLng = null;

        if (isTripStart == false) {
            originLoc = driverLocation.latitude + "," + driverLocation.longitude;
            destLoc = pickUpLocation.latitude + "," + pickUpLocation.longitude;
            destLatLng = new LatLng(pickUpLocation.latitude, pickUpLocation.longitude);
        } else {
            if (driverLocation == null) {
                originLoc = pickUpLocation.latitude + "," + pickUpLocation.longitude;
            } else {
                originLoc = driverLocation.latitude + "," + driverLocation.longitude;
            }

            destLatLng = new LatLng(generalFunc.parseDoubleValue(0.0, destLocLatitude), generalFunc.parseDoubleValue(0.0, destLocLongitude));

            destLoc = destLocLatitude + "," + destLocLongitude;
        }

        String serverKey = mainAct.getActContext().getResources().getString(R.string.google_api_get_address_from_location_serverApi);
        String url = "https://maps.googleapis.com/maps/api/directions/json?origin=" + originLoc + "&destination=" + destLoc + "&sensor=true&key=" + serverKey + "&language=" + generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY) + "&sensor=true";

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), url, true);

        final LatLng finalDestLatLng = destLatLng;
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    String status = generalFunc.getJsonValue("status", responseString);

                    if (status.equals("OK")) {

                        PolylineOptions lineOptions = generalFunc.getGoogleRouteOptions(responseString, Utils.dipToPixels(getActContext(), 5), getActContext().getResources().getColor(R.color.appThemeColor_2));


                        if (lineOptions != null) {
                            if (route_polyLine != null) {
                                route_polyLine.remove();
                            }
                            route_polyLine = gMap.addPolyline(lineOptions);
                        }


                        JSONArray obj_routes = generalFunc.getJsonArray("routes", responseString);
                        if (obj_routes != null && obj_routes.length() > 0) {
                            String duration_str =
                                    generalFunc.getJsonValue("text", generalFunc.getJsonValue("duration",
                                            generalFunc.getJsonObject(generalFunc.getJsonArray("legs", generalFunc.getJsonObject(obj_routes, 0).toString()), 0).toString()));


                            int duration = (int) Math.round((generalFunc.parseDoubleValue(0.0,
                                    generalFunc.getJsonValue("value", generalFunc.getJsonValue("duration",
                                            generalFunc.getJsonObject(generalFunc.getJsonArray("legs", generalFunc.getJsonObject(obj_routes, 0).toString()), 0).toString())))) / 60);
                            Utils.printLog("duration_str", "::" + formatHoursAndMinutes(duration));

                            if (duration < 1) {
                                duration = 1;
                            }


                            Bitmap marker_time_ic = generalFunc.writeTextOnDrawable(getActContext(), R.drawable.driver_time_marker,
                                    getTimeTxt(duration), true);
                            setMarkerBasedOnTripStatus(marker_time_ic);

                        }
                    }
                }
            }
        });
        exeWebServer.execute();
    }

    public static String formatHoursAndMinutes(int totalMinutes) {
        String minutes = Integer.toString(totalMinutes % 60);
        minutes = minutes.length() == 1 ? "0" + minutes : minutes;
        return (totalMinutes / 60) + ":" + minutes;
    }

    public void onPauseCalled() {

        if (updateDriverLocTask != null) {
            updateDriverLocTask.stopRepeatingTask();
            updateDestMarkerTask = null;
            Utils.printLog("Api ", "Object Destroyed >> Driver Assigned Header Frag onPauseCalled >> updateDriverLocTask");
        }
        if (updateDestMarkerTask != null) {
            updateDestMarkerTask.stopRepeatingTask();
            updateDestMarkerTask = null;
            Utils.printLog("Api", "Object Destroyed >> Driver Assigned Header Frag onPauseCalled >> updateDestMarkerTask");
        }

        unSubscribeToDriverLocChannel();
    }


    public void releaseAllTask() {
        if (updateDriverLocTask != null) {
            updateDriverLocTask.stopRepeatingTask();
            updateDriverLocTask = null;
            Utils.printLog("Api", "Object Destroyed Driver Assigned Header Frag releaseAllTask >> updateDriverLocTask");
        }

        if (updateDestMarkerTask != null) {
            updateDestMarkerTask.stopRepeatingTask();
            updateDestMarkerTask = null;
            Utils.printLog("Api", "Object Destroyed Driver Assigned Header Frag releaseAllTask >> updateDestMarkerTask");
        }

        if (time_marker != null) {
            time_marker.remove();
            time_marker = null;
        }

        if (route_polyLine != null) {
            route_polyLine.remove();
            route_polyLine = null;
        }
        if (destinationPointMarker_temp != null) {
            destinationPointMarker_temp.remove();
        }
        unSubscribeToDriverLocChannel();
    }

    public void onResumeCalled() {
        if (updateDriverLocTask != null && isTaskKilled == false) {
            updateDriverLocTask.startRepeatingTask();
        }

        if (updateDestMarkerTask != null && isTaskKilled == false) {
            updateDestMarkerTask.startRepeatingTask();
        }

        subscribeToDriverLocChannel();
    }

    public Context getActContext() {
        return mainAct.getActContext();
    }

    String latitude = "";
    String longitirude = "";
    String address = "";

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == Utils.SEARCH_DEST_LOC_REQ_CODE && resultCode == mainAct.RESULT_OK && data != null) {
            latitude = data.getStringExtra("Latitude");
            longitirude = data.getStringExtra("Longitude");
            address = data.getStringExtra("Address");

            addDestination(latitude, longitirude, address);
            //getTollcostValue();

        }
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActContext());
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            if (view.getId() == backImgView.getId()) {

                if (isBackVisible == true) {
                    mainAct.onBackPressed();
                } else {
                    mainAct.checkDrawerState();
                }

            } else if (view.getId() == R.id.destarea) {
                Bundle bn = new Bundle();
                bn.putString("locationArea", "dest");
                if (mainAct.getPickUpLocation() != null) {
                    bn.putDouble("PickUpLatitude", mainAct.getPickUpLocation().getLatitude());
                    bn.putDouble("PickUpLongitude", mainAct.getPickUpLocation().getLongitude());
                    if (destLocation != null) {
                        bn.putDouble("lat", destLocation.latitude);
                        bn.putDouble("long", destLocation.longitude);
                        bn.putString("address", mainAct.destAddress);
                    }
                }
                bn.putBoolean("isDriverAssigned", mainAct.isDriverAssigned);

                new StartActProcess(mainAct.getActContext()).startActForResult(driverAssignedHFrag, SearchLocationActivity.class,
                        Utils.SEARCH_DEST_LOC_REQ_CODE, bn);


            } else if (view.getId() == R.id.sourceLocSelectTxt) {

                area_source.setVisibility(View.VISIBLE);
                area2.setVisibility(View.GONE);
                removeSurceMarker();


                if (isTripStart) {
                    sourceMarker = gMap.addMarker(new MarkerOptions().position(pickUpLocation)
                            .icon(BitmapDescriptorFactory.fromResource(R.mipmap.ic_source_marker)));

                }

                if (driverData.get("DriverTripStatus").equalsIgnoreCase("Arrived")) {
                    removeSurceMarker();

                    sourceMarker = gMap.addMarker(new MarkerOptions().position(pickUpLocation)
                            .icon(BitmapDescriptorFactory.fromResource(R.mipmap.ic_source_marker)));


                }


                if (!eConfirmByUser.equalsIgnoreCase("Yes")) {
                    if (mainAct.getDestinationStatus() == true) {
                        destLocSelectTxt.setText(mainAct.getDestAddress());
                        addDestLocImgView1.setVisibility(View.GONE);
                        imgAddDestbtn.setVisibility(View.GONE);
                        if (generalFunc.getJsonValue("IS_DEST_ANYTIME_CHANGE", userProfileJson).equalsIgnoreCase("yes")
                                && eType.equalsIgnoreCase(Utils.CabGeneralType_Ride)) {
                            imgEditDestbtn.setVisibility(View.VISIBLE);
                            editDestLocImgView.setVisibility(View.VISIBLE);
                        }
                    }
                }
                if (!generalFunc.isLocationEnabled()) {
                    String TripDetails = generalFunc.getJsonValue("TripDetails", userProfileJson);
                    Location tempickuploc = new Location("temppickkup");

                    double startLat = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("tStartLat", TripDetails));
                    double startLong = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("tStartLong", TripDetails));

                    if (startLat != 0.0 && startLong != 0.0) {
                        tempickuploc.setLatitude(startLat);
                        tempickuploc.setLongitude(startLong);


                        mainAct.animateToLocation(tempickuploc.getLatitude(), tempickuploc.getLongitude(), gMap.getMaxZoomLevel() - 5);


                    }
                } else {

                    mainAct.animateToLocation(pickUpLocation.latitude, pickUpLocation.longitude, gMap.getMaxZoomLevel() - 5);
                }

                if (destinationPointMarker_temp != null) {
                    destinationPointMarker_temp.remove();
                    destinationPointMarker_temp = null;
                }
                mainAct.pinImgView.setVisibility(View.GONE);


            } else if (view.getId() == R.id.destLocSelectTxt) {
                area2.setVisibility(View.VISIBLE);
                area_source.setVisibility(View.GONE);


                if (mainAct.getDestinationStatus() == false) {

                    if (sourceMarker != null) {
                        sourceMarker.remove();
                    }


                    new Handler().postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            destarea.performClick();
                        }
                    }, 250);

                } else {
                    mainAct.pinImgView.setVisibility(View.GONE);
                    if (isTripStart == false) {

                        if (destinationPointMarker_temp != null) {
                            destinationPointMarker_temp.remove();
                        }
                        destinationPointMarker_temp = gMap.addMarker(
                                new MarkerOptions().position(new LatLng(generalFunc.parseDoubleValue(0.0, mainAct.getDestLocLatitude()),
                                        generalFunc.parseDoubleValue(0.0, mainAct.getDestLocLongitude())))
                                        .icon(BitmapDescriptorFactory.fromResource(R.mipmap.ic_dest_marker)));


                    }
                    removeSurceMarker();

                    mainAct.animateToLocation(generalFunc.parseDoubleValue(0.0, mainAct.getDestLocLatitude()),
                            generalFunc.parseDoubleValue(0.0, mainAct.getDestLocLongitude()), gMap.getMaxZoomLevel() - 5);
                }

            } else if (view == menuImgView) {
                mainAct.addDrawer.checkDrawerState(true);
            } else if (view == pickUpLocTxt) {
                sourceLocSelectTxt.performClick();
            }
        }
    }

    public void removeSurceMarker() {
        if (sourceMarker != null) {
            sourceMarker.remove();
            sourceMarker = null;
        }
    }
}
