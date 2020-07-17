package com.adapter.files;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.utils.Utils;
import com.view.MTextView;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 09-07-2016.
 */
public class WalletHistoryRecycleAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    private static final int TYPE_ITEM = 1;
    private static final int TYPE_FOOTER = 2;
    public GeneralFunctions generalFunc;
    ArrayList<HashMap<String, String>> list;
    Context mContext;
    boolean isFooterEnabled = false;
    View footerView;
    FooterViewHolder footerHolder;
    private OnItemClickListener mItemClickListener;

    public WalletHistoryRecycleAdapter(Context mContext, ArrayList<HashMap<String, String>> list, GeneralFunctions generalFunc, boolean isFooterEnabled) {
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
            View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_wallethistory_design, parent, false);

            return new ViewHolder(view);
        }

    }

    // Replace the contents of a view (invoked by the layout manager)
    @Override
    public void onBindViewHolder(final RecyclerView.ViewHolder holder, final int position) {


        if (holder instanceof ViewHolder) {
            final HashMap<String, String> item = list.get(position);
            final ViewHolder viewHolder = (ViewHolder) holder;

            viewHolder.transactiondateTxt.setText(generalFunc.getDateFormatedType(item.get("dDateOrig"), Utils.OriginalDateFormate, Utils.dateFormateInList));


            viewHolder.transactionDesVTxt.setText(item.get("tDescription"));
            viewHolder.tranasctionBalVTxt.setText(generalFunc.convertNumberWithRTL(item.get("iBalance")));

            if (item.get("eType").equalsIgnoreCase("Credit")) {
                viewHolder.arrowImg.setImageResource(R.mipmap.ic_credit);
            } else {
                viewHolder.arrowImg.setImageResource(R.mipmap.ic_debit);
            }

            viewHolder.detailExpandArea.setId(position);
            viewHolder.contentArea.setId(position);
            viewHolder.transactionDetailArea.setId(position);


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
        if (footerHolder != null)
            footerHolder.progressArea.setVisibility(View.VISIBLE);
    }

    public void removeFooterView() {
        if (footerHolder != null)
            footerHolder.progressArea.setVisibility(View.GONE);
    }


    public interface OnItemClickListener {
        void onItemClickList(View v, int position);
    }

    // inner class to hold a reference to each item of RecyclerView
    public class ViewHolder extends RecyclerView.ViewHolder {


        private ImageView arrowImg;
        private MTextView tranasctionBalVTxt;
        private MTextView transactiondateTxt;
        private MTextView transactionDesVTxt;
        private LinearLayout transactionDetailArea;
        private LinearLayout detailExpandArea;
        private LinearLayout contentArea;

        public ViewHolder(View view) {
            super(view);

            arrowImg = (ImageView) view.findViewById(R.id.arrowImg);
            tranasctionBalVTxt = (MTextView) view.findViewById(R.id.tranasctionBalVTxt);
            transactiondateTxt = (MTextView) view.findViewById(R.id.transactiondateTxt);
            transactionDesVTxt = (MTextView) view.findViewById(R.id.transactionDesVTxt);
            transactionDetailArea = (LinearLayout) view.findViewById(R.id.transactionDetailArea);
            detailExpandArea = (LinearLayout) view.findViewById(R.id.detailExpandArea);
            contentArea = (LinearLayout) view.findViewById(R.id.contentArea);

        }
    }

    class FooterViewHolder extends RecyclerView.ViewHolder {
        LinearLayout progressArea;

        public FooterViewHolder(View itemView) {
            super(itemView);

            progressArea = (LinearLayout) itemView;

        }
    }
}
