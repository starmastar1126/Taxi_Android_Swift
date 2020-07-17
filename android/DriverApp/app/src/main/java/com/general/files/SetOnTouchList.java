package com.general.files;

import android.view.MotionEvent;
import android.view.View;

/**
 * Created by Admin on 28-06-2016.
 */
public class SetOnTouchList implements View.OnTouchListener {

    @Override
    public boolean onTouch(View view, MotionEvent motionEvent) {
        if (motionEvent.getAction() == MotionEvent.ACTION_UP && !view.hasFocus()) {
            view.performClick();
        }
        return true;
    }
}
