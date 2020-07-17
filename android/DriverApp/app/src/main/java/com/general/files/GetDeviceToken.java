package com.general.files;

import android.os.AsyncTask;

/**
 * Created by Admin on 13-11-2017.
 */

public class GetDeviceToken extends AsyncTask<String, String, String> {

    SetTokenResponse setTokenRes;

    GeneralFunctions generalFunc;

    String vDeviceToken = "";

    public GetDeviceToken(GeneralFunctions generalFunc) {
        this.generalFunc = generalFunc;
    }

    @Override
    protected String doInBackground(String... strings) {
        vDeviceToken = generalFunc.generateDeviceToken();

        return null;
    }

    @Override
    protected void onPostExecute(String s) {
        super.onPostExecute(s);

        if (setTokenRes != null) {
            setTokenRes.onTokenFound(vDeviceToken);
        }
    }

    public void setDataResponseListener(SetTokenResponse setTokenRes) {
        this.setTokenRes = setTokenRes;
    }

    public interface SetTokenResponse {
        void onTokenFound(String vDeviceToken);
    }
}
