package com.fastcabtaxi.passenger;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.ImageView;

import com.fragments.AddCardFragment;
import com.fragments.ViewCardFragment;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;

public class BookingSummaryActivity extends AppCompatActivity {


    public GeneralFunctions generalFunc;
    public String serviceItemname = "";
    public String serviceprice = "";
    public String userProfileJson = "";
    public String bookingtype = "";
    public String comment = "";
    public String promocode = "";
    public String Quantity = "";
    public String Quantityprice = "";
    public String Pname = "";
    public String Stime = "";
    public String Sdate = "";
    public ImageView backImgView;
    public String ACCEPT_CASH_TRIPS;
    MTextView titleTxt;
    ViewCardFragment viewCardFrag;
    AddCardFragment addCardFrag;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_booking_summary);

        generalFunc = new GeneralFunctions(getActContext());
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView.setOnClickListener(new setOnClick());
        serviceItemname = getIntent().getStringExtra("SelectvVehicleType");
        serviceprice = getIntent().getStringExtra("SelectvVehiclePrice");
        bookingtype = getIntent().getStringExtra("type");
        Quantity = getIntent().getStringExtra("Quantity");
        ACCEPT_CASH_TRIPS = getIntent().getStringExtra("ACCEPT_CASH_TRIPS");

        Quantityprice = getIntent().getStringExtra("Quantityprice");
        Pname = getIntent().getStringExtra("Pname");
        Sdate = getIntent().getStringExtra("Sdate");
        Stime = getIntent().getStringExtra("Stime");

        titleTxt.setText(generalFunc.retrieveLangLBl("Booking Details", "LBL_BOOKING_DETAILS_TXT"));
    }

    public Context getActContext() {
        return BookingSummaryActivity.this;
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }

    public void changeUserProfileJson(String userProfileJson) {
        this.userProfileJson = userProfileJson;

    }

    public void openAddCardFrag(String mode) {

        if (addCardFrag != null) {
            addCardFrag = null;
            viewCardFrag = null;
            Utils.runGC();
        }

        Bundle bundle = new Bundle();
        bundle.putString("PAGE_MODE", mode);
        addCardFrag = new AddCardFragment();
        addCardFrag.setArguments(bundle);
        getSupportFragmentManager().beginTransaction()
                .replace(R.id.cardarea, addCardFrag).commit();
    }

    public class setOnClick implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            if (i == R.id.backImgView) {
                onBackPressed();
            }
        }
    }

}
