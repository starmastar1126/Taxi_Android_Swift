package com.adapter.files;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.fastcabtaxi.passenger.R;
import com.general.files.GeneralFunctions;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import java.util.ArrayList;
import java.util.HashMap;

import static com.adapter.files.DrawerAdapter.view;

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

    public MyBookingsRecycleAdapter(Context mContext, ArrayList<HashMap<String, String>> list, GeneralFunctions generalFunc, boolean isFooterEnabled) {
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
            viewHolder.dateTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.DateFormatewithTime));
            viewHolder.statusHTxt.setText(item.get("LBL_Status") + ":");
            viewHolder.sourceAddressTxt.setText(item.get("vSourceAddresss"));

            viewHolder.destAddressHTxt.setText(item.get("LBL_DEST_LOCATION"));
            viewHolder.sourceAddressHTxt.setText(item.get("LBL_PICK_UP_LOCATION"));
            viewHolder.slecttypearea.setVisibility(View.VISIBLE);
            if (item.get("SelectedCategory") != null && !item.get("SelectedCategory").equalsIgnoreCase("")) {
                viewHolder.SelectedTypeNameTxt.setText(item.get("SelectedCategory") + "-" + (item.get("SelectedVehicle")));
            } else {
                viewHolder.slecttypearea.setVisibility(View.GONE);

            }
            if (item.get("tDestAddress").equals("")) {
                // viewHolder.destAddressTxt.setText("---------------");
                //viewHolder.destAddressHTxt.setVisibility(View.VISIBLE);
                viewHolder.destAddressTxt.setVisibility(View.GONE);
                viewHolder.destarea.setVisibility(View.GONE);
                viewHolder.imagedest.setVisibility(View.GONE);
                viewHolder.destAddressHTxt.setVisibility(View.GONE);
            } else {
                viewHolder.destAddressTxt.setVisibility(View.VISIBLE);
                viewHolder.destAddressHTxt.setVisibility(View.VISIBLE);
                viewHolder.destAddressTxt.setText(item.get("tDestAddress"));

            }

            if (item.get("eStatus").equalsIgnoreCase("Pending")) {
                viewHolder.statusVTxt.setVisibility(View.GONE);
                viewHolder.statusHTxt.setVisibility(View.GONE);
            } else {
                viewHolder.statusVTxt.setVisibility(View.VISIBLE);
                viewHolder.statusHTxt.setVisibility(View.VISIBLE);
                viewHolder.statusVTxt.setText(item.get("eStatus"));
            }
            viewHolder.cancelBookingTxt.setText(item.get("LBL_CANCEL_BOOKING"));
            viewHolder.btn_type2.setText(item.get("LBL_CANCEL_BOOKING"));

            LinearLayout.LayoutParams statusAreaParams = (LinearLayout.LayoutParams) viewHolder.statusArea.getLayoutParams();

            if (item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_Deliver) || item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_Deliver) || item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_Ride) || item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
                viewHolder.dateTxt.setVisibility(View.INVISIBLE);
                viewHolder.etypeTxt.setText(generalFunc.getDateFormatedType(item.get("dBooking_dateOrig"), Utils.OriginalDateFormate, Utils.DateFormatewithTime));


            } else {
                viewHolder.etypeTxt.setText(item.get("eType"));
                viewHolder.dateTxt.setVisibility(View.VISIBLE);

            }

            viewHolder.statusArea.setVisibility(View.VISIBLE);
            if (!item.get("eStatusV").equals("Pending")) {
                viewHolder.btnarea.setVisibility(View.GONE);
                viewHolder.cancelBookingArea.setVisibility(View.GONE);

            } else {
                viewHolder.cancelBookingArea.setVisibility(View.GONE);
                viewHolder.btnarea.setVisibility(View.VISIBLE);
            }

            if (item.get("appType").equalsIgnoreCase(Utils.CabGeneralType_UberX)) {

                if (item.get("eStatus").equalsIgnoreCase(generalFunc.retrieveLangLBl("", "LBL_DECLINE_TXT")) || item.get("eStatus").equalsIgnoreCase(generalFunc.retrieveLangLBl("", "LBL_CANCELLED"))) {
                    viewHolder.btnarea.setVisibility(View.VISIBLE);
                    viewHolder.cancelBookingArea.setVisibility(View.GONE);
                    viewHolder.statusArea.setVisibility(View.VISIBLE);
                    viewHolder.btn_type2.setText(generalFunc.retrieveLangLBl("REBOOKING", "LBL_REBOOKING"));
                    viewHolder.btn_type_booking.setText(generalFunc.retrieveLangLBl("RESCHEDULE", "LBL_RESCHEDULE"));
                    viewHolder.reschedulearea.setVisibility(View.GONE);

                }
            }

            viewHolder.cancelBookingArea.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    if (mItemClickListener != null) {
                        mItemClickListener.onItemClickList(view, position, false);
                    }
                }
            });


            viewHolder.btn_type2.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (mItemClickListener != null) {
                        mItemClickListener.onItemClickList(view, position, false);
                    }

                }
            });
            viewHolder.btn_type_booking.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    mItemClickListener.onItemClickList(view, position, false);
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
//        Utils.printLog("Footer", "added");
        this.isFooterEnabled = true;
        notifyDataSetChanged();
        if (footerHolder != null)
            footerHolder.progressArea.setVisibility(View.VISIBLE);
    }

    public void removeFooterView() {
//        Utils.printLog("Footer", "removed");
        if (footerHolder != null)
            footerHolder.progressArea.setVisibility(View.GONE);
//        footerHolder.progressArea.setPadding(0, -1 * footerView.getHeight(), 0, 0);
    }

    public interface OnItemClickListener {
        void onItemClickList(View v, int position, boolean isSchedulebooking);
    }

    // inner class to hold a reference to each item of RecyclerView
    public class ViewHolder extends RecyclerView.ViewHolder {

        public MTextView myBookingNoHTxt;
        public MTextView myBookingNoVTxt;
        public MTextView dateTxt;
        public MTextView sourceAddressTxt;
        public MTextView destAddressTxt;
        public MTextView statusHTxt;
        public MTextView statusVTxt;
        public MTextView cancelBookingTxt;
        public LinearLayout cancelBookingArea;
        public LinearLayout statusArea;
        public MButton btn_type2;
        public MButton btn_type_booking;
        public LinearLayout btnarea;

        public MTextView sourceAddressHTxt;
        public MTextView destAddressHTxt;

        public MTextView etypeTxt;
        public ImageView imagedest;
        public LinearLayout destarea;
        public LinearLayout reschedulearea;
        public LinearLayout slecttypearea;
        public MTextView SelectedTypeNameTxt;

        public ViewHolder(View view) {
            super(view);
            int submitBtnId;
            int bookingBtnId;
            myBookingNoHTxt = (MTextView) view.findViewById(R.id.myBookingNoHTxt);
            myBookingNoVTxt = (MTextView) view.findViewById(R.id.myBookingNoVTxt);
            dateTxt = (MTextView) view.findViewById(R.id.dateTxt);
            sourceAddressTxt = (MTextView) view.findViewById(R.id.sourceAddressTxt);
            destAddressTxt = (MTextView) view.findViewById(R.id.destAddressTxt);
            statusHTxt = (MTextView) view.findViewById(R.id.statusHTxt);
            statusVTxt = (MTextView) view.findViewById(R.id.statusVTxt);
            cancelBookingTxt = (MTextView) view.findViewById(R.id.cancelBookingTxt);
            cancelBookingArea = (LinearLayout) view.findViewById(R.id.cancelBookingArea);
            statusArea = (LinearLayout) view.findViewById(R.id.statusArea);
            etypeTxt = (MTextView) view.findViewById(R.id.etypeTxt);
            btn_type2 = ((MaterialRippleLayout) view.findViewById(R.id.btn_type2)).getChildView();
            btn_type_booking = ((MaterialRippleLayout) view.findViewById(R.id.btn_type_booking)).getChildView();
            btnarea = (LinearLayout) view.findViewById(R.id.btnarea);
            sourceAddressHTxt = (MTextView) view.findViewById(R.id.sourceAddressHTxt);
            destAddressHTxt = (MTextView) view.findViewById(R.id.destAddressHTxt);
            destarea = (LinearLayout) view.findViewById(R.id.destarea);
            reschedulearea = (LinearLayout) view.findViewById(R.id.reschedulearea);
            imagedest = (ImageView) view.findViewById(R.id.imagedest);
            slecttypearea = (LinearLayout) view.findViewById(R.id.slecttypearea);
            SelectedTypeNameTxt = (MTextView) view.findViewById(R.id.SelectedTypeNameTxt);

            submitBtnId = Utils.generateViewId();
            btn_type2.setId(submitBtnId);


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
