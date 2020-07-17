package com.fragments;

import android.content.Intent;
import android.graphics.Color;
import android.location.Location;
import android.os.Bundle;
import android.os.Handler;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.fastcabtaxi.passenger.MainActivity;
import com.fastcabtaxi.passenger.R;
import com.fastcabtaxi.passenger.SearchLocationActivity;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.GetAddressFromLocation;
import com.general.files.StartActProcess;
import com.google.android.gms.common.api.Status;
import com.google.android.gms.location.places.Place;
import com.google.android.gms.location.places.ui.PlaceAutocomplete;
import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.LatLngBounds;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.MTextView;

import java.util.HashMap;

public class MainHeaderFragment extends Fragment implements GetAddressFromLocation.AddressFound {

    public ImageView menuImgView;
    public ImageView backImgView;
    public MTextView pickUpLocTxt;
    public LinearLayout pickupLocArea1;
    public MTextView sourceLocSelectTxt;
    public MTextView destLocSelectTxt;
    public boolean isDestinationMode = false;
    public boolean uberXHeaderHide = false;
    public View area_source;
    public View area2;
    public MTextView mapTxt;
    public MTextView listTxt;
    public MTextView uberXTitleTxtView;
    public ImageView uberXbackImgView;
    public LinearLayout uberXMainHeaderLayout, MainHeaderLayout;
    public boolean isfirst = false;
    public boolean isAddressEnable;
    public GetAddressFromLocation getAddressFromLocation;
    MainActivity mainAct;
    GeneralFunctions generalFunc;
    GoogleMap gMap;
    ImageView headerLogo;
    View view;
    MTextView destLocTxt;
    String pickUpAddress = "";
    String destAddress = "";
    MainHeaderFragment mainHeaderFrag;
    String userProfileJson = "";
    String uberXSelectedServiceData = "";
    public LinearLayout destarea;
    LinearLayout switchArea;
    android.support.v7.widget.Toolbar toolbar;
    MTextView pickUpLocHTxt, destLocHTxt;
    String app_type = "";
    boolean isUfx = false;
    boolean isclickabledest = false;
    boolean isclickablesource = false;

    ImageView addPickUpImage, editPickupImage, addPickArea2Image, editPickArea2Image;

    ImageView addDestImageview, editDestImageview, addDestarea2Image, editDestarea2Image;


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        if (view != null) {
            return view;
        }
        view = inflater.inflate(R.layout.fragment_main_header, container, false);
        menuImgView = (ImageView) view.findViewById(R.id.menuImgView);
        backImgView = (ImageView) view.findViewById(R.id.backImgView);
        pickUpLocTxt = (MTextView) view.findViewById(R.id.pickUpLocTxt);
        sourceLocSelectTxt = (MTextView) view.findViewById(R.id.sourceLocSelectTxt);
        destLocSelectTxt = (MTextView) view.findViewById(R.id.destLocSelectTxt);
        destLocTxt = (MTextView) view.findViewById(R.id.destLocTxt);
        pickUpLocHTxt = (MTextView) view.findViewById(R.id.pickUpLocHTxt);
        destLocHTxt = (MTextView) view.findViewById(R.id.destLocHTxt);
        pickupLocArea1 = (LinearLayout) view.findViewById(R.id.pickupLocArea1);
        pickupLocArea1.setOnClickListener(new setOnClickList());
        destarea = (LinearLayout) view.findViewById(R.id.destarea);
        destarea.setOnClickListener(new setOnClickList());

        addPickUpImage = (ImageView) view.findViewById(R.id.addPickUpImage);
        editPickupImage = (ImageView) view.findViewById(R.id.editPickupImage);
        addDestImageview = (ImageView) view.findViewById(R.id.addDestImageview);
        editDestImageview = (ImageView) view.findViewById(R.id.editDestImageview);
        addPickArea2Image = (ImageView) view.findViewById(R.id.addPickArea2Image);
        editPickArea2Image = (ImageView) view.findViewById(R.id.editPickArea2Image);

        addDestarea2Image = (ImageView) view.findViewById(R.id.addDestarea2Image);
        editDestarea2Image = (ImageView) view.findViewById(R.id.editDestarea2Image);


        // searchPickUpLocArea = (LinearLayout) view.findViewById(R.id.searchPickUpLocArea);

        uberXMainHeaderLayout = (LinearLayout) view.findViewById(R.id.uberXMainHeaderLayout);
        MainHeaderLayout = (LinearLayout) view.findViewById(R.id.MainHeaderLayout);

        switchArea = (LinearLayout) view.findViewById(R.id.switchArea);
        headerLogo = (ImageView) view.findViewById(R.id.headerLogo);
        mapTxt = (MTextView) view.findViewById(R.id.mapTxt);
        listTxt = (MTextView) view.findViewById(R.id.listTxt);
        uberXTitleTxtView = (MTextView) view.findViewById(R.id.titleTxt);
        uberXbackImgView = (ImageView) view.findViewById(R.id.backImgViewuberx);
        area_source = view.findViewById(R.id.area_source);
        area2 = view.findViewById(R.id.area2);


        mainAct = (MainActivity) getActivity();
        generalFunc = mainAct.generalFunc;

        if (!mainAct.isFrompickupaddress) {
            area_source.setVisibility(View.GONE);
        } else {
            area_source.setVisibility(View.VISIBLE);
        }


        isUfx = getArguments().getBoolean("isUfx", false);

        pickUpLocHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_PICK_UP_FROM"));
        destLocHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_DROP_AT"));
        mapTxt.setText(generalFunc.retrieveLangLBl("", "LBL_MAP_TXT"));
        listTxt.setText(generalFunc.retrieveLangLBl("", "LBL_LIST_TXT"));

        uberXTitleTxtView.setText(generalFunc.retrieveLangLBl("Service Providers", "LBL_SERVICE_PROVIDERS"));
        mainHeaderFrag = mainAct.getMainHeaderFrag();
        userProfileJson = mainAct.userProfileJson;

        getAddressFromLocation = new GetAddressFromLocation(mainAct.getActContext(), generalFunc);
        getAddressFromLocation.setAddressList(this);

        pickUpLocTxt.setText(generalFunc.retrieveLangLBl("", "LBL_SELECTING_LOCATION_TXT"));

        app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);

        if (app_type.equals(Utils.CabGeneralType_UberX)) {

            area_source.setVisibility(View.GONE);
            area2.setVisibility(View.GONE);
        }

        if (isUfx) {
            if (app_type.equals(Utils.CabGeneralTypeRide_Delivery_UberX)) {
                area_source.setVisibility(View.GONE);
                area2.setVisibility(View.GONE);

            }
        }


        //searchPickUpLocArea.setOnClickListener(new setOnClickList());
        menuImgView.setOnClickListener(new setOnClickList());
        backImgView.setOnClickListener(new setOnClickList());

        if (!isUfx) {
            if (mainAct.isFirstTime) {
                menuImgView.performClick();
                mainAct.isFirstTime = false;
            }
        }


        listTxt.setOnClickListener(new
                setOnClickList());
        mapTxt.setOnClickListener(new
                setOnClickList());
        uberXbackImgView.setOnClickListener(new
                setOnClickList());
        sourceLocSelectTxt.setOnClickListener(new

                setOnClickList());
        destLocSelectTxt.setOnClickListener(new

                setOnClickList());

        destLocTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
        destLocSelectTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));

        handleDestAddIcon();

        if (isUfx)

        {
            if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
                uberXMainHeaderLayout.setVisibility(View.VISIBLE);
                MainHeaderLayout.setVisibility(View.GONE);
                switchArea.setVisibility(View.VISIBLE);
                mainAct.redirectToMapOrList(Utils.Cab_UberX_Type_List, false);
            } else if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase("UberX")) {
                uberXMainHeaderLayout.setVisibility(View.VISIBLE);
                MainHeaderLayout.setVisibility(View.GONE);
                switchArea.setVisibility(View.VISIBLE);
                mainAct.redirectToMapOrList(Utils.Cab_UberX_Type_List, false);
            } else {
                MainHeaderLayout.setVisibility(View.VISIBLE);
                uberXMainHeaderLayout.setVisibility(View.GONE);
                switchArea.setVisibility(View.GONE);
            }
        } else

        {

            if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase("UberX")) {
                uberXMainHeaderLayout.setVisibility(View.VISIBLE);
                MainHeaderLayout.setVisibility(View.GONE);
                switchArea.setVisibility(View.VISIBLE);
                mainAct.redirectToMapOrList(Utils.Cab_UberX_Type_List, false);
            } else {
                MainHeaderLayout.setVisibility(View.VISIBLE);
                uberXMainHeaderLayout.setVisibility(View.GONE);
                switchArea.setVisibility(View.GONE);
            }
        }
        if (mainAct != null)

        {
            mainAct.addDrawer.setMenuImgClick(view, false);
        }

        new CreateRoundedView(getResources().

                getColor(R.color.pickup_req_now_btn), Utils.

                dipToPixels(mainAct, 25), 0,
                Color.parseColor("#00000000"), view.findViewById(R.id.imgsourcearea1));
        new CreateRoundedView(getResources().

                getColor(R.color.pickup_req_later_btn), Utils.

                dipToPixels(mainAct, 25), 0,
                Color.parseColor("#00000000"), view.findViewById(R.id.imagemarkerdest1));
        new CreateRoundedView(getResources().

                getColor(R.color.pickup_req_now_btn), Utils.

                dipToPixels(mainAct, 25), 0,
                Color.parseColor("#00000000"), view.findViewById(R.id.imgsourcearea2));
        new CreateRoundedView(getResources().

                getColor(R.color.pickup_req_later_btn), Utils.

                dipToPixels(mainAct, 25), 0,
                Color.parseColor("#00000000"), view.findViewById(R.id.imagemarkerdest2));

        CameraPosition cameraPosition = mainAct.cameraForUserPosition();

        if (mainAct.getMap() != null && mainAct.getIntent().

                getStringExtra("latitude") != null && mainAct.getIntent().

                getStringExtra("longitude") != null
                && !mainAct.getIntent().

                getStringExtra("latitude").

                equals("0.0") && !mainAct.getIntent().

                getStringExtra("longitude").

                equals("0.0"))

        {

            CameraPosition cameraPosition1 = new CameraPosition.Builder().target(
                    new LatLng(generalFunc.parseDoubleValue(0.0, mainAct.getIntent().getStringExtra("latitude")),
                            generalFunc.parseDoubleValue(0.0, mainAct.getIntent().getStringExtra("longitude"))))
                    .zoom(Utils.defaultZomLevel).build();
            mainAct.getMap().moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition1));
        } else if (cameraPosition != null)

        {
            mainAct.getMap().moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
        }


        if (cameraPosition != null)

        {
            onGoogleMapCameraChangeList gmap = new onGoogleMapCameraChangeList();
            gmap.onCameraIdle();
        }


        return view;
    }


    public void refreshFragment() {
        view = null;
        FragmentTransaction ft = getFragmentManager().beginTransaction();
        ft.detach(this).attach(this).commit();
    }

    public void setGoogleMapInstance(GoogleMap gMap) {
        this.gMap = gMap;
        this.gMap.setOnCameraIdleListener(new onGoogleMapCameraChangeList());
    }


    public void setDefaultView() {

        if (!app_type.equals(Utils.CabGeneralType_UberX)) {
            area_source.setVisibility(View.VISIBLE);
            area2.setVisibility(View.GONE);
        }


        destLocTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
        destLocSelectTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
        mainAct.setDestinationPoint("", "", "", false);

        if (mainAct.pickUpLocation != null) {
            CameraPosition cameraPosition = new CameraPosition.Builder().target(
                    new LatLng(mainAct.pickUpLocation.getLatitude(), mainAct.pickUpLocation.getLongitude()))
                    .zoom(gMap.getCameraPosition().zoom).build();

            gMap.moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
        }
    }

    public void setDestinationAddress(String destAddress) {


        LatLng center = null;
        if (gMap != null && gMap.getCameraPosition() != null) {
            center = gMap.getCameraPosition().target;
        }
        if (center == null) {
            return;
        }

        if (destLocTxt != null) {
            destLocTxt.setText(destAddress);
        } else {
            this.destAddress = destAddress;
        }


        mainAct.setDestinationPoint("" + center.latitude, "" + center.longitude, destAddress, true);
    }

    public String getPickUpAddress() {
        return pickUpLocTxt.getText().toString();
    }

    public void setPickUpAddress(String pickUpAddress) {
        LatLng center = null;
        if (gMap != null && gMap.getCameraPosition() != null) {
            center = gMap.getCameraPosition().target;
        }
        if (center == null) {
            return;
        }

        if (sourceLocSelectTxt != null) {
            sourceLocSelectTxt.setText(pickUpAddress);
            mainAct.isFrompickupaddress = true;
        }
        this.pickUpAddress = pickUpAddress;
        if (pickUpLocTxt != null) {
            pickUpLocTxt.setText(pickUpAddress);

            handlePickEditIcon();
            mainAct.isFrompickupaddress = true;
        } else {
            this.pickUpAddress = pickUpAddress;
        }

        mainAct.pickUpLocationAddress = this.pickUpAddress;
        Location pickupLocation = new Location("");
        pickupLocation.setLongitude(center.longitude);
        pickupLocation.setLatitude(center.latitude);
        mainAct.pickUpLocation = pickupLocation;

    }

    public void handlePickEditIcon() {
        addPickUpImage.setVisibility(View.GONE);
        editPickupImage.setVisibility(View.VISIBLE);
        addPickArea2Image.setVisibility(View.GONE);
        editPickArea2Image.setVisibility(View.VISIBLE);


    }


    public void handleDestEditIcon() {


        addDestImageview.setVisibility(View.GONE);
        editDestImageview.setVisibility(View.VISIBLE);
        addDestarea2Image.setVisibility(View.GONE);
        editDestarea2Image.setVisibility(View.VISIBLE);


    }

    public void handleDestAddIcon() {
        addDestImageview.setVisibility(View.VISIBLE);
        editDestImageview.setVisibility(View.GONE);
        addDestarea2Image.setVisibility(View.VISIBLE);
        editDestarea2Image.setVisibility(View.GONE);


    }

    public void configDestinationMode(boolean isDestinationMode) {
        this.isDestinationMode = isDestinationMode;
    }

    @Override
    public void onAddressFound(String address, double latitude, double longitude, String geocodeobject) {


        geocodeobject = Utils.removeWithSpace(geocodeobject);

        if (isDestinationMode == false) {
            mainAct.tempDestGeoCode = geocodeobject;
            pickUpLocTxt.setText(address);
            handlePickEditIcon();
            sourceLocSelectTxt.setText(address);
            mainAct.isFrompickupaddress = true;
        } else {
            mainAct.tempPickupGeoCode = geocodeobject;
        }
        mainAct.onAddressFound(address);

        Location location = new Location("gps");
        location.setLatitude(latitude);
        location.setLongitude(longitude);
        if (isDestinationMode == false) {
            mainAct.pickUpLocationAddress = address;
            mainAct.currentGeoCodeObject = geocodeobject;

            if (mainAct.cabSelectionFrag == null) {
                if (mainAct.loadAvailCabs != null) {

                    mainAct.pickUpLocation = location;

                    Utils.printLog("setPickUpLocation", "::called");
                    mainAct.loadAvailCabs.setPickUpLocation(location);
                    mainAct.loadAvailCabs.pickUpAddress = mainAct.pickUpLocationAddress;
                }
            }
        }

        if (mainAct.loadAvailCabs == null) {
            mainAct.isDriverAssigned = false;
            mainAct.initializeLoadCab();
        }


        if (mainAct.cabSelectionFrag != null) {

            //else

            if (isDestinationMode == false) {

                if (mainAct.cabTypesArrList.size() < 1) {

                    mainAct.loadAvailCabs.checkAvailableCabs();
                } else {
                    isPickUpAddressStateChanged(mainAct.pickUpLocation);

                }
            }
        }

        if (!isfirst) {
            isfirst = true;

            mainAct.uberXAddress = address;
            mainAct.uberXlat = latitude;
            mainAct.uberXlong = longitude;

            if (isDestinationMode == false) {
                pickUpLocTxt.setText(address);
                handlePickEditIcon();
                sourceLocSelectTxt.setText(address);
                mainAct.isFrompickupaddress = true;
                Location pickUpLoc = new Location("");
                pickUpLoc.setLatitude(latitude);
                pickUpLoc.setLongitude(longitude);
                mainAct.pickUpLocation = pickUpLoc;

                if (!app_type.equals(Utils.CabGeneralType_UberX)) {
                    area2.setVisibility(View.VISIBLE);
                    area_source.setVisibility(View.GONE);
                }
                if (isUfx) {
                    if (app_type.equals(Utils.CabGeneralTypeRide_Delivery_UberX)) {
                        area_source.setVisibility(View.GONE);
                        area2.setVisibility(View.GONE);

                    }
                }

            }
            if (!app_type.equals(Utils.CabGeneralType_UberX)) {
                area2.setVisibility(View.VISIBLE);
                area_source.setVisibility(View.GONE);
            }
            if (isUfx) {
                if (app_type.equals(Utils.CabGeneralTypeRide_Delivery_UberX)) {
                    area_source.setVisibility(View.GONE);
                    area2.setVisibility(View.GONE);

                }
            }


            isDestinationMode = true;
            mainAct.configDestinationMode(isDestinationMode);
            mainAct.onAddressFound(address);
        }
        mainAct.currentGeoCodeObject = geocodeobject;

        if (mainAct.noloactionview.getVisibility() == View.VISIBLE) {
            area2.setVisibility(View.GONE);
        }

    }

    public String getAvailableCarTypesIds() {
        String carTypesIds = "";
        for (int i = 0; i < mainAct.cabTypesArrList.size(); i++) {
            String iVehicleTypeId = mainAct.cabTypesArrList.get(i).get("iVehicleTypeId");

            carTypesIds = carTypesIds.equals("") ? iVehicleTypeId : (carTypesIds + "," + iVehicleTypeId);
        }
        return carTypesIds;
    }

    public void isPickUpAddressStateChanged(Location pickupLocation) {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "CheckSourceLocationState");
        parameters.put("PickUpLatitude", pickupLocation.getLatitude() + "");
        parameters.put("PickUpLongitude", pickupLocation.getLongitude() + "");
        parameters.put("SelectedCarTypeID", getAvailableCarTypesIds());
        parameters.put("CurrentCabGeneralType", mainAct.getCurrentCabGeneralType());


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mainAct, parameters);
        exeWebServer.setLoaderConfig(mainAct, false, generalFunc);

        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        if (mainAct.loadAvailCabs != null) {
                            mainAct.loadAvailCabs.checkAvailableCabs();
                        }

                    } else {

                    }
                } else {

                    if (mainAct.loadAvailCabs != null) {
                        mainAct.loadAvailCabs.checkAvailableCabs();
                    }

                }
            }
        });
        exeWebServer.execute();


    }


    public void disableDestMode() {
        isDestinationMode = false;
        mainAct.configDestinationMode(isDestinationMode);
    }

    public void releaseResources() {
        this.gMap.setOnCameraIdleListener(null);
        this.gMap = null;
        getAddressFromLocation.setAddressList(null);
        getAddressFromLocation = null;
    }

    public void releaseAddressFinder() {
        this.gMap.setOnCameraIdleListener(null);
    }

    public void addAddressFinder() {
        this.gMap.setOnCameraIdleListener(new onGoogleMapCameraChangeList());
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == Utils.SEARCH_PICKUP_LOC_REQ_CODE) {
            isclickablesource = false;

        }

        if (requestCode == Utils.SEARCH_PICKUP_LOC_REQ_CODE && resultCode == mainAct.RESULT_OK && data != null && gMap != null) {


            if (resultCode == mainAct.RESULT_OK) {


                isAddressEnable = true;

                pickUpLocTxt.setText(data.getStringExtra("Address"));
                sourceLocSelectTxt.setText(data.getStringExtra("Address"));
                mainAct.isFrompickupaddress = true;
                LatLng pickUplocation = new LatLng(generalFunc.parseDoubleValue(0.0, data.getStringExtra("Latitude")), generalFunc.parseDoubleValue(0.0, data.getStringExtra("Longitude")));

                CameraPosition cameraPosition = new CameraPosition.Builder().target(
                        new LatLng(pickUplocation.latitude, pickUplocation.longitude))
                        .zoom(gMap.getCameraPosition().zoom).build();


                mainAct.pickUpLocationAddress = data.getStringExtra("Address");
                if (mainAct.loadAvailCabs != null) {
                    mainAct.loadAvailCabs.pickUpAddress = mainAct.pickUpLocationAddress;
                }
                if (mainAct.pickUpLocation == null) {
                    final Location location = new Location("gps");
                    location.setLatitude(pickUplocation.latitude);
                    location.setLongitude(pickUplocation.longitude);

                    mainAct.pickUpLocation = location;
                } else {

                    mainAct.pickUpLocation.setLatitude(pickUplocation.latitude);
                    mainAct.pickUpLocation.setLongitude(pickUplocation.longitude);
                }

                if (mainAct.cabSelectionFrag != null) {
                    mainAct.cabSelectionFrag.findRoute("--");
                }


                if (mainAct.cabSelectionFrag == null) {
                    gMap.moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
                } else {
                    gMap.clear();
                }
                if (mainAct.loadAvailCabs != null) {
                    mainAct.loadAvailCabs.pickUpAddress = mainAct.pickUpLocationAddress;
                    mainAct.loadAvailCabs.setPickUpLocation(mainAct.pickUpLocation);
                    if (mainAct.cabSelectionFrag != null) {


                        if (isDestinationMode == false) {

                            if (mainAct.cabTypesArrList.size() < 1) {

                                mainAct.loadAvailCabs.checkAvailableCabs();
                            } else {
                                isPickUpAddressStateChanged(mainAct.pickUpLocation);

                            }
                        }
                    }


                }

                if (mainAct.cabSelectionFrag == null) {

                    CameraUpdate cu = CameraUpdateFactory.newLatLngZoom(pickUplocation, 14.0f);

                    if (gMap != null) {
                        gMap.clear();
                        gMap.moveCamera(cu);
                    } else {
                        gMap.clear();
                    }
                }


            } else if (resultCode == PlaceAutocomplete.RESULT_ERROR) {
                Status status = PlaceAutocomplete.getStatus(getActivity(), data);


                generalFunc.showMessage(generalFunc.getCurrentView(getActivity()),
                        status.getStatusMessage());
            } else if (requestCode == mainAct.RESULT_CANCELED) {

            } else {

            }

        } else if (requestCode == Utils.SEARCH_DEST_LOC_REQ_CODE) {

            if (resultCode == mainAct.RESULT_OK && data != null && gMap != null) {


                isclickabledest = false;
                isDestinationMode = true;
                mainAct.isDestinationMode = true;
                isAddressEnable = true;

                destLocTxt.setText(data.getStringExtra("Address"));
                destLocSelectTxt.setText(data.getStringExtra("Address"));
                handleDestEditIcon();

                if (data.getBooleanExtra("isSkip", false)) {
                    area2.setVisibility(View.GONE);
                    area_source.setVisibility(View.GONE);


                    mainAct.destAddress = "";
                    mainAct.destLocLatitude = "";
                    mainAct.destLocLongitude = "";

                    menuImgView.setVisibility(View.GONE);
                    backImgView.setVisibility(View.VISIBLE);
                    mainAct.addcabselectionfragment();
                    mainAct.cabSelectionFrag.isSkip = true;
                    mainAct.cabSelectionFrag.isRouteFail = false;
                    Handler handler = new Handler();
                    handler.postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            mainAct.cabSelectionFrag.handleSourceMarker("--");


                        }
                    }, 200);


                    if (gMap != null) {
                        gMap.clear();
                    }
                    return;
                }


                mainAct.setDestinationPoint(data.getStringExtra("Latitude"), data.getStringExtra("Longitude"), data.getStringExtra("Address"), true);

                LatLng destlocation = new LatLng(generalFunc.parseDoubleValue(0.0, data.getStringExtra("Latitude")), generalFunc.parseDoubleValue(0.0, data.getStringExtra("Longitude")));

                CameraPosition cameraPosition = new CameraPosition.Builder().target(
                        new LatLng(destlocation.latitude, destlocation.longitude))
                        .zoom(gMap.getCameraPosition().zoom).build();


                if (mainAct.cabSelectionFrag != null) {
                    mainAct.cabSelectionFrag.findRoute("--");
                }


                if (mainAct.cabSelectionFrag == null) {
                    gMap.moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
                } else {
                    gMap.clear();
                }

                mainAct.destAddress = data.getStringExtra("Address");
                destLocTxt.setText(data.getStringExtra("Address"));
                destLocSelectTxt.setText(data.getStringExtra("Address"));
                handleDestEditIcon();


                mainAct.addcabselectionfragment();
                mainAct.cabSelectionFrag.isSkip = false;

                area2.setVisibility(View.GONE);
                area_source.setVisibility(View.GONE);

                menuImgView.setVisibility(View.GONE);
                backImgView.setVisibility(View.VISIBLE);
            } else {
                isclickabledest = false;


            }


        } else if (requestCode == Utils.PLACE_AUTOCOMPLETE_REQUEST_CODE) {
            isclickabledest = false;
            if (resultCode == mainAct.RESULT_OK) {
                Place place = PlaceAutocomplete.getPlace(getActivity(), data);

                if (place == null) {
                    return;
                }

                LatLng placeLocation = place.getLatLng();

                if (placeLocation == null) {
                    return;
                }

                mainAct.setDestinationPoint(placeLocation.latitude + "", placeLocation.longitude + "", place.getAddress().toString(), true);


                destLocTxt.setText(place.getAddress().toString());
                destLocSelectTxt.setText(place.getAddress().toString());
                handleDestEditIcon();


                mainAct.addcabselectionfragment();
                area2.setVisibility(View.GONE);
                area_source.setVisibility(View.GONE);

                CameraUpdate cu = CameraUpdateFactory.newLatLngZoom(placeLocation, 14.0f);

                if (mainAct.cabSelectionFrag == null) {

                    if (gMap != null) {
                        gMap.clear();
                        gMap.moveCamera(cu);
                    }
                }
                destLocTxt.setText(place.getAddress().toString());
                destLocSelectTxt.setText(place.getAddress().toString());


                menuImgView.setVisibility(View.GONE);
                backImgView.setVisibility(View.VISIBLE);


            } else if (resultCode == PlaceAutocomplete.RESULT_ERROR) {
                Status status = PlaceAutocomplete.getStatus(getActivity(), data);


                generalFunc.showMessage(generalFunc.getCurrentView(getActivity()),
                        status.getStatusMessage());
            } else if (requestCode == mainAct.RESULT_CANCELED) {

            } else {
                isclickabledest = false;

            }

        }
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }


    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(getActivity());
            int id = view.getId();
            if (id == destarea.getId()) {


                try {

                    if (mainAct.pickUpLocationAddress != null && !mainAct.pickUpLocationAddress.equals("")) {
                        if (!isclickabledest) {
                            isclickabledest = true;
                            isDestinationMode = true;
                            LatLngBounds bounds = null;
                            LatLng pickupPlaceLocation = null;
                            String pickupAddressTxt = "";
                            if (mainAct.pickUpLocation != null) {

                                pickupPlaceLocation = new LatLng(mainAct.pickUpLocation.getLatitude(),
                                        mainAct.pickUpLocation.getLongitude());
                                pickupAddressTxt = mainAct.getPickUpLocationAddress();
                                // bounds = new LatLngBounds(pickupPlaceLocation, pickupPlaceLocation);
                            }
//
                            if (!mainAct.destLocLatitude.equals("")) {
                                pickupPlaceLocation = new LatLng(generalFunc.parseDoubleValue(0.0, mainAct.destLocLatitude),
                                        generalFunc.parseDoubleValue(0.0, mainAct.destLocLongitude));
                                pickupAddressTxt = mainAct.getDestAddress();

                                // bounds = new LatLngBounds(pickupPlaceLocation, pickupPlaceLocation);

                            }
//
//                            Intent intent = new PlaceAutocomplete.IntentBuilder(PlaceAutocomplete.MODE_FULLSCREEN)
//                                    .setBoundsBias(bounds)
//                                    .build(mainAct);
//                            startActivityForResult(intent, Utils.PLACE_AUTOCOMPLETE_REQUEST_CODE);

                            Bundle bn = new Bundle();
                            bn.putString("locationArea", "dest");
                            bn.putBoolean("isDriverAssigned", mainAct.isDriverAssigned);

//
                            if (mainAct.pickUpLocation != null) {

                                pickupPlaceLocation = new LatLng(mainAct.pickUpLocation.getLatitude(),
                                        mainAct.pickUpLocation.getLongitude());
                                // bounds = new LatLngBounds(pickupPlaceLocation, pickupPlaceLocation);
                                bn.putDouble("lat", pickupPlaceLocation.latitude);
                                bn.putDouble("long", pickupPlaceLocation.longitude);
                                bn.putString("address", mainAct.pickUpLocationAddress);
                                //  mainAct.animateToLocation(pickupPlaceLocation.latitude, pickupPlaceLocation.longitude);

                            } else {
                                bn.putDouble("lat", 0.0);
                                bn.putDouble("long", 0.0);
                                bn.putString("address", "");

                            }

                            if (mainAct.destLocation != null && mainAct.destLocation.getLatitude() != 0.0) {
                                bn.putDouble("lat", mainAct.destLocation.getLatitude());
                                bn.putDouble("long", mainAct.destLocation.getLongitude());
                                bn.putString("address", mainAct.destAddress);

                            }
                         /*   bn.putDouble("lat", pickupPlaceLocation.latitude);
                            bn.putDouble("long", pickupPlaceLocation.longitude);
                            bn.putString("address", pickupAddressTxt);*/
                            bn.putString("type", mainAct.getCurrentCabGeneralType());
                            new StartActProcess(mainAct.getActContext()).startActForResult(mainHeaderFrag, SearchLocationActivity.class,
                                    Utils.SEARCH_DEST_LOC_REQ_CODE, bn);
                        }
                    }

                } catch (Exception e) {

                }


            } else if (view.getId() == pickupLocArea1.getId()) {

                try {
                    if (!isclickablesource) {
                        isclickablesource = true;
                        //disableDestMode();
                        LatLng pickupPlaceLocation = null;
                        Bundle bn = new Bundle();
                        bn.putString("locationArea", "source");
                        bn.putBoolean("isDriverAssigned", mainAct.isDriverAssigned);
                        if (mainAct.pickUpLocation != null) {

                            pickupPlaceLocation = new LatLng(mainAct.pickUpLocation.getLatitude(),
                                    mainAct.pickUpLocation.getLongitude());
                            // bounds = new LatLngBounds(pickupPlaceLocation, pickupPlaceLocation);
                            bn.putDouble("lat", pickupPlaceLocation.latitude);
                            bn.putDouble("long", pickupPlaceLocation.longitude);
                            bn.putString("address", mainAct.pickUpLocationAddress);
                            //  mainAct.animateToLocation(pickupPlaceLocation.latitude, pickupPlaceLocation.longitude);

                        } else {
                            bn.putDouble("lat", 0.0);
                            bn.putDouble("long", 0.0);
                            bn.putString("address", "");


                        }


                        bn.putString("type", mainAct.getCurrentCabGeneralType());
                        new StartActProcess(mainAct.getActContext()).startActForResult(mainHeaderFrag, SearchLocationActivity.class,
                                Utils.SEARCH_PICKUP_LOC_REQ_CODE, bn);
                    }
                } catch (Exception e) {

                }
            } else if (view.getId() == R.id.sourceLocSelectTxt) {


                if (Utils.checkText(mainAct.pickUpLocationAddress)) {
                    isAddressEnable = true;
                }
                area_source.setVisibility(View.VISIBLE);
                area2.setVisibility(View.GONE);
                disableDestMode();

                if (mainAct.getDestinationStatus() == true) {
                    destLocSelectTxt.setText(mainAct.getDestAddress());
                    handleDestEditIcon();
                } else {
                    destLocSelectTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
                    handleDestAddIcon();
                }
            } else if (view.getId() == R.id.destLocSelectTxt) {
                if (mainAct.getDestinationStatus()) {
                    isAddressEnable = true;
                }

                if (mainAct.pickUpLocation != null) {

                    area2.setVisibility(View.VISIBLE);
                    area_source.setVisibility(View.GONE);

                    isDestinationMode = true;
                    mainAct.configDestinationMode(isDestinationMode);

                    if (mainAct.getDestinationStatus() == false) {
                        new Handler().postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                destarea.performClick();
                            }
                        }, 250);

                    }
                }

            } else if (view.getId() == backImgView.getId()) {
                menuImgView.setVisibility(View.VISIBLE);
                backImgView.setVisibility(View.GONE);

                mainAct.onBackPressed();

            } else if (view.getId() == menuImgView.getId()) {
                mainAct.addDrawer.checkDrawerState(true);
            } else if (view.getId() == listTxt.getId()) {
                mainAct.userLocBtnImgView.setVisibility(View.GONE);
                LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
                params.gravity = Gravity.TOP;
                mainAct.ridelaterHandleView.setLayoutParams(params);
                mainAct.redirectToMapOrList(Utils.Cab_UberX_Type_List, false);
            } else if (view.getId() == mapTxt.getId()) {

                LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
                params.gravity = Gravity.BOTTOM;
                mainAct.ridelaterHandleView.setLayoutParams(params);
                mainAct.redirectToMapOrList(Utils.Cab_UberX_Type_Map, false);
                mainAct.userLocBtnImgView.setVisibility(View.VISIBLE);
            } else if (view.getId() == uberXbackImgView.getId()) {

                mainAct.onBackPressed();
            }
        }
    }

    public class onGoogleMapCameraChangeList implements GoogleMap.OnCameraIdleListener {


        @Override
        public void onCameraIdle() {
            if (getAddressFromLocation == null) {
                return;
            }

            if (mainAct.cabSelectionFrag != null) {

                mainAct.cabSelectionFrag.mangeMrakerPostion();
                return;
            }

            LatLng center = null;
            if (gMap != null && gMap.getCameraPosition() != null) {
                center = gMap.getCameraPosition().target;
            }

            if (center == null) {
                return;
            }

            if (!isAddressEnable) {

                //need to discuss
                Utils.printLog("getAddressFromLocation", "::" + "call_2");
                getAddressFromLocation.setLocation(center.latitude, center.longitude);
                getAddressFromLocation.execute();
                mainAct.onMapCameraChanged();


            } else {
                isAddressEnable = false;
            }


        }
    }
}
