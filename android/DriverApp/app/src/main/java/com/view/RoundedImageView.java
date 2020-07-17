package com.view;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Bitmap;
import android.graphics.BitmapShader;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Shader;
import android.graphics.drawable.BitmapDrawable;
import android.util.AttributeSet;
import android.widget.ImageView;

import com.fastcabtaxi.driver.R;

/**
 * Created by Admin on 30-06-2016.
 */
public class RoundedImageView extends ImageView {
    private int borderWidth = 4;
    private int viewWidth;
    private int viewHeight;
    private Bitmap image;
    private Paint paint;
    private Paint paintBorder;
    private BitmapShader shader;
    private Context mContext;
    TypedArray typedArr;
    boolean isRoundedImgAvoid = false;
    boolean isShadowAvoid = false;

    public RoundedImageView(Context context) {
        super(context);
        this.mContext = context;
        setup();
    }

    public RoundedImageView(Context context, AttributeSet attrs) {
        super(context, attrs);
        this.mContext = context;
        typedArr = mContext.obtainStyledAttributes(attrs, R.styleable.RoundedImageView);

        setup();
    }

    public RoundedImageView(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
        this.mContext = context;
        typedArr = mContext.obtainStyledAttributes(attrs, R.styleable.RoundedImageView);

        setup();
    }

    @SuppressLint("NewApi")
    private void setup() {
        // init paint
        paint = new Paint();
        paint.setAntiAlias(true);

        paintBorder = new Paint();
        setBorderColor(Color.WHITE);
        paintBorder.setAntiAlias(true);
        this.setLayerType(LAYER_TYPE_SOFTWARE, paintBorder);
        if(typedArr != null){
            isShadowAvoid = typedArr.getBoolean(R.styleable.RoundedImageView_isShadowAvoid, false);

            float borderWidth =  typedArr.getDimension(R.styleable.RoundedImageView_imgViewBorderWidth, 0);
            setBorderWidth((int)borderWidth);

            int borderColor =  typedArr.getInt(R.styleable.RoundedImageView_imgViewBorderColor, Color.parseColor("#FFFFFF"));
            setBorderColor(borderColor);
        }

        if(isShadowAvoid == false){
            paintBorder.setShadowLayer(4.0f, 0.0f, 2.0f, Color.BLACK);
        }
    }

    public void setBorderWidth(int borderWidth) {
        this.borderWidth = borderWidth;
        this.invalidate();
    }

    public void setBorderColor(int borderColor) {
        if (paintBorder != null)
            paintBorder.setColor(borderColor);

        this.invalidate();
    }

    private void loadBitmap() {
        BitmapDrawable bitmapDrawable = (BitmapDrawable) this.getDrawable();

        if (bitmapDrawable != null)
            image = bitmapDrawable.getBitmap();
    }

    @SuppressLint("DrawAllocation")
    @Override
    public void onDraw(Canvas canvas) {
        // load the bitmap
        if (typedArr != null) {
            isRoundedImgAvoid = typedArr.getBoolean(R.styleable.RoundedImageView_isRoundedImgAvoid, false);
        }

        if (isRoundedImgAvoid == true) {
            super.onDraw(canvas);
            return;
        }

        loadBitmap();

        // init shader
        if (image != null) {
            shader = new BitmapShader(Bitmap.createScaledBitmap(image, canvas.getWidth(), canvas.getHeight(), false),
                    Shader.TileMode.CLAMP, Shader.TileMode.CLAMP);
            paint.setShader(shader);
            int circleCenter = viewWidth / 2;

            // circleCenter is the x or y of the view's center
            // radius is the radius in pixels of the cirle to be drawn
            // paint contains the shader that will texture the shape

            if(isShadowAvoid == false){
                canvas.drawCircle(circleCenter + borderWidth, circleCenter + borderWidth, circleCenter + borderWidth - 4.0f,
                        paintBorder);
                canvas.drawCircle(circleCenter + borderWidth, circleCenter + borderWidth, circleCenter - 4.0f, paint);
            }else{
                canvas.drawCircle(circleCenter + borderWidth, circleCenter + borderWidth, circleCenter + borderWidth,
                        paintBorder);
                canvas.drawCircle(circleCenter + borderWidth, circleCenter + borderWidth, circleCenter, paint);
            }

        }
    }

    @Override
    protected void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
        int width = measureWidth(widthMeasureSpec);
        int height = measureHeight(heightMeasureSpec, widthMeasureSpec);

        viewWidth = width - (borderWidth * 2);
        viewHeight = height - (borderWidth * 2);

        setMeasuredDimension(width, height);
    }

    private int measureWidth(int measureSpec) {
        int result = 0;
        int specMode = MeasureSpec.getMode(measureSpec);
        int specSize = MeasureSpec.getSize(measureSpec);

        if (specMode == MeasureSpec.EXACTLY) {
            // We were told how big to be
            result = specSize;
        } else {
            // Measure the text
            result = viewWidth;
        }

        return result;
    }

    private int measureHeight(int measureSpecHeight, int measureSpecWidth) {
        int result = 0;
        int specMode = MeasureSpec.getMode(measureSpecHeight);
        int specSize = MeasureSpec.getSize(measureSpecHeight);

        if (specMode == MeasureSpec.EXACTLY) {
            // We were told how big to be
            result = specSize;
        } else {
            // Measure the text (beware: ascent is a negative number)
            result = viewHeight;
        }

        return (result + 2);
    }
}