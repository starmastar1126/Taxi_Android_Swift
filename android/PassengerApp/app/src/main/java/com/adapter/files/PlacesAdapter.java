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
 * Created by Admin on 26-09-2017.
 */

public class PlacesAdapter extends RecyclerView.Adapter<PlacesAdapter.ViewHolder> {

    Context mContext;

    ArrayList<HashMap<String, String>> recentList;
    PlacesAdapter.setRecentLocClickList locClickList;
    View view;

    public PlacesAdapter(Context context, ArrayList<HashMap<String, String>> list) {
        this.mContext = context;
        this.recentList = list;

    }

    @Override
    public PlacesAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {


        View view = LayoutInflater.from(mContext).inflate(R.layout.item_places_details, parent, false);


        return new PlacesAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(PlacesAdapter.ViewHolder holder, final int position) {

        holder.addressText.setText(recentList.get(position).get("main_text"));
        holder.subAddressText.setText(recentList.get(position).get("secondary_text"));

        holder.placeAdapterView.setOnClickListener(new View.OnClickListener() {
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

    public void itemRecentLocClick(PlacesAdapter.setRecentLocClickList setRecentLocClickList) {
        this.locClickList = setRecentLocClickList;
    }

    public interface setRecentLocClickList {
        void itemRecentLocClick(int position);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        MTextView addressText;
        MTextView subAddressText;
        LinearLayout placeAdapterView;

        public ViewHolder(View itemView) {
            super(itemView);

            addressText = (MTextView) itemView.findViewById(R.id.addressText);
            subAddressText = (MTextView) itemView.findViewById(R.id.subAddressText);
            placeAdapterView = (LinearLayout) itemView.findViewById(R.id.placeAdapterView);

        }
    }

}
