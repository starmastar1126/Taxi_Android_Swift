package com.general.files;

import android.content.Context;
import android.view.View;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;

import com.fastcabtaxi.passenger.MainActivity;

/**
 * Created by Admin on 02-07-2016.
 */
public class CreateAnimation {
    View view;
    Context mContext;
    int animResId;
    int duration;

    boolean isMainActivity = false;
    boolean isHide = false;


    public CreateAnimation(View view, Context mContext, int animResId, int duration) {
        this.view = view;
        this.mContext = mContext;
        this.animResId = animResId;
        this.duration = duration;
    }

    public CreateAnimation(View view, boolean isHide, Context mContext, int animResId, int duration) {
        this.view = view;
        this.isHide = isHide;
        this.mContext = mContext;
        this.animResId = animResId;
        this.duration = duration;
    }

    public CreateAnimation(View view, Context mContext, int animResId, int duration, boolean isMainActivity) {
        this.view = view;
        this.mContext = mContext;
        this.animResId = animResId;
        this.duration = duration;
        this.isMainActivity = isMainActivity;
    }

    public void startAnimation() {
        Animation anim = AnimationUtils.loadAnimation(mContext, animResId);
        anim.setDuration(duration);
        view.startAnimation(anim);
        anim.setAnimationListener(new Animation.AnimationListener() {
            @Override
            public void onAnimationStart(Animation animation) {

            }

            @Override
            public void onAnimationEnd(Animation animation) {
                if (isHide == false) {
                    view.setVisibility(View.VISIBLE);
                }


                if (isMainActivity == true) {

                    ((MainActivity) mContext).setShadow();
                }
            }

            @Override
            public void onAnimationRepeat(Animation animation) {

            }
        });
    }
}
