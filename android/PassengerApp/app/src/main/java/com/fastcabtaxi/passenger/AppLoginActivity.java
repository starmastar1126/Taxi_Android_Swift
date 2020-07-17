package com.fastcabtaxi.passenger;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.view.WindowManager;
import android.widget.LinearLayout;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.InternetConnection;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;
import com.view.anim.loader.AVLoadingIndicatorView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

public class AppLoginActivity extends AppCompatActivity {

    public GeneralFunctions generalFunc;

    MTextView introductondetailstext, languageText, currancyText, loginbtn, registerbtn;

    LinearLayout languagearea, currencyarea;

    LinearLayout languageCurrancyArea;

    android.support.v7.app.AlertDialog list_language;

    String selected_language_code = "";

    ArrayList<String> items_txt_language = new ArrayList<String>();
    ArrayList<String> items_language_code = new ArrayList<String>();

    ArrayList<String> items_txt_currency = new ArrayList<String>();
    ArrayList<String> items_currency_symbol = new ArrayList<String>();

    String selected_currency = "";
    String selected_currency_symbol = "";

    android.support.v7.app.AlertDialog list_currency;

    String type = "";

    AVLoadingIndicatorView loaderView;
    InternetConnection intCheck;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN);
        //getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_ADJUST_PAN | WindowManager.LayoutParams.SOFT_INPUT_STATE_HIDDEN);
        setContentView(R.layout.activity_app_login);

        generalFunc = new GeneralFunctions(getActContext());
        intCheck = new InternetConnection(getActContext());
        generalFunc.getHasKey(getActContext());
        initview();
        setLabel();
        buildLanguageList();


    }

    private void initview() {

        introductondetailstext = (MTextView) findViewById(R.id.introductondetailstext);
        languageText = (MTextView) findViewById(R.id.languageText);
        currancyText = (MTextView) findViewById(R.id.currancyText);

        languagearea = (LinearLayout) findViewById(R.id.languagearea);
        currencyarea = (LinearLayout) findViewById(R.id.currencyarea);
        loginbtn = (MTextView) findViewById(R.id.loginbtn);
        registerbtn = (MTextView) findViewById(R.id.registerbtn);

        loaderView = (AVLoadingIndicatorView) findViewById(R.id.loaderView);
        loaderView.setVisibility(View.GONE);

        languageCurrancyArea = (LinearLayout) findViewById(R.id.languageCurrancyArea);

        loginbtn.setOnClickListener(new setOnClickAct());
        registerbtn.setOnClickListener(new setOnClickAct());
        languagearea.setOnClickListener(new setOnClickAct());
        currencyarea.setOnClickListener(new setOnClickAct());


    }


    private void setLabel() {
        introductondetailstext.setText(generalFunc.retrieveLangLBl("", "LBL_HOME_PASSENGER_INTRO_DETAILS"));
        loginbtn.setText(generalFunc.retrieveLangLBl("", "LBL_LOGIN"));
        registerbtn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_REGISTER_TXT"));

        languageText.setText(generalFunc.retrieveValue(CommonUtilities.DEFAULT_LANGUAGE_VALUE));
        currancyText.setText(generalFunc.retrieveValue(CommonUtilities.DEFAULT_CURRENCY_VALUE));


    }


    public void buildLanguageList() {
        JSONArray languageList_arr = generalFunc.getJsonArray(generalFunc.retrieveValue(CommonUtilities.LANGUAGE_LIST_KEY));

        for (int i = 0; i < languageList_arr.length(); i++) {
            JSONObject obj_temp = generalFunc.getJsonObject(languageList_arr, i);

            items_txt_language.add(generalFunc.getJsonValue("vTitle", obj_temp.toString()));
            items_language_code.add(generalFunc.getJsonValue("vCode", obj_temp.toString()));

            if ((generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY)).equals(generalFunc.getJsonValue("vCode", obj_temp.toString()))) {
                selected_language_code = generalFunc.getJsonValue("vCode", obj_temp.toString());

            }
        }

        CharSequence[] cs_languages_txt = items_txt_language.toArray(new CharSequence[items_txt_language.size()]);

        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());

        builder.setTitle(getSelectLangText());

        builder.setItems(cs_languages_txt, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int item) {
                // Do something with the selection

                if (list_language != null) {
                    list_language.dismiss();
                }
                selected_language_code = items_language_code.get(item);


                if (!intCheck.isNetworkConnected() && !intCheck.check_int()) {


                    generalFunc.showGeneralMessage("",
                            generalFunc.retrieveLangLBl("No Internet Connection", "LBL_NO_INTERNET_TXT"));
                } else {

                    if (!generalFunc.retrieveValue(CommonUtilities.DEFAULT_LANGUAGE_VALUE).equals(items_txt_language.get(item))) {
                        languageText.setText(items_txt_language.get(item));
                        generalFunc.storedata(CommonUtilities.LANGUAGE_CODE_KEY, selected_language_code);
                        generalFunc.storedata(CommonUtilities.DEFAULT_LANGUAGE_VALUE, items_txt_language.get(item));

                        changeLanguagedata(selected_language_code);
                    }
                }


            }
        });

        list_language = builder.create();

        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(list_language);
        }

        if (items_txt_language.size() < 2) {
            languagearea.setVisibility(View.GONE);
        }

        buildCurrencyList();

    }

    public void buildCurrencyList() {
        JSONArray currencyList_arr = generalFunc.getJsonArray(generalFunc.retrieveValue(CommonUtilities.CURRENCY_LIST_KEY));

        for (int i = 0; i < currencyList_arr.length(); i++) {
            JSONObject obj_temp = generalFunc.getJsonObject(currencyList_arr, i);

            items_txt_currency.add(generalFunc.getJsonValue("vName", obj_temp.toString()));
            items_currency_symbol.add(generalFunc.getJsonValue("vSymbol", obj_temp.toString()));
        }

        CharSequence[] cs_currency_txt = items_txt_currency.toArray(new CharSequence[items_txt_currency.size()]);

        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle(generalFunc.retrieveLangLBl("", "LBL_SELECT_CURRENCY"));

        builder.setItems(cs_currency_txt, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int item) {
                // Do something with the selection

                if (list_currency != null) {
                    list_currency.dismiss();
                }
                selected_currency_symbol = items_currency_symbol.get(item);

                selected_currency = items_txt_currency.get(item);
                currancyText.setText(items_txt_currency.get(item));

                generalFunc.storedata(CommonUtilities.DEFAULT_CURRENCY_VALUE, selected_currency);


            }
        });

        list_currency = builder.create();

        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(list_currency);
        }

        if (items_txt_currency.size() < 2) {
            currencyarea.setVisibility(View.GONE);

            if (items_txt_language.size() < 2) {
                languageCurrancyArea.setVisibility(View.GONE);
            }
        }
    }

    public Context getActContext() {
        return AppLoginActivity.this;
    }


    public String getSelectLangText() {
        return ("" + generalFunc.retrieveLangLBl("Select", "LBL_SELECT_LANGUAGE_HINT_TXT"));
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

    }

    public void showLanguageList() {
        if (!list_currency.isShowing()) {
            list_language.show();
        }
    }

    public void showCurrencyList() {
        if (!list_language.isShowing()) {
            list_currency.show();
        }
    }

    public void changeLanguagedata(String langcode) {
        loaderView.setVisibility(View.VISIBLE);
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "changelanguagelabel");
        parameters.put("vLang", langcode);
        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {


                        generalFunc.storedata(CommonUtilities.languageLabelsKey, generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        generalFunc.storedata(CommonUtilities.LANGUAGE_IS_RTL_KEY, generalFunc.getJsonValue("eType", responseString));
                        new Handler().postDelayed(new Runnable() {

                            @Override
                            public void run() {
                                loaderView.setVisibility(View.GONE);
                                generalFunc.restartApp();
                            }
                        }, 2000);


                    } else {
                        loaderView.setVisibility(View.GONE);

                    }
                } else {
                    loaderView.setVisibility(View.GONE);

                }

            }
        });
        exeWebServer.execute();
    }

    public class setOnClickAct implements View.OnClickListener {


        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(getActContext());
            if (i == R.id.languagearea) {

                if (loaderView.getVisibility() == View.GONE) {
                    showLanguageList();
                }

            } else if (i == R.id.currencyarea) {
                if (loaderView.getVisibility() == View.GONE) {
                    showCurrencyList();
                }
            } else if (i == R.id.loginbtn) {
                if (loaderView.getVisibility() == View.GONE) {
                    Bundle bundle = new Bundle();
                    bundle.putString("type", "login");

                    new StartActProcess(getActContext()).startActWithData(AppLoignRegisterActivity.class, bundle);
                }


            } else if (i == R.id.registerbtn) {
                if (loaderView.getVisibility() == View.GONE) {
                    Bundle bundle = new Bundle();
                    bundle.putString("type", "register");
                    new StartActProcess(getActContext()).startActWithData(AppLoignRegisterActivity.class, bundle);
                }

            }
        }


    }

}
