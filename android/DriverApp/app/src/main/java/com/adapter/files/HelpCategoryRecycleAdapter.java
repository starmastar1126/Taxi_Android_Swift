package com.adapter.files;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.view.MTextView;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 09-07-2016.
 */
public class HelpCategoryRecycleAdapter extends RecyclerView.Adapter<HelpCategoryRecycleAdapter.ViewHolder> {

    ArrayList<HashMap<String, String>> list_item;
    Context mContext;
    public GeneralFunctions generalFunc;

    OnItemClickList onItemClickList;

    public HelpCategoryRecycleAdapter(Context mContext, ArrayList<HashMap<String, String>> list_item, GeneralFunctions generalFunc) {
        this.mContext = mContext;
        this.list_item = list_item;
        this.generalFunc = generalFunc;
    }

    @Override
    public HelpCategoryRecycleAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_design_category_help, parent, false);

        ViewHolder viewHolder = new ViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(ViewHolder viewHolder, final int position) {

        HashMap<String, String> item = list_item.get(position);

        viewHolder.titleTxt.setText(item.get("vTitle"));

        viewHolder.titleTxt.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if(onItemClickList != null){
                    onItemClickList.onItemClick(position);
                }
            }
        });

        if (position == (list_item.size() - 1)) {
            viewHolder.seperationLine.setVisibility(View.GONE);
        } else {
            viewHolder.seperationLine.setVisibility(View.GONE);
        }
        if (!generalFunc.retrieveValue(CommonUtilities.LANGUAGE_IS_RTL_KEY).equals("") && generalFunc.retrieveValue(CommonUtilities.LANGUAGE_IS_RTL_KEY).equals(CommonUtilities.DATABASE_RTL_STR)) {

            viewHolder.imagearrow.setRotation(-270);
        }
    }


    public class ViewHolder extends RecyclerView.ViewHolder {

        public MTextView titleTxt;
        public View seperationLine;
        ImageView imagearrow;

        public ViewHolder(View view) {
            super(view);

            titleTxt = (MTextView) view.findViewById(R.id.titleTxt);
            seperationLine = view.findViewById(R.id.seperationLine);
            imagearrow=(ImageView) view.findViewById(R.id.imagearrow);
        }
    }

    @Override
    public int getItemCount() {
        return list_item.size();
    }

    public interface OnItemClickList {
        void onItemClick(int position);
    }

    public void setOnItemClickList(OnItemClickList onItemClickList) {
        this.onItemClickList = onItemClickList;
    }

}
