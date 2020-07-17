package com.general.files;

import android.content.Context;

import com.splunk.mint.Mint;
import com.utils.CommonUtilities;
import com.utils.Utils;

/**
 * Created by Admin on 29-06-2016.
 */
public class SetUserData {
    String userProfileJson;

    GeneralFunctions generalFunc;
    boolean isStoreUserId = true;
    Context mContext;

    public SetUserData(String userProfileJson, GeneralFunctions generalFunc,Context mContext, boolean isStoreUserId) {
        this.userProfileJson = userProfileJson;
        this.generalFunc = generalFunc;
        this.mContext = mContext;
        this.isStoreUserId = isStoreUserId;


        setData();
    }

    public SetUserData(String userProfileJson, GeneralFunctions generalFunc, boolean isStoreUserId) {
        this.userProfileJson = userProfileJson;
        this.generalFunc = generalFunc;
        this.isStoreUserId = isStoreUserId;

        setData();
    }

    public void setData() {
        String isLanguageCodeChanged = generalFunc.getJsonValue("changeLangCode", userProfileJson);
        if (isLanguageCodeChanged.equals("Yes")) {

            generalFunc.storedata(CommonUtilities.languageLabelsKey, generalFunc.getJsonValue("UpdatedLanguageLabels", userProfileJson));

            generalFunc.storedata(CommonUtilities.LANGUAGE_CODE_KEY, generalFunc.getJsonValue("vLanguageCode", userProfileJson));
            generalFunc.storedata(CommonUtilities.LANGUAGE_IS_RTL_KEY, generalFunc.getJsonValue("langType", userProfileJson));
            generalFunc.storedata(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY, generalFunc.getJsonValue("vGMapLangCode", userProfileJson));

            Utils.setAppLocal(mContext);
        }



        if (isStoreUserId == true) {
            String memberId = generalFunc.getJsonValue("iDriverId", generalFunc.getJsonValue(CommonUtilities.message_str, userProfileJson));
            generalFunc.storeUserData(memberId);

            Mint.addExtraData("iMemberId", "" + generalFunc.getMemberId());
        }

    }
}
