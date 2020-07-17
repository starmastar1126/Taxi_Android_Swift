package com.fastcabtaxi.passenger;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.view.ViewPager;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.ImageView;

import com.adapter.files.ViewPagerAdapter;
import com.fragments.BookingFragment;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;
import com.view.MaterialTabs;

import java.util.ArrayList;

public class MyBookingsActivity extends AppCompatActivity {

    public GeneralFunctions generalFunc;
    MTextView titleTxt;
    ImageView backImgView;
    String userProfileJson;
    CharSequence[] titles;
    String app_type = "Ride";
    boolean isrestart = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_bookings);

        generalFunc = new GeneralFunctions(getActContext());

        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        isrestart = getIntent().getBooleanExtra("isrestart", false);


        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);


        backImgView.setOnClickListener(new setOnClickList());

        setLabels();


        ViewPager appLogin_view_pager = (ViewPager) findViewById(R.id.appLogin_view_pager);
        MaterialTabs material_tabs = (MaterialTabs) findViewById(R.id.material_tabs);

        app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);
        ArrayList<Fragment> fragmentList = new ArrayList<>();

        if (app_type.equalsIgnoreCase("Ride-Delivery")) {
            titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_RIDE"), generalFunc.retrieveLangLBl("", "LBL_DELIVER")};
            material_tabs.setVisibility(View.VISIBLE);
            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_Ride));
            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_Deliver));
        } else if (app_type.equalsIgnoreCase("Delivery")) {
            titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_DELIVER")};
            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_Deliver));
            material_tabs.setVisibility(View.GONE);
        } else if (app_type.equalsIgnoreCase("UberX")) {
            titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_UBERX")};
            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_UberX));
            material_tabs.setVisibility(View.GONE);
        } else {
            titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_RIDE")};
            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_Ride));
            material_tabs.setVisibility(View.GONE);
        }

        ViewPagerAdapter adapter = new ViewPagerAdapter(getSupportFragmentManager(), titles, fragmentList);
        appLogin_view_pager.setAdapter(adapter);
        material_tabs.setViewPager(appLogin_view_pager);

    }

    public BookingFragment generateBookingFrag(String bookingType) {
        BookingFragment frag = new BookingFragment();
        Bundle bn = new Bundle();
        bn.putString("BOOKING_TYPE", bookingType);

        frag.setArguments(bn);

        return frag;
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_MY_BOOKINGS"));
    }


    @Override
    protected void onResume() {

        app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);
        super.onResume();

    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }

    public Context getActContext() {
        return MyBookingsActivity.this;
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();

    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(getActContext());
            switch (view.getId()) {
                case R.id.backImgView:
                    MyBookingsActivity.super.onBackPressed();
                    break;

            }
        }
    }

}
