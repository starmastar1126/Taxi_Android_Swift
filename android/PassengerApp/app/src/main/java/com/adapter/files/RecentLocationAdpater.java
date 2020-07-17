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

public class RecentLocationAdpater extends RecyclerView.Adapter<RecentLocationAdpater.ViewHolder> {

    Context mContext;

    ArrayList<HashMap<String, String>> recentList;
    setRecentLocClickList locClickList;
    View view;

    RecentLocationAdpater(Context context, ArrayList<HashMap<String, String>> list) {
        this.mContext = context;
        this.recentList = list;
    }

    @Override
    public RecentLocationAdpater.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        if (view == null) {
            view = LayoutInflater.from(mContext).inflate(R.layout.item_recent_loc_design, parent, false);
        }

        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(RecentLocationAdpater.ViewHolder holder, final int position) {

        holder.recentAddrTxtView.setText(recentList.get(position).get("tDaddress"));

        holder.recentAdapterView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (locClickList != null) {
                    locClickList.itemRecentLocClick(position);
                }
            }
        });
    }

    @Override
    public int getItemCount() {
        return recentList.size();
    }

    public void itemRecentLocClick(setRecentLocClickList setRecentLocClickList) {
        this.locClickList = setRecentLocClickList;
    }

    public interface setRecentLocClickList {
        void itemRecentLocClick(int position);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        MTextView recentAddrTxtView;
        LinearLayout recentAdapterView;

        public ViewHolder(View itemView) {
            super(itemView);

            recentAddrTxtView = (MTextView) itemView.findViewById(R.id.recentAddrTxtView);
            recentAdapterView = (LinearLayout) itemView.findViewById(R.id.recentAdapterView);

        }
    }

}
