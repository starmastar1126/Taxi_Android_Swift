package com.fastcabtaxi.driver;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.RecyclerView;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.widget.ImageView;
import android.widget.ProgressBar;

import com.adapter.files.ListOfDocAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.GenerateAlertBox;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

import okhttp3.internal.Util;

public class ListOfDocumentActivity extends AppCompatActivity implements ListOfDocAdapter.OnItemClickListener {

    String PAGE_TYPE = "Driver";

    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;
    ProgressBar loading;
    MTextView noDocumentsListTxt;

    RecyclerView listOfDocRecyclerView;
    ErrorView errorView;
    ListOfDocAdapter adapter;
    ArrayList<HashMap<String, String>> list;

    boolean isBtnClick = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list_of_document);

        Toolbar mToolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(mToolbar);

        generalFunc = new GeneralFunctions(getActContext());
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);

        loading = (ProgressBar) findViewById(R.id.loading);
        noDocumentsListTxt = (MTextView) findViewById(R.id.noDocumentsListTxt);
        listOfDocRecyclerView = (RecyclerView) findViewById(R.id.listOfDocRecyclerView);
        errorView = (ErrorView) findViewById(R.id.errorView);

        list = new ArrayList<>();

        adapter = new ListOfDocAdapter(getActContext(), list, generalFunc, false);
        listOfDocRecyclerView.setAdapter(adapter);
        backImgView.setOnClickListener(new setOnClickList());

        adapter.setOnItemClickListener(this);
        Utils.printLog("PAGE_TYPE", ":" + getIntent().getStringExtra("PAGE_TYPE"));
        PAGE_TYPE = getIntent().getStringExtra("PAGE_TYPE");

        getDocList();
        setLabels();
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_SELECT_DOC"));
    }

    public Context getActContext() {
        return ListOfDocumentActivity.this;
    }

    public void getDocList() {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (loading.getVisibility() != View.VISIBLE) {
            loading.setVisibility(View.VISIBLE);
        }

        list.clear();
        adapter.notifyDataSetChanged();

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "displayDocList");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("doc_usertype", PAGE_TYPE);
        Utils.printLog("vehicleId::", getIntent().getStringExtra("iDriverVehicleId"));

        if (!getIntent().getStringExtra("iDriverVehicleId").equals("")) {
            parameters.put("iDriverVehicleId", getIntent().getStringExtra("iDriverVehicleId"));

        }
        noDocumentsListTxt.setVisibility(View.GONE);

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                noDocumentsListTxt.setVisibility(View.GONE);

                Utils.printLog("responseString", "responseString:" + responseString);
                if (responseString != null && !responseString.equals("")) {

                    closeLoader();
                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString)) {

                        JSONArray arr_rides = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

                        if (arr_rides != null && arr_rides.length() > 0) {
                            for (int i = 0; i < arr_rides.length(); i++) {
                                JSONObject obj_temp = generalFunc.getJsonObject(arr_rides, i);

                                HashMap<String, String> map = new HashMap<String, String>();

                                map.put("doc_id", generalFunc.getJsonValue("doc_id", obj_temp.toString()));
                                map.put("doc_name", generalFunc.getJsonValue("doc_name", obj_temp.toString()));
                                map.put("doc_masterid", generalFunc.getJsonValue("masterid", obj_temp.toString()));
                                map.put("ex_date", generalFunc.getJsonValue("ex_date", obj_temp.toString()));
                                map.put("ex_status", generalFunc.getJsonValue("ex_status", obj_temp.toString()));
                                map.put("vimage", generalFunc.getJsonValue("vimage", obj_temp.toString()));
                                map.put("doc_file", generalFunc.getJsonValue("doc_file", obj_temp.toString()));
                                map.put("EXPIRE_DOCUMENT", generalFunc.getJsonValue("EXPIRE_DOCUMENT", obj_temp.toString()));
                                map.put("LBL_MANAGE", generalFunc.retrieveLangLBl("Manage", "LBL_MANAGE"));
                                map.put("LBL_UPLOAD_DOC", generalFunc.retrieveLangLBl("Upload document", "LBL_UPLOAD_DOC"));
                                map.put("LBL_MISSING_TXT", generalFunc.retrieveLangLBl("Missing", "LBL_MISSING_TXT"));
                                map.put("LBL_EXPIRED_TXT", generalFunc.retrieveLangLBl("Expired", "LBL_EXPIRED_TXT"));

                                map.put("JSON", obj_temp.toString());
                                list.add(map);

                            }
                        }
//                        adapter = new ListOfDocAdapter(getActContext(), list, generalFunc, false);
//                        listOfDocRecyclerView.setAdapter(adapter);
                        adapter.notifyDataSetChanged();
                    } else {

                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                if (btn_id == 0) {
                                    generateAlert.closeAlertBox();
                                    Bundle bn = new Bundle();
                                    bn.putBoolean("isListEmpty", false);
                                    new StartActProcess(getActContext()).setOkResult(bn);
                                    backImgView.performClick();
                                } else if (btn_id == 1) {
                                    Intent intent = new Intent(ListOfDocumentActivity.this, ContactUsActivity.class);
                                    intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_CLEAR_TOP);
                                    startActivity(intent);
                                    finish();

                                }
                            }
                        });

                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_ADD_VEHICLES"));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_CONTACT_US_TXT"));

                        generateAlert.showAlertBox();

                        noDocumentsListTxt.setText(generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        noDocumentsListTxt.setVisibility(View.VISIBLE);

                    }
                } else {
                    noDocumentsListTxt.setText(generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    noDocumentsListTxt.setVisibility(View.VISIBLE);

                    closeLoader();
                    generalFunc.showError();
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
                getDocList();
            }
        });
    }

    @Override
    protected void onResume() {
        super.onResume();
        isBtnClick = false;
    }

    @Override
    public void onItemClickList(int position) {

        if (!isBtnClick) {
            isBtnClick = true;
            Bundle bn = new Bundle();
            bn.putString("PAGE_TYPE", getIntent().getStringExtra("PAGE_TYPE"));

            bn.putString("vLicencePlate", getIntent().getStringExtra("vLicencePlate"));
            bn.putString("eStatus", getIntent().getStringExtra("eStatus"));
            bn.putString("vMake", getIntent().getStringExtra("vMake"));
            bn.putString("iDriverVehicleId", getIntent().getStringExtra("iDriverVehicleId"));
            bn.putString("vCarType", getIntent().getStringExtra("vCarType"));
            bn.putString("iMakeId", getIntent().getStringExtra("iMakeId"));
            bn.putString("iYear", getIntent().getStringExtra("iYear"));
            bn.putString("iModelId", getIntent().getStringExtra("iModelId"));
            bn.putString("vColour", getIntent().getStringExtra("vColour"));

            bn.putString("ex_status", list.get(position).get("ex_status"));
            bn.putString("doc_masterid", list.get(position).get("doc_masterid"));
            bn.putString("ex_date", list.get(position).get("ex_date"));
            bn.putString("doc_id", list.get(position).get("doc_id"));
            bn.putString("doc_name", list.get(position).get("doc_name"));
            bn.putString("doc_file", list.get(position).get("doc_file"));

            new StartActProcess(getActContext()).startActForResult(UploadDocActivity.class, bn, Utils.UPLOAD_DOC_REQ_CODE);
        }
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(ListOfDocumentActivity.this);
            switch (view.getId()) {
                case R.id.backImgView:
                    ListOfDocumentActivity.super.onBackPressed();
                    break;
            }
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == Utils.UPLOAD_DOC_REQ_CODE && resultCode == RESULT_OK) {
            getDocList();
        }
    }
}
