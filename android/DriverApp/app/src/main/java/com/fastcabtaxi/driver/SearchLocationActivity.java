package com.fastcabtaxi.driver;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.ImageView;

import com.adapter.files.PlacesAdapter;
import com.general.files.DividerItemDecoration;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.InternetConnection;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class SearchLocationActivity extends AppCompatActivity implements PlacesAdapter.setRecentLocClickList {


    public boolean isAddressEnable;
    MTextView titleTxt;
    ImageView backImgView;
    GeneralFunctions generalFunc;
    String userProfileJson = "";
    String whichLocation = "";
    MTextView cancelTxt;
    RecyclerView placesRecyclerView;
    EditText searchTxt;
    ArrayList<HashMap<String, String>> placelist;
    PlacesAdapter placesAdapter;
    ImageView imageCancel;
    MTextView noPlacedata;
    InternetConnection intCheck;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_search_location);

        generalFunc = new GeneralFunctions(getActContext());
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

        intCheck = new InternetConnection(getActContext());

        cancelTxt = (MTextView) findViewById(R.id.cancelTxt);
        placesRecyclerView = (RecyclerView) findViewById(R.id.placesRecyclerView);
        searchTxt = (EditText) findViewById(R.id.searchTxt);
        cancelTxt.setOnClickListener(new setOnClickList());
        imageCancel = (ImageView) findViewById(R.id.imageCancel);
        noPlacedata = (MTextView) findViewById(R.id.noPlacedata);
        imageCancel.setOnClickListener(new setOnClickList());

        placelist = new ArrayList<>();

        setWhichLocationAreaSelected(getIntent().getStringExtra("locationArea"));


        searchTxt.setOnFocusChangeListener(new View.OnFocusChangeListener() {
            @Override
            public void onFocusChange(View v, boolean hasFocus) {
                // If it loses focus...
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

                if (s.length() >= 2) {
                    getGooglePlaces(searchTxt.getText().toString());
                }

            }
        });


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


    @Override
    public void itemRecentLocClick(int position) {

        getSelectAddresLatLong(placelist.get(position).get("place_id"), placelist.get(position).get("description"));

    }

    public void setWhichLocationAreaSelected(String locationArea) {
        this.whichLocation = locationArea;
    }

    public Context getActContext() {
        return SearchLocationActivity.this;
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

    }

    public void getGooglePlaces(String input) {

        noPlacedata.setVisibility(View.GONE);

        String serverKey = getResources().getString(R.string.google_api_get_address_from_location_serverApi);

        String url = null;
        try {
            url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=" + input.replace(" ", "%20") + "&key=" + serverKey +
                    "&language=" + generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY) + "&sensor=true";

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
                        placesRecyclerView.setVisibility(View.VISIBLE);

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
                    placesRecyclerView.setVisibility(View.VISIBLE);

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
                    placesRecyclerView.setVisibility(View.VISIBLE);

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

                    new StartActProcess(getActContext()).setOkResult(bn);

                    finish();


                }

            }
        });
        exeWebServer.execute();

    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            if (i == R.id.cancelTxt) {
                finish();

            } else if (i == R.id.imageCancel) {
                placesRecyclerView.setVisibility(View.GONE);
                searchTxt.setText("");
                noPlacedata.setVisibility(View.GONE);
            }

        }
    }
}
