package com.fastcabtaxi.passenger;

import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.location.Location;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.provider.Settings;
import android.support.design.widget.Snackbar;
import android.support.v4.app.ActivityCompat;
import android.support.v4.view.GravityCompat;
import android.support.v4.widget.DrawerLayout;
import android.support.v7.app.ActionBarDrawerToggle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.TextUtils;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.ContextMenu;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;

import com.datepicker.files.SlideDateTimeListener;
import com.datepicker.files.SlideDateTimePicker;
import com.dialogs.NoInternetConnectionDialog;
import com.dialogs.RequestNearestCab;
import com.fragments.CabSelectionFragment;
import com.fragments.DriverAssignedHeaderFragment;
import com.fragments.DriverDetailFragment;
import com.fragments.MainHeaderFragment;
import com.fragments.PickUpLocSelectedFragment;
import com.fragments.RequestPickUpFragment;
import com.general.files.AddDrawer;
import com.general.files.ConfigPubNub;
import com.general.files.CreateAnimation;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.GetAddressFromLocation;
import com.general.files.GetLocationUpdates;
import com.general.files.HashMapComparator;
import com.general.files.InternetConnection;
import com.general.files.LoadAvailableCab;
import com.general.files.MapAnimator;
import com.general.files.MyApp;
import com.general.files.StartActProcess;
import com.general.files.UpdateFrequentTask;
import com.google.android.gms.common.api.Status;
import com.google.android.gms.location.places.Place;
import com.google.android.gms.location.places.ui.PlaceAutocomplete;
import com.google.android.gms.maps.CameraUpdate;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.LatLngBounds;
import com.google.android.gms.maps.model.Marker;
import com.google.maps.android.SphericalUtil;
import com.pubnub.api.enums.PNStatusCategory;
import com.squareup.picasso.Picasso;
import com.utils.AnimateMarker;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.SelectableRoundedImageView;
import com.view.anim.loader.AVLoadingIndicatorView;
import com.view.simpleratingbar.SimpleRatingBar;
import com.view.slidinguppanel.SlidingUpPanelLayout;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.concurrent.TimeUnit;

import static com.utils.Utils.CabGeneralType_Deliver;
import static com.utils.Utils.generateNotification;

public class MainActivity extends AppCompatActivity implements OnMapReadyCallback, GoogleMap.OnMapClickListener, GetLocationUpdates.LocationUpdates {

    public GeneralFunctions generalFunc;
    public String userProfileJson = "";
    public String currentGeoCodeObject = "";
    public SlidingUpPanelLayout sliding_layout;
    public ImageView userLocBtnImgView;
    public Location userLocation;
    public ArrayList<HashMap<String, String>> currentLoadedDriverList;
    public ImageView emeTapImgView;
    public AddDrawer addDrawer;
    public CabSelectionFragment cabSelectionFrag;
    public LoadAvailableCab loadAvailCabs;
    public Location pickUpLocation;
    public String selectedCabTypeId = "";
    public boolean isDestinationAdded = false;
    public String destLocLatitude = "";
    public String destLocLongitude = "";
    public String destAddress = "";
    public boolean isCashSelected = true;
    public String pickUpLocationAddress = "";
    public String app_type = "Ride";
    public boolean isBackVisible = false;
    public DrawerLayout mDrawerLayout;
    public AVLoadingIndicatorView loaderView;
    public ImageView pinImgView;
    public ArrayList<HashMap<String, String>> cabTypesArrList = new ArrayList<>();
    public boolean iswallet = false;
    public boolean isUserLocbtnclik = false;
    public String tempPickupGeoCode = "";
    public String tempDestGeoCode = "";
    public boolean isUfx = false;
    public String uberXAddress = "";
    public double uberXlat = 0.0;
    public double uberXlong = 0.0;
    public boolean ishandicap = false;
    public boolean isfemale = false;
    public LinearLayout noloactionview;
    public ImageView nolocmenuImgView;
    public ImageView nolocbackImgView;
    public ImageView noLocImgView;
    public boolean isFrompickupaddress = false;
    public String timeval = "";
    public DriverAssignedHeaderFragment driverAssignedHeaderFrag;
    public RequestNearestCab requestNearestCab;
    public boolean isDestinationMode = false;
    public MTextView settingTxt;
    public LinearLayout ridelaterHandleView;
    public boolean isUfxRideLater = false;
    public String bookingtype = "";
    public String selectedprovidername = "";
    public String vCurrencySymbol = "";
    public String UfxAmount = "";
    public boolean noCabAvail = false;
    public Location destLocation;
    public boolean isDriverAssigned = false;
    public GenerateAlertBox noCabAvailAlertBox;
    public JSONObject obj_userProfile;
    public String SelectDate = "";
    public String sdate = "";
    public String Stime = "";
    public boolean isFirstTime = true;
    public String ACCEPT_CASH_TRIPS = "";
    MTextView titleTxt;
    public SupportMapFragment map;
    GetLocationUpdates getLastLocation;
    GoogleMap gMap;
    boolean isFirstLocation = true;
    boolean isMovedToCurLoc = false;
    RelativeLayout dragView;
    RelativeLayout mainArea;
    View otherArea;
    FrameLayout mainContent;
    RelativeLayout uberXDriverListArea;
    public MainHeaderFragment mainHeaderFrag;
    RequestPickUpFragment reqPickUpFrag;
    DriverDetailFragment driverDetailFrag;
    ArrayList<HashMap<String, String>> cabTypeList;
    ArrayList<HashMap<String, String>> uberXDriverList = new ArrayList<>();
    HashMap<String, String> driverAssignedData;
    String assignedDriverId = "";
    public String assignedTripId = "";
    String DRIVER_REQUEST_METHOD = "All";
    MTextView uberXNoDriverTxt;
    SelectableRoundedImageView driverImgView;
    UpdateFrequentTask allCabRequestTask;
    SendNotificationsToDriverByDist sendNotificationToDriverByDist;
    String selectedDateTime = "";
    String selectedDateTimeZone = "";
    String cabRquestType = Utils.CabReqType_Now; // Later OR Now
    String destLocationAddress = "";
    View rideArea;
    View deliverArea;
    android.support.v7.app.AlertDialog pickUpTypeAlertBox = null;


    Intent deliveryData;
    String eTripType = "";
    android.support.v7.app.AlertDialog alertDialog_surgeConfirm;
    String required_str = "";
    RecyclerView uberXOnlineDriversRecyclerView;
    LinearLayout driver_detail_bottomView;
    String markerId = "";
    boolean isMarkerClickable = true;
    String currentUberXChoiceType = Utils.Cab_UberX_Type_List;
    String vUberXCategoryName = "";
    Handler ufxFreqTask = null;
    String tripId = "";
    android.support.v7.app.AlertDialog onGoingTripAlertBox = null;
    boolean isOkPressed = false;
    GetAddressFromLocation getAddressFromLocation;
    String RideDeliveryType = "";
    SelectableRoundedImageView deliverImgView, deliverImgViewsel, rideImgView, rideImgViewsel, otherImageView, otherImageViewsel;
    PickUpLocSelectedFragment pickUpLocSelectedFrag;
    boolean istollenable = false;
    double tollamount = 0.0;
    String tollcurrancy = "";
    boolean isrideschedule = false;
    boolean isreqnow = false;
    ImageView prefBtnImageView;
    android.support.v7.app.AlertDialog pref_dialog;
    android.support.v7.app.AlertDialog tolltax_dialog;
    boolean isTollCostdilaogshow = false;
    MTextView noLocTitleTxt, noLocMsgTxt, pickupredirectTxt;
    boolean istollIgnore = false;
    ProgressBar progressBar;
    boolean isgpsview = false;
    boolean isnotification = false;
    boolean isdelivernow = false;
    boolean isdeliverlater = false;
    LinearLayout ridelaterView;
    MTextView rideLaterTxt;
    MTextView btn_type_ridelater;
    public boolean isTripStarted = false;
    boolean isTripEnded = false;
    boolean isDriverArrived = false;
    InternetConnection intCheck;
    Runnable runnable = null;
    boolean isfirstsearch = true;
    boolean isufxpayment = false;
    String appliedPromoCode = "";
    String userComment = "";
    boolean schedulrefresh = false;
    String iCabBookingId = "";
    boolean isRebooking = false;
    String type = "";
    //Noti
    boolean isufxbackview = false;
    String payableAmount = "";
    private String SelectedDriverId = "";
    private String tripStatus = "";
    private String currentTripId = "";
    private NoInternetConnectionDialog noInternetConn;
    private ActionBarDrawerToggle mDrawerToggle;

    public RelativeLayout rootRelView;
    Location tempDestLocation;
    Location tempPickUpLocation;

    public static void enableDisableViewGroup(ViewGroup viewGroup, boolean enabled) {
        int childCount = viewGroup.getChildCount();
        for (int i = 0; i < childCount; i++) {
            View view = viewGroup.getChildAt(i);
            view.setEnabled(enabled);
            if (view instanceof ViewGroup) {
                enableDisableViewGroup((ViewGroup) view, enabled);
            }
        }
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        generalFunc = new GeneralFunctions(getActContext());
        cabSelectionFrag = null;

        rootRelView = (RelativeLayout) findViewById(R.id.rootRelView);
        stopReceivingPrivateMsg();

        if (getIntent().getStringExtra("iCabBookingId") != null) {
            iCabBookingId = getIntent().getStringExtra("iCabBookingId");
        }

        if (getIntent().getStringExtra("type") != null) {
            type = getIntent().getStringExtra("type");
            bookingtype = getIntent().getStringExtra("type");
        }

        isRebooking = getIntent().getBooleanExtra("isRebooking", false);
        Utils.printLog("ActivityLoadTrack", "mainAct 2.4:" + System.currentTimeMillis());
        intCheck = new InternetConnection(getActContext());
        isufxpayment = getIntent().getBooleanExtra("isufxpayment", false);


        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        obj_userProfile = generalFunc.getJsonObject(userProfileJson);

        if (generalFunc.getJsonValueStr("APP_TYPE", obj_userProfile).equals(Utils.CabGeneralTypeRide_Delivery_UberX)) {
            RideDeliveryType = Utils.CabGeneralType_Ride;
        }

        isUfx = getIntent().getBooleanExtra("isufx", false);

        isnotification = getIntent().getBooleanExtra("isnotification", false);


        Utils.printLog("ActivityLoadTrack", "mainAct 2.5:" + System.currentTimeMillis());

        if (isUfx) {

            if (getIntent().getStringExtra("SelectDate") != null) {
                SelectDate = getIntent().getStringExtra("SelectDate");
            }
            if (pickUpLocation == null) {
                Location temploc = new Location("PickupLoc");
                if (getIntent().getStringExtra("latitude") != null) {
                    temploc.setLatitude(generalFunc.parseDoubleValue(0.0, getIntent().getStringExtra("latitude")));
                    temploc.setLongitude(generalFunc.parseDoubleValue(0.0, getIntent().getStringExtra("longitude")));
                    pickUpLocation = temploc;
                    pickUpLocationAddress = getIntent().getStringExtra("address");
                }
            }
        }


        Utils.printLog("ActivityLoadTrack", "mainAct 2.6:" + System.currentTimeMillis());
        app_type = generalFunc.getJsonValueStr("APP_TYPE", obj_userProfile);
        if (getIntent().hasExtra("tripId")) {
            tripId = getIntent().getStringExtra("tripId");
        }
        String TripDetails = generalFunc.getJsonValueStr("TripDetails", obj_userProfile);

        if (TripDetails != null && !TripDetails.equals("")) {
            tripId = generalFunc.getJsonValue("iTripId", TripDetails);
        }

        mainContent = (FrameLayout) findViewById(R.id.mainContent);
        userLocBtnImgView = (ImageView) findViewById(R.id.userLocBtnImgView);
        prefBtnImageView = (ImageView) findViewById(R.id.prefBtnImageView);
        prefrenceButtonEnable();

        if (!isUfx) {
            mainContent.setVisibility(View.VISIBLE);
            userLocBtnImgView.setVisibility(View.VISIBLE);

        }

        addDrawer = new AddDrawer(getActContext(), userProfileJson);

        if (app_type.equalsIgnoreCase("UberX")) {
            addDrawer.configDrawer(true);
            selectedCabTypeId = getIntent().getStringExtra("SelectedVehicleTypeId");
            vUberXCategoryName = getIntent().getStringExtra("vCategoryName");
        } else {
            addDrawer.configDrawer(false);
        }


        if (app_type.equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
            if (isUfx) {

                selectedCabTypeId = getIntent().getStringExtra("SelectedVehicleTypeId");
                vUberXCategoryName = getIntent().getStringExtra("vCategoryName");

                setMainHeaderView();
                redirectToMapOrList(Utils.Cab_UberX_Type_List, false);


            }
        }


        Utils.printLog("ActivityLoadTrack", "mainAct 2.8:" + System.currentTimeMillis());
        mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);


        mDrawerToggle = new ActionBarDrawerToggle(this, mDrawerLayout,
                1, 2) {

            /** Called when a drawer has settled in a completely closed state. */
            public void onDrawerClosed(View view) {
                super.onDrawerClosed(view);
                // getActionBar().setTitle("Closed");
                invalidateOptionsMenu(); // creates call to onPrepareOptionsMenu()
            }

            /** Called when a drawer has settled in a completely open state. */
            public void onDrawerOpened(View drawerView) {
                super.onDrawerOpened(drawerView);
                // getActionBar().setTitle("Opened");
                invalidateOptionsMenu(); // creates call to onPrepareOptionsMenu()
            }
        };

        // Set the drawer toggle as the DrawerListener
        mDrawerLayout.setDrawerListener(mDrawerToggle);


        ridelaterView = (LinearLayout) findViewById(R.id.ridelaterView);

        uberXNoDriverTxt = (MTextView) findViewById(R.id.uberXNoDriverTxt);
        deliverImgView = (SelectableRoundedImageView) findViewById(R.id.deliverImgView);
        deliverImgViewsel = (SelectableRoundedImageView) findViewById(R.id.deliverImgViewsel);
        rideImgView = (SelectableRoundedImageView) findViewById(R.id.rideImgView);
        rideImgViewsel = (SelectableRoundedImageView) findViewById(R.id.rideImgViewsel);
        otherImageView = (SelectableRoundedImageView) findViewById(R.id.otherImageView);
        otherImageViewsel = (SelectableRoundedImageView) findViewById(R.id.otherImageViewsel);

        noloactionview = (LinearLayout) findViewById(R.id.noloactionview);
        noLocTitleTxt = (MTextView) findViewById(R.id.noLocTitleTxt);
        noLocMsgTxt = (MTextView) findViewById(R.id.noLocMsgTxt);
        settingTxt = (MTextView) findViewById(R.id.settingTxt);
        pickupredirectTxt = (MTextView) findViewById(R.id.pickupredirectTxt);
        nolocmenuImgView = (ImageView) findViewById(R.id.nolocmenuImgView);
        nolocbackImgView = (ImageView) findViewById(R.id.nolocbackImgView);
        noLocImgView = (ImageView) findViewById(R.id.noLocImgView);
        rideLaterTxt = (MTextView) findViewById(R.id.rideLaterTxt);

        nolocmenuImgView.setOnClickListener(new setOnClickList());
        nolocbackImgView.setOnClickListener(new setOnClickList());
        settingTxt.setOnClickListener(new setOnClickList());
        pickupredirectTxt.setOnClickListener(new setOnClickList());

        ridelaterHandleView = (LinearLayout) findViewById(R.id.ridelaterHandleView);


        btn_type_ridelater = (MTextView) findViewById(R.id.btn_type_ridelater);

        if (type.equals(Utils.CabReqType_Now)) {
            btn_type_ridelater.setText(generalFunc.retrieveLangLBl("", "LBL_BOOK_LATER"));
        } else {

            btn_type_ridelater.setText(generalFunc.retrieveLangLBl("", "LBL_CHANGE"));
        }


        btn_type_ridelater.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Bundle bundle = new Bundle();
                bundle.putString("latitude", getIntent().getStringExtra("latitude"));
                bundle.putString("longitude", getIntent().getStringExtra("longitude"));
                bundle.putString("address", getIntent().getStringExtra("address"));
                bundle.putString("iUserAddressId", getIntent().getStringExtra("iUserAddressId"));
                bundle.putString("SelectedVehicleTypeId", getIntent().getStringExtra("SelectedVehicleTypeId"));
                bundle.putString("SelectvVehicleType", getIntent().getStringExtra("SelectvVehicleType"));
                bundle.putString("SelectvVehiclePrice", getIntent().getStringExtra("SelectvVehiclePrice"));


                bundle.putBoolean("isMain", true);
                new StartActProcess(getActContext()).startActForResult(ScheduleDateSelectActivity.class, bundle, Utils.SCHEDULE_REQUEST_CODE);

                schedulrefresh = true;


            }
        });


        Utils.printLog("ActivityLoadTrack", "mainAct 2.9:" + System.currentTimeMillis());
        progressBar = (ProgressBar) findViewById(R.id.mProgressBar);


        new CreateRoundedView(getActContext().getResources().getColor(R.color.white), Utils.dipToPixels(getActContext(), 35), 2,
                getActContext().getResources().getColor(R.color.white), deliverImgViewsel);

        deliverImgViewsel.setColorFilter(getActContext().getResources().getColor(R.color.black));

        new CreateRoundedView(getActContext().getResources().getColor(R.color.white), Utils.dipToPixels(getActContext(), 30), 2,
                getActContext().getResources().getColor(R.color.white), deliverImgView);

        deliverImgView.setColorFilter(getActContext().getResources().getColor(R.color.black));

        new CreateRoundedView(getActContext().getResources().getColor(R.color.white), Utils.dipToPixels(getActContext(), 35), 2,
                getActContext().getResources().getColor(R.color.white), rideImgViewsel);

        new CreateRoundedView(getActContext().getResources().getColor(R.color.white), Utils.dipToPixels(getActContext(), 30), 2,
                getActContext().getResources().getColor(R.color.white), rideImgView);

        new CreateRoundedView(getActContext().getResources().getColor(R.color.white), Utils.dipToPixels(getActContext(), 35), 2,
                getActContext().getResources().getColor(R.color.white), otherImageViewsel);

        new CreateRoundedView(getActContext().getResources().getColor(R.color.white), Utils.dipToPixels(getActContext(), 30), 2,
                getActContext().getResources().getColor(R.color.white), otherImageView);

        loaderView = (AVLoadingIndicatorView) findViewById(R.id.loaderView);
        uberXOnlineDriversRecyclerView = (RecyclerView) findViewById(R.id.uberXOnlineDriversRecyclerView);
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        userLocBtnImgView = (ImageView) findViewById(R.id.userLocBtnImgView);
        map = (SupportMapFragment) getSupportFragmentManager().findFragmentById(R.id.mapV2);
        sliding_layout = (SlidingUpPanelLayout) findViewById(R.id.sliding_layout);
        dragView = (RelativeLayout) findViewById(R.id.dragView);
        mainArea = (RelativeLayout) findViewById(R.id.mainArea);
        otherArea = findViewById(R.id.otherArea);
        mainContent = (FrameLayout) findViewById(R.id.mainContent);
        driver_detail_bottomView = (LinearLayout) findViewById(R.id.driver_detail_bottomView);
        pinImgView = (ImageView) findViewById(R.id.pinImgView);

        uberXDriverListArea = (RelativeLayout) findViewById(R.id.uberXDriverListArea);
        emeTapImgView = (ImageView) findViewById(R.id.emeTapImgView);
        rideArea = findViewById(R.id.rideArea);
        deliverArea = findViewById(R.id.deliverArea);

        prefBtnImageView.setOnClickListener(new setOnClickList());


        map.getMapAsync(MainActivity.this);

        setGeneralData();
        setLabels();

        if (generalFunc.isRTLmode()) {
            ((ImageView) findViewById(R.id.deliverImg)).setRotation(-180);
            ((ImageView) findViewById(R.id.rideImg)).setRotation(-180);
            ((ImageView) findViewById(R.id.rideImg)).setScaleY(-1);
            ((ImageView) findViewById(R.id.deliverImg)).setScaleY(-1);
        }


        new CreateAnimation(dragView, getActContext(), R.anim.design_bottom_sheet_slide_in, 100, true).startAnimation();


        userLocBtnImgView.setOnClickListener(new setOnClickList());
        emeTapImgView.setOnClickListener(new setOnClickList());
        rideArea.setOnClickListener(new setOnClickList());
        deliverArea.setOnClickListener(new setOnClickList());
        otherArea.setOnClickListener(new setOnClickList());

        if (savedInstanceState != null) {
            // Restore value of members from saved state
            String restratValue_str = savedInstanceState.getString("RESTART_STATE");

            if (restratValue_str != null && !restratValue_str.equals("") && restratValue_str.trim().equals("true")) {
                releaseScheduleNotificationTask();
                generalFunc.restartApp();
            }
        }

        String vTripStatus = generalFunc.getJsonValueStr("vTripStatus", obj_userProfile);


        generalFunc.deleteTripStatusMessages();
    }

    public void showprogress() {
        progressBar.setVisibility(View.VISIBLE);
        progressBar.setIndeterminate(true);
        progressBar.getIndeterminateDrawable().setColorFilter(
                getActContext().getResources().getColor(R.color.appThemeColor_1), android.graphics.PorterDuff.Mode.SRC_IN);

    }

    public void hideprogress() {
        if (progressBar != null) {
            progressBar.setVisibility(View.GONE);
        }
    }

    public void showLoader() {
        loaderView.setVisibility(View.VISIBLE);
    }

    public void hideLoader() {
        loaderView.setVisibility(View.GONE);

    }

    public void addcabselectionfragment() {
        setRiderDefaultView();
        //handle map height
        DisplayMetrics displaymetrics = new DisplayMetrics();
        getWindowManager().getDefaultDisplay().getMetrics(displaymetrics);
        int height = displaymetrics.heightPixels;
        ViewGroup.LayoutParams params = map.getView().getLayoutParams();
        params.height = height - Utils.dpToPx(getActContext(), 280);
        Utils.printLog("height", "::" + params.height);
        map.getView().setLayoutParams(params);

    }

    public void setSelectedDriverId(String driver_id) {
        SelectedDriverId = driver_id;
    }

    public void setLabels() {
        required_str = generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT");
        ((MTextView) findViewById(R.id.rideTxt)).setText(generalFunc.retrieveLangLBl("Ride", "LBL_RIDE"));
        ((MTextView) findViewById(R.id.selrideTxt)).setText(generalFunc.retrieveLangLBl("Ride", "LBL_RIDE"));
        ((MTextView) findViewById(R.id.deliverTxt)).setText(generalFunc.retrieveLangLBl("Deliver", "LBL_DELIVER"));
        ((MTextView) findViewById(R.id.otherTxt)).setText(generalFunc.retrieveLangLBl("Other", "LBL_OTHER"));

        noLocTitleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_LOCATION_SERVICES_TURNED_OFF"));
        noLocMsgTxt.setText(generalFunc.retrieveLangLBl("", "LBL_LOCATION_SERVICES_TURNED_OFF_DETAILS"));
        settingTxt.setText(generalFunc.retrieveLangLBl("", "LBL_TURN_ON_LOC_SERVICE"));
//        pickupredirectTxt.setText(generalFunc.retrieveLangLBl("Enter pickup address", "LBL_ENTER_PICKUP_TXT"));
        pickupredirectTxt.setText(generalFunc.retrieveLangLBl("Enter pickup address", "LBL_ENTER_PICK_UP_ADDRESS"));

        if (type.equals(Utils.CabReqType_Now)) {

            if (generalFunc.getJsonValue("RIDE_LATER_BOOKING_ENABLED", userProfileJson).equalsIgnoreCase("Yes")) {
                rideLaterTxt.setText(generalFunc.retrieveLangLBl("", "LBL_NO_PROVIDERS_AVAIL_NOW"));
                btn_type_ridelater.setVisibility(View.VISIBLE);
            } else {

                rideLaterTxt.setText(generalFunc.retrieveLangLBl("", "LBL_NO_PROVIDERS_AVAIL"));
                btn_type_ridelater.setVisibility(View.GONE);
            }
        } else {
            rideLaterTxt.setText(generalFunc.retrieveLangLBl("", "LBL_NO_PROVIDERS_AVAIL_LATER"));
            btn_type_ridelater.setVisibility(View.GONE);
        }

    }

    public boolean isPubNubEnabled() {
        String ENABLE_PUBNUB = generalFunc.getJsonValueStr("ENABLE_PUBNUB", obj_userProfile);

        return ENABLE_PUBNUB.equalsIgnoreCase("Yes");
    }

    @Override
    protected void onSaveInstanceState(Bundle outState) {
        // TODO Auto-generated method stub
        try {
            outState.putString("RESTART_STATE", "true");
            super.onSaveInstanceState(outState);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void setGeneralData() {
        generalFunc.storedata(CommonUtilities.MOBILE_VERIFICATION_ENABLE_KEY, generalFunc.getJsonValueStr("MOBILE_VERIFICATION_ENABLE", obj_userProfile));
        String DRIVER_REQUEST_METHOD = generalFunc.getJsonValueStr("DRIVER_REQUEST_METHOD", obj_userProfile);

        this.DRIVER_REQUEST_METHOD = DRIVER_REQUEST_METHOD.equals("") ? "All" : DRIVER_REQUEST_METHOD;

        generalFunc.storedata(CommonUtilities.REFERRAL_SCHEME_ENABLE, generalFunc.getJsonValueStr("REFERRAL_SCHEME_ENABLE", obj_userProfile));
        generalFunc.storedata(CommonUtilities.WALLET_ENABLE, generalFunc.getJsonValueStr("WALLET_ENABLE", obj_userProfile));

    }

    public MainHeaderFragment getMainHeaderFrag() {
        return mainHeaderFrag;
    }


    @Override
    public void onMapReady(GoogleMap googleMap) {

        (findViewById(R.id.LoadingMapProgressBar)).setVisibility(View.GONE);


        if (googleMap == null) {
            return;
        }

        this.gMap = googleMap;


        if (generalFunc.checkLocationPermission(true) == true) {
            getMap().setMyLocationEnabled(true);
            getMap().getUiSettings().setTiltGesturesEnabled(false);
            getMap().getUiSettings().setCompassEnabled(false);
            getMap().getUiSettings().setMyLocationButtonEnabled(false);

            getMap().setOnMarkerClickListener(new GoogleMap.OnMarkerClickListener() {
                @Override
                public boolean onMarkerClick(Marker marker) {
                    marker.hideInfoWindow();

                    if (isUfx) {
                        if (isMarkerClickable == true) {
                            markerId = marker.getId();

                        }
                    } else {
                        try {

                            getMap().getUiSettings().setMapToolbarEnabled(false);
                            if (marker.getTag().equals("1")) {
                                if (mainHeaderFrag != null) {
                                    mainHeaderFrag.pickupLocArea1.performClick();
                                }

                            } else if (marker.getTag().equals("2")) {
                                if (mainHeaderFrag != null) {
                                    mainHeaderFrag.destarea.performClick();
                                }

                            }
                        } catch (Exception e) {

                        }

                    }
                    return true;

                }
            });


            getMap().setOnMapClickListener(this);


        }

        String vTripStatus = generalFunc.getJsonValueStr("vTripStatus", obj_userProfile);

        if (vTripStatus != null && (vTripStatus.equals("Active") || vTripStatus.equals("On Going Trip"))) {
            getMap().setMyLocationEnabled(false);
            String tripDetailJson = generalFunc.getJsonValueStr("TripDetails", obj_userProfile);

            if (tripDetailJson != null && !tripDetailJson.trim().equals("")) {
                double latitude = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("tStartLat", tripDetailJson));
                double longitude = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("tStartLong", tripDetailJson));
                Location loc = new Location("gps");
                loc.setLatitude(latitude);
                loc.setLongitude(longitude);
                onLocationUpdate(loc);
            }
        }

        if (getLastLocation != null) {
            getLastLocation.stopLocationUpdates();
            getLastLocation = null;
        }
        getLastLocation = new GetLocationUpdates(getActContext(), Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, true, this);

    }

    public void checkDrawerState() {
        if (mDrawerLayout.isDrawerOpen(GravityCompat.START) == true) {
            closeDrawer();
        } else {
            openDrawer();
        }
    }

    public void closeDrawer() {
        mDrawerLayout.closeDrawer(GravityCompat.START);
    }

    public void openDrawer() {
        mDrawerLayout.openDrawer(GravityCompat.START);
    }

    @Override
    public void onMapClick(LatLng latLng) {

        sliding_layout.setPanelState(SlidingUpPanelLayout.PanelState.COLLAPSED);
    }

    public GoogleMap getMap() {
        return this.gMap;
    }

    public void setShadow() {
        if (cabSelectionFrag != null) {
            cabSelectionFrag.setShadow();
        }
    }

    public void setUserLocImgBtnMargin(int margin) {
    }

    public void initializeLoadCab() {
        if (isDriverAssigned == true) {
            return;
        }


        loadAvailCabs = new LoadAvailableCab(getActContext(), generalFunc, selectedCabTypeId, userLocation,
                getMap(), userProfileJson);


        loadAvailCabs.pickUpAddress = pickUpLocationAddress;
        loadAvailCabs.currentGeoCodeResult = currentGeoCodeObject;
        loadAvailCabs.changeCabs();
    }

    public void getWalletBalDetails() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "GetMemberWalletBalance");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), false, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(final String responseString) {


                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail) {
                        try {
                            generalFunc.storedata(CommonUtilities.ISWALLETBALNCECHANGE, "No");
                            String userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
                            JSONObject object = generalFunc.getJsonObject(userProfileJson);
                            object.put("user_available_balance", generalFunc.getJsonValue("MemberBalance", responseString));
                            generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, object.toString());

                            userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
                            obj_userProfile = generalFunc.getJsonObject(userProfileJson);


                            setUserInfo();
                        } catch (Exception e) {

                        }

                    }


                }
            }
        });
        exeWebServer.execute();
    }


    public void showMessageWithAction(View view, String message, final Bundle bn) {
        Snackbar snackbar = Snackbar
                .make(view, message, Snackbar.LENGTH_INDEFINITE).setAction(generalFunc.retrieveLangLBl("", "LBL_BTN_VERIFY_TXT"), new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {

                        new StartActProcess(getActContext()).startActForResult(VerifyInfoActivity.class, bn, Utils.VERIFY_INFO_REQ_CODE);

                    }
                });
        snackbar.setActionTextColor(getActContext().getResources().getColor(R.color.appThemeColor_1));
        snackbar.setDuration(10000);
        snackbar.show();
    }

    public void initializeViews() {
        String eEmailVerified = generalFunc.getJsonValueStr("eEmailVerified", obj_userProfile);
        String ePhoneVerified = generalFunc.getJsonValueStr("ePhoneVerified", obj_userProfile);

        if (!eEmailVerified.equalsIgnoreCase("YES") ||
                !ePhoneVerified.equalsIgnoreCase("YES")) {

            Bundle bn = new Bundle();
            if (!eEmailVerified.equalsIgnoreCase("YES") &&
                    !ePhoneVerified.equalsIgnoreCase("YES")) {
                bn.putString("msg", "DO_EMAIL_PHONE_VERIFY");
            } else if (!eEmailVerified.equalsIgnoreCase("YES")) {
                bn.putString("msg", "DO_EMAIL_VERIFY");
            } else if (!ePhoneVerified.equalsIgnoreCase("YES")) {
                bn.putString("msg", "DO_PHONE_VERIFY");
            }

            showMessageWithAction(mainArea, generalFunc.retrieveLangLBl("", "LBL_ACCOUNT_VERIFY_ALERT_RIDER_TXT"), bn);
        }
        String vTripStatus = generalFunc.getJsonValueStr("vTripStatus", obj_userProfile);

        if (vTripStatus != null && (vTripStatus.equals("Active") || vTripStatus.equals("On Going Trip"))) {

            JSONObject tripDetailJson = generalFunc.getJsonObject("TripDetails", obj_userProfile);

            eTripType = generalFunc.getJsonValueStr("eType", tripDetailJson);

            if (eTripType.equals("Deliver")) {
                eTripType = CabGeneralType_Deliver;

            }
            if (generalFunc.getJsonValueStr("APP_TYPE", obj_userProfile).equals(Utils.CabGeneralTypeRide_Delivery_UberX)) {

                if (eTripType.equals(Utils.CabGeneralType_Ride) || isDeliver(eTripType)) {

//
                    if (!TextUtils.isEmpty(tripId)) {
                        //Assign driver

                        isDriverAssigned = true;
                        if (driverAssignedHeaderFrag != null) {
                            driverAssignedHeaderFrag.releaseAllTask();
                            driverAssignedHeaderFrag = null;
                        }
                        configureAssignedDriver(true);

                        configureDeliveryView(true);
                    } else {
                        setMainHeaderView();
                    }
                } else {
                    setMainHeaderView();
                }

            } else if (generalFunc.getJsonValueStr("APP_TYPE", obj_userProfile).equals(Utils.CabGeneralType_UberX)) {
                setMainHeaderView();
            } else {
                isDriverAssigned = true;

                if (!TextUtils.isEmpty(tripId)) {
                    addDrawer.setIsDriverAssigned(true);
                    if (driverAssignedHeaderFrag != null) {
                        driverAssignedHeaderFrag.releaseAllTask();
                        driverAssignedHeaderFrag = null;
                    }
                    configureAssignedDriver(true);

                    configureDeliveryView(true);
                } else {
                    setMainHeaderView();
                }

            }

        } else {
            setMainHeaderView();
        }

        Utils.runGC();
    }

    private void setMainHeaderView() {
        try {


            if (mainHeaderFrag == null) {
                if (generalFunc.isLocationEnabled()) {
                    isFrompickupaddress = true;

                }
                mainHeaderFrag = new MainHeaderFragment();
                Bundle bundle = new Bundle();
                bundle.putBoolean("isUfx", isUfx);
                mainHeaderFrag.setArguments(bundle);
                mainHeaderFrag.setGoogleMapInstance(getMap());
            }
            if (mainHeaderFrag != null) {
                mainHeaderFrag.releaseAddressFinder();
            }
            try {
                super.onPostResume();

            } catch (Exception e)

            {
                Utils.printLog("Exception", e.toString());

            }

            getSupportFragmentManager().beginTransaction()
                    .replace(R.id.headerContainer, mainHeaderFrag).commit();

            ConfigPubNub.getInstance().setTripId("", "");
            ConfigPubNub.getInstance().isSessionout = false;

            configureDeliveryView(false);
        } catch (Exception e)

        {
            Utils.printLog("Exception", e.toString());

        }

    }

    private void setRiderDefaultView() {

        if (cabSelectionFrag == null) {
            Bundle bundle = new Bundle();
            bundle.putString("RideDeliveryType", RideDeliveryType);
            cabSelectionFrag = new CabSelectionFragment();
            cabSelectionFrag.setArguments(bundle);


            pinImgView.setVisibility(View.GONE);


            RelativeLayout.LayoutParams params = (RelativeLayout.LayoutParams) (userLocBtnImgView).getLayoutParams();
            params.bottomMargin = Utils.dipToPixels(getActContext(), 240);

        }

        if (mainHeaderFrag != null) {
            mainHeaderFrag.addAddressFinder();
        }

        if (driverAssignedHeaderFrag != null) {
            pinImgView.setVisibility(View.GONE);
            RelativeLayout.LayoutParams params = (RelativeLayout.LayoutParams) (userLocBtnImgView).getLayoutParams();
            params.bottomMargin = Utils.dipToPixels(getActContext(), 200);

        }

        setCurrentType();

        try {
            super.onPostResume();

        } catch (Exception e)

        {

        }

        getSupportFragmentManager().beginTransaction()
                .replace(R.id.dragView, cabSelectionFrag).commit();


        configureDeliveryView(false);

    }

    private void setCurrentType() {

        if (cabSelectionFrag == null) {
            return;
        }
        if (app_type.equalsIgnoreCase("Delivery")) {
            // cabSelectionFrag.currentCabGeneralType = CabGeneralType_Deliver;
            cabSelectionFrag.currentCabGeneralType = "Deliver";
        } else if (app_type.equalsIgnoreCase("UberX")) {
            cabSelectionFrag.currentCabGeneralType = Utils.CabGeneralType_UberX;
        } else if (app_type.equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
            cabSelectionFrag.currentCabGeneralType = Utils.CabGeneralType_UberX;
        } else if (app_type.equalsIgnoreCase("Ride-Delivery")) {
            if (isDeliver(RideDeliveryType)) {
                cabSelectionFrag.currentCabGeneralType = "Deliver";
            } else {
                cabSelectionFrag.currentCabGeneralType = Utils.CabGeneralType_Ride;
            }

        } else {
            cabSelectionFrag.currentCabGeneralType = Utils.CabGeneralType_Ride;
        }
    }

    public void configureDeliveryView(boolean isHidden) {
        if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
            if (!isUfx) {
                (findViewById(R.id.deliveryArea)).setVisibility(View.VISIBLE);
                otherArea.setVisibility(View.VISIBLE);
                setUserLocImgBtnMargin(190);
            } else {
                (findViewById(R.id.deliveryArea)).setVisibility(View.GONE);
                setUserLocImgBtnMargin(105);
            }
        } else if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase("Ride-Delivery") && isHidden == false) {
            (findViewById(R.id.deliveryArea)).setVisibility(View.VISIBLE);
            setUserLocImgBtnMargin(190);
        } else {
            (findViewById(R.id.deliveryArea)).setVisibility(View.GONE);
            setUserLocImgBtnMargin(105);
        }

        if (!isHidden) {
            ConfigPubNub.getInstance().setTripId("", "");
        }
    }


    public void configDestinationMode(boolean isDestinationMode) {
        this.isDestinationMode = isDestinationMode;
        try {


            if (isDestinationMode == false) {
                if (loadAvailCabs != null) {
                    loadAvailCabs.filterDrivers(false);
                }

                animateToLocation(getPickUpLocation().getLatitude(), getPickUpLocation().getLongitude());
                if (cabSelectionFrag != null) {
                    noCabAvail = false;
                    changeLable();

                }
            } else {
                pinImgView.setImageResource(R.drawable.pin_dest_select);
                if (cabSelectionFrag != null) {
                    if (loadAvailCabs != null) {

                        if (loadAvailCabs.isAvailableCab) {
                            changeLable();
                            noCabAvail = true;
                        }
                    }
                }

                if (timeval.equalsIgnoreCase("\n" + "--")) {
                    noCabAvail = false;
                } else {
                    noCabAvail = true;
                }
                changeLable();
                pinImgView.setImageResource(R.drawable.pin_dest_select);
                if (isDestinationAdded == true && !getDestLocLatitude().trim().equals("") && !getDestLocLongitude().trim().equals("")) {
                    animateToLocation(generalFunc.parseDoubleValue(0.0, getDestLocLatitude()), generalFunc.parseDoubleValue(0.0, getDestLocLongitude()));
                }

            }
            changeLable();

            if (mainHeaderFrag != null) {
                mainHeaderFrag.configDestinationMode(isDestinationMode);
            }
        } catch (Exception e) {

        }
    }

    private void changeLable() {
        if (cabSelectionFrag != null) {
            cabSelectionFrag.setLabels(false);
        }
    }

    public void animateToLocation(double latitude, double longitude) {
        Utils.printLog("animateToLocation", "::");

        if (latitude != 0.0 && longitude != 0.0) {
            CameraPosition cameraPosition = new CameraPosition.Builder().target(
                    new LatLng(latitude, longitude))
                    .zoom(gMap.getCameraPosition().zoom).build();
            gMap.animateCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
        }
    }

    public void animateToLocation(double latitude, double longitude, float zoom) {
        try {


            Utils.printLog("animateToLocation", "::" + "zoom");

            if (latitude != 0.0 && longitude != 0.0) {
                CameraPosition cameraPosition = new CameraPosition.Builder().target(
                        new LatLng(latitude, longitude))
                        .zoom(zoom).build();
                gMap.animateCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
            }
        } catch (Exception e) {

        }
    }

    public void configureAssignedDriver(boolean isAppRestarted) {
        isDriverAssigned = true;
        addDrawer.setIsDriverAssigned(isDriverAssigned);
        driverDetailFrag = new DriverDetailFragment();
        driverAssignedHeaderFrag = new DriverAssignedHeaderFragment();

        Bundle bn = new Bundle();
        bn.putString("isAppRestarted", "" + isAppRestarted);
        RelativeLayout.LayoutParams params = (RelativeLayout.LayoutParams) (userLocBtnImgView).getLayoutParams();
        params.bottomMargin = Utils.dipToPixels(getActContext(), 200);

        driverAssignedData = new HashMap<>();
        releaseScheduleNotificationTask();
        if (isAppRestarted == true) {

            JSONObject tripDetailJson = generalFunc.getJsonObject("TripDetails", userProfileJson);
            JSONObject driverDetailJson = generalFunc.getJsonObject("DriverDetails", userProfileJson);
            JSONObject driverCarDetailJson = generalFunc.getJsonObject("DriverCarDetails", userProfileJson);

            String vTripPaymentMode = generalFunc.getJsonValueStr("vTripPaymentMode", tripDetailJson);
            String tEndLat = generalFunc.getJsonValueStr("tEndLat", tripDetailJson);
            String tEndLong = generalFunc.getJsonValueStr("tEndLong", tripDetailJson);
            String tDaddress = generalFunc.getJsonValueStr("tDaddress", tripDetailJson);

            if (vTripPaymentMode.equals("Cash")) {
                isCashSelected = true;
            } else {
                isCashSelected = false;
            }

            assignedDriverId = generalFunc.getJsonValueStr("iDriverId", tripDetailJson);
            assignedTripId = generalFunc.getJsonValueStr("iTripId", tripDetailJson);
            eTripType = generalFunc.getJsonValueStr("eType", tripDetailJson);

            if (!tEndLat.equals("0.0") && !tEndLong.equals("0.0")
                    && !tDaddress.equals("Not Set") && !tEndLat.equals("") && !tEndLong.equals("")
                    && !tDaddress.equals("")) {
                isDestinationAdded = true;
                destAddress = tDaddress;
                destLocLatitude = tEndLat;
                destLocLongitude = tEndLong;
            }

            driverAssignedData.put("destLatitude", generalFunc.getJsonValueStr("tEndLat", tripDetailJson));
            driverAssignedData.put("destLongitude", generalFunc.getJsonValueStr("tEndLong", tripDetailJson));
            driverAssignedData.put("PickUpLatitude", generalFunc.getJsonValueStr("tStartLat", tripDetailJson));
            driverAssignedData.put("PickUpLongitude", generalFunc.getJsonValueStr("tStartLong", tripDetailJson));
            driverAssignedData.put("eFlatTrip", generalFunc.getJsonValueStr("eFlatTrip", tripDetailJson));
            driverAssignedData.put("vDeliveryConfirmCode", generalFunc.getJsonValueStr("vDeliveryConfirmCode", tripDetailJson));
            driverAssignedData.put("PickUpAddress", generalFunc.getJsonValueStr("tSaddress", tripDetailJson));
            driverAssignedData.put("vVehicleType", generalFunc.getJsonValueStr("vVehicleType", tripDetailJson));
            driverAssignedData.put("eIconType", generalFunc.getJsonValueStr("eIconType", tripDetailJson));
            driverAssignedData.put("eType", generalFunc.getJsonValueStr("eType", tripDetailJson));
            driverAssignedData.put("DriverTripStatus", generalFunc.getJsonValueStr("vTripStatus", driverDetailJson));
            driverAssignedData.put("DriverPhone", generalFunc.getJsonValueStr("vPhone", driverDetailJson));
            driverAssignedData.put("DriverPhoneCode", generalFunc.getJsonValueStr("vCode", driverDetailJson));
            driverAssignedData.put("DriverRating", generalFunc.getJsonValueStr("vAvgRating", driverDetailJson));
            driverAssignedData.put("DriverAppVersion", generalFunc.getJsonValueStr("iAppVersion", driverDetailJson));
            driverAssignedData.put("DriverLatitude", generalFunc.getJsonValueStr("vLatitude", driverDetailJson));
            driverAssignedData.put("DriverLongitude", generalFunc.getJsonValueStr("vLongitude", driverDetailJson));
            driverAssignedData.put("DriverImage", generalFunc.getJsonValueStr("vImage", driverDetailJson));
            driverAssignedData.put("DriverName", generalFunc.getJsonValueStr("vName", driverDetailJson));
            driverAssignedData.put("DriverCarPlateNum", generalFunc.getJsonValueStr("vLicencePlate", driverCarDetailJson));
            driverAssignedData.put("DriverCarName", generalFunc.getJsonValueStr("make_title", driverCarDetailJson));
            driverAssignedData.put("DriverCarModelName", generalFunc.getJsonValueStr("model_title", driverCarDetailJson));
            driverAssignedData.put("DriverCarColour", generalFunc.getJsonValueStr("vColour", driverCarDetailJson));
            driverAssignedData.put("vCode", generalFunc.getJsonValueStr("vCode", driverDetailJson));


        } else {

            if (currentLoadedDriverList == null) {
                generalFunc.restartApp();
                return;
            }

            boolean isDriverIdMatch = false;
            for (int i = 0; i < currentLoadedDriverList.size(); i++) {
                HashMap<String, String> driverDataMap = currentLoadedDriverList.get(i);
                String iDriverId = driverDataMap.get("driver_id");

                if (iDriverId.equals(assignedDriverId)) {
                    isDriverIdMatch = true;

                    if (destLocation != null) {

                        driverAssignedData.put("destLatitude", destLocation.getLatitude() + "");
                        driverAssignedData.put("destLongitude", destLocation.getLongitude() + "");
                    }
                    driverAssignedData.put("PickUpLatitude", "" + getPickUpLocation().getLatitude());
                    driverAssignedData.put("PickUpLongitude", "" + getPickUpLocation().getLongitude());

                    if (mainHeaderFrag != null) {
                        driverAssignedData.put("PickUpAddress", mainHeaderFrag.getPickUpAddress());
                    } else {
                        driverAssignedData.put("PickUpAddress", pickUpLocationAddress);
                    }

                    driverAssignedData.put("vVehicleType", generalFunc.getSelectedCarTypeData(selectedCabTypeId, cabTypesArrList, "vVehicleType"));
                    driverAssignedData.put("eIconType", generalFunc.getSelectedCarTypeData(selectedCabTypeId, cabTypesArrList, "eIconType"));
                    driverAssignedData.put("vDeliveryConfirmCode", "");

                    driverAssignedData.put("DriverTripStatus", "");
                    driverAssignedData.put("DriverPhone", driverDataMap.get("vPhone_driver"));
                    driverAssignedData.put("DriverPhoneCode", driverDataMap.get("vPhoneCode_driver"));
                    driverAssignedData.put("DriverRating", driverDataMap.get("average_rating"));
                    driverAssignedData.put("DriverAppVersion", driverDataMap.get("iAppVersion"));
                    driverAssignedData.put("DriverLatitude", driverDataMap.get("Latitude"));
                    driverAssignedData.put("DriverLongitude", driverDataMap.get("Longitude"));
                    driverAssignedData.put("DriverImage", driverDataMap.get("driver_img"));
                    driverAssignedData.put("DriverName", driverDataMap.get("Name"));
                    driverAssignedData.put("DriverCarPlateNum", driverDataMap.get("vLicencePlate"));
                    driverAssignedData.put("DriverCarName", driverDataMap.get("make_title"));
                    driverAssignedData.put("DriverCarModelName", driverDataMap.get("model_title"));
                    driverAssignedData.put("DriverCarColour", driverDataMap.get("vColour"));
                    driverAssignedData.put("eType", getCurrentCabGeneralType());

                    break;
                }
            }

            if (isDriverIdMatch == false) {
                generalFunc.restartApp();
                return;
            }
        }

        driverAssignedData.put("iDriverId", assignedDriverId);
        driverAssignedData.put("iTripId", assignedTripId);


        ConfigPubNub.getInstance().setTripId(assignedDriverId, assignedTripId);

        driverAssignedData.put("PassengerName", generalFunc.getJsonValueStr("vName", obj_userProfile));
        driverAssignedData.put("PassengerImageName", generalFunc.getJsonValueStr("vImgName", obj_userProfile));

        bn.putSerializable("TripData", driverAssignedData);
        driverAssignedHeaderFrag.setArguments(bn);
        driverAssignedHeaderFrag.setGoogleMap(getMap());
        if (!TextUtils.isEmpty(tripId)) {
            driverAssignedHeaderFrag.isBackVisible = true;

            ConfigPubNub.getInstance().setTripId(tripId, assignedDriverId);

        }
        driverDetailFrag.setArguments(bn);


        Location pickUpLoc = new Location("");
        pickUpLoc.setLatitude(generalFunc.parseDoubleValue(0.0, driverAssignedData.get("PickUpLatitude")));
        pickUpLoc.setLongitude(generalFunc.parseDoubleValue(0.0, driverAssignedData.get("PickUpLongitude")));
        this.pickUpLocation = pickUpLoc;

        if (mainHeaderFrag != null) {
            mainHeaderFrag.releaseResources();
            mainHeaderFrag = null;
        }

        if (cabSelectionFrag != null) {
            cabSelectionFrag.releaseResources();
            cabSelectionFrag = null;
        }

        Utils.runGC();

        Bundle extras = getIntent().getExtras();


        if (isnotification) {
            chatMsg();

        }

        setPanelHeight(175);

        try {
            super.onPostResume();
        } catch (Exception e) {

        }

        if (driverDetailFrag != null) {
            deliverArea.setVisibility(View.GONE);
            otherArea.setEnabled(false);
            deliverArea.setEnabled(false);
            rideArea.setEnabled(false);
        }

        if (!isFinishing()) {

            gMap.clear();
            DisplayMetrics displaymetrics = new DisplayMetrics();
            getWindowManager().getDefaultDisplay().getMetrics(displaymetrics);
            int height = displaymetrics.heightPixels;
            ViewGroup.LayoutParams param = map.getView().getLayoutParams();
            param.height = height;
            map.getView().setLayoutParams(param);

            getMap().setMyLocationEnabled(false);

            getMap().setPadding(0, 0, 0, Utils.dpToPx(getActContext(), 232));
            getSupportFragmentManager().beginTransaction()
                    .replace(R.id.headerContainer, driverAssignedHeaderFrag).commit();


            if (!isAppRestarted) {
                if (isFixFare) {
                    if (driverAssignedHeaderFrag != null) {
                        driverAssignedHeaderFrag.eConfirmByUser = "Yes";
                        driverAssignedHeaderFrag.handleEditDest();
                    }
                }

            }


            getSupportFragmentManager().beginTransaction()
                    .replace(R.id.dragView, driverDetailFrag).commit();

            RelativeLayout.LayoutParams paramsval = (RelativeLayout.LayoutParams) (userLocBtnImgView).getLayoutParams();
            paramsval.bottomMargin = Utils.dipToPixels(getActContext(), 180);


            if (generalFunc.retrieveValue("OPEN_CHAT").equals("Yes")) {
                generalFunc.storedata("OPEN_CHAT", "No");
                Bundle bnChat = new Bundle();

                bnChat.putString("iFromMemberId", driverAssignedData.get("iDriverId"));
                bnChat.putString("FromMemberImageName", driverAssignedData.get("DriverImage"));
                bnChat.putString("iTripId", driverAssignedData.get("iTripId"));
                bnChat.putString("FromMemberName", driverAssignedData.get("DriverName"));


                new StartActProcess(getActContext()).startActWithData(ChatActivity.class, bnChat);
            }

        } else

        {
            generalFunc.restartApp();
        }


    }

    @Override
    public void onLocationUpdate(Location location) {

        Utils.printLog("onLocationUpdate", ":: Mainact");
        if (location == null) {
            return;
        }
        this.userLocation = location;


        if (isFirstLocation == true) {
            initializeViews();
            CameraPosition cameraPosition = cameraForUserPosition();

            if (cameraPosition != null) {
                getMap().moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
                isMovedToCurLoc = true;
            }

            isFirstLocation = false;
        }
    }

    public void setETA(String time) {

        timeval = time;
        if (cabSelectionFrag != null) {

            Utils.printLog("handleSourceMarker etaTime", "::" + time);
            cabSelectionFrag.handleSourceMarker(time);
            cabSelectionFrag.mangeMrakerPostion();
        }
    }

    public CameraPosition cameraForUserPosition() {

        try {
            if (cabSelectionFrag != null) {
                return null;
            }

            Utils.printLog("cameraForUserPosition", "::called");

            double currentZoomLevel = getMap() == null ? Utils.defaultZomLevel : getMap().getCameraPosition().zoom;
            if (Utils.defaultZomLevel > currentZoomLevel) {
                currentZoomLevel = Utils.defaultZomLevel;
            }
            String TripDetails = generalFunc.getJsonValue("TripDetails", userProfileJson);

            String vTripStatus = generalFunc.getJsonValue("vTripStatus", userProfileJson);
            if (generalFunc.isLocationEnabled()) {

                double startLat = 0.0;
                double startLong = 0.0;

                if (vTripStatus != null && startLat != 0.0 && startLong != 0.0 && ((vTripStatus.equals("Active") || vTripStatus.equals("On Going Trip")))) {

                    Location tempickuploc = new Location("temppickkup");

                    tempickuploc.setLatitude(startLat);
                    tempickuploc.setLongitude(startLong);

                    CameraPosition cameraPosition = new CameraPosition.Builder().target(new LatLng(tempickuploc.getLatitude(), tempickuploc.getLongitude()))
                            .zoom((float) currentZoomLevel).build();


                    return cameraPosition;


                } else {
//
                    if (Utils.defaultZomLevel > currentZoomLevel) {
                        currentZoomLevel = Utils.defaultZomLevel;
                    }
                    if (userLocation != null) {
                        CameraPosition cameraPosition = new CameraPosition.Builder().target(new LatLng(this.userLocation.getLatitude(), this.userLocation.getLongitude()))
                                .zoom((float) currentZoomLevel).build();

                        pickUpLocation = userLocation;

                        return cameraPosition;
                    } else {
                        return null;
                    }
                }
            } else if (userLocation != null) {
                if (Utils.defaultZomLevel > currentZoomLevel) {
                    currentZoomLevel = Utils.defaultZomLevel;
                }
                if (userLocation != null) {
                    CameraPosition cameraPosition = new CameraPosition.Builder().target(new LatLng(this.userLocation.getLatitude(), this.userLocation.getLongitude()))
                            .zoom((float) currentZoomLevel).build();

                    pickUpLocation = userLocation;

                    return cameraPosition;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } catch (Exception e) {

        }
        return null;


    }

    public void redirectToMapOrList(String choiceType, boolean autoLoad) {

        if (autoLoad == true && currentUberXChoiceType.equalsIgnoreCase(Utils.Cab_UberX_Type_Map)) {
            return;
        }

        this.currentUberXChoiceType = choiceType;

        mainHeaderFrag.listTxt.setBackgroundColor(choiceType.equalsIgnoreCase(Utils.Cab_UberX_Type_List) ?
                Color.parseColor("#FFFFFF") : getResources().getColor(R.color.appThemeColor_1));
        mainHeaderFrag.mapTxt.setBackgroundColor(choiceType.equalsIgnoreCase(Utils.Cab_UberX_Type_List) ?
                getResources().getColor(R.color.appThemeColor_1) : Color.parseColor("#FFFFFF"));

        mainHeaderFrag.mapTxt.setTextColor(choiceType.equalsIgnoreCase(Utils.Cab_UberX_Type_List) ?
                Color.parseColor("#FFFFFF") : Color.parseColor("#1C1C1C"));
        mainHeaderFrag.listTxt.setTextColor(choiceType.equalsIgnoreCase(Utils.Cab_UberX_Type_List) ?
                Color.parseColor("#1C1C1C") : Color.parseColor("#FFFFFF"));
        if (driver_detail_bottomView != null || driver_detail_bottomView.getVisibility() == View.VISIBLE) {

            driver_detail_bottomView.setVisibility(View.GONE);
        }
        if (choiceType.equalsIgnoreCase(Utils.Cab_UberX_Type_List)) {

            uberXNoDriverTxt.setText(generalFunc.retrieveLangLBl("No Provider Available", "LBL_NO_PROVIDER_AVAIL_TXT"));

            if (!isUfxRideLater) {

                uberXDriverListArea.setVisibility(View.VISIBLE);
                uberXNoDriverTxt.setVisibility(View.GONE);
                ridelaterView.setVisibility(View.GONE);

                uberXDriverList.clear();
            }
        } else {

            (findViewById(R.id.driverListAreaLoader)).setVisibility(View.GONE);
            mainContent.setVisibility(View.VISIBLE);
            uberXDriverListArea.setVisibility(View.GONE);
        }
    }

    public void OpenCardPaymentAct(boolean fromcabselection) {
        iswallet = true;
        Bundle bn = new Bundle();
        // bn.putString("UserProfileJson", userProfileJson);
        bn.putBoolean("fromcabselection", fromcabselection);
        new StartActProcess(getActContext()).startActForResult(CardPaymentActivity.class, bn, Utils.CARD_PAYMENT_REQ_CODE);
    }

    public boolean isPickUpLocationCorrect() {
        String pickUpLocAdd = mainHeaderFrag != null ? (mainHeaderFrag.getPickUpAddress().equals(
                generalFunc.retrieveLangLBl("", "LBL_SELECTING_LOCATION_TXT")) ? "" : mainHeaderFrag.getPickUpAddress()) : "";

        if (!pickUpLocAdd.equals("")) {
            return true;
        }
        return false;
    }

    public void continuePickUpProcess() {
        String pickUpLocAdd = mainHeaderFrag != null ? (mainHeaderFrag.getPickUpAddress().equals(
                generalFunc.retrieveLangLBl("", "LBL_SELECTING_LOCATION_TXT")) ? "" : mainHeaderFrag.getPickUpAddress()) : "";

        if (!pickUpLocAdd.equals("")) {

            if (isUfx) {
                checkSurgePrice("", null);
            } else if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase("UberX")) {
                checkSurgePrice("", null);
            } else {
                setCabReqType(Utils.CabReqType_Now);
                checkSurgePrice("", null);
            }
        }
    }

    public String getCurrentCabGeneralType() {

        if (app_type.equalsIgnoreCase("Ride-Delivery")) {
            if (!RideDeliveryType.equals("")) {

                if (isDeliver(RideDeliveryType)) {
                    return "Deliver";
                } else {
                    return RideDeliveryType;
                }

            } else {

                return Utils.CabGeneralType_Ride;
            }
        }

        if (cabSelectionFrag != null) {
            return cabSelectionFrag.getCurrentCabGeneralType();
        } else if (!eTripType.trim().equals("")) {
            return eTripType;
        }

        if (isUfx) {
            return Utils.CabGeneralType_UberX;
        }
        return app_type;
    }

    public void chooseDateTime() {


        if (isPickUpLocationCorrect() == false) {
            return;
        }

        new SlideDateTimePicker.Builder(getSupportFragmentManager())
                .setListener(new SlideDateTimeListener() {
                    @Override
                    public void onDateTimeSet(Date date) {


                        selectedDateTime = Utils.convertDateToFormat("yyyy-MM-dd HH:mm:ss", date);
                        selectedDateTimeZone = Calendar.getInstance().getTimeZone().getID();

                        if (Utils.isValidTimeSelect(date, TimeUnit.HOURS.toMillis(1)) == false) {

                            generalFunc.showGeneralMessage(generalFunc.retrieveLangLBl("Invalid pickup time", "LBL_INVALID_PICKUP_TIME"),
                                    generalFunc.retrieveLangLBl("Please make sure that pickup time is after atleast an hour from now.", "LBL_INVALID_PICKUP_NOTE_MSG"));

                            return;
                        }

                        if (Utils.isValidTimeSelectForLater(date, TimeUnit.DAYS.toMillis(30)) == false) {

                            generalFunc.showGeneralMessage(generalFunc.retrieveLangLBl("Invalid pickup time", "LBL_INVALID_PICKUP_TIME"),
                                    generalFunc.retrieveLangLBl("Please make sure that pickup time is after atleast an 1 month from now.", "LBL_INVALID_PICKUP_NOTE_MONTH_MSG"));
                            return;
                        }


                        setCabReqType(Utils.CabReqType_Later);

                        String selectedTime = Utils.convertDateToFormat("yyyy-MM-dd HH:mm:ss", date);

                        if (isDeliver(getCurrentCabGeneralType())) {
                            setDeliverySchedule();

                        } else {
                            checkSurgePrice(selectedTime, null);

                        }
                    }

                    @Override
                    public void onDateTimeCancel() {

                    }

                })

                .setInitialDate(new Date())
                .setMinDate(Calendar.getInstance().getTime())
                .setIs24HourTime(false)
                .setIndicatorColor(getResources().getColor(R.color.appThemeColor_2))
                .build()
                .show();
    }

    public void setCabTypeList(ArrayList<HashMap<String, String>> cabTypeList) {
        this.cabTypeList = cabTypeList;
    }

    public void changeCabType(String selectedCabTypeId) {
        this.selectedCabTypeId = selectedCabTypeId;

        if (loadAvailCabs != null) {
            loadAvailCabs.setCabTypeId(this.selectedCabTypeId);
            loadAvailCabs.setPickUpLocation(pickUpLocation);
            loadAvailCabs.changeCabs();
        }
    }

    public String getSelectedCabTypeId() {

        return this.selectedCabTypeId;

    }

    public boolean isFixFare = false;

    public void checkSurgePrice(final String selectedTime, final Intent data) {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "checkSurgePrice");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("UserType", Utils.userType);
        parameters.put("SelectedCarTypeID", "" + getSelectedCabTypeId());
        if (!selectedTime.trim().equals("")) {
            parameters.put("SelectedTime", selectedTime);
        }

        if (getPickUpLocation() != null) {
            parameters.put("PickUpLatitude", "" + getPickUpLocation().getLatitude());
            parameters.put("PickUpLongitude", "" + getPickUpLocation().getLongitude());
        }

        if (getDestLocLatitude() != null && !getDestLocLatitude().equalsIgnoreCase("")) {
            parameters.put("DestLatitude", "" + getDestLocLatitude());
            parameters.put("DestLongitude", "" + getDestLocLongitude());
        }

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {


                    generalFunc.sendHeartBeat();

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        if (!selectedTime.trim().equals("")) {

                            if (app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                                ridelaterView.setVisibility(View.GONE);
                                uberXDriverListArea.setVisibility(View.GONE);
                                pickUpLocClicked();
                            } else {

                                if (generalFunc.getJsonValue("eFlatTrip", responseString).equalsIgnoreCase("Yes")) {
                                    isFixFare = true;
                                    openFixChargeDialog(responseString, false, data);
                                } else {
                                    handleRequest(data);
                                }

                            }
                        } else {
                            if (generalFunc.getJsonValue("eFlatTrip", responseString).equalsIgnoreCase("Yes")) {
                                isFixFare = true;
                                openFixChargeDialog(responseString, false, data);
                            } else {
                                handleRequest(data);
                            }
                        }

                    } else {
                        if (generalFunc.getJsonValue("eFlatTrip", responseString).equalsIgnoreCase("Yes")) {
                            isFixFare = true;
                            openFixChargeDialog(responseString, true, null);

                        } else {
                            openSurgeConfirmDialog(responseString, selectedTime, data);
                        }
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }


    private void handleRequest(Intent data) {


        String driverIds = getAvailableDriverIds();

        JSONObject cabRequestedJson = new JSONObject();
        try {
            cabRequestedJson.put("Message", "CabRequested");
            cabRequestedJson.put("sourceLatitude", "" + getPickUpLocation().getLatitude());
            cabRequestedJson.put("sourceLongitude", "" + getPickUpLocation().getLongitude());
            cabRequestedJson.put("PassengerId", generalFunc.getMemberId());
            cabRequestedJson.put("PName", generalFunc.getJsonValue("vName", userProfileJson) + " "
                    + generalFunc.getJsonValue("vLastName", userProfileJson));
            cabRequestedJson.put("PPicName", generalFunc.getJsonValue("vImgName", userProfileJson));
            cabRequestedJson.put("PFId", generalFunc.getJsonValue("vFbId", userProfileJson));
            cabRequestedJson.put("PRating", generalFunc.getJsonValue("vAvgRating", userProfileJson));
            cabRequestedJson.put("PPhone", generalFunc.getJsonValue("vPhone", userProfileJson));
            cabRequestedJson.put("PPhoneC", generalFunc.getJsonValue("vPhoneCode", userProfileJson));
            cabRequestedJson.put("REQUEST_TYPE", getCurrentCabGeneralType());

            cabRequestedJson.put("selectedCatType", vUberXCategoryName);
            if (getDestinationStatus() == true) {
                cabRequestedJson.put("destLatitude", "" + getDestLocLatitude());
                cabRequestedJson.put("destLongitude", "" + getDestLocLongitude());
            } else {
                cabRequestedJson.put("destLatitude", "");
                cabRequestedJson.put("destLongitude", "");
            }

            if (deliveryData != null) {
            }
            getTollcostValue(driverIds, cabRequestedJson.toString(), data);
        } catch (JSONException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
    }

    public void openFixChargeDialog(String responseString, boolean isSurCharge, Intent data) {

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

                payableAmount = generalFunc.getJsonValue("fFlatTripPricewithsymbol", responseString) + " " + "(" + generalFunc.retrieveLangLBl("", "LBL_AT_TXT") + " " +
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

                cabSelectionFrag.ride_now_btn.setClickable(false);
                cabSelectionFrag.ride_now_btn.setEnabled(false);
                handleRequest(data);
            }
        });
        (dialogView.findViewById(R.id.tryLaterTxt)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                isFixFare = false;
                alertDialog_surgeConfirm.dismiss();
                closeRequestDialog(false);
                cabSelectionFrag.ride_now_btn.setEnabled(true);
                cabSelectionFrag.ride_now_btn.setClickable(true);
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

    public void openSurgeConfirmDialog(String responseString, final String selectedTime, Intent data) {

        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle("");
        builder.setCancelable(false);
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.surge_confirm_design, null);
        builder.setView(dialogView);

        MTextView payableAmountTxt;
        MTextView payableTxt;

        ((MTextView) dialogView.findViewById(R.id.headerMsgTxt)).setText(generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
        ((MTextView) dialogView.findViewById(R.id.surgePriceTxt)).setText(generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("SurgePrice", responseString)));

        ((MTextView) dialogView.findViewById(R.id.tryLaterTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_TRY_LATER"));

        payableTxt = (MTextView) dialogView.findViewById(R.id.payableTxt);
        payableAmountTxt = (MTextView) dialogView.findViewById(R.id.payableAmountTxt);
        payableTxt.setText(generalFunc.retrieveLangLBl("", "LBL_PAYABLE_AMOUNT"));


        if (cabSelectionFrag != null && cabTypeList != null && cabTypeList.get(cabSelectionFrag.selpos).get("total_fare") != null && !cabTypeList.get(cabSelectionFrag.selpos).get("total_fare").equals("")) {

            payableAmountTxt.setVisibility(View.VISIBLE);
            payableTxt.setVisibility(View.GONE);
            payableAmount = generalFunc.convertNumberWithRTL(cabTypeList.get(cabSelectionFrag.selpos).get("total_fare"));

            payableAmountTxt.setText(generalFunc.retrieveLangLBl("Approx payable amount", "LBL_APPROX_PAY_AMOUNT") + ": " + payableAmount);
        } else {
            payableAmountTxt.setVisibility(View.GONE);
            payableTxt.setVisibility(View.VISIBLE);

        }

        MButton btn_type2 = ((MaterialRippleLayout) dialogView.findViewById(R.id.btn_type2)).getChildView();
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_ACCEPT_SURGE"));
        btn_type2.setId(Utils.generateViewId());

        btn_type2.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                alertDialog_surgeConfirm.dismiss();
                cabSelectionFrag.ride_now_btn.setClickable(false);
                cabSelectionFrag.ride_now_btn.setEnabled(false);
                handleRequest(null);


            }
        });
        (dialogView.findViewById(R.id.tryLaterTxt)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                alertDialog_surgeConfirm.dismiss();
                closeRequestDialog(false);
                cabSelectionFrag.ride_now_btn.setEnabled(true);
                cabSelectionFrag.ride_now_btn.setClickable(true);

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

    public void pickUpLocClicked() {

        configureDeliveryView(true);
        redirectToMapOrList(Utils.Cab_UberX_Type_List, false);

        Bundle bundle = new Bundle();
        bundle.putString("latitude", getIntent().getStringExtra("latitude"));
        bundle.putString("longitude", getIntent().getStringExtra("longitude"));
        bundle.putString("address", getIntent().getStringExtra("address"));
        bundle.putString("SelectvVehicleType", getIntent().getStringExtra("SelectvVehicleType"));

        bundle.putString("type", bookingtype);
        bundle.putString("Quantity", getIntent().getStringExtra("Quantity"));

        bundle.putString("Pname", selectedprovidername);
        if (sdate.equals("")) {
            sdate = getIntent().getStringExtra("Sdate");

        }
        if (Stime.equals("")) {
            Stime = getIntent().getStringExtra("Stime");

        }
        bundle.putString("Sdate", sdate);
        bundle.putString("Stime", Stime);

        if (UfxAmount.equals("")) {
            bundle.putString("SelectvVehiclePrice", getIntent().getStringExtra("SelectvVehiclePrice"));
            bundle.putString("Quantityprice", getIntent().getStringExtra("Quantityprice"));
        } else {

            bundle.putString("SelectvVehiclePrice", UfxAmount + "");


            if (!getIntent().getStringExtra("Quantity").equals("0")) {
                UfxAmount = UfxAmount.replace(vCurrencySymbol, "");
                int qty = GeneralFunctions.parseIntegerValue(0, getIntent().getStringExtra("Quantity"));
                float amount = GeneralFunctions.parseFloatValue(0, UfxAmount);
                bundle.putString("Quantityprice", vCurrencySymbol + (qty * amount) + "");
            } else {
                bundle.putString("Quantityprice", UfxAmount + "");
            }


            UfxAmount = "";
        }

        bundle.putString("ACCEPT_CASH_TRIPS", ACCEPT_CASH_TRIPS);
        new StartActProcess(getActContext()).startActForResult(BookingSummaryActivity.class, bundle, Utils.UFX_REQUEST_CODE);
    }

    public void setDefaultView() {

        try {
            super.onPostResume();
        } catch (Exception e) {

        }


        try {


            cabRquestType = Utils.CabReqType_Now;


            if (mainHeaderFrag != null) {
                getSupportFragmentManager().beginTransaction()
                        .replace(R.id.headerContainer, mainHeaderFrag).commit();
            }


            if (!app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                if (mainHeaderFrag != null) {
                    mainHeaderFrag.releaseAddressFinder();
                }

            } else if (app_type.equalsIgnoreCase("UberX")) {


                if (reqPickUpFrag != null) {
                    getSupportFragmentManager().beginTransaction().remove(reqPickUpFrag).commit();
                }

                (findViewById(R.id.dragView)).setVisibility(View.GONE);
                setUserLocImgBtnMargin(5);
            }


            configDestinationMode(false);
            userLocBtnImgView.performClick();
            reqPickUpFrag = null;
            Utils.runGC();

            if (!app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {

                configureDeliveryView(false);
            }

            sliding_layout.setPanelState(SlidingUpPanelLayout.PanelState.COLLAPSED);

            try {
                new CreateAnimation(dragView, getActContext(), R.anim.design_bottom_sheet_slide_in, 600, true).startAnimation();
            } catch (Exception e) {

            }


            if (loadAvailCabs != null) {
                loadAvailCabs.setTaskKilledValue(false);
                loadAvailCabs.onResumeCalled();
            }
        } catch (Exception e) {

        }


    }

    public void setPanelHeight(int value) {

        sliding_layout.setPanelHeight(Utils.dipToPixels(getActContext(), value));

    }

    public Location getPickUpLocation() {
        return this.pickUpLocation;
    }

    public String getPickUpLocationAddress() {
        return this.pickUpLocationAddress;
    }

    public void notifyCarSearching() {
        setETA("\n" + "--");

        if (reqPickUpFrag != null) {
            if (reqPickUpFrag.requestPickUpBtn != null) {
                if (!isUfxRideLater) {
                    reqPickUpFrag.requestPickUpBtn.setEnabled(false);
                    reqPickUpFrag.requestPickUpBtn.setTextColor(Color.parseColor("#BABABA"));
                }
            }
        }

        if (getCurrentCabGeneralType().equals(Utils.CabGeneralType_UberX)) {
            if (currentUberXChoiceType.equalsIgnoreCase(Utils.Cab_UberX_Type_List)) {

                (findViewById(R.id.driverListAreaLoader)).setVisibility(View.VISIBLE);
                (findViewById(R.id.searchingDriverTxt)).setVisibility(View.VISIBLE);
                ((MTextView) findViewById(R.id.searchingDriverTxt)).setText(generalFunc.retrieveLangLBl("Searching Provider", "LBL_SEARCH_CAR_WAIT_TXT"));
                uberXNoDriverTxt.setVisibility(View.GONE);
                ridelaterView.setVisibility(View.GONE);

                uberXDriverList.clear();
            }
        }
    }

    public void notifyNoCabs() {
        Utils.printLog("notifyNoCabs()", "::called");

        if (isufxbackview) {
            return;
        }

        setETA("\n" + "--");
        setCurrentLoadedDriverList(new ArrayList<HashMap<String, String>>());

        if (cabSelectionFrag != null) {
            noCabAvail = false;
            changeLable();
        }


        if (reqPickUpFrag != null) {
            if (!isUfxRideLater) {
                noCabAvail = false;
                reqPickUpFrag.requestPickUpBtn.setEnabled(false);
                reqPickUpFrag.requestPickUpBtn.setTextColor(Color.parseColor("#BABABA"));
            }
        }

        changeLable();

    }


    public void notifyCabsAvailable() {
        if (cabSelectionFrag != null && loadAvailCabs != null && loadAvailCabs.listOfDrivers != null && loadAvailCabs.listOfDrivers.size() > 0) {
            if (cabSelectionFrag.isroutefound) {
                if (loadAvailCabs.isAvailableCab) {
                    if (!timeval.equalsIgnoreCase("\n" + "--")) {
                        noCabAvail = true;
                    }
                }
            }
        }
        if (reqPickUpFrag != null) {
            if (loadAvailCabs != null && loadAvailCabs.listOfDrivers != null) {

                if (loadAvailCabs.listOfDrivers.size() > 0) {
                    if (reqPickUpFrag.requestPickUpBtn != null) {
                        reqPickUpFrag.requestPickUpBtn.setEnabled(true);
                        reqPickUpFrag.requestPickUpBtn.setTextColor(getResources().getColor(R.color.btn_text_color_type2));
                    }
                }
            }

        }

        if (cabSelectionFrag != null) {
            cabSelectionFrag.setLabels(false);
        }


    }

    public void onMapCameraChanged() {
        if (cabSelectionFrag != null) {

            if (loadAvailCabs != null) {
                loadAvailCabs.filterDrivers(true);
            }

            if (mainHeaderFrag != null) {
                //notifyCarSearching();
                cabSelectionFrag.img_ridelater.setEnabled(false);

                if (isDestinationMode == true) {
                    mainHeaderFrag.setDestinationAddress(generalFunc.retrieveLangLBl("", "LBL_SELECTING_LOCATION_TXT"));

                } else {
                    mainHeaderFrag.setPickUpAddress(generalFunc.retrieveLangLBl("", "LBL_SELECTING_LOCATION_TXT"));

                }

            }

        }
    }

    public void onAddressFound(String address) {
        if (cabSelectionFrag != null) {
            notifyCabsAvailable();
            cabSelectionFrag.img_ridelater.setEnabled(true);
            if (mainHeaderFrag != null) {

                if (isDestinationMode == true) {

                    mainHeaderFrag.setDestinationAddress(address);
                } else {

                    mainHeaderFrag.setPickUpAddress(address);
                }
            }

        } else {
            if (isUserLocbtnclik) {
                isUserLocbtnclik = false;
                if (address != null && Utils.checkText(address)) {
                    isFrompickupaddress = true;
                }
                mainHeaderFrag.setPickUpAddress(address);

            }
        }


    }

    public void setDestinationPoint(String destLocLatitude, String destLocLongitude, String destAddress, boolean isDestinationAdded) {

        if (destLocation == null) {
            destLocation = new Location("dest");
        }
        destLocation.setLatitude(GeneralFunctions.parseDoubleValue(0.0, destLocLatitude));
        destLocation.setLongitude(GeneralFunctions.parseDoubleValue(0.0, destLocLongitude));


        this.isDestinationAdded = isDestinationAdded;
        this.destLocLatitude = destLocLatitude;
        this.destLocLongitude = destLocLongitude;
        this.destAddress = destAddress;
    }

    public boolean getDestinationStatus() {
        return this.isDestinationAdded;
    }

    public String getDestLocLatitude() {
        return this.destLocLatitude;
    }

    public String getDestLocLongitude() {
        return this.destLocLongitude;
    }

    public String getDestAddress() {
        return this.destAddress;
    }

    public void setCashSelection(boolean isCashSelected) {
        this.isCashSelected = isCashSelected;
        if (loadAvailCabs != null) {
            loadAvailCabs.changeCabs();
        }
    }

    public String getCabReqType() {
        return this.cabRquestType;
    }

    public void setCabReqType(String cabRquestType) {
        this.cabRquestType = cabRquestType;
    }

    public Bundle getFareEstimateBundle() {
        Bundle bn = new Bundle();
        bn.putString("PickUpLatitude", "" + getPickUpLocation().getLatitude());
        bn.putString("PickUpLongitude", "" + getPickUpLocation().getLongitude());
        bn.putString("isDestinationAdded", "" + getDestinationStatus());
        bn.putString("DestLocLatitude", "" + getDestLocLatitude());
        bn.putString("DestLocLongitude", "" + getDestLocLongitude());
        bn.putString("DestLocAddress", "" + getDestAddress());
        bn.putString("SelectedCarId", "" + getSelectedCabTypeId());
        bn.putString("SelectedCabType", "" + generalFunc.getSelectedCarTypeData(getSelectedCabTypeId(), cabTypesArrList, "vVehicleType"));
        return bn;
    }

    public void continueDeliveryProcess() {
        String pickUpLocAdd = mainHeaderFrag != null ? (mainHeaderFrag.getPickUpAddress().equals(
                generalFunc.retrieveLangLBl("", "LBL_SELECTING_LOCATION_TXT")) ? "" : mainHeaderFrag.getPickUpAddress()) : "";

        if (!pickUpLocAdd.equals("")) {

            if (isDeliver(getCurrentCabGeneralType())) {

                setDeliverySchedule();


            } else {
                checkSurgePrice("", null);

            }
        }
    }

    public void setRideSchedule() {
        isrideschedule = true;


        if (getDestinationStatus() == false && generalFunc.retrieveValue(CommonUtilities.APP_DESTINATION_MODE).equalsIgnoreCase(CommonUtilities.STRICT_DESTINATION)) {
            generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_ADD_DEST_MSG_BOOK_RIDE"));
        }
    }

    public void setDeliverySchedule() {

        if (getDestinationStatus() == false) {
            generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Please add your destination location " +
                    "to deliver your package.", "LBL_ADD_DEST_MSG_DELIVER_ITEM"));
        } else {

            Bundle bn = new Bundle();
            bn.putString("isDeliverNow", "" + getCabReqType().equals(Utils.CabReqType_Now));
        }
    }

    public void bookRide() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "ScheduleARide");

        if (mainHeaderFrag != null) {
            if (!app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                if (isUfx) {
                    parameters.put("pickUpLocAdd", pickUpLocationAddress);
                } else {
                    parameters.put("pickUpLocAdd", mainHeaderFrag != null ? mainHeaderFrag.getPickUpAddress() : "");
                }

            } else {
                parameters.put("pickUpLocAdd", pickUpLocationAddress);
            }
        }
        parameters.put("iUserId", generalFunc.getMemberId());
        if (isUfx) {
            parameters.put("pickUpLatitude", getIntent().getStringExtra("latitude"));
            parameters.put("pickUpLongitude", getIntent().getStringExtra("longitude"));
        } else {
            parameters.put("pickUpLatitude", "" + getPickUpLocation().getLatitude());
            parameters.put("pickUpLongitude", "" + getPickUpLocation().getLongitude());
        }
        parameters.put("destLocAdd", getDestAddress());
        parameters.put("destLatitude", getDestLocLatitude());
        parameters.put("destLongitude", getDestLocLongitude());
        parameters.put("iCabBookingId", iCabBookingId);
        parameters.put("scheduleDate", selectedDateTime);
        parameters.put("iVehicleTypeId", getSelectedCabTypeId());
        parameters.put("SelectedDriverId", SelectedDriverId);
        parameters.put("CashPayment", "" + isCashSelected);

        String handicapval = "";
        String femaleval = "";
        if (ishandicap) {
            handicapval = "Yes";

        } else {
            handicapval = "No";
        }
        if (isfemale) {
            femaleval = "Yes";

        } else {
            femaleval = "No";
        }

        parameters.put("HandicapPrefEnabled", handicapval);
        parameters.put("PreferFemaleDriverEnable", femaleval);
        parameters.put("vTollPriceCurrencyCode", tollcurrancy);
        String tollskiptxt = "";

        if (istollIgnore) {
            tollamount = 0;
            tollskiptxt = "Yes";

        } else {
            tollskiptxt = "No";
        }
        parameters.put("fTollPrice", tollamount + "");
        parameters.put("eTollSkipped", tollskiptxt);


        parameters.put("eType", getCurrentCabGeneralType());
        if (reqPickUpFrag != null) {
            parameters.put("PromoCode", reqPickUpFrag.getAppliedPromoCode());
        }

        if (cabSelectionFrag != null) {
            parameters.put("PromoCode", cabSelectionFrag.getAppliedPromoCode());
        }
        if (app_type.equalsIgnoreCase("UberX")) {
            parameters.put("PromoCode", appliedPromoCode);
            if (getIntent().getStringExtra("Quantity").equals("0")) {
                parameters.put("Quantity", "1");
            } else {
                parameters.put("Quantity", getIntent().getStringExtra("Quantity"));
            }

            parameters.put("iUserAddressId", getIntent().getStringExtra("iUserAddressId"));
            parameters.put("tUserComment", userComment);
            parameters.put("scheduleDate", SelectDate);
        } else {
            parameters.put("scheduleDate", selectedDateTime);
        }
        if (app_type.equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
            if (isUfx) {
                if (getIntent().getStringExtra("Quantity").equals("0")) {
                    parameters.put("Quantity", "1");
                } else {
                    parameters.put("Quantity", getIntent().getStringExtra("Quantity"));
                }
            }

        }

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {


                    if (generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_EMAIL_PHONE_VERIFY") ||
                            generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_PHONE_VERIFY") ||
                            generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_EMAIL_VERIFY")) {
                        Bundle bn = new Bundle();
                        bn.putString("msg", "" + generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        accountVerificationAlert(generalFunc.retrieveLangLBl("", "LBL_ACCOUNT_VERIFY_ALERT_RIDER_TXT"), bn);

                        return;
                    }

                    String action = generalFunc.getJsonValue(CommonUtilities.action_str, responseString);

                    if (action.equals("1")) {
                        setDestinationPoint("", "", "", false);
                        setDefaultView();
                        isrideschedule = false;

                        if (isRebooking) {


                            showBookingAlert();
                        } else {
                            showBookingAlert(generalFunc.retrieveLangLBl("",
                                    generalFunc.getJsonValue(CommonUtilities.message_str, responseString)), false);
                        }
                    } else {
                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("",
                                generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }

                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void chatMsg() {
        Bundle bn = new Bundle();
        bn.putString("iFromMemberId", driverDetailFrag.getTripData().get("iDriverId"));
        bn.putString("FromMemberImageName", driverDetailFrag.getTripData().get("DriverImage"));
        bn.putString("iTripId", driverDetailFrag.getTripData().get("iTripId"));
        bn.putString("FromMemberName", driverDetailFrag.getTripData().get("DriverName"));


        new StartActProcess(getActContext()).startActWithData(ChatActivity.class, bn);
    }


    public void showBookingAlert() {
        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());


        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();
                Bundle bn = new Bundle();
                bn.putBoolean("isrestart", true);
                new StartActProcess(getActContext()).startActWithData(HistoryActivity.class, bn);

                finish();
            }
        });
        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("Your selected booking has been updated.", "LBL_BOOKING_UPDATED"));
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

        generateAlert.showAlertBox();
    }

    public void showBookingAlert(String message, boolean isongoing) {
        android.support.v7.app.AlertDialog alertDialog;
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle("");
        builder.setCancelable(false);
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.dialog_booking_view, null);
        builder.setView(dialogView);

        final MTextView titleTxt = (MTextView) dialogView.findViewById(R.id.titleTxt);
        final MTextView mesasgeTxt = (MTextView) dialogView.findViewById(R.id.mesasgeTxt);


        titleTxt.setText(generalFunc.retrieveLangLBl("Booking Successful", "LBL_BOOKING_ACCEPTED"));


        mesasgeTxt.setText(message);


        builder.setNegativeButton(generalFunc.retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
                Bundle bn = new Bundle();
                if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equals(Utils.CabGeneralType_UberX)) {
                } else {
                    new StartActProcess(getActContext()).startActWithData(MainActivity.class, bn);
                }
                finishAffinity();
            }
        });

        if (isongoing) {

            builder.setPositiveButton(generalFunc.retrieveLangLBl("", "LBL_VIEW_ON_GOING_TRIPS"), new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                }
            });

        } else {

            builder.setPositiveButton(generalFunc.retrieveLangLBl("Done", "LBL_VIEW_BOOKINGS"), new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    dialog.cancel();
                    Bundle bn = new Bundle();
                    bn.putBoolean("isrestart", true);
                    new StartActProcess(getActContext()).startActWithData(HistoryActivity.class, bn);
                    finish();
                }
            });
        }


        alertDialog = builder.create();
        alertDialog.setCancelable(false);
        alertDialog.setCanceledOnTouchOutside(false);
        alertDialog.show();

    }

    public void scheduleDelivery(Intent data) {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "ScheduleARide");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("pickUpLocAdd", mainHeaderFrag != null ? mainHeaderFrag.getPickUpAddress() : "");
        parameters.put("pickUpLatitude", "" + getPickUpLocation().getLatitude());
        parameters.put("pickUpLongitude", "" + getPickUpLocation().getLongitude());
        parameters.put("destLocAdd", getDestAddress());
        parameters.put("destLatitude", getDestLocLatitude());
        parameters.put("destLongitude", getDestLocLongitude());
        parameters.put("scheduleDate", selectedDateTime);
        parameters.put("iVehicleTypeId", getSelectedCabTypeId());
        parameters.put("CashPayment", "" + isCashSelected);
        parameters.put("eType", "Deliver");

        String tollskiptxt = "";

        if (istollIgnore) {
            tollskiptxt = "Yes";
            tollamount = 0;
        } else {
            tollskiptxt = "No";
        }
        parameters.put("fTollPrice", tollamount + "");
        parameters.put("vTollPriceCurrencyCode", tollcurrancy);
        parameters.put("eTollSkipped", tollskiptxt);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    if (generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_EMAIL_PHONE_VERIFY") ||
                            generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_PHONE_VERIFY") ||
                            generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_EMAIL_VERIFY")) {
                        Bundle bn = new Bundle();
                        bn.putString("msg", "" + generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        //bn.putString("UserProfileJson", userProfileJson);
                        accountVerificationAlert(generalFunc.retrieveLangLBl("", "LBL_ACCOUNT_VERIFY_ALERT_RIDER_TXT"), bn);

                        return;
                    }

                    String action = generalFunc.getJsonValue(CommonUtilities.action_str, responseString);

                    if (action.equals("1")) {
                        setDestinationPoint("", "", "", false);
                        setDefaultView();


                        if (isRebooking) {
                            showBookingAlert();
                        } else {
                            showBookingAlert(generalFunc.retrieveLangLBl("",
                                    generalFunc.getJsonValue(CommonUtilities.message_str, responseString)), false);
                        }
                    } else {
                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("",
                                generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();

    }

    public void deliverNow(Intent data) {

        this.deliveryData = data;

        requestPickUp();
    }

    public void requestPickUp() {
        Utils.printLog("requestPickUp", "::called");


        setLoadAvailCabTaskValue(true);
        requestNearestCab = new RequestNearestCab(getActContext(), generalFunc);
        requestNearestCab.run();

        String driverIds = getAvailableDriverIds();

        JSONObject cabRequestedJson = new JSONObject();
        try {
            cabRequestedJson.put("Message", "CabRequested");
            cabRequestedJson.put("sourceLatitude", "" + getPickUpLocation().getLatitude());
            cabRequestedJson.put("sourceLongitude", "" + getPickUpLocation().getLongitude());
            cabRequestedJson.put("PassengerId", generalFunc.getMemberId());
            cabRequestedJson.put("PName", generalFunc.getJsonValue("vName", userProfileJson) + " "
                    + generalFunc.getJsonValue("vLastName", userProfileJson));
            cabRequestedJson.put("PPicName", generalFunc.getJsonValue("vImgName", userProfileJson));
            cabRequestedJson.put("PFId", generalFunc.getJsonValue("vFbId", userProfileJson));
            cabRequestedJson.put("PRating", generalFunc.getJsonValue("vAvgRating", userProfileJson));
            cabRequestedJson.put("PPhone", generalFunc.getJsonValue("vPhone", userProfileJson));
            cabRequestedJson.put("PPhoneC", generalFunc.getJsonValue("vPhoneCode", userProfileJson));
            cabRequestedJson.put("REQUEST_TYPE", getCurrentCabGeneralType());

            cabRequestedJson.put("selectedCatType", vUberXCategoryName);
            if (getDestinationStatus() == true) {
                cabRequestedJson.put("destLatitude", "" + getDestLocLatitude());
                cabRequestedJson.put("destLongitude", "" + getDestLocLongitude());
            } else {
                cabRequestedJson.put("destLatitude", "");
                cabRequestedJson.put("destLongitude", "");
            }

            if (deliveryData != null) {
            }

        } catch (JSONException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }

        if (!generalFunc.getJsonValue("Message", cabRequestedJson.toString()).equals("")) {
            requestNearestCab.setRequestData(driverIds, cabRequestedJson.toString());

            if (DRIVER_REQUEST_METHOD.equals("All")) {
                sendReqToAll(driverIds, cabRequestedJson.toString());
            } else if (DRIVER_REQUEST_METHOD.equals("Distance") || DRIVER_REQUEST_METHOD.equals("Time")) {
                Utils.printLog("sendReqByDist", "called");
                sendReqByDist(driverIds, cabRequestedJson.toString());
            } else {
                sendReqToAll(driverIds, cabRequestedJson.toString());
            }
        }


    }

    public void sendReqToAll(String driverIds, String cabRequestedJson) {
        isreqnow = true;
        sendRequestToDrivers(driverIds, cabRequestedJson);
        if (allCabRequestTask != null) {
            allCabRequestTask.stopRepeatingTask();
            allCabRequestTask = null;
        }

        int interval = generalFunc.parseIntegerValue(30, generalFunc.getJsonValue("RIDER_REQUEST_ACCEPT_TIME", generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON)));


        Utils.printLog("Api", "interval get" + interval);
        allCabRequestTask = new UpdateFrequentTask((interval + 5) * 1000);
        allCabRequestTask.startRepeatingTask();
        allCabRequestTask.setTaskRunListener(new UpdateFrequentTask.OnTaskRunCalled() {
            @Override
            public void onTaskRun() {
                setRetryReqBtn(true);
                allCabRequestTask.stopRepeatingTask();
            }
        });

    }

    public void sendReqByDist(String driverIds, String cabRequestedJson) {
        if (sendNotificationToDriverByDist == null) {
            sendNotificationToDriverByDist = new SendNotificationsToDriverByDist(driverIds, cabRequestedJson);
        } else {
            sendNotificationToDriverByDist.startRepeatingTask();
        }
    }

    public void setRetryReqBtn(boolean isVisible) {
        if (isVisible == true) {
            if (requestNearestCab != null) {
                requestNearestCab.setVisibilityOfRetryArea(View.VISIBLE);
            }
        } else {
            if (requestNearestCab != null) {
                requestNearestCab.setVisibilityOfRetryArea(View.GONE);
            }
        }
    }

    public void retryReqBtnPressed(String driverIds, String cabRequestedJson) {

        if (DRIVER_REQUEST_METHOD.equals("All")) {
            sendReqToAll(driverIds, cabRequestedJson.toString());
        } else if (DRIVER_REQUEST_METHOD.equals("Distance") || DRIVER_REQUEST_METHOD.equals("Time")) {
            sendReqByDist(driverIds, cabRequestedJson.toString());
        } else {
            sendReqToAll(driverIds, cabRequestedJson.toString());
        }

        setRetryReqBtn(false);
    }

    public void setLoadAvailCabTaskValue(boolean value) {
        if (loadAvailCabs != null) {
            loadAvailCabs.setTaskKilledValue(value);
        }
    }

    public void setCurrentLoadedDriverList(ArrayList<HashMap<String, String>> currentLoadedDriverList) {
        this.currentLoadedDriverList = currentLoadedDriverList;

        if (app_type.equalsIgnoreCase("UberX")) {
            // load list here but wait for 5 seconds
            redirectToMapOrList(Utils.Cab_UberX_Type_List, true);

        }
    }

    public ArrayList<String> getDriverLocationChannelList() {

        ArrayList<String> channels_update_loc = new ArrayList<>();

        if (currentLoadedDriverList != null) {

            for (int i = 0; i < currentLoadedDriverList.size(); i++) {
                channels_update_loc.add(Utils.pubNub_Update_Loc_Channel_Prefix + "" + (currentLoadedDriverList.get(i).get("driver_id")));
            }

        }
        return channels_update_loc;
    }

    public ArrayList<String> getDriverLocationChannelList(ArrayList<HashMap<String, String>> listData) {

        ArrayList<String> channels_update_loc = new ArrayList<>();

        if (listData != null) {

            for (int i = 0; i < listData.size(); i++) {
                channels_update_loc.add(Utils.pubNub_Update_Loc_Channel_Prefix + "" + (listData.get(i).get("driver_id")));
            }

        }
        return channels_update_loc;
    }

    public String getAvailableDriverIds() {
        String driverIds = "";

        if (currentLoadedDriverList == null) {
            return driverIds;
        }

        ArrayList<HashMap<String, String>> finalLoadedDriverList = new ArrayList<HashMap<String, String>>();
        finalLoadedDriverList.addAll(currentLoadedDriverList);

        if (DRIVER_REQUEST_METHOD.equals("Distance")) {
            Collections.sort(finalLoadedDriverList, new HashMapComparator("DIST_TO_PICKUP"));
        }

        for (int i = 0; i < finalLoadedDriverList.size(); i++) {
            String iDriverId = finalLoadedDriverList.get(i).get("driver_id");

            driverIds = driverIds.equals("") ? iDriverId : (driverIds + "," + iDriverId);
        }

        return driverIds;
    }

    public void sendRequestToDrivers(String driverIds, String cabRequestedJson) {

        HashMap<String, String> requestCabData = new HashMap<String, String>();
        requestCabData.put("type", "sendRequestToDrivers");
        requestCabData.put("message", cabRequestedJson);
        requestCabData.put("userId", generalFunc.getMemberId());
        requestCabData.put("CashPayment", "" + isCashSelected);

        requestCabData.put("PickUpAddress", getPickUpLocationAddress());


        requestCabData.put("vTollPriceCurrencyCode", tollcurrancy);
        String tollskiptxt = "";

        if (istollIgnore) {
            tollamount = 0;
            tollskiptxt = "Yes";

        } else {
            tollskiptxt = "No";
        }
        requestCabData.put("fTollPrice", tollamount + "");
        requestCabData.put("eTollSkipped", tollskiptxt);

        if ((app_type.equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX))) {
            if (isUfx) {
                requestCabData.put("driverIds", generalFunc.retrieveValue(CommonUtilities.SELECTEDRIVERID));

            } else {
                requestCabData.put("driverIds", driverIds);
            }

        }
        if ((app_type.equalsIgnoreCase("UberX"))) {

            requestCabData.put("driverIds", generalFunc.retrieveValue(CommonUtilities.SELECTEDRIVERID));
        } else {

            requestCabData.put("driverIds", driverIds);

        }
        requestCabData.put("SelectedCarTypeID", "" + selectedCabTypeId);

        requestCabData.put("DestLatitude", getDestLocLatitude());
        requestCabData.put("DestLongitude", getDestLocLongitude());
        requestCabData.put("DestAddress", getDestAddress());

        if (isUfx) {
            requestCabData.put("PickUpLatitude", getIntent().getStringExtra("latitude"));
            requestCabData.put("PickUpLongitude", getIntent().getStringExtra("longitude"));
        } else {
            requestCabData.put("PickUpLatitude", "" + getPickUpLocation().getLatitude());
            requestCabData.put("PickUpLongitude", "" + getPickUpLocation().getLongitude());
        }

        requestCabData.put("eType", getCurrentCabGeneralType());


        if (deliveryData != null) {
        }

        if ((app_type.equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX))) {
            if (isUfx) {
                requestCabData.put("Quantity", getIntent().getStringExtra("Quantity"));

            }


        }
        if (app_type.equalsIgnoreCase("UberX")) {
            requestCabData.put("PromoCode", appliedPromoCode);
            requestCabData.put("iUserAddressId", getIntent().getStringExtra("iUserAddressId"));
            requestCabData.put("tUserComment", userComment);

            if (getIntent().getStringExtra("Quantity").equals("0")) {
                requestCabData.put("Quantity", "1");
            } else {
                requestCabData.put("Quantity", getIntent().getStringExtra("Quantity"));
            }
        }

        if (cabSelectionFrag != null) {
            requestCabData.put("PromoCode", cabSelectionFrag.getAppliedPromoCode());
        }

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), requestCabData);
        exeWebServer.setIsDeviceTokenGenerate(true, "vDeviceToken", generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (cabSelectionFrag != null) {
                    cabSelectionFrag.isclickableridebtn = false;
                }

                if (responseString != null && !responseString.equals("")) {

                    generalFunc.sendHeartBeat();

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == false) {


                        Bundle bn = new Bundle();
                        bn.putString("msg", "" + generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        // bn.putString("UserProfileJson", userProfileJson);

                        String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);

                        if (message.equals("SESSION_OUT")) {
                            closeRequestDialog(false);
                            generalFunc.notifySessionTimeOut();
                            Utils.runGC();
                            return;
                        }

                        if (message.equals("NO_CARS") && !DRIVER_REQUEST_METHOD.equalsIgnoreCase("ALL") && sendNotificationToDriverByDist != null) {
                            sendNotificationToDriverByDist.incTask();
                            return;

                        }
                        if (message.equals("NO_CARS") || message.equals("LBL_PICK_DROP_LOCATION_NOT_ALLOW")
                                || message.equals("LBL_DROP_LOCATION_NOT_ALLOW") || message.equals("LBL_PICKUP_LOCATION_NOT_ALLOW")) {
                            closeRequestDialog(false);
                            buildMessage(generalFunc.retrieveLangLBl("", message.equals("NO_CARS") ? "LBL_NO_CAR_AVAIL_TXT" : message),
                                    generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"), false);

                        } else if (message.equals(CommonUtilities.GCM_FAILED_KEY) || message.equals(CommonUtilities.APNS_FAILED_KEY)) {
                            releaseScheduleNotificationTask();
                            generalFunc.restartApp();
                        } else if (generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_EMAIL_PHONE_VERIFY") ||
                                generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_PHONE_VERIFY") ||
                                generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("DO_EMAIL_VERIFY")) {
                            closeRequestDialog(true);
                            accountVerificationAlert(generalFunc.retrieveLangLBl("", "LBL_ACCOUNT_VERIFY_ALERT_RIDER_TXT"), bn);

                        } else {
                            closeRequestDialog(false);
                            buildMessage(generalFunc.retrieveLangLBl("", "LBL_REQUEST_FAILED_PROCESS"), generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"), true);
                        }

                    }
                } else {
                    closeRequestDialog(true);
                    buildMessage(generalFunc.retrieveLangLBl("", "LBL_REQUEST_FAILED_PROCESS"), generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"), false);
                }
            }
        });
        exeWebServer.execute();

        generalFunc.sendHeartBeat();
    }

    public void accountVerificationAlert(String message, final Bundle bn) {
        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                if (btn_id == 1) {
                    generateAlert.closeAlertBox();
                    (new StartActProcess(getActContext())).startActForResult(VerifyInfoActivity.class, bn, Utils.VERIFY_INFO_REQ_CODE);
                } else if (btn_id == 0) {
                    generateAlert.closeAlertBox();
                }
            }
        });
        generateAlert.setContentMessage("", message);
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_CANCEL_TRIP_TXT"));
        generateAlert.showAlertBox();

    }

    public void closeRequestDialog(boolean isSetDefault) {
        if (requestNearestCab != null) {
            requestNearestCab.dismissDialog();
        }

        releaseScheduleNotificationTask();

        if (isSetDefault == true) {
            setDefaultView();
        }

    }

    public void releaseScheduleNotificationTask() {
        if (allCabRequestTask != null) {
            Utils.printLog("Api", "Object Destroyed >> releaseScheduleNotificationTask>> allCabRequestTask");
            allCabRequestTask.stopRepeatingTask();
            allCabRequestTask = null;
        }

        if (sendNotificationToDriverByDist != null) {
            Utils.printLog("Api", "Object Destroyed >> releaseScheduleNotificationTask>> sendNotificationToDriverByDist");
            sendNotificationToDriverByDist.stopRepeatingTask();
            sendNotificationToDriverByDist = null;
        }
    }

    public DriverDetailFragment getDriverDetailFragment() {
        return driverDetailFrag;
    }

    public void buildMessage(String message, String positiveBtn, final boolean isRestart) {
        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();
                if (isRestart == true) {
                    generalFunc.restartApp();
                } else if (!TextUtils.isEmpty(tripId) && eTripType.equals(CabGeneralType_Deliver)) {

                    generalFunc.autoLogin(MainActivity.this, tripId);
                }
            }
        });
        generateAlert.setContentMessage("", message);
        generateAlert.setPositiveBtn(positiveBtn);
        generateAlert.showAlertBox();
    }


    public void onGcmMessageArrived(String message) {

        String driverMsg = generalFunc.getJsonValue("Message", message);
        currentTripId = generalFunc.getJsonValue("iTripId", message);

        if (driverMsg.equals("CabRequestAccepted")) {
            if (isDriverAssigned == true) {
                return;
            }
            isDriverAssigned = true;
            addDrawer.setIsDriverAssigned(isDriverAssigned);
            userLocBtnImgView.setVisibility(View.VISIBLE);

            RelativeLayout.LayoutParams params = (RelativeLayout.LayoutParams) (userLocBtnImgView).getLayoutParams();
            params.bottomMargin = Utils.dipToPixels(getActContext(), 200);
            assignedDriverId = generalFunc.getJsonValue("iDriverId", message);
            assignedTripId = generalFunc.getJsonValue("iTripId", message);

            generalFunc.removeValue(CommonUtilities.DELIVERY_DETAILS_KEY);

            if (generalFunc.isJSONkeyAvail("iCabBookingId", message) == true && !generalFunc.getJsonValue("iCabBookingId", message).trim().equals("")) {
                // generalFunc.restartApp();
                Utils.printLog("cabrequest", "Restart_app");
                generalFunc.restartwithGetDataApp();
            } else {
                if ((!TextUtils.isEmpty(tripId) && (isDeliver(getCurrentCabGeneralType())) ||
                        getCurrentCabGeneralType().equalsIgnoreCase(Utils.CabGeneralType_Ride)
                        || isDeliver(getCurrentCabGeneralType()))) {
                    configureAssignedDriver(false);
                    pinImgView.setVisibility(View.GONE);
                    closeRequestDialog(false);
                    configureDeliveryView(true);
                } else {
                    pinImgView.setVisibility(View.GONE);
                    setDestinationPoint("", "", "", false);
                    closeRequestDialog(true);
                    //showOngoingTripViewDialoge();

                    showBookingAlert(generalFunc.retrieveLangLBl("", "LBL_ONGOING_TRIP_TXT"), true);
                }
            }
            tripStatus = "Assigned";

            Handler handler = new Handler();
            handler.postDelayed(new Runnable() {
                @Override
                public void run() {
                    userLocBtnImgView.performClick();

                }
            }, 1500);

        } else if (driverMsg.equals("TripEnd")) {
            if (isDriverAssigned == false) {
                return;
            }

            if (isTripEnded == true && isDriverAssigned == false) {
                generalFunc.restartApp();
                return;
            }

            if (isTripEnded) {
                return;
            }

            tripStatus = "TripEnd";
            if (driverAssignedHeaderFrag != null) {


                if ((!TextUtils.isEmpty(tripId) && (getCurrentCabGeneralType().equalsIgnoreCase(CabGeneralType_Deliver)))) {

                    isTripEnded = true;


                } else {


                    isTripEnded = true;

                }

                if (driverAssignedHeaderFrag != null) {
                    driverAssignedHeaderFrag.setTaskKilledValue(true);
                }


            }

        } else if (driverMsg.equals("TripStarted")) {
            try {


                if (isDriverAssigned == false) {
                    return;
                }

                if (isDriverAssigned == false && isTripStarted == true) {
                    generalFunc.restartApp();
                    return;
                }

                if (isTripStarted) {
                    return;
                }


                tripStatus = "TripStarted";


                isTripStarted = true;

                Utils.printLog("driverAssignedHeaderFrag", "::" + "not called");
                if (driverAssignedHeaderFrag != null) {
                    Utils.printLog("driverAssignedHeaderFrag", "::" + "called");
                    driverAssignedHeaderFrag.setTripStartValue(true);
                }

                if (driverAssignedHeaderFrag.sourceMarker != null) {
                    driverAssignedHeaderFrag.sourceMarker.remove();
                }

                if (driverDetailFrag != null) {
                    driverDetailFrag.configTripStartView(generalFunc.getJsonValue("VerificationCode", message));
                }

                userLocBtnImgView.performClick();
            } catch (Exception e) {

            }


        } else if (driverMsg.equals("DestinationAdded")) {
            if (isDriverAssigned == false) {
                return;
            }

            generateNotification(getActContext(), generalFunc.retrieveLangLBl("Destination is added by driver.", "LBL_DEST_ADD_BY_DRIVER"));
            buildMessage(generalFunc.retrieveLangLBl("Destination is added by driver.", "LBL_DEST_ADD_BY_DRIVER"), generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"), false);

            String destLatitude = generalFunc.getJsonValue("DLatitude", message);
            String destLongitude = generalFunc.getJsonValue("DLongitude", message);
            String destAddress = generalFunc.getJsonValue("DAddress", message);
            String eFlatTrip = generalFunc.getJsonValue("eFlatTrip", message);

            setDestinationPoint(destLatitude, destLongitude, destAddress, true);
            if (driverAssignedHeaderFrag != null) {
                driverAssignedHeaderFrag.setDestinationAddress(eFlatTrip);
                driverAssignedHeaderFrag.configDestinationView();
            }
        } else if (driverMsg.equals("TripCancelledByDriver")) {


            if (MyApp.getCurrentAct() instanceof ChatActivity) {
                ChatActivity chatActobj = (ChatActivity) MyApp.getCurrentAct();
                chatActobj.onGcmMessageArrived(generalFunc.getJsonValue("Reason", message));
            }

            if (isDriverAssigned == false) {
                generalFunc.restartApp();
                return;
            }

            if (tripStatus.equals("TripCanelled")) {
                return;
            }

            tripStatus = "TripCanelled";
            if (driverAssignedHeaderFrag != null) {


                if (driverAssignedHeaderFrag != null) {
                    driverAssignedHeaderFrag.setTaskKilledValue(true);
                }
            }
        }
    }

    public DriverAssignedHeaderFragment getDriverAssignedHeaderFrag() {
        return driverAssignedHeaderFrag;
    }

    public void unSubscribeCurrentDriverChannels() {
        if (currentLoadedDriverList != null) {
            ConfigPubNub.getInstance().unSubscribeToChannels(getDriverLocationChannelList());
        }
    }

    public boolean isDeliver(String selctedType) {
        return (selctedType.equalsIgnoreCase(CabGeneralType_Deliver) || selctedType.equalsIgnoreCase("Deliver"));
    }

    @Override
    protected void onPause() {
        super.onPause();

        if (loadAvailCabs != null) {
            loadAvailCabs.onPauseCalled();
        }

        if (driverAssignedHeaderFrag != null) {
            driverAssignedHeaderFrag.onPauseCalled();
        }

        unSubscribeCurrentDriverChannels();


    }

    @Override
    protected void onResume() {
        super.onResume();


        resetView("resume");


        if (generalFunc.retrieveValue(CommonUtilities.ISWALLETBALNCECHANGE).equalsIgnoreCase("Yes")) {
            getWalletBalDetails();
        }

        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        obj_userProfile = generalFunc.getJsonObject(userProfileJson);

        ConfigPubNub.getInstance().unRegisterGcmReceiver();
        ConfigPubNub.getInstance().ConfigPubNub(getActContext());
        ConfigPubNub.getInstance().setTripId("", "");

        setUserInfo();


        if (addDrawer != null) {
            addDrawer.userProfileJson = userProfileJson;
        }


        if (iswallet) {
            obj_userProfile = generalFunc.getJsonObject(userProfileJson);
            if (addDrawer != null) {
                addDrawer.changeUserProfileJson(userProfileJson);
            }
            iswallet = false;
        }

        if (addDrawer != null) {
            addDrawer.walletbalncetxt.setText(generalFunc.retrieveLangLBl("wallet Balance", "LBL_WALLET_BALANCE") + ": " + generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("user_available_balance", userProfileJson)));

        }

        Utils.printLog("schedulrefresh", "::" + schedulrefresh);

        if (!schedulrefresh) {
            if (loadAvailCabs != null) {

                loadAvailCabs.onResumeCalled();
            }
        }
        app_type = generalFunc.getJsonValueStr("APP_TYPE", obj_userProfile);


        if (driverAssignedHeaderFrag != null) {
            driverAssignedHeaderFrag.onResumeCalled();
            pinImgView.setVisibility(View.GONE);
            RelativeLayout.LayoutParams params = (RelativeLayout.LayoutParams) (userLocBtnImgView).getLayoutParams();
            params.bottomMargin = Utils.dipToPixels(getActContext(), 200);
        }

        if (!isufxbackview) {

            if (currentLoadedDriverList != null) {
                ConfigPubNub.getInstance().subscribeToChannels(getDriverLocationChannelList());
            }
        }


    }

    public void setUserInfo() {
        View view = ((Activity) getActContext()).findViewById(android.R.id.content);
        ((MTextView) view.findViewById(R.id.userNameTxt)).setText(generalFunc.getJsonValueStr("vName", obj_userProfile) + " "
                + generalFunc.getJsonValueStr("vLastName", obj_userProfile));
        ((MTextView) view.findViewById(R.id.walletbalncetxt)).setText(generalFunc.retrieveLangLBl("", "LBL_WALLET_BALANCE") + ": " + generalFunc.convertNumberWithRTL(generalFunc.getJsonValueStr("user_available_balance", obj_userProfile)));

        generalFunc.checkProfileImage((SelectableRoundedImageView) view.findViewById(R.id.userImgView), userProfileJson, "vImgName");
    }

    private void resetView(String from) {
        try {


            String vTripStatus = generalFunc.getJsonValueStr("vTripStatus", obj_userProfile);

            if (intCheck.isNetworkConnected() && intCheck.check_int()) {
                setNoLocViewEnableOrDisabled(false);
            }

            if (generalFunc.isLocationEnabled()) {

                if (noloactionview.getVisibility() == View.VISIBLE) {
                    noloactionview.setVisibility(View.GONE);
                    enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), true);
                }


                if (from.equals("gps")) {
                    NoLocationView();
                }

            } else {
                if (vTripStatus != null && !vTripStatus.equals("Active") && !vTripStatus.equals("On Going Trip")) {

                    NoLocationView();
                }

            }

            if (driverAssignedHeaderFrag != null) {
                if (getMap() != null) {
                    if (ActivityCompat.checkSelfPermission(this, android.Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(this, android.Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
                        // TODO: Consider calling
                        //    ActivityCompat#requestPermissions
                        // here to request the missing permissions, and then overriding
                        //   public void onRequestPermissionsResult(int requestCode, String[] permissions,
                        //                                          int[] grantResults)
                        // to handle the case where the user grants the permission. See the documentation
                        // for ActivityCompat#requestPermissions for more details.
                        return;
                    }
                    getMap().setMyLocationEnabled(false);
                }
            }

            if (vTripStatus != null && !(vTripStatus.contains("Not Active") && !(vTripStatus.contains("NONE")))) {

                try {
                    if (!vTripStatus.contains("Not Requesting")) {
                        if (gMap != null) {

                        }
                    } else {
                        if (!isgpsview) {
                            NoLocationView();
                        }
                    }
                } catch (Exception e) {

                }


            } else {
                if (!isgpsview) {
                    NoLocationView();
                }

            }
        } catch (Exception e) {

        }
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        try {


            stopReceivingPrivateMsg();

            releaseScheduleNotificationTask();
            if (getLastLocation != null) {
                getLastLocation.stopLocationUpdates();
                getLastLocation = null;
            }

            if (gMap != null) {
                gMap.clear();
                gMap = null;
            }

            Utils.runGC();

        } catch (Exception e) {

        }

    }


    public void stopReceivingPrivateMsg() {

        if (ConfigPubNub.getInstance(true) != null) {
            ConfigPubNub.getInstance().releaseInstances();
        }
        generalFunc.sendHeartBeat();
        Utils.runGC();

    }


    public void setDriverImgView(SelectableRoundedImageView driverImgView) {
        this.driverImgView = driverImgView;
    }

    public Bitmap getDriverImg() {

        try {
            if (driverImgView != null) {
                driverImgView.buildDrawingCache();
                Bitmap driverBitmap = driverImgView.getDrawingCache();

                if (driverBitmap != null) {
                    return driverBitmap;
                } else {
                    return BitmapFactory.decodeResource(getResources(), R.mipmap.ic_no_pic_user);
                }
            }

            return BitmapFactory.decodeResource(getResources(), R.mipmap.ic_no_pic_user);
        } catch (Exception e) {
            return BitmapFactory.decodeResource(getResources(), R.mipmap.ic_no_pic_user);
        }
    }

    public Bitmap getUserImg() {
        try {
            ((SelectableRoundedImageView) findViewById(R.id.userImgView)).buildDrawingCache();
            Bitmap userBitmap = ((SelectableRoundedImageView) findViewById(R.id.userImgView)).getDrawingCache();

            if (userBitmap != null) {
                return userBitmap;
            } else {
                return BitmapFactory.decodeResource(getResources(), R.mipmap.ic_no_pic_user);
            }
        } catch (Exception e) {
            return BitmapFactory.decodeResource(getResources(), R.mipmap.ic_no_pic_user);
        }

    }

    public void pubNubStatus(PNStatusCategory status) {

    }

    public void pubNubMsgArrived(final String message) {

        currentTripId = generalFunc.getJsonValue("iTripId", message);
        runOnUiThread(new Runnable() {
            @Override
            public void run() {

                String msgType = generalFunc.getJsonValue("MsgType", message);

                if (msgType.equals("TripEnd")) {

                    if (isDriverAssigned == false) {
                        generalFunc.restartApp();
                        return;
                    }
                }
                if (msgType.equals("LocationUpdate")) {
                    if (loadAvailCabs == null) {
                        return;
                    }

                    String iDriverId = generalFunc.getJsonValue("iDriverId", message);
                    String vLatitude = generalFunc.getJsonValue("vLatitude", message);
                    String vLongitude = generalFunc.getJsonValue("vLongitude", message);

                    Marker driverMarker = getDriverMarkerOnPubNubMsg(iDriverId, false);

                    LatLng driverLocation_update = new LatLng(generalFunc.parseDoubleValue(0.0, vLatitude),
                            generalFunc.parseDoubleValue(0.0, vLongitude));
                    Location driver_loc = new Location("gps");
                    driver_loc.setLatitude(driverLocation_update.latitude);
                    driver_loc.setLongitude(driverLocation_update.longitude);

                    if (driverMarker != null) {
                        float rotation = (float) SphericalUtil.computeHeading(driverMarker.getPosition(), driverLocation_update);

                        if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase("UberX")) {
                            rotation = 0;
                        }

                        AnimateMarker.animateMarker(driverMarker, gMap, driver_loc, rotation, 1200);
                    }

                } else if (msgType.equals("TripRequestCancel")) {
                    Utils.printLog("TripRequestCancel", "called");


                    tripStatus = "TripCanelled";
                    Utils.printLog("DRIVER_REQUEST_METHOD", "::" + DRIVER_REQUEST_METHOD);
                    if (TextUtils.isEmpty(tripId) && eTripType.equals(CabGeneralType_Deliver) && getCurrentCabGeneralType().equals(CabGeneralType_Deliver)) {
                        if (tripId.equalsIgnoreCase(currentTripId)) {
                            if (DRIVER_REQUEST_METHOD.equals("Distance") || DRIVER_REQUEST_METHOD.equals("Time")) {
                                if (sendNotificationToDriverByDist != null) {
                                    sendNotificationToDriverByDist.incTask();
                                }
                            }
                        }
                    } else {
                        if (DRIVER_REQUEST_METHOD.equals("Distance") || DRIVER_REQUEST_METHOD.equals("Time")) {
                            if (sendNotificationToDriverByDist != null) {
                                sendNotificationToDriverByDist.incTask();
                            }
                        }
                    }
                } else if (msgType.equals("LocationUpdateOnTrip")) {

                    if (!isDriverAssigned) {
                        return;
                    }

                    if (generalFunc.checkLocationPermission(true)) {
                        getMap().setMyLocationEnabled(false);
                    }
                    if (driverAssignedHeaderFrag != null) {
                        driverAssignedHeaderFrag.updateDriverLocation(message);
                    }

                } else if (msgType.equals("DriverArrived")) {

                    if (isDriverAssigned == false) {
                        generalFunc.restartApp();
                        return;
                    }


                    tripStatus = "DriverArrived";
                    if (driverAssignedHeaderFrag != null) {
                        driverAssignedHeaderFrag.isDriverArrived = true;
                        driverAssignedHeaderFrag.setDriverStatusTitle(generalFunc.retrieveLangLBl("", "LBL_DRIVER_ARRIVED_TXT"));

                        gMap.clear();


                        if (driverAssignedHeaderFrag.updateDestMarkerTask != null) {
                            driverAssignedHeaderFrag.updateDestMarkerTask.stopRepeatingTask();
                            driverAssignedHeaderFrag.updateDestMarkerTask = null;
                            if (driverAssignedHeaderFrag.time_marker != null) {
                                driverAssignedHeaderFrag.time_marker.remove();
                                driverAssignedHeaderFrag.time_marker = null;
                            }
                            if (driverAssignedHeaderFrag.route_polyLine != null) {
                                driverAssignedHeaderFrag.route_polyLine.remove();
                            }
                        }
                        if (driverAssignedHeaderFrag.driverMarker != null) {
                            driverAssignedHeaderFrag.driverMarker.remove();
                            driverAssignedHeaderFrag.driverMarker = null;
                        }
                        if (driverAssignedHeaderFrag.driverData != null) {
                            driverAssignedHeaderFrag.driverData.get("DriverTripStatus");
                            driverAssignedHeaderFrag.driverData.put("DriverTripStatus", "Arrived");
                        }
                        driverAssignedHeaderFrag.configDriverLoc();
                        driverAssignedHeaderFrag.addPickupMarker();

                    }

                    userLocBtnImgView.performClick();

                    if (driverAssignedHeaderFrag != null) {
                        if (driverAssignedHeaderFrag.isDriverArrived || driverAssignedHeaderFrag.isDriverArrivedNotGenerated) {
                            return;
                        }
                    }

                } else {

                    onGcmMessageArrived(message);

                }

            }
        });

    }

    public Marker getDriverMarkerOnPubNubMsg(String iDriverId, boolean isRemoveFromList) {

        if (loadAvailCabs != null) {
            ArrayList<Marker> currentDriverMarkerList = loadAvailCabs.getDriverMarkerList();

            if (currentDriverMarkerList != null) {
                for (int i = 0; i < currentDriverMarkerList.size(); i++) {
                    Marker marker = currentDriverMarkerList.get(i);

                    String driver_id = marker.getTitle().replace("DriverId", "");

                    if (driver_id.equals(iDriverId)) {

                        if (isRemoveFromList) {
                            loadAvailCabs.getDriverMarkerList().remove(i);
                        }

                        return marker;
                    }

                }
            }
        }


        return null;
    }

    @Override
    public void onBackPressed() {
        Utils.printLog("onbackpress", "call");

        callBackEvent(false);

    }

    public void callBackEvent(boolean status) {
        try {


            if (pickUpLocSelectedFrag != null) {
                pickUpLocSelectedFrag = null;

                if (loadAvailCabs != null) {
                    loadAvailCabs.selectProviderId = "";

                    loadAvailCabs.changeCabs();
                }
                if (reqPickUpFrag != null) {
                    getSupportFragmentManager().beginTransaction().
                            remove(reqPickUpFrag).commit();
                    reqPickUpFrag = null;
                    RelativeLayout.LayoutParams params = (RelativeLayout.LayoutParams) (userLocBtnImgView).getLayoutParams();
                    params.bottomMargin = Utils.dipToPixels(getActContext(), 20);

                    ridelaterView.setVisibility(View.GONE);

                    isUfxRideLater = false;

                    isMarkerClickable = true;

                    try {
                        LinearLayout.LayoutParams paramsRide = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
                        paramsRide.gravity = Gravity.TOP;
                        ridelaterHandleView.setLayoutParams(paramsRide);
                    } catch (Exception e) {

                    }

                }

                if (mainHeaderFrag != null) {
                    mainHeaderFrag = null;

                }


                setMainHeaderView();
                // setDefaultView();
                return;

            }
            if (status) {
                if (requestNearestCab != null) {
                    requestNearestCab.dismissDialog();
                }

                releaseScheduleNotificationTask();
            }


            if (addDrawer.checkDrawerState(false)) {
                return;
            }

            if (cabSelectionFrag == null) {

            } else {
                MapAnimator.getInstance().stopRouteAnim();
                getSupportFragmentManager().beginTransaction().remove(cabSelectionFrag).commit();
                cabSelectionFrag = null;


                gMap.clear();


                mainHeaderFrag.menuImgView.setVisibility(View.VISIBLE);
                mainHeaderFrag.backImgView.setVisibility(View.GONE);

                mainHeaderFrag.handleDestAddIcon();
                cabTypesArrList.clear();
                //  mainHeaderFrag.setDestinationAddress(generalFunc.retrieveLangLBl("", "LBL_ADD_DESTINATION_BTN_TXT"));
                mainHeaderFrag.setDefaultView();
                pinImgView.setVisibility(View.GONE);
                if (loadAvailCabs != null) {
                    selectedCabTypeId = loadAvailCabs.getFirstCarTypeID();
                }
                RelativeLayout.LayoutParams params = (RelativeLayout.LayoutParams) (userLocBtnImgView).getLayoutParams();
                params.bottomMargin = Utils.dipToPixels(getActContext(), 10);

                if (mainHeaderFrag != null) {
                    mainHeaderFrag.releaseAddressFinder();
                }
                gMap.setPadding(0, 0, 0, 60);
                DisplayMetrics displaymetrics = new DisplayMetrics();
                getWindowManager().getDefaultDisplay().getMetrics(displaymetrics);
                int height = displaymetrics.heightPixels;
                ViewGroup.LayoutParams param = map.getView().getLayoutParams();
                param.height = height;
                map.getView().setLayoutParams(param);
                userLocBtnImgView.performClick();
                return;
            }


            if ((!TextUtils.isEmpty(tripId) &&
                    (getCurrentCabGeneralType().equalsIgnoreCase(CabGeneralType_Deliver) &&
                            eTripType.equalsIgnoreCase(CabGeneralType_Deliver)))) {

                generalFunc.autoLogin(MainActivity.this, "");

                return;
            }


            super.onBackPressed();
        } catch (Exception e) {
            Log.e("Exception", "::" + e.toString());
        }
    }

    public Context getActContext() {
        return MainActivity.this;
    }

    @Override
    public void onCreateContextMenu(ContextMenu menu, View v, ContextMenu.ContextMenuInfo menuInfo) {
        super.onCreateContextMenu(menu, v, menuInfo);

        menu.add(0, 1, 0, "" + generalFunc.retrieveLangLBl("", "LBL_CALL_TXT"));
        menu.add(0, 2, 0, "" + generalFunc.retrieveLangLBl("", "LBL_MESSAGE_TXT"));
    }

    @Override
    public boolean onContextItemSelected(MenuItem item) {

        if (item.getItemId() == 1) {

            try {
                Intent callIntent = new Intent(Intent.ACTION_DIAL);
                callIntent.setData(Uri.parse("tel:" + driverDetailFrag.getDriverPhone()));
                startActivity(callIntent);
            } catch (Exception e) {
                // TODO: handle exception
            }

        } else if (item.getItemId() == 2) {

            try {
                Intent smsIntent = new Intent(Intent.ACTION_VIEW);
                smsIntent.setType("vnd.android-dir/mms-sms");
                smsIntent.putExtra("address", "" + driverDetailFrag.getDriverPhone());
                startActivity(smsIntent);
            } catch (Exception e) {
                // TODO: handle exception
            }

        }

        return super.onContextItemSelected(item);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == Utils.MY_PROFILE_REQ_CODE && resultCode == RESULT_OK && data != null) {
            String userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
            obj_userProfile = generalFunc.getJsonObject(userProfileJson);
            this.userProfileJson = userProfileJson;
            addDrawer.changeUserProfileJson(this.userProfileJson);
        } else if (requestCode == Utils.VERIFY_INFO_REQ_CODE && resultCode == RESULT_OK && data != null) {

            String msgType = data.getStringExtra("MSG_TYPE");

            if (msgType.equalsIgnoreCase("EDIT_PROFILE")) {
                addDrawer.openMenuProfile();
            }
            this.userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
            obj_userProfile = generalFunc.getJsonObject(userProfileJson);
            addDrawer.userProfileJson = this.userProfileJson;
            addDrawer.obj_userProfile = generalFunc.getJsonObject(this.userProfileJson);
            addDrawer.buildDrawer();
        } else if (requestCode == Utils.VERIFY_INFO_REQ_CODE) {

            this.userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
            obj_userProfile = generalFunc.getJsonObject(userProfileJson);
            addDrawer.userProfileJson = this.userProfileJson;
            addDrawer.obj_userProfile = generalFunc.getJsonObject(this.userProfileJson);
            addDrawer.buildDrawer();
        } else if (requestCode == Utils.CARD_PAYMENT_REQ_CODE && resultCode == RESULT_OK && data != null) {
            iswallet = true;
            String userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
            obj_userProfile = generalFunc.getJsonObject(userProfileJson);
            this.userProfileJson = userProfileJson;

            if (cabSelectionFrag != null) {
                cabSelectionFrag.isCardValidated = true;
            }
            addDrawer.changeUserProfileJson(this.userProfileJson);
        } else if (requestCode == Utils.DELIVERY_DETAILS_REQ_CODE && resultCode == RESULT_OK && data != null) {
            try {
                if (!getCabReqType().equals(Utils.CabReqType_Later)) {
                    isdelivernow = true;
                } else {
                    isdeliverlater = true;
                }
                checkSurgePrice("", data);

            } catch (Exception e) {

            }
        } else if (requestCode == Utils.PLACE_AUTOCOMPLETE_REQUEST_CODE) {
            if (resultCode == RESULT_OK) {
                Place place = PlaceAutocomplete.getPlace(this, data);

                LatLng placeLocation = place.getLatLng();

                setDestinationPoint(placeLocation.latitude + "", placeLocation.longitude + "", place.getAddress().toString(), true);
                mainHeaderFrag.setDestinationAddress(place.getAddress().toString());

                CameraUpdate cu = CameraUpdateFactory.newLatLngZoom(placeLocation, Utils.defaultZomLevel);

                if (gMap != null) {
                    gMap.clear();
                    gMap.moveCamera(cu);
                }

            } else if (resultCode == PlaceAutocomplete.RESULT_ERROR) {
                Status status = PlaceAutocomplete.getStatus(this, data);


                generalFunc.showMessage(generalFunc.getCurrentView(MainActivity.this),
                        status.getStatusMessage());
            } else if (requestCode == RESULT_CANCELED) {

            }


        } else if (requestCode == Utils.ASSIGN_DRIVER_CODE) {

            if (app_type.equals(Utils.CabGeneralTypeRide_Delivery_UberX)) {
                if (!isUfx) {
                } else {
                    isUfx = false;
                    Bundle bn = new Bundle();
                    new StartActProcess(getActContext()).startActWithData(MainActivity.class, bn);
                    finishAffinity();
                }
            } else {
            }
        } else if (requestCode == Utils.REQUEST_CODE_GPS_ON) {

            gpsEnabled();

        } else if (requestCode == Utils.REQUEST_CODE_NETWOEK_ON) {

            setNoLocViewEnableOrDisabled(true);
            showprogress();

            final Handler handler = new Handler();
            int delay = 1000; //milliseconds

            handler.postDelayed(new Runnable() {
                public void run() {


                    if (intCheck.isNetworkConnected() && intCheck.check_int()) {
                        hideprogress();
                        setNoLocViewEnableOrDisabled(false);

                    } else {
                        setNoLocViewEnableOrDisabled(true);
                        handler.postDelayed(this, 1000);

                    }
                    //

                }
            }, delay);


        } else if (requestCode == Utils.SEARCH_PICKUP_LOC_REQ_CODE && resultCode == RESULT_OK && data != null && gMap != null) {

            if (resultCode == RESULT_OK) {

                if (!isFrompickupaddress) {
                    NoLocationView();
                }

                isFrompickupaddress = true;

                final Location location = new Location("rider");
                location.setLatitude(generalFunc.parseDoubleValue(0.0, data.getStringExtra("Latitude")));
                location.setLongitude(generalFunc.parseDoubleValue(0.0, data.getStringExtra("Longitude")));
                onLocationUpdate(location);


            }

        } else if (requestCode == Utils.UFX_REQUEST_CODE) {
            if (resultCode == RESULT_OK) {


                schedulrefresh = true;
                isufxbackview = true;
                ridelaterView.setVisibility(View.GONE);

                if (loadAvailCabs != null) {
                    loadAvailCabs.setTaskKilledValue(true);
                }

                appliedPromoCode = data.getStringExtra("promocode");
                userComment = data.getStringExtra("comment");

                if (data.getStringExtra("paymenttype").equalsIgnoreCase("cash")) {
                    isCashSelected = true;

                } else {
                    isCashSelected = false;

                }
                if (bookingtype.equals(Utils.CabReqType_Now)) {
                    requestPickUp();
                } else {
                    setRideSchedule();
                    bookRide();
                }
            } else {
                loadAvailCabs.selectProviderId = "";
            }
        } else if (requestCode == Utils.SCHEDULE_REQUEST_CODE && resultCode == RESULT_OK) {

            SelectDate = data.getStringExtra("SelectDate");
            sdate = data.getStringExtra("Sdate");
            Stime = data.getStringExtra("Stime");
//
            bookingtype = Utils.CabReqType_Later;

            uberXDriverListArea.setVisibility(View.VISIBLE);
            uberXNoDriverTxt.setVisibility(View.GONE);
            ridelaterView.setVisibility(View.GONE);
            (findViewById(R.id.driverListAreaLoader)).setVisibility(View.VISIBLE);
            (findViewById(R.id.searchingDriverTxt)).setVisibility(View.VISIBLE);

            if (loadAvailCabs != null) {
                loadAvailCabs.changeCabs();
            }
            schedulrefresh = false;

        }


    }

    private void gpsEnabled() {
        try {


            isgpsview = true;

            if (generalFunc.isLocationEnabled()) {
                showprogress();

                if (getLastLocation != null) {
                    getLastLocation.stopLocationUpdates();
                    getLastLocation = null;
                }
                getLastLocation = new GetLocationUpdates(getActContext(), Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, true, this);

                if (getLastLocation != null) {


                    final Handler handler = new Handler();

                    int delay = 1000; //milliseconds

                    runnable = new Runnable() {
                        @Override
                        public void run() {
                            {
                                isgpsview = true;
                                //do something
                                if (getLastLocation.getLastLocation() != null) {
                                    isgpsview = false;
                                    hideprogress();

                                    userLocation = getLastLocation.getLastLocation();
                                    pickUpLocation = getLastLocation.getLastLocation();

                                    if (mainHeaderFrag != null) {
                                        mainHeaderFrag.refreshFragment();
                                    }

                                    NoLocationView();

                                    if (isFrompickupaddress && handler != null && runnable != null) {
                                        handler.removeCallbacks(runnable);
                                    }

                                } else {
                                    handler.postDelayed(this, 1000);
                                }


                            }
                        }
                    };
                    handler.postDelayed(runnable, delay);
                }


            } else {
                isgpsview = false;
            }
        } catch (Exception e) {

        }
    }

    public void openPrefrancedailog() {


        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());

        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.activity_prefrance, null);

        final MTextView TitleTxt = (MTextView) dialogView.findViewById(R.id.TitleTxt);

        final CheckBox checkboxHandicap = (CheckBox) dialogView.findViewById(R.id.checkboxHandicap);
        final CheckBox checkboxFemale = (CheckBox) dialogView.findViewById(R.id.checkboxFemale);

        if (generalFunc.retrieveValue(CommonUtilities.HANDICAP_ACCESSIBILITY_OPTION).equalsIgnoreCase("yes")) {
            checkboxHandicap.setVisibility(View.VISIBLE);
        } else {
            checkboxHandicap.setVisibility(View.GONE);
        }

        if (generalFunc.retrieveValue(CommonUtilities.FEMALE_RIDE_REQ_ENABLE).equalsIgnoreCase("yes")) {
            if (!generalFunc.getJsonValue("eGender", userProfileJson).equalsIgnoreCase("Male")) {
                checkboxFemale.setVisibility(View.VISIBLE);
            } else {
                checkboxFemale.setVisibility(View.GONE);
            }
        } else {
            checkboxFemale.setVisibility(View.GONE);
        }
        if (isfemale) {
            checkboxFemale.setChecked(true);
        }

        if (ishandicap) {
            checkboxHandicap.setChecked(true);
        }
        MButton btn_type2 = btn_type2 = ((MaterialRippleLayout) dialogView.findViewById(R.id.btn_type2)).getChildView();
        int submitBtnId = Utils.generateViewId();
        btn_type2.setId(submitBtnId);
        btn_type2.setText(generalFunc.retrieveLangLBl("Update", "LBL_UPDATE"));
        btn_type2.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                pref_dialog.dismiss();
                if (checkboxFemale.isChecked()) {
                    isfemale = true;

                } else {
                    isfemale = false;

                }
                if (checkboxHandicap.isChecked()) {
                    ishandicap = true;

                } else {
                    ishandicap = false;
                }

                if (loadAvailCabs != null) {
                    loadAvailCabs.changeCabs();
                }

            }
        });


        builder.setView(dialogView);
        TitleTxt.setText(generalFunc.retrieveLangLBl("Prefrance", "LBL_PREFRANCE_TXT"));
        checkboxHandicap.setText(generalFunc.retrieveLangLBl("Filter handicap accessibility drivers only", "LBL_MUST_HAVE_HANDICAP_ASS_CAR"));
        checkboxFemale.setText(generalFunc.retrieveLangLBl("Accept Female Only trip request", "LBL_ACCEPT_FEMALE_REQ_ONLY_PASSENGER"));


        pref_dialog = builder.create();
        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(pref_dialog);
        }
        pref_dialog.show();

    }

    public void getTollcostValue(final String driverIds, final String cabRequestedJson, final Intent data) {

        if (isFixFare) {
            setDeliverOrRideReq(driverIds, cabRequestedJson, data);
            return;
        }


        if (cabSelectionFrag != null) {
            if (cabSelectionFrag.isSkip) {
                setDeliverOrRideReq(driverIds, cabRequestedJson, data);
                return;
            }
        }

        if (generalFunc.retrieveValue(CommonUtilities.ENABLE_TOLL_COST).equalsIgnoreCase("Yes")) {

            String url = CommonUtilities.TOLLURL + generalFunc.retrieveValue(CommonUtilities.TOLL_COST_APP_ID)
                    + "&app_code=" + generalFunc.retrieveValue(CommonUtilities.TOLL_COST_APP_CODE) + "&waypoint0=" + getPickUpLocation().getLatitude()
                    + "," + getPickUpLocation().getLongitude() + "&waypoint1=" + getDestLocLatitude() + "," + getDestLocLongitude() + "&mode=fastest;car";

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


                                TollTaxDialog(driverIds, cabRequestedJson, data);


                            } catch (Exception e) {

                                TollTaxDialog(driverIds, cabRequestedJson, data);
                            }

                        } else {
                            TollTaxDialog(driverIds, cabRequestedJson, data);
                        }


                    } else {
                        generalFunc.showError();
                    }

                }

            });
            exeWebServer.execute();


        } else {
            setDeliverOrRideReq(driverIds, cabRequestedJson, data);


        }

    }

    private void setDeliverOrRideReq(String driverIds, String cabRequestedJson, Intent data) {

        if (isDeliver(getCurrentCabGeneralType()) && isDeliver(app_type)) {
            // setDeliverySchedule();
        } else {

            if (app_type.equals(Utils.CabGeneralType_UberX)) {
                pickUpLocClicked();
            } else {

                if (getCabReqType().equals(Utils.CabReqType_Later)) {
                    isrideschedule = true;

                } else {
                    isreqnow = true;

                }
                // requestPickUp();
            }
        }


        if (data != null) {
            if (isdelivernow) {
                isdelivernow = false;
                deliverNow(data);
            } else if (isdeliverlater) {
                isdeliverlater = false;
                scheduleDelivery(data);
            }


        } else {
            if (isrideschedule) {
                isrideschedule = false;
                bookRide();
            } else if (isreqnow) {
                isreqnow = false;
                //sendRequestToDrivers(driverIds, cabRequestedJson);
                requestPickUp();
            }

        }
    }

    public void TollTaxDialog(final String driverIds, final String cabRequestedJson, final Intent data) {

        if (!isTollCostdilaogshow) {
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
                        isTollCostdilaogshow = true;
                        setDeliverOrRideReq(driverIds, cabRequestedJson, data);


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

                        closeRequestDialog(true);
                    }
                });


                tolltax_dialog = builder.create();
                if (generalFunc.isRTLmode() == true) {
                    generalFunc.forceRTLIfSupported(tolltax_dialog);
                }
                tolltax_dialog.show();
            } else {
                setDeliverOrRideReq(driverIds, cabRequestedJson, data);
            }
        } else {
            setDeliverOrRideReq(driverIds, cabRequestedJson, data);

        }
    }

    public void callgederApi(String egender)

    {
        HashMap<String, String> parameters = new HashMap<>();
        parameters.put("type", "updateUserGender");
        parameters.put("UserType", Utils.userType);
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("eGender", egender);


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);


                String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
                if (isDataAvail) {
                    generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, message);
                    userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
                    obj_userProfile = generalFunc.getJsonObject(userProfileJson);
                    prefBtnImageView.performClick();
                }


            }
        });
        exeWebServer.execute();
    }

    public void genderDailog() {
        closeDrawer();
        final Dialog builder = new Dialog(getActContext(), R.style.Theme_Dialog);
        builder.requestWindowFeature(Window.FEATURE_NO_TITLE);
        builder.getWindow().setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));
        builder.setContentView(R.layout.gender_view);
        builder.getWindow().setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);

        final MTextView genderTitleTxt = (MTextView) builder.findViewById(R.id.genderTitleTxt);
        final MTextView maleTxt = (MTextView) builder.findViewById(R.id.maleTxt);
        final MTextView femaleTxt = (MTextView) builder.findViewById(R.id.femaleTxt);
        final ImageView gendercancel = (ImageView) builder.findViewById(R.id.gendercancel);
        final ImageView gendermale = (ImageView) builder.findViewById(R.id.gendermale);
        final ImageView genderfemale = (ImageView) builder.findViewById(R.id.genderfemale);
        final LinearLayout male_area = (LinearLayout) builder.findViewById(R.id.male_area);
        final LinearLayout female_area = (LinearLayout) builder.findViewById(R.id.female_area);

        genderTitleTxt.setText(generalFunc.retrieveLangLBl("Select your gender to continue", "LBL_SELECT_GENDER"));
        maleTxt.setText(generalFunc.retrieveLangLBl("Male", "LBL_MALE_TXT"));
        femaleTxt.setText(generalFunc.retrieveLangLBl("FeMale", "LBL_FEMALE_TXT"));

        gendercancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                builder.dismiss();
            }
        });

        male_area.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                callgederApi("Male");
                builder.dismiss();

            }
        });
        female_area.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                callgederApi("Female");
                builder.dismiss();

            }
        });

        builder.show();

    }

    public void NoLocationView() {

        if (!isUfx) {

            if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {
                setNoLocViewEnableOrDisabled(true);
                return;
            }

            if (!isFrompickupaddress) {
                if (!generalFunc.isLocationEnabled()) {
                    if (userLocation == null) {
                        setSelectHeaders(true);
                    } else {
                        setSelectHeaders(true);

                    }
                } else {
                    setSelectHeaders(false);
                }
            } else {
                if (isFrompickupaddress) {
                    setSelectHeaders(false);
                } else if (!generalFunc.isLocationEnabled() || !isFrompickupaddress) {
                    setSelectHeaders(true);
                } else if (generalFunc.isLocationEnabled() || !isFrompickupaddress) {
                    setSelectHeaders(false);

                    if (userLocation != null) {
                        pickUpLocation = userLocation;

                    } else {

                        if (getLastLocation != null) {
                            getLastLocation.stopLocationUpdates();
                            getLastLocation = null;
                        }
                        getLastLocation = new GetLocationUpdates(getActContext(), Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, true, this);

                        userLocation = getLastLocation.getLastLocation();
                        pickUpLocation = getLastLocation.getLastLocation();

                    }
                    if (mainHeaderFrag != null) {
                        mainHeaderFrag.refreshFragment();
                    }

                } else {
                    setSelectHeaders(false);
                }
            }
        }

    }

    public void setSelectHeaders(boolean selectHeaders) {

        try {


            if (selectHeaders) {
                noloactionview.setVisibility(View.VISIBLE);
                enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), false);
                if (mainHeaderFrag != null) {
                    mainHeaderFrag.area_source.setVisibility(View.GONE);
                    mainHeaderFrag.area2.setVisibility(View.GONE);

                }
            } else {
                noloactionview.setVisibility(View.GONE);
                enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), true);

                if (!isUfx) {
                    if (mainHeaderFrag != null) {

                        if (cabSelectionFrag == null) {

                            if (!isFrompickupaddress) {
                                mainHeaderFrag.area_source.setVisibility(View.VISIBLE);
                                mainHeaderFrag.area2.setVisibility(View.GONE);
                            } else if (isDestinationMode == true) {
                                mainHeaderFrag.area_source.setVisibility(View.GONE);
                                mainHeaderFrag.area2.setVisibility(View.VISIBLE);
                            } else if (isFrompickupaddress) {
                                mainHeaderFrag.area_source.setVisibility(View.VISIBLE);
                                mainHeaderFrag.area2.setVisibility(View.GONE);
                            } else {
                                mainHeaderFrag.area_source.setVisibility(View.VISIBLE);
                                mainHeaderFrag.area2.setVisibility(View.GONE);


                            }
                        }
                    }
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }


    }

    public void setNoLocViewEnableOrDisabled(boolean show) {

        String vTripStatus = generalFunc.getJsonValue("vTripStatus", userProfileJson);

        if (show & (vTripStatus != null && (vTripStatus.equals("Active") || vTripStatus.equals("On Going Trip")) || (requestNearestCab != null))) {
            removeAllNetworkViews();
            noloactionview.setVisibility(View.GONE);
            enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), true);
            return;
        }

        if (show) {
            noloactionview.setVisibility(View.VISIBLE);
            enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), false);

            setLocationTitles(false);
        } else {

            if (noInternetConn != null) {
                noInternetConn.setTaskKilledValue(true);
                noInternetConn = null;
            }

            enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), true);

            if (pickupredirectTxt.getVisibility() == View.GONE) {
                noloactionview.setVisibility(View.GONE);
                enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), true);
                setLocationTitles(true);

            }

            showLocationView();
        }
    }


    // network location

    private void removeAllNetworkViews() {
        if (noInternetConn != null) {
            noInternetConn.setTaskKilledValue(true);
            noInternetConn = null;
        }

        if (pickupredirectTxt.getVisibility() == View.GONE) {
            noloactionview.setVisibility(View.GONE);
            enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), true);
        }

        return;
    }

    private void showLocationView() {
        String vTripStatus = generalFunc.getJsonValue("vTripStatus", userProfileJson);


        if (generalFunc.isLocationEnabled()) {

        } else if (vTripStatus != null && !vTripStatus.equals("Active") && !vTripStatus.equals("On Going Trip")) {
            NoLocationView();
        }


        if (vTripStatus != null && !(vTripStatus.contains("Not Active") && !(vTripStatus.contains("NONE")))) {

            try {
                if (!vTripStatus.contains("Not Requesting")) {

                } else {
                    if (!isgpsview) {
                        if (!vTripStatus.equals("On Going Trip")) {
                            NoLocationView();
                        }
                    }
                }
            } catch (Exception e) {
            }
        } else {
            if (!isgpsview) {
                NoLocationView();
            }

        }
    }

    public void setNoGpsViewEnableOrDisabled(boolean show) {

        if (pickupredirectTxt.getVisibility() == View.VISIBLE) {
            setLocationTitles(true);
        }

        if (show) {
            showLocationView();
        } else {
            enableDisableViewGroup((RelativeLayout) findViewById(R.id.rootRelView), true);

            resetView("gps");
        }
    }

    private void setLocationTitles(boolean setLocDetails) {
        if (setLocDetails) {
            noLocImgView.setImageDrawable(getActContext().getResources().getDrawable(R.mipmap.ic_gps_off));
            noLocTitleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_LOCATION_SERVICES_TURNED_OFF"));
            noLocMsgTxt.setText(generalFunc.retrieveLangLBl("", "LBL_LOCATION_SERVICES_TURNED_OFF_DETAILS"));
            settingTxt.setText(generalFunc.retrieveLangLBl("", "LBL_TURN_ON_LOC_SERVICE"));
            pickupredirectTxt.setText(generalFunc.retrieveLangLBl("Enter pickup address", "LBL_ENTER_PICK_UP_ADDRESS"));
            pickupredirectTxt.setVisibility(View.VISIBLE);
            pickupredirectTxt.setOnClickListener(new setOnClickList());
        } else {
            noLocTitleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_NO_INTERNET_TITLE"));
            noLocMsgTxt.setText(generalFunc.retrieveLangLBl("", "LBL_NO_INTERNET_SUB_TITLE"));
            settingTxt.setText(generalFunc.retrieveLangLBl("", "LBL_SETTINGS"));
            pickupredirectTxt.setVisibility(View.GONE);
            noLocImgView.setImageDrawable(getActContext().getResources().getDrawable(R.mipmap.ic_wifi_off));
            pickupredirectTxt.setOnClickListener(null);

        }
    }

    private void prefrenceButtonEnable() {
        if (generalFunc.retrieveValue(CommonUtilities.FEMALE_RIDE_REQ_ENABLE).equalsIgnoreCase("No") &&
                generalFunc.retrieveValue(CommonUtilities.HANDICAP_ACCESSIBILITY_OPTION).equalsIgnoreCase("No")) {
            prefBtnImageView.setVisibility(View.GONE);

        } else if (generalFunc.retrieveValue(CommonUtilities.HANDICAP_ACCESSIBILITY_OPTION).equalsIgnoreCase("No") &&
                !generalFunc.retrieveValue(CommonUtilities.FEMALE_RIDE_REQ_ENABLE).equalsIgnoreCase("Yes")
                || (generalFunc.retrieveValue(CommonUtilities.FEMALE_RIDE_REQ_ENABLE).equalsIgnoreCase("Yes") &&
                generalFunc.getJsonValue("eGender", userProfileJson).equals("Male")
                && !generalFunc.retrieveValue(CommonUtilities.HANDICAP_ACCESSIBILITY_OPTION).equalsIgnoreCase("Yes"))) {
            prefBtnImageView.setVisibility(View.GONE);
        }
    }


    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(getActContext());
            if (i == userLocBtnImgView.getId()) {
                if (!generalFunc.isLocationEnabled()) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Please enable you GPS location service", "LBL_GPSENABLE_TXT"));
                    return;


                }
                isUserLocbtnclik = true;

                if (cabSelectionFrag == null) {

                    if (driverAssignedHeaderFrag != null) {
                        if (driverAssignedHeaderFrag.sourceMarker != null) {
                            driverAssignedHeaderFrag.sourceMarker.remove();
                            driverAssignedHeaderFrag.sourceMarker = null;
                        }

                        if (driverAssignedHeaderFrag.destinationPointMarker_temp != null) {
                            driverAssignedHeaderFrag.destinationPointMarker_temp.remove();
                            driverAssignedHeaderFrag.destinationPointMarker_temp = null;
                        }
                    }

                    if (isDriverAssigned && !isTripStarted && driverAssignedHeaderFrag != null) {
                        //driver topickup
                        LatLngBounds.Builder builder = new LatLngBounds.Builder();
                        if (driverAssignedHeaderFrag.driverMarker != null) {
                            builder.include(driverAssignedHeaderFrag.driverMarker.getPosition());
                        }
                        if (driverAssignedHeaderFrag.time_marker != null) {
                            builder.include(driverAssignedHeaderFrag.time_marker.getPosition());
                        } else {
                            driverAssignedHeaderFrag.addPickupMarker();
                            if (driverAssignedHeaderFrag.sourceMarker != null) {
                                builder.include(driverAssignedHeaderFrag.sourceMarker.getPosition());
                            }
                        }

                        if (driverAssignedHeaderFrag.driverMarker != null) {
                            DisplayMetrics metrics = new DisplayMetrics();
                            getWindowManager().getDefaultDisplay().getMetrics(metrics);

                            int width = metrics.widthPixels;
                            int height = metrics.heightPixels;
                            float maxZoomLevel = gMap.getMaxZoomLevel();
                            try {

                                gMap.setPadding(0, Utils.dpToPx(getActContext(), 200), 0, Utils.dpToPx(getActContext(), 200));
                                gMap.setMaxZoomPreference(maxZoomLevel - 5);
                                gMap.animateCamera(CameraUpdateFactory.newLatLngBounds(builder.build(), width - Utils.dipToPixels(getActContext(), 80), metrics.heightPixels - Utils.dpToPx(getActContext(), 200), 100), new GoogleMap.CancelableCallback() {
                                    @Override
                                    public void onFinish() {
                                        try {
                                            gMap.setMaxZoomPreference(maxZoomLevel);
                                            gMap.setPadding(0, 0, 0, Utils.dpToPx(getActContext(), 232));
                                        } catch (Exception e) {

                                        }

                                    }

                                    @Override
                                    public void onCancel() {
                                        try {
                                            gMap.setMaxZoomPreference(maxZoomLevel);
                                            gMap.setPadding(0, 0, 0, Utils.dpToPx(getActContext(), 232));

                                            gMap.moveCamera(CameraUpdateFactory.newLatLngBounds(builder.build(), width - Utils.dipToPixels(getActContext(), 80), metrics.heightPixels - Utils.dpToPx(getActContext(), 200), 100));

                                        } catch (Exception e) {

                                        }
                                    }
                                });
                            } catch (Exception e) {
                                Utils.printLog("animateCamera", "::" + e.toString());
                            }
                        }


                    } else if (isDriverAssigned && isTripStarted && driverAssignedHeaderFrag != null) {
                        //driver to dest;
                        LatLngBounds.Builder builder = new LatLngBounds.Builder();
                        if (driverAssignedHeaderFrag.driverMarker != null) {
                            builder.include(driverAssignedHeaderFrag.driverMarker.getPosition());
                        }
                        if (driverAssignedHeaderFrag.destLocation != null) {
                            builder.include(driverAssignedHeaderFrag.destLocation);
                        }
                        if (driverAssignedHeaderFrag.driverMarker != null) {
                            DisplayMetrics metrics = new DisplayMetrics();
                            getWindowManager().getDefaultDisplay().getMetrics(metrics);
                            int width = metrics.widthPixels;
                            float maxZoomLevel = gMap.getMaxZoomLevel();

                            try {


                                gMap.setPadding(0, Utils.dpToPx(getActContext(), 200), 0, Utils.dpToPx(getActContext(), 200));

                                gMap.setMaxZoomPreference(maxZoomLevel - 5);
                                gMap.animateCamera(CameraUpdateFactory.newLatLngBounds(builder.build(), width - Utils.dipToPixels(getActContext(), 80), metrics.heightPixels - Utils.dpToPx(getActContext(), 200), 0), new GoogleMap.CancelableCallback() {
                                    @Override
                                    public void onFinish() {
                                        try {
                                            gMap.setMaxZoomPreference(maxZoomLevel);
                                            gMap.setPadding(0, 0, 0, Utils.dpToPx(getActContext(), 232));
                                        } catch (Exception e) {

                                        }
                                    }

                                    @Override
                                    public void onCancel() {
                                        try {

                                            gMap.setMaxZoomPreference(maxZoomLevel);
                                            gMap.setPadding(0, 0, 0, Utils.dpToPx(getActContext(), 232));
                                            gMap.moveCamera(CameraUpdateFactory.newLatLngBounds(builder.build(), width - Utils.dipToPixels(getActContext(), 80), metrics.heightPixels - Utils.dpToPx(getActContext(), 200), 100));
                                            //  gMap.animateCamera(CameraUpdateFactory.scrollBy(0, Utils.dpToPx(getActContext(), -200)));

                                        } catch (Exception e) {

                                        }

                                    }
                                });
                            } catch (Exception e) {

                            }
                        }
                    } else {
                        try {
                            Utils.printLog("getAddressFromLocation", "::" + "call_1");
                            CameraPosition cameraPosition = cameraForUserPosition();
                            if (cameraPosition != null) {
                                getMap().moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
                                if (mainHeaderFrag != null && mainHeaderFrag.getAddressFromLocation != null && userLocation != null) {
                                    mainHeaderFrag.getAddressFromLocation.setLocation(userLocation.getLatitude(), userLocation.getLongitude());
                                    mainHeaderFrag.getAddressFromLocation.execute();
                                }
                            }
                        } catch (Exception e) {

                        }
                    }


                } else if (cabSelectionFrag != null) {


                    if (cabSelectionFrag.isSkip) {
                        cabSelectionFrag.handleSourceMarker(timeval);
                        return;
                    }

                    LatLngBounds.Builder builder = new LatLngBounds.Builder();
                    if (cabSelectionFrag.sourceMarker != null) {
                        builder.include(cabSelectionFrag.sourceMarker.getPosition());
                    }
                    if (cabSelectionFrag.destDotMarker != null) {
                        builder.include(cabSelectionFrag.destDotMarker.getPosition());
                    }

                    if (cabSelectionFrag.sourceDotMarker != null && cabSelectionFrag.destDotMarker != null) {
                        DisplayMetrics metrics = new DisplayMetrics();
                        getWindowManager().getDefaultDisplay().getMetrics(metrics);
                        int width = metrics.widthPixels;
                        gMap.animateCamera(CameraUpdateFactory.newLatLngBounds(builder.build(), width - Utils.dipToPixels(getActContext(), 80), (metrics.heightPixels - Utils.dipToPixels(getActContext(), 300)), 0));

                    }

                }


            } else if (i == emeTapImgView.getId()) {
                Bundle bn = new Bundle();
                bn.putString("UserProfileJson", userProfileJson);
                bn.putString("TripId", assignedTripId);
                new StartActProcess(getActContext()).startActWithData(ConfirmEmergencyTapActivity.class, bn);
            } else if (i == rideArea.getId()) {
                ((ImageView) findViewById(R.id.rideImg)).setImageResource(R.mipmap.ride_on);
                rideImgViewsel.setVisibility(View.VISIBLE);
                ((MTextView) findViewById(R.id.selrideTxt)).setVisibility(View.VISIBLE);
                ((MTextView) findViewById(R.id.rideTxt)).setVisibility(View.GONE);
                rideImgView.setVisibility(View.GONE);
                deliverImgView.setVisibility(View.VISIBLE);
                deliverImgViewsel.setVisibility(View.GONE);
                otherImageView.setVisibility(View.VISIBLE);
                otherImageViewsel.setVisibility(View.GONE);
                ((ImageView) findViewById(R.id.deliverImg)).setImageResource(R.mipmap.delivery_off);

                ((MTextView) findViewById(R.id.rideTxt)).setTextColor(Color.parseColor("#000000"));
                ((MTextView) findViewById(R.id.deliverTxt)).setTextColor(Color.parseColor("#000000"));


                RideDeliveryType = Utils.CabGeneralType_Ride;
                prefBtnImageView.setVisibility(View.VISIBLE);
                prefrenceButtonEnable();

                if (cabSelectionFrag != null) {
                    cabSelectionFrag.changeCabGeneralType(Utils.CabGeneralType_Ride);
                    cabSelectionFrag.currentCabGeneralType = Utils.CabGeneralType_Ride;

                    if (cabSelectionFrag.cabTypeList != null) {
                        cabSelectionFrag.cabTypeList.clear();
                        cabSelectionFrag.adapter.notifyDataSetChanged();
                    }
                }

                if (loadAvailCabs != null) {
                    loadAvailCabs.checkAvailableCabs();
                }

            } else if (i == deliverArea.getId()) {


                rideImgViewsel.setVisibility(View.GONE);
                ((MTextView) findViewById(R.id.selrideTxt)).setVisibility(View.GONE);
                ((MTextView) findViewById(R.id.rideTxt)).setVisibility(View.VISIBLE);
                rideImgView.setVisibility(View.VISIBLE);
                deliverImgView.setVisibility(View.GONE);
                deliverImgViewsel.setVisibility(View.VISIBLE);
                otherImageView.setVisibility(View.VISIBLE);
                otherImageViewsel.setVisibility(View.GONE);

                ((ImageView) findViewById(R.id.rideImg)).setImageResource(R.mipmap.ride_off);
                ((ImageView) findViewById(R.id.deliverImg)).setImageResource(R.mipmap.delivery_on);

                ((MTextView) findViewById(R.id.rideTxt)).setTextColor(Color.parseColor("#000000"));

                ((MTextView) findViewById(R.id.deliverTxt)).setTextColor(Color.parseColor("#000000"));

                RideDeliveryType = CabGeneralType_Deliver;

                isfemale = false;
                ishandicap = false;
                prefBtnImageView.setVisibility(View.GONE);

                if (cabSelectionFrag != null) {
                    cabSelectionFrag.changeCabGeneralType(CabGeneralType_Deliver);
                    cabSelectionFrag.currentCabGeneralType = Utils.CabGeneralType_Deliver;

                    if (cabSelectionFrag.cabTypeList != null) {
                        cabSelectionFrag.cabTypeList.clear();
                        cabSelectionFrag.adapter.notifyDataSetChanged();
                    }
                }

                if (loadAvailCabs != null) {
                    loadAvailCabs.checkAvailableCabs();
                }


            } else if (i == otherArea.getId()) {
                rideImgViewsel.setVisibility(View.GONE);
                ((MTextView) findViewById(R.id.selrideTxt)).setVisibility(View.GONE);
                ((MTextView) findViewById(R.id.rideTxt)).setVisibility(View.VISIBLE);
                rideImgView.setVisibility(View.VISIBLE);
                deliverImgView.setVisibility(View.VISIBLE);
                deliverImgViewsel.setVisibility(View.GONE);
                otherImageView.setVisibility(View.GONE);
                otherImageViewsel.setVisibility(View.VISIBLE);


                RideDeliveryType = Utils.CabGeneralType_Ride;
                Bundle bn = new Bundle();
                bn.putBoolean("isback", true);
            } else if (i == prefBtnImageView.getId()) {

                userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
                obj_userProfile = generalFunc.getJsonObject(userProfileJson);
                if (generalFunc.retrieveValue(CommonUtilities.FEMALE_RIDE_REQ_ENABLE).equalsIgnoreCase("Yes") && generalFunc.getJsonValue("eGender", userProfileJson).equals("")) {
                    genderDailog();

                } else {
                    openPrefrancedailog();
                }
            } else if (i == settingTxt.getId()) {

                if (pickupredirectTxt.getVisibility() == View.GONE || noInternetConn != null) {

                    new StartActProcess(getActContext()).
                            startActForResult(Settings.ACTION_SETTINGS, Utils.REQUEST_CODE_NETWOEK_ON);

                } else {
                    isgpsview = true;

                    new StartActProcess(getActContext()).
                            startActForResult(Settings.ACTION_LOCATION_SOURCE_SETTINGS, Utils.REQUEST_CODE_GPS_ON);

                }


            } else if (i == pickupredirectTxt.getId()) {


                try {


                    Bundle bn = new Bundle();
                    bn.putString("locationArea", "source");
                    bn.putDouble("lat", 0.0);
                    bn.putDouble("long", 0.0);
                    new StartActProcess(getActContext()).startActForResult(SearchLocationActivity.class, bn,
                            Utils.SEARCH_PICKUP_LOC_REQ_CODE);


                } catch (Exception e) {

                }

            } else if (i == nolocbackImgView.getId()) {
                nolocmenuImgView.setVisibility(View.VISIBLE);
                nolocbackImgView.setVisibility(View.GONE);


            } else if (i == nolocmenuImgView.getId()) {
                addDrawer.checkDrawerState(true);
            }

        }
    }


    public boolean calculateDistnace(Location start, Location end) {


        float distance = start.distanceTo(end);
        Utils.printLog("distance", "::" + distance);
        if (distance > 200) {
            return true;
        } else {
            return false;
        }
    }

    public class SendNotificationsToDriverByDist implements Runnable {

        String[] list_drivers_ids;
        String cabRequestedJson;


        int interval = generalFunc.parseIntegerValue(30, generalFunc.getJsonValue("RIDER_REQUEST_ACCEPT_TIME", userProfileJson));


        int mInterval = (interval + 5) * 1000;


        int current_position_driver_id = 0;
        private Handler mHandler_sendNotification;

        public SendNotificationsToDriverByDist(String list_drivers_ids, String cabRequestedJson) {
            this.list_drivers_ids = list_drivers_ids.split(",");
            this.cabRequestedJson = cabRequestedJson;
            mHandler_sendNotification = new Handler();


            startRepeatingTask();
        }

        @Override
        public void run() {
            setRetryReqBtn(false);

            if ((current_position_driver_id + 1) <= list_drivers_ids.length) {
                sendRequestToDrivers(list_drivers_ids[current_position_driver_id], cabRequestedJson);
                current_position_driver_id = current_position_driver_id + 1;
                Utils.printLog("Api", "interval get :: " + interval);
                mHandler_sendNotification.postDelayed(this, mInterval);
            } else {
                setRetryReqBtn(true);
                stopRepeatingTask();
            }

        }


        public void stopRepeatingTask() {
            mHandler_sendNotification.removeCallbacks(this);
            mHandler_sendNotification.removeCallbacksAndMessages(null);
            current_position_driver_id = 0;
        }

        public void incTask() {
            mHandler_sendNotification.removeCallbacks(this);
            mHandler_sendNotification.removeCallbacksAndMessages(null);
            this.run();
        }

        public void startRepeatingTask() {
            stopRepeatingTask();

            this.run();
        }

    }


}
