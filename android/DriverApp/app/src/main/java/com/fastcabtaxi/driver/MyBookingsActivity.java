package com.fastcabtaxi.driver;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.Fragment;
import android.support.v4.view.ViewPager;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.ImageView;

import com.adapter.files.ViewPagerAdapter;
import com.fragments.BookingFragment;
import com.general.files.GeneralFunctions;
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

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_bookings);

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);

        generalFunc = new GeneralFunctions(getActContext());
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);

        backImgView.setOnClickListener(new setOnClickList());

        setLabels();

        ViewPager appLogin_view_pager = (ViewPager) findViewById(R.id.appLogin_view_pager);
        MaterialTabs material_tabs = (MaterialTabs) findViewById(R.id.material_tabs);

        String APP_TYPE = generalFunc.getJsonValue("APP_TYPE", userProfileJson);

        ArrayList<Fragment> fragmentList = new ArrayList<>();

        if (APP_TYPE.equalsIgnoreCase("Ride-Delivery")) {
            titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_RIDE"), generalFunc.retrieveLangLBl("", "LBL_DELIVER")};
            material_tabs.setVisibility(View.VISIBLE);
            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_Ride));

            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_Deliver));
        } else if (APP_TYPE.equalsIgnoreCase("Delivery")) {
            titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_DELIVER")};
            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_Deliver));

            material_tabs.setVisibility(View.GONE);
        } else if (APP_TYPE.equalsIgnoreCase("UberX")) {
            titles = new CharSequence[]{generalFunc.retrieveLangLBl("", "LBL_UBERX")};
            fragmentList.add(generateBookingFrag(Utils.CabGeneralType_UberX));
            material_tabs.setVisibility(View.GONE);
        } else if (APP_TYPE.equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
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

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_MY_BOOKINGS"));
    }

    public BookingFragment generateBookingFrag(String bookingType) {
        BookingFragment frag = new BookingFragment();
        Bundle bn = new Bundle();
        bn.putString("BOOKING_TYPE", bookingType);
        frag.setArguments(bn);
        return frag;
    }

    public Context getActContext() {
        return MyBookingsActivity.this;
    }

    public void finishScreens() {
        ActivityCompat.finishAffinity(MyBookingsActivity.this);
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(MyBookingsActivity.this);
            switch (view.getId()) {
                case R.id.backImgView:
                    MyBookingsActivity.super.onBackPressed();
                    break;

            }
        }
    }
}
