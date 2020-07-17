package com.fastcabtaxi.driver;

import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.database.DatabaseUtils;
import android.net.Uri;
import android.os.Bundle;
import android.provider.ContactsContract;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;

import com.adapter.files.EmergencyContactRecycleAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class EmergencyContactActivity extends AppCompatActivity implements EmergencyContactRecycleAdapter.OnItemClickList {

    int PICK_CONTACT = 2121;

    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;

    MButton btn_type2;

    ProgressBar loading;
    RelativeLayout dataContainer;
    LinearLayout noContactArea;

    RecyclerView emeContactRecyclerView;
    EmergencyContactRecycleAdapter adapter;
    ErrorView errorView;

    ArrayList<HashMap<String, String>> list;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_emergency_contact);

        generalFunc = new GeneralFunctions(getActContext());


        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        loading = (ProgressBar) findViewById(R.id.loading);
        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        emeContactRecyclerView = (RecyclerView) findViewById(R.id.emeContactRecyclerView);
        errorView = (ErrorView) findViewById(R.id.errorView);
        dataContainer = (RelativeLayout) findViewById(R.id.dataContainer);
        noContactArea = (LinearLayout) findViewById(R.id.noContactArea);

        list = new ArrayList<>();
        adapter = new EmergencyContactRecycleAdapter(getActContext(), list);
        emeContactRecyclerView.setAdapter(adapter);

        setLabels();

        btn_type2.setId(Utils.generateViewId());
        btn_type2.setOnClickListener(new setOnClickList());
        backImgView.setOnClickListener(new setOnClickList());

        getContacts();

        adapter.setOnItemClickList(this);
    }

    @Override
    public void onItemClick(int position) {
        buildWarningMessage(list.get(position).get("iEmergencyId"));
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_EMERGENCY_CONTACT"));

        ((MTextView) findViewById(R.id.emeTitleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_EMERGENCY_CONTACT_TITLE"));
        ((MTextView) findViewById(R.id.emeSubTitleTxt1)).setText(generalFunc.retrieveLangLBl("", "LBL_EMERGENCY_CONTACT_SUB_TITLE1"));
        ((MTextView) findViewById(R.id.emeSubTitleTxt2)).setText(generalFunc.retrieveLangLBl("", "LBL_EMERGENCY_CONTACT_SUB_TITLE2"));
        ((MTextView) findViewById(R.id.notifyTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_ADD_EMERGENCY_UP_TO_COUNT"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_CONTACTS"));

    }

    public void closeLoader() {
        if (loading.getVisibility() == View.VISIBLE) {
            loading.setVisibility(View.GONE);
        }
    }

    public void getContacts() {
        (findViewById(R.id.btn_type2)).setVisibility(View.GONE);
        (findViewById(R.id.notifyTxt)).setVisibility(View.GONE);
        dataContainer.setVisibility(View.VISIBLE);
        noContactArea.setVisibility(View.GONE);

        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (loading.getVisibility() != View.VISIBLE) {
            loading.setVisibility(View.VISIBLE);
        }

        if (list.size() > 0) {
            list.clear();
        }

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "loadEmergencyContacts");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("UserType", Utils.userType);


        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(),parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                noContactArea.setVisibility(View.GONE);

                if (responseString != null && !responseString.equals("")) {

                    closeLoader();

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {

                        JSONArray obj_arr = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

                        for (int i = 0; i < obj_arr.length(); i++) {
                            JSONObject obj_temp = generalFunc.getJsonObject(obj_arr, i);

                            HashMap<String, String> map = new HashMap<String, String>();

                            map.put("ContactName", generalFunc.getJsonValue("vName", obj_temp.toString()));
                            map.put("ContactPhone", generalFunc.getJsonValue("vPhone", obj_temp.toString()));
                            map.put("iEmergencyId", generalFunc.getJsonValue("iEmergencyId", obj_temp.toString()));

                            list.add(map);
                        }

                        adapter.notifyDataSetChanged();

                        if (obj_arr.length() >= 5) {
                            (findViewById(R.id.notifyTxt)).setVisibility(View.GONE);
                            (findViewById(R.id.btn_type2)).setVisibility(View.GONE);
                        } else {
                            (findViewById(R.id.notifyTxt)).setVisibility(View.VISIBLE);
                            (findViewById(R.id.btn_type2)).setVisibility(View.VISIBLE);
                        }

                    } else {
                        noContactArea.setVisibility(View.VISIBLE);
                        dataContainer.setVisibility(View.GONE);

                        (findViewById(R.id.notifyTxt)).setVisibility(View.VISIBLE);
                        (findViewById(R.id.btn_type2)).setVisibility(View.VISIBLE);
                    }
                } else {
                    generateErrorView();
                }
            }
        });
        exeWebServer.execute();
    }

    public void addContact(String contactName, String contactPhone) {
        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "addEmergencyContacts");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("vName", contactName);
        parameters.put("Phone", contactPhone);
        parameters.put("UserType", Utils.userType);

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(),parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {
                    Utils.printLog("responseString", "responseString:" + responseString);

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {
                        getContacts();

                        generalFunc.showMessage(generalFunc.getCurrentView(EmergencyContactActivity.this),
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

    public void buildWarningMessage(final String iEmergencyId) {
        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();

                if (btn_id == 1) {
                    deleteContact(iEmergencyId);
                }
            }
        });
        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", "LBL_CONFIRM_MSG_DELETE_EME_CONTACT"));
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));


        generateAlert.showAlertBox();
    }

    public void deleteContact(String iEmergencyId) {
        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "deleteEmergencyContacts");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("iEmergencyId", iEmergencyId);
        parameters.put("UserType", Utils.userType);

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(),parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {
                    Utils.printLog("responseString", "responseString:" + responseString);

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {
                        getContacts();

                        generalFunc.showMessage(generalFunc.getCurrentView(EmergencyContactActivity.this),
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

    public void generateErrorView() {

        closeLoader();

        generalFunc.generateErrorView(errorView, "LBL_ERROR_TXT", "LBL_NO_INTERNET_TXT");

        if (errorView.getVisibility() != View.VISIBLE) {
            errorView.setVisibility(View.VISIBLE);
        }
        errorView.setOnRetryListener(new ErrorView.RetryListener() {
            @Override
            public void onRetry() {
                getContacts();
            }
        });
    }

    public Context getActContext() {
        return EmergencyContactActivity.this;
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == PICK_CONTACT && data != null) {
            Uri uri = data.getData();

            if (uri != null) {
                Cursor c = null;

                try {
                    c = getContentResolver().query(uri, new String[]{ContactsContract.CommonDataKinds.Phone.NUMBER, ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME,
                            ContactsContract.CommonDataKinds.Phone.TYPE}, null, null, null);

                    if (c != null && c.moveToFirst()) {
                        String number = c.getString(c.getColumnIndex(ContactsContract.CommonDataKinds.Phone.NUMBER));
                        String name = c.getString(c.getColumnIndex(ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME));

                        Utils.printLog("Cursor Object", "::" + DatabaseUtils.dumpCursorToString(c));
                        Utils.printLog("number ", "::" + number);
                        Utils.printLog("number ", "::" + c.getString(c.getColumnIndex(ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME)));

                        addContact(name, number);
//                        Intent smsIntent = new Intent(Intent.ACTION_VIEW);
//                        smsIntent.setType("vnd.android-dir/mms-sms");
//                        smsIntent.putExtra("address", "" + number);
//
//                        String link_location = "http://maps.google.com/?q=" + mainAct.userLocation.getLatitude() + "," + mainAct.userLocation.getLongitude();
//                        smsIntent.putExtra("sms_body", generalFunc.retrieveLangLBl("", "LBL_SEND_STATUS_CONTENT_TXT") + " " + link_location);
//                        startActivity(smsIntent);
                    }
                } finally {
                    if (c != null) {
                        c.close();
                    }
                }
            }

        }
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(EmergencyContactActivity.this);
            if (i == R.id.backImgView) {
                EmergencyContactActivity.super.onBackPressed();

            } else if (i == btn_type2.getId()) {
                Intent intent = new Intent(Intent.ACTION_PICK);
                intent.setType(ContactsContract.Contacts.CONTENT_TYPE);
                intent.setType(ContactsContract.CommonDataKinds.Phone.CONTENT_TYPE);
                startActivityForResult(intent, PICK_CONTACT);
            }
        }
    }

}
