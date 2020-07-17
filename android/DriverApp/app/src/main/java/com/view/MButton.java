package com.view;

import android.content.Context;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.Button;

import com.fastcabtaxi.driver.R;

/**
 * Created by Admin on 28-01-2016.
 */
public class MButton extends Button {
    private static Typeface mTypeface;

    public MButton(Context context) {
        super(context);
        init();
    }

    public MButton(final Context context, final AttributeSet attrs, final int defStyle) {
        super(context, attrs, defStyle);

    }

    public MButton(Context context, AttributeSet attrs) {
        super(context, attrs);
        init();
    }

    private void init() {

        if (mTypeface == null) {
            String defaultFont_path = getResources().getString(R.string.robotolightFont);
            mTypeface = Typeface.createFromAsset(getContext().getAssets(), defaultFont_path);
        }
        this.setTypeface(mTypeface);
    }
}
