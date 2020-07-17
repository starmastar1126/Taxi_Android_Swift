package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v7.app.AppCompatActivity;
import android.text.Editable;
import android.text.InputFilter;
import android.text.InputType;
import android.text.TextWatcher;
import android.text.method.LinkMovementMethod;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.ScrollView;

import com.general.files.DecimalDigitsInputFilter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;

import java.util.HashMap;

/**
 * Created by Admin on 04-11-2016.
 */
public class MyWalletActivity extends AppCompatActivity {

    public GeneralFunctions generalFunc;
    MTextView titleTxt;
    ImageView backImgView;
    ProgressBar loading_wallet_history;
    MTextView viewTransactionsTxt;

    ErrorView errorView;
    String required_str = "";
    String error_money_str = "";
    String userProfileJson = "";
    boolean mIsLoading = false;
    String next_page_str = "0";
    private ScrollView scrollView;
    private MaterialEditText rechargeBox;
    private MTextView policyTxt;
    private MTextView termsTxt;
    private MTextView yourBalTxt;
    private MButton btn_type1, btn_type2;

    private MTextView addMoneybtn1;
    private MTextView addMoneybtn2;
    private MTextView addMoneybtn3;
    private MTextView withDrawMoneyTxt;
    private MTextView addMoneyTagTxt;
    private MTextView addMoneyTxt;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_mywallet);

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);

        loading_wallet_history = (ProgressBar) findViewById(R.id.loading_wallet_history);
        viewTransactionsTxt = (MTextView) findViewById(R.id.viewTransactionsTxt);
        errorView = (ErrorView) findViewById(R.id.errorView);
        addMoneybtn1 = (MTextView) findViewById(R.id.addMoneybtn1);
        addMoneybtn2 = (MTextView) findViewById(R.id.addMoneybtn2);
        addMoneybtn3 = (MTextView) findViewById(R.id.addMoneybtn3);
        withDrawMoneyTxt = (MTextView) findViewById(R.id.withDrawMoneyTxt);
        addMoneyTxt = (MTextView) findViewById(R.id.addMoneyTxt);
        addMoneyTagTxt = (MTextView) findViewById(R.id.addMoneyTagTxt);
        errorView = (ErrorView) findViewById(R.id.errorView);
        rechargeBox = (MaterialEditText) findViewById(R.id.rechargeBox);
        termsTxt = (MTextView) findViewById(R.id.termsTxt);
        yourBalTxt = (MTextView) findViewById(R.id.yourBalTxt);
        policyTxt = (MTextView) findViewById(R.id.policyTxt);
        scrollView = (ScrollView) findViewById(R.id.scrollView);
        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        btn_type1 = ((MaterialRippleLayout) findViewById(R.id.btn_type1)).getChildView();

        generalFunc = new GeneralFunctions(getActContext());
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

        rechargeBox.setFilters(new InputFilter[]{new DecimalDigitsInputFilter(2)});

        backImgView.setOnClickListener(new setOnClickList());
        viewTransactionsTxt.setOnClickListener(new setOnClickList());
        addMoneybtn1.setOnClickListener(new setOnClickList());
        addMoneybtn2.setOnClickListener(new setOnClickList());
        addMoneybtn3.setOnClickListener(new setOnClickList());
        btn_type2.setId(Utils.generateViewId());
        btn_type2.setOnClickListener(new setOnClickList());
        btn_type1.setId(Utils.generateViewId());
        btn_type1.setOnClickListener(new setOnClickList());
        termsTxt.setOnClickListener(new setOnClickList());

        withDrawMoneyTxt.setMovementMethod(LinkMovementMethod.getInstance());

        setLabels();

        withDrawMoneyTxt.setVisibility(View.GONE);
        if (!generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Cash")) {
            ((LinearLayout) findViewById(R.id.addMoneyToWalletArea)).setVisibility(View.VISIBLE);
        }
        getTransactionHistory(false);

        rechargeBox.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {

            }

            @Override
            public void afterTextChanged(Editable s) {


                if (rechargeBox.getText().length() == 1) {
                    if (rechargeBox.getText().toString().contains(".")) {
                        rechargeBox.setText("0.");
                        rechargeBox.setSelection(rechargeBox.length());
                    }
                }

            }
        });
    }

    public void setLabels() {

        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_LEFT_MENU_WALLET"));
        yourBalTxt.setText(generalFunc.retrieveLangLBl("", "LBL_USER_BALANCE"));
        viewTransactionsTxt.setText(generalFunc.retrieveLangLBl("", "LBL_VIEW_TRANS_HISTORY"));
        btn_type1.setText(generalFunc.retrieveLangLBl("", "LBL_VIEW_TRANS_HISTORY"));

        rechargeBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_RECHARGE_AMOUNT_TXT"), generalFunc.retrieveLangLBl("", "LBL_RECHARGE_AMOUNT_TXT"));
        //rechargeBox.setInputType(InputType.TYPE_CLASS_NUMBER);
        rechargeBox.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_SIGNED | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        rechargeBox.getLabelFocusAnimator().start();


        withDrawMoneyTxt.setText(generalFunc.retrieveLangLBl("", "LBL_WITHDRAW_MONEY_TXT"));
        addMoneyTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_MONEY_TXT"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_MONEY_TXT"));
        addMoneyTagTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_MONEY_TXT1"));
        policyTxt.setText(generalFunc.retrieveLangLBl("", "LBL_PRIVACY_POLICY"));
        termsTxt.setText(generalFunc.retrieveLangLBl("", "LBL_PRIVACY_POLICY1"));

        required_str = generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT");
        error_money_str = generalFunc.retrieveLangLBl("", "LBL_ADD_CORRECT_DETAIL_TXT");

        addMoneybtn1.setText(generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("WALLET_FIXED_AMOUNT_1", userProfileJson)));
        addMoneybtn2.setText(generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("WALLET_FIXED_AMOUNT_2", userProfileJson)));
        addMoneybtn3.setText(generalFunc.convertNumberWithRTL(generalFunc.getJsonValue("WALLET_FIXED_AMOUNT_3", userProfileJson)));

    }

    public void checkValues() {
        Utils.hideKeyboard(getActContext());

        Double moneyAdded = 0.0;

        if (Utils.checkText(rechargeBox) == true) {

            moneyAdded = generalFunc.parseDoubleValue(0, Utils.getText(rechargeBox));
        }
        boolean addMoneyAmountEntered = Utils.checkText(rechargeBox) ? (moneyAdded > 0 ? true : Utils.setErrorFields(rechargeBox, error_money_str))
                : Utils.setErrorFields(rechargeBox, required_str);

        if (addMoneyAmountEntered == false) {
            return;
        }

        addMoneyToWallet();
    }

    private void addMoneyToWallet() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "addMoneyUserWallet");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("fAmount", Utils.getText(rechargeBox));
        parameters.put("UserType", CommonUtilities.app_type);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {


                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        rechargeBox.setText("");
                        String memberBalance = generalFunc.getJsonValue("MemberBalance", responseString);


                        ((MTextView) findViewById(R.id.walletamountTxt)).setText(generalFunc.convertNumberWithRTL(memberBalance));
                        generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str_one, responseString)));
                    } else {
                        String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);

                        if (message.equalsIgnoreCase("LBL_NO_CARD_AVAIL_NOTE")) {

                            final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                            generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                            generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_ADD_CARD"));
                            generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"));
                            generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                                @Override
                                public void handleBtnClick(int btn_id) {

                                    if (btn_id == 1) {
                                        generateAlert.closeAlertBox();
                                        Bundle bn = new Bundle();
                                        new StartActProcess(getActContext()).startActForResult(CardPaymentActivity.class, bn, Utils.CARD_PAYMENT_REQ_CODE);

                                    } else {
                                        generateAlert.closeAlertBox();
                                    }

                                }
                            });

                            generateAlert.showAlertBox();

                        } else {
                            generalFunc.showGeneralMessage("",
                                    generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        }

                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();

    }

    public void closeLoader() {
        if (loading_wallet_history.getVisibility() == View.VISIBLE) {
            loading_wallet_history.setVisibility(View.GONE);
        }
    }

    public void generateErrorView() {

        closeLoader();
        generalFunc.generateErrorView(errorView, "", "LBL_NO_INTERNET_TXT");

        if (errorView.getVisibility() != View.VISIBLE) {
            errorView.setVisibility(View.VISIBLE);
            scrollView.setVisibility(View.GONE);
        }
        errorView.setOnRetryListener(new ErrorView.RetryListener() {
            @Override
            public void onRetry() {
                getTransactionHistory(false);
            }
        });
    }

    public void getTransactionHistory(final boolean isLoadMore) {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
            scrollView.setVisibility(View.VISIBLE);
        }
        if (loading_wallet_history.getVisibility() != View.VISIBLE && isLoadMore == false) {
            loading_wallet_history.setVisibility(View.VISIBLE);
            scrollView.setVisibility(View.GONE);
        }

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getTransactionHistory");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("page", next_page_str);
//        parameters.put("TimeZone", generalFunc.getTimezone());

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    closeLoader();
                    scrollView.setVisibility(View.VISIBLE);

                    String LBL_BALANCE = generalFunc.getJsonValue("user_available_balance", responseString);

                    ((MTextView) findViewById(R.id.yourBalTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_USER_BALANCE"));

                    ((MTextView) findViewById(R.id.walletamountTxt)).setText(generalFunc.convertNumberWithRTL(LBL_BALANCE));


                } else {
                    if (isLoadMore == false) {
                        generateErrorView();
                    }
                }

                mIsLoading = false;
            }
        });
        exeWebServer.execute();
    }

    public Context getActContext() {
        return MyWalletActivity.this;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            if (view.getId() == btn_type2.getId()) {
                checkValues();
            } else if (view.getId() == btn_type1.getId()) {
                new StartActProcess(getActContext()).startAct(MyWalletHistoryActivity.class);
            }
            switch (view.getId()) {
                case R.id.backImgView:
                    onBackPressed();
                    break;
                case R.id.viewTransactionsTxt:
                    new StartActProcess(getActContext()).startAct(MyWalletHistoryActivity.class);
                    break;
                case R.id.addMoneybtn1:
                    rechargeBox.setText(generalFunc.getJsonValue("WALLET_FIXED_AMOUNT_1", userProfileJson));
                    break;
                case R.id.addMoneybtn2:
                    rechargeBox.setText(generalFunc.getJsonValue("WALLET_FIXED_AMOUNT_2", userProfileJson));
                    break;
                case R.id.addMoneybtn3:
                    rechargeBox.setText(generalFunc.getJsonValue("WALLET_FIXED_AMOUNT_3", userProfileJson));
                    break;
                case R.id.termsTxt:
                    Bundle bn = new Bundle();
                    bn.putString("staticpage", "4");
                    new StartActProcess(getActContext()).startActWithData(StaticPageActivity.class, bn);
                    break;
            }
        }
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }
}

