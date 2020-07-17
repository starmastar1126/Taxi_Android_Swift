package com.adapter.files;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioButton;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import java.util.ArrayList;
import java.util.HashMap;

import okhttp3.internal.Util;

public class ListOfDocAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    private static final int TYPE_ITEM = 1;
    private static final int TYPE_FOOTER = 2;
    public GeneralFunctions generalFunc;
    ArrayList<HashMap<String, String>> list;
    Context mContext;
    boolean isFooterEnabled = false;
    View footerView;
    FooterViewHolder footerHolder;
    private OnItemClickListener mItemClickListener;
    private int currSelectedPosition = -1;

    public ListOfDocAdapter(Context mContext, ArrayList<HashMap<String, String>> list, GeneralFunctions generalFunc, boolean isFooterEnabled) {
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
            View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.list_of_doc_item_design, parent, false);
            return new ViewHolder(view);
        }
    }

    // Replace the contents of a view (invoked by the layout manager)
    @Override
    public void onBindViewHolder(final RecyclerView.ViewHolder holder, final int position) {

        if (holder instanceof ViewHolder) {

            final HashMap<String, String> item = list.get(position);
            final ViewHolder viewHolder = (ViewHolder) holder;

            viewHolder.titleTxt.setText(item.get("doc_name"));

            //CHANGES FOR DOCUMENT MISSING
            viewHolder.missingTxt.setText(item.get("LBL_MISSING_TXT"));

            if (item.get("doc_file").equals("")) {
                viewHolder.infoImg.setVisibility(View.VISIBLE);
                viewHolder.missingTxt.setVisibility(View.VISIBLE);

            } else {
                viewHolder.infoImg.setVisibility(View.GONE);
                viewHolder.missingTxt.setVisibility(View.GONE);
                if (item.get("EXPIRE_DOCUMENT").equalsIgnoreCase("Yes")) {
                    viewHolder.missingTxt.setVisibility(View.VISIBLE);
                    viewHolder.infoImg.setVisibility(View.VISIBLE);
                    viewHolder.missingTxt.setText(item.get("LBL_EXPIRED_TXT"));
                }
            }
            //CHANGES OVER FOR DOCUMENT MISSING

            if (item.get("vimage").equals("")) {
                viewHolder.docImgView.setImageResource(R.mipmap.doc_off);
                viewHolder.docImgView.setOnClickListener(null);
                viewHolder.btn_type2.setText(item.get("LBL_UPLOAD_DOC"));
                viewHolder.docImgView.setVisibility(View.GONE);
            } else {
                viewHolder.docImgView.setVisibility(View.VISIBLE);
                viewHolder.btn_type2.setText(item.get("LBL_MANAGE"));

                viewHolder.docImgView.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        new StartActProcess(mContext).openURL(item.get("vimage"));
                    }
                });
                viewHolder.docImgView.setImageResource(R.mipmap.doc_on);
            }

            if (currSelectedPosition == -1 || currSelectedPosition != position) {
                viewHolder.indicatorImg.setImageResource(R.mipmap.ic_arrow_down);
                viewHolder.detailArea.setVisibility(View.GONE);
                viewHolder.seperatorView.setVisibility(View.GONE);
            }

            viewHolder.datarea.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
//                    if (mItemClickListener != null) {
//                        mItemClickListener.onItemClickList(view, position);
//                    }

                    if (viewHolder.detailArea.getVisibility() == View.GONE) {

                        currSelectedPosition = position;
                        viewHolder.indicatorImg.setImageResource(R.mipmap.ic_arrow_up);
                        viewHolder.detailArea.setVisibility(View.VISIBLE);
                        viewHolder.seperatorView.setVisibility(View.VISIBLE);

                    } else {

                        currSelectedPosition = -1;
                        viewHolder.indicatorImg.setImageResource(R.mipmap.ic_arrow_down);
                        viewHolder.detailArea.setVisibility(View.GONE);
                        viewHolder.seperatorView.setVisibility(View.GONE);
                    }


                    notifyDataSetChanged();
                }
            });

            viewHolder.btn_type2.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    if (mItemClickListener != null) {
                        mItemClickListener.onItemClickList(position);
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
        void onItemClickList(int position);
    }

    // inner class to hold a reference to each item of RecyclerView
    public class ViewHolder extends RecyclerView.ViewHolder {

        public MTextView titleTxt;
        public ImageView indicatorImg;
        public ImageView docImgView;
        public LinearLayout detailArea;
        public View datarea;
        public View seperatorView;
        public MButton btn_type2;
        public MTextView missingTxt;
        public ImageView infoImg;
        LinearLayout linearView;

        public ViewHolder(View view) {
            super(view);

            titleTxt = (MTextView) view.findViewById(R.id.titleTxt);
            linearView = (LinearLayout) view.findViewById(R.id.linearView);
            indicatorImg = (ImageView) view.findViewById(R.id.indicatorImg);
            docImgView = (ImageView) view.findViewById(R.id.docImgView);
            detailArea = (LinearLayout) view.findViewById(R.id.detailArea);
            datarea = view.findViewById(R.id.datarea);
            seperatorView = view.findViewById(R.id.seperatorView);
            missingTxt = (MTextView) view.findViewById(R.id.missingTxt);
            infoImg = (ImageView) view.findViewById(R.id.infoImg);
            btn_type2 = ((MaterialRippleLayout) view.findViewById(R.id.btn_type2)).getChildView();
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
