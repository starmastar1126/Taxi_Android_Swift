package com.fastcabtaxi.passenger;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.location.Location;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.TypedValue;
import android.view.LayoutInflater;
import android.view.View;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ScrollView;

import com.adapter.files.PlacesAdapter;
import com.adapter.files.RecentLocationAdpater;
import com.general.files.DividerItemDecoration;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.GetAddressFromLocation;
import com.general.files.GetLocationUpdates;
import com.general.files.InternetConnection;
import com.general.files.StartActProcess;
import com.google.android.gms.maps.model.LatLng;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class SearchLocationActivity extends AppCompatActivity implements PlacesAdapter.setRecentLocClickList, GetAddressFromLocation.AddressFound,GetLocationUpdates.LocationUpdates  {


    MTextView titleTxt;
    ImageView backImgView;
    GeneralFunctions generalFunc;
    LinearLayout mapLocArea, sourceLocationView, destLocationView;
    RecentLocationAdpater recentLocationAdpater;
    ArrayList<HashMap<String, String>> recentLocList = new ArrayList<>();

    JSONArray SourceLocations_arr;
    JSONArray DestinationLocations_arr;
    MTextView placesTxt, recentLocHTxtView;
    ScrollView recentScrollView;
    String userProfileJson = "";
    String whichLocation = "";
    MTextView mapLocTxt, homePlaceTxt, homePlaceHTxt;
    MTextView workPlaceTxt, workPlaceHTxt;
    LinearLayout homeLocArea, workLocArea;

    ImageView homeActionImgView, workActionImgView;

    MTextView cancelTxt;

    RecyclerView placesRecyclerView;
    EditText searchTxt;

    // MainActivity mainAct;
    ArrayList<HashMap<String, String>> placelist;
    PlacesAdapter placesAdapter;
    ImageView imageCancel;

    MTextView noPlacedata;
    InternetConnection intCheck;


    boolean isaddressview = false;

    LinearLayout placearea;
    LinearLayout myLocationarea;
    LinearLayout destinationLaterArea;
    MTextView destHTxtView;

    GetAddressFromLocation getAddressFromLocation;
    GetLocationUpdates getLastLocation;

    double currentLat = 0.0;
    double currentLong = 0.0;

    MTextView mylocHTxtView;

    LinearLayout placesarea;

    private boolean isFirstLocation = true;
    boolean isDriverAssigned;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_search_location);

        generalFunc = new GeneralFunctions(getActContext());
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        isDriverAssigned = getIntent().getBooleanExtra("isDriverAssigned", false);

        intCheck = new InternetConnection(getActContext());

        getLastLocation = new GetLocationUpdates(getActContext(), Utils.LOCATION_UPDATE_MIN_DISTANCE_IN_MITERS, false,this);

        sourceLocationView = (LinearLayout) findViewById(R.id.sourceLocationView);
        placesarea = (LinearLayout) findViewById(R.id.placesarea);

        mapLocArea = (LinearLayout) findViewById(R.id.mapLocArea);
        mapLocArea.setOnClickListener(new setOnClickList());
        mapLocTxt = (MTextView) findViewById(R.id.mapLocTxt);

        destLocationView = (LinearLayout) findViewById(R.id.destLocationView);
        homePlaceTxt = (MTextView) findViewById(R.id.homePlaceTxt);
        homePlaceHTxt = (MTextView) findViewById(R.id.homePlaceHTxt);
        workPlaceTxt = (MTextView) findViewById(R.id.workPlaceTxt);
        workPlaceHTxt = (MTextView) findViewById(R.id.workPlaceHTxt);
        placesTxt = (MTextView) findViewById(R.id.locPlacesTxt);
        recentLocHTxtView = (MTextView) findViewById(R.id.recentLocHTxtView);
        cancelTxt = (MTextView) findViewById(R.id.cancelTxt);

        cancelTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));
        placesarea = (LinearLayout) findViewById(R.id.placesarea);
        placesRecyclerView = (RecyclerView) findViewById(R.id.placesRecyclerView);
        searchTxt = (EditText) findViewById(R.id.searchTxt);
        cancelTxt.setOnClickListener(new setOnClickList());
        imageCancel = (ImageView) findViewById(R.id.imageCancel);
        noPlacedata = (MTextView) findViewById(R.id.noPlacedata);
        imageCancel.setOnClickListener(new setOnClickList());
        myLocationarea = (LinearLayout) findViewById(R.id.myLocationarea);
        mylocHTxtView = (MTextView) findViewById(R.id.mylocHTxtView);

        homeActionImgView = (ImageView) findViewById(R.id.homeActionImgView);
        workActionImgView = (ImageView) findViewById(R.id.workActionImgView);
        homeLocArea = (LinearLayout) findViewById(R.id.homeLocArea);
        workLocArea = (LinearLayout) findViewById(R.id.workLocArea);
        placearea = (LinearLayout) findViewById(R.id.placearea);
        destinationLaterArea = (LinearLayout) findViewById(R.id.destinationLaterArea);
        destHTxtView = (MTextView) findViewById(R.id.destHTxtView);

        homeLocArea.setOnClickListener(new setOnClickList());
        workLocArea.setOnClickListener(new setOnClickList());
        placesTxt.setOnClickListener(new setOnClickList());

        destinationLaterArea.setOnClickListener(new setOnClickList());
        workActionImgView.setOnClickListener(new setOnClickList());
        homeActionImgView.setOnClickListener(new setOnClickList());
        myLocationarea.setOnClickListener(new setOnClickList());

        placelist = new ArrayList<>();

        setLabel();
        setWhichLocationAreaSelected(getIntent().getStringExtra("locationArea"));

        if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
            placearea.setVisibility(View.GONE);
            myLocationarea.setVisibility(View.VISIBLE);

        }

        if (getIntent().getStringExtra("locationArea").equalsIgnoreCase("dest")) {
            if ((getIntent().getStringExtra("type")!=null && getIntent().getStringExtra("type").equalsIgnoreCase(Utils.CabGeneralType_Ride) && generalFunc.getJsonValue("APP_DESTINATION_MODE", userProfileJson).equalsIgnoreCase("NONSTRICT") && !isDriverAssigned)) {

                destinationLaterArea.setVisibility(View.VISIBLE);


            }
        }

        searchTxt.setOnFocusChangeListener(new View.OnFocusChangeListener() {
            @Override
            public void onFocusChange(View v, boolean hasFocus) {

                if (!hasFocus) {
                    hideSoftKeyboard(searchTxt);
                } else {
                    showSoftKeyboard(searchTxt);
                }
            }
        });

        searchTxt.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {

            }

            @Override
            public void afterTextChanged(Editable s) {


                Utils.printLog("StringLength", "=>" + s.length());
                if (s.length() >= 2) {
                    placesRecyclerView.setVisibility(View.VISIBLE);
                    placesarea.setVisibility(View.GONE);
                    getGooglePlaces(searchTxt.getText().toString());
                } else {
                    placesarea.setVisibility(View.VISIBLE);
                    placesRecyclerView.setVisibility(View.GONE);
                    noPlacedata.setVisibility(View.GONE);
                }

            }
        });

        if (getIntent().hasExtra("hideSetMapLoc")) {
            mapLocArea.setVisibility(View.GONE);
            placesarea.setVisibility(View.GONE);
        } else {
            mapLocArea.setVisibility(View.VISIBLE);
        }
        placesRecyclerView.setHasFixedSize(true);
        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(getApplicationContext());
        placesRecyclerView.setLayoutManager(mLayoutManager);
        placesRecyclerView.addItemDecoration(new DividerItemDecoration(this, LinearLayoutManager.VERTICAL));
        placesRecyclerView.setItemAnimator(new DefaultItemAnimator());


    }


    public void showSoftKeyboard(EditText view) {
        if (view.requestFocus()) {
            this.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_VISIBLE);
            InputMethodManager imm = (InputMethodManager)
                    this.getSystemService(getActContext().INPUT_METHOD_SERVICE);
            imm.toggleSoftInput(InputMethodManager.SHOW_FORCED, InputMethodManager.SHOW_IMPLICIT);
        }
    }


    public void hideSoftKeyboard(View view) {
        this.getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_HIDDEN);
        InputMethodManager imm = (InputMethodManager) this.getSystemService(getActContext().INPUT_METHOD_SERVICE);
        imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
    }


    void setLabel() {


        homePlaceTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_HOME_PLACE_TXT"));
        workPlaceTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_WORK_PLACE_TXT"));
        homePlaceHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOME_PLACE"));
        workPlaceHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_WORK_PLACE"));
        mapLocTxt.setText(generalFunc.retrieveLangLBl("Set location on map", "LBL_SET_LOC_ON_MAP"));

        placesTxt.setText(generalFunc.retrieveLangLBl("", "LBL_PLACE_RECENT_FAV"));
        recentLocHTxtView.setText(generalFunc.retrieveLangLBl("", "LBL_RECENT_LOCATIONS"));


        mylocHTxtView.setText(generalFunc.retrieveLangLBl("I want services at my current location", "LBL_SERVICE_MY_LOCATION_HINT_INFO"));
        destHTxtView.setText(generalFunc.retrieveLangLBl("Enter destination later", "LBL_DEST_ADD_LATER"));

    }

    @Override
    public void itemRecentLocClick(int position) {

        getSelectAddresLatLong(placelist.get(position).get("place_id"), placelist.get(position).get("description"));

    }

    @Override
    public void onAddressFound(String address, double latitude, double longitude, String geocodeobject) {


        Bundle bn = new Bundle();
        bn.putString("Address", address);
        bn.putString("Latitude", "" + latitude);
        bn.putString("Longitude", "" + longitude);
        bn.putBoolean("isSkip", false);

        new StartActProcess(getActContext()).setOkResult(bn);

        finish();

    }


    public void setWhichLocationAreaSelected(String locationArea) {
        this.whichLocation = locationArea;

        if (locationArea.equals("dest")) {
            destLocationView.setVisibility(View.VISIBLE);
            sourceLocationView.setVisibility(View.GONE);
            getRecentLocations("dest");
            checkPlaces(locationArea);

        } else if (locationArea.equals("source")) {
            destLocationView.setVisibility(View.GONE);
            sourceLocationView.setVisibility(View.VISIBLE);
            getRecentLocations("source");
            checkPlaces(locationArea);
        }


    }

    public void checkPlaces(final String whichLocationArea) {

        final SharedPreferences mpref_place = PreferenceManager.getDefaultSharedPreferences(getActContext());

        final String home_address_str = mpref_place.getString("userHomeLocationAddress", null);
        String work_address_str = mpref_place.getString("userWorkLocationAddress", null);

        if (home_address_str != null) {

            homePlaceTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOME_PLACE"));
            homePlaceHTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 16);
            homePlaceTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14);
            homePlaceTxt.setTextColor(getResources().getColor(R.color.gray));
            homePlaceHTxt.setText("" + home_address_str);
            homePlaceHTxt.setVisibility(View.VISIBLE);
            homePlaceHTxt.setTextColor(getResources().getColor(R.color.black));
            homeActionImgView.setImageResource(R.mipmap.ic_edit);

        } else {
            homePlaceHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOME_PLACE"));
            homePlaceHTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14);
            homePlaceTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 16);
            homePlaceTxt.setText("" + generalFunc.retrieveLangLBl("", "LBL_ADD_HOME_PLACE_TXT"));
            homePlaceTxt.setTextColor(getResources().getColor(R.color.gray));
            homeActionImgView.setImageResource(R.mipmap.ic_pluse);
        }

        if (work_address_str != null) {

            workPlaceTxt.setText(generalFunc.retrieveLangLBl("", "LBL_WORK_PLACE"));
            workPlaceHTxt.setText("" + work_address_str);
            workPlaceHTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 16);
            workPlaceTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14);
            workPlaceTxt.setTextColor(getResources().getColor(R.color.gray));
            workPlaceHTxt.setVisibility(View.VISIBLE);
//            workPlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_edit, null);
            workPlaceHTxt.setTextColor(getResources().getColor(R.color.black));
            workActionImgView.setImageResource(R.mipmap.ic_edit);

        } else {
            workPlaceHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_WORK_PLACE"));
            workPlaceHTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14);
            workPlaceTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 16);
            workPlaceTxt.setText("" + generalFunc.retrieveLangLBl("", "LBL_ADD_WORK_PLACE_TXT"));
            workPlaceTxt.setTextColor(getResources().getColor(R.color.gray));
            workActionImgView.setImageResource(R.mipmap.ic_pluse);
        }
    }

    private void getRecentLocations(final String whichView) {
        final LayoutInflater mInflater = (LayoutInflater)
                getActContext().getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

        DestinationLocations_arr = generalFunc.getJsonArray("DestinationLocations", userProfileJson);
        SourceLocations_arr = generalFunc.getJsonArray("SourceLocations", userProfileJson);

        if (DestinationLocations_arr != null || SourceLocations_arr != null) {

            if (whichView.equals("dest")) {

                if (destLocationView != null) {
                    destLocationView.removeAllViews();
                    recentLocList.clear();
                }
                for (int i = 0; i < DestinationLocations_arr.length(); i++) {
                    final View view = mInflater.inflate(R.layout.item_recent_loc_design, null);
                    JSONObject destLoc_obj = generalFunc.getJsonObject(DestinationLocations_arr, i);

                    MTextView recentAddrTxtView = (MTextView) view.findViewById(R.id.recentAddrTxtView);
                    LinearLayout recentAdapterView = (LinearLayout) view.findViewById(R.id.recentAdapterView);

                    final String tEndLat = generalFunc.getJsonValue("tEndLat", destLoc_obj.toString());
                    final String tEndLong = generalFunc.getJsonValue("tEndLong", destLoc_obj.toString());
                    final String tDaddress = generalFunc.getJsonValue("tDaddress", destLoc_obj.toString());

                    recentAddrTxtView.setText(tDaddress);

                    HashMap<String, String> map = new HashMap<>();
                    map.put("tLat", tEndLat);
                    map.put("tLong", tEndLong);
                    map.put("taddress", tDaddress);

                    recentLocList.add(map);
                    recentAdapterView.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View view) {
                            if (whichView != null) {
                                if (whichView.equals("dest")) {

                                    Bundle bn = new Bundle();
                                    bn.putString("Address", tDaddress);
                                    bn.putString("Latitude", "" + tEndLat);
                                    bn.putString("Longitude", "" + tEndLong);
                                    bn.putBoolean("isSkip", false);
                                    new StartActProcess(getActContext()).setOkResult(bn);

                                    finish();
                                }

                            } else {

                            }
                        }
                    });
                    destLocationView.addView(view);
                }
            } else {
                if (sourceLocationView != null) {
                    sourceLocationView.removeAllViews();
                    recentLocList.clear();
                }
                for (int i = 0; i < SourceLocations_arr.length(); i++) {

                    final View view = mInflater.inflate(R.layout.item_recent_loc_design, null);
                    JSONObject loc_obj = generalFunc.getJsonObject(SourceLocations_arr, i);

                    MTextView recentAddrTxtView = (MTextView) view.findViewById(R.id.recentAddrTxtView);
                    LinearLayout recentAdapterView = (LinearLayout) view.findViewById(R.id.recentAdapterView);

                    final String tStartLat = generalFunc.getJsonValue("tStartLat", loc_obj.toString());
                    final String tStartLong = generalFunc.getJsonValue("tStartLong", loc_obj.toString());
                    final String tSaddress = generalFunc.getJsonValue("tSaddress", loc_obj.toString());

                    recentAddrTxtView.setText(tSaddress);
                    HashMap<String, String> map = new HashMap<>();
                    map.put("tLat", tStartLat);
                    map.put("tLong", tStartLong);
                    map.put("taddress", tSaddress);

                    recentLocList.add(map);
                    recentAdapterView.setOnClickListener(new View.OnClickListener() {
                        @Override
                        public void onClick(View view) {
                            if (whichView != null) {
                                if (whichView.equals("source")) {

                                    Bundle bn = new Bundle();
                                    bn.putString("Address", tSaddress);
                                    bn.putString("Latitude", "" + tStartLat);
                                    bn.putString("Longitude", "" + tStartLong);

                                    new StartActProcess(getActContext()).setOkResult(bn);

                                    finish();

                                }


                            } else {

                            }
                        }
                    });
                    sourceLocationView.addView(view);
                }
            }

        } else {
            destLocationView.setVisibility(View.GONE);
            sourceLocationView.setVisibility(View.GONE);
            recentLocHTxtView.setVisibility(View.GONE);
        }
    }

    public Context getActContext() {
        return SearchLocationActivity.this;
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(getActContext());

        if (requestCode == Utils.ADD_HOME_LOC_REQ_CODE && resultCode == RESULT_OK && data != null) {
            SharedPreferences.Editor editor = mPrefs.edit();
            editor.putString("userHomeLocationLatitude", "" + data.getStringExtra("Latitude"));
            editor.putString("userHomeLocationLongitude", "" + data.getStringExtra("Longitude"));
            editor.putString("userHomeLocationAddress", "" + data.getStringExtra("Address"));

            editor.commit();
            homePlaceTxt.setText(data.getStringExtra("Address"));
            checkPlaces(whichLocation);


            Bundle bn = new Bundle();
            bn.putString("Latitude", data.getStringExtra("Latitude"));
            bn.putString("Longitude", "" + data.getStringExtra("Longitude"));
            bn.putString("Address", "" + data.getStringExtra("Address"));

            bn.putBoolean("isSkip", false);
            new StartActProcess(getActContext()).setOkResult(bn);
            finish();

        } else if (requestCode == Utils.ADD_MAP_LOC_REQ_CODE && resultCode == RESULT_OK && data != null) {

            Bundle bn = new Bundle();
            bn.putString("Latitude", data.getStringExtra("Latitude"));
            bn.putString("Longitude", "" + data.getStringExtra("Longitude"));
            bn.putString("Address", "" + data.getStringExtra("Address"));
            bn.putBoolean("isSkip", false);
            new StartActProcess(getActContext()).setOkResult(bn);
            finish();

        } else if (requestCode == Utils.ADD_WORK_LOC_REQ_CODE && resultCode == RESULT_OK && data != null) {
            SharedPreferences.Editor editor = mPrefs.edit();
            editor.putString("userWorkLocationLatitude", "" + data.getStringExtra("Latitude"));
            editor.putString("userWorkLocationLongitude", "" + data.getStringExtra("Longitude"));
            editor.putString("userWorkLocationAddress", "" + data.getStringExtra("Address"));

            editor.commit();
            workPlaceTxt.setText(data.getStringExtra("Address"));
            checkPlaces(whichLocation);


            Bundle bn = new Bundle();
            bn.putString("Latitude", data.getStringExtra("Latitude"));
            bn.putString("Longitude", "" + data.getStringExtra("Longitude"));
            bn.putString("Address", "" + data.getStringExtra("Address"));
            bn.putBoolean("isSkip", false);
            new StartActProcess(getActContext()).setOkResult(bn);
            finish();


        }


    }

    public void getGooglePlaces(String input) {

        noPlacedata.setVisibility(View.GONE);

        String serverKey = getResources().getString(R.string.google_api_get_address_from_location_serverApi);

        String url = null;
        //   URLEncoder.encode(input.replace(" ", "%20"), "UTF-8")
        try {


            url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=" + input.replace(" ", "%20") + "&key=" + serverKey +
                    "&language=" + generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY) + "&sensor=true";

            Utils.printLog("SearchLatlong", "::" + getIntent().getDoubleExtra("long", 0.0));
            Utils.printLog("SearchLatlat", "::" + getIntent().getDoubleExtra("lat", 0.0));
            if (getIntent().getDoubleExtra("long", 0.0) != 0.0) {


                url = url + "&location=" + getIntent().getDoubleExtra("lat", 0.0) + "," + getIntent().getDoubleExtra("long", 0.0) + "&radius=20";

            }


        } catch (Exception e) {
            e.printStackTrace();
        }


        if (url == null) {
            return;
        }
        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), url, true);

        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (generalFunc.getJsonValue("status", responseString).equals("OK")) {
                    JSONArray predictionsArr = generalFunc.getJsonArray("predictions", responseString);

                    if (searchTxt.getText().toString().length() == 0) {
                        placesRecyclerView.setVisibility(View.GONE);
                        noPlacedata.setVisibility(View.GONE);
                        placesarea.setVisibility(View.VISIBLE);
                        return;
                    }

                    placelist.clear();
                    for (int i = 0; i < predictionsArr.length(); i++) {
                        JSONObject item = generalFunc.getJsonObject(predictionsArr, i);

                        if (!generalFunc.getJsonValue("place_id", item.toString()).equals("")) {

                            HashMap<String, String> map = new HashMap<String, String>();

                            String structured_formatting = generalFunc.getJsonValue("structured_formatting", item.toString());
                            map.put("main_text", generalFunc.getJsonValue("main_text", structured_formatting.toString()));
                            map.put("secondary_text", generalFunc.getJsonValue("secondary_text", structured_formatting.toString()));
                            map.put("place_id", generalFunc.getJsonValue("place_id", item.toString()));
                            map.put("description", generalFunc.getJsonValue("description", item.toString()));

                            placelist.add(map);

                        }
                    }
                    if (placelist.size() > 0) {
                        placesarea.setVisibility(View.GONE);
                        placesRecyclerView.setVisibility(View.VISIBLE);
                        noPlacedata.setVisibility(View.GONE);

                        if (placesAdapter == null) {
                            placesAdapter = new PlacesAdapter(getActContext(), placelist);
                            placesRecyclerView.setAdapter(placesAdapter);
                            placesAdapter.itemRecentLocClick(SearchLocationActivity.this);

                        } else {
                            placesAdapter.notifyDataSetChanged();
                        }
                    }
                } else if (generalFunc.getJsonValue("status", responseString).equals("ZERO_RESULTS")) {
                    placelist.clear();
                    if (placesAdapter != null) {
                        placesAdapter.notifyDataSetChanged();
                    }

                    String msg = generalFunc.retrieveLangLBl("We didn't find any places matched to your entered place. Please try again with another text.", "LBL_NO_PLACES_FOUND");
                    noPlacedata.setText(msg);
                    placesarea.setVisibility(View.GONE);
                    placesRecyclerView.setVisibility(View.GONE);
                    noPlacedata.setVisibility(View.VISIBLE);


                } else {
                    placelist.clear();
                    if (placesAdapter != null) {
                        placesAdapter.notifyDataSetChanged();
                    }
                    String msg = "";
                    if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {
                        msg = generalFunc.retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT");

                    } else {
                        msg = generalFunc.retrieveLangLBl("Error occurred while searching nearest places. Please try again later.", "LBL_PLACE_SEARCH_ERROR");

                    }

                    noPlacedata.setText(msg);
                    placesarea.setVisibility(View.GONE);
                    placesRecyclerView.setVisibility(View.GONE);
                    noPlacedata.setVisibility(View.VISIBLE);

                }

            }
        });
        exeWebServer.execute();
    }

    public void getSelectAddresLatLong(String Place_id, final String address) {
        String serverKey = getResources().getString(R.string.google_api_get_address_from_location_serverApi);


        String url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=" + Place_id + "&key=" + serverKey +
                "&language=" + generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY) + "&sensor=true";

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), url, true);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (generalFunc.getJsonValue("status", responseString).equals("OK")) {
                    String resultObj = generalFunc.getJsonValue("result", responseString);
                    String geometryObj = generalFunc.getJsonValue("geometry", resultObj);
                    String locationObj = generalFunc.getJsonValue("location", geometryObj);
                    String latitude = generalFunc.getJsonValue("lat", locationObj);
                    String longitude = generalFunc.getJsonValue("lng", locationObj);

                    Bundle bn = new Bundle();
                    bn.putString("Address", address);
                    bn.putString("Latitude", "" + latitude);
                    bn.putString("Longitude", "" + longitude);
                    bn.putBoolean("isSkip", false);
                    new StartActProcess(getActContext()).setOkResult(bn);
                    finish();


                }

            }
        });
        exeWebServer.execute();

    }

    @Override
    public void onLocationUpdate(Location mLastLocation) {
        if (isFirstLocation) {
            isFirstLocation = false;
            currentLat = mLastLocation.getLatitude();
            currentLong = mLastLocation.getLongitude();
        }

    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();

            Bundle bndl = new Bundle();

            if (i == R.id.cancelTxt) {
                finish();

            } else if (i == R.id.mapLocArea) {
                bndl.putString("locationArea", getIntent().getStringExtra("locationArea"));
                String from = !whichLocation.equals("dest") ? "isPickUpLoc" : "isDestLoc";
                String lati = !whichLocation.equals("dest") ? "PickUpLatitude" : "DestLatitude";
                String longi = !whichLocation.equals("dest") ? "PickUpLongitude" : "DestLongitude";
                String address = !whichLocation.equals("dest") ? "PickUpAddress" : "DestAddress";


                bndl.putString(from, "true");
                if (getIntent().getDoubleExtra("lat", 0.0) != 0.0 && getIntent().getDoubleExtra("long", 0.0) != 0.0) {
                    bndl.putString(lati, "" + getIntent().getDoubleExtra("lat", 0.0));
                    bndl.putString(longi, "" + getIntent().getDoubleExtra("long", 0.0));
                    if (!getIntent().getStringExtra("address").equalsIgnoreCase("")) {
                        bndl.putString(address, "" + getIntent().getStringExtra("address"));
                    } else {
                        bndl.putString(address, "");
                    }

                }


                new StartActProcess(getActContext()).startActForResult(SearchPickupLocationActivity.class,
                        bndl, Utils.ADD_MAP_LOC_REQ_CODE);


            } else if (i == R.id.locPlacesTxt) {

                //  bottomSheet.performClick();
            } else if (i == R.id.homeLocArea) {
                final SharedPreferences mpref_place = PreferenceManager.getDefaultSharedPreferences(getActContext());

                if (mpref_place != null) {

                    final String home_address_str = mpref_place.getString("userHomeLocationAddress", null);
                    final String home_addr_latitude = mpref_place.getString("userHomeLocationLatitude", null);
                    final String home_addr_longitude = mpref_place.getString("userHomeLocationLongitude", null);

                    if (home_address_str != null) {

                        if (whichLocation.equals("dest")) {


                            LatLng placeLocation = new LatLng(generalFunc.parseDoubleValue(0.0, home_addr_latitude), generalFunc.parseDoubleValue(0.0, home_addr_longitude));


                            Bundle bn = new Bundle();
                            bn.putString("Address", home_address_str);
                            bn.putString("Latitude", "" + placeLocation.latitude);
                            bn.putString("Longitude", "" + placeLocation.longitude);

                            bn.putBoolean("isSkip", false);
                            new StartActProcess(getActContext()).setOkResult(bn);
                            finish();
                        } else {

                            LatLng placeLocation = new LatLng(generalFunc.parseDoubleValue(0.0, home_addr_latitude), generalFunc.parseDoubleValue(0.0, home_addr_longitude));

                            Bundle bn = new Bundle();
                            bn.putString("Address", home_address_str);
                            bn.putString("Latitude", "" + placeLocation.latitude);
                            bn.putString("Longitude", "" + placeLocation.longitude);
                            bn.putBoolean("isSkip", false);
                            new StartActProcess(getActContext()).setOkResult(bn);
                            finish();
                        }
                    } else {
                        bndl.putString("isHome", "true");
                        new StartActProcess(getActContext()).startActForResult(SearchPickupLocationActivity.class,
                                bndl, Utils.ADD_HOME_LOC_REQ_CODE);
                    }
                } else {
                    bndl.putString("isHome", "true");
                    new StartActProcess(getActContext()).startActForResult(SearchPickupLocationActivity.class,
                            bndl, Utils.ADD_HOME_LOC_REQ_CODE);
                }

            } else if (i == R.id.workLocArea) {
                final SharedPreferences mpref_place = PreferenceManager.getDefaultSharedPreferences(getActContext());

                if (mpref_place != null) {

                    String work_address_str = mpref_place.getString("userWorkLocationAddress", null);

                    String work_addr_latitude = mpref_place.getString("userWorkLocationLatitude", null);
                    String work_addr_longitude = mpref_place.getString("userWorkLocationLongitude", null);

                    if (work_address_str != null) {

                        if (whichLocation.equals("dest")) {

                            LatLng placeLocation = new LatLng(generalFunc.parseDoubleValue(0.0, work_addr_latitude), generalFunc.parseDoubleValue(0.0, work_addr_longitude));

                            Bundle bn = new Bundle();
                            bn.putString("Address", work_address_str);
                            bn.putString("Latitude", "" + placeLocation.latitude);
                            bn.putString("Longitude", "" + placeLocation.longitude);
                            bn.putBoolean("isSkip", false);
                            new StartActProcess(getActContext()).setOkResult(bn);
                            finish();
                        } else {


                            LatLng placeLocation = new LatLng(generalFunc.parseDoubleValue(0.0, work_addr_latitude), generalFunc.parseDoubleValue(0.0, work_addr_longitude));

                            Bundle bn = new Bundle();
                            bn.putString("Address", work_address_str);
                            bn.putString("Latitude", "" + placeLocation.latitude);
                            bn.putString("Longitude", "" + placeLocation.longitude);
                            bn.putBoolean("isSkip", false);
                            new StartActProcess(getActContext()).setOkResult(bn);
                            finish();
                        }
                    } else {

                        bndl.putString("isWork", "true");
                        new StartActProcess(getActContext()).startActForResult(SearchPickupLocationActivity.class,
                                bndl, Utils.ADD_WORK_LOC_REQ_CODE);
                    }
                } else {
                    bndl.putString("isWork", "true");
                    new StartActProcess(getActContext()).startActForResult(SearchPickupLocationActivity.class,
                            bndl, Utils.ADD_WORK_LOC_REQ_CODE);
                }
            } else if (i == R.id.homeActionImgView) {
                Bundle bn = new Bundle();
                bn.putString("isHome", "true");
                new StartActProcess(getActContext()).startActForResult(SearchPickupLocationActivity.class,
                        bn, Utils.ADD_HOME_LOC_REQ_CODE);
            } else if (i == R.id.workActionImgView) {
                Bundle bn = new Bundle();
                bn.putString("isWork", "true");
                new StartActProcess(getActContext()).startActForResult(SearchPickupLocationActivity.class,
                        bn, Utils.ADD_WORK_LOC_REQ_CODE);
            } else if (i == R.id.imageCancel) {
                placesRecyclerView.setVisibility(View.GONE);
                placesarea.setVisibility(View.VISIBLE);
                searchTxt.setText("");
                noPlacedata.setVisibility(View.GONE);
            } else if (i == R.id.myLocationarea) {

                if (generalFunc.isLocationEnabled()) {
                    if (currentLat == 0.0 || currentLong == 0.0) {
                        return;
                    }

                    getAddressFromLocation = new GetAddressFromLocation(getActContext(), generalFunc);
                    getAddressFromLocation.setLoaderEnable(true);
                    getAddressFromLocation.setLocation(currentLat, currentLong);
                    getAddressFromLocation.setAddressList(SearchLocationActivity.this);
                    getAddressFromLocation.execute();


                } else {
                    //GPS Dialog
                }
            } else if (i == R.id.destinationLaterArea) {

                Bundle bn = new Bundle();
                bn.putBoolean("isSkip", true);
                new StartActProcess(getActContext()).setOkResult(bn);
                finish();

            }

        }
    }
}
