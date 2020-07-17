package com.datepicker.files;

import android.content.Context;
import android.support.v4.view.ViewPager;
import android.util.AttributeSet;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewConfiguration;
import android.widget.DatePicker;
import android.widget.TimePicker;

import com.fastcabtaxi.passenger.R;


/**
 * A custom {@link ViewPager} implementation that corrects
 * the height of the ViewPager and also dispatches touch events to either the ViewPager
 * or the date or time picker depending on the direction of the swipe.
 *
 * @author jjobes
 */


public class CustomViewPager extends ViewPager {
    boolean showTimepicker = true;
    private DatePicker mDatePicker;
    private TimePicker mTimePicker;
    private float x1, y1, x2, y2;
    private float mTouchSlop;
    private Context context;

    public CustomViewPager(Context context) {
        super(context);

        init(context);
    }

    public CustomViewPager(Context context, AttributeSet attrs) {
        super(context, attrs);

        init(context);

    }

    public void showTimePicker(boolean enableTimepicker) {
        showTimepicker = enableTimepicker;
    }

    private void init(Context context) {
        this.context = context;
        mTouchSlop = ViewConfiguration.get(context).getScaledPagingTouchSlop();
    }

    /**
     * Setting wrap_content on a ViewPager's layout_height in XML
     * doesn't seem to be recognized and the ViewPager will fill the
     * height of the screen regardless. We'll force the ViewPager to
     * have the same height as its immediate child.
     * <p>
     * Thanks to alexrainman for the bugfix!
     */
    @Override
    public void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
        int mode = MeasureSpec.getMode(heightMeasureSpec);
        // Unspecified means that the ViewPager is in a ScrollView WRAP_CONTENT.
        // At Most means that the ViewPager is not in a ScrollView WRAP_CONTENT.
        if (mode == MeasureSpec.UNSPECIFIED || mode == MeasureSpec.AT_MOST) {
            // super has to be called in the beginning so the child views can be initialized.
            super.onMeasure(widthMeasureSpec, heightMeasureSpec);
            int height = 0;
            for (int i = 0; i < getChildCount(); i++) {
                View child = getChildAt(i);
                child.measure(widthMeasureSpec, MeasureSpec.makeMeasureSpec(0, MeasureSpec.UNSPECIFIED));
                int h = child.getMeasuredHeight();
                if (h > height) height = h;
            }
            heightMeasureSpec = MeasureSpec.makeMeasureSpec(height, MeasureSpec.EXACTLY);
        }
        // super has to be called again so the new specs are treated as exact measurements
        super.onMeasure(widthMeasureSpec, heightMeasureSpec);

        mDatePicker = (DatePicker) findViewById(R.id.datePicker);
        if (showTimepicker) {
            mTimePicker = (TimePicker) findViewById(R.id.timePicker);
        }
    }
    /*@Override
    public void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
        int height = 0;

        for (int i = 0; i < getChildCount(); i++) {
            View child = getChildAt(i);
            child.measure(widthMeasureSpec, MeasureSpec.makeMeasureSpec(0, MeasureSpec.UNSPECIFIED));
            int h = child.getMeasuredHeight();
            if (h > height)
                height = h;
        }


        DisplayMetrics dm = new DisplayMetrics();

        WindowManager windowManager = (WindowManager) context.getApplicationContext().getSystemService(WINDOW_SERVICE);
        windowManager.getDefaultDisplay().getMetrics(dm);
        int widthInDP = Math.round(dm.heightPixels / dm.density);
        height = (int) Math.round(widthInDP * 0.75);


//         this.Window.Attributes.Height = (int) ((Resources.DisplayMetrics.HeightPixels / Resources.DisplayMetrics.Density) * 0.66);


        // height = 450;
        heightMeasureSpec = MeasureSpec.makeMeasureSpec(height, MeasureSpec.EXACTLY);
//        heightMeasureSpec = Utils.dipToPixels(getContext(), 250);
        super.onMeasure(widthMeasureSpec, heightMeasureSpec);

        mDatePicker = (DatePicker) findViewById(R.id.datePicker);
        if (showTimepicker) {
            mTimePicker = (TimePicker) findViewById(R.id.timePicker);
        }
    }*/

    /**
     * When the rider swipes their finger horizontally, dispatch
     * those touch events to the ViewPager. When they swipe
     * vertically, dispatch those touch events to the date or
     * time picker (depending on which page we're currently on).
     *
     * @param event
     */
    @Override
    public boolean dispatchTouchEvent(MotionEvent event) {
        switch (event.getAction()) {
            case MotionEvent.ACTION_DOWN:
                x1 = event.getX();
                y1 = event.getY();

                break;

            case MotionEvent.ACTION_MOVE:
                x2 = event.getX();
                y2 = event.getY();

                if (isScrollingHorizontal(x1, y1, x2, y2)) {
                    // When the rider is scrolling the ViewPager horizontally,
                    // block the pickers from scrolling vertically.
                    return super.dispatchTouchEvent(event);
                }

                break;
        }

        // As long as the ViewPager isn't scrolling horizontally,
        // dispatch the event to the DatePicker or TimePicker,
        // depending on which page the ViewPager is currently on.

        switch (getCurrentItem()) {
            case 0:

                if (mDatePicker != null)
                    mDatePicker.dispatchTouchEvent(event);

                break;

            case 1:

                if (mTimePicker != null)
                    mTimePicker.dispatchTouchEvent(event);

                break;
        }

        // need this for the ViewPager to scroll horizontally at all
        return super.onTouchEvent(event);
    }

    /**
     * Determine whether the distance between the rider's ACTION_DOWN
     * event (x1, y1) and the current ACTION_MOVE event (x2, y2) should
     * be interpreted as a horizontal swipe.
     *
     * @param x1
     * @param y1
     * @param x2
     * @param y2
     * @return
     */
    private boolean isScrollingHorizontal(float x1, float y1, float x2, float y2) {
        float deltaX = x2 - x1;
        float deltaY = y2 - y1;

        if (Math.abs(deltaX) > mTouchSlop &&
                Math.abs(deltaX) > Math.abs(deltaY)) {

            return true;
        }

        return false;
    }
}
