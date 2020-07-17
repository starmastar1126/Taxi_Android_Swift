package com.view;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.TextView;

import com.fastcabtaxi.driver.R;


/**
 * Created by Admin on 05-05-2016.
 */
public class MTextView extends TextView {
    private static Typeface mTypeface;

    public MTextView(final Context context) {
        this(context, null);
    }

    public MTextView(final Context context, final AttributeSet attrs) {
        this(context, attrs, 0);
    }

    public MTextView(final Context context, final AttributeSet attrs, final int defStyle) {
        super(context, attrs, defStyle);


        TypedArray typeArr = context.obtainStyledAttributes(attrs, R.styleable.MTextView);

        if (typeArr != null) {
            String typeFace_str = typeArr.getString(R.styleable.MTextView_customTypeFace);
            try {

                if (typeFace_str.equalsIgnoreCase("roboto_medium")) {
                    typeFace_str = getResources().getString(R.string.robotomediumFont);

                } else if (typeFace_str.equalsIgnoreCase("roboto_light")) {
                    typeFace_str = getResources().getString(R.string.robotolightFont);

                } else if (typeFace_str.equalsIgnoreCase("roboto_bold")) {
                    typeFace_str = getResources().getString(R.string.robotobold);
                }
                if (typeFace_str != null) {
                    mTypeface = Typeface.createFromAsset(context.getAssets(), typeFace_str);
                } else {
                    String defaultFont_path = getResources().getString(R.string.robotolightFont);
                    mTypeface = Typeface.createFromAsset(context.getAssets(), defaultFont_path);

                }
            } catch (Exception e) {

            }

        } else {
            if (mTypeface == null) {
                String defaultFont_path = getResources().getString(R.string.robotolightFont);
                mTypeface = Typeface.createFromAsset(context.getAssets(), defaultFont_path);
            }
        }


        setTypeface(mTypeface);

//     this.setTextSize(22);
    }
}
