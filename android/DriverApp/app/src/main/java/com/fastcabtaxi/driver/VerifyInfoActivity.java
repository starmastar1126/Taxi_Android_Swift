package com.fastcabtaxi.driver;

import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.CardView;
import android.text.TextUtils;
import android.view.View;
import android.widget.ImageView;
import android.widget.ProgressBar;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;

import java.util.HashMap;
import java.util.concurrent.TimeUnit;

import okhttp3.internal.Util;

public class VerifyInfoActivity extends AppCompatActivity {

    CardView emailView, smsView;
    ProgressBar loading;
    MaterialEditText codeBox;
    MaterialEditText emailBox;

    ImageView backImgView;
    GeneralFunctions generalFunc;
    String required_str = "";
    String error_verification_code = "";

    String userProfileJson = "";
    MTextView titleTxt;

    MButton okBtn, emailOkBtn;
    MButton resendBtn, emailResendBtn;
    MButton editBtn, emailEditBtn;
    Bundle bundle;
    String reqType = "";
    String vEmail = "", vPhone = "";

    String phoneVerificationCode = "";
    String emailVerificationCode = "";
//    int resendSecAfter = 30 * 1000;
    int resendSecAfter;
    int maxAllowdCount;
    int resendSecInMilliseconds;
    boolean isonlyphoneVerified = false;
    boolean isEmailVeried = false;

    boolean isEditInfoTapped = false;
    CountDownTimer countDnTimer;

    int maxAttemptCount=0;
    int resendTime=0;

    boolean isProcessRunning=false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_verify_info);

        generalFunc = new GeneralFunctions(getActContext());
        bundle = new Bundle();
        bundle = getIntent().getExtras();
        String msg = bundle.getString("msg");


        resendSecAfter = generalFunc.parseIntegerValue(30, generalFunc.getJsonValue(Utils.VERIFICATION_CODE_RESEND_TIME_IN_SECONDS_KEY, generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON)));
        maxAllowdCount = generalFunc.parseIntegerValue(5, generalFunc.getJsonValue(Utils.VERIFICATION_CODE_RESEND_COUNT_KEY, generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON)));
        resendTime = generalFunc.parseIntegerValue(30, generalFunc.getJsonValue(Utils.VERIFICATION_CODE_RESEND_COUNT_RESTRICTION_KEY, generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON)));
        resendSecInMilliseconds=resendSecAfter*1*1000;

        phonetxt = ((MTextView) findViewById(R.id.phoneTxt));
        emailTxt = ((MTextView) findViewById(R.id.emailTxt));


        if (!getIntent().hasExtra("MOBILE")) {
            userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

            vEmail = generalFunc.getJsonValue("vEmail", userProfileJson);
            vPhone = generalFunc.getJsonValue("vCode", userProfileJson) + "" + generalFunc.getJsonValue("vPhone", userProfileJson);
        } else {
            vPhone = generalFunc.getJsonValue("vCode", userProfileJson) + "" + getIntent().getStringExtra("MOBILE");
        }

        emailView = (CardView) findViewById(R.id.emailView);
        smsView = (CardView) findViewById(R.id.smsView);

        if (msg.equalsIgnoreCase("DO_EMAIL_PHONE_VERIFY")) {
            emailView.setVisibility(View.VISIBLE);
            smsView.setVisibility(View.VISIBLE);
           // maxAttemptCount++;
            reqType = "DO_EMAIL_PHONE_VERIFY";
        } else if (msg.equalsIgnoreCase("DO_EMAIL_VERIFY")) {
            emailView.setVisibility(View.VISIBLE);
            smsView.setVisibility(View.GONE);
            reqType = "DO_EMAIL_VERIFY";
        } else if (msg.equalsIgnoreCase("DO_PHONE_VERIFY")) {
            smsView.setVisibility(View.VISIBLE);
            emailView.setVisibility(View.GONE);
         //   maxAttemptCount++;
            reqType = "DO_PHONE_VERIFY";
        }

        okBtn = ((MaterialRippleLayout) findViewById(R.id.okBtn)).getChildView();
        resendBtn = ((MaterialRippleLayout) findViewById(R.id.resendBtn)).getChildView();
        editBtn = ((MaterialRippleLayout) findViewById(R.id.editBtn)).getChildView();
        codeBox = (MaterialEditText) findViewById(R.id.codeBox);
        emailBox = (MaterialEditText) findViewById(R.id.emailCodeBox);
        emailOkBtn = ((MaterialRippleLayout) findViewById(R.id.emailOkBtn)).getChildView();
        emailResendBtn = ((MaterialRippleLayout) findViewById(R.id.emailResendBtn)).getChildView();
        emailEditBtn = ((MaterialRippleLayout) findViewById(R.id.emailEditBtn)).getChildView();

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        backImgView.setOnClickListener(new setOnClickList());
        loading = (ProgressBar) findViewById(R.id.loading);

        okBtn.setId(Utils.generateViewId());
        okBtn.setOnClickListener(new setOnClickList());

        resendBtn.setId(Utils.generateViewId());
        resendBtn.setOnClickListener(new setOnClickList());

        editBtn.setId(Utils.generateViewId());
        editBtn.setOnClickListener(new setOnClickList());

        emailOkBtn.setId(Utils.generateViewId());
        emailOkBtn.setOnClickListener(new setOnClickList());

        emailResendBtn.setId(Utils.generateViewId());
        emailResendBtn.setOnClickListener(new setOnClickList());

        emailEditBtn.setId(Utils.generateViewId());
        emailEditBtn.setOnClickListener(new setOnClickList());
        setLabels();

        sendVerificationSMS(null);

        if (generalFunc.retrieveValue(CommonUtilities.SITE_TYPE_KEY).equalsIgnoreCase("Demo")) {
            findViewById(R.id.helpOTPTxtView).setVisibility(View.VISIBLE);
        } else {
            findViewById(R.id.helpOTPTxtView).setVisibility(View.GONE);
        }
    }


    @Override
    protected void onResume() {
        super.onResume();
    }

    MTextView phonetxt;
    MTextView emailTxt;

    private void setLabels() {

        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ACCOUNT_VERIFY_TXT"));
        ((MTextView) findViewById(R.id.smsTitleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_MOBILE_VERIFy_TXT"));
        ((MTextView) findViewById(R.id.smsSubTitleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_SMS_SENT_TO") + ": ");
        ((MTextView) findViewById(R.id.emailTitleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_EMAIL_VERIFy_TXT"));
        ((MTextView) findViewById(R.id.emailSubTitleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_EMAIL_SENT_TO") + " ");
        ((MTextView) findViewById(R.id.smsHelpTitleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_SMS_SENT_NOTE"));
        ((MTextView) findViewById(R.id.emailHelpTitleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_EMAIL_SENT_NOTE"));

        ((MTextView) findViewById(R.id.phoneTxt)).setText("+"+vPhone);
        ((MTextView) findViewById(R.id.emailTxt)).setText(vEmail);

        okBtn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
        resendBtn.setText(generalFunc.retrieveLangLBl("", "LBL_RESEND_SMS"));
        editBtn.setText(generalFunc.retrieveLangLBl("", "LBL_EDIT_MOBILE"));

        emailOkBtn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
        emailResendBtn.setText(generalFunc.retrieveLangLBl("", "LBL_RESEND_EMAIL"));
        emailEditBtn.setText(generalFunc.retrieveLangLBl("", "LBL_EDIT_EMAIL"));

        error_verification_code = generalFunc.retrieveLangLBl("", "LBL_VERIFICATION_CODE_INVALID");
        required_str = generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT");
    }

    public void sendVerificationSMS(MButton btn) {

        if (btn!=null)
        {
            //loading.setVisibility(View.VISIBLE);
        }

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "sendVerificationSMS");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("MobileNo", vPhone);
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("REQ_TYPE", reqType);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {
                loading.setVisibility(View.GONE);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        switch (reqType) {
                            case "DO_EMAIL_PHONE_VERIFY":
                                if (!generalFunc.getJsonValue(CommonUtilities.message_str, responseString).equals("")) {
                                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("",
                                            generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                                } else {
                                    if (!generalFunc.getJsonValue(CommonUtilities.message_str + "_sms", responseString).equalsIgnoreCase("LBL_MOBILE_VERIFICATION_FAILED_TXT")) {
                                        phoneVerificationCode = generalFunc.getJsonValue(CommonUtilities.message_str + "_sms", responseString);
                                    } else {
                                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("",
                                                generalFunc.getJsonValue(CommonUtilities.message_str + "_sms", responseString)));
                                    }
                                    if (!generalFunc.getJsonValue(CommonUtilities.message_str + "_email", responseString).equalsIgnoreCase("LBL_EMAIL_VERIFICATION_FAILED_TXT")) {
                                        emailVerificationCode = generalFunc.getJsonValue(CommonUtilities.message_str + "_email", responseString);
                                    } else {
                                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("",
                                                generalFunc.getJsonValue(CommonUtilities.message_str + "_email", responseString)));
                                    }
                                }
                                break;
                            case "DO_EMAIL_VERIFY":
                                emailVerificationCode = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
                                break;
                            case "DO_PHONE_VERIFY":
                                isProcessRunning=false;
                                phoneVerificationCode = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
                                break;
                            case "PHONE_VERIFIED":
                                verifySuccessMessage(generalFunc.retrieveLangLBl("",
                                        generalFunc.getJsonValue(CommonUtilities.message_str, responseString)), true, false);

                                break;
                            case "EMAIL_VERIFIED":
                                verifySuccessMessage(generalFunc.retrieveLangLBl("",
                                        generalFunc.getJsonValue(CommonUtilities.message_str, responseString)), false, true);
                                break;
                        }

                        String userdetails = generalFunc.getJsonValue("userDetails", responseString);
                        if (!userdetails.equals("") && userdetails != null) {
                            String messageData = generalFunc.getJsonValue(CommonUtilities.message_str, userdetails);
                            generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, messageData);
                        }
                        resendProcess(btn);
                    } else {

                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void verifySuccessMessage(String message, final boolean sms, final boolean email) {

        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();
                if (TextUtils.isEmpty(generalFunc.getMemberId())) {
                    if (TextUtils.isEmpty(generalFunc.getMemberId())) {
                        isProcessRunning=false;
                        new StartActProcess(getActContext()).setOkResult();
                        VerifyInfoActivity.super.onBackPressed();
                    }
                } else {
                    if (sms == true) {
                        smsView.setVisibility(View.GONE);
                        if (emailView.getVisibility() == View.GONE) {
                            isProcessRunning=false;
                            VerifyInfoActivity.super.onBackPressed();
                        }
                    } else if (email == true) {
                        emailView.setVisibility(View.GONE);
                        if (smsView.getVisibility() == View.GONE) {
                            isProcessRunning=false;
                            VerifyInfoActivity.super.onBackPressed();
                        }
                    }
                }
            }
        });
        generateAlert.setContentMessage("", message);
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
        generateAlert.showAlertBox();
    }

    public void resendProcess(final MButton btn) {

      /*  btn.setTextColor(Color.parseColor("#BABABA"));
        btn.setClickable(false);*/

        if (btn==null)
        {
            return;
        }

        enableOrDisable(false,btn);

        if (btn.getId()==resendBtn.getId())
        {
            setTime(generalFunc.parseLongValue(0L, String.valueOf(resendSecInMilliseconds)),resendBtn);
            showTimer();
        }
        else {
            Handler handler = new Handler();
            handler.postDelayed(new Runnable() {
                @Override
                public void run() {
                    enableOrDisable(true,btn);
                    /*btn.setTextColor(getResources().getColor(R.color.appThemeColor_TXT_1));
                    btn.setClickable(true);*/
                }
            }, resendSecInMilliseconds);
        }
    }

    private void setTime(long milliseconds, MButton btn) {
        int minutes = (int) (milliseconds/ 1000)  / 60;
        int seconds = (int) (milliseconds/ 1000) % 60;

        if (btn.getId()==resendBtn.getId()) {
            btn.setTextColor(Color.parseColor("#FFFFFF"));
        }
        btn.setText(String.format("%02d:%02d",minutes, seconds));
    }

    public void showTimer()
    {
        countDnTimer = new CountDownTimer(resendSecInMilliseconds, 1000) {
            @Override
            public void onTick(long milliseconds) {
                isProcessRunning=true;
                setTime(milliseconds,resendBtn);


            }

            @Override
            public void onFinish() {
                isProcessRunning=false;
                // this function will be called when the timecount is finished
                resendBtn.setText(generalFunc.retrieveLangLBl("", "LBL_RESEND_SMS"));
                /*resendBtn.setTextColor(getResources().getColor(R.color.appThemeColor_TXT_1));
                resendBtn.setClickable(true);*/
                enableOrDisable(true,resendBtn);
                removecountDownTimer();
            }
        }.start();

    }
    private void removecountDownTimer() {

        if (countDnTimer != null) {
            countDnTimer.cancel();
            countDnTimer = null;
        }
    }

    public Context getActContext() {
        return VerifyInfoActivity.this;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(VerifyInfoActivity.this);
            if (i == R.id.backImgView) {
                onBackPressed();
               // VerifyInfoActivity.super.onBackPressed();
            } else if (i == okBtn.getId()) {
                boolean isCodeEntered = Utils.checkText(codeBox) ?
                        ((phoneVerificationCode.equalsIgnoreCase(Utils.getText(codeBox)) ||
                                (generalFunc.retrieveValue(CommonUtilities.SITE_TYPE_KEY).equalsIgnoreCase("Demo") && Utils.getText(codeBox).equalsIgnoreCase("12345"))) ? true
                                : Utils.setErrorFields(codeBox, error_verification_code)) : Utils.setErrorFields(codeBox, required_str);

                if (isCodeEntered) {
                    reqType = "PHONE_VERIFIED";
                    sendVerificationSMS(null);
                }
            } else if (i == resendBtn.getId()) {
                reqType = "DO_PHONE_VERIFY";

               /* if (maxAttemptCount>=maxAllowdCount)
                {
                    // show blockage msg
                    generalFunc.showGeneralMessage("","You reached maximum attempt limit.Please try after "+resendTime +"min");
                }
                else
                {*/
                   // maxAttemptCount++;
                    sendVerificationSMS(resendBtn);

                    //resendProcess(resendBtn);
               // }

            } else if (i == editBtn.getId()) {
                Bundle bn = new Bundle();
                bn.putBoolean("isEdit", true);
                bn.putBoolean("isMobile", true);
                isonlyphoneVerified = true;
                isEditInfoTapped = true;
                new StartActProcess(getActContext()).startActForResult(MyProfileActivity.class, bn, Utils.MY_PROFILE_REQ_CODE);
            } else if (i == emailOkBtn.getId()) {
                boolean isEmailCodeEntered = Utils.checkText(emailBox) ?
                        ((emailVerificationCode.equalsIgnoreCase(Utils.getText(emailBox)) ||
                                (generalFunc.retrieveValue(CommonUtilities.SITE_TYPE_KEY).equalsIgnoreCase("Demo") && Utils.getText(emailBox).equalsIgnoreCase("12345"))) ? true
                                : Utils.setErrorFields(emailBox, error_verification_code)) : Utils.setErrorFields(emailBox, required_str);
                if (isEmailCodeEntered) {
                    reqType = "EMAIL_VERIFIED";
                    sendVerificationSMS(null);
                }
            } else if (i == emailResendBtn.getId()) {
                reqType = "DO_EMAIL_VERIFY";
//                resendProcess(emailResendBtn);
                sendVerificationSMS(emailResendBtn);
            } else if (i == emailEditBtn.getId()) {

                isEmailVeried = true;
                isEditInfoTapped = true;
                Bundle bn = new Bundle();
                bn.putBoolean("isEdit", true);
                bn.putBoolean("isEmail", true);
                new StartActProcess(getActContext()).startActForResult(MyProfileActivity.class, bn, Utils.MY_PROFILE_REQ_CODE);
            }
        }
    }

    public void enableOrDisable(boolean activate,MButton btn)
    {

        if (activate) {

            btn.setFocusableInTouchMode(true);
            btn.setFocusable(true);
            btn.setEnabled(true);
            btn.setOnClickListener(new setOnClickList());
            btn.setTextColor(Color.parseColor("#FFFFFF"));
            btn.setClickable(true);

        }else
        {
            btn.setFocusableInTouchMode(false);
            btn.setFocusable(false);
            btn.setEnabled(false);
            btn.setOnClickListener(null);
            btn.setTextColor(Color.parseColor("#BABABA"));
            btn.setClickable(false);

        }
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == Utils.MY_PROFILE_REQ_CODE) {
            userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

            vEmail = generalFunc.getJsonValue("vEmail", userProfileJson);
            vPhone = generalFunc.getJsonValue("vCode", userProfileJson) + "" + generalFunc.getJsonValue("vPhone", userProfileJson);


            String ePhoneVerified = generalFunc.getJsonValue("ePhoneVerified", userProfileJson);
            String eEmailVerified = generalFunc.getJsonValue("eEmailVerified", userProfileJson);

            if (isEditInfoTapped == true) {

                if (!phonetxt.getText().toString().equalsIgnoreCase("") && !ePhoneVerified.equalsIgnoreCase("Yes")
                        && !emailTxt.getText().toString().equalsIgnoreCase("") && !eEmailVerified.equalsIgnoreCase("Yes")) {
                    reqType = "DO_EMAIL_PHONE_VERIFY";
                    emailVerificationCode = "";
                    phoneVerificationCode = "";

                    sendVerificationSMS(null);

                } else if (!phonetxt.getText().toString().equalsIgnoreCase("") && !ePhoneVerified.equalsIgnoreCase("Yes")) {
                    reqType = "DO_PHONE_VERIFY";

                    phoneVerificationCode = "";
                    sendVerificationSMS(null);

                } else if (!emailTxt.getText().toString().equalsIgnoreCase("") && !eEmailVerified.equalsIgnoreCase("Yes")) {
                    reqType = "DO_EMAIL_VERIFY";
                    emailVerificationCode = "";

                    sendVerificationSMS(null);

                } else {

                }

                isEditInfoTapped = false;
            }


            setLabels();


        }
    }


    @Override
    public void onBackPressed() {

        if (isProcessRunning)
        {
            final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
            generateAlert.setCancelable(false);
            generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                @Override
                public void handleBtnClick(int btn_id) {
                    generateAlert.closeAlertBox();

                    if (btn_id==0)
                    {
                        VerifyInfoActivity.super.onBackPressed();
                    }


                }
            });
            generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("Are you sure you want to cancel current running request's process?","LBL_CANCEL_VERIFY_SCREEN_PROCESS_TXT"));
            generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_CONTINUE_TXT"));
            generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));
            generateAlert.showAlertBox();
        }
         else
        {
            super.onBackPressed();
        }


    }


}
