package com.fastcabtaxi.driver;

import android.content.Context;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.CardView;
import android.view.MotionEvent;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.ImageView;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.MTextView;
import com.view.anim.loader.AVLoadingIndicatorView;
import com.view.chart.model.Axis;
import com.view.chart.model.AxisValue;
import com.view.chart.model.Line;
import com.view.chart.model.LineChartData;
import com.view.chart.model.PointValue;
import com.view.chart.model.ValueShape;
import com.view.chart.model.Viewport;
import com.view.chart.view.LineChartView;
import com.view.editBox.MaterialEditText;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import static com.fastcabtaxi.driver.R.id.chart1;


public class StatisticsActivity extends AppCompatActivity {

    MTextView titleTxt;
    ImageView backImgView;
    GeneralFunctions generalFunc;
    String userProfileJson = "";
    String selectedyear = "";
    MTextView monthHTxt;
    MTextView noTripHTxt, noTripVTxt;
    MTextView totalearnHTxt, totalearnVTxt;
    MaterialEditText yearBox;

    ArrayList<String> items_txt_year = new ArrayList<String>();
    android.support.v7.app.AlertDialog list_year;
    String TotalEarning = "";
    String TripCount = "";
    ArrayList<String> listData = new ArrayList<>();
//    private LineChart mChart;

    LineChartView chart;
    ArrayList<String> monthList = new ArrayList<>();
    private LineChartData data;

    AVLoadingIndicatorView loaderView;
    ErrorView errorView;
    FrameLayout yearSelectArea;
    CardView bottomarea;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_statistics);
        generalFunc = new GeneralFunctions(getActContext());
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        monthHTxt = (MTextView) findViewById(R.id.monthHTxt);
        noTripHTxt = (MTextView) findViewById(R.id.noTripHTxt);
        noTripVTxt = (MTextView) findViewById(R.id.noTripVTxt);
        totalearnHTxt = (MTextView) findViewById(R.id.totalearnHTxt);
        totalearnVTxt = (MTextView) findViewById(R.id.totalearnVTxt);
        yearBox = (MaterialEditText) findViewById(R.id.yearBox);
        chart = (LineChartView) findViewById(chart1);
        errorView = (ErrorView) findViewById(R.id.errorView);
        loaderView = (AVLoadingIndicatorView) findViewById(R.id.loaderView);
        backImgView.setOnClickListener(new setOnClickList());
        yearBox.getLabelFocusAnimator().start();
        yearSelectArea = (FrameLayout) findViewById(R.id.yearSelectArea);
        bottomarea = (CardView) findViewById(R.id.bottomarea);
        setLabels();
        getChartDetails();
        buildLanguageList();

        Utils.removeInput(yearBox);
        yearBox.setOnTouchListener(new setOnTouchList());
        yearBox.setOnClickListener(new setOnClickList());

    }

    public void generateErrorView() {

        yearSelectArea.setVisibility(View.GONE);
        bottomarea.setVisibility(View.GONE);
        generalFunc.generateErrorView(errorView, "LBL_ERROR_TXT", "LBL_NO_INTERNET_TXT");

        if (errorView.getVisibility() != View.VISIBLE) {
            errorView.setVisibility(View.VISIBLE);
        }
        errorView.setOnRetryListener(new ErrorView.RetryListener() {
            @Override
            public void onRetry() {
                getChartDetails();
            }
        });
    }


    private void resetViewport(float top) {
        // Reset viewport height range to (0,100)
        final Viewport v = new Viewport(chart.getMaximumViewport());
        v.bottom = 0;
        v.top = top;
        v.left = 0;
        v.right = monthList.size() - 1;
        chart.setMaximumViewport(v);
        chart.setCurrentViewport(v);
    }

    private void generateData() {

        List<Line> lines = new ArrayList<Line>();


        List<PointValue> values = new ArrayList<PointValue>();
        for (int j = 0; j < monthList.size(); ++j) {
            values.add(new PointValue(j, generalFunc.parseFloatValue(0, listData.get(j))));
        }

        Line line = new Line(values);
        line.setColor(getResources().getColor(R.color.appThemeColor_1));
        line.setShape(ValueShape.CIRCLE);
        line.setCubic(true);
        line.setFilled(true);
        line.setHasLabels(true);
        line.setHasLabelsOnlyForSelected(false);
        line.setHasLines(true);
        line.setHasPoints(true);
        line.setHasGradientToTransparent(true);

        line.setPointColor(getResources().getColor(R.color.appThemeColor_1));

        lines.add(line);


        data = new LineChartData(lines);

        List<AxisValue> axisValues = new ArrayList<AxisValue>();
        for (int i = 0; i < monthList.size(); ++i) {
            axisValues.add(new AxisValue(i).setLabel(monthList.get(i)));
        }
        Axis dataSetAxis = new Axis(axisValues).setHasLines(false);
        dataSetAxis.setTextColor(getResources().getColor(R.color.appThemeColor_1));
        data.setAxisXBottom(dataSetAxis);

        data.setBaseValue(Float.NEGATIVE_INFINITY);
        chart.setLineChartData(data);

    }


    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("Trip Statistics", "LBL_TRIP_STATISTICS_TXT"));
        totalearnHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_TOTAL_EARNINGS"));
        noTripHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_NUMBER_OF_TRIPS"));
        yearBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_YEAR"), generalFunc.retrieveLangLBl("", "LBL_CHOOSE_YEAR"));
    }

    public Context getActContext() {
        return StatisticsActivity.this;
    }

    public void buildLanguageList() {


        CharSequence[] cs_languages_txt = items_txt_year.toArray(new CharSequence[items_txt_year.size()]);

        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());

        builder.setTitle(getSelectYearText());

        builder.setItems(cs_languages_txt, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int item) {
                // Do something with the selection

                if (list_year != null) {
                    list_year.dismiss();
                }

                yearBox.setText(items_txt_year.get(item));
                selectedyear = items_txt_year.get(item).toString();
                getChartDetails();

            }
        });

        list_year = builder.create();

        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(list_year);
        }

    }

    public String getSelectYearText() {
        return ("" + generalFunc.retrieveLangLBl("", "LBL_CHOOSE_YEAR"));
    }

    private void setData() {
        totalearnVTxt.setText(TotalEarning);
        noTripVTxt.setText((TripCount));
        yearBox.setText(selectedyear);
        buildLanguageList();

    }

    public void getChartDetails() {

        loaderView.setVisibility(View.VISIBLE);
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }


        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getYearTotalEarnings");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("year", selectedyear);


        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                if (responseString != null && !responseString.equals("")) {
                    yearSelectArea.setVisibility(View.VISIBLE);
                    bottomarea.setVisibility(View.VISIBLE);

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {
                        TripCount = generalFunc.getJsonValue("TripCount", responseString);
                        String MaxEarning = generalFunc.getJsonValue("MaxEarning", responseString);
                        TotalEarning = generalFunc.getJsonValue("TotalEarning", responseString);
                        selectedyear = generalFunc.getJsonValue("CurrentYear", responseString);
                        JSONArray YearMonthArr = generalFunc.getJsonArray("YearMonthArr", responseString);
                        listData.clear();
                        monthList.clear();
                        items_txt_year.clear();
                        for (int j = 0; j < YearMonthArr.length(); j++) {
                            JSONObject jsonObject = generalFunc.getJsonObject(YearMonthArr, j);

                            monthList.add(generalFunc.getJsonValue("CurrentMonth", jsonObject.toString()));

                            listData.add(jsonObject.optString("TotalEarnings"));
                        }
                        JSONArray yeararray = generalFunc.getJsonArray("YearArr", responseString);
                        for (int i = 0; i < yeararray.length(); i++) {
                            items_txt_year.add((String) generalFunc.getValueFromJsonArr(yeararray, i));
                        }


                        setData();

                        generateData();

                        chart.setViewportCalculationEnabled(false);

                        if (MaxEarning.equals("0")) {
                            MaxEarning = "1";
                        }
                        chart.setVisibility(View.VISIBLE);
                        loaderView.setVisibility(View.GONE);
                        resetViewport(generalFunc.parseFloatValue(0, MaxEarning));

                    } else {

                    }
                } else {
                    generateErrorView();
                    loaderView.setVisibility(View.GONE);
                }
            }


        });
        exeWebServer.execute();
    }


    public class setOnTouchList implements View.OnTouchListener {

        @Override
        public boolean onTouch(View view, MotionEvent motionEvent) {
            if (motionEvent.getAction() == MotionEvent.ACTION_UP && !view.hasFocus()) {
                view.performClick();
            }
            return true;
        }
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(StatisticsActivity.this);

            if (i == R.id.backImgView) {
                StatisticsActivity.super.onBackPressed();
            } else if (i == R.id.yearBox) {
                list_year.show();

            }
        }
    }
}
