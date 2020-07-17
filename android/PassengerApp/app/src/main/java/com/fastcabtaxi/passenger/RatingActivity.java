package com.fastcabtaxi.passenger;

import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.CardView;
import android.text.InputType;
import android.util.DisplayMetrics;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TableLayout;
import android.widget.TableRow;

import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.ErrorView;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;
import com.view.simpleratingbar.SimpleRatingBar;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;

public class RatingActivity extends AppCompatActivity {

    String vehicleIconPath = CommonUtilities.SERVER_URL + "webimages/icons/VehicleType/";

    MTextView titleTxt;
    ImageView backImgView;

    GeneralFunctions generalFunc;

    ProgressBar loading;
    ErrorView errorView;
    MButton btn_type2;
    MTextView generalCommentTxt;
    CardView generalCommentArea;
    MaterialEditText commentBox;

    int submitBtnId;

    LinearLayout container;

    SimpleRatingBar ratingBar;
    String iTripId_str;
    LinearLayout uberXRatingLayoutArea, mainRatingArea;
    android.support.v7.app.AlertDialog giveTipAlertDialog;

    MTextView totalFareTxt;
    MTextView dateVTxt;
    MTextView promoAppliedVTxt;
    MTextView ratingHeaderTxt;
    float rating = 0;

    String tipamount = "";
    boolean isCollectTip = false;

    boolean isUfx = false;
    boolean isFirst = false;

    MTextView fareHeadrtxt;
    ImageView fareindicatorImg;
    LinearLayout farecontainer;
    View convertView = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_rating);

        generalFunc = new GeneralFunctions(getActContext());
        Utils.ClerAllNotification();
        isUfx = getIntent().getBooleanExtra("isUfx", false);
        isFirst = getIntent().getBooleanExtra("isFirst", false);


        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        backImgView.setOnClickListener(new setOnClickList());
        loading = (ProgressBar) findViewById(R.id.loading);
        errorView = (ErrorView) findViewById(R.id.errorView);
        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        commentBox = (MaterialEditText) findViewById(R.id.commentBox);
        generalCommentTxt = (MTextView) findViewById(R.id.generalCommentTxt);
        generalCommentArea = (CardView) findViewById(R.id.generalCommentArea);
        container = (LinearLayout) findViewById(R.id.container);
        ratingBar = (SimpleRatingBar) findViewById(R.id.ratingBar);
        fareHeadrtxt = (MTextView) findViewById(R.id.fareHeadrtxt);
        farecontainer = (LinearLayout) findViewById(R.id.farecontainer);
        fareindicatorImg = (ImageView) findViewById(R.id.fareindicatorImg);
        fareindicatorImg.setOnClickListener(new setOnClickList());
        fareHeadrtxt.setOnClickListener(new setOnClickList());


        uberXRatingLayoutArea = (LinearLayout) findViewById(R.id.uberXRatingLayoutArea);
        mainRatingArea = (LinearLayout) findViewById(R.id.mainRatingArea);

        totalFareTxt = (MTextView) findViewById(R.id.totalFareTxt);
        dateVTxt = (MTextView) findViewById(R.id.dateVTxt);
        promoAppliedVTxt = (MTextView) findViewById(R.id.promoAppliedVTxt);
        ratingHeaderTxt = (MTextView) findViewById(R.id.ratingHeaderTxt);

        submitBtnId = Utils.generateViewId();
        btn_type2.setId(submitBtnId);

        btn_type2.setOnClickListener(new setOnClickList());
        if (!isUfx) {
            backImgView.setVisibility(View.GONE);
        } else {
            //getDetails();
            backImgView.setVisibility(View.VISIBLE);
        }
        setLabels();

        getFare();

        LinearLayout.LayoutParams params = (LinearLayout.LayoutParams) titleTxt.getLayoutParams();
        params.setMargins(Utils.dipToPixels(getActContext(), 15), 0, 0, 0);
        titleTxt.setLayoutParams(params);


        commentBox.setSingleLine(false);
        commentBox.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_FLAG_MULTI_LINE);
        commentBox.setGravity(Gravity.TOP);
        commentBox.setHideUnderline(true);
        commentBox.setFloatingLabel(MaterialEditText.FLOATING_LABEL_NONE);
    }

    public Context getActContext() {
        return RatingActivity.this;
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_RATING"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_SUBMIT_TXT"));
        commentBox.setHint(generalFunc.retrieveLangLBl("", "LBL_WRITE_COMMENT_HINT_TXT"));
        dateVTxt.setText(generalFunc.retrieveLangLBl("", "LBL_MYTRIP_Trip_Date"));
        promoAppliedVTxt.setText(generalFunc.retrieveLangLBl("", "LBL_DIS_APPLIED"));
        ratingHeaderTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOW_WAS_RIDE"));
        fareHeadrtxt.setText(generalFunc.retrieveLangLBl("Fare Details", "LBL_FARE_DETAILS"));

        totalFareTxt.setText(generalFunc.retrieveLangLBl("Total Fare", "LBL_Total_Fare"));

    }

    public void getFare() {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (container.getVisibility() == View.VISIBLE) {
            container.setVisibility(View.GONE);
        }
        if (loading.getVisibility() != View.VISIBLE) {
            loading.setVisibility(View.VISIBLE);
        }

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "displayFare");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        if (isUfx) {
            parameters.put("iTripId", getIntent().getStringExtra("iTripId"));
        }

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    closeLoader();
                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {

                        String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);

                        String FormattedTripDate = generalFunc.getJsonValue("tTripRequestDateOrig", message);
                        String TotalFare = generalFunc.getJsonValue("TotalFare", message);
                        String fDiscount = generalFunc.getJsonValue("fDiscount", message);
                        String vDriverImage = generalFunc.getJsonValue("vDriverImage", message);
                        String CurrencySymbol = generalFunc.getJsonValue("CurrencySymbol", message);
                        String iVehicleTypeId = generalFunc.getJsonValue("iVehicleTypeId", message);
                        String iDriverId = generalFunc.getJsonValue("iDriverId", message);
                        String tEndLat = generalFunc.getJsonValue("tEndLat", message);
                        String tEndLong = generalFunc.getJsonValue("tEndLong", message);
                        String eCancelled = generalFunc.getJsonValue("eCancelled", message);
                        String vCancelReason = generalFunc.getJsonValue("vCancelReason", message);
                        String vCancelComment = generalFunc.getJsonValue("vCancelComment", message);
                        String vCouponCode = generalFunc.getJsonValue("vCouponCode", message);
                        String carImageLogo = generalFunc.getJsonValue("carImageLogo", message);
                        String iTripId = generalFunc.getJsonValue("iTripId", message);
                        String eType = generalFunc.getJsonValue("eType", message);
                        iTripId_str = iTripId;


                        JSONArray FareDetailsArrNewObj = null;

                        FareDetailsArrNewObj = generalFunc.getJsonArray("FareDetailsNewArr", message);

                        addFareDetailLayout(FareDetailsArrNewObj);

                        ((MTextView) findViewById(R.id.dateTxt)).setText(generalFunc.getDateFormatedType(FormattedTripDate, Utils.OriginalDateFormate, Utils.DateFormatewithTime));
                        ((MTextView) findViewById(R.id.sourceAddressTxt)).setText(generalFunc.getJsonValue("tSaddress", message));
                        if (generalFunc.getJsonValue("tDaddress", message).equals("")) {
                            ((LinearLayout) findViewById(R.id.destarea)).setVisibility(View.GONE);
                            ((ImageView) findViewById(R.id.imagedest)).setVisibility(View.GONE);
                            ((View) findViewById(R.id.destdivideview)).setVisibility(View.GONE);

                        } else {
                            ((LinearLayout) findViewById(R.id.destarea)).setVisibility(View.VISIBLE);
                            ((ImageView) findViewById(R.id.imagedest)).setVisibility(View.VISIBLE);
                            ((View) findViewById(R.id.destdivideview)).setVisibility(View.VISIBLE);
                            ((MTextView) findViewById(R.id.destAddressTxt)).setText(generalFunc.getJsonValue("tDaddress", message));
                        }
                        ((MTextView) findViewById(R.id.carType)).setText(generalFunc.getJsonValue("carTypeName", message));
                        ((MTextView) findViewById(R.id.fareTxt)).setText(CurrencySymbol + " " + generalFunc.convertNumberWithRTL(TotalFare));

                        LinearLayout towTruckdestAddrArea = (LinearLayout) findViewById(R.id.towTruckdestAddrArea);

                        if (eType.equalsIgnoreCase("UberX")) {
                            uberXRatingLayoutArea.setVisibility(View.GONE);
                            mainRatingArea.setVisibility(View.VISIBLE);

                            new CreateRoundedView(Color.parseColor("#54A626"), Utils.dipToPixels(getActContext(), 9), 0, 0, findViewById(R.id.ufxPickArea));


                            ((MTextView) findViewById(R.id.sourceAddressTxt)).setText(generalFunc.getJsonValue("tSaddress", message));
                            ((MTextView) findViewById(R.id.carType)).setText(generalFunc.getJsonValue("carTypeName", message));

                            if (generalFunc.getJsonValue("APP_DESTINATION_MODE", message).equalsIgnoreCase("Strict") || generalFunc.getJsonValue("APP_DESTINATION_MODE", message).equalsIgnoreCase("NonStrict")) {

                                if (towTruckdestAddrArea.getVisibility() == View.GONE) {
                                    towTruckdestAddrArea.setVisibility(View.VISIBLE);
                                    ((MTextView) findViewById(R.id.destAddressTxt)).setText(generalFunc.getJsonValue("tDaddress", message));
                                }
                            }

                            setImages("", "", generalFunc.getJsonValue("vLogoVehicleCategoryPath", message), generalFunc.getJsonValue("vLogoVehicleCategory", message));

                        } else {

                            mainRatingArea.setVisibility(View.VISIBLE);
                            uberXRatingLayoutArea.setVisibility(View.GONE);

                            setImages(carImageLogo, iVehicleTypeId, "", "");

                        }


                        if (eType.equals("Deliver")) {
                            ratingHeaderTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOW_WAS_DELIVERY"));
                        } else {
                            ratingHeaderTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOW_WAS_RIDE"));
                        }
                        if (eCancelled.equals("Yes")) {
                            generalCommentTxt.setText(generalFunc.retrieveLangLBl("Trip is cancelled by driver. Reason:", "LBL_PREFIX_TRIP_CANCEL_DRIVER")
                                    + " " + vCancelReason);
                            generalCommentTxt.setVisibility(View.VISIBLE);
                            generalCommentArea.setVisibility(View.VISIBLE);
                        } else {
                            generalCommentTxt.setVisibility(View.GONE);
                            generalCommentArea.setVisibility(View.GONE);
                        }

                        if (!fDiscount.equals("") && !fDiscount.equals("0") && !fDiscount.equals("0.00")) {

                            ((MTextView) findViewById(R.id.promoAppliedTxt)).setText(CurrencySymbol + generalFunc.convertNumberWithRTL(fDiscount));

                            (findViewById(R.id.promoView)).setVisibility(View.VISIBLE);
                        } else {
                            ((MTextView) findViewById(R.id.promoAppliedTxt)).setText("--");

                        }

                        if (generalFunc.getJsonValue("ENABLE_TIP_MODULE", message).equalsIgnoreCase("Yes")) {
                            isCollectTip = true;


                        } else {
                            isCollectTip = false;
                        }


                        container.setVisibility(View.VISIBLE);
                    } else {
                        generateErrorView();
                    }
                } else {
                    generateErrorView();
                }
            }
        });
        exeWebServer.execute();
    }


    private void addFareDetailLayout(JSONArray jobjArray) {


        for (int i = 0; i < jobjArray.length(); i++) {
            JSONObject jobject = generalFunc.getJsonObject(jobjArray, i);
            try {
                addFareDetailRow(jobject.names().getString(0), jobject.get(jobject.names().getString(0)).toString());
            } catch (JSONException e) {
                e.printStackTrace();
            }
        }

    }

    public void setImages(String carImageLogo, String iVehicleTypeId, String vLogoVehicleCategoryPath, String vLogoVehicleCategory) {
        String imageName = "";
        String size = "";
        switch (getResources().getDisplayMetrics().densityDpi) {
            case DisplayMetrics.DENSITY_LOW:
                imageName = "mdpi_" + (carImageLogo.equals("") ? vLogoVehicleCategory : carImageLogo);
                size = "80";
                break;
            case DisplayMetrics.DENSITY_MEDIUM:
                imageName = "mdpi_" + (carImageLogo.equals("") ? vLogoVehicleCategory : carImageLogo);
                size = "80";
                break;
            case DisplayMetrics.DENSITY_HIGH:
                imageName = "hdpi_" + (carImageLogo.equals("") ? vLogoVehicleCategory : carImageLogo);
                size = "120";
                break;
            case DisplayMetrics.DENSITY_TV:
                imageName = "hdpi_" + (carImageLogo.equals("") ? vLogoVehicleCategory : carImageLogo);
                size = "120";
                break;
            case DisplayMetrics.DENSITY_XHIGH:
                imageName = "xhdpi_" + (carImageLogo.equals("") ? vLogoVehicleCategory : carImageLogo);
                size = "160";
                break;
            case DisplayMetrics.DENSITY_XXHIGH:
                imageName = "xxhdpi_" + (carImageLogo.equals("") ? vLogoVehicleCategory : carImageLogo);
                size = "240";
                break;
            case DisplayMetrics.DENSITY_XXXHIGH:
                imageName = "xxxhdpi_" + (carImageLogo.equals("") ? vLogoVehicleCategory : carImageLogo);
                size = "320";
                break;
        }

    }

    public void submitRating() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "submitRating");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("tripID", iTripId_str);
        parameters.put("rating", "" + ratingBar.getRating());
        parameters.put("message", Utils.getText(commentBox));
        parameters.put("UserType", CommonUtilities.app_type);

        parameters.put("fAmount", tipamount);
        if (isCollectTip) {
            parameters.put("isCollectTip", "Yes");
        } else {
            parameters.put("isCollectTip", "No");

        }


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        btn_type2.setEnabled(true);

                        showBookingAlert(generalFunc.retrieveLangLBl("", "LBL_TRIP_FINISHED_TXT"));
                    } else {
                        btn_type2.setEnabled(true);
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }
                } else {
                    btn_type2.setEnabled(true);
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }


    public void showBookingAlert(String message) {
        android.support.v7.app.AlertDialog alertDialog;
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle("");
        builder.setCancelable(false);
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.dialog_booking_view, null);
        builder.setView(dialogView);

        final MTextView titleTxt = (MTextView) dialogView.findViewById(R.id.titleTxt);
        final MTextView mesasgeTxt = (MTextView) dialogView.findViewById(R.id.mesasgeTxt);


        titleTxt.setText(generalFunc.retrieveLangLBl("Booking Successful", "LBL_SUCCESS_FINISHED"));
        mesasgeTxt.setText(message);


        builder.setPositiveButton(generalFunc.retrieveLangLBl("", "LBL_OK_THANKS"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();

                // generalFunc.restartApp();

                if (isFirst) {
                    generalFunc.restartwithGetDataApp();
                    return;
                }
                if (isUfx) {
                    onBackPressed();
                } else {
                    generalFunc.restartwithGetDataApp();
                }

            }
        });


        alertDialog = builder.create();
        alertDialog.setCancelable(false);
        alertDialog.setCanceledOnTouchOutside(false);
        alertDialog.show();

    }

    public void buildTipCollectMessage(String message, String positiveBtn, String negativeBtn) {

        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.desgin_add_tip_layout, null);
        builder.setView(dialogView);

        final MaterialEditText tipAmountEditBox = (MaterialEditText) dialogView.findViewById(R.id.editBox);
        tipAmountEditBox.setVisibility(View.GONE);
        final MTextView giveTipTxtArea = (MTextView) dialogView.findViewById(R.id.giveTipTxtArea);
        final MTextView msgTxt = (MTextView) dialogView.findViewById(R.id.msgTxt);
        msgTxt.setVisibility(View.VISIBLE);
        final MTextView skipTxtArea = (MTextView) dialogView.findViewById(R.id.skipTxtArea);
        final MTextView titileTxt = (MTextView) dialogView.findViewById(R.id.titileTxt);
        titileTxt.setText(generalFunc.retrieveLangLBl("", "LBL_TIP_TITLE_TXT"));
        msgTxt.setText(message);
        giveTipTxtArea.setText(negativeBtn);
        skipTxtArea.setText(positiveBtn);

        skipTxtArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //generalFunc.restartApp();
                giveTipAlertDialog.dismiss();
                //  generalFunc.restartwithGetDataApp();
                tipamount = 0 + "";

                btn_type2.setEnabled(false);
                submitRating();
                isCollectTip = false;
            }
        });

        giveTipTxtArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                giveTipAlertDialog.dismiss();
                showTipBox();

            }
        });
        giveTipAlertDialog = builder.create();
        giveTipAlertDialog.setCancelable(true);
        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(giveTipAlertDialog);
        }
        giveTipAlertDialog.show();
    }

    public void showTipBox() {
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.desgin_add_tip_layout, null);
        builder.setView(dialogView);

        final MaterialEditText tipAmountEditBox = (MaterialEditText) dialogView.findViewById(R.id.editBox);
        tipAmountEditBox.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_SIGNED | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        final MTextView giveTipTxtArea = (MTextView) dialogView.findViewById(R.id.giveTipTxtArea);
        final MTextView skipTxtArea = (MTextView) dialogView.findViewById(R.id.skipTxtArea);
        final MTextView titileTxt = (MTextView) dialogView.findViewById(R.id.titileTxt);
        titileTxt.setText(generalFunc.retrieveLangLBl("", "LBL_TIP_AMOUNT_ENTER_TITLE"));
        giveTipTxtArea.setText("" + generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));
        skipTxtArea.setText("" + generalFunc.retrieveLangLBl("", "LBL_SKIP_TXT"));

        skipTxtArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Utils.hideKeyboard(getActContext());
                giveTipAlertDialog.dismiss();
                btn_type2.setEnabled(false);
                submitRating();

            }
        });

        giveTipTxtArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                final boolean tipAmountEntered = Utils.checkText(tipAmountEditBox) ? true : Utils.setErrorFields(tipAmountEditBox, generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT"));
                if (tipAmountEntered == false) {
                    return;
                }
                Utils.hideKeyboard(getActContext());
                giveTipAlertDialog.dismiss();
                collectTip(Utils.getText(tipAmountEditBox));
                btn_type2.setEnabled(false);
                submitRating();


            }
        });
        giveTipAlertDialog = builder.create();
        giveTipAlertDialog.setCancelable(true);
        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(giveTipAlertDialog);
        }
        giveTipAlertDialog.show();

    }

    private void collectTip(String tipAmount) {


        tipamount = tipAmount;


    }


    public void closeLoader() {
        if (loading.getVisibility() == View.VISIBLE) {
            loading.setVisibility(View.GONE);
        }
    }

    public void generateErrorView() {

        closeLoader();

        generalFunc.generateErrorView(errorView, "LBL_ERROR_TXT", "LBL_NO_INTERNET_TXT");

        if (errorView.getVisibility() != View.VISIBLE) {
            errorView.setVisibility(View.VISIBLE);
        }
        errorView.setOnRetryListener(new ErrorView.RetryListener() {
            @Override
            public void onRetry() {
                getFare();
            }
        });
    }

    @Override
    public void onBackPressed() {

        if (isFirst) {
            generalFunc.restartwithGetDataApp();
            return;
        }
        if (isUfx) {
            super.onBackPressed();
        } else {
            return;
        }
    }

    @Override
    protected void onResume() {
        super.onResume();


    }

    @Override
    protected void onDestroy() {
        super.onDestroy();

    }

    private void addFareDetailRow(String row_name, String row_value) {
        LayoutInflater infalInflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = infalInflater.inflate(R.layout.design_fare_breakdown_row, null);
        TableRow FareDetailRow = (TableRow) convertView.findViewById(R.id.FareDetailRow);
        TableLayout fair_area_table_layout = (TableLayout) convertView.findViewById(R.id.fair_area);
        MTextView titleHTxt = (MTextView) convertView.findViewById(R.id.titleHTxt);
        MTextView titleVTxt = (MTextView) convertView.findViewById(R.id.titleVTxt);

        titleHTxt.setText(generalFunc.convertNumberWithRTL(row_name));
        titleVTxt.setText(generalFunc.convertNumberWithRTL(row_value));


        titleHTxt.setTextColor(Color.parseColor("#303030"));
        titleVTxt.setTextColor(Color.parseColor("#111111"));

        if (convertView != null)
            farecontainer.addView(convertView);
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(getActContext());
            if (i == submitBtnId) {
                if (ratingBar.getRating() < 0.5) {
                    generalFunc.showMessage(generalFunc.getCurrentView(RatingActivity.this), generalFunc.retrieveLangLBl("", "LBL_ERROR_RATING_DIALOG_TXT"));
                    return;
                }

                if (isCollectTip) {
                    buildTipCollectMessage(generalFunc.retrieveLangLBl("", "LBL_TIP_TXT"), generalFunc.retrieveLangLBl("No,Thanks", "LBL_NO_THANKS"),
                            generalFunc.retrieveLangLBl("Give Tip", "LBL_GIVE_TIP_TXT"));
                    return;
                } else {
                    btn_type2.setEnabled(false);
                    submitRating();
                }

            } else if (i == backImgView.getId()) {
                onBackPressed();
            } else if (i == fareindicatorImg.getId()) {
                fareHeadrtxt.performClick();


            } else if (i == fareHeadrtxt.getId()) {
                if (farecontainer.getVisibility() == View.GONE) {
                    fareindicatorImg.setImageResource(R.mipmap.ic_arrow_up);
                    farecontainer.setVisibility(View.VISIBLE);

                } else {
                    fareindicatorImg.setImageResource(R.mipmap.ic_arrow_down);
                    farecontainer.setVisibility(View.GONE);

                }
            }
        }
    }


}
