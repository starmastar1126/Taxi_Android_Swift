package com.general.files;

import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Color;
import android.support.v7.app.AlertDialog;
import android.text.InputType;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.LinearLayout;

import com.fastcabtaxi.driver.ActiveTripActivity;
import com.fastcabtaxi.driver.R;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;
import com.view.editBox.MaterialEditText;

import java.util.HashMap;

/**
 * Created by Admin on 21-07-2016.
 */
public class CancelTripDialog {

    Context mContext;
    GeneralFunctions generalFunc;
    boolean isTripStart = false;
    HashMap<String, String> data_trip;
    android.support.v7.app.AlertDialog alertDialog;

    public CancelTripDialog(Context mContext, HashMap<String, String> data_trip, GeneralFunctions generalFunc, boolean isTripStart) {
        this.mContext = mContext;
        this.generalFunc = generalFunc;
        this.data_trip = data_trip;
        this.isTripStart = isTripStart;

        show();
    }

    public void show() {
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(mContext);

        MTextView titleTxtView = new MTextView(mContext);

        if (data_trip.get("REQUEST_TYPE").equals(Utils.CabGeneralType_Deliver)) {
            titleTxtView.setText(generalFunc.retrieveLangLBl("Cancel Delivery", "LBL_CANCEL_DELIVERY"));
        } else {
            titleTxtView.setText(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TRIP"));
        }

        titleTxtView.setPadding(Utils.dipToPixels(mContext,15), Utils.dipToPixels(mContext,15), Utils.dipToPixels(mContext,15), Utils.dipToPixels(mContext,15));
        titleTxtView.setGravity(Gravity.CENTER);
        titleTxtView.setTextColor(Color.parseColor("#1c1c1c"));
        titleTxtView.setTextSize(22);

        builder.setCustomTitle(titleTxtView);

        LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.input_box_view, null);


        final MaterialEditText reasonBox = (MaterialEditText) dialogView.findViewById(R.id.editBox);
        final MaterialEditText commentBox = (MaterialEditText) inflater.inflate(R.layout.editbox_form_design, null);
        commentBox.setLayoutParams(reasonBox.getLayoutParams());
        commentBox.setId(Utils.generateViewId());

        commentBox.setSingleLine(false);
        commentBox.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_FLAG_MULTI_LINE);
        commentBox.setGravity(Gravity.TOP);
        commentBox.setFloatingLabel(MaterialEditText.FLOATING_LABEL_HIGHLIGHT);

        commentBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_COMMENT_TXT"), generalFunc.retrieveLangLBl("", "LBL_WRITE_COMMENT_HINT_TXT"));
        reasonBox.setBothText(generalFunc.retrieveLangLBl("Reason", "LBL_REASON"), generalFunc.retrieveLangLBl("Enter your reason", "LBL_ENTER_REASON"));

        ((LinearLayout) dialogView).addView(commentBox);

        builder.setView(dialogView);
        builder.setPositiveButton(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TRIP_NOW"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {

            }
        });
        builder.setNegativeButton(generalFunc.retrieveLangLBl("", "LBL_CONTINUE_TRIP_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
            }
        });

        alertDialog = builder.create();
        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(alertDialog);
        }
        alertDialog.show();
//        alertDialog.getButton(AlertDialog.BUTTON_POSITIVE).setTextColor(Color.parseColor("#1C1C1C"));
//        alertDialog.getButton(AlertDialog.BUTTON_NEGATIVE).setTextColor(Color.parseColor("#909090"));
        alertDialog.getButton(AlertDialog.BUTTON_POSITIVE).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                if (Utils.checkText(reasonBox) == false) {
                    reasonBox.setError(generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT"));
                    return;
                }

                alertDialog.dismiss();

                if (isTripStart == false) {
                    cancelTrip(Utils.getText(reasonBox), Utils.getText(commentBox));
                } else {
                    ((ActiveTripActivity) mContext).cancelTrip(Utils.getText(reasonBox), Utils.getText(commentBox));
                }

            }
        });

        alertDialog.getButton(AlertDialog.BUTTON_NEGATIVE).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                alertDialog.dismiss();
            }
        });
    }

    public void cancelTrip(String reason, String comment) {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "cancelTrip");
        parameters.put("iDriverId", generalFunc.getMemberId());
        parameters.put("iUserId", data_trip.get("PassengerId"));
        parameters.put("iTripId", data_trip.get("TripId"));
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("Reason", reason);
        parameters.put("Comment", comment);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext,parameters);
        exeWebServer.setLoaderConfig(mContext, true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        generalFunc.saveGoOnlineInfo();
                       // generalFunc.restartApp();
                        generalFunc.restartwithGetDataApp();
                    } else {
                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }
}
