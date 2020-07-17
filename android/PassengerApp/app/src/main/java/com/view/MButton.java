package com.view;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.Button;

import com.fastcabtaxi.passenger.R;

/**
 * Created by Admin on 28-01-2016.
 */
public class MButton extends Button {
    private static Typeface mTypeface;

    public MButton(Context context) {
        super(context);
        init(null);
    }

    public MButton(final Context context, final AttributeSet attrs, final int defStyle) {
        super(context, attrs, defStyle);

    }

    public MButton(Context context, AttributeSet attrs) {
        super(context, attrs);
        init(attrs);
    }

    private void init(AttributeSet attrs) {

        TypedArray typeArr = (getContext().obtainStyledAttributes(attrs, R.styleable.MButton));

        if (typeArr != null) {
            String typeFace_str = typeArr.getString(R.styleable.MButton_customButtonTypeFace);
            if (typeFace_str != null) {
                mTypeface = Typeface.createFromAsset(getContext().getAssets(), typeFace_str);
            } else {
                String defaultFont_path = getResources().getString(R.string.robotolightFont);
                mTypeface = Typeface.createFromAsset(getContext().getAssets(), defaultFont_path);

            }
        } else {
            if (mTypeface == null) {
                String defaultFont_path = getResources().getString(R.string.robotolightFont);
                mTypeface = Typeface.createFromAsset(getContext().getAssets(), defaultFont_path);
            }
        }


        this.setTypeface(mTypeface);
        this.setAllCaps(true);
    }
}
