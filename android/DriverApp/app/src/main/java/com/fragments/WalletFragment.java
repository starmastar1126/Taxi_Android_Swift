package com.fragments;

import android.content.Context;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ProgressBar;

import com.adapter.files.WalletHistoryRecycleAdapter;
import com.fastcabtaxi.driver.MyWalletHistoryActivity;
import com.fastcabtaxi.driver.R;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;

import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.ErrorView;
import com.view.MTextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 27-05-2017.
 */

public class WalletFragment extends Fragment {

    View view;
    GeneralFunctions generalFunc;

    MyWalletHistoryActivity myWalletAct;

    ProgressBar loading_transaction_history;
    MTextView noTransactionTxt;
    MTextView transactionsTxt;

    RecyclerView walletHistoryRecyclerView;
    ErrorView errorView;

    ArrayList<HashMap<String, String>> list = new ArrayList<>();

    boolean mIsLoading = false;
    boolean isNextPageAvailable = false;

    String next_page_str = "";

    private WalletHistoryRecycleAdapter wallethistoryRecyclerAdapter;


    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {

        view = inflater.inflate(R.layout.fragment_wallet, container, false);

        myWalletAct = (MyWalletHistoryActivity) getActivity();
        generalFunc = myWalletAct.generalFunc;
        loading_transaction_history = (ProgressBar) view.findViewById(R.id.loading_transaction_history);
        noTransactionTxt = (MTextView) view.findViewById(R.id.noTransactionTxt);
        transactionsTxt = (MTextView) view.findViewById(R.id.transactionsTxt);
        walletHistoryRecyclerView = (RecyclerView) view.findViewById(R.id.walletTransactionRecyclerView);
        errorView = (ErrorView) view.findViewById(R.id.errorView);

        list = new ArrayList<>();
        wallethistoryRecyclerAdapter = new WalletHistoryRecycleAdapter(getActContext(), list, generalFunc, false);
        walletHistoryRecyclerView.setAdapter(wallethistoryRecyclerAdapter);

        walletHistoryRecyclerView.addOnScrollListener(new RecyclerView.OnScrollListener()

                                                      {
                                                          @Override
                                                          public void onScrolled(RecyclerView recyclerView, int dx, int dy) {
                                                              super.onScrolled(recyclerView, dx, dy);

                                                              int visibleItemCount = recyclerView.getLayoutManager().getChildCount();
                                                              int totalItemCount = recyclerView.getLayoutManager().getItemCount();
                                                              int firstVisibleItemPosition = ((LinearLayoutManager) recyclerView.getLayoutManager()).findFirstVisibleItemPosition();

                                                              int lastInScreen = firstVisibleItemPosition + visibleItemCount;
                                                              if ((lastInScreen == totalItemCount) && !(mIsLoading) && isNextPageAvailable == true) {

                                                                  mIsLoading = true;
                                                                  wallethistoryRecyclerAdapter.addFooterView();

                                                                  getTransactionHistory(true);

                                                              } else if (isNextPageAvailable == false) {
                                                                  wallethistoryRecyclerAdapter.removeFooterView();
                                                              }
                                                          }
                                                      }

        );

        getTransactionHistory(false);
        return view;
    }

    public Context getActContext() {
        return myWalletAct.getActContext();
    }


    public void removeNextPageConfig() {
        next_page_str = "";
        isNextPageAvailable = false;
        mIsLoading = false;
        wallethistoryRecyclerAdapter.removeFooterView();
    }

    public void closeLoader() {
        if (loading_transaction_history.getVisibility() == View.VISIBLE) {
            loading_transaction_history.setVisibility(View.GONE);
        }
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
                getTransactionHistory(false);
            }
        });
    }

    public void getTransactionHistory(final boolean isLoadMore) {
        if (errorView.getVisibility() == View.VISIBLE) {
            errorView.setVisibility(View.GONE);
        }
//        if (loading_transaction_history.getVisibility() != View.VISIBLE && isLoadMore == false) {
//            if(list.size()==0) {
//                loading_transaction_history.setVisibility(View.VISIBLE);
//            }
//        }

        final HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getTransactionHistory");
        parameters.put("iMemberId", generalFunc.getMemberId());
        parameters.put("UserType", CommonUtilities.app_type);
        parameters.put("ListType", getArguments().getString("ListType"));
        if (isLoadMore == true) {
            parameters.put("page", next_page_str);
        }

        noTransactionTxt.setVisibility(View.GONE);

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                noTransactionTxt.setVisibility(View.GONE);
                closeLoader();
                if (responseString != null && !responseString.equals("")) {


                    if (generalFunc.checkDataAvail(CommonUtilities.action_str, responseString) == true) {

                        String nextPage = generalFunc.getJsonValue("NextPage", responseString);
                        JSONArray arr_transhistory = generalFunc.getJsonArray(CommonUtilities.message_str, responseString);

                        if (arr_transhistory != null && arr_transhistory.length() > 0) {
                            for (int i = 0; i < arr_transhistory.length(); i++) {
                                // for (int i = 0; i < 10; i++) {
                                JSONObject obj_temp = generalFunc.getJsonObject(arr_transhistory, i);
                                HashMap<String, String> map = new HashMap<String, String>();
                                map.put("iUserWalletId", generalFunc.getJsonValue("iUserWalletId", obj_temp.toString()));
                                map.put("iUserId", generalFunc.getJsonValue("iUserId", obj_temp.toString()));
                                map.put("eUserType", generalFunc.getJsonValue("eUserType", obj_temp.toString()));
                                map.put("iBalance", generalFunc.getJsonValue("iBalance", obj_temp.toString()));
                                map.put("eType", generalFunc.getJsonValue("eType", obj_temp.toString()));
                                map.put("iTripId", generalFunc.getJsonValue("iTripId", obj_temp.toString()));
                                map.put("eFor", generalFunc.getJsonValue("eFor", obj_temp.toString()));
                                map.put("tDescription", generalFunc.getJsonValue("tDescription", obj_temp.toString()));
                                map.put("ePaymentStatus", generalFunc.getJsonValue("ePaymentStatus", obj_temp.toString()));
                                map.put("dDateOrig", generalFunc.getJsonValue("dDateOrig", obj_temp.toString()));
                                map.put("currentbal", generalFunc.getJsonValue("currentbal", obj_temp.toString()));
                                map.put("LBL_Status", generalFunc.retrieveLangLBl("", "LBL_Status"));
                                map.put("LBL_TRIP_NO", generalFunc.retrieveLangLBl("", "LBL_TRIP_NO"));
                                map.put("LBL_BALANCE_TYPE", generalFunc.retrieveLangLBl("", "LBL_BALANCE_TYPE"));
                                map.put("LBL_DESCRIPTION", generalFunc.retrieveLangLBl("", "LBL_DESCRIPTION"));
                                map.put("LBL_AMOUNT", generalFunc.retrieveLangLBl("", "LBL_AMOUNT"));
                                list.add(map);
                            }
                        }

                        String LBL_BALANCE = generalFunc.getJsonValue("user_available_balance", responseString);

                        ((MTextView) view.findViewById(R.id.yourBalTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_USER_BALANCE"));


                        ((MTextView) view.findViewById(R.id.walletamountTxt)).setText(LBL_BALANCE);


                        if (!nextPage.equals("") && !nextPage.equals("0")) {
                            next_page_str = nextPage;
                            isNextPageAvailable = true;
                        } else {
                            removeNextPageConfig();
                        }

                        wallethistoryRecyclerAdapter.notifyDataSetChanged();
                    } else {
                        if (list.size() == 0) {
                            removeNextPageConfig();
                            noTransactionTxt.setText(generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
                            noTransactionTxt.setVisibility(View.VISIBLE);
                        }
                    }

                    wallethistoryRecyclerAdapter.notifyDataSetChanged();


                } else {
                    if (isLoadMore == false) {
                        removeNextPageConfig();
                        generateErrorView();
                    }

                }

                mIsLoading = false;
            }
        });

        if (!isLoadMore) {
            if (list.size() == 0) {
                exeWebServer.execute();
            }

        } else {
            exeWebServer.execute();
        }

    }

    @Override
    public void onDestroyView() {
        super.onDestroyView();
        Utils.hideKeyboard(getActivity());
    }
}
