package com.view.border;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Color;
import android.graphics.drawable.Drawable;
import android.util.AttributeSet;
import android.widget.FrameLayout;

import com.fastcabtaxi.passenger.R;


public class BorderFrameLayout extends FrameLayout {

    boolean allBorderSet = false;
    boolean allBorderDashed = false;
    private BorderDrawable borderDrawable;

    public BorderFrameLayout(Context context, AttributeSet attrs) {
        super(context, attrs);

        if (borderDrawable == null)
            borderDrawable = new BorderDrawable();

        if (attrs != null) {
            TypedArray a = getResources().obtainAttributes(attrs, R.styleable.BorderFrameLayout);

            int width, color;

            allBorderSet = a.getBoolean(R.styleable.BorderFrameLayout_allBorderSet, false);
            allBorderDashed = a.getBoolean(R.styleable.BorderFrameLayout_allBorderDashed, false);
            if (allBorderSet == true) {
                width = (int) a.getDimension(R.styleable.BorderFrameLayout_allBorderWidth, 0);
                color = a.getColor(R.styleable.BorderFrameLayout_allBorderColor, Color.BLACK);

                if (allBorderDashed == true) {
                    borderDrawable.setDashedBorderAvail(true);
                }

                borderDrawable.setLeftBorder(width, color);
                borderDrawable.setTopBorder(width, color);
                borderDrawable.setRightBorder(width, color);
                borderDrawable.setBottomBorder(width, color);
            } else {
                width = (int) a.getDimension(R.styleable.BorderFrameLayout_leftBorderWidth, 0);
                color = a.getColor(R.styleable.BorderFrameLayout_leftBorderColor, Color.BLACK);
                borderDrawable.setLeftBorder(width, color);


                width = (int) a.getDimension(R.styleable.BorderFrameLayout_rightBorderWidth, 0);
                color = a.getColor(R.styleable.BorderFrameLayout_rightBorderColor, Color.BLACK);
                borderDrawable.setRightBorder(width, color);


                width = (int) a.getDimension(R.styleable.BorderFrameLayout_topBorderWidth, 0);
                color = a.getColor(R.styleable.BorderFrameLayout_topBorderColor, Color.BLACK);
                borderDrawable.setTopBorder(width, color);

                width = (int) a.getDimension(R.styleable.BorderFrameLayout_bottomBorderWidth, 0);
                color = a.getColor(R.styleable.BorderFrameLayout_bottomBorderColor, Color.BLACK);
                borderDrawable.setBottomBorder(width, color);

            }


        }

        if (getBackground() != null) {
            borderDrawable.setBackground(borderDrawable);
        }

        setBackgroundDrawable(borderDrawable);
    }

    public BorderFrameLayout(Context context) {
        this(context, null);
    }

    @Override
    public void setBackgroundDrawable(Drawable d) {
        if (d == borderDrawable)
            super.setBackgroundDrawable(d);
        else {
            if (borderDrawable == null)
                borderDrawable = new BorderDrawable();
            borderDrawable.setBackground(d);
        }
    }


}
