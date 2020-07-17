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
import com.fastcabtaxi.passenger.HistoryActivity;
import com.fastcabtaxi.passenger.R;
import com.fastcabtaxi.passenger.ScheduleDateSelectActivity;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
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
    String APP_TYPE = "";

    GeneralFunctions generalFunc;

    HistoryActivity myBookingAct;

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

        APP_TYPE = generalFunc.getJsonValue("APP_TYPE", generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON));

        list = new ArrayList<>();
        myBookingsRecyclerAdapter = new MyBookingsRecycleAdapter(getActContext(), list, generalFunc, false);
        myBookingsRecyclerView.setAdapter(myBookingsRecyclerAdapter);
        myBookingsRecyclerAdapter.setOnItemClickListener(this);


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


    public boolean isDeliver(String eType) {
        if (getArguments().getString("BOOKING_TYPE").equals(Utils.CabGeneralType_Deliver) || eType.equals("Deliver")) {
            return true;
        }
        return false;
    }

    public void onItemClickList(View v, int position, boolean isSchedulebooking) {
        Utils.hideKeyboard(getActContext());

        if (isSchedulebooking) {
            rescheduleBooking(position);
        } else {
            if (list.get(position).get("eStatus").equalsIgnoreCase(generalFunc.retrieveLangLBl("", "LBL_DECLINE_TXT")) || list.get(position).get("eStatus").equalsIgnoreCase(generalFunc.retrieveLangLBl("", "LBL_CANCELLED"))) {

                rescheduleBooking(position);
            } else {
                confirmCancelBooking(list.get(position).get("iCabBookingId"));
            }
        }
    }

    public void rescheduleBooking(int position) {
        Bundle bundle = new Bundle();
        bundle.putString("SelectedVehicleTypeId", list.get(position).get("iVehicleTypeId"));
        bundle.putBoolean("isufx", true);
        bundle.putString("latitude", list.get(position).get("vSourceLatitude"));
        bundle.putString("longitude", list.get(position).get("vSourceLongitude"));
        bundle.putString("address", list.get(position).get("vSourceAddresss"));
        bundle.putString("SelectDate", list.get(position).get("selecteddatetime"));
        bundle.putString("SelectvVehicleType", list.get(position).get("SelectedVehicle"));
        bundle.putString("SelectvVehiclePrice", list.get(position).get("SelectedPrice"));
        bundle.putString("iUserAddressId", list.get(position).get("iUserAddressId"));
        bundle.putString("type", Utils.CabReqType_Later);
        bundle.putString("Sdate", generalFunc.getDateFormatedType(list.get(position).get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.dateFormateForBooking));
        bundle.putString("Stime", list.get(position).get("selectedtime"));


        if (list.get(position).get("SelectedAllowQty").equalsIgnoreCase("yes")) {

            bundle.putString("Quantity", list.get(position).get("iQty"));
            bundle.putString("Quantityprice", list.get(position).get("SelectedCurrencySymbol") + "" + (GeneralFunctions.parseIntegerValue(1, list.get(position).get("iQty"))) * (GeneralFunctions.parseIntegerValue(1, list.get(position).get("SelectedPrice"))) + "");
        } else {
            bundle.putString("Quantityprice", list.get(position).get("SelectedCurrencySymbol") + "" + list.get(position).get("SelectedPrice"));
            bundle.putString("Quantity", "0");
        }

        bundle.putString("iCabBookingId", list.get(position).get("iCabBookingId"));
        bundle.putBoolean("isRebooking", true);

        new StartActProcess(getActContext()).startActWithData(ScheduleDateSelectActivity.class, bundle);
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
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("bookingType", getArguments().getString("BOOKING_TYPE"));
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

                                map.put("dBooking_dateOrig", generalFunc.getJsonValue("dBooking_dateOrig", obj_temp.toString()));
                                map.put("vSourceAddresss", generalFunc.getJsonValue("vSourceAddresss", obj_temp.toString()));
                                map.put("tDestAddress", generalFunc.getJsonValue("tDestAddress", obj_temp.toString()));
                                map.put("vBookingNo", generalFunc.getJsonValue("vBookingNo", obj_temp.toString()));
                                map.put("eStatus", generalFunc.getJsonValue("eStatus", obj_temp.toString()));
                                map.put("eStatusV", generalFunc.getJsonValue("eStatus", obj_temp.toString()));
                                map.put("iCabBookingId", generalFunc.getJsonValue("iCabBookingId", obj_temp.toString()));

                                if (generalFunc.getJsonValue("eType", obj_temp.toString()).equalsIgnoreCase(Utils.CabGeneralType_Deliver)) {
                                    map.put("eType", generalFunc.retrieveLangLBl("Delivery", "LBL_DELIVERY"));
                                } else {
                                    map.put("eType", generalFunc.getJsonValue("eType", obj_temp.toString()));
                                }
                                map.put("appType", APP_TYPE);

                                if (map.get("eStatus").equals("Completed")) {
                                    map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_ASSIGNED"));
                                } else if (map.get("eStatus").equals("Cancel")) {

                                    if (generalFunc.getJsonValue("eType", obj_temp.toString()).equals(Utils.CabGeneralType_UberX) && !generalFunc.getJsonValue("eFareType", obj_temp.toString()).equals(Utils.CabFaretypeRegular)) {
                                        map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_CANCELLED"));
                                    } else {
                                        map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_CANCELLED"));
                                    }
                                } else if (map.get("eStatus").equals("Pending")) {
                                    map.put("eStatus", generalFunc.retrieveLangLBl("Pending", "LBL_PENDING"));
                                } else if (map.get("eStatus").equals("Declined")) {
                                    map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_DECLINE_TXT"));

                                } else if (map.get("eStatus").equals("Accepted")) {
                                    map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_BOOKING_ACCEPTED"));

                                }

                                if (generalFunc.getJsonValue("eCancelBy", obj_temp.toString()).equals("Driver")) {

                                    if (generalFunc.getJsonValue("eType", obj_temp.toString()).equals(Utils.CabGeneralType_UberX) && !generalFunc.getJsonValue("eFareType", obj_temp.toString()).equals(Utils.CabFaretypeRegular)) {
                                        map.get("eStatus").equals("Cancel");
                                    } else {
                                        map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_CANCELLED_BY_DRIVER"));

                                    }
                                }

                                if (generalFunc.getJsonValue("eCancelBy", obj_temp.toString()).equals("Admin")) {

                                    map.put("eStatus", generalFunc.retrieveLangLBl("", "LBL_CANCELLED_BY_ADMIN"));

                                }

                                if (isDeliver(generalFunc.getJsonValue("eType", obj_temp.toString()))) {
                                    map.put("LBL_BOOKING_NO", generalFunc.retrieveLangLBl("Delivery No", "LBL_DELIVERY_NO"));
                                    map.put("LBL_CANCEL_BOOKING", generalFunc.retrieveLangLBl("Cancel Delivery", "LBL_CANCEL_DELIVERY"));
                                    map.put("LBL_PICK_UP_LOCATION", generalFunc.retrieveLangLBl("Sender Location", "LBL_SENDER_LOCATION"));
                                    map.put("LBL_DEST_LOCATION", generalFunc.retrieveLangLBl("Receiver's Location", "LBL_RECEIVER_LOCATION"));

                                } else {
                                    map.put("LBL_BOOKING_NO", generalFunc.retrieveLangLBl("", "LBL_BOOKING"));
                                    map.put("LBL_CANCEL_BOOKING", generalFunc.retrieveLangLBl("", "LBL_CANCEL_BOOKING"));
                                    map.put("LBL_JOB_LOCATION_TXT", generalFunc.retrieveLangLBl("", "LBL_JOB_LOCATION_TXT"));
                                    map.put("LBL_DEST_LOCATION", generalFunc.retrieveLangLBl("", "LBL_DEST_LOCATION"));
                                }

                                map.put("LBL_Status", generalFunc.retrieveLangLBl("", "LBL_Status"));
                                map.put("JSON", obj_temp.toString());
                                map.put("LBL_PICK_UP_LOCATION", generalFunc.retrieveLangLBl("", "LBL_PICK_UP_LOCATION"));


                                if (generalFunc.getJsonValue("eType", obj_temp.toString()).equals(Utils.CabGeneralType_UberX) &&
                                        !generalFunc.getJsonValue("eFareType", obj_temp.toString()).equalsIgnoreCase(Utils.CabFaretypeRegular)) {
                                    map.put("selectedtime", generalFunc.getJsonValue("selectedtime", obj_temp.toString()));

                                    map.put("iVehicleTypeId", generalFunc.getJsonValue("iVehicleTypeId", obj_temp.toString()));

                                    map.put("iQty", generalFunc.getJsonValue("iQty", obj_temp.toString()));

                                    map.put("vSourceLatitude", generalFunc.getJsonValue("vSourceLatitude", obj_temp.toString()));

                                    map.put("vSourceLongitude", generalFunc.getJsonValue("vSourceLongitude", obj_temp.toString()));

                                    map.put("iUserAddressId", generalFunc.getJsonValue("iUserAddressId", obj_temp.toString()));

                                    map.put("dBooking_dateOrig", generalFunc.getJsonValue("dBooking_dateOrig", obj_temp.toString()));

                                    map.put("selecteddatetime", generalFunc.getJsonValue("selecteddatetime", obj_temp.toString()));

                                    map.put("SelectedCurrencySymbol", generalFunc.getJsonValue("SelectedCurrencySymbol", obj_temp.toString()));

                                    map.put("SelectedAllowQty", generalFunc.getJsonValue("SelectedAllowQty", obj_temp.toString()));

                                    map.put("SelectedPrice", generalFunc.getJsonValue("SelectedPrice", obj_temp.toString()));

                                    map.put("SelectedVehicle", generalFunc.getJsonValue("SelectedVehicle", obj_temp.toString()));

                                    map.put("SelectedCurrencySymbol", generalFunc.getJsonValue("SelectedCurrencySymbol", obj_temp.toString()));
                                    map.put("SelectedCategory", generalFunc.getJsonValue("SelectedCategory", obj_temp.toString()));


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

    public void confirmCancelBooking(final String iCabBookingId) {
        final android.support.v7.app.AlertDialog alertDialog;
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle(generalFunc.retrieveLangLBl("Cancel Booking", "LBL_CANCEL_BOOKING"));

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

                cancelBooking(iCabBookingId, Utils.getText(reasonBox));

            }
        });

        alertDialog.getButton(AlertDialog.BUTTON_NEGATIVE).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                alertDialog.dismiss();
            }
        });
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

    public Context getActContext() {
        return myBookingAct.getActContext();
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActContext());
    }


}
