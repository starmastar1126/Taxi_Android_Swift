package com.fastcabtaxi.passenger;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.text.InputType;
import android.view.Gravity;
import android.view.View;
import android.widget.ImageView;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;

import java.util.HashMap;

public class ContactUsActivity extends AppCompatActivity {
    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;

    MaterialEditText subjectBox;
    MaterialEditText contentBox;
    MButton btn_type2;

    String required_str = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_contact_us);

        generalFunc = new GeneralFunctions(getActContext());


        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        subjectBox = (MaterialEditText) findViewById(R.id.subjectBox);
        contentBox = (MaterialEditText) findViewById(R.id.contentBox);
        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();


        setLabels();


        btn_type2.setId(Utils.generateViewId());
        btn_type2.setOnClickListener(new setOnClickList());

        backImgView.setOnClickListener(new setOnClickList());
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CONTACT_US_HEADER_TXT"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_SEND_QUERY_BTN_TXT"));

        subjectBox.setHint(generalFunc.retrieveLangLBl("", "LBL_ADD_SUBJECT_HINT_CONTACT_TXT"));
        subjectBox.setFloatingLabelText(generalFunc.retrieveLangLBl("Reason to contact", "LBL_RES_TO_CONTACT"));
        subjectBox.setFloatingLabelAlwaysShown(true);

        contentBox.setHint(generalFunc.retrieveLangLBl("", "LBL_CONTACT_US_WRITE_EMAIL_TXT"));
        contentBox.setFloatingLabelText(generalFunc.retrieveLangLBl("Your Query", "LBL_YOUR_QUERY"));
        contentBox.setFloatingLabelAlwaysShown(true);

        contentBox.setSingleLine(false);
        contentBox.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_FLAG_MULTI_LINE);
        contentBox.setGravity(Gravity.TOP);

        required_str = generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT");
    }

    public void submitQuery() {
        boolean subjectEntered = Utils.checkText(subjectBox) ? true : Utils.setErrorFields(subjectBox, required_str);
        boolean contentEntered = Utils.checkText(contentBox) ? true : Utils.setErrorFields(contentBox, required_str);

        if (subjectEntered == false || contentEntered == false) {
            return;
        }

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "sendContactQuery");
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("UserId", generalFunc.getMemberId());
        parameters.put("message", Utils.getText(contentBox));
        parameters.put("subject", Utils.getText(subjectBox));

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        contentBox.setText("");
                        subjectBox.setText("");
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

    public Context getActContext() {
        return ContactUsActivity.this;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(getActContext());
            if (i == R.id.backImgView) {
                ContactUsActivity.super.onBackPressed();
            } else if (i == btn_type2.getId()) {
                submitQuery();
            }
        }
    }

}
