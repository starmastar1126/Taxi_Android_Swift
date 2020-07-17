package com.adapter.files;

import android.content.Context;
import android.support.v4.content.ContextCompat;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.general.files.LineType;
import com.general.files.TimelineView;
import com.view.MTextView;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 19-06-2017.
 */

public class InactiveRecycleAdapter extends RecyclerView.Adapter<InactiveRecycleAdapter.ViewHolder> {


    ArrayList<HashMap<String, String>> list_item;
    Context mContext;
    public GeneralFunctions generalFunc;

    InactiveRecycleAdapter.OnItemClickList onItemClickList;

    public InactiveRecycleAdapter(Context mContext, ArrayList<HashMap<String, String>> list_item, GeneralFunctions generalFunc) {
        this.mContext = mContext;
        this.list_item = list_item;
        this.generalFunc = generalFunc;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_inactive, parent, false);
        ViewHolder viewHolder = new ViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, final int position) {

        holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.marker), ContextCompat.getColor(mContext, R.color.appThemeColor_1));

        if (list_item.get(position).get("line").equals("start")) {
            holder.mTimelineView.initLine(LineType.BEGIN);

            holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_check_mark_button));

        } else if (list_item.get(position).get("line").equals("two")) {
            if (list_item.get(position).get("state").equals("false")) {
                holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_two));
            } else {
                holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_check_mark_button));

            }
        } else if (list_item.get(position).get("line").equals("three")) {
            if (list_item.get(position).get("state").equals("false")) {
                holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_three));
            } else {
                holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_check_mark_button));

            }
        } else if (list_item.get(position).get("line").equals("four")) {
            if (list_item.get(position).get("state").equals("false")) {
                holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_four));
            } else {
                holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_check_mark_button));

            }

        } else if (list_item.get(position).get("line").equals("end")) {
            holder.mTimelineView.initLine(LineType.END);
            if (list_item.get(position).get("state").equals("false")) {
                if (list_item.size() == 4) {
                    holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_four));
                } else {
                    holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_five));
                }
            } else {
                holder.mTimelineView.setMarker(ContextCompat.getDrawable(mContext, R.drawable.ic_check_mark_button));

            }
        }
        holder.text_inactive_title.setText(list_item.get(position).get("title"));
        if (list_item.get(position).get("msg").equals("")) {
            holder.text_inactive_msg.setVisibility(View.GONE);
        } else {
            holder.text_inactive_msg.setText(list_item.get(position).get("msg"));
            holder.text_inactive_msg.setVisibility(View.VISIBLE);
        }
        if (list_item.get(position).get("btn").equals("")) {
            holder.text_inactive_btn.setVisibility(View.GONE);
        } else {
            holder.text_inactive_btn.setVisibility(View.VISIBLE);
            holder.text_inactive_btn.setText(list_item.get(position).get("btn"));

        }

        holder.text_inactive_btn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (onItemClickList != null) {
                    onItemClickList.onItemClick(position);
                }
            }
        });


    }

    @Override
    public int getItemCount() {
        return list_item.size();
    }

    public interface OnItemClickList {
        void onItemClick(int position);
    }

    public void setOnItemClickList(InactiveRecycleAdapter.OnItemClickList onItemClickList) {
        this.onItemClickList = onItemClickList;
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        public MTextView text_inactive_title;
        public MTextView text_inactive_msg;
        public MTextView text_inactive_btn;
        public TimelineView mTimelineView;


        public ViewHolder(View view) {
            super(view);

            text_inactive_title = (MTextView) view.findViewById(R.id.text_inactive_title);
            text_inactive_msg = (MTextView) view.findViewById(R.id.text_inactive_msg);
            text_inactive_btn = (MTextView) view.findViewById(R.id.text_inactive_btn);
            mTimelineView = (TimelineView) view.findViewById(R.id.time_marker);
        }
    }

}
