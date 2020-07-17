package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;

import com.adapter.files.DriverFeedbackRecycleAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class DriverFeedbackActivity extends AppCompatActivity {

    MTextView titleTxt;
    MTextView vAvgRatingTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;

    ProgressBar loading_ride_history;
    MTextView noRidesTxt;

    RecyclerView historyRecyclerView;
    ErrorView errorView;

    DriverFeedbackRecycleAdapter feedbackRecyclerAdapter;

    ArrayList<HashMap<String, String>> list;

    boolean mIsLoading = false;
    boolean isNextPageAvailable = false;

    String next_page_str = "";
    String vAvgRating = "";
    String userProfileJson = "";
    LinearLayout avgRatingArea;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_driver_feedback);

        generalFunc = new GeneralFunctions(getActContext());

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        vAvgRatingTxt = (MTextView) findViewById(R.id.vAvgRatingTxt);

        loading_ride_history = (ProgressBar) findViewById(R.id.loading_ride_history);
        noRidesTxt = (MTextView) findViewById(R.id.noRidesTxt);
        historyRecyclerView = (RecyclerView) findViewById(R.id.historyRecyclerView);
        avgRatingArea = (LinearLayout) findViewById(R.id.avgRatingArea);
        errorView = (ErrorView) findViewById(R.id.errorView);

        //userProfileJson = getIntent().getStringExtra("UserProfileJson");
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

        vAvgRating = generalFunc.getJsonValue("vAvgRating", userProfileJson);
        vAvgRatingTxt.setText(generalFunc.retrieveLangLBl("", "LBL_AVERAGE_RATING_TXT") + " : " + vAvgRating);

        list = new ArrayList<>();
        feedbackRecyclerAdapter = new DriverFeedbackRecycleAdapter(getActContext(), list, generalFunc, false);
        historyRecyclerView.setAdapter(feedbackRecyclerAdapter);
        backImgView.setOnClickListener(new setOnClickList());

        setLabels();

        historyRecyclerView.addOnScrollListener(new RecyclerView.OnScrollListener() {
            @Override
            public void onScrolled(RecyclerView recyclerView, int dx, int dy) {
                super.onScrolled(recyclerView, dx, dy);

                int visibleItemCount = recyclerView.getLayoutManager().getChildCount();
                int totalItemCount = recyclerView.getLayoutManager().getItemCount();
                int firstVisibleItemPosition = ((LinearLayoutManager) recyclerView.getLayoutManager()).findFirstVisibleItemPosition();

                int lastInScreen = firstVisibleItemPosition + visibleItemCount;
                if ((lastInScreen == totalItemCount) && !(mIsLoading) && isNextPageAvailable == true) {
                    mIsLoading = true;
                    feedbackRecyclerAdapter.addFooterView();
                    getFeedback(true);
                }
            }
        });

        getFeedback(false);
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("Rider Feedback", "LBL_RIDER_FEEDBACK"));
    }

    public void getFeedback(final boolean isLoadMore) {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (loading_ride_history.getVisibility() != View.VISIBLE && isLoadMore == false) {
            loading_ride_history.setVisibility(View.VISIBLE);
        }

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "loadDriverFeedBack");
        parameters.put("iDriverId", generalFunc.getMemberId());

        Utils.printLog("next_page_str", ":" + next_page_str);
        if (isLoadMore == true) {
            parameters.put("page", next_page_str);
        }

        noRidesTxt.setVisibility(View.GONE);

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                noRidesTxt.setVisibility(View.GONE);

                if (responseString != null && !responseString.equals("")) {

                    closeLoader();
                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {

                        String nextPage = generalFunc.getJsonValue("NextPage", responseString);
                        vAvgRating = generalFunc.getJsonValue("vAvgRating", responseString);
                        Utils.printLog("vAvgRating", "" + vAvgRating);
                        vAvgRatingTxt.setText(generalFunc.retrieveLangLBl("", "LBL_AVERAGE_RATING_TXT") + " : " + vAvgRating);

                        JSONArray arr_rides = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

                        if (arr_rides != null && arr_rides.length() > 0) {
                            for (int i = 0; i < arr_rides.length(); i++) {
                                JSONObject obj_temp = generalFunc.getJsonObject(arr_rides, i);

                                HashMap<String, String> map = new HashMap<String, String>();

                                map.put("iRatingId", generalFunc.getJsonValue("iRatingId", obj_temp.toString()));
                                map.put("iTripId", generalFunc.getJsonValue("iTripId", obj_temp.toString()));
                                map.put("vRating1", generalFunc.getJsonValue("vRating1", obj_temp.toString()));
                                map.put("tDateOrig", generalFunc.getJsonValue("tDateOrig", obj_temp.toString()));
                                map.put("vMessage", generalFunc.getJsonValue("vMessage", obj_temp.toString()));
                                map.put("vName", generalFunc.getJsonValue("vName", obj_temp.toString()));
                                map.put("vImage", generalFunc.getJsonValue("vImage", obj_temp.toString()));


                                map.put("LBL_READ_MORE", generalFunc.retrieveLangLBl("", "LBL_READ_MORE"));
                                map.put("JSON", obj_temp.toString());

                                list.add(map);

                            }
                        }

                        if (!nextPage.equals("") && !nextPage.equals("0")) {
                            next_page_str = nextPage;
                            isNextPageAvailable = true;
                        } else {
                            removeNextPageConfig();
                        }

                        feedbackRecyclerAdapter.notifyDataSetChanged();
                        if (list.size() > 0)
                            avgRatingArea.setVisibility(View.VISIBLE);

                    } else {
                        if (list.size() == 0) {
                            removeNextPageConfig();
                            noRidesTxt.setText(generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                            noRidesTxt.setVisibility(View.VISIBLE);
                            avgRatingArea.setVisibility(View.GONE);
                        }

                    }
                } else {
                    if (isLoadMore == false) {
                        removeNextPageConfig();
                        generateErrorView();
                    }

                }

                mIsLoading = false;
            }
        });
        exeWebServer.execute();
    }

    public void removeNextPageConfig() {
        next_page_str = "";
        isNextPageAvailable = false;
        mIsLoading = false;
        feedbackRecyclerAdapter.removeFooterView();
    }

    public void closeLoader() {
        if (loading_ride_history.getVisibility() == View.VISIBLE) {
            loading_ride_history.setVisibility(View.GONE);
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
                getFeedback(false);
            }
        });
    }

    public Context getActContext() {
        return DriverFeedbackActivity.this;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(DriverFeedbackActivity.this);
            if (i == R.id.backImgView) {
                DriverFeedbackActivity.super.onBackPressed();
            }
        }
    }
}
