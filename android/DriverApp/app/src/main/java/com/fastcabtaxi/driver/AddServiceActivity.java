package com.fastcabtaxi.driver;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.AppCompatCheckBox;
import android.text.Editable;
import android.text.InputFilter;
import android.text.InputType;
import android.text.Spanned;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.CompoundButton;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
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
import java.util.regex.Matcher;
import java.util.regex.Pattern;


public class AddServiceActivity extends AppCompatActivity {


    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;
    String iVehicleCategoryId = "";
    String vTitle = "";
    ArrayList<String> dataList = new ArrayList<>();
    LinearLayout serviceSelectArea;

    MButton btn_type2;
    int submitBtnId;
    android.support.v7.app.AlertDialog PriceEditConifrmAlertDialog;

    String fAmount = "";
    String iVehicleTypeId = "";

    ArrayList<Boolean> carTypesStatusArr;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_add_service);

        generalFunc = new GeneralFunctions(getActContext());
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        backImgView.setOnClickListener(new setOnClickList());
        serviceSelectArea = (LinearLayout) findViewById(R.id.serviceSelectArea);
        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        submitBtnId = Utils.generateViewId();
        btn_type2.setId(submitBtnId);

        btn_type2.setOnClickListener(new setOnClickList());


        Intent in = getIntent();
        iVehicleCategoryId = in.getStringExtra("iVehicleCategoryId");
        vTitle = in.getStringExtra("vTitle");
        setLabels();
        carTypesStatusArr = new ArrayList<>();
        getsubCategoryList();


    }

    public void getsubCategoryList() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getServiceTypes");
        parameters.put("iVehicleCategoryId", iVehicleCategoryId);
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("UserType", Utils.userType);


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        JSONArray carList_arr = generalFunc.getJsonArray("message", responseString);

                        if (carList_arr != null) {
                            for (int i = 0; i < carList_arr.length(); i++) {

                                JSONObject obj = generalFunc.getJsonObject(carList_arr, i);
                                dataList.add(obj.toString());
                            }
                        }


                        buildServices();


                    } else {

                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                generateAlert.closeAlertBox();

                                backImgView.performClick();
                            }
                        });
                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

                        generateAlert.showAlertBox();
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void buildServices() {

        if (serviceSelectArea.getChildCount() > 0) {
            serviceSelectArea.removeAllViewsInLayout();
        }
        for (int i = 0; i < dataList.size(); i++) {
            String obj = dataList.get(i);

            final LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            View view = inflater.inflate(R.layout.item_select_service_design, null);

            MTextView serviceNameTxtView = (MTextView) view.findViewById(R.id.serviceNameTxtView);
            MTextView serviceTypeNameTxtView = (MTextView) view.findViewById(R.id.serviceTypeNameTxtView);

            final MTextView serviceamtHtxt = (MTextView) view.findViewById(R.id.serviceamtHtxt);
            final MTextView serviceamtVtxt = (MTextView) view.findViewById(R.id.serviceamtVtxt);
            final MTextView editBtn = (MTextView) view.findViewById(R.id.editBtn);
            final LinearLayout editarea = (LinearLayout) view.findViewById(R.id.editarea);

            String[] vCarTypes = {};

            AppCompatCheckBox chkBox = (AppCompatCheckBox) view.findViewById(R.id.chkBox);

            editBtn.setText(generalFunc.retrieveLangLBl("", "LBL_RIDER_EDIT"));

            serviceamtHtxt.setText(generalFunc.retrieveLangLBl("Service Amount : ", "LBL_SERVICE_AMOUNT_TXT"));

            serviceNameTxtView.setText(generalFunc.getJsonValue("vTitle", obj.toString()));
            serviceTypeNameTxtView.setText(generalFunc.getJsonValue("SubTitle", obj.toString()));

            String ischeck = generalFunc.getJsonValue("VehicleServiceStatus", obj.toString());
            if (ischeck.equalsIgnoreCase("true") || Arrays.asList(vCarTypes).contains(generalFunc.getJsonValue("iVehicleTypeId", obj.toString()))) {
                chkBox.setChecked(true);
                carTypesStatusArr.add(true);
            } else {
                carTypesStatusArr.add(false);
            }


            final int finalI = i;
            if (generalFunc.getJsonValue("ePriceType", obj.toString()).equalsIgnoreCase("Provider") && (generalFunc.getJsonValue("eFareType", obj.toString()).equalsIgnoreCase("Fixed") || generalFunc.getJsonValue("eFareType", obj.toString()).equalsIgnoreCase("Hourly"))) {
                editarea.setVisibility(View.VISIBLE);
            } else {
                editarea.setVisibility(View.GONE);
            }

            chkBox.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                    carTypesStatusArr.set(finalI, isChecked);
                }
            });


            if (generalFunc.getJsonValue("eFareType", obj.toString()).equalsIgnoreCase("Hourly")) {
                serviceamtVtxt.setText(generalFunc.getJsonValue("vCurrencySymbol", obj.toString()) + " " + generalFunc.getJsonValue("fAmount", obj.toString()) + "/" + generalFunc.retrieveLangLBl("hour", "LBL_HOUR"));
            } else {
                serviceamtVtxt.setText(generalFunc.getJsonValue("vCurrencySymbol", obj.toString()) + " " + generalFunc.getJsonValue("fAmount", obj.toString()));
            }

            fAmount = generalFunc.getJsonValue("fAmount", obj.toString());


            editBtn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    driverChangePriceDilalg(finalI, editBtn);

                }
            });


            serviceSelectArea.addView(view);
        }


    }


    public void driverChangePriceDilalg(final int pos, final MTextView editPriceButton) {


        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.desgin_extracharge_confirm, null);
        builder.setView(dialogView);

        final MaterialEditText tipAmountEditBox = (MaterialEditText) dialogView.findViewById(R.id.editBox);
        tipAmountEditBox.setInputType(InputType.TYPE_NUMBER_FLAG_DECIMAL | InputType.TYPE_CLASS_NUMBER);
        //tipAmountEditBox.setMaxCharacters(10);
        tipAmountEditBox.setVisibility(View.VISIBLE);
        tipAmountEditBox.setFilters(new InputFilter[] { new InputFilter.LengthFilter(10) });

        final MTextView giveTipTxtArea = (MTextView) dialogView.findViewById(R.id.giveTipTxtArea);
        final MTextView skipTxtArea = (MTextView) dialogView.findViewById(R.id.skipTxtArea);
        final MTextView titileTxt = (MTextView) dialogView.findViewById(R.id.titileTxt);
        final MTextView msgTxt = (MTextView) dialogView.findViewById(R.id.msgTxt);
        final MTextView CurrencySymbolTXT = (MTextView) dialogView.findViewById(R.id.CurrencySymbolTXT);

        msgTxt.setVisibility(View.VISIBLE);
        titileTxt.setText(generalFunc.retrieveLangLBl("Enter Service Amount Below:", "LBL_ENTER_SERVICE_AMOUNT"));

        msgTxt.setText("");

        msgTxt.setVisibility(View.GONE);
        giveTipTxtArea.setText("" + generalFunc.retrieveLangLBl("", "LBL_CONFIRM_TXT"));
        skipTxtArea.setText("" + generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));

        skipTxtArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Utils.hideKeyboard(AddServiceActivity.this);
                PriceEditConifrmAlertDialog.dismiss();

            }
        });

        tipAmountEditBox.setBothText("", generalFunc.retrieveLangLBl("", "LBL_ENTER_AMOUNT"));
        tipAmountEditBox.setPaddings(42, 0, 0, 0);


        String obj = dataList.get(pos);

        if (!generalFunc.getJsonValue("fAmount", obj).equals("") && generalFunc.getJsonValue("fAmount", obj) != null) {

            tipAmountEditBox.setText(generalFunc.getJsonValue("fAmount", obj));
        } else {
            tipAmountEditBox.setText("0");
        }
        CurrencySymbolTXT.setText(generalFunc.getJsonValue("vCurrencySymbol", obj));

        tipAmountEditBox.setFilters(new InputFilter[]{new DecimalDigitsInputFilter(10, 2)});
        tipAmountEditBox.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {


            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });


        giveTipTxtArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Utils.hideKeyboard(AddServiceActivity.this);
                final boolean tipAmountEntered = Utils.checkText(tipAmountEditBox) ? true : Utils.setErrorFields(tipAmountEditBox, generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT"));
                if (tipAmountEntered == false) {
                    LinearLayout.LayoutParams params = (LinearLayout.LayoutParams) CurrencySymbolTXT.getLayoutParams();
                    params.setMargins(0, 0, 0, 10);
                    CurrencySymbolTXT.setLayoutParams(params);
                    return;

                }
                if (GeneralFunctions.parseDoubleValue(0, tipAmountEditBox.getText().toString()) > 0) {


                    PriceEditConifrmAlertDialog.dismiss();

                    try {

                        fAmount = tipAmountEditBox.getText().toString();
                        addServiceAmount(pos);
                    } catch (Exception e) {

                        Utils.printLog("Exception", e.toString());
                    }
                } else {
                    tipAmountEditBox.setText("");
                    Utils.setErrorFields(tipAmountEditBox, generalFunc.retrieveLangLBl("", "LBL_ADD_CORRECT_DETAIL_TXT"));
                }
            }
        });
        PriceEditConifrmAlertDialog = builder.create();
        PriceEditConifrmAlertDialog.setCancelable(false);
        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(PriceEditConifrmAlertDialog);
        }
        PriceEditConifrmAlertDialog.show();


    }


    public void setLabels() {
        titleTxt.setText(vTitle);
        btn_type2.setText(generalFunc.retrieveLangLBl("Update Services", "LBL_UPDATE_SERVICES"));
    }

    public Context getActContext() {
        return AddServiceActivity.this;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            if (i == submitBtnId) {
                boolean isCarTypeSelected = false;
                String carTypes = "";
                for (int j = 0; j < carTypesStatusArr.size(); j++) {
                    if (carTypesStatusArr.get(j) == true) {
                        isCarTypeSelected = true;


                        String iVehicleTypeId = generalFunc.getJsonValue("iVehicleTypeId", dataList.get(j).toString());

                        carTypes = carTypes.equals("") ? iVehicleTypeId : (carTypes + "," + iVehicleTypeId);
                    }
                }
                addService(carTypes);

            } else if (view == backImgView) {
                onBackPressed();
            }

        }
    }

    public void addServiceAmount(int pos) {
        String obj = dataList.get(pos);

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "UpdateDriverServiceAmount");
        parameters.put("iVehicleTypeId", generalFunc.getJsonValue("iVehicleTypeId", obj.toString()));
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("fAmount", fAmount);
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


                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                generateAlert.closeAlertBox();

                                carTypesStatusArr.clear();
                                getsubCategoryList();

                            }
                        });
                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

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


    public void addService(String vCarType) {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "UpdateDriverVehicle");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("vCarType", vCarType);
        parameters.put("iVehicleCategoryId", iVehicleCategoryId);


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {
                  //  dataList.clear();
                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {


                        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                        generateAlert.setCancelable(false);
                        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                generateAlert.closeAlertBox();

                                setResult(RESULT_OK);
                                backImgView.performClick();
                            }
                        });
                        generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

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

    public class DecimalDigitsInputFilter implements InputFilter {

        Pattern mPattern;

        public DecimalDigitsInputFilter(int digitsBeforeZero, int digitsAfterZero) {
            mPattern = Pattern.compile("[0-9]{0," + (digitsBeforeZero - 1) + "}+((\\.[0-9]{0," + (digitsAfterZero - 1) + "})?)||(\\.)?");
        }

        @Override
        public CharSequence filter(CharSequence source, int start, int end, Spanned dest, int dstart, int dend) {

            Matcher matcher = mPattern.matcher(dest);
            if (!matcher.matches())
                return "";
            return null;
        }

    }
}
