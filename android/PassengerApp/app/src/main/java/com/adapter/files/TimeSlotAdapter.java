package com.adapter.files;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.fastcabtaxi.passenger.R;
import com.view.MTextView;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 09-10-2017.
 */

public class TimeSlotAdapter extends RecyclerView.Adapter<TimeSlotAdapter.ViewHolder> {

    Context mContext;
    View view;
    int isSelectedPos = -1;
    setRecentTimeSlotClickList setRecentTimeSlotClickList;

    ArrayList<HashMap<String, String>> timeSlotList;

    public TimeSlotAdapter(Context context, ArrayList<HashMap<String, String>> timeSlotList) {
        this.mContext = context;
        this.timeSlotList = timeSlotList;


    }

    @Override
    public TimeSlotAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {


        View view = LayoutInflater.from(mContext).inflate(R.layout.item_timeslot_view, parent, false);


        return new TimeSlotAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(final TimeSlotAdapter.ViewHolder holder, final int position) {

        holder.stratTimeTxtView.setText(timeSlotList.get(position).get("name"));
        holder.stratselTimeTxtView.setText(timeSlotList.get(position).get("name"));
        if (isSelectedPos != -1) {
            if (isSelectedPos == position) {
                isSelectedPos = position;
                holder.selmainarea.setVisibility(View.VISIBLE);
                holder.mainarea.setVisibility(View.GONE);

            } else {
                holder.selmainarea.setVisibility(View.GONE);
                holder.mainarea.setVisibility(View.VISIBLE);
            }

        }

        holder.mainarea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                isSelectedPos = position;
                if (setRecentTimeSlotClickList != null) {
                    setRecentTimeSlotClickList.itemTimeSlotLocClick(position);
                }

                notifyDataSetChanged();

            }
        });


    }

    @Override
    public int getItemCount() {
        //  return recentList.size();
        // return 23;
        return timeSlotList.size();
    }

    public void setOnClickList(setRecentTimeSlotClickList setRecentTimeSlotClickList) {
        this.setRecentTimeSlotClickList = setRecentTimeSlotClickList;
    }

    public interface setRecentTimeSlotClickList {
        void itemTimeSlotLocClick(int position);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        MTextView stratTimeTxtView, stratselTimeTxtView;
        LinearLayout mainarea, selmainarea;


        public ViewHolder(View itemView) {
            super(itemView);

            stratTimeTxtView = (MTextView) itemView.findViewById(R.id.stratTimeTxtView);
            mainarea = (LinearLayout) itemView.findViewById(R.id.mainarea);
            selmainarea = (LinearLayout) itemView.findViewById(R.id.selmainarea);
            stratselTimeTxtView = (MTextView) itemView.findViewById(R.id.stratselTimeTxtView);


        }
    }


}
