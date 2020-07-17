package com.fastcabtaxi.driver;

import android.content.Context;
import android.content.DialogInterface;
import android.location.Location;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.text.InputType;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.GetLocationUpdates;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;
import com.view.simpleratingbar.SimpleRatingBar;

import java.util.HashMap;

public class TripRatingActivity extends AppCompatActivity implements OnMapReadyCallback, GetLocationUpdates.LocationUpdates {

    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;

    GoogleMap gMap;

    Location userLocation;

    MButton btn_type2;
    MaterialEditText commentBox;

    SimpleRatingBar ratingBar;
    String iTripId_str;

    HashMap<String, String> data_trip;
    boolean isSubmitClicked = false;

    GetLocationUpdates getLocationUpdates;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_trip_rating);

        generalFunc = new GeneralFunctions(getActContext());

        data_trip = (HashMap<String, String>) getIntent().getSerializableExtra("TRIP_DATA");
        iTripId_str = data_trip.get("TripId");

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        commentBox = (MaterialEditText) findViewById(R.id.commentBox);
        ratingBar = (SimpleRatingBar) findViewById(R.id.ratingBar);
        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();


        SupportMapFragment map = (SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.mapV2);
        map.getMapAsync(this);


        (findViewById(R.id.backImgView)).setVisibility(View.GONE);

        LinearLayout.LayoutParams params = (LinearLayout.LayoutParams) titleTxt.getLayoutParams();
        params.setMargins(Utils.dipToPixels(getActContext(), 20), 0, 0, 0);
        titleTxt.setLayoutParams(params);

        btn_type2.setId(Utils.generateViewId());
        btn_type2.setOnClickListener(new setOnClickList());

        commentBox.setSingleLine(false);
        commentBox.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_FLAG_MULTI_LINE);
        commentBox.setImeOptions(EditorInfo.IME_ACTION_DONE);
        commentBox.setGravity(Gravity.TOP);
        commentBox.setFloatingLabel(MaterialEditText.FLOATING_LABEL_NONE);

        setLabels();

        ((MTextView) findViewById(R.id.nameTxt)).setText(data_trip.get("PName"));

        if (savedInstanceState != null) {
            // Restore value of members from saved state
            String restratValue_str = savedInstanceState.getString("RESTART_STATE");

            if (restratValue_str != null && !restratValue_str.equals("") && restratValue_str.trim().equals("true")) {
                generalFunc.restartApp();
            }
        }
    }

    @Override
    protected void onSaveInstanceState(Bundle outState) {
        // TODO Auto-generated method stub
        outState.putString("RESTART_STATE", "true");
        super.onSaveInstanceState(outState);
    }

    @Override
    protected void onResume() {
        super.onResume();
    }

    @Override
    protected void onDestroy() {
        if (getLocationUpdates != null) {
            getLocationUpdates.stopLocationUpdates();
        }
        super.onDestroy();

    }

    @Override
    protected void onPause() {
        super.onPause();


    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_RATING"));
        ((MTextView) findViewById(R.id.rateTxt)).setText(generalFunc.retrieveLangLBl("Rate", "LBL_RATE"));
        commentBox.setHint(generalFunc.retrieveLangLBl("", "LBL_WRITE_COMMENT_HINT_TXT"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_SUBMIT_TXT"));
    }

    public void submitRating() {
        isSubmitClicked = true;
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "submitRating");
        parameters.put("iGeneralUserId", generalFunc.getMemberId());
        parameters.put("tripID", iTripId_str);
        parameters.put("rating", "" + ratingBar.getRating() + "");
        parameters.put("message", Utils.getText(commentBox));
        parameters.put("UserType", CommonUtilities.app_type);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {
                    isSubmitClicked = true;
                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        isSubmitClicked = false;

                        showBookingAlert(generalFunc.retrieveLangLBl("", "LBL_TRIP_FINISHED_TXT"));

                    } else {
                        isSubmitClicked = false;
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }
                } else {
                    isSubmitClicked = false;
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void showBookingAlert(String message) {
        android.support.v7.app.AlertDialog alertDialog;
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle("");
        builder.setCancelable(false);
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.dialog_booking_view, null);
        builder.setView(dialogView);

        final MTextView titleTxt = (MTextView) dialogView.findViewById(R.id.titleTxt);
        final MTextView mesasgeTxt = (MTextView) dialogView.findViewById(R.id.mesasgeTxt);


        titleTxt.setText(generalFunc.retrieveLangLBl("Booking Successful", "LBL_SUCCESS_FINISHED"));
        mesasgeTxt.setText(message);


        builder.setPositiveButton(generalFunc.retrieveLangLBl("", "LBL_OK_THANKS"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
                generalFunc.saveGoOnlineInfo();
                generalFunc.restartwithGetDataApp();

            }
        });


        alertDialog = builder.create();
        alertDialog.setCancelable(false);
        alertDialog.setCanceledOnTouchOutside(false);
        alertDialog.show();



    }



    public Context getActContext() {
        return TripRatingActivity.this;
    }

    @Override
    public void onBackPressed() {
        return;
    }

    @Override
    public void onLocationUpdate(Location location) {
        this.userLocation = location;
        CameraPosition cameraPosition = cameraForUserPosition();

        if (cameraPosition != null) {
            getMap().moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));

        }
    }

    public CameraPosition cameraForUserPosition() {


        if (userLocation == null) {
            return null;
        }


        double currentZoomLevel = getMap().getCameraPosition().zoom;

        if (Utils.defaultZomLevel > currentZoomLevel) {
            currentZoomLevel = Utils.defaultZomLevel;
        }
        CameraPosition cameraPosition = new CameraPosition.Builder().target(new LatLng(this.userLocation.getLatitude(), this.userLocation.getLongitude()))
                .zoom((float) currentZoomLevel).build();

        return cameraPosition;
    }

    public GoogleMap getMap() {
        return this.gMap;
    }

    @Override
    public void onMapReady(GoogleMap googleMap) {
        this.gMap = googleMap;
        if (generalFunc.checkLocationPermission(true) == true) {
            getMap().setMyLocationEnabled(true);
        }

        getMap().getUiSettings().setTiltGesturesEnabled(false);
        getMap().getUiSettings().setCompassEnabled(false);
        getMap().getUiSettings().setMyLocationButtonEnabled(false);

        if(getLocationUpdates != null ){
            getLocationUpdates.stopLocationUpdates();
            getLocationUpdates = null;
        }
        getLocationUpdates =new GetLocationUpdates(getActContext(), Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, false,this);

    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(TripRatingActivity.this);

            if (i == btn_type2.getId()) {
                if (!isSubmitClicked) {

                    if (ratingBar.getRating() < 0.5) {
                        generalFunc.showMessage(generalFunc.getCurrentView(TripRatingActivity.this),
                                generalFunc.retrieveLangLBl("", "LBL_ERROR_RATING_DIALOG_TXT"));
                        return;
                    }
                    submitRating();
                }
            }
        }
    }
}
