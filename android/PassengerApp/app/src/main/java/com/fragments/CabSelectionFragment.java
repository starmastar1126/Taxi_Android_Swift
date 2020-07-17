package com.fragments;


import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Point;
import android.location.Location;
import android.os.Bundle;
import android.os.Handler;
import android.support.design.widget.BottomSheetBehavior;
import android.support.design.widget.BottomSheetDialog;
import android.support.v4.app.Fragment;
import android.support.v7.widget.RecyclerView;
import android.util.DisplayMetrics;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RadioButton;
import android.widget.RelativeLayout;

import com.adapter.files.CabTypeAdapter;
import com.fastcabtaxi.passenger.FareBreakDownActivity;
import com.fastcabtaxi.passenger.MainActivity;
import com.fastcabtaxi.passenger.R;
import com.drawRoute.DirectionsJSONParser;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.MapAnimator;
import com.general.files.StartActProcess;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.LatLngBounds;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.maps.model.Polyline;
import com.google.android.gms.maps.model.PolylineOptions;
import com.squareup.picasso.Picasso;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.anim.loader.AVLoadingIndicatorView;
import com.view.editBox.MaterialEditText;
import com.view.slidinguppanel.SlidingUpPanelLayout;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

/**
 * A simple {@link Fragment} subclass.
 */
public class CabSelectionFragment extends Fragment implements CabTypeAdapter.OnItemClickList {


    static MainActivity mainAct;
    static GeneralFunctions generalFunc;
    static MTextView payTypeTxt;
    static RadioButton cardRadioBtn;
    static ImageView payImgView;
    // public LinearLayout rideBtnContainer;
    public MButton ride_now_btn;
    public int currentPanelDefaultStateHeight = 100;
    public String currentCabGeneralType = "";
    public CabTypeAdapter adapter;
    public ArrayList<HashMap<String, String>> cabTypeList;
    public String app_type = "Ride";
    public ImageView img_ridelater;
    LinearLayout imageLaterarea;
    //    public int isSelcted = -1;
    public boolean isclickableridebtn = false;
    public boolean isroutefound = false;
    public int selpos = 0;
    View view = null;
    String userProfileJson = "";
    RecyclerView carTypeRecyclerView;
    ArrayList<HashMap<String, String>> cabCategoryList;

    String currency_sign = "";
    boolean isKilled = false;
    LinearLayout paymentArea;
    LinearLayout promoArea;
    View payTypeSelectArea;
    String appliedPromoCode = "";
    public boolean isCardValidated = true;
    RadioButton cashRadioBtn;
    LinearLayout casharea;
    LinearLayout cardarea;
    LinearLayout cashcardarea;
    String distance = "";
    String time = "";
    AVLoadingIndicatorView loaderView;
    MTextView noServiceTxt;
    boolean isCardnowselcted = false;
    boolean isCardlaterselcted = false;
    //    boolean dialogShowOnce = true;
    String RideDeliveryType = "";
    MTextView promoTxt;
    boolean ridelaterclick = false;
    boolean ridenowclick = false;
    int i = 0;
    int j = 0;
    Location tempDestLocation;
    Location tempPickUpLocation;
    ExecuteWebServerUrl estimateFareTask;
    Polyline route_polyLine;

    public boolean isSkip = false;

    // PolylineOptions polyLineOptions;


    public LatLng sourceLocation = null;
    public LatLng destLocation = null;

    boolean isRouteFail = false;

    int height = 0;
    int width = 0;
    int maxX = 0;
    int maxY = 0;
    public Marker sourceMarker, destMarker, sourceDotMarker, destDotMarker;
    MarkerOptions source_dot_option, dest_dot_option;

    String required_str = "";
    ProgressBar mProgressBar;

    public static void setCardSelection() {
        if (generalFunc == null) {
            generalFunc = mainAct.generalFunc;
        }
        payTypeTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CARD"));


        mainAct.setCashSelection(false);

        cardRadioBtn.setChecked(true);

        payImgView.setImageResource(R.mipmap.ic_card_new);
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        if (view != null) {
            return view;
        }


        view = inflater.inflate(R.layout.fragment_new_cab_selection, container, false);

        DisplayMetrics displayMetrics = new DisplayMetrics();
        ((Activity) getContext()).getWindowManager()
                .getDefaultDisplay()
                .getMetrics(displayMetrics);
        height = displayMetrics.heightPixels;
        width = displayMetrics.widthPixels;


        mainAct = (MainActivity) getActivity();
        generalFunc = mainAct.generalFunc;

        height = displayMetrics.heightPixels - Utils.dpToPx(getActContext(), 300);

//        Display mdisp = mainAct.getWindowManager().getDefaultDisplay();
//        Point mdispSize = new Point();
//        mdisp.getSize(mdispSize);
//        maxX = mdispSize.x;
//        maxY = mdispSize.y;
        ride_now_btn = ((MaterialRippleLayout) view.findViewById(R.id.ride_now_btn)).getChildView();
        ride_now_btn.setId(Utils.generateViewId());
        mProgressBar = (ProgressBar) view.findViewById(R.id.mProgressBar);
        mProgressBar.getIndeterminateDrawable().setColorFilter(
                getActContext().getResources().getColor(R.color.appThemeColor_2), android.graphics.PorterDuff.Mode.SRC_IN);
        findRoute("--");
        RideDeliveryType = getArguments().getString("RideDeliveryType");

        carTypeRecyclerView = (RecyclerView) view.findViewById(R.id.carTypeRecyclerView);
        loaderView = (AVLoadingIndicatorView) view.findViewById(R.id.loaderView);
        payTypeSelectArea = view.findViewById(R.id.payTypeSelectArea);
        payTypeTxt = (MTextView) view.findViewById(R.id.payTypeTxt);
        promoTxt = (MTextView) view.findViewById(R.id.promoTxt);
        promoTxt.setText(generalFunc.retrieveLangLBl("", "LBL_PRMO_TXT"));


        img_ridelater = (ImageView) view.findViewById(R.id.img_ridelater);
        imageLaterarea = (LinearLayout) view.findViewById(R.id.imageLaterarea);
        noServiceTxt = (MTextView) view.findViewById(R.id.noServiceTxt);


        casharea = (LinearLayout) view.findViewById(R.id.casharea);
        cardarea = (LinearLayout) view.findViewById(R.id.cardarea);

        casharea.setOnClickListener(new setOnClickList());
        cardarea.setOnClickListener(new setOnClickList());

        img_ridelater.setOnClickListener(new setOnClickList());

        paymentArea = (LinearLayout) view.findViewById(R.id.paymentArea);
        promoArea = (LinearLayout) view.findViewById(R.id.promoArea);
        promoArea.setOnClickListener(new setOnClickList());
        paymentArea.setOnClickListener(new setOnClickList());
        cashRadioBtn = (RadioButton) view.findViewById(R.id.cashRadioBtn);
        cardRadioBtn = (RadioButton) view.findViewById(R.id.cardRadioBtn);

        payImgView = (ImageView) view.findViewById(R.id.payImgView);

        cashcardarea = (LinearLayout) view.findViewById(R.id.cashcardarea);

        userProfileJson = mainAct.userProfileJson;

        currency_sign = generalFunc.getJsonValue("CurrencySymbol", userProfileJson);
        app_type = generalFunc.getJsonValue("APP_TYPE", userProfileJson);

        if (generalFunc.getJsonValue("RIDE_LATER_BOOKING_ENABLED", userProfileJson).equalsIgnoreCase("Yes")) {

            imageLaterarea.setVisibility(View.VISIBLE);

        } else {
            imageLaterarea.setVisibility(View.GONE);
        }

        if (mainAct.isDeliver(mainAct.getCurrentCabGeneralType())) {
            img_ridelater.setImageResource(R.mipmap.ride_later_delivery);
        }

        if (app_type.equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
            app_type = "Ride";
        }

        if (app_type.equals(Utils.CabGeneralType_UberX)) {
            view.setVisibility(View.GONE);
            return view;
        }

        isKilled = false;


        if (generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Cash")) {
            cashRadioBtn.setVisibility(View.VISIBLE);
            payTypeTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CASH_TXT"));
            cardRadioBtn.setVisibility(View.GONE);
        } else if (generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Card")) {
            cashRadioBtn.setVisibility(View.GONE);
            cardRadioBtn.setVisibility(View.VISIBLE);
            payTypeTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CARD"));
            isCardValidated = true;
            setCardSelection();
            isCardValidated = false;
        }


        setLabels(true);

        ride_now_btn.setOnClickListener(new setOnClickList());


        configRideLaterBtnArea(false);

        mainAct.sliding_layout.addPanelSlideListener(new SlidingUpPanelLayout.PanelSlideListener() {
            @Override
            public void onPanelSlide(View panel, float slideOffset) {

                if (isKilled) {
                    return;
                }

            }

            @Override
            public void onPanelStateChanged(View panel, SlidingUpPanelLayout.PanelState previousState, SlidingUpPanelLayout.PanelState newState) {

                if (isKilled) {
                    return;
                }
                if (newState == SlidingUpPanelLayout.PanelState.COLLAPSED) {
                    configRideLaterBtnArea(false);
                }
            }
        });
        return view;
    }

    public void showLoader() {
        loaderView.setVisibility(View.VISIBLE);
        closeNoServiceText();
    }

    public void showNoServiceText() {
        noServiceTxt.setVisibility(View.VISIBLE);
    }

    public void closeNoServiceText() {
        noServiceTxt.setVisibility(View.GONE);
    }

    public void closeLoader() {
        loaderView.setVisibility(View.GONE);
        if (mainAct.cabTypesArrList.size() == 0) {
            showNoServiceText();
        } else {
            closeNoServiceText();
        }
    }

    public void setUserProfileJson() {
        userProfileJson = mainAct.userProfileJson;
    }

    public void checkCardConfig() {
        setUserProfileJson();

        String vStripeCusId = generalFunc.getJsonValue("vStripeCusId", userProfileJson);

        if (vStripeCusId.equals("")) {
            mainAct.OpenCardPaymentAct(true);
        } else {
            showPaymentBox();
        }
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
                mainAct.OpenCardPaymentAct(true);
                ridelaterclick = false;
            }
        });
        builder.setNegativeButton(generalFunc.retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
                ridelaterclick = false;
            }
        });


        alertDialog = builder.create();
        alertDialog.setCancelable(false);
        alertDialog.setCanceledOnTouchOutside(false);
        alertDialog.show();
    }

    public void setCashSelection() {
        payTypeTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CASH_TXT"));

        isCardValidated = false;
        mainAct.setCashSelection(true);
        cashRadioBtn.setChecked(true);

        payImgView.setImageResource(R.mipmap.ic_cash_new);
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

                        if (mainAct.pickUpLocation == null) {
                            return;
                        }

                        isCardValidated = true;
                        setCardSelection();

                        if (isCardnowselcted) {
                            isCardnowselcted = false;


                            if (mainAct.isDeliver(mainAct.getCurrentCabGeneralType())) {
                                if (mainAct.getDestinationStatus() == false) {
                                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Please add your destination location " +
                                            "to deliver your package.", "LBL_ADD_DEST_MSG_DELIVER_ITEM"));
                                    return;
                                }
                                mainAct.continueDeliveryProcess();
                                return;
                            } else {
                                if (!mainAct.getCabReqType().equals(Utils.CabReqType_Later)) {
                                    mainAct.continuePickUpProcess();
                                } else {
                                    mainAct.setRideSchedule();
                                }

                            }
                        }

                        if (isCardlaterselcted) {
                            isCardlaterselcted = false;
                            mainAct.chooseDateTime();
                        }

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

    public void setLabels(boolean isCallGenerateType) {

        required_str = generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT");

        if ((mainAct.currentLoadedDriverList != null && mainAct.currentLoadedDriverList.size() < 1) || mainAct.currentLoadedDriverList == null) {
            ride_now_btn.setText(generalFunc.retrieveLangLBl("No Car available.", "LBL_NO_CARS"));

            if (isCallGenerateType) {
                Utils.printLog("getCurrentCabGenralType", "isCallGenerateType" + isCallGenerateType);

                generateCarType();
            }

            return;


        }

        noServiceTxt.setText(generalFunc.retrieveLangLBl("service not available in this location", "LBL_NO_SERVICE_AVAILABLE_TXT"));


        if (app_type.equalsIgnoreCase("UberX")) {
            currentCabGeneralType = Utils.CabGeneralType_UberX;
        } else {
            String type = mainAct.isDeliver(app_type) || mainAct.isDeliver(RideDeliveryType) ? "Deliver" : Utils.CabGeneralType_Ride;
            if (type.equals("Deliver")) {

                Utils.printLog("Handle btn", mainAct.getCabReqType());
                if (mainAct.getCabReqType().equals(Utils.CabReqType_Now)) {
                    ride_now_btn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_NEXT_TXT"));
                } else if (mainAct.getCabReqType().equals(Utils.CabReqType_Later)) {
                    ride_now_btn.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_NEXT_TXT"));
                }
            } else {
                ride_now_btn.setText(generalFunc.retrieveLangLBl("Request Now", "LBL_REQUEST_NOW"));
            }
        }


        if (isCallGenerateType) {
            Utils.printLog("getCurrentCabGenralType", "isCallGenerateType 1" + isCallGenerateType);

            generateCarType();
        }

    }

    public boolean calculateDistnace(Location start, Location end) {


        float distance = start.distanceTo(end);
        Utils.printLog("distance", "::" + distance);
        if (distance > 200) {
            return true;
        } else {
            return false;
        }
    }

    public void releaseResources() {
        isKilled = true;
    }

    public void changeCabGeneralType(String currentCabGeneralType) {
        this.currentCabGeneralType = currentCabGeneralType;
    }

    public String getCurrentCabGeneralType() {
        return this.currentCabGeneralType;
    }

    public void configRideLaterBtnArea(boolean isGone) {
        if (isGone == true || app_type.equalsIgnoreCase("Ride-Delivery")) {
            mainAct.setPanelHeight(237);
            if (!app_type.equalsIgnoreCase("Ride-Delivery")) {
                mainAct.setUserLocImgBtnMargin(105);
            }
            return;
        }
        if (!generalFunc.getJsonValue("RIIDE_LATER", userProfileJson).equalsIgnoreCase("YES") && !app_type.equalsIgnoreCase("Ride-Delivery")) {
            mainAct.setUserLocImgBtnMargin(105);
            mainAct.setPanelHeight(237);
        } else {

            mainAct.setPanelHeight(237);
            currentPanelDefaultStateHeight = 237;
            mainAct.setUserLocImgBtnMargin(164);
        }
    }

    public void generateCarType() {

        if (cabTypeList == null) {
            Utils.printLog("getCurrentCabGenralType", "cabTypeList" + cabTypeList);


            cabTypeList = new ArrayList<>();
            if (adapter == null) {
                adapter = new CabTypeAdapter(getActContext(), cabTypeList, generalFunc);
                adapter.setSelectedVehicleTypeId(mainAct.getSelectedCabTypeId());
                carTypeRecyclerView.setAdapter(adapter);
                adapter.setOnItemClickList(this);
            } else {
                adapter.notifyDataSetChanged();
            }
        } else {
            cabTypeList.clear();
        }

        if (mainAct.isDeliver(currentCabGeneralType)) {
            this.currentCabGeneralType = "Deliver";
        }
//        showLoader();

        for (int i = 0; i < mainAct.cabTypesArrList.size(); i++) {
            //  JSONObject obj_temp = generalFunc.getJsonObject(vehicleTypesArr, i);

            HashMap<String, String> map = new HashMap<>();
            String iVehicleTypeId = mainAct.cabTypesArrList.get(i).get("iVehicleTypeId");

            String vVehicleType = mainAct.cabTypesArrList.get(i).get("vVehicleType");
            String fPricePerKM = mainAct.cabTypesArrList.get(i).get("fPricePerKM");
            String fPricePerMin = mainAct.cabTypesArrList.get(i).get("fPricePerMin");
            String iBaseFare = mainAct.cabTypesArrList.get(i).get("iBaseFare");
            String fCommision = mainAct.cabTypesArrList.get(i).get("fCommision");
            String iPersonSize = mainAct.cabTypesArrList.get(i).get("iPersonSize");
            String vLogo = mainAct.cabTypesArrList.get(i).get("vLogo");
            String vLogo1 = mainAct.cabTypesArrList.get(i).get("vLogo1");
            String eType = mainAct.cabTypesArrList.get(i).get("eType");
            if (!eType.equalsIgnoreCase(currentCabGeneralType)) {
                continue;
            }
            map.put("iVehicleTypeId", iVehicleTypeId);
            map.put("vVehicleType", vVehicleType);
            map.put("fPricePerKM", fPricePerKM);
            map.put("fPricePerMin", fPricePerMin);
            map.put("iBaseFare", iBaseFare);
            map.put("fCommision", fCommision);
            map.put("iPersonSize", iPersonSize);
            map.put("vLogo", vLogo);
            map.put("vLogo1", vLogo1);


            if (i == 0) {
                adapter.setSelectedVehicleTypeId(iVehicleTypeId);
            }
            cabTypeList.add(map);

        }
        mainAct.setCabTypeList(cabTypeList);
        adapter.notifyDataSetChanged();
        Utils.printLog("getCurrentCabGenralType", "cabTypeList size" + cabTypeList.size());

        if (cabTypeList.size() > 0) {
            onItemClick(0);
        }
    }

    public void closeLoadernTxt() {
        loaderView.setVisibility(View.GONE);
        closeNoServiceText();

    }

    public void setShadow() {
        (view.findViewById(R.id.shadowView)).setVisibility(View.VISIBLE);
    }


    public Context getActContext() {
        return mainAct.getActContext();
    }

    @Override
    public void onItemClick(int position) {

        selpos = position;
        String iVehicleTypeId = cabTypeList.get(position).get("iVehicleTypeId");

        if (!iVehicleTypeId.equals(mainAct.getSelectedCabTypeId())) {
            adapter.setSelectedVehicleTypeId(iVehicleTypeId);
            adapter.notifyDataSetChanged();
            mainAct.changeCabType(iVehicleTypeId);

            if (cabTypeList.get(position).get("eFlatTrip") != null &&
                    !cabTypeList.get(position).get("eFlatTrip").equalsIgnoreCase("")
                    && cabTypeList.get(position).get("eFlatTrip").equalsIgnoreCase("Yes")) {
                mainAct.isFixFare = true;
            } else {
                mainAct.isFixFare = false;

            }
        } else {
            openFareDetailsDilaog(position);
        }


    }

    public void openFareEstimateDialog() {
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle("");

        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.fare_detail_design, null);
        builder.setView(dialogView);

        ((MTextView) dialogView.findViewById(R.id.fareDetailHTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_FARE_DETAIL_TXT"));
        ((MTextView) dialogView.findViewById(R.id.baseFareHTxt)).setText(" " + generalFunc.retrieveLangLBl("", "LBL_BASE_FARE_TXT"));
        ((MTextView) dialogView.findViewById(R.id.parMinHTxt)).setText(" / " + generalFunc.retrieveLangLBl("", "LBL_MIN_TXT"));
        ((MTextView) dialogView.findViewById(R.id.parMinHTxt)).setVisibility(View.GONE);
        ((MTextView) dialogView.findViewById(R.id.andTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_AND_TXT"));
        ((MTextView) dialogView.findViewById(R.id.parKmHTxt)).setText(" / " + generalFunc.retrieveLangLBl("", "LBL_KM_TXT"));
        ((MTextView) dialogView.findViewById(R.id.parKmHTxt)).setVisibility(View.GONE);

        ((MTextView) dialogView.findViewById(R.id.baseFareVTxt)).setText(currency_sign + " " +
                generalFunc.getSelectedCarTypeData(mainAct.getSelectedCabTypeId(), mainAct.cabTypesArrList, "iBaseFare"));

        ((MTextView) dialogView.findViewById(R.id.parMinVTxt)).setText(currency_sign + " " +
                generalFunc.getSelectedCarTypeData(mainAct.getSelectedCabTypeId(), mainAct.cabTypesArrList, "fPricePerMin") + " / " + generalFunc.retrieveLangLBl("", "LBL_MIN_TXT"));

        ((MTextView) dialogView.findViewById(R.id.parKmVTxt)).setText(currency_sign + " " +
                generalFunc.getSelectedCarTypeData(mainAct.getSelectedCabTypeId(), mainAct.cabTypesArrList, "fPricePerKM") + " / " + generalFunc.retrieveLangLBl("", "LBL_KM_TXT"));

        builder.show();
    }

    public void hidePayTypeSelectionArea() {
        payTypeSelectArea.setVisibility(View.GONE);
        cashcardarea.setVisibility(View.VISIBLE);
        mainAct.setPanelHeight(237);
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

                        findRoute("--");
                    }
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void showPromoBox() {
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle(generalFunc.retrieveLangLBl("", "LBL_PROMO_CODE_ENTER_TITLE"));

        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.input_box_view, null);
        builder.setView(dialogView);

        final MaterialEditText input = (MaterialEditText) dialogView.findViewById(R.id.editBox);


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
        builder.setNegativeButton(generalFunc.retrieveLangLBl("", "LBL_CANCEL_GENERAL"), new DialogInterface.OnClickListener() {
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

    public void buildNoCabMessage(String message, String positiveBtn) {

        if (mainAct.noCabAvailAlertBox != null) {
            mainAct.noCabAvailAlertBox.closeAlertBox();
            mainAct.noCabAvailAlertBox = null;
        }

        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setCancelable(true);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();
            }
        });
        generateAlert.setContentMessage("", message);
        generateAlert.setPositiveBtn(positiveBtn);
        generateAlert.showAlertBox();

        mainAct.noCabAvailAlertBox = generateAlert;
    }

    public String getAppliedPromoCode() {
        return this.appliedPromoCode;
    }

    public void findRoute(String etaVal) {
        try {

            String originLoc = mainAct.getPickUpLocation().getLatitude() + "," + mainAct.getPickUpLocation().getLongitude();
            String destLoc = null;
            if (mainAct.destLocation != null) {
                destLoc = mainAct.getDestLocLatitude() + "," + mainAct.getDestLocLongitude();
            } else {
                destLoc = mainAct.getPickUpLocation().getLatitude() + "," + mainAct.getPickUpLocation().getLongitude();

            }

            mProgressBar.setIndeterminate(true);
            mProgressBar.setVisibility(View.VISIBLE);


            String serverKey = getResources().getString(R.string.google_api_get_address_from_location_serverApi);
            String url = "https://maps.googleapis.com/maps/api/directions/json?origin=" + originLoc + "&destination=" + destLoc + "&sensor=true&key=" + serverKey + "&language=" + generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY) + "&sensor=true";

            Utils.printLog("FindRoute", "::" + url);

            ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), url, true);
            exeWebServer.setLoaderConfig(getActContext(), false, generalFunc);
            exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
                @Override
                public void setResponse(String responseString) {

                    mProgressBar.setIndeterminate(false);
                    mProgressBar.setVisibility(View.INVISIBLE);

                    if (responseString != null && !responseString.equals("")) {
                        String status = generalFunc.getJsonValue("status", responseString);
                        if (status.equals("OK")) {
                            isRouteFail = false;

                            JSONArray obj_routes = generalFunc.getJsonArray("routes", responseString);
                            if (obj_routes != null && obj_routes.length() > 0) {
                                JSONObject obj_legs = generalFunc.getJsonObject(generalFunc.getJsonArray("legs", generalFunc.getJsonObject(obj_routes, 0).toString()), 0);


                                distance = "" + (generalFunc.parseDoubleValue(0, generalFunc.getJsonValue("value",
                                        generalFunc.getJsonValue("distance", obj_legs.toString()).toString())));

                                time = "" + (generalFunc.parseDoubleValue(0, generalFunc.getJsonValue("value",
                                        generalFunc.getJsonValue("duration", obj_legs.toString()).toString())));

                                sourceLocation = new LatLng(generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("lat", generalFunc.getJsonValue("start_location", obj_legs.toString()))),
                                        generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("lng", generalFunc.getJsonValue("start_location", obj_legs.toString()))));

                                destLocation = new LatLng(generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("lat", generalFunc.getJsonValue("end_location", obj_legs.toString()))),
                                        generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("lng", generalFunc.getJsonValue("end_location", obj_legs.toString()))));

                                if (getActivity() != null) {
                                    estimateFare(distance, time);
                                }

                                //temp animation test


                                handleMapAnimation(responseString, sourceLocation, destLocation, etaVal);
                            }

                        } else {


                            isRouteFail = true;
                            if (!isSkip)

                            {

                                GenerateAlertBox alertBox = new GenerateAlertBox(getActContext());
                                alertBox.setContentMessage("", generalFunc.retrieveLangLBl("Route not found", "LBL_DEST_ROUTE_NOT_FOUND"));
                                alertBox.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
                                alertBox.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                                    @Override
                                    public void handleBtnClick(int btn_id) {
                                        alertBox.closeAlertBox();
                                        mainAct.userLocBtnImgView.performClick();

                                    }
                                });
                                alertBox.showAlertBox();

                            }

                            if (isSkip) {
                                isRouteFail = false;
                                if (mainAct.destLocation != null && mainAct.pickUpLocation != null) {
                                    handleMapAnimation(responseString, new LatLng(mainAct.pickUpLocation.getLatitude(), mainAct.pickUpLocation.getLongitude()), new LatLng(mainAct.destLocation.getLatitude(), mainAct.destLocation.getLongitude()), "--");
                                }
                            } else {
                                mainAct.userLocBtnImgView.performClick();
                            }
//
                            isSkip = true;
                            if (getActivity() != null) {
                                estimateFare(null, null);
                            }

//                            if (mainAct.destLocation != null) {
//                                ride_now_btn.setEnabled(false);
//                                ride_now_btn.setTextColor(Color.parseColor("#BABABA"));
//                                ride_now_btn.setClickable(false);
//                            }
                        }

                    }
                }
            });
            exeWebServer.execute();
        } catch (Exception e) {

        }
    }

    View marker_view;
    MTextView addressTxt, etaTxt;


    public void setEta(String time) {
        if (etaTxt != null) {
            etaTxt.setText(time);
        }


    }


    public void mangeMrakerPostion() {
        try {

            if (mainAct.pickUpLocation != null) {
                Point PickupPoint = mainAct.getMap().getProjection().toScreenLocation(new LatLng(mainAct.pickUpLocation.getLatitude(), mainAct.pickUpLocation.getLongitude()));
                Utils.printLog("PickupPoint", "::" + "screen:" + (width - PickupPoint.x) + "x,y::" + PickupPoint + " ::" + Utils.dpToPx(getActContext(), 200));
                //pickup
                if (sourceMarker != null) {
                    sourceMarker.setAnchor(PickupPoint.x < Utils.dpToPx(getActContext(), 200) ? 0.00f : 1.00f, PickupPoint.y < Utils.dpToPx(getActContext(), 100) ? 0.20f : 1.20f);
                }
            }
            if (destLocation != null) {
                Point DestinationPoint = mainAct.getMap().getProjection().toScreenLocation(destLocation);
                //dest
                if (destMarker != null) {
                    destMarker.setAnchor(DestinationPoint.x < Utils.dpToPx(getActContext(), 200) ? 0.00f : 1.00f, DestinationPoint.y < Utils.dpToPx(getActContext(), 100) ? 0.20f : 1.20f);
                }
            }
        } catch (Exception e) {

        }


    }


    public void handleSourceMarker(String etaVal) {
        if (!isSkip) {
            if (mainAct.pickUpLocation == null) {
                return;
            }
        }


        Utils.printLog("HandleMarker", "called");
        if (marker_view == null) {
            marker_view = ((LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE))
                    .inflate(R.layout.custom_marker, null);
            addressTxt = (MTextView) marker_view
                    .findViewById(R.id.addressTxt);
            etaTxt = (MTextView) marker_view.findViewById(R.id.etaTxt);
        }

        addressTxt.setTextColor(getActContext().getResources().getColor(R.color.sourceAddressTxt));

        LatLng fromLnt;
        if (isSkip) {
            estimateFare(null, null);
            if (destMarker != null) {
                destMarker.remove();
            }
            if (destDotMarker != null) {
                destDotMarker.remove();
            }
            if (route_polyLine != null) {
                route_polyLine.remove();
            }

            destLocation = null;
            mainAct.destLocation = null;

            fromLnt = new LatLng(mainAct.pickUpLocation.getLatitude(), mainAct.pickUpLocation.getLongitude());

        } else {
            fromLnt = new LatLng(mainAct.pickUpLocation.getLatitude(), mainAct.pickUpLocation.getLongitude());

            if (sourceLocation != null) {
                fromLnt = sourceLocation;
            }


        }

        Utils.printLog("fromLnt", ":::" + fromLnt);
        etaTxt.setVisibility(View.VISIBLE);
        etaTxt.setText(etaVal);

        if (sourceMarker != null) {
            sourceMarker.remove();
        }

        if (source_dot_option != null) {
            sourceDotMarker.remove();
        }

        source_dot_option = new MarkerOptions().position(fromLnt).icon(BitmapDescriptorFactory.fromResource(R.mipmap.dot));

        if (mainAct.getMap() != null) {
            sourceDotMarker = mainAct.getMap().addMarker(source_dot_option);
        }
        addressTxt.setText(mainAct.pickUpLocationAddress);
        MarkerOptions marker_opt_source = new MarkerOptions().position(fromLnt).icon(BitmapDescriptorFactory.fromBitmap(createDrawableFromView(getActContext(), marker_view))).anchor(0.00f, 0.20f);
        if (mainAct.getMap() != null) {
            sourceMarker = mainAct.getMap().addMarker(marker_opt_source);
            sourceMarker.setTag("1");
        }

        Utils.printLog("isSkip", "::" + isSkip);
        if (isSkip) {
            if (mainAct.getMap() != null) {
                CameraPosition cameraPosition = new CameraPosition.Builder().target(
                        new LatLng(mainAct.pickUpLocation.getLatitude(), mainAct.pickUpLocation.getLongitude()))
                        .zoom(Utils.defaultZomLevel).build();
                mainAct.getMap().moveCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
            }
        }


    }


    public void handleMapAnimation(String responseString, LatLng sourceLocation, LatLng destLocation, String etaVal) {


        //    mainAct.getMap().clear();
        if (mainAct.cabSelectionFrag == null) {
            Utils.printLog("handleMapAnimation", "cabselfrag null");
            return;
        }
        MapAnimator.getInstance().stopRouteAnim();
        Utils.printLog("findRoute", ":: handleMapAnimation Called");


        LatLng fromLnt = new LatLng(sourceLocation.latitude, sourceLocation.longitude);
        LatLng toLnt = new LatLng(destLocation.latitude, destLocation.longitude);


        if (marker_view == null) {

            marker_view = ((LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE))
                    .inflate(R.layout.custom_marker, null);
            addressTxt = (MTextView) marker_view
                    .findViewById(R.id.addressTxt);
            etaTxt = (MTextView) marker_view.findViewById(R.id.etaTxt);
        }

        addressTxt.setTextColor(getActContext().getResources().getColor(R.color.destAddressTxt));


        addressTxt.setText(mainAct.destAddress);

        MarkerOptions marker_opt_dest = new MarkerOptions().position(toLnt);
        etaTxt.setVisibility(View.GONE);

        marker_opt_dest.icon(BitmapDescriptorFactory.fromBitmap(createDrawableFromView(getActContext(), marker_view))).anchor(0.00f, 0.20f);
        if (dest_dot_option != null) {
            destDotMarker.remove();
        }
        dest_dot_option = new MarkerOptions().position(toLnt).icon(BitmapDescriptorFactory.fromResource(R.mipmap.dot));
        destDotMarker = mainAct.getMap().addMarker(dest_dot_option);

        if (destMarker != null) {
            destMarker.remove();
        }
        destMarker = mainAct.getMap().addMarker(marker_opt_dest);
        destMarker.setTag("2");
        LatLngBounds.Builder builder = new LatLngBounds.Builder();
        builder.include(fromLnt);
        builder.include(toLnt);


        handleSourceMarker(etaVal);

        JSONArray obj_routes1 = generalFunc.getJsonArray("routes", responseString);


        if (obj_routes1 != null && obj_routes1.length() > 0) {
            PolylineOptions lineOptions = getGoogleRouteOptions(responseString, Utils.dipToPixels(getActContext(), 5), getActContext().getResources().getColor(android.R.color.black));

            if (lineOptions != null) {
                if (route_polyLine != null) {
                    Utils.printLog("route_polyLine", "notnull");
                    route_polyLine.remove();
                    route_polyLine = null;

                }
                route_polyLine = mainAct.getMap().addPolyline(lineOptions);
                route_polyLine.remove();
            }
        }

        Utils.printLog("handleSourceMarker", "cabsel");


        DisplayMetrics metrics = new DisplayMetrics();
        mainAct.getWindowManager().getDefaultDisplay().getMetrics(metrics);
        int width = metrics.widthPixels;
        mainAct.getMap().moveCamera(CameraUpdateFactory.newLatLngBounds(builder.build(), width - Utils.dpToPx(getActContext(), 80), metrics.heightPixels - Utils.dipToPixels(getActContext(), 300), 0));

        if (route_polyLine != null && route_polyLine.getPoints().size() > 1) {
            MapAnimator.getInstance().animateRoute(mainAct.getMap(), route_polyLine.getPoints(), getActContext());
        }

        mainAct.getMap().setOnCameraMoveListener(new GoogleMap.OnCameraMoveListener() {
            @Override
            public void onCameraMove() {

                DisplayMetrics displaymetrics = new DisplayMetrics();
                mainAct.getWindowManager().getDefaultDisplay().getMetrics(displaymetrics);
                int height = displaymetrics.heightPixels;
                int width = displaymetrics.widthPixels;


            }
        });


//        mainAct.getMap().setOnMarkerClickListener(new GoogleMap.OnMarkerClickListener() {
//            @Override
//            public boolean onMarkerClick(Marker marker) {
//                if (marker == null) {
//                    return false;
//                }
//
//                if (marker.getTag().equals("1")) {
//                    if (mainAct.mainHeaderFrag != null) {
//                        mainAct.mainHeaderFrag.pickupLocArea1.performClick();
//                    }
//
//                } else if (marker.getTag().equals("2")) {
//                    if (mainAct.mainHeaderFrag != null) {
//                        mainAct.mainHeaderFrag.destarea.performClick();
//                    }
//
//                }
//
//                return false;
//            }
//        });


        if (mainAct.loadAvailCabs != null) {
            mainAct.loadAvailCabs.changeCabs();
        }


    }

    public static Bitmap createDrawableFromView(Context context, View view) {
        DisplayMetrics displayMetrics = new DisplayMetrics();
        ((Activity) context).getWindowManager().getDefaultDisplay().getMetrics(displayMetrics);
        view.setLayoutParams(new RelativeLayout.LayoutParams(RelativeLayout.LayoutParams.WRAP_CONTENT, RelativeLayout.LayoutParams.WRAP_CONTENT));
        view.measure(displayMetrics.widthPixels, displayMetrics.heightPixels);
        view.layout(0, 0, displayMetrics.widthPixels, displayMetrics.heightPixels);
        view.buildDrawingCache();
        Bitmap bitmap = Bitmap.createBitmap(view.getMeasuredWidth(), view.getMeasuredHeight(), Bitmap.Config.ARGB_8888);

        Canvas canvas = new Canvas(bitmap);
        view.draw(canvas);

        return bitmap;
    }

    public PolylineOptions getGoogleRouteOptions(String directionJson, int width, int color) {
        PolylineOptions lineOptions = new PolylineOptions();

        try {
            DirectionsJSONParser parser = new DirectionsJSONParser();
            List<List<HashMap<String, String>>> routes_list = parser.parse(new JSONObject(directionJson));

            ArrayList<LatLng> points = new ArrayList<LatLng>();

            if (routes_list.size() > 0) {
                // Fetching i-th route
                List<HashMap<String, String>> path = routes_list.get(0);

                // Fetching all the points in i-th route
                for (int j = 0; j < path.size(); j++) {
                    HashMap<String, String> point = path.get(j);

                    double lat = Double.parseDouble(point.get("lat"));
                    double lng = Double.parseDouble(point.get("lng"));
                    LatLng position = new LatLng(lat, lng);

                    points.add(position);

                }

                lineOptions.addAll(points);
                lineOptions.width(width);
                lineOptions.color(color);

                return lineOptions;
            } else {
                return null;
            }
        } catch (Exception e) {
            return null;
        }
    }

    public String getAvailableCarTypesIds() {
        String carTypesIds = "";
        for (int i = 0; i < mainAct.cabTypesArrList.size(); i++) {
            String iVehicleTypeId = mainAct.cabTypesArrList.get(i).get("iVehicleTypeId");

            carTypesIds = carTypesIds.equals("") ? iVehicleTypeId : (carTypesIds + "," + iVehicleTypeId);
        }
        return carTypesIds;
    }

    public void estimateFare(final String distance, String time) {


        //  loaderView.setVisibility(View.VISIBLE);

        if (estimateFareTask != null) {
            estimateFareTask.cancel(true);
            estimateFareTask = null;
        }
        if (distance == null && time == null) {
            //  mainAct.noCabAvail = false;
            // isroutefound = false;

        } else {
            if (mainAct.loadAvailCabs != null) {
                if (mainAct.loadAvailCabs.isAvailableCab) {
                    isroutefound = true;
                    if (!mainAct.timeval.equalsIgnoreCase("\n" + "--")) {
                        mainAct.noCabAvail = false;
                    }
                }
            }

        }

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "estimateFareNew");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("SelectedCarTypeID", getAvailableCarTypesIds());
        if (distance != null && time != null) {
            parameters.put("distance", distance);
            parameters.put("time", time);
        }
        parameters.put("SelectedCar", mainAct.getSelectedCabTypeId());
        parameters.put("PromoCode", getAppliedPromoCode());

        if (mainAct.getPickUpLocation() != null) {
            parameters.put("StartLatitude", "" + mainAct.getPickUpLocation().getLatitude());
            parameters.put("EndLongitude", "" + mainAct.getPickUpLocation().getLongitude());
        }

        if (mainAct.getDestLocLatitude() != null && !mainAct.getDestLocLatitude().equalsIgnoreCase("")) {
            parameters.put("DestLatitude", "" + mainAct.getDestLocLatitude());
            parameters.put("DestLongitude", "" + mainAct.getDestLocLongitude());
        }

        Utils.printLog("PromoCode", "::" + getAppliedPromoCode());

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        estimateFareTask = exeWebServer;
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        JSONArray vehicleTypesArr = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);
                        for (int i = 0; i < vehicleTypesArr.length(); i++) {

                            JSONObject obj_temp = generalFunc.getJsonObject(vehicleTypesArr, i);

                            if (distance != null) {
                                if (generalFunc.getJsonValue("eType", obj_temp.toString()).
                                        contains(mainAct.getCurrentCabGeneralType())) {


                                    if (cabTypeList != null) {
                                        for (int k = 0; k < cabTypeList.size(); k++) {
                                            HashMap<String, String> map = cabTypeList.get(k);

                                            if (/*map.get("vVehicleType").equalsIgnoreCase(generalFunc.getJsonValue("vVehicleType", obj_temp.toString()))
                                                    && */map.get("iVehicleTypeId").equalsIgnoreCase(generalFunc.getJsonValue("iVehicleTypeId", obj_temp.toString()))) {

                                                String totalfare = generalFunc.getJsonValue("total_fare", obj_temp.toString());
                                                if (!totalfare.equals("") && totalfare != null) {
                                                    map.put("total_fare", totalfare);
                                                    map.put("eFlatTrip", generalFunc.getJsonValue("eFlatTrip", obj_temp.toString()));
                                                    cabTypeList.set(k, map);
                                                } else {
                                                    map.put("eFlatTrip", generalFunc.getJsonValue("eFlatTrip", obj_temp.toString()));
                                                    cabTypeList.set(k, map);
                                                }
                                            } else {

                                            }

                                        }
                                    }


                                }
                            } else {
                                if (generalFunc.getJsonValue("eType", obj_temp.toString()).equalsIgnoreCase(mainAct.getCurrentCabGeneralType())) {

                                    for (int k = 0; k < cabTypeList.size(); k++) {
                                        HashMap<String, String> map = cabTypeList.get(k);


                                        if (/*map.get("vVehicleType").equalsIgnoreCase(generalFunc.getJsonValue("vVehicleType", obj_temp.toString()))
                                                &&*/ map.get("iVehicleTypeId").equalsIgnoreCase(generalFunc.getJsonValue("iVehicleTypeId", obj_temp.toString()))) {
                                            map.put("total_fare", "");
                                            cabTypeList.set(k, map);

                                            Utils.printELog("cabTypeList", ":: " + cabTypeList);
                                        }
                                    }
                                }
                            }

//                            if (generalFunc.getJsonValue("eFlatTrip", responseString).equalsIgnoreCase("Yes")) {
//                                mainAct.isFixFare = true;
//                            } else {
//                                mainAct.isFixFare = false;
//
//                            }

                        }
                        adapter.notifyDataSetChanged();
                    }
                }
            }
        });
        exeWebServer.execute();
    }

    public void openFareDetailsDilaog(final int pos) {

        // if (cabTypeList.get(pos).get("total_fare") != null && !cabTypeList.get(pos).get("total_fare").equalsIgnoreCase("")) {
        if (cabTypeList.get(pos).get("total_fare") != null) {
            String vehicleIconPath = CommonUtilities.SERVER_URL + "webimages/icons/VehicleType/";
            String vehicleDefaultIconPath = CommonUtilities.SERVER_URL + "webimages/icons/DefaultImg/";
            final BottomSheetDialog faredialog = new BottomSheetDialog(getActContext());

            View contentView = View.inflate(getContext(), R.layout.dailog_faredetails, null);
            faredialog.setContentView(contentView);
            BottomSheetBehavior mBehavior = BottomSheetBehavior.from((View) contentView.getParent());
            mBehavior.setPeekHeight(1500);
            View bottomSheetView = faredialog.getWindow().getDecorView().findViewById(android.support.design.R.id.design_bottom_sheet);
            BottomSheetBehavior.from(bottomSheetView).setHideable(false);
            setCancelable(faredialog, false);

            ImageView imagecar;
            final MTextView carTypeTitle, capacityHTxt, capacityVTxt, fareHTxt, fareVTxt, mordetailsTxt, farenoteTxt;
            MButton btn_type2;
            int submitBtnId;
            imagecar = (ImageView) faredialog.findViewById(R.id.imagecar);
            carTypeTitle = (MTextView) faredialog.findViewById(R.id.carTypeTitle);
            capacityHTxt = (MTextView) faredialog.findViewById(R.id.capacityHTxt);
            capacityVTxt = (MTextView) faredialog.findViewById(R.id.capacityVTxt);
            fareHTxt = (MTextView) faredialog.findViewById(R.id.fareHTxt);
            fareVTxt = (MTextView) faredialog.findViewById(R.id.fareVTxt);
            mordetailsTxt = (MTextView) faredialog.findViewById(R.id.mordetailsTxt);
            farenoteTxt = (MTextView) faredialog.findViewById(R.id.farenoteTxt);
            btn_type2 = ((MaterialRippleLayout) faredialog.findViewById(R.id.btn_type2)).getChildView();
            submitBtnId = Utils.generateViewId();
            btn_type2.setId(submitBtnId);


            capacityHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_CAPACITY"));
            fareHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_FARE_TXT"));
            mordetailsTxt.setText(generalFunc.retrieveLangLBl("", "LBL_MORE_DETAILS"));

            if (mainAct.isFixFare) {
                farenoteTxt.setText(generalFunc.retrieveLangLBl("", "LBL_GENERAL_NOTE_FLAT_FARE_EST"));
            } else {
                farenoteTxt.setText(generalFunc.retrieveLangLBl("", "LBL_GENERAL_NOTE_FARE_EST"));
            }
            btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_DONE"));


            carTypeTitle.setText(cabTypeList.get(pos).get("vVehicleType"));
            if (cabTypeList.get(pos).get("total_fare") != null && !cabTypeList.get(pos).get("total_fare").equalsIgnoreCase("")) {
                fareVTxt.setText(generalFunc.convertNumberWithRTL(cabTypeList.get(pos).get("total_fare")));
            } else {
                fareVTxt.setText("--");
            }
            if (mainAct.getCurrentCabGeneralType().equals(Utils.CabGeneralType_Ride)) {
                capacityVTxt.setText(generalFunc.convertNumberWithRTL(cabTypeList.get(pos).get("iPersonSize")) + " " + generalFunc.retrieveLangLBl("", "LBL_PEOPLE_TXT"));

            } else {
                capacityVTxt.setText("---");
            }

            String imgName = cabTypeList.get(pos).get("vLogo1");
            if (imgName.equals("")) {
                imgName = vehicleDefaultIconPath + "hover_ic_car.png";
            } else {
                imgName = vehicleIconPath + cabTypeList.get(pos).get("iVehicleTypeId") + "/android/" + "xxxhdpi_" +
                        cabTypeList.get(pos).get("vLogo1");

            }

            Picasso.with(getActContext())
                    .load(imgName)
                    .into(imagecar, new com.squareup.picasso.Callback() {
                        @Override
                        public void onSuccess() {

                        }

                        @Override
                        public void onError() {
                        }
                    });

            Utils.printLog("Imagpath", "::" + vehicleIconPath + cabTypeList.get(pos).get("iVehicleTypeId")
                    + "/android/" + "xxxhdpi_" + cabTypeList.get(pos).get("vLogo"));


            btn_type2.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    faredialog.dismiss();

                }
            });

            mordetailsTxt.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
//                    dialogShowOnce = true;
                    Bundle bn = new Bundle();
                    bn.putString("SelectedCar", cabTypeList.get(pos).get("iVehicleTypeId"));
                    bn.putString("iUserId", generalFunc.getMemberId());
                    bn.putString("distance", distance);
                    bn.putString("time", time);
                    bn.putString("PromoCode", appliedPromoCode);
                    bn.putString("vVehicleType", cabTypeList.get(pos).get("vVehicleType"));
                    bn.putBoolean("isSkip", isSkip);
                    if (mainAct.getPickUpLocation() != null) {
                        bn.putString("picupLat", mainAct.getPickUpLocation().getLatitude() + "");
                        bn.putString("pickUpLong", mainAct.getPickUpLocation().getLongitude() + "");
                    }
                    if (mainAct.destLocation != null) {
                        bn.putString("destLat", mainAct.destLocLatitude + "");
                        bn.putString("destLong", mainAct.destLocLongitude + "");
                    }
                    if (mainAct.isFixFare) {
                        bn.putBoolean("isFixFare", true);
                    } else {
                        bn.putBoolean("isFixFare", false);
                    }

                    new StartActProcess(getActContext()).startActWithData(FareBreakDownActivity.class, bn);
                    faredialog.dismiss();
                }
            });


            faredialog.setOnDismissListener(new DialogInterface.OnDismissListener() {
                @Override
                public void onDismiss(DialogInterface dialog) {
                }
            });
            faredialog.show();
        }


    }

    public void setCancelable(Dialog dialogview, boolean cancelable) {
        final Dialog dialog = dialogview;
        View touchOutsideView = dialog.getWindow().getDecorView().findViewById(android.support.design.R.id.touch_outside);
        View bottomSheetView = dialog.getWindow().getDecorView().findViewById(android.support.design.R.id.design_bottom_sheet);

        if (cancelable) {
            touchOutsideView.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (dialog.isShowing()) {
                        dialog.cancel();
                    }
                }
            });
            BottomSheetBehavior.from(bottomSheetView).setHideable(true);
        } else {
            touchOutsideView.setOnClickListener(null);
            BottomSheetBehavior.from(bottomSheetView).setHideable(false);
        }
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();

        releseInstances();
    }

    private void releseInstances() {
        Utils.hideKeyboard(getActContext());
        if (estimateFareTask != null) {
            estimateFareTask.cancel(true);
            estimateFareTask = null;
        }
    }

    public void Checkpickupdropoffrestriction() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "Checkpickupdropoffrestriction");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("PickUpLatitude", "" + mainAct.getPickUpLocation().getLatitude());
        parameters.put("PickUpLongitude", "" + mainAct.getPickUpLocation().getLongitude());
        parameters.put("DestLatitude", mainAct.getDestLocLatitude());
        parameters.put("DestLongitude", mainAct.getDestLocLongitude());
        parameters.put("UserType", Utils.userType);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
                if (responseString != null && !responseString.equals("")) {
                    if (generalFunc.getJsonValue("Action", responseString).equalsIgnoreCase("0")) {
                        if (message.equalsIgnoreCase("LBL_DROP_LOCATION_NOT_ALLOW")) {
                            generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_DROP_LOCATION_NOT_ALLOW"));
                        } else if (message.equalsIgnoreCase("LBL_PICKUP_LOCATION_NOT_ALLOW")) {
                            generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_PICKUP_LOCATION_NOT_ALLOW"));
                        }
                    } else if (generalFunc.getJsonValue("Action", responseString).equalsIgnoreCase("1")) {
                        mainAct.continueDeliveryProcess();
                    }

                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    @Override
    public void onDestroy() {
        super.onDestroy();

        releseInstances();
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(getActContext());
            if (i == R.id.minFareArea) {
                openFareEstimateDialog();
            } else if (i == ride_now_btn.getId()) {

                if (mProgressBar.getVisibility() == View.VISIBLE) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Route not found", "LBL_DEST_ROUTE_NOT_FOUND"));
                    return;
                }
                if ((mainAct.currentLoadedDriverList != null && mainAct.currentLoadedDriverList.size() < 1) || mainAct.currentLoadedDriverList == null || (cabTypeList != null && cabTypeList.size() < 1) || cabTypeList == null) {

                    buildNoCabMessage(generalFunc.retrieveLangLBl("", "LBL_NO_CARS_AVAIL_IN_TYPE"),
                            generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
                    return;
                }

                if (isRouteFail) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Route not found", "LBL_DEST_ROUTE_NOT_FOUND"));
                    return;
                }


                if (!ridenowclick) {

                    mainAct.setCabReqType(Utils.CabReqType_Now);
//                    if (mainAct.getDestinationStatus()) {
//                        String destLocAdd = mainAct != null ? (mainAct.getDestAddress().equals(
//                                generalFunc.retrieveLangLBl("", "LBL_SELECTING_LOCATION_TXT")) ? "" : mainAct.getDestAddress()) : "";
//                        if (destLocAdd.equals("")) {
//                            return;
//                        }
//                    }

                    if (isCardValidated == false && generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Card")) {
                        isCardnowselcted = true;
                        isCardlaterselcted = false;
                        checkCardConfig();
                        return;
                    }


                    if (mainAct.isDeliver(mainAct.getCurrentCabGeneralType())) {
                        if (mainAct.getDestinationStatus() == false) {
                            generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Please add your destination location " +
                                    "to deliver your package.", "LBL_ADD_DEST_MSG_DELIVER_ITEM"));
                            return;
                        }
                        Checkpickupdropoffrestriction();
                        // mainAct.setDeliverySchedule();
                        return;
                    }

                    ridenowclick = true;


                    if (!mainAct.getCabReqType().equals(Utils.CabReqType_Later)) {
                        //  mainAct.requestPickUp();
                        ride_now_btn.setEnabled(false);
                        ride_now_btn.setClickable(false);
                        mainAct.continuePickUpProcess();
                    } else {
                        ride_now_btn.setEnabled(false);
                        ride_now_btn.setClickable(false);
                        mainAct.setRideSchedule();
                    }

                    Handler handler = new Handler();
                    handler.postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            ridenowclick = false;
                        }
                    }, 500);
                }
            } else if (i == img_ridelater.getId()) {

                if (mProgressBar.getVisibility() == View.VISIBLE) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Route not found", "LBL_DEST_ROUTE_NOT_FOUND"));
                    return;
                }


                if (mainAct.destAddress == null || mainAct.destAddress.equalsIgnoreCase("")) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Destination is required to create scheduled booking.", "LBL_DEST_REQ_FOR_LATER"));

                    return;
                }

                if (isRouteFail) {
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Route not found", "LBL_DEST_ROUTE_NOT_FOUND"));
                    return;
                }


                if (!ridelaterclick) {
                    ridelaterclick = true;
                    if (cabTypeList.size() > 0) {
                        if (isCardValidated == false && generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Card")) {
                            isCardlaterselcted = true;
                            isCardnowselcted = false;
                            checkCardConfig();
                            return;
                        }
                        ride_now_btn.setEnabled(false);
                        ride_now_btn.setTextColor(Color.parseColor("#BABABA"));
                        ride_now_btn.setClickable(false);
                        mainAct.chooseDateTime();
                    }
                    Handler handler = new Handler();
                    handler.postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            ridelaterclick = false;
                        }
                    }, 200);
                }
            } else if (i == R.id.paymentArea) {

                if (payTypeSelectArea.getVisibility() == View.VISIBLE) {
                    hidePayTypeSelectionArea();
                } else {
                    if (generalFunc.getJsonValue("APP_PAYMENT_MODE", userProfileJson).equalsIgnoreCase("Cash-Card")) {
                        mainAct.setPanelHeight(283);
                        payTypeSelectArea.setVisibility(View.VISIBLE);
                        cashcardarea.setVisibility(View.GONE);
                    } else {
                        mainAct.setPanelHeight(283 - 48);
                    }
                }

            } else if (i == R.id.promoArea) {
                showPromoBox();
            } else if (i == R.id.cardarea) {
                hidePayTypeSelectionArea();
                setCashSelection();
                checkCardConfig();
                //   }

            } else if (i == R.id.casharea) {
                hidePayTypeSelectionArea();
                setCashSelection();
            }
        }
    }
}
