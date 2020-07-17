package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.ImageView;

import com.adapter.files.TimeSlotAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import java.util.ArrayList;
import java.util.HashMap;

public class setTimeScheduleActivity extends AppCompatActivity implements TimeSlotAdapter.setRecentTimeSlotClickList {

    GeneralFunctions generalFunc;
    ImageView backImgView;
    MTextView titleTxt;
    RecyclerView timeslotRecyclerView;
    ArrayList daylist;
    MTextView serviceAddrHederTxtView;
    MButton btn_type2;
    int submitBtnId;

    ArrayList<String> selctlist = new ArrayList<String>();
    ;
    String selectday;
    TimeSlotAdapter adapter;


    ArrayList<HashMap<String, String>> timeSlotList;
    ArrayList<HashMap<String, String>> selTimeSlotList;
    ArrayList<HashMap<String, String>> checkTimeSlotList;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_set_time_schedule);


        generalFunc = new GeneralFunctions(getActContext());
        selectday = getIntent().getStringExtra("selectday");
        backImgView = (ImageView) findViewById(R.id.backImgView);
        titleTxt = (MTextView) findViewById(R.id.titleTxt);

        timeSlotList = new ArrayList<HashMap<String, String>>();
        selTimeSlotList = new ArrayList<HashMap<String, String>>();
        checkTimeSlotList = new ArrayList<HashMap<String, String>>();

        backImgView.setOnClickListener(new setOnClick());

        settimeSlotData();
        timeslotRecyclerView = (RecyclerView) findViewById(R.id.timeslotRecyclerView);
        serviceAddrHederTxtView = (MTextView) findViewById(R.id.serviceAddrHederTxtView);


        timeslotRecyclerView.setLayoutManager(new GridLayoutManager(this, 3));
        // timeslotRecyclerView.setLayoutManager(new LinearLayoutManager(this, LinearLayoutManager.VERTICAL, false));
        adapter = new TimeSlotAdapter(getActContext(), timeSlotList, selTimeSlotList, checkTimeSlotList);
        timeslotRecyclerView.setAdapter(adapter);
        adapter.setOnClickList(this);
        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        submitBtnId = Utils.generateViewId();
        btn_type2.setId(submitBtnId);
        btn_type2.setOnClickListener(new setOnClick());
        setLabel();

        getTimeSlotDetails();


    }


    public void setLabel() {
        titleTxt.setText(getIntent().getStringExtra("selectday_language"));
//        titleTxt.setText(selectday);
        serviceAddrHederTxtView.setText(generalFunc.retrieveLangLBl("Select the timeslot you are available to work.", "LBL_SELECT_TIME_SLOT"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_UPDATE_GENERAL"));
    }

    @Override
    public void itemTimeSlotLocClick(ArrayList<HashMap<String, String>> timeSlotList) {
        this.timeSlotList = timeSlotList;
    }


    public class setOnClick implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            if (i == R.id.backImgView) {
                setTimeScheduleActivity
                        .super.onBackPressed();
            } else if (i == submitBtnId) {
                addTimeSlotApi();
            }
        }
    }

    public void getTimeSlotDetails() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "DisplayAvailability");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("vDay", selectday);


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {


                        selTimeSlotList.clear();

                        String messageJson = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);

                        String[] vAvailableTimes = generalFunc.getJsonValue("vAvailableTimes", messageJson).split(",");
                        for (int i = 0; i < vAvailableTimes.length; i++) {

                            HashMap<String, String> map = new HashMap<String, String>();
                            map.put("selname", vAvailableTimes[i]);
                            map.put("status", "yes");
                            selTimeSlotList.add(map);
                        }
                        adapter.notifyDataSetChanged();
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void addTimeSlotApi() {

        String selectedtime = "";
        for (int i = 0; i < timeSlotList.size(); i++) {
            if (timeSlotList.get(i).get("status").equals("yes")) {
                if (selectedtime.length() == 0) {
                    selectedtime = checkTimeSlotList.get(i).get("selname");
                } else {
                    selectedtime = selectedtime + "," + checkTimeSlotList.get(i).get("selname");
                }
            }
        }


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "UpdateAvailability");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("vDay", selectday);
        parameters.put("vAvailableTimes", selectedtime);
        parameters.put("UserType", CommonUtilities.app_type);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                generateAlert.closeAlertBox();


                                new StartActProcess(getActContext()).setOkResult();
                                backImgView.performClick();

                            }
                        });
                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("Time slots added successfully", "LBL_TIMESLOT_ADD_SUCESS_MSG"));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));

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

    public Context getActContext() {
        return setTimeScheduleActivity.this;
    }


    public void settimeSlotData() {

        for (int i = 0; i <= 23; i++) {
            HashMap<String, String> map = new HashMap<>();
            HashMap<String, String> checkmap = new HashMap<>();

            map.put("status", "no");
            checkmap.put("status", "no");

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
                map.put("selname", generalFunc.convertNumberWithRTL(selfromtime + "-" + seltoTime));

                checkmap.put("name", fromtimedisp + " - " + Totimedisp + " " + generalFunc.retrieveLangLBl("am", "LBL_AM_TXT"));
                checkmap.put("selname", selfromtime + "-" + seltoTime);


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
                map.put("selname", generalFunc.convertNumberWithRTL(selfromtime + "-" + seltoTime));

                checkmap.put("name", fromtimedisp + " - " + Totimedisp + " " + generalFunc.retrieveLangLBl("pm", "LBL_PM_TXT"));
                checkmap.put("selname", selfromtime + "-" + seltoTime);
            }

            timeSlotList.add(map);
            checkTimeSlotList.add(checkmap);
        }

    }
}
