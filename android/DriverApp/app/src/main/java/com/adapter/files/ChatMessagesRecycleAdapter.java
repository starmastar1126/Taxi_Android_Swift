package com.adapter.files;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.squareup.picasso.Picasso;
import com.utils.CommonUtilities;
import com.view.SelectableRoundedImageView;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 09-07-2016.
 */
public class ChatMessagesRecycleAdapter extends RecyclerView.Adapter<ChatMessagesRecycleAdapter.ViewHolder> {

    ArrayList<HashMap<String, Object>> list_item;
    Context mContext;
    public GeneralFunctions generalFunc;
    private HashMap<String, String> data_trip;

    public OnItemClickList onItemClickList;

    public ChatMessagesRecycleAdapter(Context mContext, ArrayList<HashMap<String, Object>> list_item, GeneralFunctions generalFunc, HashMap<String, String> data_trip) {
        this.mContext = mContext;
        this.list_item = list_item;
        this.data_trip = data_trip;
        this.generalFunc = generalFunc;

    }

    @Override
    public ChatMessagesRecycleAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.message, parent, false);

        ViewHolder viewHolder = new ViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(ViewHolder viewHolder, final int position) {

        HashMap<String, Object> item = list_item.get(position);


        if(item.get("eUserType").equals(CommonUtilities.app_type)){
            viewHolder.activity_main.setVisibility(View.VISIBLE);
            viewHolder.activity_main.setBackground(mContext.getResources().getDrawable(R.drawable.inputchat));
            viewHolder.messageUser.setText("You");
            viewHolder.rightuserImageview.setVisibility(View.VISIBLE);
            viewHolder.lefttuserImageview.setVisibility(View.GONE);
            RelativeLayout.LayoutParams params =
                    new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.WRAP_CONTENT,
                            RelativeLayout.LayoutParams.WRAP_CONTENT);
            params.addRule(RelativeLayout.ALIGN_PARENT_END, RelativeLayout.TRUE);
            viewHolder.activity_main.setLayoutParams(params);
            viewHolder.mainlayout.setVisibility(View.VISIBLE);
            viewHolder.rightshape.setVisibility(View.VISIBLE);
            viewHolder.leftshap.setVisibility(View.GONE);



            String image_url = CommonUtilities.SERVER_URL_PHOTOS + "upload/Driver/" + item.get("driverId") + "/" +  item.get("driverImageName");


            Picasso.with(mContext)
                    .load(image_url)
                    .placeholder(R.mipmap.ic_no_pic_user)
                    .error(R.mipmap.ic_no_pic_user)
                    .into(viewHolder.rightuserImageview);
        }else{
            viewHolder.activity_main.setVisibility(View.VISIBLE);
            viewHolder.activity_main.setBackground(mContext.getResources().getDrawable(R.drawable.outputchat));

            viewHolder.messageUser.setText(data_trip.get("FromMemberName"));
            viewHolder.lefttuserImageview.setVisibility(View.VISIBLE);
            viewHolder.rightuserImageview.setVisibility(View.GONE);

            RelativeLayout.LayoutParams params =
                    new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.WRAP_CONTENT,
                            RelativeLayout.LayoutParams.WRAP_CONTENT);
            params.addRule(RelativeLayout.ALIGN_PARENT_START, RelativeLayout.TRUE);
            viewHolder.activity_main.setLayoutParams(params);
            viewHolder.rightshape.setVisibility(View.GONE);
            viewHolder.leftshap.setVisibility(View.VISIBLE);

            String image_url = CommonUtilities.SERVER_URL_PHOTOS + "upload/Passenger/" + item.get("passengerId") + "/"
                    + item.get("passengerImageName");

            Picasso.with(mContext)
                    .load(image_url)
                    .placeholder(R.mipmap.ic_no_pic_user)
                    .error(R.mipmap.ic_no_pic_user)
                    .into(viewHolder.lefttuserImageview);
        }

        if(item.get("Text").toString().length()==1)
        {
            viewHolder.messageText.setText(" " +item.get("Text").toString()+" ");
        }
        else {
            viewHolder.messageText.setText(item.get("Text").toString());
        }

        viewHolder.messageUser.setText(item.get("eUserType").toString());

    }


    public class ViewHolder extends RecyclerView.ViewHolder {

        public TextView messageText, messageUser, messageTime;
        public LinearLayout activity_main;
        public ImageView rightshape, leftshap;
        public LinearLayout mainlayout;
        public SelectableRoundedImageView lefttuserImageview, rightuserImageview;

        public ViewHolder(View view) {
            super(view);

            rightshape = (ImageView) view.findViewById(R.id.rightshape);
            leftshap = (ImageView) view.findViewById(R.id.leftshap);
            messageText = (TextView) view.findViewById(R.id.message_text);
            messageUser = (TextView) view.findViewById(R.id.message_user);
            messageTime = (TextView) view.findViewById(R.id.message_time);
            mainlayout = (LinearLayout) view.findViewById(R.id.mainlayout);
            lefttuserImageview = (SelectableRoundedImageView) view.findViewById(R.id.lefttuserImageview);
            rightuserImageview = (SelectableRoundedImageView) view.findViewById(R.id.rightuserImageview);

            activity_main = (LinearLayout) view.findViewById(R.id.activity_main);
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
