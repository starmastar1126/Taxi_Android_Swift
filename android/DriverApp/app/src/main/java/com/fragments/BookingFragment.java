package com.fragments;


import android.content.Context;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.app.AlertDialog;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;

import com.adapter.files.MyBookingsRecycleAdapter;
import com.fastcabtaxi.driver.HistoryActivity;
import com.fastcabtaxi.driver.R;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.GenerateAlertBox;
import com.view.MTextView;
import com.view.editBox.MaterialEditText;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * A simple {@link Fragment} subclass.
 */
public class BookingFragment extends Fragment implements MyBookingsRecycleAdapter.OnItemClickListener {


    View view;

    ProgressBar loading_my_bookings;
    MTextView noRidesTxt;

    RecyclerView myBookingsRecyclerView;
    ErrorView errorView;

    MyBookingsRecycleAdapter myBookingsRecyclerAdapter;

    ArrayList<HashMap<String, String>> list;

    boolean mIsLoading = false;
    boolean isNextPageAvailable = false;

    String next_page_str = "";

    GeneralFunctions generalFunc;

    HistoryActivity myBookingAct;
    String type = "";
    String userProfileJson = "";
    String app_type = "";
    String APP_TYPE = "";

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        view = inflater.inflate(R.layout.fragment_booking, container, false);

        loading_my_bookings = (ProgressBar) view.findViewById(R.id.loading_my_bookings);
        noRidesTxt = (MTextView) view.findViewById(R.id.noRidesTxt);
        myBookingsRecyclerView = (RecyclerView) view.findViewById(R.id.myBookingsRecyclerView);
        errorView = (ErrorView) view.findViewById(R.id.errorView);
        myBookingAct = (HistoryActivity) getActivity();
        generalFunc = myBookingAct.generalFunc;
        type = getArguments().getString("type");
        list = new ArrayList<>();
        myBookingsRecyclerAdapter = new MyBookingsRecycleAdapter(getActContext(), list, type, generalFunc, false);
        myBookingsRecyclerView.setAdapter(myBookingsRecyclerAdapter);
        myBookingsRecyclerAdapter.setOnItemClickListener(this);
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);
        APP_TYPE = generalFunc.getJsonValue("APP_TYPE", generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON));


        myBookingsRecyclerView.addOnScrollListener(new RecyclerView.OnScrollListener() {
            @Override
            public void onScrolled(RecyclerView recyclerView, int dx, int dy) {
                super.onScrolled(recyclerView, dx, dy);

                int visibleItemCount = recyclerView.getLayoutManager().getChildCount();
                int totalItemCount = recyclerView.getLayoutManager().getItemCount();
                int firstVisibleItemPosition = ((LinearLayoutManager) recyclerView.getLayoutManager()).findFirstVisibleItemPosition();

                int lastInScreen = firstVisibleItemPosition + visibleItemCount;
                if ((lastInScreen == totalItemCount) && !(mIsLoading) && isNextPageAvailable == true) {

                    mIsLoading = true;
                    myBookingsRecyclerAdapter.addFooterView();


                    getBookingsHistory(true);


                } else if (isNextPageAvailable == false) {
                    myBookingsRecyclerAdapter.removeFooterView();
                }
            }
        });

        getBookingsHistory(false);

        return view;
    }

    @Override
    public void onResume() {
        super.onResume();
        Utils.printLog("BookingFragment", "::onresume called");
        getBookingsHistory(false);
    }

    public boolean isDeliver() {
        if (getArguments().getString("BOOKING_TYPE").equals(Utils.CabGeneralType_Deliver)) {
            return true;
        }
        return false;
    }

    @Override
    public void onCancelBookingClickList(View v, int position) {

        confirmCancelBooking(list.get(position).get("iCabBookingId"));
    }

    @Override
    public void onTripStartClickList(View v, int position) {

        buildMsgOnStartTripBtn(list.get(position).get("iCabBookingId"));
    }

    public void confirmCancelBooking(final String iCabBookingId) {
        final android.support.v7.app.AlertDialog alertDialog;
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        if (type.equalsIgnoreCase("Pending")) {

            builder.setTitle(generalFunc.retrieveLangLBl("Decline Job", "LBL_DECLINE_BOOKING"));

        } else {
            builder.setTitle(generalFunc.retrieveLangLBl("Cancel Booking", "LBL_CANCEL_TRIP"));
        }

        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.input_box_view, null);


        final MaterialEditText reasonBox = (MaterialEditText) dialogView.findViewById(R.id.editBox);

        reasonBox.setSingleLine(false);
        reasonBox.setMaxLines(5);

        reasonBox.setBothText(generalFunc.retrieveLangLBl("Reason", "LBL_REASON"), generalFunc.retrieveLangLBl("Enter your reason", "LBL_ENTER_REASON"));


        builder.setView(dialogView);
        builder.setPositiveButton(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {

            }
        });
        builder.setNegativeButton(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
            }
        });

        alertDialog = builder.create();
        alertDialog.show();

        alertDialog.getButton(AlertDialog.BUTTON_POSITIVE).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                if (Utils.checkText(reasonBox) == false) {
                    reasonBox.setError(generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT"));
                    return;
                }

                alertDialog.dismiss();

                if (type.equalsIgnoreCase("Pending")) {
                    declineBooking(iCabBookingId, Utils.getText(reasonBox));
                } else {
                    cancelBooking(iCabBookingId, Utils.getText(reasonBox));
                }

            }
        });

        alertDialog.getButton(AlertDialog.BUTTON_NEGATIVE).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                alertDialog.dismiss();
            }
        });
    }

    public void acceptBooking(String iCabBookingId, String eConfirmByProvider) {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "UpdateBookingStatus");
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("iCabBookingId", iCabBookingId);
        parameters.put("eStatus", "Accepted");
        parameters.put("eConfirmByProvider", eConfirmByProvider);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);


                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        list.clear();
                        myBookingsRecyclerAdapter.notifyDataSetChanged();


                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                generateAlert.closeAlertBox();

                                getBookingsHistory(false);
                            }
                        });
                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

                        generateAlert.showAlertBox();
                    } else {

                        String BookingFound = generalFunc.getJsonValue("BookingFound", responseString);

                        if (BookingFound.equalsIgnoreCase("Yes")) {

                            GenerateAlertBox alertBox = new GenerateAlertBox(getActContext());
                            alertBox.setCancelable(false);
                            alertBox.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                            alertBox.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
                            alertBox.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));
                            alertBox.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                                @Override
                                public void handleBtnClick(int btn_id) {
                                    if (btn_id == 0) {
                                        alertBox.closeAlertBox();
                                    } else if (btn_id == 1) {
                                        acceptBooking(iCabBookingId, "Yes");
                                        alertBox.closeAlertBox();
                                    }
                                }
                            });
                            alertBox.showAlertBox();
                        } else {
                            generalFunc.showGeneralMessage("",
                                    generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        }
                    }

                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void declineBooking(String iCabBookingId, String reason) {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "UpdateBookingStatus");
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("iCabBookingId", iCabBookingId);
        parameters.put("vCancelReason", reason);
        parameters.put("eStatus", "Declined");

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        list.clear();
                        myBookingsRecyclerAdapter.notifyDataSetChanged();


                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                generateAlert.closeAlertBox();
                                getBookingsHistory(false);
                            }
                        });
                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

                        generateAlert.showAlertBox();
                    } else {
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }

                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void cancelBooking(String iCabBookingId, String reason) {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "cancelBooking");
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("iCabBookingId", iCabBookingId);
        parameters.put("Reason", reason);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        list.clear();
                        myBookingsRecyclerAdapter.notifyDataSetChanged();
                        getBookingsHistory(false);
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    } else {
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }

                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void buildMsgOnStartTripBtn(final String iCabBookingId) {
        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                if (btn_id == 0) {
                    generateAlert.closeAlertBox();
                } else {
                    if (type.equalsIgnoreCase("Pending")) {
                        acceptBooking(iCabBookingId, "No");
                    } else {
                        startTrip(iCabBookingId);
                    }
                }

            }
        });
        if (type.equalsIgnoreCase("Pending")) {

            generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("Are you sure? You want to accept this job.", "LBL_CONFIRM_ACCEPT_JOB"));
        } else {
            generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", "LBL_CONFIRM_START_TRIP_TXT"));
        }
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));
        generateAlert.showAlertBox();
    }

    public void startTrip(String iCabBookingId) {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "GenerateTrip");
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("DriverID", generalFunc.getMemberId());
        parameters.put("iCabBookingId", iCabBookingId);
        parameters.put("GoogleServerKey", getResources().getString(R.string.google_api_get_address_from_location_serverApi));

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
//                        Bundle bn = new Bundle();
//
//                        String message_json = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
//                        HashMap<String, String> map = new HashMap<>();
//                        map.put("Message", "CabRequested");
//                        map.put("sourceLatitude", generalFunc.getJsonValue("sourceLatitude", responseString));
//                        map.put("sourceLongitude", generalFunc.getJsonValue("sourceLongitude", responseString));
//                        map.put("tSaddress", generalFunc.getJsonValue("tSaddress", responseString));
//
//                        map.put("PassengerId", generalFunc.getJsonValue("PassengerId", responseString));
//                        map.put("PName", generalFunc.getJsonValue("PName", responseString));
//                        map.put("PPicName", generalFunc.getJsonValue("PPicName", responseString));
//                        map.put("PFId", generalFunc.getJsonValue("PFId", responseString));
//                        map.put("PRating", generalFunc.getJsonValue("PRating", responseString));
//                        map.put("PPhone", generalFunc.getJsonValue("PPhone", responseString));
//                        map.put("PPhoneC", generalFunc.getJsonValue("PPhoneC", responseString));
//                        map.put("TripId", generalFunc.getJsonValue("TripId", responseString));
//                        map.put("DestLocLatitude", generalFunc.getJsonValue("DestLocLatitude", responseString));
//                        map.put("DestLocLongitude", generalFunc.getJsonValue("DestLocLongitude", responseString));
//                        map.put("DestLocAddress", generalFunc.getJsonValue("DestLocAddress", responseString));
//                        map.put("PAppVersion", generalFunc.getJsonValue("PAppVersion", responseString));
//                        //  map.put("REQUEST_TYPE", getArguments().getString("BOOKING_TYPE"));
//                        map.put("eFareType", generalFunc.getJsonValue("eFareType", message_json));
//                        bn.putSerializable("TRIP_DATA", map);
//                        map.put("REQUEST_TYPE", generalFunc.getJsonValue("REQUEST_TYPE", message_json));
//                        map.put("iTripId", generalFunc.getJsonValue("iTripId", message_json));
//                        new StartActProcess(getActContext()).startActWithData(DriverArrivedActivity.class, bn);

                        // myBookingAct.finishScreens();
                        generalFunc.restartwithGetDataApp();
                    } else {
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }

                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }


    public void getBookingsHistory(final boolean isLoadMore) {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (loading_my_bookings.getVisibility() != View.VISIBLE && isLoadMore == false) {
            loading_my_bookings.setVisibility(View.VISIBLE);
        }

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "checkBookings");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("bookingType", getArguments().getString("BOOKING_TYPE"));
        parameters.put("DataType", type);
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

                        list.clear();
                        String nextPage = generalFunc.getJsonValue("NextPage", responseString);
                        JSONArray arr_rides = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

                        if (arr_rides != null && arr_rides.length() > 0) {
                            for (int i = 0; i < arr_rides.length(); i++) {
                                JSONObject obj_temp = generalFunc.getJsonObject(arr_rides, i);
                                HashMap<String, String> map = new HashMap<String, String>();
                                map.put("dBooking_date", generalFunc.getJsonValue("dBooking_date", obj_temp.toString()));
                                map.put("vSourceAddresss", generalFunc.getJsonValue("vSourceAddresss", obj_temp.toString()));
                                map.put("tDestAddress", generalFunc.getJsonValue("tDestAddress", obj_temp.toString()));
                                map.put("vBookingNo", generalFunc.getJsonValue("vBookingNo", obj_temp.toString()));
                                map.put("eStatus", generalFunc.getJsonValue("eStatus", obj_temp.toString()));
                                map.put("iCabBookingId", generalFunc.getJsonValue("iCabBookingId", obj_temp.toString()));
                                map.put("dBooking_dateOrig", generalFunc.getJsonValue("dBooking_dateOrig", obj_temp.toString()));

                                if (generalFunc.getJsonValue("selectedtime", obj_temp.toString()) != null) {
                                    map.put("selectedtime", generalFunc.getJsonValue("selectedtime", obj_temp.toString()));
                                }

                                if (generalFunc.getJsonValue("eType", obj_temp.toString()).equalsIgnoreCase(Utils.CabGeneralType_Deliver)) {
                                    map.put("eType", generalFunc.retrieveLangLBl("Delivery", "LBL_DELIVERY"));
                                } else {
                                    map.put("eType", generalFunc.getJsonValue("eType", obj_temp.toString()));
                                }
                                map.put("eFareType", generalFunc.getJsonValue("eFareType", obj_temp.toString()));
                                map.put("appType", APP_TYPE);

                                if (map.get("eStatus").equals("Completed")) {
                                    map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_ASSIGNED"));
                                } else if (map.get("eStatus").equals("Cancel")) {
                                    map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_CANCELLED"));
                                }

                                if (generalFunc.getJsonValue("eCancelBy", obj_temp.toString()).equals("Driver")) {
                                    map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_CANCELLED_BY_DRIVER"));
                                }

                                if (isDeliver()) {
                                    map.put("LBL_BOOKING_NO", generalFunc.retrieveLangLBl("Delivery No", "LBL_DELIVERY_NO"));
                                    map.put("LBL_START_TRIP", generalFunc.retrieveLangLBl("Start Delivery", "LBL_BEGIN_DELIVERY"));
                                    map.put("LBL_CANCEL_BOOKING", generalFunc.retrieveLangLBl("Cancel Delivery", "LBL_CANCEL_DELIVERY"));
                                    map.put("LBL_PICK_UP_LOCATION", generalFunc.retrieveLangLBl("Sender Location", "LBL_SENDER_LOCATION"));
                                    map.put("LBL_DEST_LOCATION", generalFunc.retrieveLangLBl("Receiver's Location", "LBL_RECEIVER_LOCATION"));
                                } else {
                                    map.put("LBL_BOOKING_NO", generalFunc.retrieveLangLBl("", "LBL_BOOKING"));
                                    map.put("LBL_START_TRIP", generalFunc.retrieveLangLBl("", "LBL_BEGIN_TRIP"));
                                    map.put("LBL_CANCEL_TRIP", generalFunc.retrieveLangLBl("", "LBL_CANCEL_TRIP"));
                                    map.put("LBL_PICK_UP_LOCATION", generalFunc.retrieveLangLBl("", "LBL_PICK_UP_LOCATION"));
                                    map.put("LBL_DEST_LOCATION", generalFunc.retrieveLangLBl("", "LBL_DEST_LOCATION"));
                                }


                                if (app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                                    map.put("LBL_ACCEPT_JOB", generalFunc.retrieveLangLBl("Accept Job", "LBL_ACCEPT_JOB"));
                                    map.put("LBL_DECLINE_JOB", generalFunc.retrieveLangLBl("Decline job", "LBL_DECLINE_JOB"));

                                    map.put("SelectedCategory", generalFunc.getJsonValue("SelectedCategory", obj_temp.toString()));
                                    map.put("SelectedVehicle", generalFunc.getJsonValue("SelectedVehicle", obj_temp.toString()));


                                }


                                map.put("LBL_Status", generalFunc.retrieveLangLBl("", "LBL_Status"));
                                map.put("JSON", obj_temp.toString());

                                map.put("LBL_JOB_LOCATION_TXT", generalFunc.retrieveLangLBl("", "LBL_JOB_LOCATION_TXT"));


                                list.add(map);

                            }
                        }

                        if (!nextPage.equals("") && !nextPage.equals("0")) {
                            next_page_str = nextPage;
                            isNextPageAvailable = true;
                        } else {
                            removeNextPageConfig();
                        }

                        myBookingsRecyclerAdapter.notifyDataSetChanged();

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
        myBookingsRecyclerAdapter.removeFooterView();
    }

    public void closeLoader() {
        if (loading_my_bookings.getVisibility() == View.VISIBLE) {
            loading_my_bookings.setVisibility(View.GONE);
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
        return myBookingAct.getActContext();
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }
}
