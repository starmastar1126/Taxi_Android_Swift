package com.general.files;

import android.content.Context;
import android.text.TextPaint;
import android.text.style.ClickableSpan;

import com.fastcabtaxi.passenger.R;


/**
 * Created by Admin on 04-03-2017.
 */


public abstract class MyClickableSpan extends ClickableSpan {
    Context mContext;

    public MyClickableSpan(Context mContext) {
        this.mContext = mContext;
    }

    public void updateDrawState(TextPaint ds) {
        ds.setColor(mContext.getResources().getColor(R.color.appThemeColor_1));
        ds.setTextSize(mContext.getResources().getDimension(R.dimen.txt_size_16));
        ds.setUnderlineText(false);
    }
}