package com.dialogs;

import android.app.Dialog;
import android.content.Context;
import android.graphics.drawable.ColorDrawable;
import android.os.Handler;
import android.view.View;
import android.view.Window;
import android.widget.LinearLayout;
import android.widget.ProgressBar;

import com.fastcabtaxi.passenger.R;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Map;

public class GetReceiverLocation implements Runnable, GenerateAlertBox.HandleAlertBtnClick {

    Context mContext;
    GeneralFunctions generalFunc;
    Dialog dialogRequestReceipientLoc;
    GenerateAlertBox generateAlert;
    ExecuteWebServerUrl currentWebTask;
    String receiverMobNo;
    String receiverName;
    int iTempReceiverId = 0;
    OnAddressSelected onAddressSelected;
    Handler getAddressHandler;
    Runnable runnable;

    public GetReceiverLocation(Context mContext, GeneralFunctions generalFunc, String receiverMobNo, String receiverName) {
        this.mContext = mContext;
        this.generalFunc = generalFunc;
        this.receiverMobNo = receiverMobNo;
        this.receiverName = receiverName;
    }

    public static String getPostDataString(HashMap<String, String> params) throws UnsupportedEncodingException {
        StringBuilder result = new StringBuilder();
        boolean first = true;
        for (Map.Entry<String, String> entry : params.entrySet()) {
            if (first)
                first = false;
            else
                result.append("&");

            result.append(URLEncoder.encode(entry.getKey(), "UTF-8"));
            result.append("=");
            result.append(URLEncoder.encode(entry.getValue(), "UTF-8"));
        }

        return result.toString();
    }

    @Override
    public void run() {

        dialogRequestReceipientLoc = new Dialog(mContext, R.style.Theme_Dialog);
        dialogRequestReceipientLoc.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogRequestReceipientLoc.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
        dialogRequestReceipientLoc.setContentView(R.layout.design_get_receipient_location_dialog);

        setVisibilityOfRetryArea(View.GONE);
        sendSmsToRecipient();

        MButton btn_type2 = ((MaterialRippleLayout) dialogRequestReceipientLoc.findViewById(R.id.btn_type2)).getChildView();
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_RETRY_TXT"));

        (dialogRequestReceipientLoc.findViewById(R.id.backImgView)).setVisibility(View.GONE);
        ((MTextView) dialogRequestReceipientLoc.findViewById(R.id.titleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_REQUESTING_TXT"));
        ((MTextView) dialogRequestReceipientLoc.findViewById(R.id.receiverLocNotUpdatedNotifyTxt)).setText(
                generalFunc.retrieveLangLBl("Receiver's location not updated yet.You can skip and try again OR retry.",
                        "LBL_NOTE_RECEVIER_LOCATION_NOT_UPDATED_TXT"));
        ((ProgressBar) dialogRequestReceipientLoc.findViewById(R.id.mProgressBar)).setIndeterminate(true);

        dialogRequestReceipientLoc.setCancelable(false);
        dialogRequestReceipientLoc.setCanceledOnTouchOutside(false);
        dialogRequestReceipientLoc.show();

        (dialogRequestReceipientLoc.findViewById(R.id.cancelImgView)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                cancelRequestConfirm();
            }
        });

        ((ProgressBar) dialogRequestReceipientLoc.findViewById(R.id.mProgressBar)).getIndeterminateDrawable().setColorFilter(
                mContext.getResources().getColor(R.color.appThemeColor_2), android.graphics.PorterDuff.Mode.SRC_IN);

        LinearLayout.LayoutParams layoutParams = (LinearLayout.LayoutParams) (dialogRequestReceipientLoc.findViewById(R.id.titleTxt)).getLayoutParams();

        layoutParams.setMargins(Utils.dipToPixels(mContext, 25), 0, 0, 0);
        (dialogRequestReceipientLoc.findViewById(R.id.titleTxt)).setLayoutParams(layoutParams);

        btn_type2.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                (dialogRequestReceipientLoc.findViewById(R.id.backImgView)).setVisibility(View.GONE);
                scheduleLocUpdate();
            }
        });
    }

    public void scheduleLocUpdate() {
        getAddressHandler = new Handler();

        runnable = new Runnable() {
            @Override
            public void run() {
                getReciepientLocation();
                setVisibilityOfRetryArea(View.VISIBLE);
            }
        };
        getAddressHandler.postDelayed(runnable, 5000);

    }

    private void getReciepientLocation() {
        if (iTempReceiverId == 0) {
            return;
        }
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getTempReceiverLocation");
        parameters.put("iTempReceiverId", "" + iTempReceiverId);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        this.currentWebTask = exeWebServer;
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {
                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        JSONArray msg_array = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);
                        JSONObject obj_temp = generalFunc.getJsonObject(msg_array, 0);
                        Utils.printLog("Api", "responseString" + responseString);

                        if (generalFunc.getJsonValue("eStatus", obj_temp.toString()).equalsIgnoreCase("Confirm")) {
                            if (onAddressSelected != null) {
                                Utils.printLog("Api", "Confirm");
                                onAddressSelected.getAddress(Integer.parseInt(generalFunc.getJsonValue("iTempReceiverId", obj_temp.toString())), generalFunc.getJsonValue("vLatitude", obj_temp.toString()), generalFunc.getJsonValue("vLongitude", obj_temp.toString()), generalFunc.getJsonValue("tAddress", obj_temp.toString()));
                            }
                        } else {
                            scheduleLocUpdate();
                        }

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

    public void setAddress(OnAddressSelected addressSelected) {
        this.onAddressSelected = addressSelected;
    }

    public void sendSmsToRecipient() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "confirmReceiverLocation");
        parameters.put("iUserId", generalFunc.getMemberId());
        parameters.put("vMobile", receiverMobNo);
        parameters.put("vName", receiverMobNo);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {
                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);


                    if (isDataAvail == true) {

                        String message = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
                        iTempReceiverId = Integer.parseInt(generalFunc.getJsonValue("iTempReceiverId", message));
                        scheduleLocUpdate();
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

    public void setVisibilityOfRetryArea(int visibility) {
        if (dialogRequestReceipientLoc != null)
            (dialogRequestReceipientLoc.findViewById(R.id.retryBtnArea)).setVisibility(visibility);
    }

    public void dismissDialog() {

        if (currentWebTask != null) {
            currentWebTask.cancel(true);
            currentWebTask = null;
        }

        if (getAddressHandler != null) {
            getAddressHandler.removeCallbacks(runnable);
            getAddressHandler = null;
        }

        if (dialogRequestReceipientLoc != null) {
            dialogRequestReceipientLoc.dismiss();
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
                generalFunc.retrieveLangLBl("Receiver's location not updated yet.Do you want to cancel request?", "LBL_RECIPIENT_LOC_UPDATE_CANCEL_TXT"));
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_TRIP_CANCEL_CONFIRM_TXT"));
        generateAlert.setNegativeBtn(generalFunc.retrieveLangLBl("Cancel", "LBL_CANCEL_TXT"));
        generateAlert.showAlertBox();
    }

    @Override
    public void handleBtnClick(int btn_id) {
        if (btn_id == 0) {
            if (generateAlert != null) {
                generateAlert.closeAlertBox();
                generateAlert = null;
            }
        } else {
            if (generateAlert != null) {
                generateAlert.closeAlertBox();
                generateAlert = null;
            }

            ((MTextView) dialogRequestReceipientLoc.findViewById(R.id.titleTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_CANCELING_TXT"));

            dismissDialog();
        }
    }

    public interface OnAddressSelected {
        void getAddress(int iTempReceiverId, String vLatitude, String vLongitude, String address);
    }
}
