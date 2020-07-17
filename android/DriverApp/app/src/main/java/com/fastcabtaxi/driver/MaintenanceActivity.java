package com.fastcabtaxi.driver;

import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;

import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

public class MaintenanceActivity extends AppCompatActivity {

    MTextView maitenanceHTxt, maitenanceMsgTxt;

    GeneralFunctions generalFunctions;

    MButton btn_type2;
    int submitBtnId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_maintenance);

        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        btn_type2.setId(submitBtnId);

        submitBtnId = Utils.generateViewId();
        btn_type2.setId(submitBtnId);

        btn_type2.setOnClickListener(new setOnClickList());

        generalFunctions = new GeneralFunctions(MaintenanceActivity.this);
        maitenanceMsgTxt = (MTextView) findViewById(R.id.maitenanceMsgTxt);
        maitenanceHTxt = (MTextView) findViewById(R.id.maitenanceHTxt);
        maitenanceHTxt.setText(generalFunctions.retrieveLangLBl("", "LBL_MAINTENANCE_HEADER_MSG"));
        maitenanceMsgTxt.setText(generalFunctions.retrieveLangLBl("", "LBL_MAINTENANCE_CONTENT_MSG"));
        btn_type2.setText(generalFunctions.retrieveLangLBl("","LBL_CONTACT_US_HEADER_TXT"));


    }


    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();

            if (i == submitBtnId) {
                new StartActProcess(MaintenanceActivity.this).startAct(ContactUsActivity.class);
            }


        }
    }
}
