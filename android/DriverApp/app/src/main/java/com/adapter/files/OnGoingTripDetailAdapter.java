package com.adapter.files;

import android.content.Context;
import android.graphics.PorterDuff;
import android.graphics.PorterDuffColorFilter;
import android.graphics.drawable.Drawable;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.MTextView;
import com.view.SelectableRoundedImageView;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 22-02-2017.
 */
public class OnGoingTripDetailAdapter extends RecyclerView.Adapter<OnGoingTripDetailAdapter.ViewHolder> {

    public GeneralFunctions generalFunc;
    ArrayList<HashMap<String, String>> list_item;
    Context mContext;
    OnItemClickList onItemClickList;

    public OnGoingTripDetailAdapter(Context mContext, ArrayList<HashMap<String, String>> list_item, GeneralFunctions generalFunc) {
        this.mContext = mContext;
        this.list_item = list_item;
        this.generalFunc = generalFunc;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_design_ongoing_trip_cell, parent, false);

        ViewHolder viewHolder = new ViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(ViewHolder viewHolder, final int position) {

        HashMap<String, String> item = list_item.get(position);
        viewHolder.tripStatusTxt.setText(item.get("msg"));
        viewHolder.tripStatusTimeTxt.setText(item.get("time"));
        viewHolder.tripTimeTxt.setText(item.get("time"));
        viewHolder.tripTimeTxt.setVisibility(View.GONE);
        new CreateRoundedView(mContext.getResources().getColor(R.color.appThemeColor_1), Utils.dipToPixels(mContext, 60), 0,
                mContext.getResources().getColor(R.color.appThemeColor_1), viewHolder.driverImgView);

        Utils.printLog("status::", item.get("status"));
        if (item.get("status").equalsIgnoreCase("Accept")) {
            Drawable mDrawable = mContext.getResources().getDrawable(R.mipmap.one);
            mDrawable.setColorFilter(new PorterDuffColorFilter(mContext.getResources().getColor(R.color.white), PorterDuff.Mode.MULTIPLY));
            viewHolder.driverImgView.setImageDrawable(mDrawable);
        } else if (item.get("status").equalsIgnoreCase("Arrived")) {
            Drawable mDrawable = mContext.getResources().getDrawable(R.mipmap.two);
            mDrawable.setColorFilter(new PorterDuffColorFilter(mContext.getResources().getColor(R.color.white), PorterDuff.Mode.MULTIPLY));
            viewHolder.driverImgView.setImageDrawable(mDrawable);
        } else if (item.get("status").equalsIgnoreCase("Onway")) {
            Drawable mDrawable = mContext.getResources().getDrawable(R.mipmap.three);
            mDrawable.setColorFilter(new PorterDuffColorFilter(mContext.getResources().getColor(R.color.white), PorterDuff.Mode.MULTIPLY));
            viewHolder.driverImgView.setImageDrawable(mDrawable);
            //  viewHolder.driverImgView.setImageResource(R.mipmap.two);
        } else if (item.get("status").equalsIgnoreCase("Delivered")) {
            Drawable mDrawable = mContext.getResources().getDrawable(R.mipmap.four);
            mDrawable.setColorFilter(new PorterDuffColorFilter(mContext.getResources().getColor(R.color.white), PorterDuff.Mode.MULTIPLY));
            viewHolder.driverImgView.setImageDrawable(mDrawable);
            //   viewHolder.driverImgView.setImageResource(R.mipmap.three);
        } else if (item.get("status").equalsIgnoreCase("Cancelled")) {
            Drawable mDrawable = mContext.getResources().getDrawable(R.mipmap.five);
            mDrawable.setColorFilter(new PorterDuffColorFilter(mContext.getResources().getColor(R.color.white), PorterDuff.Mode.MULTIPLY));
            viewHolder.driverImgView.setImageDrawable(mDrawable);
            //viewHolder.driverImgView.setImageResource(R.mipmap.four);
        } else if (item.get("status").equalsIgnoreCase("On the way")) {
            Drawable mDrawable = mContext.getResources().getDrawable(R.mipmap.five);
            mDrawable.setColorFilter(new PorterDuffColorFilter(mContext.getResources().getColor(R.color.white), PorterDuff.Mode.MULTIPLY));
            viewHolder.driverImgView.setImageDrawable(mDrawable);
            //  viewHolder.driverImgView.setImageResource(R.mipmap.five);
        }
    }

    @Override
    public int getItemCount() {
        return list_item.size();
    }

    public void setOnItemClickList(OnItemClickList onItemClickList) {
        this.onItemClickList = onItemClickList;
    }

    public interface OnItemClickList {
        void onItemClick(int position);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        public MTextView tripStatusTxt;
        public MTextView tripStatusTimeTxt;
        public MTextView tripTimeTxt;
        public SelectableRoundedImageView driverImgView;

        public ViewHolder(View view) {
            super(view);

            tripStatusTxt = (MTextView) view.findViewById(R.id.tripStatusTxt);
            tripStatusTimeTxt = (MTextView) view.findViewById(R.id.tripStatusTimeTxt);
            tripTimeTxt = (MTextView) view.findViewById(R.id.tripTimeTxt);
            driverImgView = (SelectableRoundedImageView) view.findViewById(R.id.driverImgView);
        }
    }

}
