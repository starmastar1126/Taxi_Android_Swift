package com.fastcabtaxi.passenger;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;

import com.adapter.files.AddressListAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class ListAddressActivity extends AppCompatActivity implements AddressListAdapter.ItemClickListener {


    GeneralFunctions generalFunc;
    ImageView backImgView;


    ProgressBar addressListPageLoader;
    MTextView titleTxt;
    MTextView noAddrTxt;
    MTextView chooseAddrTxtView;
    RecyclerView AddrListRecyclerView;
    AddressListAdapter addressListAdapterobj;
    LinearLayout addDeliveryArea;

    ImageView rightImgView;

    ArrayList<HashMap<String, String>> addrList = new ArrayList<HashMap<String, String>>();
    String type = "";
    String SelectedVehicleTypeId = "";
    String quantity = "0";


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_list_address);

        generalFunc = new GeneralFunctions(getActContext());
        type = getIntent().getStringExtra("type");
        SelectedVehicleTypeId = getIntent().getStringExtra("SelectedVehicleTypeId");
        quantity = getIntent().getStringExtra("Quantity");


        backImgView = (ImageView) findViewById(R.id.backImgView);
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        noAddrTxt = (MTextView) findViewById(R.id.noAddrTxt);
        chooseAddrTxtView = (MTextView) findViewById(R.id.chooseAddrTxtView);
        addDeliveryArea = (LinearLayout) findViewById(R.id.addDeliveryArea);
        addressListPageLoader = (ProgressBar) findViewById(R.id.addressListPageLoader);
        AddrListRecyclerView = (RecyclerView) findViewById(R.id.AddrListRecyclerView);
        rightImgView = (ImageView) findViewById(R.id.rightImgView);


        addressListAdapterobj = new AddressListAdapter(getActContext(), addrList, generalFunc);
        AddrListRecyclerView.setAdapter(addressListAdapterobj);


        addressListAdapterobj.onClickListener(this);

        backImgView.setOnClickListener(new setOnClick());
        addDeliveryArea.setOnClickListener(new setOnClick());
        rightImgView.setOnClickListener(new setOnClick());

        rightImgView.setVisibility(View.VISIBLE);
        setLabel();
    }

    private void setLabel() {
        titleTxt.setText(generalFunc.retrieveLangLBl("Select Address", "LBL_SELECT_ADDRESS_TITLE_TXT"));
        chooseAddrTxtView.setText(generalFunc.retrieveLangLBl("Choose Address", "LBL_CHOOSE_ADDRESS_HINT_INFO"));

    }

    @Override
    protected void onResume() {
        super.onResume();
        getAddrDetail();
    }

    private void getAddrDetail() {
        addressListPageLoader.setVisibility(View.VISIBLE);
        noAddrTxt.setVisibility(View.GONE);
        addrList.clear();
        addressListAdapterobj.notifyDataSetChanged();


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "DisplayUserAddress");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("eUserType", CommonUtilities.app_type);
        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("AddResponse", "::" + responseString);
                noAddrTxt.setVisibility(View.GONE);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        JSONArray message_arr = generalFunc.getJsonArray("message", responseString);

                        if (message_arr != null && message_arr.length() > 0) {
                            addrList.clear();

                            for (int i = 0; i < message_arr.length(); i++) {
                                JSONObject addr_obj = generalFunc.getJsonObject(message_arr, i);

                                HashMap<String, String> map = new HashMap<String, String>();
                                map.put("vServiceAddress", generalFunc.getJsonValue("vServiceAddress", addr_obj.toString()));
                                map.put("iUserAddressId", generalFunc.getJsonValue("iUserAddressId", addr_obj.toString()));
                                map.put("vBuildingNo", generalFunc.getJsonValue("vBuildingNo", addr_obj.toString()));
                                map.put("vLandmark", generalFunc.getJsonValue("vLandmark", addr_obj.toString()));
                                map.put("vAddressType", generalFunc.getJsonValue("vAddressType", addr_obj.toString()));
                                map.put("vLatitude", generalFunc.getJsonValue("vLatitude", addr_obj.toString()));
                                map.put("vLongitude", generalFunc.getJsonValue("vLongitude", addr_obj.toString()));
                                map.put("eStatus", generalFunc.getJsonValue("eStatus", addr_obj.toString()));
                                map.put("isSelected", "false");
                                addrList.add(map);
                            }
                            addressListAdapterobj.notifyDataSetChanged();
                            addressListPageLoader.setVisibility(View.GONE);

                        }
                    } else {
                        if (addrList.size() == 0) {
                            noAddrTxt.setVisibility(View.VISIBLE);
                            noAddrTxt.setText(generalFunc.retrieveLangLBl("", "LBL_NO_ADDRESS_TXT"));
                            addressListPageLoader.setVisibility(View.GONE);
                        }
                        finish();
                    }
                } else {


                }
            }
        });
        exeWebServer.execute();
    }

    public void Checkuseraddressrestriction(String iAddressId, String selectedVehicleTypeId, final String type, final int position) {
        final Bundle bundle = new Bundle();

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "Checkuseraddressrestriction");
        parameters.put("iUserAddressId", iAddressId);
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("iSelectVehicalId", selectedVehicleTypeId);
        parameters.put("eUserType", CommonUtilities.app_type);


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail) {


                        if (type.equalsIgnoreCase(Utils.CabReqType_Later)) {
                            bundle.putString("latitude", addrList.get(position).get("vLatitude"));
                            bundle.putString("longitude", addrList.get(position).get("vLongitude"));
                            bundle.putString("address", addrList.get(position).get("vServiceAddress"));
                            bundle.putString("iUserAddressId", addrList.get(position).get("iUserAddressId"));
                            bundle.putString("SelectedVehicleTypeId", SelectedVehicleTypeId);
                            bundle.putString("SelectvVehicleType", getIntent().getStringExtra("SelectvVehicleType"));
                            bundle.putString("SelectvVehiclePrice", getIntent().getStringExtra("SelectvVehiclePrice"));
                            if (addrList.get(position).get("vAddressType") != null && !addrList.get(position).get("vAddressType").equals("")) {
                                String tempadd = addrList.get(position).get("vAddressType") + "\n" + addrList.get(position).get("vBuildingNo") + ", " + addrList.get(position).get("vLandmark") + "\n" + addrList.get(position).get("vServiceAddress");
                                bundle.putString("address", tempadd);
                            } else {
                                String tempadd = addrList.get(position).get("vBuildingNo") + ", " + addrList.get(position).get("vLandmark") + "\n" + addrList.get(position).get("vServiceAddress");
                                bundle.putString("address", tempadd);
                            }
                            bundle.putString("Quantity", quantity);
                            bundle.putString("Quantityprice", getIntent().getStringExtra("Quantityprice"));
                            bundle.putString("SelectedVehicleTypeId", SelectedVehicleTypeId);
                            new StartActProcess(getActContext()).startActWithData(ScheduleDateSelectActivity.class, bundle);

                        } else {
                            bundle.putBoolean("isufx", true);
                            bundle.putString("latitude", addrList.get(position).get("vLatitude"));
                            bundle.putString("longitude", addrList.get(position).get("vLongitude"));
                            bundle.putString("address", addrList.get(position).get("vServiceAddress"));
                            bundle.putString("SelectedVehicleTypeId", SelectedVehicleTypeId);
                            bundle.putString("iUserAddressId", addrList.get(position).get("iUserAddressId"));
                            bundle.putString("SelectvVehicleType", getIntent().getStringExtra("SelectvVehicleType"));
                            bundle.putString("SelectvVehiclePrice", getIntent().getStringExtra("SelectvVehiclePrice"));
                            bundle.putString("Quantity", quantity);
                            bundle.putString("Quantityprice", getIntent().getStringExtra("Quantityprice"));
                            bundle.putString("type", Utils.CabReqType_Now);
                            Utils.printLog("ActivityLoadTrack", "ListAddAct 2:" + System.currentTimeMillis());
                            bundle.putString("Sdate", "");
                            bundle.putString("Stime", "");

                            new StartActProcess(getActContext()).startActWithData(MainActivity.class, bundle);

                        }


                    } else {

                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                generateAlert.closeAlertBox();

                                finish();

                            }
                        });
                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));

                        generateAlert.showAlertBox();


                    }
                } else {

                }
            }
        });
        exeWebServer.execute();


    }

    public void removeAddressApi(String iAddressId) {


        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "DeleteUserAddressDetail");
        parameters.put("iUserAddressId", iAddressId);
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("eUserType", CommonUtilities.app_type);


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, generalFunc.getJsonValue(CommonUtilities.message_str, responseString));

                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                generateAlert.closeAlertBox();

                                String userprofileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
                                if (generalFunc.getJsonValue("ToTalAddress", userprofileJson).equalsIgnoreCase("0")) {
                                    finish();
                                } else {
                                    getAddrDetail();
                                }

                            }
                        });
                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str_one, responseString)));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));

                        generateAlert.showAlertBox();


                    } else {

                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));

                    }
                } else {

                }
            }
        });
        exeWebServer.execute();
    }

    public Context getActContext() {
        return ListAddressActivity.this;
    }

    @Override
    public void setOnClick(int position) {
        Utils.printLog("ActivityLoadTrack", "ListAddAct 1:" + System.currentTimeMillis());

        if (type.equals(Utils.CabReqType_Later)) {

            Checkuseraddressrestriction(addrList.get(position).get("iUserAddressId"), SelectedVehicleTypeId, Utils.CabReqType_Later, position);


        } else {
            Checkuseraddressrestriction(addrList.get(position).get("iUserAddressId"), SelectedVehicleTypeId, Utils.CabReqType_Now, position);

        }

    }

    @Override
    public void setOnDeleteClick(final int position) {


        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();
                if (btn_id == 1) {

                    removeAddressApi(addrList.get(position).get("iUserAddressId"));

                } else {
                }
            }
        });
        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("Are you sure want to delete", "LBL_DELETE_CONFIRM_MSG"));
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("Ok", "LBL_BTN_OK_TXT"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("cANCEL", "LBL_CANCEL_TXT"));
        generateAlert.showAlertBox();


    }

    public class setOnClick implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            if (i == R.id.backImgView) {
                ListAddressActivity.super.onBackPressed();
            } else if (i == R.id.addDeliveryArea) {

                Bundle bundle = new Bundle();
                bundle.putBoolean("isufx", true);
                bundle.putString("latitude", getIntent().getStringExtra("latitude"));
                bundle.putString("longitude", getIntent().getStringExtra("longitude"));
                bundle.putString("address", getIntent().getStringExtra("address"));
                bundle.putString("type", type);

                bundle.putString("Quantity", quantity);
                bundle.putString("SelectedVehicleTypeId", SelectedVehicleTypeId);
                bundle.putString("Quantityprice", getIntent().getStringExtra("Quantityprice"));

                new StartActProcess(getActContext()).startActWithData(AddAddressActivity.class, bundle);


            } else if (i == R.id.rightImgView) {
                addDeliveryArea.performClick();

            }
        }
    }
}
