package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.Utils;
import com.view.MTextView;

public class SupportActivity extends AppCompatActivity {

    public GeneralFunctions generalFunc;
    MTextView titleTxt;
    ImageView backImgView;

    LinearLayout aboutusarea, privacyarea, contactarea, helparea, termsCondArea;

    MTextView helpHTxt, contactHTxt, privacyHTxt, aboutusHTxt, termsHTxt;

    View seperationLine, seperationLine_contact, seperationLine_help;

    boolean islogin = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_support);
        initView();
        setLabel();

        islogin = getIntent().getBooleanExtra("islogin", false);
        if (islogin) {
            aboutusarea.setVisibility(View.GONE);
            contactarea.setVisibility(View.GONE);
            helparea.setVisibility(View.GONE);
            seperationLine_help.setVisibility(View.GONE);
            seperationLine_contact.setVisibility(View.GONE);
            seperationLine.setVisibility(View.GONE);

        }
    }


    private void initView() {

        generalFunc = new GeneralFunctions(getActContext());
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        backImgView.setOnClickListener(new setOnClickList());

        helpHTxt = (MTextView) findViewById(R.id.helpHTxt);
        contactHTxt = (MTextView) findViewById(R.id.contactHTxt);
        privacyHTxt = (MTextView) findViewById(R.id.privacyHTxt);
        aboutusHTxt = (MTextView) findViewById(R.id.aboutusHTxt);
        termsHTxt = (MTextView) findViewById(R.id.termsHTxt);

        aboutusarea = (LinearLayout) findViewById(R.id.aboutusarea);
        privacyarea = (LinearLayout) findViewById(R.id.privacyarea);
        contactarea = (LinearLayout) findViewById(R.id.contactarea);
        helparea = (LinearLayout) findViewById(R.id.helparea);
        termsCondArea = (LinearLayout) findViewById(R.id.termsCondArea);

        seperationLine = (View) findViewById(R.id.seperationLine);
        seperationLine_contact = (View) findViewById(R.id.seperationLine_contact);
        seperationLine_help = (View) findViewById(R.id.seperationLine_help);

        aboutusarea.setOnClickListener(new setOnClickList());
        privacyarea.setOnClickListener(new setOnClickList());
        contactarea.setOnClickListener(new setOnClickList());
        helparea.setOnClickListener(new setOnClickList());
        termsCondArea.setOnClickListener(new setOnClickList());


    }

    public Context getActContext() {
        return SupportActivity.this;
    }


    private void setLabel() {

        helpHTxt.setText(generalFunc.retrieveLangLBl("FAQ", "LBL_FAQ_TXT"));
        contactHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CONTACT_US_TXT"));
        privacyHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_PRIVACY_POLICY_TEXT"));
        aboutusHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ABOUT_US_TXT"));
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_SUPPORT_HEADER_TXT"));
        termsHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_TERMS_AND_CONDITION"));

    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(SupportActivity.this);
            Bundle bn = new Bundle();
            switch (view.getId()) {
                case R.id.backImgView:
                    SupportActivity.super.onBackPressed();
                    break;
                case R.id.aboutusarea:
                    bn.putString("staticpage", "1");
                    new StartActProcess(getActContext()).startActWithData(StaticPageActivity.class, bn);
                    break;
                case R.id.privacyarea:
                    bn.putString("staticpage", "33");
                    new StartActProcess(getActContext()).startActWithData(StaticPageActivity.class, bn);
                    break;
                case R.id.contactarea:
                    new StartActProcess(getActContext()).startAct(ContactUsActivity.class);
                    break;
                case R.id.helparea:
                    new StartActProcess(getActContext()).startAct(HelpActivity.class);
                    break;
                case R.id.termsCondArea:
                    bn.putString("staticpage", "4");
                    new StartActProcess(getActContext()).startActWithData(StaticPageActivity.class, bn);
                    break;

            }
        }
    }
}
