package com.fastcabtaxi.driver;

import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TableLayout;
import android.widget.TableRow;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;

public class FareBreakDownActivity extends AppCompatActivity {


    MTextView titleTxt;
    ImageView backImgView;
    GeneralFunctions generalFunc;
    MTextView fareBreakdownNoteTxt;
    MTextView carTypeTitle;
    LinearLayout fareDetailDisplayArea;
    View convertView = null;

    String selectedcar = "";
    String iUserId = "";
    String distance = "";
    String time = "";
    String vVehicleType = "";
    boolean isFixFare;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_fare_break_down);

        generalFunc = new GeneralFunctions(getActContext());
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        fareBreakdownNoteTxt = (MTextView) findViewById(R.id.fareBreakdownNoteTxt);
        carTypeTitle = (MTextView) findViewById(R.id.carTypeTitle);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        backImgView.setOnClickListener(new setOnClickAct());
        fareDetailDisplayArea = (LinearLayout) findViewById(R.id.fareDetailDisplayArea);
        isFixFare = getIntent().getBooleanExtra("isFixFare", false);
        selectedcar = getIntent().getStringExtra("SelectedCar");
        iUserId = getIntent().getStringExtra("iUserId");
        distance = getIntent().getStringExtra("distance");
        time = getIntent().getStringExtra("time");
        vVehicleType = getIntent().getStringExtra("vVehicleType");
        setLabels();
        callBreakdownRequest();
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_FARE_BREAKDOWN_TXT"));
        if (isFixFare) {
            fareBreakdownNoteTxt.setText(generalFunc.retrieveLangLBl("", "LBL_GENERAL_NOTE_FLAT_FARE_EST"));
        } else {
            fareBreakdownNoteTxt.setText(generalFunc.retrieveLangLBl("", "LBL_GENERAL_NOTE_FARE_EST"));

        }

        carTypeTitle.setText(vVehicleType);


    }

    public Context getActContext() {
        return FareBreakDownActivity.this;
    }


    public class setOnClickAct implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(FareBreakDownActivity.this);
            switch (view.getId()) {

                case R.id.backImgView:
                    FareBreakDownActivity.super.onBackPressed();
                    break;

            }
        }
    }

    public void callBreakdownRequest() {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getEstimateFareDetailsArr");
        parameters.put("iUserId", generalFunc.getMemberId());
        if (!distance.equals("")) {
            parameters.put("distance", distance);
        }
        if (!time.equals("")) {
            parameters.put("time", time);
        }
        parameters.put("SelectedCar", selectedcar);
        parameters.put("UserType", Utils.userType);
        parameters.put("isDestinationAdded", getIntent().getStringExtra("isDestinationAdded"));

        if (getIntent().getStringExtra("destLat") != null && !getIntent().getStringExtra("destLat").equalsIgnoreCase("")) {
            parameters.put("DestLatitude", getIntent().getStringExtra("destLat"));
            parameters.put("DestLongitude", getIntent().getStringExtra("destLong"));
        }
        if (getIntent().getStringExtra("picupLat") != null && !getIntent().getStringExtra("picupLat").equalsIgnoreCase("")) {
            parameters.put("StartLatitude", getIntent().getStringExtra("picupLat"));
            parameters.put("EndLongitude", getIntent().getStringExtra("pickUpLong"));
        }


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {


                        JSONArray FareDetailsArrNewObj = null;
                        FareDetailsArrNewObj = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);
                        addFareDetailLayout(FareDetailsArrNewObj);


                    } else {

                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();

    }

    private void addFareDetailLayout(JSONArray jobjArray) {

        if (fareDetailDisplayArea.getChildCount() > 0) {
            fareDetailDisplayArea.removeAllViewsInLayout();
        }

        for (int i = 0; i < jobjArray.length(); i++) {
            JSONObject jobject = generalFunc.getJsonObject(jobjArray, i);
            try {
                addFareDetailRow(jobject.names().getString(0), jobject.get(jobject.names().getString(0)).toString(), (jobjArray.length() - 1) == i ? true : false);
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }

    }

    private void addFareDetailRow(String row_name, String row_value, boolean isLast) {
        LayoutInflater infalInflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = infalInflater.inflate(R.layout.design_fare_breakdown_row, null);
        TableRow FareDetailRow = (TableRow) convertView.findViewById(R.id.FareDetailRow);
        TableLayout fair_area_table_layout = (TableLayout) convertView.findViewById(R.id.fair_area);
        MTextView titleHTxt = (MTextView) convertView.findViewById(R.id.titleHTxt);
        MTextView titleVTxt = (MTextView) convertView.findViewById(R.id.titleVTxt);

        titleHTxt.setText(generalFunc.convertNumberWithRTL(row_name));
        titleVTxt.setText(generalFunc.convertNumberWithRTL(row_value));

        if (isLast == true) {
            TableLayout.LayoutParams tableRowParams =
                    new TableLayout.LayoutParams
                            (TableLayout.LayoutParams.FILL_PARENT, TableLayout.LayoutParams.FILL_PARENT);
            tableRowParams.setMargins(0, 10, 0, 0);

            fair_area_table_layout.setLayoutParams(tableRowParams);
            FareDetailRow.setLayoutParams(tableRowParams);
            fair_area_table_layout.setBackgroundColor(Color.parseColor("#EBEBEB"));
            fair_area_table_layout.getChildAt(0).setPadding(5, 0, 5, 0);
        } else {
            titleHTxt.setTextColor(Color.parseColor("#303030"));
            titleVTxt.setTextColor(Color.parseColor("#111111"));
        }
        if (convertView != null)
            fareDetailDisplayArea.addView(convertView);
    }
}
