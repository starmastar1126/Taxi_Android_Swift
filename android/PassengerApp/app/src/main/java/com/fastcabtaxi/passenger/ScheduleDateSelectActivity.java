package com.fastcabtaxi.passenger;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.ImageView;

import com.adapter.files.DatesRecyclerAdapter;
import com.adapter.files.TimeSlotAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Locale;

public class ScheduleDateSelectActivity extends AppCompatActivity implements TimeSlotAdapter.setRecentTimeSlotClickList, DatesRecyclerAdapter.OnDateSelectListener {


    GeneralFunctions generalFunc;
    ImageView backImgView;
    MTextView titleTxt;
    MTextView monthTxt;
    MButton btn_type2;
    MTextView AddressTxtView, serviceAddrHederTxtView;
    String address = "";
    String latitude = "";
    String longitude = "";
    String iUserAddressId = "";
    RecyclerView timeslotRecyclerView;
    String seldate = "";
    String seltime = "";
    String Stime = "";
    boolean ismain = false;
    String iCabBookingId = "";
    ArrayList<HashMap<String, String>> timeSlotList;

    ArrayList<HashMap<String, String>> timeSlotListOrig = new ArrayList<>();

    boolean isRebooking = false;
    ArrayList<Date> dateList = new ArrayList<>();

    RecyclerView datesRecyclerView;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_schedule_date_select);
        timeSlotList = new ArrayList<HashMap<String, String>>();
        generalFunc = new GeneralFunctions(getActContext());
        settimeSlotData();

        if (getIntent().getStringExtra("iCabBookingId") != null) {
            iCabBookingId = getIntent().getStringExtra("iCabBookingId");
        }

        isRebooking = getIntent().getBooleanExtra("isRebooking", false);

        if (getIntent().getStringExtra("Stime") != null) {
            Stime = getIntent().getStringExtra("Stime");
        }


        address = getIntent().getStringExtra("address");
        latitude = getIntent().getStringExtra("latitude");
        longitude = getIntent().getStringExtra("longitude");
        iUserAddressId = getIntent().getStringExtra("iUserAddressId");
        ismain = getIntent().getBooleanExtra(
                "isMain", false);


        backImgView = (ImageView) findViewById(R.id.backImgView);
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        monthTxt = (MTextView) findViewById(R.id.monthTxt);
        AddressTxtView = (MTextView) findViewById(R.id.AddressTxtView);
        datesRecyclerView = (RecyclerView) findViewById(R.id.datesRecyclerView);
        serviceAddrHederTxtView = (MTextView) findViewById(R.id.serviceAddrHederTxtView);
        AddressTxtView.setText(address);
        timeslotRecyclerView = (RecyclerView) findViewById(R.id.timeslotRecyclerView);
        timeslotRecyclerView.setLayoutManager(new GridLayoutManager(this, 3));
        TimeSlotAdapter adapter = new TimeSlotAdapter(getActContext(), timeSlotList);

        timeslotRecyclerView.setAdapter(adapter);
        adapter.setOnClickList(this);


        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        btn_type2.setOnClickListener(new setOnClick());
        btn_type2.setText(generalFunc.retrieveLangLBl("Continue", "LBL_CONTINUE_BTN"));


        backImgView.setOnClickListener(new setOnClick());

        /** end after 1 month from now */
        Calendar endDate = Calendar.getInstance(Locale.getDefault());
        endDate.add(Calendar.MONTH, 1);
        // endDate.add(Calendar.MONTH, 0);

        /** start before 1 month from now */
        Calendar startDate = Calendar.getInstance(Locale.getDefault());
        startDate.add(Calendar.MONTH, 0);
        // startDate.add(Calendar.MONTH, 0);

        Date currentTempDate = startDate.getTime();
        int position = 0;
        while (currentTempDate.before(endDate.getTime())) {

            Utils.printELog("currentTempDate", "::" + currentTempDate);
            dateList.add(currentTempDate);

            position = position + 1;

            Calendar tmpCal = Calendar.getInstance(Locale.getDefault());
            tmpCal.add(Calendar.DATE, position);

            currentTempDate = tmpCal.getTime();
        }

        DatesRecyclerAdapter dateAdapter = new DatesRecyclerAdapter(generalFunc, dateList, getActContext(), startDate.getTime());

        dateAdapter.setOnDateSelectListener(this);
        datesRecyclerView.setLayoutManager(new LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, true));

        datesRecyclerView.setAdapter(dateAdapter);

        dateAdapter.notifyDataSetChanged();

        if (!generalFunc.isRTLmode()) {
            datesRecyclerView.setLayoutDirection(View.LAYOUT_DIRECTION_RTL);
        } else {
            datesRecyclerView.setLayoutDirection(View.LAYOUT_DIRECTION_LTR);
        }

        final Calendar defaultDate = Calendar.getInstance(Locale.getDefault());
        //defaultDate.add(Calendar.MONTH, -1);
        defaultDate.add(Calendar.MONTH, 0);
        // defaultDate.add(Calendar.DAY_OF_WEEK, +5);
        defaultDate.add(Calendar.DAY_OF_WEEK, 0);


        setLabel();
    }

    private void setLabel() {
        titleTxt.setText(generalFunc.retrieveLangLBl("LBL_CHOOSE_BOOKING_DATE", "LBL_CHOOSE_BOOKING_DATE"));
        serviceAddrHederTxtView.setText(generalFunc.retrieveLangLBl("Service address", "LBL_SERVICE_ADDRESS_HINT_INFO"));
    }

    public Context getActContext() {
        return ScheduleDateSelectActivity.this;
    }

    @Override
    public void itemTimeSlotLocClick(int position) {

        seltime = timeSlotListOrig.get(position).get("selname");
        Stime = timeSlotListOrig.get(position).get("name");

    }

    private void CheckDateTimeApi() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "CheckScheduleTimeAvailability");
        parameters.put("scheduleDate", seldate + " " + seltime);


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        Bundle bundle = new Bundle();
                        bundle.putString("SelectedVehicleTypeId", getIntent().getStringExtra("SelectedVehicleTypeId"));
                        bundle.putString("Quantity", getIntent().getStringExtra("Quantity"));
                        bundle.putBoolean("isufx", true);
                        bundle.putString("latitude", getIntent().getStringExtra("latitude"));
                        bundle.putString("longitude", getIntent().getStringExtra("longitude"));
                        bundle.putString("address", getIntent().getStringExtra("address"));
                        bundle.putString("SelectDate", seldate + " " + seltime);
                        bundle.putString("SelectvVehicleType", getIntent().getStringExtra("SelectvVehicleType"));
                        bundle.putString("SelectvVehiclePrice", getIntent().getStringExtra("SelectvVehiclePrice"));
                        bundle.putString("iUserAddressId", getIntent().getStringExtra("iUserAddressId"));
                        bundle.putString("Quantityprice", getIntent().getStringExtra("Quantityprice"));
                        bundle.putString("type", Utils.CabReqType_Later);

                        bundle.putString("Sdate", generalFunc.getDateFormatedType(seldate, Utils.DefaultDatefromate, Utils.dateFormateForBooking));
                        bundle.putString("Stime", Stime);
                        bundle.putString("iCabBookingId", iCabBookingId);


                        if (ismain) {
                            new StartActProcess(getActContext()).setOkResult(bundle);
                            finish();

                        } else {

                            Utils.printLog("ActSchedule", "::" + System.currentTimeMillis());
                            bundle.putBoolean("isRebooking", isRebooking);
                            new StartActProcess(getActContext()).startActWithData(MainActivity.class, bundle);
                            //  finish();
                        }


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

    public void settimeSlotData() {

        for (int i = 0; i <= 23; i++) {
            HashMap<String, String> map = new HashMap<>();
            HashMap<String, String> mapOrig = new HashMap<>();

            map.put("status", "no");
            mapOrig.put("status", "no");

            int fromtime = i;
            int toTime = i + 1;


            String fromtimedisp = "";
            String Totimedisp = "";
            String selfromtime = "";
            String seltoTime = "";

            if (fromtime == 0) {
                fromtime = 12;
            }

            if (fromtime < 10) {
                selfromtime = "0" + fromtime;
            } else {
                selfromtime = fromtime + "";
            }

            if (toTime < 10) {
                seltoTime = "0" + toTime;
            } else {
                seltoTime = toTime + "";
            }

            if (i < 12) {


                if (fromtime < 10) {
                    fromtimedisp = "0" + fromtime;

                } else {
                    fromtimedisp = fromtime + "";

                }

                if (toTime < 10) {
                    Totimedisp = "0" + toTime;

                } else {
                    Totimedisp = toTime + "";
                }


                map.put("name", generalFunc.convertNumberWithRTL(fromtimedisp + " - " + Totimedisp + " " + generalFunc.retrieveLangLBl("am", "LBL_AM_TXT")));
                mapOrig.put("name", fromtimedisp + " - " + Totimedisp + " " + generalFunc.retrieveLangLBl("am", "LBL_AM_TXT"));
                map.put("selname", generalFunc.convertNumberWithRTL(selfromtime + "-" + seltoTime));
                mapOrig.put("selname", selfromtime + "-" + seltoTime);
            } else {

                fromtime = fromtime % 12;
                toTime = toTime % 12;
                if (fromtime == 0) {
                    fromtime = 12;
                }

                if (toTime == 0) {
                    toTime = 12;
                }
                if (fromtime < 10) {
                    fromtimedisp = "0" + fromtime;
                } else {
                    fromtimedisp = fromtime + "";
                }

                if (toTime < 10) {
                    Totimedisp = "0" + toTime;
                } else {
                    Totimedisp = toTime + "";
                }

                map.put("name", generalFunc.convertNumberWithRTL(fromtimedisp + " - " + Totimedisp + " " + generalFunc.retrieveLangLBl("pm", "LBL_PM_TXT")));
                mapOrig.put("name", fromtimedisp + " - " + Totimedisp + " " + generalFunc.retrieveLangLBl("pm", "LBL_PM_TXT"));
                map.put("selname", generalFunc.convertNumberWithRTL(selfromtime + "-" + seltoTime));
                mapOrig.put("selname", selfromtime + "-" + seltoTime);
            }


            timeSlotList.add(map);
            timeSlotListOrig.add(mapOrig);
        }

    }

    @Override
    public void onDateSelect(int position) {

        Date date = dateList.get(position);

        String tempdate = Utils.convertDateToFormat("yyyy-MM-dd HH:mm:ss", date);
        seldate = generalFunc.getDateFormatedType(tempdate, "yyyy-MM-dd HH:mm:ss", Utils.DefaultDatefromate, new Locale("en"));

        Locale locale = new Locale(generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY));
        DateFormat formatter = new SimpleDateFormat("MMMM", locale);

        String monthname = formatter.format(date);
        String year = (String) android.text.format.DateFormat.format("yyyy", date);
        monthTxt.setText(monthname + " " + generalFunc.convertNumberWithRTL(year));

    }

    public class setOnClick implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            if (i == R.id.backImgView) {
                ScheduleDateSelectActivity.super.onBackPressed();
            } else if (i == btn_type2.getId()) {
                if (seltime.equals("")) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Please Select Booking Time.", "LBL_SELECT_SERVICE_BOOKING_TIME"));
                    return;
                }

                CheckDateTimeApi();

            }
        }
    }

}
