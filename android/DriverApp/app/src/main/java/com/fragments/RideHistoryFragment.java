package com.fragments;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.fastcabtaxi.driver.HistoryActivity;
import com.fastcabtaxi.driver.R;
import com.fastcabtaxi.driver.SelectedDayHistoryActivity;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.Utils;
import com.view.MTextView;
import com.view.calendarview.CalendarListener;
import com.view.calendarview.CustomCalendarView;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

public class RideHistoryFragment extends Fragment {
    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;

    CustomCalendarView calendar_view;

    View view;

    View convertView = null;
    HistoryActivity myBookingAct;
    LinearLayout calContainerView;


    //Date registrationDate = null;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        view = inflater.inflate(R.layout.activity_ride_history, container, false);

        myBookingAct = (HistoryActivity) getActivity();
        generalFunc = myBookingAct.generalFunc;




        titleTxt = (MTextView) view.findViewById(R.id.titleTxt);
        backImgView = (ImageView) view.findViewById(R.id.backImgView);
        calContainerView = (LinearLayout) view.findViewById(R.id.calContainerView);


        addCalenderView();


        backImgView.setOnClickListener(new setOnClickList());

        setLabels();


        calendar_view.setCalendarListener(new CalendarListener() {
            @Override
            public void onDateSelected(Date date) {
                Calendar cal = Calendar.getInstance();
                cal.setTime(date);

                SimpleDateFormat date_format = new SimpleDateFormat("yyyy/MM/dd", Locale.US);

                String date_formatted = date_format.format(cal.getTime());

                Bundle bn = new Bundle();
                bn.putString("SELECTED_DATE", date_formatted);

                new StartActProcess(getActContext()).startActWithData(SelectedDayHistoryActivity.class, bn);

            }

            @Override
            public void onMonthChanged(Date time) {

            }
        });

        return view;
    }

    private void addCalenderView() {
        LayoutInflater infalInflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = infalInflater.inflate(R.layout.ride_history_cal, null);
        calendar_view = (CustomCalendarView) convertView;

        calContainerView.addView(convertView);
    }


    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_RIDE_HISTORY"));
    }

    public Context getActContext() {
        return myBookingAct.getActContext();
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(getActivity());
            int i = view.getId();

        }
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }

}
