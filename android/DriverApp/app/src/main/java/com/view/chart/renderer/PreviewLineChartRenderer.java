package com.view.chart.renderer;

import android.content.Context;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;

import com.view.chart.model.Viewport;
import com.view.chart.provider.LineChartDataProvider;
import com.view.chart.util.ChartUtils;
import com.view.chart.view.Chart;

/**
 * Renderer for preview chart based on LineChart. In addition to drawing chart data it also draw current viewport as
 * preview area.
 */
public class PreviewLineChartRenderer extends LineChartRenderer {
    private static final int DEFAULT_PREVIEW_TRANSPARENCY = 64;
    private static final int FULL_ALPHA = 255;
    private static final int DEFAULT_PREVIEW_STROKE_WIDTH_DP = 2;

    private Paint previewPaint = new Paint();

    public PreviewLineChartRenderer(Context context, Chart chart, LineChartDataProvider dataProvider) {
        super(context, chart, dataProvider);
        previewPaint.setAntiAlias(true);
        previewPaint.setColor(Color.LTGRAY);
        previewPaint.setStrokeWidth(ChartUtils.dp2px(density, DEFAULT_PREVIEW_STROKE_WIDTH_DP));
    }

    @Override
    public void drawUnclipped(Canvas canvas) {
        super.drawUnclipped(canvas);
        final Viewport currentViewport = computator.getCurrentViewport();
        final float left = computator.computeRawX(currentViewport.left);
        final float top = computator.computeRawY(currentViewport.top);
        final float right = computator.computeRawX(currentViewport.right);
        final float bottom = computator.computeRawY(currentViewport.bottom);
        previewPaint.setAlpha(DEFAULT_PREVIEW_TRANSPARENCY);
        previewPaint.setStyle(Paint.Style.FILL);
        canvas.drawRect(left, top, right, bottom, previewPaint);
        previewPaint.setStyle(Paint.Style.STROKE);
        previewPaint.setAlpha(FULL_ALPHA);
        canvas.drawRect(left, top, right, bottom, previewPaint);
    }

    public int getPreviewColor() {
        return previewPaint.getColor();
    }

    public void setPreviewColor(int color) {
        previewPaint.setColor(color);
    }
}