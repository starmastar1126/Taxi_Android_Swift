package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Build;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.ProgressBar;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.MTextView;
import com.view.pinnedListView.CountryListItem;
import com.view.pinnedListView.PinnedSectionListAdapter;
import com.view.pinnedListView.PinnedSectionListView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class SelectCountryActivity extends AppCompatActivity implements PinnedSectionListAdapter.CountryClick {

    ArrayList<CountryListItem> items_list;
    MTextView titleTxt;
    ImageView backImgView;
    GeneralFunctions generalFunc;
    ProgressBar loading;
    ErrorView errorView;
    MTextView noResTxt;
    PinnedSectionListView country_list;
    PinnedSectionListAdapter pinnedSectionListAdapter;
    private CountryListItem[] sections;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_select_country);

        generalFunc = new GeneralFunctions(getActContext());

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        noResTxt = (MTextView) findViewById(R.id.noResTxt);
        loading = (ProgressBar) findViewById(R.id.loading);
        errorView = (ErrorView) findViewById(R.id.errorView);
        country_list = (PinnedSectionListView) findViewById(R.id.country_list);
        country_list.setShadowVisible(true);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB) {
            country_list.setFastScrollEnabled(true);
            country_list.setFastScrollAlwaysVisible(true);
        }
        items_list = new ArrayList<>();

        setLabels();

        backImgView.setOnClickListener(new setOnClickList());
        getCountryList();
    }


    @Override
    public void countryClickList(CountryListItem countryListItem) {
        Bundle bn = new Bundle();
        bn.putString("vCountry", countryListItem.text);
        bn.putString("vCountryCode", countryListItem.getvCountryCode());
        bn.putString("vPhoneCode", countryListItem.getvPhoneCode());
        new StartActProcess(getActContext()).setOkResult(bn);
        finish();
    }

    public Context getActContext() {
        return SelectCountryActivity.this;
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_SELECT_CONTRY"));
    }

    public void getCountryList() {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (loading.getVisibility() != View.VISIBLE) {
            loading.setVisibility(View.VISIBLE);
        }

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "countryList");

        noResTxt.setVisibility(View.GONE);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(),parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                noResTxt.setVisibility(View.GONE);

                if (responseString != null && !responseString.equals("")) {

                    closeLoader();

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {

                        items_list.clear();

                        sections = new CountryListItem[generalFunc.parseIntegerValue(0, generalFunc.getJsonValue("totalValues", responseString))];
                        pinnedSectionListAdapter = new PinnedSectionListAdapter(getActContext(), items_list, sections);
                        country_list.setAdapter(pinnedSectionListAdapter);

                        pinnedSectionListAdapter.setCountryClickListener(SelectCountryActivity.this);
                        items_list.clear();
                        pinnedSectionListAdapter.notifyDataSetChanged();

                        JSONArray countryArr = generalFunc.getJsonArray("CountryList", responseString);

                        int sectionPosition = 0, listPosition = 0;
                        for (int i = 0; i < countryArr.length(); i++) {
                            JSONObject tempJson = generalFunc.getJsonObject(countryArr, i);

                            String key_str = generalFunc.getJsonValue("key", tempJson.toString());
                            String count_str = generalFunc.getJsonValue("TotalCount", tempJson.toString());

                            CountryListItem section = new CountryListItem(CountryListItem.SECTION, key_str);
                            section.sectionPosition = sectionPosition;
                            section.listPosition = listPosition++;
                            section.CountSubItems = generalFunc.parseIntegerValue(0, count_str);
                            onSectionAdded(section, sectionPosition);
                            items_list.add(section);

                            JSONArray subListArr = generalFunc.getJsonArray("List", tempJson.toString());

                            for (int j = 0; j < subListArr.length(); j++) {
                                JSONObject subTempJson = generalFunc.getJsonObject(subListArr, j);

                                CountryListItem countryListItem = new CountryListItem(CountryListItem.ITEM, generalFunc.getJsonValue("vCountry", subTempJson.toString()));
                                countryListItem.sectionPosition = sectionPosition;
                                countryListItem.listPosition = listPosition++;
                                countryListItem.setvCountryCode(generalFunc.getJsonValue("vCountryCode", subTempJson.toString()));
                                countryListItem.setvPhoneCode(generalFunc.getJsonValue("vPhoneCode", subTempJson.toString()));
                                countryListItem.setiCountryId(generalFunc.getJsonValue("iCountryId", subTempJson.toString()));
                                items_list.add(countryListItem);
                            }

                            sectionPosition++;
                        }
                        pinnedSectionListAdapter.notifyDataSetChanged();
                    } else {
                        noResTxt.setText("");
                        noResTxt.setVisibility(View.VISIBLE);
                    }
                } else {
                    generateErrorView();
                }
            }
        });
        exeWebServer.execute();
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
                getCountryList();
            }
        });
    }

    protected void onSectionAdded(CountryListItem section, int sectionPosition) {
        sections[sectionPosition] = section;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(SelectCountryActivity.this);
            switch (view.getId()) {
                case R.id.backImgView:
                    SelectCountryActivity.super.onBackPressed();
                    break;

            }
        }
    }
}

