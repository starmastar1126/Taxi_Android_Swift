package com.adapter.files;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.fastcabtaxi.driver.R;
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
    ArrayList<String> templist = new ArrayList<>();
    ArrayList<String> sellist = new ArrayList<>();

    ArrayList<HashMap<String, String>> timeSlotList;
    ArrayList<HashMap<String, String>> selTimeSlotList;
    ArrayList<HashMap<String, String>> checkTimeSlotList;


    public TimeSlotAdapter(Context context, ArrayList<HashMap<String, String>> timeSlotList, ArrayList<HashMap<String, String>> selTimeSlotList,ArrayList<HashMap<String, String>> checkTimeSlotList) {
        this.mContext = context;
        this.timeSlotList = timeSlotList;
        this.selTimeSlotList = selTimeSlotList;
        this.checkTimeSlotList=checkTimeSlotList;


    }

    @Override
    public TimeSlotAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {


        View view = LayoutInflater.from(mContext).inflate(R.layout.item_timeslot_view, parent, false);


        return new TimeSlotAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(final TimeSlotAdapter.ViewHolder holder, final int position) {


//        if (position == 12) {
//            k = 1;
//        }
//
//        if (position < 8) {
//            Utils.printLog("timeslot", "::" + "0" + (position + 1) + "-" + "0" + (position + 2));
//            if ((sellist.contains("0" + (position + 1) + "-" + "0" + (position + 2)))) {
//                templist.add(position + "");
//            }
//
//        } else if (position == 8) {
//            if ((sellist.contains("0" + position + 1 + "-" + (position + 2)))) {
//                templist.add(position + "");
//            }
//
//        } else {
//            if ((sellist.contains(position + 1 + "-" + (position + 2)))) {
//                templist.add(position + "");
//            }
//        }
//
//        if (position < 11) {
//
//            holder.stratTimeTxtView.setText(position + 1 + " - " + (position + 2) + " " + "am");
//            holder.stratselTimeTxtView.setText(position + 1 + " - " + (position + 2) + " " + "am");
//        } else {
//            if (position == 11) {
//
//                holder.stratTimeTxtView.setText(12 + " - " + 1 + " " + "pm");
//                holder.stratselTimeTxtView.setText(12 + " - " + 1 + " " + "pm");
//            } else {
//
//                holder.stratTimeTxtView.setText(k + " - " + (k + 1) + " " + "pm");
//                holder.stratselTimeTxtView.setText(k + " - " + (k + 1) + " " + "pm");
//                k++;
//            }
//
//        }
        holder.stratTimeTxtView.setText(timeSlotList.get(position).get("name"));
        holder.stratselTimeTxtView.setText(timeSlotList.get(position).get("name"));


        holder.selmainarea.setVisibility(View.GONE);
        holder.mainarea.setVisibility(View.VISIBLE);


        if (timeSlotList.size() > 0) {
            for (int j = 0; j < selTimeSlotList.size(); j++) {
                if (selTimeSlotList.get(j).get("selname").equals(checkTimeSlotList.get(position).get("selname"))) {

                    HashMap<String, String> mapdata = timeSlotList.get(position);
                    mapdata.put("status", "yes");
                    timeSlotList.set(position, mapdata);

                    if (selTimeSlotList.get(j).get("status").equals("yes")) {
                        isSelectedPos = position;

                    }

                    selTimeSlotList.remove(j);

                }
            }
        }


        if (timeSlotList.get(position).get("status").equals("yes")) {
            holder.selmainarea.setVisibility(View.VISIBLE);
            holder.mainarea.setVisibility(View.GONE);
        }


        holder.mainarea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                HashMap<String, String> map = timeSlotList.get(position);
                map.put("status", "yes");
                timeSlotList.set(position, map);

                if (setRecentTimeSlotClickList != null) {
                    setRecentTimeSlotClickList.itemTimeSlotLocClick(timeSlotList);
                }

                notifyDataSetChanged();

            }
        });

        holder.selmainarea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {


                isSelectedPos = position;
                HashMap<String, String> map = timeSlotList.get(position);
                map.put("status", "no");
                timeSlotList.set(position, map);

                if (setRecentTimeSlotClickList != null) {
                    setRecentTimeSlotClickList.itemTimeSlotLocClick(timeSlotList);
                }

                notifyDataSetChanged();

            }
        });


    }

    @Override
    public int getItemCount() {
        //  return recentList.size();
        return timeSlotList.size();
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

    public interface setRecentTimeSlotClickList {
        void itemTimeSlotLocClick(ArrayList<HashMap<String, String>> timeSlotList);
    }

    public void setOnClickList(setRecentTimeSlotClickList setRecentTimeSlotClickList) {
        this.setRecentTimeSlotClickList = setRecentTimeSlotClickList;
    }


}
