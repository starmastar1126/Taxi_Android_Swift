package com.fragments;


import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;

import com.adapter.files.HistoryRecycleAdapter;
import com.fastcabtaxi.passenger.HistoryActivity;
import com.fastcabtaxi.passenger.HistoryDetailActivity;
import com.fastcabtaxi.passenger.R;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * A simple {@link Fragment} subclass.
 */
public class HistoryFragment extends Fragment implements HistoryRecycleAdapter.OnItemClickListener {


    View view;

    ProgressBar loading_ride_history;
    MTextView noRidesTxt;

    RecyclerView historyRecyclerView;
    ErrorView errorView;

    HistoryRecycleAdapter historyRecyclerAdapter;

    ArrayList<HashMap<String, String>> list;

    boolean mIsLoading = false;
    boolean isNextPageAvailable = false;

    String next_page_str = "";

    GeneralFunctions generalFunc;

    HistoryActivity historyAct;

    String userProfileJson = "";

    int HISTORYDETAILS = 1;

    String APP_TYPE;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        view = inflater.inflate(R.layout.fragment_booking, container, false);

        loading_ride_history = (ProgressBar) view.findViewById(R.id.loading_my_bookings);
        noRidesTxt = (MTextView) view.findViewById(R.id.noRidesTxt);
        historyRecyclerView = (RecyclerView) view.findViewById(R.id.myBookingsRecyclerView);
        errorView = (ErrorView) view.findViewById(R.id.errorView);
        historyAct = (HistoryActivity) getActivity();
        generalFunc = historyAct.generalFunc;
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        APP_TYPE = generalFunc.getJsonValue("APP_TYPE", userProfileJson);

        list = new ArrayList<>();
        historyRecyclerAdapter = new HistoryRecycleAdapter(getActContext(), list, generalFunc, false);
        historyRecyclerView.setAdapter(historyRecyclerAdapter);
        historyRecyclerAdapter.setOnItemClickListener(this);

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
                    historyRecyclerAdapter.addFooterView();

                    getBookingsHistory(true);

                } else if (isNextPageAvailable == false) {
                    historyRecyclerAdapter.removeFooterView();
                }
            }
        });

        getBookingsHistory(false);

        return view;
    }

    public boolean isDeliver() {
        if (getArguments().getString("HISTORY_TYPE").equals(Utils.CabGeneralType_Deliver)) {
            return true;
        }
        return false;
    }

    @Override
    public void onItemClickList(View v, int position) {
        Utils.hideKeyboard(getActivity());
        Bundle bn = new Bundle();
        bn.putString("TripData", list.get(position).get("JSON"));
        new StartActProcess(getActContext()).startActForResult(HistoryDetailActivity.class, bn, HISTORYDETAILS);
    }

    public void getBookingsHistory(final boolean isLoadMore) {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (loading_ride_history.getVisibility() != View.VISIBLE && isLoadMore == false) {
            loading_ride_history.setVisibility(View.VISIBLE);
        }

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", getArguments().getString("HISTORY_TYPE"));
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        //parameters.put("eType", getArguments().getString("HISTORY_TYPE"));
        if (isLoadMore == true) {
            parameters.put("page", next_page_str);
        }

        noRidesTxt.setVisibility(View.GONE);

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                noRidesTxt.setVisibility(View.GONE);

                Utils.printLog("responseString", ":" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    closeLoader();

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {

                        String nextPage = generalFunc.getJsonValue("NextPage", responseString);
                        JSONArray arr_rides = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

                        if (arr_rides != null && arr_rides.length() > 0) {
                            for (int i = 0; i < arr_rides.length(); i++) {
                                JSONObject obj_temp = generalFunc.getJsonObject(arr_rides, i);

                                HashMap<String, String> map = new HashMap<String, String>();

                                map.put("tTripRequestDateOrig", generalFunc.getJsonValue("tTripRequestDateOrig", obj_temp.toString()));
                                map.put("CurrencySymbol", generalFunc.getJsonValue("CurrencySymbol", obj_temp.toString()));
                                map.put("tSaddress", generalFunc.getJsonValue("tSaddress", obj_temp.toString()));
                                map.put("tDaddress", generalFunc.getJsonValue("tDaddress", obj_temp.toString()));
                                map.put("vRideNo", generalFunc.getJsonValue("vRideNo", obj_temp.toString()));

                                map.put("LBL_BOOKING_NO", generalFunc.retrieveLangLBl("", "LBL_BOOKING"));
                                map.put("LBL_Status", generalFunc.retrieveLangLBl("", "LBL_Status"));
                                map.put("is_rating", generalFunc.getJsonValue("is_rating", obj_temp.toString()));
                                map.put("iTripId", generalFunc.getJsonValue("iTripId", obj_temp.toString()));
                                if (generalFunc.getJsonValue("eType", obj_temp.toString()).equalsIgnoreCase("deliver")) {
                                    map.put("eType", generalFunc.retrieveLangLBl("Delivery", "LBL_DELIVERY"));
                                    map.put("LBL_PICK_UP_LOCATION", generalFunc.retrieveLangLBl("Sender Location", "LBL_SENDER_LOCATION"));
                                    map.put("LBL_DEST_LOCATION", generalFunc.retrieveLangLBl("Receiver's Location", "LBL_RECEIVER_LOCATION"));
                                } else {
                                    map.put("LBL_PICK_UP_LOCATION", generalFunc.retrieveLangLBl("", "LBL_PICK_UP_LOCATION"));
                                    map.put("eType", generalFunc.getJsonValue("eType", obj_temp.toString()));
                                    map.put("LBL_DEST_LOCATION", generalFunc.retrieveLangLBl("", "LBL_DEST_LOCATION"));
                                }
                                map.put("eFareType", generalFunc.getJsonValue("eFareType", obj_temp.toString()));


                                map.put("appType", APP_TYPE);
                                map.put("LBL_JOB_LOCATION_TXT", generalFunc.retrieveLangLBl("", "LBL_JOB_LOCATION_TXT"));


                                if (generalFunc.getJsonValue("eCancelled", obj_temp.toString()).equals("Yes")) {
                                    map.put("iActive", generalFunc.retrieveLangLBl("", "LBL_CANCELED_TXT"));
                                } else {
                                    if (generalFunc.getJsonValue("iActive", obj_temp.toString()).equals("Canceled")) {
                                        map.put("iActive", generalFunc.retrieveLangLBl("", "LBL_CANCELED_TXT"));
                                    } else if (generalFunc.getJsonValue("iActive", obj_temp.toString()).equals("Finished")) {
                                        map.put("iActive", generalFunc.retrieveLangLBl("", "LBL_FINISHED_TXT"));
                                    } else {
                                        map.put("iActive", generalFunc.getJsonValue("iActive", obj_temp.toString()));
                                    }
                                }

                                if (isDeliver()) {
                                    map.put("LBL_BOOKING_NO", generalFunc.retrieveLangLBl("Delivery No", "LBL_DELIVERY_NO"));
                                    map.put("LBL_CANCEL_BOOKING", generalFunc.retrieveLangLBl("Cancel Delivery", "LBL_CANCEL_DELIVERY"));
                                } else {
                                    map.put("LBL_BOOKING_NO", generalFunc.retrieveLangLBl("", "LBL_BOOKING"));
                                    map.put("LBL_CANCEL_BOOKING", generalFunc.retrieveLangLBl("", "LBL_CANCEL_BOOKING"));
                                }
                                if (generalFunc.retrieveValue(CommonUtilities.APP_DESTINATION_MODE).equalsIgnoreCase(CommonUtilities.NONE_DESTINATION)) {
                                    map.put("DESTINATION", "No");
                                } else {
                                    map.put("DESTINATION", "Yes");
                                }


                                map.put("JSON", obj_temp.toString());
                                map.put("APP_TYPE", APP_TYPE);

                                if (generalFunc.getJsonValue("eType", obj_temp.toString()).equals(Utils.CabGeneralType_UberX) &&
                                        !generalFunc.getJsonValue("eFareType", obj_temp.toString()).equalsIgnoreCase(Utils.CabFaretypeRegular)) {

                                    map.put("SelectedVehicle", generalFunc.getJsonValue("carTypeName", obj_temp.toString()));
                                    map.put("SelectedCategory", generalFunc.getJsonValue("vVehicleCategory", obj_temp.toString()));


                                }

                                list.add(map);

                            }
                        }

                        if (!nextPage.equals("") && !nextPage.equals("0")) {
                            next_page_str = nextPage;
                            isNextPageAvailable = true;
                        } else {
                            removeNextPageConfig();
                        }

                        historyRecyclerAdapter.notifyDataSetChanged();

                    } else {
                        if (list.size() == 0) {
                            removeNextPageConfig();
                            noRidesTxt.setText(generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                            noRidesTxt.setVisibility(View.VISIBLE);
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
        historyRecyclerAdapter.removeFooterView();
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
                getBookingsHistory(false);
            }
        });
    }


    public Context getActContext() {
        return historyAct.getActContext();
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (resultCode == Activity.RESULT_OK) {
            next_page_str = "2";
            userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
            list.clear();
            getBookingsHistory(true);

        }
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }
}
