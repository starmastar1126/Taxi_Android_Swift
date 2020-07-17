package com.view;

import android.graphics.drawable.GradientDrawable;
import android.view.View;

/**
 * Created by Admin on 11-05-2016.
 */
public class CreateRoundedView {
    int bgColor;
    int cornerRadius;
    int strokeWidth;
    int strokeColor;
    View view;
    boolean isImageView = false;

    public CreateRoundedView(int bgColor, int cornerRadius, int strokeWidth, int strokeColor, View view) {
        this.bgColor = bgColor;
        this.cornerRadius = cornerRadius;
        this.strokeWidth = strokeWidth;
        this.strokeColor = strokeColor;
        this.view = view;

        buildRoundedView();
    }

    public CreateRoundedView(int bgColor, int cornerRadius, int strokeWidth, int strokeColor, View view, boolean isImageView) {
        this.bgColor = bgColor;
        this.cornerRadius = cornerRadius;
        this.strokeWidth = strokeWidth;
        this.strokeColor = strokeColor;
        this.view = view;
        this.isImageView = isImageView;

        buildRoundedImgView();
    }

    public void buildRoundedView() {
        GradientDrawable gdDefault = new GradientDrawable();
        gdDefault.setColor(bgColor);
        gdDefault.setCornerRadius(cornerRadius);
        gdDefault.setStroke(strokeWidth, strokeColor);
        view.setBackground(gdDefault);
    }

    public void buildRoundedImgView() {
        GradientDrawable gdDefault = new GradientDrawable();
        gdDefault.setColor(bgColor);
        gdDefault.setShape(GradientDrawable.OVAL);
        gdDefault.setCornerRadius(cornerRadius);
        gdDefault.setStroke(strokeWidth, strokeColor);
        view.setBackground(gdDefault);
    }
}
