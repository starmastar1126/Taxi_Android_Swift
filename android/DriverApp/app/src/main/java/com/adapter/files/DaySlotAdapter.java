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

/**
 * Created by Admin on 09-10-2017.
 */

public class DaySlotAdapter extends RecyclerView.Adapter<DaySlotAdapter.ViewHolder> {

    Context mContext;
    View view;
    public int isSelectedPos = -1;
    setRecentTimeSlotClickList setRecentTimeSlotClickList;
    ArrayList<String> daylist;
    ArrayList<String> selectedlist;
    ArrayList<String> displaylist;

    public DaySlotAdapter(Context context, ArrayList<String> daylist, ArrayList<String> selectedlist,ArrayList<String> displaylist) {
        this.mContext = context;
        this.daylist = daylist;
        this.selectedlist = selectedlist;
        this.displaylist=displaylist;


    }

    @Override
    public DaySlotAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {


        View view = LayoutInflater.from(mContext).inflate(R.layout.item_dayslot_view, parent, false);


        return new DaySlotAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(final DaySlotAdapter.ViewHolder holder, final int position) {

        holder.stratTimeTxtView.setText(displaylist.get(position));
        holder.stratselTimeTxtView.setText(displaylist.get(position));
        holder.sTimeTxtView.setText(daylist.get(position));


        if (selectedlist.size() > 0) {
            isSelectedPos = position;
            if (selectedlist.contains(daylist.get(position))) {
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

                // notifyDataSetChanged();

            }
        });

        holder.selmainarea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                isSelectedPos = position;

                if (setRecentTimeSlotClickList != null) {
                    setRecentTimeSlotClickList.itemTimeSlotLocClick(position);
                }

                //       notifyDataSetChanged();

            }
        });


    }

    @Override
    public int getItemCount() {
        //  return recentList.size();
        return daylist.size();
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        MTextView stratTimeTxtView, stratselTimeTxtView, sTimeTxtView;
        LinearLayout mainarea, selmainarea, selarea;


        public ViewHolder(View itemView) {
            super(itemView);

            stratTimeTxtView = (MTextView) itemView.findViewById(R.id.stratTimeTxtView);
            mainarea = (LinearLayout) itemView.findViewById(R.id.mainarea);
            selmainarea = (LinearLayout) itemView.findViewById(R.id.selmainarea);
            selarea = (LinearLayout) itemView.findViewById(R.id.selarea);
            stratselTimeTxtView = (MTextView) itemView.findViewById(R.id.stratselTimeTxtView);
            sTimeTxtView = (MTextView) itemView.findViewById(R.id.sTimeTxtView);


        }
    }

    public interface setRecentTimeSlotClickList {
        void itemTimeSlotLocClick(int position);
    }

    public void setOnClickList(setRecentTimeSlotClickList setRecentTimeSlotClickList) {
        this.setRecentTimeSlotClickList = setRecentTimeSlotClickList;
    }


}
