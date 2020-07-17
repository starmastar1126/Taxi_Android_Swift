package com.fastcabtaxi.driver;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.Fragment;
import android.support.v4.view.ViewPager;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.ImageView;

import com.adapter.files.ViewPagerAdapter;
import com.fragments.BookingFragment;
import com.fragments.RideHistoryFragment;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;
import com.view.MaterialTabs;

import java.util.ArrayList;

public class HistoryActivity extends AppCompatActivity {

    public GeneralFunctions generalFunc;
    MTextView titleTxt;
    ImageView backImgView;
    public String userProfileJson;
    CharSequence[] titles;
    String app_type = "Ride";
    boolean ispending = false;
    boolean isupcoming = false;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_history);

        generalFunc = new GeneralFunctions(getActContext());

        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        isupcoming = getIntent().getBooleanExtra("isupcoming", false);
        ispending = getIntent().getBooleanExtra("ispending", false);

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        backImgView.setOnClickListener(new setOnClickList());


        setLabels();

        ViewPager appLogin_view_pager = (ViewPager) findViewById(R.id.appLogin_view_pager);
        MaterialTabs material_tabs = (MaterialTabs) findViewById(R.id.material_tabs);

        app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);
        final ArrayList<Fragment> fragmentList = new ArrayList<>();


        if (app_type.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
            if (generalFunc.getJsonValue("RIDE_LATER_BOOKING_ENABLED", userProfileJson).equalsIgnoreCase("Yes")) {
                titles = new CharSequence[]{generalFunc.retrieveLangLBl("Pending", "LBL_PENDING"), generalFunc.retrieveLangLBl("", "LBL_UPCOMING"), generalFunc.retrieveLangLBl("", "LBL_PAST")};
                material_tabs.setVisibility(View.VISIBLE);
                fragmentList.add(generateBookingFragPendiing(Utils.Upcoming));
                fragmentList.add(generateBookingFrag(Utils.Upcoming));
                fragmentList.add(generateBookingFragHistory(Utils.Past));
            } else {
                titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_PAST")};
                material_tabs.setVisibility(View.GONE);
                fragmentList.add(generateBookingFragHistory(Utils.Past));

            }
        } else {

            if (generalFunc.getJsonValue("RIDE_LATER_BOOKING_ENABLED", userProfileJson).equalsIgnoreCase("Yes")) {
                titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_PAST"), generalFunc.retrieveLangLBl("", "LBL_UPCOMING"),};
                material_tabs.setVisibility(View.VISIBLE);
                fragmentList.add(generateBookingFragHistory(Utils.Past));
                fragmentList.add(generateBookingFrag(Utils.Upcoming));
            } else {
                titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_PAST")};
                material_tabs.setVisibility(View.GONE);
                fragmentList.add(generateBookingFragHistory(Utils.Past));

            }


        }


        ViewPagerAdapter adapter = new ViewPagerAdapter(getSupportFragmentManager(), titles, fragmentList);
        appLogin_view_pager.setAdapter(adapter);
        material_tabs.setViewPager(appLogin_view_pager);

        if (ispending) {
            appLogin_view_pager.setCurrentItem(0);
        }
        if (isupcoming) {
            appLogin_view_pager.setCurrentItem(1);
        }

        appLogin_view_pager.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {


                Utils.printLog("viewpager", "::" + "onPageScrolled");
                fragmentList.get(position).onResume();
            }

            @Override
            public void onPageSelected(int position) {
                Utils.printLog("viewpager", "::" + "onPageSelected");
            }

            @Override
            public void onPageScrollStateChanged(int state) {

                Utils.printLog("viewpager", "::" + "onPageScrollStateChanged");
            }
        });


    }

    public void finishScreens() {
        ActivityCompat.finishAffinity(HistoryActivity.this);
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_YOUR_TRIPS"));

    }


    @Override
    protected void onResume() {

        app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);
        super.onResume();

    }

    public Context getActContext() {
        return HistoryActivity.this;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(HistoryActivity.this);
            switch (view.getId()) {
                case R.id.backImgView:
                    HistoryActivity.super.onBackPressed();
                    break;

            }
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (resultCode == Activity.RESULT_OK) {
            ViewPager appLogin_view_pager = (ViewPager) findViewById(R.id.appLogin_view_pager);
            MaterialTabs material_tabs = (MaterialTabs) findViewById(R.id.material_tabs);

            userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
            app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);
            ArrayList<Fragment> fragmentList = new ArrayList<>();

            ViewPagerAdapter adapter = new ViewPagerAdapter(getSupportFragmentManager(), titles, fragmentList);
            appLogin_view_pager.setAdapter(adapter);
            material_tabs.setViewPager(appLogin_view_pager);


        }
    }

    public BookingFragment generateBookingFrag(String bookingType) {
        BookingFragment frag = new BookingFragment();
        Bundle bn = new Bundle();
        bn.putString("BOOKING_TYPE", bookingType);
        bn.putString("type", "LATER");
        frag.setArguments(bn);
        return frag;
    }

    public BookingFragment generateBookingFragPendiing(String bookingType) {
        BookingFragment frag = new BookingFragment();
        Bundle bn = new Bundle();
        bn.putString("BOOKING_TYPE", bookingType);
        bn.putString("type", "PENDING");
        frag.setArguments(bn);
        return frag;
    }


    public RideHistoryFragment generateBookingFragHistory(String bookingType) {
        RideHistoryFragment frag = new RideHistoryFragment();
        Bundle bn = new Bundle();
        bn.putString("BOOKING_TYPE", bookingType);
        bn.putString("type", "PAST");
        frag.setArguments(bn);
        return frag;
    }


}
