package com.view.line;

import android.annotation.TargetApi;
import android.content.Context;
import android.content.res.Resources;
import android.graphics.Canvas;
import android.graphics.Paint;
import android.graphics.Path;
import android.graphics.PathDashPathEffect;
import android.os.Build;
import android.util.AttributeSet;
import android.view.View;

import com.fastcabtaxi.passenger.R;

/**
 * Created by Admin on 04-07-2017.
 */

public class DottedLine extends View {

    private Paint mPaint;

    public DottedLine(Context context) {
        super(context);
        init();
    }

    public DottedLine(Context context, AttributeSet attrs) {
        super(context, attrs);
        init();
    }

    public DottedLine(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        init();
    }

    @TargetApi(Build.VERSION_CODES.LOLLIPOP)
    public DottedLine(Context context, AttributeSet attrs, int defStyleAttr, int defStyleRes) {
        super(context, attrs, defStyleAttr, defStyleRes);
        init();
    }

    private void init() {
        Resources res = getResources();
        mPaint = new Paint();

        mPaint.setColor(res.getColor(R.color.gray));
        int size = res.getDimensionPixelSize(R.dimen.divider_size_vert);
        int gap = res.getDimensionPixelSize(R.dimen.divider_gap_vert);
        mPaint.setStyle(Paint.Style.FILL);

        // To get actually round dots, we define a circle...
        Path path = new Path();
        path.addCircle(0, 0, size, Path.Direction.CW);
        // ...and use the path with the circle as our path effect
        mPaint.setPathEffect(new PathDashPathEffect(path, gap, 0, PathDashPathEffect.Style.ROTATE));

        // If we don't render in software mode, the dotted line becomes a solid line.
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB) {
            setLayerType(View.LAYER_TYPE_SOFTWARE, null);
        }
    }

    @Override
    protected void onDraw(Canvas canvas) {
        super.onDraw(canvas);
        canvas.drawLine(getWidth() / 2, 0, getWidth() / 2, getHeight(), mPaint);
    }
}
