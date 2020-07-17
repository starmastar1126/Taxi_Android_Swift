package com.adapter.files;

import android.content.Context;
import android.graphics.Color;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.fastcabtaxi.passenger.R;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.MTextView;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;

/**
 * Created by tarwindersingh on 06/01/18.
 */

public class DatesRecyclerAdapter extends RecyclerView.Adapter<DatesRecyclerAdapter.ViewHolder> {

    GeneralFunctions generalFunc;
    ArrayList<Date> listData;
    Context mContext;
    OnDateSelectListener onDateSelectListener;

    Date selectedDate;
    private Locale locale;

    private java.text.DateFormat dayNameFormatter;
    private java.text.DateFormat dayNumFormatter;
    private java.text.DateFormat dayMonthFormatter;

    public DatesRecyclerAdapter(GeneralFunctions generalFunc, ArrayList<Date> listData, Context mContext, Date selectedDate) {
        this.generalFunc = generalFunc;
        this.listData = listData;
        this.mContext = mContext;
        this.selectedDate = selectedDate;

        locale = new Locale(generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY));
        dayNameFormatter = new SimpleDateFormat("EEE", locale);
        dayNumFormatter = new SimpleDateFormat("dd", locale);
        dayMonthFormatter = new SimpleDateFormat("MMM", locale);
    }

    public void setSelectedDate(Date selectedDate) {
        this.selectedDate = selectedDate;
        this.notifyDataSetChanged();
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_dates_design, parent, false);

        ViewHolder viewHolder = new ViewHolder(view);
        return viewHolder;
    }


    public void setOnDateSelectListener(OnDateSelectListener onDateSelectListener) {
        this.onDateSelectListener = onDateSelectListener;
    }

    // Replace the contents of a view (invoked by the layout manager)
    @Override
    public void onBindViewHolder(final ViewHolder holder, final int position) {


        final Date currentDate = listData.get(position);

        holder.dayTxtView.setText(dayNameFormatter.format(currentDate));
        holder.dayNumTxtView.setText(dayNumFormatter.format(currentDate));

        if (selectedDate.equals(currentDate)) {
            new CreateRoundedView(mContext.getResources().getColor(R.color.appThemeColor_1), Utils.dipToPixels(mContext, 25), Utils.dipToPixels(mContext, 1),
                    Color.parseColor("#CECECE"), holder.dayNumTxtView);

            holder.dayNumTxtView.setTextColor(mContext.getResources().getColor(R.color.appThemeColor_TXT_1));

            if (onDateSelectListener != null) {
                onDateSelectListener.onDateSelect(position);
            }
        } else {
            new CreateRoundedView(Color.parseColor("#ffffff"), Utils.dipToPixels(mContext, 25), Utils.dipToPixels(mContext, 1),
                    Color.parseColor("#FFFFFF"), holder.dayNumTxtView);
            holder.dayNumTxtView.setTextColor(Color.parseColor("#1C1C1C"));
        }


        holder.containerView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                setSelectedDate(currentDate);

                if (onDateSelectListener != null) {
                    onDateSelectListener.onDateSelect(position);
                }
            }
        });

    }

    // Return the size of your itemsData (invoked by the layout manager)
    @Override
    public int getItemCount() {
        return listData.size();
    }


    public interface OnDateSelectListener {
        void onDateSelect(int position);
    }

    // inner class to hold a reference to each item of RecyclerView
    public class ViewHolder extends RecyclerView.ViewHolder {


        private MTextView dayTxtView;
        private MTextView dayNumTxtView;
        private View containerView;

        public ViewHolder(View view) {
            super(view);

            containerView = view;
            dayTxtView = (MTextView) view.findViewById(R.id.dayTxtView);
            dayNumTxtView = (MTextView) view.findViewById(R.id.dayNumTxtView);

        }
    }
}
