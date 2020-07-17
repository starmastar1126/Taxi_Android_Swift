package com.fastcabtaxi.driver;

import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.AppCompatCheckBox;
import android.support.v7.widget.Toolbar;
import android.text.InputFilter;
import android.text.Spanned;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.SetOnTouchList;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;

public class AddVehicleActivity extends AppCompatActivity {

    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;
    String[] vCarTypes = null;
    MButton submitVehicleBtn;
    MaterialEditText makeBox, modelBox, yearBox, licencePlateBox, colorPlateBox, vehicleTypeBox;

    ArrayList<String> dataList = new ArrayList<>();
    android.support.v7.app.AlertDialog list_make;
    android.support.v7.app.AlertDialog list_model;
    android.support.v7.app.AlertDialog list_year;
    android.support.v7.app.AlertDialog list_vehicleType;

    LinearLayout serviceSelectArea;

    String iSelectedMakeId = "";
    String iSelectedModelId = "";


    int iSelectedMakePosition = 0;

    JSONArray year_arr;
    JSONArray vehicletypelist;

    ArrayList<Boolean> carTypesStatusArr;

    String iDriverVehicleId = "";
    CheckBox checkboxHandicap;
    boolean ishandicapavilabel = false;
    LinearLayout NotInUFXView;
    String app_type = "";
    String userProfileJson = "";
    String selectedtype = "";

    FrameLayout vehicleTypeArea;

    String ENABLE_EDIT_DRIVER_VEHICLE = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_add_vehicle);

        Toolbar mToolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(mToolbar);

        generalFunc = new GeneralFunctions(getActContext());

        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

        ENABLE_EDIT_DRIVER_VEHICLE = generalFunc.getJsonValue("ENABLE_EDIT_DRIVER_VEHICLE", userProfileJson);
        Utils.printLog("ENABLE_EDIT_DRIVER_VEHICLE", "" + ENABLE_EDIT_DRIVER_VEHICLE);

        backImgView = (ImageView) findViewById(R.id.backImgView);
        checkboxHandicap = (CheckBox) findViewById(R.id.checkboxHandicap);
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        serviceSelectArea = (LinearLayout) findViewById(R.id.serviceSelectArea);

        submitVehicleBtn = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();

        NotInUFXView = (LinearLayout) findViewById(R.id.NotInUFXView);

        makeBox = (MaterialEditText) findViewById(R.id.makeBox);
        modelBox = (MaterialEditText) findViewById(R.id.modelBox);
        yearBox = (MaterialEditText) findViewById(R.id.yearBox);
        licencePlateBox = (MaterialEditText) findViewById(R.id.licencePlateBox);
        colorPlateBox = (MaterialEditText) findViewById(R.id.colorPlateBox);
        vehicleTypeArea = (FrameLayout) findViewById(R.id.vehicleTypeArea);
        vehicleTypeBox = (MaterialEditText) findViewById(R.id.vehicleTypeBox);
        app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);

        selectedtype = app_type;

        if (!app_type.equals(Utils.CabGeneralType_UberX)) {
            String isHadicap = generalFunc.getJsonValue("HANDICAP_ACCESSIBILITY_OPTION", userProfileJson);
            if (isHadicap != null && !isHadicap.equals("")) {
                if (isHadicap.equalsIgnoreCase("Yes")) {
                    checkboxHandicap.setVisibility(View.VISIBLE);
                } else {
                    checkboxHandicap.setVisibility(View.GONE);
                }
            } else {
                checkboxHandicap.setVisibility(View.GONE);
            }
        } else {
            checkboxHandicap.setVisibility(View.GONE);
        }


        InputFilter filter = new InputFilter() {
            @Override
            public CharSequence filter(CharSequence source, int start, int end,
                                       Spanned dest, int dstart, int dend) {
                for (int i = start; i < end; i++) {

                    if (Character.isSpaceChar(source.charAt(i))) {
                        return " ";
                    }


                    if (!Character.isLetterOrDigit(source.charAt(i))) {

                        return "";
                    }
                }
                return null;
            }
        };

        backImgView.setOnClickListener(new setOnClickList());

        setLabels();

        if (!app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
            if (getIntent().getStringExtra("iDriverVehicleId") != null) {
                iDriverVehicleId = getIntent().getStringExtra("iDriverVehicleId");
                iSelectedMakeId = getIntent().getStringExtra("iMakeId");
                iSelectedModelId = getIntent().getStringExtra("iModelId");
                String vLicencePlate = getIntent().getStringExtra("vLicencePlate");
                String vColour = getIntent().getStringExtra("vColour");
                String iYear = getIntent().getStringExtra("iYear");
                String hadicap = getIntent().getStringExtra("eHandiCapAccessibility");

                if (hadicap.equalsIgnoreCase("yes")) {
                    checkboxHandicap.setChecked(true);
                }

                licencePlateBox.setText(vLicencePlate.trim());
                colorPlateBox.setText(vColour);
                yearBox.setText(iYear);
            }
        } else {
            iDriverVehicleId = getIntent().getStringExtra("iDriverVehicleId");
        }

        licencePlateBox.setFilters(new InputFilter[]{filter});

        if (app_type.equals(Utils.CabGeneralTypeRide_Delivery)) {
            vehicleTypeArea.setVisibility(View.VISIBLE);
        } else {
            vehicleTypeArea.setVisibility(View.GONE);
        }


    }

    public void setLabels() {
        if (!app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
            if (getIntent().getStringExtra("isfrom") != null && getIntent().getStringExtra("isfrom").equalsIgnoreCase("edit")) {
                titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_EDIT_VEHICLE"));
            } else {
                titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_VEHICLE"));
                NotInUFXView.setVisibility(View.VISIBLE);
            }
        } else {
            titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_MANAGE_VEHICLES"));
            NotInUFXView.setVisibility(View.GONE);
        }


        submitVehicleBtn.setId(Utils.generateViewId());
        submitVehicleBtn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_SUBMIT_TXT"));

        makeBox.setBothText(generalFunc.retrieveLangLBl("Make", "LBL_MAKE"));
        modelBox.setBothText(generalFunc.retrieveLangLBl("Model", "LBL_MODEL"));
        yearBox.setBothText(generalFunc.retrieveLangLBl("Year", "LBL_YEAR"));
        licencePlateBox.setBothText(generalFunc.retrieveLangLBl("Licence", "LBL_LICENCE_PLATE_TXT"));
        colorPlateBox.setBothText(generalFunc.retrieveLangLBl("Color", "LBL_COLOR_TXT"));

        vehicleTypeBox.setBothText(generalFunc.retrieveLangLBl("Vehicle Type", "LBL_VEHICLE_TYPE_SMALL_TXT"));
        checkboxHandicap.setText(generalFunc.retrieveLangLBl("Handicap accessibility available?", "LBL_HANDICAP_QUESTION"));

        backImgView.setOnClickListener(new setOnClickList());
        submitVehicleBtn.setOnClickListener(new setOnClickList());


        removeInput();
        buildMakeList();
    }

    private void removeInput() {
        Utils.removeInput(makeBox);
        Utils.removeInput(modelBox);
        Utils.removeInput(yearBox);
        Utils.removeInput(vehicleTypeBox);

        makeBox.setOnTouchListener(new SetOnTouchList());
        modelBox.setOnTouchListener(new SetOnTouchList());
        yearBox.setOnTouchListener(new SetOnTouchList());


        makeBox.setOnClickListener(new setOnClickList());
        modelBox.setOnClickListener(new setOnClickList());
        yearBox.setOnClickListener(new setOnClickList());
        vehicleTypeBox.setOnClickListener(new setOnClickList());
        vehicleTypeBox.setOnTouchListener(new SetOnTouchList());

        vehicleTypeBox.setText(generalFunc.getJsonValue("APP_TYPE", userProfileJson));
        // vehicleTypeBox.setText(generalFunc.retrieveLangLBl("Ride_delivery", "LBL_RIDE_DELIVRY"));


    }

    public void buildMakeList() {
        dataList.clear();
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getUserVehicleDetails");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("eType", selectedtype);

        parameters.put("UserType", CommonUtilities.app_type);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {
                    dataList.clear();
                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        JSONObject message_obj = generalFunc.getJsonObject("message", responseString);
                        year_arr = generalFunc.getJsonArray("year", message_obj.toString());
                        vehicletypelist = generalFunc.getJsonArray("vehicletypelist", message_obj.toString());

                        if (vehicletypelist.length() == 0) {
                            final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                            generateAlert.setCancelable(false);
                            generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                                @Override
                                public void handleBtnClick(int btn_id) {
                                    if (btn_id == 0) {
                                        generateAlert.closeAlertBox();
                                        Bundle bn = new Bundle();
                                        bn.putBoolean("isContactus", false);
                                        new StartActProcess(getActContext()).setOkResult(bn);
                                        backImgView.performClick();
                                    } else if (btn_id == 1) {
                                        Bundle bn = new Bundle();
                                        bn.putBoolean("isContactus", true);
                                        new StartActProcess(getActContext()).setOkResult(bn);
                                        backImgView.performClick();

                                    }
                                }
                            });
                            generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str_one, responseString)));
                            generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
                            generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_CONTACT_US_TXT"));

                            generateAlert.showAlertBox();
                        }

                        JSONArray carList_arr;
                        dataList.clear();
                        if (message_obj.length() > 0 && message_obj != null) {
                            carList_arr = generalFunc.getJsonArray("carlist", message_obj.toString());

                            if (carList_arr != null) {
                                for (int i = 0; i < carList_arr.length(); i++) {

                                    JSONObject obj = generalFunc.getJsonObject(carList_arr, i);
                                    dataList.add(obj.toString());
                                }
                            }
                        }

                        buildMake();

                        buildServices();

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

    public void buildMake() {
        ArrayList<String> items = new ArrayList<String>();

        for (int i = 0; i < dataList.size(); i++) {
            items.add(generalFunc.getJsonValue("vMake", dataList.get(i)));

            String iMakeId = generalFunc.getJsonValue("iMakeId", dataList.get(i));
            if (!iSelectedMakeId.equals("") && iSelectedMakeId.equals(iMakeId)) {
                iSelectedMakePosition = i;
                makeBox.setText(generalFunc.getJsonValue("vMake", dataList.get(i)));

                buildModelList(false);
            }
        }

        CharSequence[] cs_currency_txt = items.toArray(new CharSequence[items.size()]);


        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle(generalFunc.retrieveLangLBl("Select Make", "LBL_SELECT_MAKE"));

        builder.setItems(cs_currency_txt, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int item) {
                // Do something with the selection

                if (list_make != null) {
                    list_make.dismiss();
                }

                modelBox.setText("");
                iSelectedModelId = "";

                modelBox.setBothText(generalFunc.retrieveLangLBl("Model", "LBL_MODEL"));

                makeBox.setText(generalFunc.getJsonValue("vMake", dataList.get(item)));
                iSelectedMakeId = generalFunc.getJsonValue("iMakeId", dataList.get(item));
                iSelectedMakePosition = item;

            }
        });

        list_make = builder.create();

        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(list_make);
        }
    }

    private void buildYear() {
        if (year_arr == null || year_arr.length() == 0) {
            return;
        }

        ArrayList<String> items = new ArrayList<String>();

        for (int i = 0; i < year_arr.length(); i++) {
            items.add((String) generalFunc.getValueFromJsonArr(year_arr, i));
        }

        CharSequence[] cs_currency_txt = items.toArray(new CharSequence[items.size()]);


        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle(generalFunc.retrieveLangLBl("Select Year", "LBL_SELECT_YEAR"));

        builder.setItems(cs_currency_txt, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int item) {
                // Do something with the selection

                if (list_year != null) {
                    list_year.dismiss();
                }
                yearBox.setText((String) generalFunc.getValueFromJsonArr(year_arr, item));

            }
        });

        list_year = builder.create();

        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(list_make);
        }

        list_year.show();
    }

    private void buildModelList(final boolean isShow) {

        ArrayList<String> items = new ArrayList<String>();

        JSONArray vModellistArr = generalFunc.getJsonArray("vModellist", dataList.get(iSelectedMakePosition));
        if (vModellistArr != null) {
            for (int i = 0; i < vModellistArr.length(); i++) {
                JSONObject obj_temp = generalFunc.getJsonObject(vModellistArr, i);

                items.add(generalFunc.getJsonValue("vTitle", obj_temp.toString()));

                String iModelId = generalFunc.getJsonValue("iModelId", obj_temp.toString());
                if (!iSelectedModelId.equals("") && iSelectedModelId.equals(iModelId)) {
                    modelBox.setText(generalFunc.getJsonValue("vTitle", obj_temp.toString()));
                }
            }

            CharSequence[] cs_currency_txt = items.toArray(new CharSequence[items.size()]);

            android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
            builder.setTitle(generalFunc.retrieveLangLBl("Select Models", "LBL_SELECT_MODEL"));

            builder.setItems(cs_currency_txt, new DialogInterface.OnClickListener() {
                public void onClick(DialogInterface dialog, int item) {
                    // Do something with the selection

                    if (list_make != null) {
                        list_make.dismiss();
                    }
                    JSONArray vModellistArr = generalFunc.getJsonArray("vModellist", dataList.get(iSelectedMakePosition));
                    JSONObject obj_temp = generalFunc.getJsonObject(vModellistArr, item);

                    modelBox.setText(generalFunc.getJsonValue("vTitle", obj_temp.toString()));
                    iSelectedModelId = generalFunc.getJsonValue("iModelId", obj_temp.toString());

                    if (!isShow) {
                        Utils.removeInput(modelBox);
                    }


                }
            });

            list_model = builder.create();

            if (generalFunc.isRTLmode() == true) {
                generalFunc.forceRTLIfSupported(list_model);
            }

            if (isShow) {
                list_model.show();
            }
        }

    }

    public void buildServices() {

        if (serviceSelectArea.getChildCount() > 0) {
            serviceSelectArea.removeAllViewsInLayout();
        }

        carTypesStatusArr = new ArrayList<>();

        String[] vCarTypes = {};


//        if (!iDriverVehicleId.equals("")) {

        if (getIntent().getStringExtra("vCarType") != null && !getIntent().getStringExtra("vCarType").equals("")) {
            vCarTypes = getIntent().getStringExtra("vCarType").split(",");
        }
        Utils.printLog("vCarTypes[]", "" + vCarTypes.toString());

        for (int i = 0; i < vehicletypelist.length(); i++) {
            JSONObject obj = generalFunc.getJsonObject(vehicletypelist, i);

            LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            View view = inflater.inflate(R.layout.item_select_service_ride_del_design, null);

            MTextView serviceNameTxtView = (MTextView) view.findViewById(R.id.serviceNameTxtView);
            MTextView serviceTypeNameTxtView = (MTextView) view.findViewById(R.id.serviceTypeNameTxtView);
            MTextView apptypeTxtView = (MTextView) view.findViewById(R.id.apptypeTxtView);

            LinearLayout editarea = (LinearLayout) view.findViewById(R.id.editarea);
            editarea.setVisibility(View.GONE);
            serviceNameTxtView.setText(generalFunc.getJsonValue("vVehicleType", obj.toString()));
            serviceTypeNameTxtView.setText(generalFunc.getJsonValue("SubTitle", obj.toString()));
            if (selectedtype.equals(Utils.CabGeneralTypeRide_Delivery)) {
                apptypeTxtView.setVisibility(View.VISIBLE);
                apptypeTxtView.setText("(" + generalFunc.getJsonValue("eType", obj.toString()) + ")");
            } else {
                apptypeTxtView.setVisibility(View.GONE);

            }


            final AppCompatCheckBox chkBox = (AppCompatCheckBox) view.findViewById(R.id.chkBox);

            Utils.printLog("vCarTypes", "00" + vCarTypes);

            if (vCarTypes != null && vCarTypes.length > 0) {
                Utils.printLog("vCarTypess", "compare: " + vCarTypes + " " + generalFunc.getJsonValue("iVehicleTypeId", obj.toString()));
                String ischeck = generalFunc.getJsonValue("VehicleServiceStatus", obj.toString());
                if (ischeck.equalsIgnoreCase("true") || Arrays.asList(vCarTypes).contains(generalFunc.getJsonValue("iVehicleTypeId", obj.toString()))) {
                    chkBox.setChecked(true);
                    carTypesStatusArr.add(true);
                } else {
                    carTypesStatusArr.add(false);
                }

            } else {
                carTypesStatusArr.add(false);
            }


            final int finalI = i;
            chkBox.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                    carTypesStatusArr.set(finalI, isChecked);
                }
            });
            serviceSelectArea.addView(view);
        }

//        }


    }


    public boolean getCarTypeStatus(String[] vCarTypes, String iVehicleTypeId) {

        for (int i = 0; i < vCarTypes.length; i++) {
            if (iVehicleTypeId.equals(vCarTypes[i])) {
                return true;
            }
        }
        return false;
    }

    public Context getActContext() {
        return AddVehicleActivity.this;
    }

    public void checkData() {

        if (!app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
            if (iSelectedMakeId.equals("")) {
                generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl("", "LBL_CHOOSE_MAKE"));
                return;
            }
            if (iSelectedModelId.equals("")) {
                generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl("", "LBL_CHOOSE_VEHICLE_MODEL"));
                return;
            }

            if (Utils.getText(yearBox).equals("")) {
                generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl("", "LBL_CHOOSE_YEAR"));
                return;
            }
            if (Utils.getText(licencePlateBox).equals("")) {
                generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl("Please add your car's licence plate no.", "LBL_ADD_LICENCE_PLATE"));
                return;
            }
        }

        boolean isCarTypeSelected = false;

        String carTypes = "";
        if (app_type.equals(Utils.CabGeneralType_UberX)) {

            for (int i = 0; i < carTypesStatusArr.size(); i++) {
                if (carTypesStatusArr.get(i) == true) {
                    isCarTypeSelected = true;

                    JSONObject obj = generalFunc.getJsonObject(vehicletypelist, i);

                    String iVehicleTypeId = generalFunc.getJsonValue("iVehicleTypeId", obj.toString());

                    carTypes = carTypes.equals("") ? iVehicleTypeId : (carTypes + "," + iVehicleTypeId);
                }
            }

            if (isCarTypeSelected == false) {
                generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl(".", "LBL_SELECT_CAR_TYPE"));
                return;
            }
        } else {
            for (int i = 0; i < carTypesStatusArr.size(); i++) {
                if (carTypesStatusArr.get(i) == true) {
                    isCarTypeSelected = true;

                    JSONObject obj = generalFunc.getJsonObject(vehicletypelist, i);

                    String iVehicleTypeId = generalFunc.getJsonValue("iVehicleTypeId", obj.toString());
                    carTypes = carTypes.equals("") ? iVehicleTypeId : (carTypes + "," + iVehicleTypeId);
                }
            }
            if (isCarTypeSelected == false) {
                generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl(".", "LBL_SELECT_CAR_TYPE"));
                return;
            }
        }

        if (checkboxHandicap.isChecked()) {
            ishandicapavilabel = true;
        } else {
            ishandicapavilabel = false;
        }

        if (iDriverVehicleId.equals("")) {
            if (ENABLE_EDIT_DRIVER_VEHICLE != null && ENABLE_EDIT_DRIVER_VEHICLE.equalsIgnoreCase("No")) {

                Utils.printLog("callEdit", "" + ENABLE_EDIT_DRIVER_VEHICLE);
                try {

                    GenerateAlertBox editVehicleConfirmDialog = new GenerateAlertBox(getActContext());
                    editVehicleConfirmDialog.setContentMessage("", generalFunc.retrieveLangLBl("", "LBL_COMFIRM_ADD_VEHICLE"));
                    editVehicleConfirmDialog.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));
                    editVehicleConfirmDialog.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_CONFIRM_TXT"));
                    editVehicleConfirmDialog.setCancelable(false);
                    String finalCarTypes = carTypes;
                    editVehicleConfirmDialog.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                        @Override
                        public void handleBtnClick(int btn_id) {

                            if (btn_id == 0) {
                                editVehicleConfirmDialog.closeAlertBox();
                            } else {
                                addVehicle(iSelectedMakeId, iSelectedModelId, finalCarTypes);
                            }
                        }
                    });
                    editVehicleConfirmDialog.showAlertBox();
                } catch (Exception e) {
                    addVehicle(iSelectedMakeId, iSelectedModelId, carTypes);
                }
            } else {
                Utils.printLog("callEdit11", "" + ENABLE_EDIT_DRIVER_VEHICLE);
                addVehicle(iSelectedMakeId, iSelectedModelId, carTypes);
            }
        } else {
            Utils.printLog("callEdit11111", "" + ENABLE_EDIT_DRIVER_VEHICLE);
            addVehicle(iSelectedMakeId, iSelectedModelId, carTypes);
        }
    }

    public void addVehicle(String iMakeId, String iModelId, String vCarType) {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "UpdateDriverVehicle");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("iMakeId", iMakeId);
        parameters.put("iModelId", iModelId);
        parameters.put("iYear", Utils.getText(yearBox));
        parameters.put("vLicencePlate", Utils.getText(licencePlateBox));
        parameters.put("vCarType", vCarType);
        parameters.put("iDriverVehicleId", iDriverVehicleId);
        parameters.put("vColor", Utils.getText(colorPlateBox));
        String HandiCap = "";
        if (ishandicapavilabel) {
            HandiCap = "Yes";

        } else {
            HandiCap = "No";
        }
        parameters.put("HandiCap", HandiCap);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(final String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {
                    dataList.clear();
                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                if (iDriverVehicleId.equals("")) {
                                    if (btn_id == 0) {
                                        generateAlert.closeAlertBox();
                                        Bundle bn = new Bundle();
                                        bn.putBoolean("isUploadDoc", false);
                                        new StartActProcess(getActContext()).setOkResult(bn);
                                        backImgView.performClick();
                                    } else if (btn_id == 1) {

                                        /*Bundle bn = new Bundle();
                                        bn.putBoolean("isUploadDoc", true);
                                        bn.putString("iDriverVehicleId", generalFunc.getJsonValue("VehicleInsertId", responseString));
                                        new StartActProcess(getActContext()).setOkResult(bn);
                                        backImgView.performClick();*/

                                        new StartActProcess(getActContext()).setOkResult();

                                        backImgView.performClick();

                                        Bundle bn = new Bundle();
                                        bn.putString("PAGE_TYPE", "vehicle");
                                        bn.putString("vLicencePlate", Utils.getText(licencePlateBox));
                                        bn.putString("eStatus", generalFunc.getJsonValue("VehicleStatus", responseString));
                                        bn.putString("vMake", Utils.getText(makeBox));
                                        bn.putString("iDriverVehicleId", generalFunc.getJsonValue("VehicleInsertId", responseString));
                                        bn.putString("vCarType", vCarType);
                                        bn.putString("iMakeId", iMakeId);
                                        bn.putString("iYear", Utils.getText(yearBox));
                                        bn.putString("iModelId", iModelId);
                                        bn.putString("vColour", Utils.getText(colorPlateBox));
                                        bn.putString("app_type", app_type);
                                        new StartActProcess(getApplicationContext()).startActWithData_uploadDoc(ListOfDocumentActivity.class, bn);


                                    }
                                } else {
                                    if (btn_id == 0) {
                                        generateAlert.closeAlertBox();
                                        Bundle bn = new Bundle();
                                        bn.putBoolean("isUploadDoc", false);
                                        new StartActProcess(getActContext()).setOkResult(bn);
                                        backImgView.performClick();
                                    } else if (btn_id == 1) {
                                        generateAlert.closeAlertBox();
                                        Bundle bn = new Bundle();
                                        bn.putBoolean("isUploadDoc", false);
                                        new StartActProcess(getActContext()).setOkResult(bn);
                                        backImgView.performClick();

                                    }
                                }

                            }
                        });
                        if (iDriverVehicleId.equals("")) {
                            generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                            generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_SKIP_TXT"));
                            generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_UPLOAD_DOC"));

                        } else {
                            generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                            generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

                        }
                       /* generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_SKIP_TXT"));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_UPLOAD_DOC"));*/

                        generateAlert.showAlertBox();

                    } else {
//                    21-02-2018  CHANGES

                        String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
                        if (!iDriverVehicleId.equals("") && message.equalsIgnoreCase("LBL_EDIT_VEHICLE_DISABLED")) {
                            GenerateAlertBox alertBox = new GenerateAlertBox(getActContext());
                            alertBox.setContentMessage("", generalFunc.retrieveLangLBl("", message));
                            alertBox.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
                            alertBox.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_CONTACT_US_TXT"));
                            alertBox.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                                @Override
                                public void handleBtnClick(int btn_id) {

                                    if (btn_id == 0) {
                                        alertBox.closeAlertBox();
                                        new StartActProcess(getActContext()).startAct(ContactUsActivity.class);
                                    } else {
                                        alertBox.closeAlertBox();
                                    }
                                }
                            });
                            alertBox.showAlertBox();
                        } else {
                            generalFunc.showGeneralMessage("",
                                    generalFunc.retrieveLangLBl("", message));
                        }
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(AddVehicleActivity.this);
            if (i == R.id.backImgView) {
                AddVehicleActivity.super.onBackPressed();

            } else if (i == R.id.makeBox) {
                if (list_make == null) {
                    buildMake();
                    list_make.show();
                } else {
                    list_make.show();
                }
            } else if (i == R.id.modelBox) {

                if (iSelectedMakeId.equals("")) {
                    generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl("", "LBL_CHOOSE_MAKE"));
                    return;
                } else {
                    buildModelList(true);
                }

            } else if (i == R.id.yearBox) {
                if (list_year == null) {
                    buildYear();

                } else {
                    list_year.show();
                }
            } else if (i == submitVehicleBtn.getId()) {
                checkData();
            } else if (i == vehicleTypeBox.getId()) {
                openVehicleTypeDialog();
            }
        }
    }

    private void openVehicleTypeDialog() {

        ArrayList<String> vehicleitems = new ArrayList<String>();


        vehicleitems.add(generalFunc.retrieveLangLBl("Ride", "LBL_RIDE"));
        vehicleitems.add(generalFunc.retrieveLangLBl("Delivery", "LBL_DELIVERY"));
        vehicleitems.add(generalFunc.retrieveLangLBl("Ride - delivery", "LBL_RIDE_DELIVRY"));


        CharSequence[] cs_currency_txt = vehicleitems.toArray(new CharSequence[vehicleitems.size()]);

        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle(generalFunc.retrieveLangLBl("Select Vehicle Type", "LBL_SELECT_VEHICLE_TYPE"));

        builder.setItems(cs_currency_txt, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int item) {
                // Do something with the selection

                if (list_vehicleType != null) {
                    list_vehicleType.dismiss();
                }


                if (item == 0) {
                    selectedtype = Utils.CabGeneralType_Ride;
                    checkboxHandicap.setVisibility(View.VISIBLE);
                } else if (item == 1) {
                    selectedtype = "Delivery";
                    checkboxHandicap.setChecked(false);
                    checkboxHandicap.setVisibility(View.GONE);
                } else {
                    selectedtype = "Ride-Delivery";
                    checkboxHandicap.setVisibility(View.VISIBLE);
                }

                vehicleTypeBox.setText(vehicleitems.get(item));

                buildMakeList();


            }
        });

        list_vehicleType = builder.create();

        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(list_vehicleType);
        }


        list_vehicleType.show();

    }


}
