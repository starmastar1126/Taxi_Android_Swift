package com.fastcabtaxi.passenger;

import android.content.Context;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.RecyclerView;
import android.view.View;
import android.widget.ImageView;
import android.widget.ProgressBar;

import com.adapter.files.HelpMainCategoryRecycleAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.StartActProcess;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 08-03-18.
 */

public class Help_MainCategory extends AppCompatActivity implements HelpMainCategoryRecycleAdapter.OnItemClickList
{
    public GeneralFunctions generalFunc;
    MTextView titleTxt;
    ImageView backImgView;
    ProgressBar loading;
    MTextView noHelpTxt;

    RecyclerView helpCategoryRecyclerView;
    HelpMainCategoryRecycleAdapter adapter;
    ErrorView errorView;

    ArrayList<HashMap<String, String>> list;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_help_maincategory);

        generalFunc = new GeneralFunctions(getActContext());


        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);

        loading = (ProgressBar) findViewById(R.id.loading);
        noHelpTxt = (MTextView) findViewById(R.id.noHelpTxt);
        helpCategoryRecyclerView = (RecyclerView) findViewById(R.id.helpCategoryRecyclerView);
        errorView = (ErrorView) findViewById(R.id.errorView);

        list = new ArrayList<>();
        adapter = new HelpMainCategoryRecycleAdapter(getActContext(), list, generalFunc);
        helpCategoryRecyclerView.setAdapter(adapter);

        getHelpCategory();
        setLabels();

        backImgView.setOnClickListener(new setOnClickList());

        adapter.setOnItemClickList(this);
    }

    public void setLabels(){
        titleTxt.setText(generalFunc.retrieveLangLBl("Help?", "LBL_HEADER_HELP_TXT"));
    }

    @Override
    public void onItemClick(int position) {
        Bundle bn = new Bundle();
        bn.putString("iHelpDetailCategoryId", list.get(position).get("iHelpDetailCategoryId"));
        bn.putString("vTitle", list.get(position).get("vTitle"));
        bn.putString("iUniqueId",list.get(position).get("iUniqueId"));
        new StartActProcess(getActContext()).startActWithData(Help_SubCategoryActivity.class, bn);
    }

    public void closeLoader() {
        if (loading.getVisibility() == View.VISIBLE) {
            loading.setVisibility(View.GONE);
        }
    }

    public void getHelpCategory() {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
        if (loading.getVisibility() != View.VISIBLE) {
            loading.setVisibility(View.VISIBLE);
        }

        if (list.size() > 0) {
            list.clear();
        }

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getHelpDetailCategoty");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("appType", CommonUtilities.app_type);

        Utils.printLog("data_","param::"+parameters.toString());

        noHelpTxt.setVisibility(View.GONE);

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                noHelpTxt.setVisibility(View.GONE);

                if (responseString != null && !responseString.equals("")) {

                    Utils.printLog("data_","response::"+responseString);

                    closeLoader();

                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {

                        JSONArray obj_arr = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

                        for (int i = 0; i < obj_arr.length(); i++) {
                            JSONObject obj_temp = generalFunc.getJsonObject(obj_arr, i);

                            HashMap<String, String> map = new HashMap<String, String>();

                            map.put("iHelpDetailCategoryId", generalFunc.getJsonValue("iHelpDetailCategoryId", obj_temp.toString()));
                            map.put("vTitle", generalFunc.getJsonValue("vTitle", obj_temp.toString()));
                            map.put("iUniqueId", generalFunc.getJsonValue("iUniqueId", obj_temp.toString()));

                            list.add(map);
                        }

                        adapter.notifyDataSetChanged();

                    } else {
                        noHelpTxt.setText(generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                        noHelpTxt.setVisibility(View.VISIBLE);
                    }
                } else {
                    generateErrorView();
                }
            }
        });
        exeWebServer.execute();
    }

    public void generateErrorView() {

        closeLoader();

        generalFunc.generateErrorView(errorView, "LBL_ERROR_TXT", "LBL_NO_INTERNET_TXT");

        if (errorView.getVisibility() != View.VISIBLE) {
            errorView.setVisibility(View.VISIBLE);
        }
        errorView.setOnRetryListener(new ErrorView.RetryListener() {
            @Override
            public void onRetry() {
                getHelpCategory();
            }
        });
    }

    public Context getActContext() {
        return Help_MainCategory.this;
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(getActContext());
            switch (view.getId()) {
                case R.id.backImgView:
                    Help_MainCategory.super.onBackPressed();
                    break;

            }
        }
    }

}
