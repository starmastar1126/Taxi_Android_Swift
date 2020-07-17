package com.fragments;


import android.content.Context;
import android.content.DialogInterface;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.widget.RecyclerView;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioButton;

import com.adapter.files.RequestPickUpAdapter;
import com.fastcabtaxi.passenger.FareEstimateActivity;
import com.fastcabtaxi.passenger.MainActivity;
import com.fastcabtaxi.passenger.R;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * A simple {@link Fragment} subclass.
 */
public class RequestPickUpFragment extends Fragment implements RequestPickUpAdapter.OnItemClickList {

    public final int BOTTOM_MENU_CASH = 1;
    public final int BOTTOM_MENU_CARD = 2;
    public final int BOTTOM_MENU_FARE_ESTIMATE = 3;
    public final int BOTTOM_MENU_PROMO = 4;
    public MButton requestPickUpBtn;
    View view;
    GeneralFunctions generalFunc;
    MainActivity mainAct;
    boolean isKilled = false;
    RecyclerView reqPickUpRecyclerView;
    RequestPickUpAdapter adapter;
    ArrayList<HashMap<String, String>> list_item;
    String userProfileJson = "";

    String appliedPromoCode = "";

    int requestPickUpBtnId;

    boolean isreqnow = false;
    boolean isreqlater = false;


    boolean isCardValidated = false;
    boolean isProcessOnClicked = true;

    View payTypeSelectArea;
    MTextView payTypeTxt;

    RadioButton cashRadioBtn;
    RadioButton cardRadioBtn;
    ImageView payImgView;

    ImageView img_ridelater;
    LinearLayout ridelaterview;
    LinearLayout casharea;
    LinearLayout cardarea;


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        view = inflater.inflate(R.layout.fragment_request_pick_up, container, false);

        mainAct = (MainActivity) getActivity();
        generalFunc = mainAct.generalFunc;
        userProfileJson = mainAct.userProfileJson;

        mainAct.setCashSelection(true);


        reqPickUpRecyclerView = (RecyclerView) view.findViewById(R.id.reqPickUpRecyclerView);
        payTypeTxt = (MTextView) view.findViewById(R.id.payTypeTxt);
        requestPickUpBtn = ((MaterialRippleLayout) view.findViewById(R.id.btn_type2)).getChildView();
        payTypeSelectArea = view.findViewById(R.id.payTypeSelectArea);
        cashRadioBtn = (RadioButton) view.findViewById(R.id.cashRadioBtn);
        cardRadioBtn = (RadioButton) view.findViewById(R.id.cardRadioBtn);
        payImgView = (ImageView) view.findViewById(R.id.payImgView);
        img_ridelater = (ImageView) view.findViewById(R.id.img_ridelater);
        ridelaterview = (LinearLayout) view.findViewById(R.id.ridelaterview);
        img_ridelater.setOnClickListener(new setOnClickList());
        cashRadioBtn = (RadioButton) view.findViewById(R.id.cashRadioBtn);
        cardRadioBtn = (RadioButton) view.findViewById(R.id.cardRadioBtn);

        casharea = (LinearLayout) view.findViewById(R.id.casharea);
        cardarea = (LinearLayout) view.findViewById(R.id.cardarea);

        casharea.setOnClickListener(new setOnClickList());
        cardarea.setOnClickListener(new setOnClickList());

        if (mainAct.isUfxRideLater) {
            ridelaterview.setVisibility(View.GONE);
        }

        (view.findViewById(R.id.shadowView)).setVisibility(View.GONE);

        generateData();

        requestPickUpBtnId = Utils.generateViewId();
        requestPickUpBtn.setId(requestPickUpBtnId);

        requestPickUpBtn.setOnClickListener(new setOnClickList());
        (view.findViewById(R.id.paymentArea)).setOnClickListener(new setOnClickList());
        // (view.findViewById(R.id.fareEstArea)).setOnClickListener(new setOnClickList());
        (view.findViewById(R.id.promoArea)).setOnClickListener(new setOnClickList());

        setLabels();

        if (generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Cash")) {
            cashRadioBtn.setVisibility(View.VISIBLE);
            cardRadioBtn.setVisibility(View.GONE);
        } else if (generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Card")) {
            cashRadioBtn.setVisibility(View.GONE);
            cardRadioBtn.setVisibility(View.VISIBLE);

            isCardValidated = true;
            setCardSelection();
            isCardValidated = false;
        }


        if (generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Cash")) {
            cashRadioBtn.setVisibility(View.VISIBLE);
            cardRadioBtn.setVisibility(View.GONE);
        } else if (generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Card")) {
            cashRadioBtn.setVisibility(View.GONE);
            cardRadioBtn.setVisibility(View.VISIBLE);

            setCardSelection();
            isCardValidated = false;
        }

        if (generalFunc.getJsonValue("PROMO_CODE", userProfileJson).equalsIgnoreCase("YES")
                /*&& !mainAct.getCabReqType().equals(Utils.CabReqType_Later)*/) {
            (view.findViewById(R.id.promoArea)).setVisibility(View.VISIBLE);
            // (view.findViewById(R.id.cashviewright)).setVisibility(View.VISIBLE);
        } else {
            (view.findViewById(R.id.promoArea)).setVisibility(View.GONE);
            // (view.findViewById(R.id.cashviewright)).setVisibility(View.GONE);
        }


        return view;
    }

    public void setLabels() {
        if (mainAct.isDeliver(mainAct.getCurrentCabGeneralType())) {
            requestPickUpBtn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_NEXT_TXT"));
        } else if (mainAct.getCabReqType().equals(Utils.CabReqType_Later)) {
            requestPickUpBtn.setText(generalFunc.retrieveLangLBl("", "LBL_CONFIRM_BOOKING"));
        } else {
            requestPickUpBtn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_REQUEST_PICKUP_TXT"));
        }

        cashRadioBtn.setText(generalFunc.retrieveLangLBl("", "LBL_PAY_BY_CASH_TXT"));
        payTypeTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CASH_TXT"));
        cardRadioBtn.setText(generalFunc.retrieveLangLBl("", "LBL_PAY_BY_CARD_TXT"));
//        ((MTextView) view.findViewById(R.id.fareEstTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_FARE_ESTIMATE_TXT"));
        ((MTextView) view.findViewById(R.id.promoTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_PRMO_TXT"));
    }

    public void generateData() {

        if (list_item == null) {
            list_item = new ArrayList<>();
            adapter = new RequestPickUpAdapter(getActContext(), list_item, generalFunc);

            reqPickUpRecyclerView.setAdapter(adapter);

//            adapter.setIsFirstRun(true);
            adapter.setOnItemClickList(this);
        } else {
            list_item.clear();
        }

        if (!mainAct.isDeliver(generalFunc.getJsonValue("APP_TYPE", userProfileJson)) &&
                !mainAct.isDeliver(mainAct.getCurrentCabGeneralType())) {
            list_item.add(buildMap(generalFunc.retrieveLangLBl("", "LBL_CASH_TXT"), R.mipmap.ic_cash, R.mipmap.ic_cash_hover, BOTTOM_MENU_CASH));
        }

        if (generalFunc.getJsonValue("PayPalConfiguration", userProfileJson).equalsIgnoreCase("Yes")
                && !mainAct.getCabReqType().equals(Utils.CabReqType_Later)) {
            list_item.add(buildMap(generalFunc.retrieveLangLBl("Card", "LBL_CARD"), R.mipmap.ic_card, R.mipmap.ic_card_hover, BOTTOM_MENU_CARD));
        }

        if (!mainAct.getCurrentCabGeneralType().equals(Utils.CabGeneralType_UberX)) {

            list_item.add(buildMap(generalFunc.retrieveLangLBl("", "LBL_FARE_ESTIMATE_TXT"), R.mipmap.ic_fare_estimate, R.mipmap.ic_fare_estimate_hover, BOTTOM_MENU_FARE_ESTIMATE));
        }

        if (generalFunc.getJsonValue("PROMO_CODE", userProfileJson).equalsIgnoreCase("YES")) {
            list_item.add(buildMap(generalFunc.retrieveLangLBl("", "LBL_PRMO_TXT"), R.mipmap.ic_fare_estimate, R.mipmap.ic_fare_estimate_hover, BOTTOM_MENU_PROMO));
        }


    }

    public void hidePayTypeSelectionArea() {
        payTypeSelectArea.setVisibility(View.GONE);
        mainAct.setPanelHeight(100);
    }

    public void setCashSelection() {
        payTypeTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CASH_TXT"));

        isCardValidated = false;
        mainAct.setCashSelection(true);
        cashRadioBtn.setChecked(true);

        payImgView.setImageResource(R.mipmap.ic_cash_new);


    }

    public void setCardSelection() {


        payTypeTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CARD"));

        isCardValidated = true;
        mainAct.setCashSelection(false);

        cardRadioBtn.setChecked(true);

        payImgView.setImageResource(R.mipmap.ic_card_new);


    }

    public HashMap<String, String> buildMap(String labelValue, int icon, int icon_hover, int position) {
        HashMap<String, String> map = new HashMap<>();
        map.put("Title", labelValue);
        map.put("Icon", "" + icon);
        map.put("IconHover", "" + icon_hover);
        map.put("Position", "" + position);
        if (position == BOTTOM_MENU_FARE_ESTIMATE) {
            map.put("isDivider", "true");
        } else {
            map.put("isDivider", "false");
        }

        return map;
    }

    public Context getActContext() {
        return mainAct.getActContext();
    }

    public void setUserProfileJson() {
        userProfileJson = mainAct.userProfileJson;
    }

    @Override
    public void onItemClick(int position) {

        HashMap<String, String> map = list_item.get(position);
        int clickID = generalFunc.parseIntegerValue(0, map.get("Position"));

        for (int i = 0; i < list_item.size(); i++) {
            RequestPickUpAdapter.ViewHolder cabTypeViewHolder = (RequestPickUpAdapter.ViewHolder) reqPickUpRecyclerView.findViewHolderForAdapterPosition(i);

            if (clickID == BOTTOM_MENU_CASH || (clickID == BOTTOM_MENU_CARD && isCardValidated == true)) {
                if (i != position) {
                    adapter.setData(cabTypeViewHolder, i, false);
                } else if (i == position) {
                    adapter.setData(cabTypeViewHolder, i, true);

                }
            }
        }

        if (isProcessOnClicked == false) {
            isProcessOnClicked = true;
            return;
        }

        switch (clickID) {
            case BOTTOM_MENU_CASH:
                isCardValidated = false;
                mainAct.setCashSelection(true);
                break;
            case BOTTOM_MENU_CARD:
                checkCardConfig();
                break;
            case BOTTOM_MENU_FARE_ESTIMATE:
                new StartActProcess(getActContext()).startActWithData(FareEstimateActivity.class, mainAct.getFareEstimateBundle());
                break;
            case BOTTOM_MENU_PROMO:
                showPromoBox();
                break;
        }
    }

    public void checkCardConfig() {
        setUserProfileJson();

        String vStripeCusId = generalFunc.getJsonValue("vStripeCusId", userProfileJson);

        if (vStripeCusId.equals("")) {
            // Open CardPaymentActivity
            mainAct.OpenCardPaymentAct(false);
        } else {
            showPaymentBox();
        }
    }

    public void showPromoBox() {
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle(generalFunc.retrieveLangLBl("", "LBL_PROMO_CODE_ENTER_TITLE"));

        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.input_box_view, null);
        builder.setView(dialogView);

        final MaterialEditText input = (MaterialEditText) dialogView.findViewById(R.id.editBox);
        input.setGravity(Gravity.CENTER_HORIZONTAL);
        // input.setTextAlignment();


        if (!appliedPromoCode.equals("")) {
            input.setText(appliedPromoCode);
        }
        builder.setPositiveButton(generalFunc.retrieveLangLBl("OK", "LBL_BTN_OK_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                if (input.getText().toString().trim().equals("") && appliedPromoCode.equals("")) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_ENTER_PROMO"));
                } else if (input.getText().toString().trim().equals("") && !appliedPromoCode.equals("")) {
                    appliedPromoCode = "";
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_PROMO_REMOVED"));
                } else if (input.getText().toString().contains(" ")) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_PROMO_INVALIED"));
                } else {
                    checkPromoCode(input.getText().toString().trim());
                }
            }
        });
        builder.setNegativeButton(generalFunc.retrieveLangLBl("", "LBL_SKIP_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
            }
        });

        android.support.v7.app.AlertDialog alertDialog = builder.create();


        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(alertDialog);
        }
        alertDialog.show();
        alertDialog.setOnCancelListener(new DialogInterface.OnCancelListener() {
            @Override
            public void onCancel(DialogInterface dialogInterface) {
                Utils.hideKeyboard(mainAct);
            }
        });

    }

    public void showPaymentBox() {
        android.support.v7.app.AlertDialog alertDialog;
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle("");
        builder.setCancelable(false);
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.input_box_view, null);
        builder.setView(dialogView);

        final MaterialEditText input = (MaterialEditText) dialogView.findViewById(R.id.editBox);
        final MTextView subTitleTxt = (MTextView) dialogView.findViewById(R.id.subTitleTxt);

        Utils.removeInput(input);

        subTitleTxt.setVisibility(View.VISIBLE);
        subTitleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_TITLE_PAYMENT_ALERT"));
        input.setText(generalFunc.getJsonValue("vCreditCard", userProfileJson));

        builder.setPositiveButton(generalFunc.retrieveLangLBl("Confirm", "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
                checkPaymentCard();
            }
        });
        builder.setNeutralButton(generalFunc.retrieveLangLBl("Change", "LBL_CHANGE"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
                mainAct.OpenCardPaymentAct(false);
            }
        });
        builder.setNegativeButton(generalFunc.retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
            }
        });


        alertDialog = builder.create();
        alertDialog.setCancelable(false);
        alertDialog.setCanceledOnTouchOutside(false);
        alertDialog.show();
    }

    public void checkPaymentCard() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "CheckCard");
        parameters.put("iUserId", generalFunc.getMemberId());

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    String action = generalFunc.getJsonValue(CommonUtilities.action_str, responseString);
                    if (action.equals("1")) {


                        setCardSelection();
                    } else {
                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void performClickOnPayment() {

        for (int i = 0; i < list_item.size(); i++) {
            HashMap<String, String> map = list_item.get(i);
            int clickID = generalFunc.parseIntegerValue(0, map.get("Position"));

            if (clickID == BOTTOM_MENU_CARD) {
                isProcessOnClicked = false;
                adapter.performClick(i);
            }
        }
    }

    public String getAppliedPromoCode() {
        return this.appliedPromoCode;
    }

    public void checkPromoCode(final String promoCode) {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "CheckPromoCode");
        parameters.put("PromoCode", promoCode);
        parameters.put("iUserId", generalFunc.getMemberId());

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    String action = generalFunc.getJsonValue(CommonUtilities.action_str, responseString);
                    if (action.equals("1")) {
                        appliedPromoCode = promoCode;
                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_PROMO_APPLIED"));
                    } else if (action.equals("01")) {
                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_PROMO_USED"));
                    } else {
                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_PROMO_INVALIED"));
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(getActivity());
            if (i == requestPickUpBtnId) {

                if (mainAct.getDestinationStatus()) {
                    String destLocAdd = mainAct != null ? (mainAct.getDestAddress().equals(
                            generalFunc.retrieveLangLBl("", "LBL_SELECTING_LOCATION_TXT")) ? "" : mainAct.getDestAddress()) : "";
                    if (destLocAdd.equals("")) {
                        return;
                    }
                }

                if (isCardValidated == false && generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Card")) {

                    if (!mainAct.getCabReqType().equals(Utils.CabReqType_Later)) {
                        // mainAct.requestPickUp();
                        isreqnow = true;
                    } else {
                        isreqlater = true;
                        // mainAct.setRideSchedule();
                    }
                    checkCardConfig();
                    return;
                }

                if (mainAct.getCurrentCabGeneralType().equals("Deliver")) {
                    if (mainAct.getDestinationStatus() == false) {
                        generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Please add your destination location " +
                                "to deliver your package.", "LBL_ADD_DEST_MSG_DELIVER_ITEM"));
                        return;
                    }
                    mainAct.setDeliverySchedule();
                    return;
                }


                if (!mainAct.getCabReqType().equals(Utils.CabReqType_Later)) {
                    mainAct.requestPickUp();
                } else {

                    mainAct.setRideSchedule();
                }
            } else if (i == R.id.paymentArea) {

                if (payTypeSelectArea.getVisibility() == View.VISIBLE) {
                    hidePayTypeSelectionArea();
                } else {
                    payTypeSelectArea.setVisibility(View.VISIBLE);

                    if (generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Cash-Card")) {
                        mainAct.setPanelHeight(200);
                    } else {
                        mainAct.setPanelHeight(200 - 48);
                    }
                }

            } else if (i == R.id.cardarea) {
                hidePayTypeSelectionArea();
                setCashSelection();
                checkCardConfig();

            } else if (i == R.id.casharea) {
                hidePayTypeSelectionArea();
                setCashSelection();
            } else if (i == R.id.promoArea) {
                showPromoBox();
            } else if (i == R.id.img_ridelater) {
                mainAct.chooseDateTime();
            }
        }
    }
}
