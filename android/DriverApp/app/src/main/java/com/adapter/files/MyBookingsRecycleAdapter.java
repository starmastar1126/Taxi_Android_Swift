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
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 09-07-2016.
 */
public class MyBookingsRecycleAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {

    private static final int TYPE_ITEM = 1;
    private static final int TYPE_FOOTER = 2;
    public GeneralFunctions generalFunc;
    ArrayList<HashMap<String, String>> list;
    Context mContext;
    boolean isFooterEnabled = false;
    View footerView;
    FooterViewHolder footerHolder;
    private OnItemClickListener mItemClickListener;
    String type;


    public MyBookingsRecycleAdapter(Context mContext, ArrayList<HashMap<String, String>> list, String type, GeneralFunctions generalFunc, boolean isFooterEnabled) {
        this.mContext = mContext;
        this.list = list;
        this.generalFunc = generalFunc;
        this.type = type;
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
            View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_my_bookings_design, parent, false);
            return new ViewHolder(view);
        }

    }

    // Replace the contents of a view (invoked by the layout manager)
    @Override
    public void onBindViewHolder(final RecyclerView.ViewHolder holder, final int position) {


        if (holder instanceof ViewHolder) {
            final HashMap<String, String> item = list.get(position);
            final ViewHolder viewHolder = (ViewHolder) holder;

            viewHolder.myBookingNoHTxt.setText(item.get("LBL_BOOKING_NO") + "#");
            viewHolder.myBookingNoVTxt.setText(generalFunc.convertNumberWithRTL(item.get("vBookingNo")));
            if (item.get("selectdtime") != null && !item.get("selectdtime").equalsIgnoreCase("")) {
                viewHolder.dateTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.dateFormateInList) + " " + item.get("selectdtime"));

                viewHolder.etypeTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.DateFormatewithTime));
            } else {
                viewHolder.dateTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.DateFormatewithTime));

            }
            viewHolder.statusHTxt.setText(item.get("LBL_Status") + ":");
            viewHolder.sourceAddressTxt.setText(item.get("vSourceAddresss"));
            viewHolder.destAddressHTxt.setText(item.get("LBL_DEST_LOCATION"));
            viewHolder.sourceAddressHTxt.setText(item.get("LBL_PICK_UP_LOCATION"));
            if (item.get("tDestAddress").equals("")) {
                viewHolder.destAddressTxt.setVisibility(View.GONE);
                viewHolder.destarea.setVisibility(View.GONE);
                viewHolder.dashImage.setVisibility(View.GONE);
                viewHolder.imagedest.setVisibility(View.GONE);
                viewHolder.slecttypearea.setVisibility(View.VISIBLE);
                if (item.get("SelectedCategory") != null && !item.get("SelectedCategory").equalsIgnoreCase("")) {
                    viewHolder.SelectedTypeNameTxt.setText(item.get("SelectedCategory") + "-" + (item.get("SelectedVehicle")));
                } else {
                    viewHolder.slecttypearea.setVisibility(View.GONE);

                }
            } else {
                viewHolder.slecttypearea.setVisibility(View.GONE);
                viewHolder.destarea.setVisibility(View.VISIBLE);
                viewHolder.destAddressTxt.setVisibility(View.VISIBLE);
                viewHolder.dashImage.setVisibility(View.VISIBLE);
                viewHolder.imagedest.setVisibility(View.VISIBLE);
                viewHolder.destAddressTxt.setText(item.get("tDestAddress"));
            }
            viewHolder.statusVTxt.setText(item.get("eStatus"));

            if (type.equalsIgnoreCase("pending")) {
                viewHolder.startTrip.setText(item.get("LBL_ACCEPT_JOB"));
                viewHolder.cancelBooking.setText(item.get("LBL_DECLINE_JOB"));

            } else {
                viewHolder.startTrip.setText(item.get("LBL_START_TRIP"));
                viewHolder.cancelBooking.setText(item.get("LBL_CANCEL_TRIP"));
            }


            if (item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_Deliver) || item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_Deliver) || item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_Ride) || item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                viewHolder.dateTxt.setVisibility(View.INVISIBLE);

                if (item.get("selectdtime") != null && !item.get("selectdtime").equalsIgnoreCase("")) {
                    viewHolder.etypeTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.dateFormateInList) + " " + item.get("selectdtime"));


                    viewHolder.etypeTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.DateFormatewithTime));

                } else {
                    viewHolder.etypeTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.DateFormatewithTime));
                }


            } else {
                viewHolder.etypeTxt.setText(item.get("eType"));
                viewHolder.dateTxt.setVisibility(View.VISIBLE);

            }

//            if (item.get("eType").equalsIgnoreCase(Utils.CabGeneralType_Deliver) || item.get("eType").equalsIgnoreCase(Utils.CabGeneralType_Ride)) {
//                viewHolder.dateTxt.setVisibility(View.INVISIBLE);
//                viewHolder.etypeTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.dateFormateInList));
//            } else if (item.get("eType").equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
//
//                viewHolder.dateTxt.setVisibility(View.INVISIBLE);
//                viewHolder.etypeTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.DateFormateInDetailScreen));
//
//                if (!item.get("eFareType").equalsIgnoreCase(Utils.CabFaretypeRegular)) {
//                    viewHolder.sourceAddressHTxt.setText(item.get("LBL_JOB_LOCATION_TXT"));
//                    viewHolder.destarea.setVisibility(View.GONE);
//                    viewHolder.imagedest.setVisibility(View.GONE);
//                    viewHolder.destAddressTxt.setVisibility(View.GONE);
//                    viewHolder.destAddressHTxt.setVisibility(View.GONE);
//
//                }
//
//
//            } else {
//                viewHolder.etypeTxt.setText(item.get("eType"));
//                viewHolder.dateTxt.setVisibility(View.VISIBLE);
//
//            }

//            if(APP_TYPE == Utils.CabGeneralType_Deliver || APP_TYPE == Utils.CabGeneralType_Ride || APP_TYPE == Utils.CabGeneralType_UberX){
////                cell.rideTypeLbl.text = cell.rideDateLbl.text
////                cell.rideDateLbl.isHidden = true
//                viewHolder.dateTxt.setVisibility(View.VISIBLE);
//
//            }


            viewHolder.startTrip.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    if (mItemClickListener != null) {
                        mItemClickListener.onTripStartClickList(view, position);
                    }
                }
            });
            viewHolder.cancelBooking.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    if (mItemClickListener != null) {
                        mItemClickListener.onCancelBookingClickList(view, position);
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
        if (footerHolder != null)
            footerHolder.progressArea.setVisibility(View.VISIBLE);
    }

    public void removeFooterView() {
        if (footerHolder != null)
            footerHolder.progressArea.setVisibility(View.GONE);
    }


    public interface OnItemClickListener {

        void onCancelBookingClickList(View v, int position);

        void onTripStartClickList(View v, int position);
    }

    // inner class to hold a reference to each item of RecyclerView
    public class ViewHolder extends RecyclerView.ViewHolder {

        public MTextView myBookingNoHTxt;
        public MTextView myBookingNoVTxt;
        public MTextView dateTxt;
        public MTextView sourceAddressTxt;
        public MTextView destAddressTxt;
        public ImageView imagedest;
        public LinearLayout destarea;
        public ImageView dashImage;
        public MTextView statusHTxt;
        public MTextView statusVTxt;
        public MButton startTrip;
        public MButton cancelBooking;
        public MTextView etypeTxt;
        public MTextView sourceAddressHTxt;
        public MTextView destAddressHTxt;
        public LinearLayout slecttypearea;
        public MTextView SelectedTypeNameTxt;


        public ViewHolder(View view) {
            super(view);

            myBookingNoHTxt = (MTextView) view.findViewById(R.id.myBookingNoHTxt);
            myBookingNoVTxt = (MTextView) view.findViewById(R.id.myBookingNoVTxt);
            dateTxt = (MTextView) view.findViewById(R.id.dateTxt);
            sourceAddressTxt = (MTextView) view.findViewById(R.id.sourceAddressTxt);
            destAddressTxt = (MTextView) view.findViewById(R.id.destAddressTxt);
            dashImage = (ImageView) view.findViewById(R.id.dashImage);
            destarea = (LinearLayout) view.findViewById(R.id.destarea);
            imagedest = (ImageView) view.findViewById(R.id.imagedest);
            statusHTxt = (MTextView) view.findViewById(R.id.statusHTxt);
            statusVTxt = (MTextView) view.findViewById(R.id.statusVTxt);
            startTrip = ((MaterialRippleLayout) view.findViewById(R.id.btn_type2_start)).getChildView();
            cancelBooking = ((MaterialRippleLayout) view.findViewById(R.id.btn_type2_cancel)).getChildView();
            etypeTxt = (MTextView) view.findViewById(R.id.etypeTxt);
            sourceAddressHTxt = (MTextView) view.findViewById(R.id.sourceAddressHTxt);
            destAddressHTxt = (MTextView) view.findViewById(R.id.destAddressHTxt);
            slecttypearea = (LinearLayout) view.findViewById(R.id.slecttypearea);
            SelectedTypeNameTxt = (MTextView) view.findViewById(R.id.SelectedTypeNameTxt);


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
