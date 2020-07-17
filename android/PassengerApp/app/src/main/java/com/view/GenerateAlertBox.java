package com.view;

import android.content.Context;
import android.content.DialogInterface;

import com.general.files.GeneralFunctions;
import com.utils.Utils;

/**
 * Created by Admin on 27-06-2016.
 */
public class GenerateAlertBox {
    public android.support.v7.app.AlertDialog alertDialog;
    Context mContext;
    HandleAlertBtnClick listener;

    android.support.v7.app.AlertDialog.Builder alertDialogBuilder;

    GeneralFunctions generalFunc;

    public GenerateAlertBox(Context mContext) {
        this.mContext = mContext;

        alertDialogBuilder = new android.support.v7.app.AlertDialog.Builder(
                mContext);
        generalFunc = new GeneralFunctions(this.mContext);

    }

    public android.support.v7.app.AlertDialog.Builder getBuilder() {
        return this.alertDialogBuilder;
    }

    public void setContentMessage(String title_str, String message_str) {
        alertDialogBuilder.setTitle(title_str);
        alertDialogBuilder
                .setMessage(message_str);
    }

    public void setCancelable(boolean value) {
        alertDialogBuilder.setCancelable(value);
        if (alertDialog != null) {
            alertDialog.setCanceledOnTouchOutside(value);
            alertDialog.setCancelable(value);
        }
    }

    public void setNegativeBtn(String negative_btn_str) {
        alertDialogBuilder.setNegativeButton(negative_btn_str, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                if (listener != null) {
                    listener.handleBtnClick(0);
                }
            }
        });
    }

    public void setPositiveBtn(String positive_btn_str) {
        alertDialogBuilder.setPositiveButton(positive_btn_str, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {

                if (listener != null) {
                    listener.handleBtnClick(1);
                }
            }
        });
    }

    public void resetBtn() {
        alertDialogBuilder.setNegativeButton(null, null);
        alertDialogBuilder.setPositiveButton(null, null);
    }

    public void showAlertBox() {
        try {


            alertDialog = alertDialogBuilder.create();
            alertDialog.setCancelable(false);

            if (generalFunc.isRTLmode()) {
                generalFunc.forceRTLIfSupported(alertDialog);
            } else {
                generalFunc.forceLTRIfSupported(alertDialog);
            }
            alertDialog.show();


        } catch (Exception e) {
            Utils.printLog("ExceptionAlert", e.toString());

        }

    }

    public void closeAlertBox() {
        try {
            if (alertDialog != null) {
                alertDialog.dismiss();
            }
        } catch (Exception e) {

        }
    }

    public void setBtnClickList(HandleAlertBtnClick listener) {
        this.listener = listener;
    }

    public interface HandleAlertBtnClick {
        void handleBtnClick(int btn_id);
    }
}
