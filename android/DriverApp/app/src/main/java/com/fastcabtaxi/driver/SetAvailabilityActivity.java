package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.GridLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.ImageView;

import com.adapter.files.DaySlotAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import org.json.JSONArray;
import org.json.JSONObject;

import java.text.DateFormatSymbols;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Locale;

public class SetAvailabilityActivity extends AppCompatActivity implements DaySlotAdapter.setRecentTimeSlotClickList {

    GeneralFunctions generalFunc;
    ImageView backImgView;
    MTextView titleTxt;
    RecyclerView timeslotRecyclerView;
    ArrayList daylist;
    ArrayList passApidaylist;
    ArrayList passApidaylist1;

    MButton btn_type2;
    int submitBtnId;
    MTextView serviceAddrHederTxtView;

    String selectday = "";
    String selectday_language = "";
    DaySlotAdapter adapter;
    ArrayList<String> selectedlist;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_set_availability);
        selectedlist = new ArrayList<>();


        generalFunc = new GeneralFunctions(getActContext());
        setDayData();

        backImgView = (ImageView) findViewById(R.id.backImgView);
        titleTxt = (MTextView) findViewById(R.id.titleTxt);

        backImgView.setOnClickListener(new setOnClick());

        serviceAddrHederTxtView = (MTextView) findViewById(R.id.serviceAddrHederTxtView);


        timeslotRecyclerView = (RecyclerView) findViewById(R.id.timeslotRecyclerView);
        timeslotRecyclerView.setLayoutManager(new GridLayoutManager(this, 4));
        adapter = new DaySlotAdapter(getActContext(), passApidaylist, selectedlist, daylist);
        timeslotRecyclerView.setAdapter(adapter);
        adapter.setOnClickList(this);

        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();


        submitBtnId = Utils.generateViewId();
        btn_type2.setId(submitBtnId);

        btn_type2.setOnClickListener(new setOnClick());
        setLabel();


    }

    @Override
    protected void onResume() {
        super.onResume();
        getselDayApi();
    }

    public void setLabel() {
        titleTxt.setText(generalFunc.retrieveLangLBl("Set Availability", "LBL_MY_AVAILABILITY"));
        serviceAddrHederTxtView.setText(generalFunc.retrieveLangLBl("Select the days you are available to work.", "LBL_SELECT_BELOW_TIMES_SLOT_TXT"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_CONTINUE_BTN"));

    }


    public void setDayData() {
        daylist = new ArrayList<>();
        passApidaylist = new ArrayList();
        passApidaylist1 = new ArrayList();
        Locale locale = new Locale(generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY));

        String[] namesOfDays = DateFormatSymbols.getInstance(Locale.ENGLISH).getWeekdays();
        String[] namesOfDays1 = DateFormatSymbols.getInstance(locale).getWeekdays();
        for (int i = 0; i < namesOfDays.length; i++) {
            if (i != 0) {
                passApidaylist.add(namesOfDays[i]);
                passApidaylist1.add(namesOfDays1[i]);
            }
        }

        String[] passnamesOfDays = DateFormatSymbols.getInstance(locale).getWeekdays();
        for (int i = 0; i < passnamesOfDays.length; i++) {
            if (i != 0) {
                daylist.add(passnamesOfDays[i]);
            }
        }


    }

    public void getselDayApi() {
        // http://192.168.1.131/cubetaxidev/webservice_test_ufx.php?type=DisplayDriverDaysAvailability&iDriverId=31

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "DisplayDriverDaysAvailability");
        parameters.put("iDriverId", generalFunc.getMemberId());

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        selectedlist.clear();
                        JSONArray obj_arr = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);
                        if (obj_arr == null || obj_arr.length() == 0) {
                            return;
                        }

                        for (int i = 0; i < obj_arr.length(); i++) {
                            JSONObject obj_temp = generalFunc.getJsonObject(obj_arr, i);
                            selectedlist.add(generalFunc.getJsonValue("vDay", obj_temp.toString()));
                        }
                        adapter.notifyDataSetChanged();

                    }
                }
            }
        });
        exeWebServer.execute();
    }

    public Context getActContext() {
        return SetAvailabilityActivity.this;
    }

    @Override
    public void itemTimeSlotLocClick(int position) {
        selectday = passApidaylist.get(position).toString();
        selectday_language = passApidaylist1.get(position).toString();
        String dispselectday = daylist.get(position).toString();
        Bundle bundle = new Bundle();
        bundle.putString("selectday", selectday);
        bundle.putString("selectday_language", selectday_language);
        bundle.putString("dispselectday", dispselectday);

        new StartActProcess(getActContext()).startActWithData(setTimeScheduleActivity.class, bundle);
        adapter.isSelectedPos = -1;
        adapter.notifyDataSetChanged();

    }


    public class setOnClick implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            if (i == R.id.backImgView) {
                SetAvailabilityActivity
                        .super.onBackPressed();
            } else if (i == submitBtnId) {

                if (selectday.equals("")) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Please select Day", "LBL_SELECT_DAY_TXT"));
                    return;
                }

            }
        }
    }
}
