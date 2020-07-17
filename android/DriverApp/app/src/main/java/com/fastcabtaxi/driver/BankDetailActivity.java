package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.text.InputType;
import android.view.View;
import android.widget.ImageView;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;

import org.json.JSONObject;

import java.util.HashMap;

public class BankDetailActivity extends AppCompatActivity {

    GeneralFunctions generalFunc;
    MButton submitBtn;
    ImageView backImgView;
    MTextView titleTxt;

    MaterialEditText vPaymentEmail, vBankAccountHolderName, vAccountNumber, vBankLocation, vBankName, vBIC_SWIFT_Code;
    String required_str = "";
    String error_email_str = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_bank_detail);

        generalFunc = new GeneralFunctions(getActContext());
        submitBtn = ((MaterialRippleLayout) findViewById(R.id.submitBtn)).getChildView();

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);


        vPaymentEmail = (MaterialEditText) findViewById(R.id.vPaymentEmailBox);
        vBankAccountHolderName = (MaterialEditText) findViewById(R.id.vBankAccountHolderNameBox);
        vAccountNumber = (MaterialEditText) findViewById(R.id.vAccountNumberBox);
        vBankLocation = (MaterialEditText) findViewById(R.id.vBankLocation);
        vBankName = (MaterialEditText) findViewById(R.id.vBankName);
        vBIC_SWIFT_Code = (MaterialEditText) findViewById(R.id.vBIC_SWIFT_Code);

        setData();
        submitBtn.setId(Utils.generateViewId());

        submitBtn.setOnClickListener(new setOnClickList());
        backImgView.setOnClickListener(new setOnClickList());
        isBankDetailDisplay("", "", "", "", "", "", "Yes", false);
    }

    private void setData() {

        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_BANK_DETAILS_TXT"));
        submitBtn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_SUBMIT_TXT"));

        vPaymentEmail.setBothText(generalFunc.retrieveLangLBl("", "LBL_PAYMENT_EMAIL_TXT"));
        vBankAccountHolderName.setBothText(generalFunc.retrieveLangLBl("", "LBL_PROFILE_BANK_HOLDER_TXT"));
        vAccountNumber.setBothText(generalFunc.retrieveLangLBl("", "LBL_ACCOUNT_NUMBER"));
        vBankLocation.setBothText(generalFunc.retrieveLangLBl("", "LBL_BANK_LOCATION"));
        vBankName.setBothText(generalFunc.retrieveLangLBl("", "LBL_BANK_NAME"));
        vBIC_SWIFT_Code.setBothText(generalFunc.retrieveLangLBl("", "LBL_BIC_SWIFT_CODE"));

        required_str = generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT");
        error_email_str = generalFunc.retrieveLangLBl("", "LBL_FEILD_EMAIL_ERROR_TXT");

        vAccountNumber.setInputType(InputType.TYPE_CLASS_NUMBER);
        vPaymentEmail.setInputType(InputType.TYPE_TEXT_VARIATION_EMAIL_ADDRESS | InputType.TYPE_CLASS_TEXT);

    }

    private void isBankDetailDisplay(String vPaymentEmail, String vBankAccountHolderName, String vAccountNumber, String vBankLocation, String vBankName,
                                     String vBIC_SWIFT_Code, String eDisplay, final boolean isAlert) {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "DriverBankDetails");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("userType", CommonUtilities.APP_TYPE);
        parameters.put("vPaymentEmail", vPaymentEmail);
        parameters.put("vBankAccountHolderName", vBankAccountHolderName);
        parameters.put("vAccountNumber", vAccountNumber);
        parameters.put("vBankLocation", vBankLocation);
        parameters.put("vBankName", vBankName);
        parameters.put("vBIC_SWIFT_Code", vBIC_SWIFT_Code);
        parameters.put("eDisplay", eDisplay);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setIsDeviceTokenGenerate(true, "vDeviceToken", generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);
                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);
                    if (isDataAvail == true) {
                        JSONObject msg_obj = generalFunc.getJsonObject("message", responseString);

                        String vPaymentEmail = generalFunc.getJsonValue("vPaymentEmail", msg_obj.toString());
                        String vBankAccountHolderName = generalFunc.getJsonValue("vBankAccountHolderName", msg_obj.toString());
                        String vAccountNumber = generalFunc.getJsonValue("vAccountNumber", msg_obj.toString());
                        String vBankLocation = generalFunc.getJsonValue("vBankLocation", msg_obj.toString());
                        String vBankName = generalFunc.getJsonValue("vBankName", msg_obj.toString());
                        String vBIC_SWIFT_Code = generalFunc.getJsonValue("vBIC_SWIFT_Code", msg_obj.toString());

                        if (!vPaymentEmail.equals("")) {
                            ((MaterialEditText) findViewById(R.id.vPaymentEmailBox)).setText(vPaymentEmail);
                        }
                        if (!vBankAccountHolderName.equals("")) {
                            ((MaterialEditText) findViewById(R.id.vBankAccountHolderNameBox)).setText(vBankAccountHolderName);
                        }
                        if (!vAccountNumber.equals("")) {
                            ((MaterialEditText) findViewById(R.id.vAccountNumberBox)).setText(vAccountNumber);
                        }
                        if (!vBankLocation.equals("")) {
                            ((MaterialEditText) findViewById(R.id.vBankLocation)).setText(vBankLocation);
                        }
                        if (!vBankName.equals("")) {
                            ((MaterialEditText) findViewById(R.id.vBankName)).setText(vBankName);
                        }
                        if (!vBIC_SWIFT_Code.equals("")) {
                            ((MaterialEditText) findViewById(R.id.vBIC_SWIFT_Code)).setText(vBIC_SWIFT_Code);
                        }


                        if (isAlert == true) {
                            GenerateAlertBox alertBox = new GenerateAlertBox(getActContext());
                            alertBox.setContentMessage("", generalFunc.retrieveLangLBl("", "LBL_BANK_DETAILS_UPDATED"));
                            alertBox.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_GENERAL"));
                            alertBox.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                                @Override
                                public void handleBtnClick(int btn_id) {
                                    if (btn_id == 1) {
                                        BankDetailActivity.super.onBackPressed();
                                    }
                                }
                            });
                            alertBox.showAlertBox();
                        }
                    } else {

                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public Context getActContext() {
        return BankDetailActivity.this;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();

            if (i == submitBtn.getId()) {
                checkData();
            } else if (i == R.id.backImgView) {
                BankDetailActivity.this.onBackPressed();
            }
        }
    }

    private void checkData() {

        boolean isPaymentEmail = Utils.checkText(vPaymentEmail) ? generalFunc.isEmailValid(Utils.getText(vPaymentEmail)) ? true : Utils.setErrorFields(vPaymentEmail, error_email_str) : Utils.setErrorFields(vPaymentEmail, required_str);

        boolean isSwiftCode = Utils.checkText(vBIC_SWIFT_Code) ? true : Utils.setErrorFields(vBIC_SWIFT_Code, required_str);
        boolean isAccountNumber = Utils.checkText(vAccountNumber) ? true : Utils.setErrorFields(vAccountNumber, required_str);
        boolean isBankAccountHolderName = Utils.checkText(vBankAccountHolderName) ? true : Utils.setErrorFields(vBankAccountHolderName, required_str);
        boolean isBankName = Utils.checkText(vBankName) ? true : Utils.setErrorFields(vBankName, required_str);
        boolean isBankLocation = Utils.checkText(vBankLocation) ? true : Utils.setErrorFields(vBankLocation, required_str);

        if (isPaymentEmail == false || isBankAccountHolderName == false || isAccountNumber == false || isBankLocation == false || isBankName == false || isSwiftCode == false) {
            return;
        }

        isBankDetailDisplay(Utils.getText(vPaymentEmail), Utils.getText(vBankAccountHolderName), Utils.getText(vAccountNumber),
                Utils.getText(vBankLocation), Utils.getText(vBankName), Utils.getText(vBIC_SWIFT_Code), "No", true);


    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }
}
