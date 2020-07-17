package com.fragments;


import android.graphics.Color;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.text.Editable;
import android.text.InputFilter;
import android.text.InputType;
import android.text.SpannableString;
import android.text.SpannableStringBuilder;
import android.text.TextUtils;
import android.text.TextWatcher;
import android.text.style.ForegroundColorSpan;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import com.fastcabtaxi.driver.CardPaymentActivity;
import com.fastcabtaxi.driver.R;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;

import java.util.HashMap;

import static com.fastcabtaxi.driver.R.id.demoText;

/**
 * A simple {@link Fragment} subclass.
 */
public class ViewCardFragment extends Fragment {

    GeneralFunctions generalFunc;
    View view;

    CardPaymentActivity cardPayAct;

    String userProfileJson;
    MButton btn_type2;
    MButton btn_type2_change;

    boolean isDemoMsgShown = false;
    MaterialEditText creditCardBox;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        view = inflater.inflate(R.layout.fragment_view_card, container, false);

        cardPayAct = (CardPaymentActivity) getActivity();
        generalFunc = cardPayAct.generalFunc;
        userProfileJson = cardPayAct.userProfileJson;
        btn_type2 = ((MaterialRippleLayout) view.findViewById(R.id.btn_type2)).getChildView();
        btn_type2_change = ((MaterialRippleLayout) view.findViewById(R.id.btn_type2_change)).getChildView();

        creditCardBox = (MaterialEditText) view.findViewById(R.id.creditCardBox);



        creditCardBox.setInputType(InputType.TYPE_CLASS_PHONE);
        int maxLength = 24;
        creditCardBox.setFilters(new InputFilter[] {new InputFilter.LengthFilter(maxLength)});

        creditCardBox.addTextChangedListener(new TextWatcher() {
            private static final char space = ' ';
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {

            }

            @Override
            public void afterTextChanged(Editable s) {
                if (s.length() > 0 && (s.length() % 5) == 0) {
                    final char c = s.charAt(s.length() - 1);
                    if (space == c) {
                        s.delete(s.length() - 1, s.length());
                    }
                }
                // Insert char where needed.
                if (s.length() > 0 && (s.length() % 5) == 0) {
                    char c = s.charAt(s.length() - 1);
                    // Only if its a digit where there should be a space we insert a space
                    if (Character.isDigit(c) && TextUtils.split(s.toString(), String.valueOf(space)).length <= 3) {
                        s.insert(s.length() - 1, String.valueOf(space));
                    }
                }

            }
        });


        checkData();
        setLabels();

        cardPayAct.changePageTitle(generalFunc.retrieveLangLBl("", "LBL_CARD_PAYMENT_DETAILS"));

        btn_type2.setId(Utils.generateViewId());
        btn_type2_change.setId(Utils.generateViewId());

        btn_type2.setOnClickListener(new setOnClickList());
        btn_type2_change.setOnClickListener(new setOnClickList());



        if(generalFunc.getJsonValue("SITE_TYPE",userProfileJson).equalsIgnoreCase("Demo")){
            SpannableStringBuilder builder = new SpannableStringBuilder();

            String content = cardPayAct.getResources().getString(R.string.demo_text);
            SpannableString redSpannable= new SpannableString(content);
            redSpannable.setSpan(new ForegroundColorSpan(Color.RED), 0, 4, 0);
            builder.append(redSpannable);

            (view.findViewById(R.id.demoarea)).setVisibility(View.VISIBLE);
            ((MTextView) view.findViewById(demoText)).setText(builder, TextView.BufferType.SPANNABLE);
        }else{
            (view.findViewById(R.id.demoarea)).setVisibility(View.GONE);
            ((MTextView) view.findViewById(demoText)).setVisibility(View.VISIBLE);
            ((MTextView) view.findViewById(demoText)).setText(generalFunc.retrieveLangLBl("","LBL_CARD_INFO_SECURE_NOTE"));
        }

        return view;
    }


    @Override
    public void onResume() {
        super.onResume();
        try {
            creditCardBox.requestFocus();
        }
        catch (Exception e)
        {

        }
    }

    public void checkData() {
        Utils.hideKeyboard(getActivity());

//        String STRIPE_PUBLISH_KEY = generalFunc.getJsonValue("STRIPE_PUBLISH_KEY", userProfileJson);
        String vStripeToken = generalFunc.getJsonValue("vStripeToken", userProfileJson);
        String vCreditCard = generalFunc.getJsonValue("vCreditCard", userProfileJson);
        String vStripeCusId = generalFunc.getJsonValue("vStripeCusId", userProfileJson);

        if (vStripeCusId.equals("")) {
            (view.findViewById(R.id.cardAddArea)).setVisibility(View.VISIBLE);
            (view.findViewById(R.id.cardViewArea)).setVisibility(View.GONE);
            creditCardBox.setVisibility(View.GONE);
        } else {

            (view.findViewById(R.id.noCardAvailheadrTxt)).setVisibility(View.GONE);
            (view.findViewById(R.id.noCardAvailTxt)).setVisibility(View.GONE);
            (view.findViewById(R.id.cardAddArea)).setVisibility(View.GONE);
            (view.findViewById(R.id.cardViewArea)).setVisibility(View.VISIBLE);
            ((MTextView) view.findViewById(R.id.cardTxt)).setText(vCreditCard);
            creditCardBox.setVisibility(View.VISIBLE);
            creditCardBox.setText(vCreditCard);
            creditCardBox.setTextColor(getResources().getColor(R.color.white));
        }


    }

    public void setLabels() {
        ((MTextView) view.findViewById(R.id.noCardAvailTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_NO_CARD_AVAIL_NOTE"));
        ((MTextView) view.findViewById(R.id.noCardAvailheadrTxt)).setText(generalFunc.retrieveLangLBl("NO CARD AVAILABLE.", "LBL_NO_CARD_AVAIL_HEADER_NOTE"));
        ((MTextView) view.findViewById(R.id.demonoteText)).setText(generalFunc.retrieveLangLBl("", "LBL_NOTES"));
        ((MTextView) view.findViewById(demoText)).setText(generalFunc.retrieveLangLBl("", "LBL_DEMO_CARD_DESC"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_ADD_CARD"));
        creditCardBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_CARD_NUMBER_HEADER_TXT"), generalFunc.retrieveLangLBl("", "LBL_CARD_NUMBER_HINT_TXT"));
        btn_type2_change.setText(generalFunc.retrieveLangLBl("", "LBL_CHANGE"));
//        btn_type2_change.setText(generalFunc.retrieveLangLBl("", "LBL_CHANGE"));
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(getActivity());
//            String vTripStatus = generalFunc.getJsonValue("vTripStatus",userProfileJson);
//            if(!vTripStatus.equals("Not Active") && !vTripStatus.equals("NONE") && !vTripStatus.equals("Not Requesting")
//                    && !vTripStatus.equals("Cancelled")){
//
//                generalFunc.showGeneralMessage("",generalFunc.retrieveLangLBl("","LBL_DIS_ALLOW_EDIT_CARD"));
//                return;
//            }



            checkUserData(i);
        }
    }

    public void checkUserData(final int i)
    {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "checkUserStatus");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);


        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActivity(), parameters);
        exeWebServer.setLoaderConfig(getActivity(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {



                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        if (i == btn_type2.getId()) {
                            cardPayAct.openAddCardFrag("ADD_CARD");
                        } else if (i == btn_type2_change.getId()) {
                            cardPayAct.openAddCardFrag("EDIT_CARD");
                        }
                    } else {

                        generalFunc.showGeneralMessage("",
                                generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                    }
                } else {
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();

    }
    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }
}
