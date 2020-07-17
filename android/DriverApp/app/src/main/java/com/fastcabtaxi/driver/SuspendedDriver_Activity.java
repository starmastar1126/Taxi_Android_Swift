package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.ImageView;

import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

public class SuspendedDriver_Activity extends AppCompatActivity {




    GeneralFunctions generalFunc;

    MButton btn_type2;
    int submitBtnId;
    ImageView menuImgView;
    MTextView suspendedNote;
    ImageView menuImgRightView;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_suspended_driver_);
        initView();
        setLabel();
    }

    private void initView()
    {
        generalFunc=new GeneralFunctions(getActContext());

        menuImgView=(ImageView) findViewById(R.id.menuImgView);


        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        menuImgRightView=(ImageView)findViewById(R.id.menuImgRightView);

        submitBtnId = Utils.generateViewId();
        btn_type2.setId(submitBtnId);

        btn_type2.setOnClickListener(new setOnClickList());
        suspendedNote=(MTextView)findViewById(R.id.suspendedNote);
        menuImgView.setVisibility(View.GONE);

        menuImgRightView.setOnClickListener(new setOnClickList());
        menuImgRightView.setVisibility(View.VISIBLE);


    }

    public Context getActContext() {
        return SuspendedDriver_Activity.this;
    }

    private void setLabel()
    {


        btn_type2.setText(generalFunc.retrieveLangLBl("Contact Us", "LBL_FOOTER_HOME_CONTACT_US_TXT"));
        suspendedNote.setText(generalFunc.retrieveLangLBl("Oops! Seems your account is Suspended.Kindly contact administrator.","LBL_CONTACT_US_STATUS_SUSPENDED_DRIVER"));
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {

            int i=view.getId();

             if (i == submitBtnId) {

                 new StartActProcess(getActContext()).startAct(ContactUsActivity.class);

            }
            else if(i == menuImgRightView.getId())
             {
                 generalFunc.logOutUser();
                 generalFunc.restartApp();
             }

        }
    }
}
