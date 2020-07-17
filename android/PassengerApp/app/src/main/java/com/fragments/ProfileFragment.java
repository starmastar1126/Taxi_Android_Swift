package com.fragments;


import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.support.v4.app.Fragment;
import android.support.v4.graphics.drawable.DrawableCompat;
import android.util.TypedValue;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;

import com.fastcabtaxi.passenger.MyProfileActivity;
import com.fastcabtaxi.passenger.R;
import com.fastcabtaxi.passenger.SearchPickupLocationActivity;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.google.android.gms.location.places.Place;
import com.google.android.gms.location.places.ui.PlaceAutocomplete;
import com.google.android.gms.maps.model.LatLng;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MTextView;
import com.view.editBox.MaterialEditText;

import org.json.JSONArray;
import org.json.JSONObject;

/**
 * A simple {@link Fragment} subclass.
 */
public class ProfileFragment extends Fragment {


    View view;
    MyProfileActivity myProfileAct;
    GeneralFunctions generalFunc;
    String userProfileJson = "";

    MaterialEditText fNameBox;
    MaterialEditText lNameBox;
    MaterialEditText emailBox;
    MaterialEditText mobileBox;
    MaterialEditText langBox;
    MaterialEditText currencyBox;

    MTextView homePlaceTxt, homePlaceHTxt;
    MTextView workPlaceTxt, workPlaceHTxt;

    LinearLayout placearea;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        setHasOptionsMenu(true);

        view = inflater.inflate(R.layout.fragment_profile, container, false);
        myProfileAct = (MyProfileActivity) getActivity();
        generalFunc = myProfileAct.generalFunc;
        userProfileJson = myProfileAct.userProfileJson;

        fNameBox = (MaterialEditText) view.findViewById(R.id.fNameBox);
        lNameBox = (MaterialEditText) view.findViewById(R.id.lNameBox);
        emailBox = (MaterialEditText) view.findViewById(R.id.emailBox);
        mobileBox = (MaterialEditText) view.findViewById(R.id.mobileBox);
        langBox = (MaterialEditText) view.findViewById(R.id.langBox);
        currencyBox = (MaterialEditText) view.findViewById(R.id.currencyBox);
        homePlaceTxt = (MTextView) view.findViewById(R.id.homePlaceTxt);
        homePlaceHTxt = (MTextView) view.findViewById(R.id.homePlaceHTxt);
        workPlaceTxt = (MTextView) view.findViewById(R.id.workPlaceTxt);
        workPlaceHTxt = (MTextView) view.findViewById(R.id.workPlaceHTxt);
        placearea = (LinearLayout) view.findViewById(R.id.placearea);

        removeInput();
        setLabels();

        setData();

        myProfileAct.changePageTitle(generalFunc.retrieveLangLBl("", "LBL_PROFILE_TITLE_TXT"));
        homePlaceTxt.setOnClickListener(new setOnClickList());
        workPlaceTxt.setOnClickListener(new setOnClickList());

        checkPlaces();

        if (myProfileAct.isEdit) {
            placearea.setVisibility(View.GONE);
        }

        if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equals(Utils.CabGeneralType_UberX)) {
            placearea.setVisibility(View.GONE);
        }

        return view;
    }

    public void setLabels() {
        fNameBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_FIRST_NAME_HEADER_TXT"));
        lNameBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_LAST_NAME_HEADER_TXT"));
        emailBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_EMAIL_LBL_TXT"));
        mobileBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_MOBILE_NUMBER_HEADER_TXT"));
        langBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_LANGUAGE_TXT"));
        currencyBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_CURRENCY_TXT"));
        homePlaceTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_HOME_PLACE_TXT"));
        workPlaceTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_WORK_PLACE_TXT"));
        homePlaceHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOME_PLACE"));
        workPlaceHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_WORK_PLACE"));
        ((MTextView) view.findViewById(R.id.placesTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_PLACES_HEADER_TXT"));
    }

    public void removeInput() {
        Utils.removeInput(fNameBox);
        Utils.removeInput(lNameBox);
        Utils.removeInput(emailBox);
        Utils.removeInput(mobileBox);
        Utils.removeInput(langBox);
        Utils.removeInput(currencyBox);

        fNameBox.setHideUnderline(true);
        lNameBox.setHideUnderline(true);
        emailBox.setHideUnderline(true);
        mobileBox.setHideUnderline(true);
        langBox.setHideUnderline(true);
        currencyBox.setHideUnderline(true);
    }

    public void setData() {
        fNameBox.setText(generalFunc.getJsonValue("vName", userProfileJson));
        lNameBox.setText(generalFunc.getJsonValue("vLastName", userProfileJson));
        emailBox.setText(generalFunc.getJsonValue("vEmail", userProfileJson));
        currencyBox.setText(generalFunc.getJsonValue("vCurrencyPassenger", userProfileJson));
        mobileBox.setText("+" + generalFunc.getJsonValue("vPhoneCode", userProfileJson) + generalFunc.getJsonValue("vPhone", userProfileJson));

        fNameBox.getLabelFocusAnimator().start();
        lNameBox.getLabelFocusAnimator().start();
        emailBox.getLabelFocusAnimator().start();
        mobileBox.getLabelFocusAnimator().start();
        langBox.getLabelFocusAnimator().start();
        currencyBox.getLabelFocusAnimator().start();

        setLanguage();
    }

    public void checkPlaces() {
        final SharedPreferences mpref_place = PreferenceManager.getDefaultSharedPreferences(myProfileAct.getActContext());

        String home_address_str = mpref_place.getString("userHomeLocationAddress", null);
        String work_address_str = mpref_place.getString("userWorkLocationAddress", null);


        final Drawable img_delete = getResources().getDrawable(R.mipmap.ic_edit);
        final Drawable img_edit = getResources().getDrawable(R.mipmap.ic_pluse);
        final Drawable img_home_place = getResources().getDrawable(R.mipmap.ic_home);
        final Drawable img_work_place = getResources().getDrawable(R.mipmap.ic_work);

        int color = Color.parseColor("#909090");
        DrawableCompat.setTint(img_delete, color);
        DrawableCompat.setTint(img_edit, color);


        if (home_address_str != null) {
//            homePlaceTxt.setText("" + home_address_str);

            homePlaceTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOME_PLACE"));
            homePlaceHTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 16);
            homePlaceTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14);
            homePlaceTxt.setTextColor(getResources().getColor(R.color.gray));
            homePlaceHTxt.setText("" + home_address_str);
            homePlaceHTxt.setVisibility(View.VISIBLE);
            homePlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_delete, null);
            homePlaceHTxt.setTextColor(getResources().getColor(R.color.black));

            homePlaceTxt.setOnTouchListener(new View.OnTouchListener() {
                final int DRAWABLE_LEFT = 0;
                final int DRAWABLE_TOP = 1;
                final int DRAWABLE_RIGHT = 2;
                final int DRAWABLE_BOTTOM = 3;

                @Override
                public boolean onTouch(View v, MotionEvent event) {

                    if (generalFunc.isRTLmode() == true) {
                        if (event.getAction() == MotionEvent.ACTION_UP && homePlaceTxt.getCompoundDrawables()[DRAWABLE_LEFT] != null) {
                            if (event.getRawX() <= (homePlaceTxt.getLeft() + homePlaceTxt.getCompoundDrawables()[DRAWABLE_LEFT].getBounds().width())) {
                                // your action here

//                                mpref_place.edit().remove("userHomeLocationAddress").commit();
//                                mpref_place.edit().remove("userHomeLocationLatitude").commit();
//                                mpref_place.edit().remove("userHomeLocationLongitude").commit();
//
//                                homePlaceTxt.setText("" + generalFunc.retrieveLangLBl("", "LBL_ADD_HOME_PLACE_TXT"));
//
//                                homePlaceTxt.setCompoundDrawablesWithIntrinsicBounds(null, null, null, null);
//                                homePlaceTxt.setOnTouchListener(null);
//                                homePlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_edit, null);
//                                homePlaceTxt.setTextColor(getResources().getColor(R.color.gray));

                                Bundle bn = new Bundle();
                                bn.putString("isHome", "true");
                                if (generalFunc.isLocationEnabled()) {
                                    new StartActProcess(myProfileAct.getActContext()).startActForResult(myProfileAct.getProfileFrag(), SearchPickupLocationActivity.class,
                                            Utils.ADD_HOME_LOC_REQ_CODE, bn);

                                } else {
                                    try {

                                        Intent intent = new PlaceAutocomplete.IntentBuilder(PlaceAutocomplete.MODE_FULLSCREEN)
                                                .build(getActivity());
                                        startActivityForResult(intent, Utils.ADD_HOME_LOC_REQ_CODE);
                                    } catch (Exception e) {

                                    }
                                }

                                return true;
                            }
                        }
                    } else {
                        if (event.getAction() == MotionEvent.ACTION_UP && homePlaceTxt.getCompoundDrawables()[DRAWABLE_RIGHT] != null) {
                            if (event.getRawX() >= (homePlaceTxt.getRight() - homePlaceTxt.getCompoundDrawables()[DRAWABLE_RIGHT].getBounds().width())) {
                                // your action here

//                                mpref_place.edit().remove("userHomeLocationAddress").commit();
//                                mpref_place.edit().remove("userHomeLocationLatitude").commit();
//                                mpref_place.edit().remove("userHomeLocationLongitude").commit();
//
//                                homePlaceTxt.setText("" + generalFunc.retrieveLangLBl("", "LBL_ADD_HOME_PLACE_TXT"));
//
//                                homePlaceTxt.setCompoundDrawablesWithIntrinsicBounds(null, null, null, null);
//                                homePlaceTxt.setOnTouchListener(null);
//                                homePlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_edit, null);
//                                homePlaceTxt.setTextColor(getResources().getColor(R.color.gray));

                                Bundle bn = new Bundle();
                                bn.putString("isHome", "true");
                                if (generalFunc.isLocationEnabled()) {
                                    new StartActProcess(myProfileAct.getActContext()).startActForResult(myProfileAct.getProfileFrag(), SearchPickupLocationActivity.class,
                                            Utils.ADD_HOME_LOC_REQ_CODE, bn);

                                } else {
                                    try {

                                        Intent intent = new PlaceAutocomplete.IntentBuilder(PlaceAutocomplete.MODE_FULLSCREEN)
                                                .build(getActivity());
                                        startActivityForResult(intent, Utils.ADD_HOME_LOC_REQ_CODE);
                                    } catch (Exception e) {

                                    }
                                }


                                return true;
                            }
                        }
                    }

                    return false;
                }
            });
        } else {
            homePlaceHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_HOME_PLACE"));
            homePlaceHTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14);
            homePlaceTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 16);
            homePlaceTxt.setText("" + generalFunc.retrieveLangLBl("", "LBL_ADD_HOME_PLACE_TXT"));
            homePlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_edit, null);
            homePlaceTxt.setTextColor(getResources().getColor(R.color.gray));
        }

        if (work_address_str != null) {

            workPlaceTxt.setText(generalFunc.retrieveLangLBl("", "LBL_WORK_PLACE"));
            workPlaceHTxt.setText("" + work_address_str);
            workPlaceHTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 16);
            workPlaceTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14);
            workPlaceTxt.setTextColor(getResources().getColor(R.color.gray));
            workPlaceHTxt.setVisibility(View.VISIBLE);
            workPlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_delete, null);
            workPlaceHTxt.setTextColor(getResources().getColor(R.color.black));

            workPlaceTxt.setOnTouchListener(new View.OnTouchListener() {
                final int DRAWABLE_LEFT = 0;
                final int DRAWABLE_TOP = 1;
                final int DRAWABLE_RIGHT = 2;
                final int DRAWABLE_BOTTOM = 3;

                @Override
                public boolean onTouch(View v, MotionEvent event) {


                    if (generalFunc.isRTLmode() == true) {
                        if (event.getAction() == MotionEvent.ACTION_UP && workPlaceTxt.getCompoundDrawables()[DRAWABLE_LEFT] != null) {
                            if (event.getRawX() <= (workPlaceTxt.getLeft() + workPlaceTxt.getCompoundDrawables()[DRAWABLE_LEFT].getBounds().width())) {
                                // your action here

//                                mpref_place.edit().remove("userWorkLocationAddress").commit();
//                                mpref_place.edit().remove("userWorkLocationLatitude").commit();
//                                mpref_place.edit().remove("userWorkLocationLongitude").commit();
//
//                                workPlaceTxt.setText("" + generalFunc.retrieveLangLBl("", "LBL_ADD_WORK_PLACE_TXT"));
//
//                                workPlaceTxt.setCompoundDrawablesWithIntrinsicBounds(null, null, null, null);
//
//                                workPlaceTxt.setOnTouchListener(null);
//                                workPlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_edit, null);
//                                workPlaceTxt.setTextColor(getResources().getColor(R.color.gray));
                                Bundle bn = new Bundle();
                                if (generalFunc.isLocationEnabled()) {
                                    bn.putString("isWork", "true");
                                    new StartActProcess(myProfileAct.getActContext()).startActForResult(myProfileAct.getProfileFrag(), SearchPickupLocationActivity.class,
                                            Utils.ADD_WORK_LOC_REQ_CODE, bn);

                                } else {
                                    try {
                                        Intent intent = new PlaceAutocomplete.IntentBuilder(PlaceAutocomplete.MODE_FULLSCREEN)
                                                .build(getActivity());
                                        startActivityForResult(intent, Utils.ADD_WORK_LOC_REQ_CODE);
                                    } catch (Exception e) {


                                    }
                                }

                                return true;
                            }
                        }
                    } else {
                        if (event.getAction() == MotionEvent.ACTION_UP && workPlaceTxt.getCompoundDrawables()[DRAWABLE_RIGHT] != null) {
                            if (event.getRawX() >= (workPlaceTxt.getRight() - workPlaceTxt.getCompoundDrawables()[DRAWABLE_RIGHT].getBounds().width())) {
                                // your action here

//                                mpref_place.edit().remove("userWorkLocationAddress").commit();
//                                mpref_place.edit().remove("userWorkLocationLatitude").commit();
//                                mpref_place.edit().remove("userWorkLocationLongitude").commit();
//
//                                workPlaceTxt.setText("" + generalFunc.retrieveLangLBl("", "LBL_ADD_WORK_PLACE_TXT"));
//
//                                workPlaceTxt.setCompoundDrawablesWithIntrinsicBounds(null, null, null, null);
//
//                                workPlaceTxt.setOnTouchListener(null);
//                                workPlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_edit, null);
//                                workPlaceTxt.setTextColor(getResources().getColor(R.color.gray));
                                Bundle bn = new Bundle();
                                if (generalFunc.isLocationEnabled()) {
                                    bn.putString("isWork", "true");
                                    new StartActProcess(myProfileAct.getActContext()).startActForResult(myProfileAct.getProfileFrag(), SearchPickupLocationActivity.class,
                                            Utils.ADD_WORK_LOC_REQ_CODE, bn);

                                } else {
                                    try {
                                        Intent intent = new PlaceAutocomplete.IntentBuilder(PlaceAutocomplete.MODE_FULLSCREEN)
                                                .build(getActivity());
                                        startActivityForResult(intent, Utils.ADD_WORK_LOC_REQ_CODE);
                                    } catch (Exception e) {


                                    }
                                }

                                return true;
                            }
                        }
                    }
                    return false;
                }
            });
        } else {
            workPlaceHTxt.setText(generalFunc.retrieveLangLBl("", "LBL_WORK_PLACE"));
            workPlaceHTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 14);
            workPlaceTxt.setTextSize(TypedValue.COMPLEX_UNIT_SP, 16);
            workPlaceTxt.setText("" + generalFunc.retrieveLangLBl("", "LBL_ADD_WORK_PLACE_TXT"));
            workPlaceTxt.setCompoundDrawablesRelativeWithIntrinsicBounds(null, null, img_edit, null);
            workPlaceTxt.setTextColor(getResources().getColor(R.color.gray));

        }
    }

    public void setLanguage() {
        JSONArray languageList_arr = generalFunc.getJsonArray(generalFunc.retrieveValue(CommonUtilities.LANGUAGE_LIST_KEY));

        for (int i = 0; i < languageList_arr.length(); i++) {
            JSONObject obj_temp = generalFunc.getJsonObject(languageList_arr, i);

            if ((generalFunc.retrieveValue(CommonUtilities.LANGUAGE_CODE_KEY)).equals(generalFunc.getJsonValue("vCode", obj_temp.toString()))) {

                langBox.setText(generalFunc.getJsonValue("vTitle", obj_temp.toString()));
            }
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        SharedPreferences mPrefs = PreferenceManager.getDefaultSharedPreferences(myProfileAct.getActContext());
        if (requestCode == Utils.ADD_HOME_LOC_REQ_CODE && resultCode == myProfileAct.RESULT_OK && data != null) {


            if (generalFunc.isLocationEnabled()) {
                SharedPreferences.Editor editor = mPrefs.edit();
                editor.putString("userHomeLocationLatitude", "" + data.getStringExtra("Latitude"));
                editor.putString("userHomeLocationLongitude", "" + data.getStringExtra("Longitude"));
                editor.putString("userHomeLocationAddress", "" + data.getStringExtra("Address"));

                editor.commit();

                homePlaceTxt.setText(data.getStringExtra("Address"));
                checkPlaces();

            } else {

                Place place = PlaceAutocomplete.getPlace(getActivity(), data);


                homePlaceTxt.setText(place.getAddress());
                LatLng placeLocation = place.getLatLng();
                SharedPreferences.Editor editor = mPrefs.edit();
                editor.putString("userHomeLocationLatitude", "" + placeLocation.latitude);
                editor.putString("userHomeLocationLongitude", "" + placeLocation.longitude);
                editor.putString("userHomeLocationAddress", "" + place.getAddress());

                editor.commit();
                checkPlaces();


            }

        } else if (requestCode == Utils.ADD_WORK_LOC_REQ_CODE && resultCode == myProfileAct.RESULT_OK && data != null) {

            if (generalFunc.isLocationEnabled()) {
                SharedPreferences.Editor editor = mPrefs.edit();
                editor.putString("userWorkLocationLatitude", "" + data.getStringExtra("Latitude"));
                editor.putString("userWorkLocationLongitude", "" + data.getStringExtra("Longitude"));
                editor.putString("userWorkLocationAddress", "" + data.getStringExtra("Address"));

                editor.commit();

                workPlaceTxt.setText(data.getStringExtra("Address"));

                checkPlaces();
            } else {


                Place place = PlaceAutocomplete.getPlace(getActivity(), data);
                workPlaceTxt.setText(place.getAddress());
                LatLng placeLocation = place.getLatLng();
                SharedPreferences.Editor editor = mPrefs.edit();
                editor.putString("userWorkLocationLatitude", "" + placeLocation.latitude);
                editor.putString("userWorkLocationLongitude", "" + placeLocation.longitude);
                editor.putString("userWorkLocationAddress", "" + place.getAddress());

                editor.commit();
                checkPlaces();

            }
        }
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Bundle bn = new Bundle();
            if (i == R.id.homePlaceTxt) {
                bn.putString("isHome", "true");


                if (generalFunc.isLocationEnabled()) {
                    new StartActProcess(myProfileAct.getActContext()).startActForResult(myProfileAct.getProfileFrag(), SearchPickupLocationActivity.class,
                            Utils.ADD_HOME_LOC_REQ_CODE, bn);

                } else {
                    try {

                        Intent intent = new PlaceAutocomplete.IntentBuilder(PlaceAutocomplete.MODE_FULLSCREEN)
                                .build(getActivity());
                        startActivityForResult(intent, Utils.ADD_HOME_LOC_REQ_CODE);
                    } catch (Exception e) {

                    }
                }

            } else if (i == R.id.workPlaceTxt) {


                if (generalFunc.isLocationEnabled()) {
                    bn.putString("isWork", "true");
                    new StartActProcess(myProfileAct.getActContext()).startActForResult(myProfileAct.getProfileFrag(), SearchPickupLocationActivity.class,
                            Utils.ADD_WORK_LOC_REQ_CODE, bn);

                } else {
                    try {
                        Intent intent = new PlaceAutocomplete.IntentBuilder(PlaceAutocomplete.MODE_FULLSCREEN)
                                .build(getActivity());
                        startActivityForResult(intent, Utils.ADD_WORK_LOC_REQ_CODE);
                    } catch (Exception e) {


                    }
                }
            }
        }
    }
}
