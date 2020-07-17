package com.dialogs;

import android.app.Dialog;
import android.content.Context;
import android.graphics.drawable.ColorDrawable;
import android.view.View;
import android.view.Window;
import android.widget.LinearLayout;
import android.widget.ProgressBar;

import com.fastcabtaxi.passenger.MainActivity;
import com.fastcabtaxi.passenger.R;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.skyfishjy.library.RippleBackground;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import java.util.HashMap;

/**
 * Created by Admin on 11-07-2016.
 */
public class RequestNearestCab implements Runnable, GenerateAlertBox.HandleAlertBtnClick {

    Context mContext;
    GeneralFunctions generalFunc;
    Dialog dialogRequestNearestCab;
    GenerateAlertBox generateAlert;
    String driverIds;
    String cabRequestedJson;

    boolean isCancelBtnClick = false;

    public RequestNearestCab(Context mContext, GeneralFunctions generalFunc) {
        this.mContext = mContext;
        this.generalFunc = generalFunc;
    }

    public void setRequestData(String driverIds, String cabRequestedJson) {
        this.driverIds = driverIds;
        this.cabRequestedJson = cabRequestedJson;
    }

    @Override
    public void run() {

        dialogRequestNearestCab = new Dialog(mContext, R.style.Theme_Dialog);
        dialogRequestNearestCab.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogRequestNearestCab.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));

        dialogRequestNearestCab.setContentView(R.layout.design_request_nearest_cab_dialog);

        MButton btn_type2 = ((MaterialRippleLayout) dialogRequestNearestCab.findViewById(R.id.btn_type2)).getChildView();
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_RETRY_TXT"));


        ((RippleBackground) dialogRequestNearestCab.findViewById(R.id.rippleBgView)).startRippleAnimation();
        (dialogRequestNearestCab.findViewById(R.id.backImgView)).setVisibility(View.GONE);
        ((MTextView) dialogRequestNearestCab.findViewById(R.id.titleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_REQUESTING_TXT"));
        ((MTextView) dialogRequestNearestCab.findViewById(R.id.noDriverNotifyTxt)).setText(
                generalFunc.retrieveLangLBl("Driver is not able to take your request. Please cancel request and try again OR retry.",
                        "LBL_NOTE_NO_DRIVER_REQUEST"));
        ((ProgressBar) dialogRequestNearestCab.findViewById(R.id.mProgressBar)).setIndeterminate(true);

        dialogRequestNearestCab.setCancelable(false);
        dialogRequestNearestCab.setCanceledOnTouchOutside(false);
        dialogRequestNearestCab.show();

        (dialogRequestNearestCab.findViewById(R.id.cancelImgView)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (!isCancelBtnClick) {
                    isCancelBtnClick = true;
                    cancelRequestConfirm();
                }
            }
        });

        ((ProgressBar) dialogRequestNearestCab.findViewById(R.id.mProgressBar)).getIndeterminateDrawable().setColorFilter(
                mContext.getResources().getColor(R.color.appThemeColor_2), android.graphics.PorterDuff.Mode.SRC_IN);

        LinearLayout.LayoutParams layoutParams = (LinearLayout.LayoutParams) (dialogRequestNearestCab.findViewById(R.id.titleTxt)).getLayoutParams();

        layoutParams.setMargins(Utils.dipToPixels(mContext, 25), 0, 0, 0);
        (dialogRequestNearestCab.findViewById(R.id.titleTxt)).setLayoutParams(layoutParams);

        btn_type2.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (mContext instanceof MainActivity) {
                    ((MainActivity) mContext).retryReqBtnPressed(driverIds, cabRequestedJson);
                }

            }
        });
    }

    public void setVisibilityOfRetryArea(int visibility) {
        (dialogRequestNearestCab.findViewById(R.id.retryBtnArea)).setVisibility(visibility);
    }

    public void dismissDialog() {
        if (dialogRequestNearestCab != null && dialogRequestNearestCab.isShowing()) {
            dialogRequestNearestCab.dismiss();
        }
    }

    public void releaseMainTask() {
        if (mContext != null && mContext instanceof MainActivity) {
            ((MainActivity) mContext).releaseScheduleNotificationTask();
        }
    }

    public void cancelRequestConfirm() {
        if (generateAlert != null) {
            generateAlert.closeAlertBox();
            generateAlert = null;
        }
        generateAlert = new GenerateAlertBox(mContext);
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(this);
        generateAlert.setContentMessage("",
                generalFunc.retrieveLangLBl("", "LBL_CONFIRM_REQUEST_CANCEL_TXT"));
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"));
        generateAlert.showAlertBox();
    }

    @Override
    public void handleBtnClick(int btn_id) {
        if (btn_id == 0) {
            isCancelBtnClick = false;
            if (generateAlert != null) {
                generateAlert.closeAlertBox();
                generateAlert = null;
            }
        } else {
            if (generateAlert != null) {
                generateAlert.closeAlertBox();
                generateAlert = null;
            }

            ((MTextView) dialogRequestNearestCab.findViewById(R.id.titleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_CANCELING_TXT"));

            cancelRequest();
        }
    }

    public void cancelRequest() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "cancelCabRequest");
        parameters.put("iUserId", generalFunc.getMemberId());

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    Utils.printLog("Cancel cab req", "::" + responseString);

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {
                        dismissDialog();
                        releaseMainTask();
                        //generalFunc.restartApp();
                        generalFunc.restartwithGetDataApp();
                    } else {
                        String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);

                        if (message.equals("DO_RESTART") || message.equals(CommonUtilities.GCM_FAILED_KEY) || message.equals(CommonUtilities.APNS_FAILED_KEY) || message.equals("LBL_SERVER_COMM_ERROR")) {
                            dismissDialog();
                            releaseMainTask();
                            generalFunc.restartApp();
                        } else {
                            generalFunc.showGeneralMessage("",
                                    generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        }

                        ((MTextView) dialogRequestNearestCab.findViewById(R.id.titleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_REQUESTING_TXT"));
                    }
                } else {
                    ((MTextView) dialogRequestNearestCab.findViewById(R.id.titleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_REQUESTING_TXT"));
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }
}
