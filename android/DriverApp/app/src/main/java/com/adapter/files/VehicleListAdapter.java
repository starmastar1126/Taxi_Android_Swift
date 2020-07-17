package com.adapter.files;

import android.content.Context;
import android.graphics.Color;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.MTextView;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 09-06-2017.
 */
public class VehicleListAdapter extends RecyclerView.Adapter<VehicleListAdapter.ViewHolder>  {

    public GeneralFunctions generalFunc;
    ArrayList<HashMap<String, String>> list_item;
    Context mContext;
    OnItemClickList onItemClickList;

    public VehicleListAdapter(Context mContext, ArrayList<HashMap<String, String>> list_item, GeneralFunctions generalFunc) {
        this.mContext = mContext;
        this.list_item = list_item;
        this.generalFunc = generalFunc;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_list_manage_vehicle_design, parent, false);

        ViewHolder viewHolder = new ViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(ViewHolder viewHolder, final int position) {

        HashMap<String, String> item = list_item.get(position);

        if(item.get("eStatus") == "Active"){
            viewHolder.statusTxtView.setText(generalFunc.retrieveLangLBl("","LBL_ACTIVE"));
        }else if(item.get("eStatus") == "Inactive"){
            viewHolder.statusTxtView.setText(generalFunc.retrieveLangLBl("","LBL_INACTIVE"));
        }else if(item.get("eStatus") == "Deleted"){
            viewHolder.statusTxtView.setText(generalFunc.retrieveLangLBl("","LBL_DELETED"));
        }else{
            viewHolder.statusTxtView.setText(item.get("eStatus"));
        }

        viewHolder.vNameTxtView.setText(item.get("vMake"));
        viewHolder.vOthInfoTxtView.setText(item.get("vLicencePlate"));

        new CreateRoundedView(Color.parseColor("#E8E8E8"), Utils.dipToPixels(mContext, 5), 0,
                mContext.getResources().getColor(R.color.appThemeColor_2), viewHolder.docImgView);
        new CreateRoundedView(Color.parseColor("#E8E8E8"), Utils.dipToPixels(mContext, 5), 0,
                mContext.getResources().getColor(R.color.appThemeColor_2), viewHolder.editImgView);
        new CreateRoundedView(Color.parseColor("#E8E8E8"), Utils.dipToPixels(mContext, 5), 0,
                mContext.getResources().getColor(R.color.appThemeColor_2), viewHolder.deleteImgView);

        viewHolder.docImgView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                if(onItemClickList != null){
                    onItemClickList.onItemClick(position,0);
                }
            }
        });
        viewHolder.editImgView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                if(onItemClickList != null){
                    onItemClickList.onItemClick(position,1);
                }
            }
        });
        viewHolder.deleteImgView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                if(onItemClickList != null){
                    onItemClickList.onItemClick(position,2);
                }
            }
        });
    }

    @Override
    public int getItemCount() {
        return list_item.size();
    }

    public void setOnItemClickList(OnItemClickList onItemClickList) {
        this.onItemClickList = onItemClickList;
    }

    public interface OnItemClickList {
        void onItemClick(int position, int viewClickId);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        public MTextView statusTxtView;
        public MTextView vNameTxtView;
        public MTextView vOthInfoTxtView;
        public ImageView docImgView;
        public ImageView editImgView;
        public ImageView deleteImgView;

        public ViewHolder(View view) {
            super(view);

            statusTxtView = (MTextView) view.findViewById(R.id.statusTxtView);
            vNameTxtView = (MTextView) view.findViewById(R.id.vNameTxtView);
            vOthInfoTxtView = (MTextView) view.findViewById(R.id.vOthInfoTxtView);
            docImgView = (ImageView) view.findViewById(R.id.docImgView);
            editImgView = (ImageView) view.findViewById(R.id.editImgView);
            deleteImgView = (ImageView) view.findViewById(R.id.deleteImgView);
        }
    }

}
