package com.adapter.files;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.squareup.picasso.Picasso;
import com.utils.Utils;
import com.view.MTextView;
import com.view.SelectableRoundedImageView;
import com.view.simpleratingbar.SimpleRatingBar;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 10-08-2016.
 */
public class DriverFeedbackRecycleAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    private static final int TYPE_ITEM = 1;
    private static final int TYPE_FOOTER = 2;
    public GeneralFunctions generalFunc;
    ArrayList<HashMap<String, String>> list;
    Context mContext;
    boolean isFooterEnabled = false;
    View footerView;
    FooterViewHolder footerHolder;
    private OnItemClickListener mItemClickListener;

    public DriverFeedbackRecycleAdapter(Context mContext, ArrayList<HashMap<String, String>> list, GeneralFunctions generalFunc, boolean isFooterEnabled) {
        this.mContext = mContext;
        this.list = list;
        this.generalFunc = generalFunc;
        this.isFooterEnabled = isFooterEnabled;
    }

    public void setOnItemClickListener(OnItemClickListener mItemClickListener) {
        this.mItemClickListener = mItemClickListener;
    }

    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        if (viewType == TYPE_FOOTER) {
            View v = LayoutInflater.from(parent.getContext()).inflate(R.layout.footer_list, parent, false);
            this.footerView = v;
            return new FooterViewHolder(v);
        } else {
            View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_feedback_design, parent, false);
            return new ViewHolder(view);
        }

    }

    // Replace the contents of a view (invoked by the layout manager)
    @Override
    public void onBindViewHolder(final RecyclerView.ViewHolder holder, final int position) {


        if (holder instanceof ViewHolder) {
            final HashMap<String, String> item = list.get(position);
            final ViewHolder viewHolder = (ViewHolder) holder;




            if (item.get("vMessage").trim().equals("")) {
                viewHolder.commentTxt.setVisibility(View.GONE);

            } else {
                viewHolder.commentTxt.setVisibility(View.VISIBLE);

              //  viewHolder.commentTxt.setText("\"" + item.get("vMessage") + "\"");
                viewHolder.commentTxt.setText(item.get("vMessage"));

                if (viewHolder.commentTxt.getLineCount() > 3) {

                }

            }


            String driverImageName =item.get("vImage");
            if (driverImageName == null || driverImageName.equals("") || driverImageName.equals("NONE")) {
                viewHolder.userProfileImgView.setImageResource(R.mipmap.ic_no_pic_user);
            } else {
                String image_url =driverImageName;
                Picasso.with(mContext)
                        .load(image_url)
                        .placeholder(R.mipmap.ic_no_pic_user)
                        .error(R.mipmap.ic_no_pic_user)
                        .into(viewHolder.userProfileImgView);
            }

            viewHolder.ratingBar.setRating(generalFunc.parseFloatValue(0, item.get("vRating1")));
            viewHolder.dateTxt.setText(generalFunc.getDateFormatedType(item.get("tDateOrig"), Utils.OriginalDateFormate,Utils.dateFormateInList));
            viewHolder.nameTxt.setText(item.get("vName"));
            viewHolder.contentArea.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    if (mItemClickListener != null) {
                        mItemClickListener.onItemClickList(view, position);
                    }
                }
            });
        } else {
            FooterViewHolder footerHolder = (FooterViewHolder) holder;
            this.footerHolder = footerHolder;
        }


    }

    @Override
    public int getItemViewType(int position) {
        if (isPositionFooter(position) && isFooterEnabled == true) {
            return TYPE_FOOTER;
        }
        return TYPE_ITEM;
    }

    private boolean isPositionFooter(int position) {
        return position == list.size();
    }

    // Return the size of your itemsData (invoked by the layout manager)
    @Override
    public int getItemCount() {
        if (isFooterEnabled == true) {
            return list.size() + 1;
        } else {
            return list.size();
        }

    }

    public void addFooterView() {

        this.isFooterEnabled = true;
        notifyDataSetChanged();
        if (footerHolder != null) {
            footerHolder.progressContainer.setVisibility(View.VISIBLE);
        }
    }

    public void removeFooterView() {
        if (footerHolder != null)
            footerHolder.progressContainer.setVisibility(View.GONE);
    }


    public interface OnItemClickListener {
        void onItemClickList(View v, int position);
    }

    // inner class to hold a reference to each item of RecyclerView
    public class ViewHolder extends RecyclerView.ViewHolder {

        public MTextView commentTxt;
        public MTextView dateTxt;
        public SimpleRatingBar ratingBar;
        public LinearLayout contentArea;
        public  MTextView nameTxt;
        SelectableRoundedImageView userProfileImgView;

        public ViewHolder(View view) {
            super(view);

            commentTxt = (MTextView) view.findViewById(R.id.commentTxt);
            dateTxt = (MTextView) view.findViewById(R.id.dateTxt);
            ratingBar = (SimpleRatingBar) view.findViewById(R.id.ratingBar);
            contentArea = (LinearLayout) view.findViewById(R.id.contentArea);
            nameTxt=(MTextView)view.findViewById(R.id.nameTxt);
            userProfileImgView = (SelectableRoundedImageView)view.findViewById(R.id.userProfileImgView);

        }
    }

    class FooterViewHolder extends RecyclerView.ViewHolder {
        LinearLayout progressContainer;

        public FooterViewHolder(View itemView) {
            super(itemView);

            progressContainer = (LinearLayout) itemView.findViewById(R.id.progressContainer);

        }
    }
}
