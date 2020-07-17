package com.fragments;


import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.fastcabtaxi.driver.MyProfileActivity;
import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
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


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
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

        removeInput();
        setLabels();

        setData();

        myProfileAct.changePageTitle(generalFunc.retrieveLangLBl("", "LBL_PROFILE_TITLE_TXT"));


        return view;
    }

    public void setLabels() {
        fNameBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_FIRST_NAME_HEADER_TXT"));
        lNameBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_LAST_NAME_HEADER_TXT"));
        emailBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_EMAIL_LBL_TXT"));
        mobileBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_MOBILE_NUMBER_HEADER_TXT"));
        langBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_LANGUAGE_TXT"));
        currencyBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_CURRENCY_TXT"));
        ((MTextView) view.findViewById(R.id.serviceDesHTxtView)).setText(generalFunc.retrieveLangLBl("Service Description", "LBL_SERVICE_DESCRIPTION"));
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(getActivity());
            int i = view.getId();
            Bundle bn = new Bundle();

        }
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
        currencyBox.setText(generalFunc.getJsonValue("vCurrencyDriver", userProfileJson));
        if (generalFunc.getJsonValue("tProfileDescription", userProfileJson).equals("")) {
            ((MTextView) (view.findViewById(R.id.serviceDesVTxtView))).setText("----");
        } else {
            ((MTextView) (view.findViewById(R.id.serviceDesVTxtView))).setText(generalFunc.getJsonValue("tProfileDescription", userProfileJson));
        }
        mobileBox.setText("+" + generalFunc.getJsonValue("vCode", userProfileJson) + generalFunc.getJsonValue("vPhone", userProfileJson));

        fNameBox.getLabelFocusAnimator().start();
        lNameBox.getLabelFocusAnimator().start();
        emailBox.getLabelFocusAnimator().start();
        mobileBox.getLabelFocusAnimator().start();
        langBox.getLabelFocusAnimator().start();
        currencyBox.getLabelFocusAnimator().start();

        if (generalFunc.getJsonValue("APP_TYPE", userProfileJson).equalsIgnoreCase("UberX")) {
            (view.findViewById(R.id.serviceDesHTxtView)).setVisibility(View.VISIBLE);
            (view.findViewById(R.id.serviceDesVTxtView)).setVisibility(View.VISIBLE);
        }

        setLanguage();
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
    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }
}
