package com.fragments;


import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import com.fastcabtaxi.passenger.MainActivity;
import com.fastcabtaxi.passenger.R;
import com.fastcabtaxi.passenger.SearchPickupLocationActivity;
import com.general.files.GeneralFunctions;
import com.general.files.GetAddressFromLocation;
import com.general.files.StartActProcess;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;

/**
 * A simple {@link Fragment} subclass.
 */
public class PickUpLocSelectedFragment extends Fragment implements GetAddressFromLocation.AddressFound {


    public MTextView sourceLocSelectTxt;
    public MTextView destLocSelectTxt;
    View view;
    MainActivity mainAct;
    GeneralFunctions generalFunc;
    ImageView backImgView;
    MTextView titleTxt;
    PickUpLocSelectedFragment pickUpLocSelectedFrag;
    MTextView pickUpLocTxt;
    MTextView destLocTxt;

    ImageView rmDestLocImgView;

    String pickUpAddress = "";
    String destAddress = "";

    GoogleMap gMap;

    Marker destinationPointMarker_temp;

    View area_source;
    View area2;

    boolean isDestinationMode = false;
    GetAddressFromLocation getAddressFromLocation;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        view = inflater.inflate(R.layout.fragment_pick_up_loc_selected, container, false);
        mainAct = (MainActivity) getActivity();
        generalFunc = mainAct.generalFunc;

        titleTxt = (MTextView) view.findViewById(R.id.titleTxt);
        pickUpLocTxt = (MTextView) view.findViewById(R.id.pickUpLocTxt);
        sourceLocSelectTxt = (MTextView) view.findViewById(R.id.sourceLocSelectTxt);
        destLocSelectTxt = (MTextView) view.findViewById(R.id.destLocSelectTxt);
        destLocTxt = (MTextView) view.findViewById(R.id.destLocTxt);
        rmDestLocImgView = (ImageView) view.findViewById(R.id.rmDestLocImgView);
        backImgView = (ImageView) view.findViewById(R.id.backImgView);
        area_source = view.findViewById(R.id.area_source);
        area2 = view.findViewById(R.id.area2);

        if (mainAct != null) {
            if (mainAct.loadAvailCabs != null) {
                mainAct.loadAvailCabs.changeCabs();
            }
        }
        getAddressFromLocation = new GetAddressFromLocation(mainAct.getActContext(), generalFunc);
        getAddressFromLocation.setAddressList(this);

        if (mainAct.app_type.equalsIgnoreCase("UberX")) {
            destLocSelectTxt.setVisibility(View.GONE);
            if (generalFunc.retrieveValue(CommonUtilities.APP_DESTINATION_MODE).equalsIgnoreCase(CommonUtilities.STRICT_DESTINATION) || generalFunc.retrieveValue(CommonUtilities.APP_DESTINATION_MODE).equalsIgnoreCase(CommonUtilities.NON_STRICT_DESTINATION)) {
                if (destLocSelectTxt.getVisibility() == View.GONE) {
                    destLocSelectTxt.setVisibility(View.VISIBLE);
                }
            }
        } else {
            destLocSelectTxt.setVisibility(View.VISIBLE);
        }

        rmDestLocImgView.setOnClickListener(new setOnClickList());
        backImgView.setOnClickListener(new setOnClickList());
        pickUpLocTxt.setOnClickListener(new setOnClickList());
        destLocTxt.setOnClickListener(new setOnClickList());
        sourceLocSelectTxt.setOnClickListener(new setOnClickList());
        destLocSelectTxt.setOnClickListener(new setOnClickList());

        pickUpLocTxt.setText(pickUpAddress);
        sourceLocSelectTxt.setText(pickUpAddress);

        mainAct.pinImgView.setVisibility(View.VISIBLE);
        mainAct.pinImgView.setImageResource(R.drawable.pin_source_select);

        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_SOURCE_CONFIRM_HEADER_TXT"));
        destLocTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
        destLocSelectTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
        return view;
    }

    public void setPickUpLocSelectedFrag(PickUpLocSelectedFragment pickUpLocSelectedFrag) {
        this.pickUpLocSelectedFrag = pickUpLocSelectedFrag;
    }

    public void setGoogleMap(GoogleMap gMap) {
        this.gMap = gMap;
    }

    public void setGoogleMapInstance(GoogleMap gMap) {
//        gMap = mainAct.getMap();
        this.gMap = gMap;
        this.gMap.setOnCameraChangeListener(new onGoogleMapCameraChangeList());
    }

    @Override
    public void onAddressFound(String address, double latitude, double longitude, String geocodeobject) {

        if (generalFunc.isLocationEnabled()) {
            pickUpLocTxt.setText(address);
            if (mainAct != null) {
                if (mainAct.loadAvailCabs != null) {
                    mainAct.loadAvailCabs.changeCabs();
                }

                mainAct.pickUpLocationAddress = address;
                mainAct.pickUpLocation.setLatitude(latitude);
                mainAct.pickUpLocation.setLongitude(longitude);

            }
        }


    }

    @Override
    public void onResume() {
        super.onResume();

        if (mainAct != null) {
            mainAct.pinImgView.setVisibility(View.GONE);
        }
    }

    @Override
    public void onPause() {
        super.onPause();
        if (mainAct != null) {
            mainAct.pinImgView.setVisibility(View.GONE);
        }
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        if (mainAct != null) {
            mainAct.pinImgView.setVisibility(View.GONE);
        }
    }

    public void setDestinationAddress(String destAddress) {
        if (destLocTxt != null) {
            destLocTxt.setText(destAddress);
        } else {
            this.destAddress = destAddress;
        }

        LatLng center = gMap.getCameraPosition().target;
        mainAct.setDestinationPoint("" + center.latitude, "" + center.longitude, destAddress, true);
        rmDestLocImgView.setVisibility(View.VISIBLE);
    }

    public String getPickUpAddress() {
        return this.pickUpAddress;
    }

    public void setPickUpAddress(String pickUpAddress) {
        if (sourceLocSelectTxt != null) {
            sourceLocSelectTxt.setText(pickUpAddress);
        }
        this.pickUpAddress = pickUpAddress;
        if (pickUpLocTxt != null) {
            pickUpLocTxt.setText(pickUpAddress);
        } else {
            this.pickUpAddress = pickUpAddress;
        }
    }

    public void removeDestMarker() {
        if (destinationPointMarker_temp != null) {
            destinationPointMarker_temp.remove();
        }
    }

    public void disableDestMode() {
        isDestinationMode = false;
        mainAct.configDestinationMode(isDestinationMode);
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == Utils.SEARCH_PICKUP_LOC_REQ_CODE && resultCode == mainAct.RESULT_OK && data != null && gMap != null) {

//            mainAct.configPickUpDrag(true, false, false);
            pickUpLocTxt.setText(data.getStringExtra("Address"));

            mainAct.pickUpLocationAddress = data.getStringExtra("Address");

            mainAct.pickUpLocation.setLatitude(generalFunc.parseDoubleValue(0.0, data.getStringExtra("Latitude")));
            mainAct.pickUpLocation.setLongitude(generalFunc.parseDoubleValue(0.0, data.getStringExtra("Longitude")));
            CameraPosition cameraPosition = new CameraPosition.Builder().target(
                    new LatLng(generalFunc.parseDoubleValue(0.0, data.getStringExtra("Latitude")),
                            generalFunc.parseDoubleValue(0.0, data.getStringExtra("Longitude"))))
                    .zoom(gMap.getCameraPosition().zoom).build();
            gMap.moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));

            if (mainAct.loadAvailCabs != null) {
                mainAct.loadAvailCabs.changeCabs();
            }


        } else if (requestCode == Utils.SEARCH_DEST_LOC_REQ_CODE && resultCode == mainAct.RESULT_OK && data != null && gMap != null) {

            destLocTxt.setText(data.getStringExtra("Address"));

            mainAct.setDestinationPoint(data.getStringExtra("Latitude"), data.getStringExtra("Longitude"), data.getStringExtra("Address"), true);

            CameraPosition cameraPosition = new CameraPosition.Builder().target(
                    new LatLng(generalFunc.parseDoubleValue(0.0, data.getStringExtra("Latitude")),
                            generalFunc.parseDoubleValue(0.0, data.getStringExtra("Longitude"))))
                    .zoom(gMap.getCameraPosition().zoom).build();
            gMap.moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));


        }
    }

    public Context getActContext() {
        return mainAct.getActContext();
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }

    public class onGoogleMapCameraChangeList implements GoogleMap.OnCameraChangeListener {

        @Override
        public void onCameraChange(CameraPosition cameraPosition) {

            LatLng center = gMap.getCameraPosition().target;
            getAddressFromLocation.setLocation(center.latitude, center.longitude);
            getAddressFromLocation.execute();

        }
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int id = view.getId();
            Utils.hideKeyboard(getActivity());
            if (id == destLocTxt.getId()) {


                Bundle bn = new Bundle();
                bn.putString("isPickUpLoc", "false");
                if (mainAct.getPickUpLocation() != null) {
                    bn.putString("PickUpLatitude", "" + mainAct.getPickUpLocation().getLatitude());
                    bn.putString("PickUpLongitude", "" + mainAct.getPickUpLocation().getLongitude());
                }
                new StartActProcess(mainAct.getActContext()).startActForResult(pickUpLocSelectedFrag, SearchPickupLocationActivity.class,
                        Utils.SEARCH_DEST_LOC_REQ_CODE, bn);
            } else if (id == rmDestLocImgView.getId()) {
                mainAct.setDestinationPoint("", "", "", false);
                destAddress = "";
                destLocTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
                sourceLocSelectTxt.performClick();
                rmDestLocImgView.setVisibility(View.GONE);
            } else if (id == backImgView.getId()) {
                mainAct.onBackPressed();

            } else if (view.getId() == pickUpLocTxt.getId()) {

                Bundle bn = new Bundle();
                bn.putString("isPickUpLoc", "true");
                if (mainAct.getPickUpLocation() != null) {
                    bn.putString("PickUpLatitude", "" + mainAct.getPickUpLocation().getLatitude());
                    bn.putString("PickUpLongitude", "" + mainAct.getPickUpLocation().getLongitude());
                }
                new StartActProcess(mainAct.getActContext()).startActForResult(pickUpLocSelectedFrag, SearchPickupLocationActivity.class,
                        Utils.SEARCH_PICKUP_LOC_REQ_CODE, bn);
            } else if (view.getId() == R.id.sourceLocSelectTxt) {

                area_source.setVisibility(View.VISIBLE);
                area2.setVisibility(View.GONE);
                disableDestMode();

                if (mainAct.getDestinationStatus() == true) {
                    destLocSelectTxt.setText(mainAct.getDestAddress());
                } else {
                    destLocSelectTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
                }
            } else if (view.getId() == R.id.destLocSelectTxt) {
                area2.setVisibility(View.VISIBLE);
                area_source.setVisibility(View.GONE);

                isDestinationMode = true;
                mainAct.configDestinationMode(isDestinationMode);

                if (mainAct.getDestinationStatus() == false) {
                    new Handler().postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            destLocTxt.performClick();
                        }
                    }, 250);

                }

            }
        }
    }
}
