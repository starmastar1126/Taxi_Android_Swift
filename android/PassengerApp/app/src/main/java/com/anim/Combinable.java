package com.anim;

import android.animation.AnimatorSet;
import android.animation.TimeInterpolator;

/**
 * Created by Admin on 25-08-2016.
 */
public interface Combinable {
    public void animate();

    public AnimatorSet getAnimatorSet();

    public Animation setInterpolator(TimeInterpolator interpolator);

    public long getDuration();

    public Animation setDuration(long duration);

    public Animation setListener(AnimationListener listener);
}
