package com.fastcabtaxi.driver;

import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.MTextView;
import com.view.simpleratingbar.SimpleRatingBar;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class SelectedDayHistoryActivity extends AppCompatActivity {

    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;

    ProgressBar loading;
    ErrorView errorView;
    LinearLayout dataContainer;
    LinearLayout listContainer;

    ArrayList<String> list_item;
    MTextView fareHTxt;
    String selecteddate = "";
    MTextView tripsCountTxt;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_selected_day_history);

        generalFunc = new GeneralFunctions(getActContext());

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        errorView = (ErrorView) findViewById(R.id.errorView);
        loading = (ProgressBar) findViewById(R.id.loading);
        dataContainer = (LinearLayout) findViewById(R.id.dataContainer);
        listContainer = (LinearLayout) findViewById(R.id.listContainer);
        fareHTxt = (MTextView) findViewById(R.id.fareHTxt);
        tripsCountTxt = (MTextView) findViewById(R.id.tripsCountTxt);


        backImgView.setOnClickListener(new setOnClickList());

        setLabels();

        try {
            titleTxt.setText(generalFunc.getDateFormatedType(getIntent().getStringExtra("SELECTED_DATE"), Utils.DefaultDatefromate, Utils.dateFormateInHeaderBar));
        } catch (Exception e) {
            e.printStackTrace();
        }
        getDetails();
    }

    public void setLabels() {
        ((MTextView) findViewById(R.id.tripsCompletedTxt)).setText(generalFunc.retrieveLangLBl("Completed Trips", "LBL_COMPLETED_TRIPS"));
        ((MTextView) findViewById(R.id.tripEarningTxt)).setText(generalFunc.retrieveLangLBl("Trip Earning", "LBL_TRIP_EARNING"));
        ((MTextView) findViewById(R.id.avgRatingTxt)).setText(generalFunc.retrieveLangLBl("Avg. Rating", "LBL_AVG_RATING"));
        fareHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_Total_Fare"));
    }

    public Context getActContext() {
        return SelectedDayHistoryActivity.this;
    }

    public void getDetails() {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (dataContainer.getVisibility() == View.VISIBLE) {
            dataContainer.setVisibility(View.GONE);
        }
        if (loading.getVisibility() != View.VISIBLE) {
            loading.setVisibility(View.VISIBLE);
        }

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getDriverRideHistory");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("date", getIntent().getStringExtra("SELECTED_DATE"));

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    closeLoader();

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {
                        setData(true, responseString);
                    } else {
                        setData(false, responseString);
                    }
                } else {
                    generateErrorView();
                }
            }
        });
        exeWebServer.execute();
    }

    public void setData(boolean isDataAvail, String responseString) {

        String currencySymbol = generalFunc.getJsonValue("CurrencySymbol", responseString);

        if (isDataAvail) {
            if (list_item != null) {
                list_item.clear();
                list_item = null;
            }
            list_item = new ArrayList<>();

            JSONArray msgArr = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

            for (int i = 0; i < msgArr.length(); i++) {
                JSONObject obj_temp = generalFunc.getJsonObject(msgArr, i);

                LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                View customView = inflater.inflate(R.layout.selected_day_trip_history_item, null);

                ((MTextView) customView.findViewById(R.id.timeTxt)).setText(generalFunc.getDateFormatedType(generalFunc.getJsonValue("tTripRequestDateOrig", obj_temp.toString()), Utils.OriginalDateFormate, Utils.dateFormateTimeOnly));
                ((MTextView) customView.findViewById(R.id.fareTxt)).setText(currencySymbol + generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("iFare", obj_temp.toString())));

                if (Utils.checkText(generalFunc.getJsonValue("PPetId", obj_temp.toString())) && !generalFunc.getJsonValue("PPetId", obj_temp.toString()).equalsIgnoreCase("0")) {
                    (customView.findViewById(R.id.typeTxt)).setVisibility(View.GONE);
                } else {
                    (customView.findViewById(R.id.typeTxt)).setVisibility(View.VISIBLE);
                }

                if (generalFunc.isRTLmode()) {
                    ((ImageView) customView.findViewById(R.id.arrowImgView)).setRotation(-180);
                }


                if (generalFunc.getJsonValue("eHailTrip", obj_temp.toString()).equalsIgnoreCase("Yes")) {
                    ((MTextView) customView.findViewById(R.id.typeTxt)).setText(generalFunc.retrieveLangLBl("Hail", "LBL_HAIL"));

                } else {
                    if (generalFunc.getJsonValue("eType", obj_temp.toString()).equalsIgnoreCase(Utils.CabGeneralType_Ride)) {
                        ((MTextView) customView.findViewById(R.id.typeTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_RIDE"));
                    } else if (generalFunc.getJsonValue("eType", obj_temp.toString()).equalsIgnoreCase(Utils.CabGeneralType_Deliver)) {
                        ((MTextView) customView.findViewById(R.id.typeTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_DELIVERY"));
                    } else {
                        ((MTextView) customView.findViewById(R.id.typeTxt)).setText(generalFunc.getJsonValue("vVehicleType", obj_temp.toString()));
                    }
                }


                if (Utils.checkText(generalFunc.getJsonValue("PPetId", obj_temp.toString())) && !generalFunc.getJsonValue("PPetId", obj_temp.toString()).equalsIgnoreCase("0")) {
                    (customView.findViewById(R.id.typeTxt)).setVisibility(View.GONE);
                }


                ((ImageView) customView.findViewById(R.id.arrowImgView)).setColorFilter(Color.parseColor("#2F2F2F"));

                (customView.findViewById(R.id.tripItem)).setOnClickListener(new setOnClickList(true, i));
                listContainer.addView(customView);

                list_item.add(obj_temp.toString());

            }
            ((MTextView) findViewById(R.id.tripEarningTxt)).setVisibility(View.VISIBLE);

        } else {
            ((MTextView) findViewById(R.id.noRidesFound)).setText(generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
            (findViewById(R.id.noRidesFound)).setVisibility(View.VISIBLE);
            ((MTextView) findViewById(R.id.tripEarningTxt)).setVisibility(View.GONE);
        }



        tripsCountTxt.setText(generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("TripCount", responseString)));


        ((MTextView) findViewById(R.id.fareTxt)).setText(currencySymbol +
                generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("TotalEarning", responseString)));
        ((SimpleRatingBar) findViewById(R.id.ratingBar)).setRating(generalFunc.parseFloatValue(0, generalFunc.getJsonValue("AvgRating", responseString)));
        ((MTextView) findViewById(R.id.avgRatingCalcTxt)).setText("( " + generalFunc.parseFloatValue(0, generalFunc.getJsonValue("AvgRating", responseString)) + " )");

        dataContainer.setVisibility(View.VISIBLE);


    }

    public void closeLoader() {
        if (loading.getVisibility() == View.VISIBLE) {
            loading.setVisibility(View.GONE);
        }
    }

    public void generateErrorView() {

        closeLoader();

        generalFunc.generateErrorView(errorView, "LBL_ERROR_TXT", "LBL_NO_INTERNET_TXT");

        if (errorView.getVisibility() != View.VISIBLE) {
            errorView.setVisibility(View.VISIBLE);
        }
        errorView.setOnRetryListener(new ErrorView.RetryListener() {
            @Override
            public void onRetry() {
                getDetails();
            }
        });
    }

    public class setOnClickList implements View.OnClickListener {
        boolean isTripItemClick = false;
        int tripItemPosition = 0;

        public setOnClickList() {
        }

        public setOnClickList(boolean isTripItemClick, int tripItemPosition) {
            this.isTripItemClick = isTripItemClick;
            this.tripItemPosition = tripItemPosition;
        }

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(SelectedDayHistoryActivity.this);

            if (isTripItemClick == true) {
                Bundle bn = new Bundle();
                bn.putString("TripData", list_item.get(tripItemPosition));
                new StartActProcess(getActContext()).startActWithData(RideHistoryDetailActivity.class, bn);
            } else {
                int i = view.getId();
                if (i == R.id.backImgView) {
                    SelectedDayHistoryActivity.super.onBackPressed();
                }
            }

        }
    }
}
