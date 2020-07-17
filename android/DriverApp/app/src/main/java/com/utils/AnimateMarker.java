package com.utils;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.animation.ValueAnimator;
import android.location.Location;
import android.util.Log;
import android.view.animation.LinearInterpolator;

import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 28-09-2017.
 */
public class AnimateMarker {

    public boolean driverMarkerAnimFinished = true;

    ArrayList<HashMap<String, String>> driverMarkersPositionList = new ArrayList<>();
    public HashMap<String,String> toPositionLat = new HashMap<>();
    public HashMap<String,String> toPositionLong = new HashMap<>();


    ValueAnimator currentValueAnimator;

    public LatLng currentLng = null;
    public static void animateMarker(final Marker marker, final GoogleMap gMap, final Location toPosition, final float rotationAngle, final float duration) {
        if (marker == null || toPosition == null || gMap == null) {
            return;
        }

        final LatLng startPosition = marker.getPosition();
        final LatLng endPosition = new LatLng(toPosition.getLatitude(), toPosition.getLongitude());
        final float startRotation = marker.getRotation();

        final LatLngInterpolator latLngInterpolator = new LatLngInterpolator.LinearFixed();
        ValueAnimator valueAnimator = ValueAnimator.ofFloat(0, 1);
        valueAnimator.setDuration((long) duration); // In milli seconds
        valueAnimator.setInterpolator(new LinearInterpolator());
        valueAnimator.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animation) {
                try {
                    float v = animation.getAnimatedFraction();
                    LatLng newPosition = latLngInterpolator.interpolate(v, startPosition, endPosition);
                    marker.setPosition(newPosition);
                    marker.setRotation(computeRotation(v, startRotation, rotationAngle));
                } catch (Exception ex) {
                }
            }
        });

        valueAnimator.start();
    }

    private static float computeRotation(float fraction, float start, float end) {
        float normalizeEnd = end - start; // rotate start to 0
        float normalizedEndAbs = (normalizeEnd + 360) % 360;

        float direction = (normalizedEndAbs > 180) ? -1 : 1; // -1 = anticlockwise, 1 = clockwise
        float rotation;
        if (direction > 0) {
            rotation = normalizedEndAbs;
        } else {
            rotation = normalizedEndAbs - 360;
        }

        float result = fraction * rotation + start;
        return (result + 360) % 360;
    }

    public HashMap<String, String> getNextBufferedLocationData(String iDriverId) {

        for (int i = 0; i < driverMarkersPositionList.size(); i++) {
            HashMap<String, String> item = driverMarkersPositionList.get(i);
            if (item.get("iDriverId").equals(iDriverId)) {
                item.put("Position", "" + i);
                return item;
            }
        }
        return null;
    }

    public void removeBufferedLocation(String iDriverId, String LocTime) {

        for (int i = 0; i < driverMarkersPositionList.size(); i++) {
            HashMap<String, String> item = driverMarkersPositionList.get(i);
            if (item.get("LocTime").equals(LocTime) && item.get("iDriverId").equals(iDriverId)) {
                driverMarkersPositionList.remove(i);
                break;
            }
        }

    }

    public void addToListAndStartNext(final Marker marker, final GoogleMap gMap, final Location toPosition, final float rotationAngle, final float duration, final String iDriverId, final String locTime) {
        if (currentValueAnimator != null) {
            currentValueAnimator.cancel();
            currentValueAnimator = null;
        }

        animateMarker(marker, gMap, toPosition, rotationAngle, duration, iDriverId, locTime);
    }

    public void animateMarker(final Marker marker, final GoogleMap gMap, final Location toPosition, final float rotationAngle, final float duration, final String iDriverId, final String locTime) {
        if (marker == null || toPosition == null || gMap == null) {
            return;
        }

        toPositionLat.put(""+toPosition.getLatitude(),"yes");
        toPositionLong.put(""+toPosition.getLongitude(),"yes");

        driverMarkerAnimFinished = false;

        final LatLng startPosition = marker.getPosition();
        final LatLng endPosition = new LatLng(toPosition.getLatitude(), toPosition.getLongitude());
        final float startRotation = marker.getRotation();


        final LatLngInterpolator latLngInterpolator = new LatLngInterpolator.LinearFixed();
        ValueAnimator valueAnimator = ValueAnimator.ofFloat(0, 1);
        valueAnimator.setDuration((long) duration); // In milli seconds
        valueAnimator.setInterpolator(new LinearInterpolator());

        currentValueAnimator = valueAnimator;

        valueAnimator.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
            @Override
            public void onAnimationUpdate(ValueAnimator animation) {
                try {

                    float v = animation.getAnimatedFraction();
                    LatLng newPosition = latLngInterpolator.interpolate(v, startPosition, endPosition);


                    currentLng = newPosition;

                    marker.setPosition(newPosition);
                    marker.setRotation(computeRotation(v, startRotation, rotationAngle));
                    Log.e("rotationAngle==>", "" + rotationAngle);
                } catch (Exception ex) {
                }
            }
        });

        valueAnimator.addListener(new AnimatorListenerAdapter() {
            @Override
            public void onAnimationCancel(Animator animation) {
                marker.setRotation(rotationAngle);
                super.onAnimationCancel(animation);

            }

            @Override
            public void onAnimationEnd(Animator animation) {
                driverMarkerAnimFinished = true;
                marker.setRotation(rotationAngle);
            }
        });

        valueAnimator.start();
    }



    public HashMap<String, String> getLastLocationDataOfMarker(Marker marker) {

        if (marker == null || marker.getTitle() == null || marker.getTitle() == "") {
            return (new HashMap<String, String>());
        }

        int lastIndex = driverMarkersPositionList.size() - 1;

        for (int i = 0; i < driverMarkersPositionList.size(); i++) {
            HashMap<String, String> item = driverMarkersPositionList.get(lastIndex - i);

            if (item.get("iDriverId").equals(marker.getTitle().replace("DriverId", ""))) {
                return item;
            }
        }

        return (new HashMap<String, String>());
    }



    public double bearingBetweenLocations(LatLng latLng1, LatLng latLng2) {

        double PI = 3.14159;
        double lat1 = latLng1.latitude * PI / 180;
        double long1 = latLng1.longitude * PI / 180;
        double lat2 = latLng2.latitude * PI / 180;
        double long2 = latLng2.longitude * PI / 180;

        double dLon = (long2 - long1);

        double y = Math.sin(dLon) * Math.cos(lat2);
        double x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1)
                * Math.cos(lat2) * Math.cos(dLon);

        double brng = Math.atan2(y, x);

        brng = Math.toDegrees(brng);
        brng = (brng + 360) % 360;

        return brng;
    }

}