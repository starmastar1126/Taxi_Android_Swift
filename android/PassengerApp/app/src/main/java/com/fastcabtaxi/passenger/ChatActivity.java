package com.fastcabtaxi.passenger;

import android.content.Context;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.RecyclerView;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;

import com.adapter.files.ChatMessagesRecycleAdapter;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.google.firebase.database.ChildEventListener;
import com.google.firebase.database.DataSnapshot;
import com.google.firebase.database.DatabaseError;
import com.google.firebase.database.DatabaseReference;
import com.google.firebase.database.FirebaseDatabase;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MTextView;

import java.util.ArrayList;
import java.util.HashMap;

public class ChatActivity extends AppCompatActivity {

    Context mContext;
    GeneralFunctions generalFunc;

    EditText input;
    HashMap<String, String> data_trip_ada;
    DatabaseReference dbRef;
    String userProfileJson;
    String passengerImgName = "";
    private ChatMessagesRecycleAdapter chatAdapter;
    private ArrayList<HashMap<String, Object>> chatList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.design_trip_chat_detail_dialog);


        data_trip_ada = new HashMap<>();
        data_trip_ada.put("iFromMemberId", getIntent().getStringExtra("iFromMemberId"));
        data_trip_ada.put("FromMemberImageName", getIntent().getStringExtra("FromMemberImageName"));
        data_trip_ada.put("iTripId", getIntent().getStringExtra("iTripId"));
        data_trip_ada.put("FromMemberName", getIntent().getStringExtra("FromMemberName"));

        Utils.printLog("data_trip_ada", "::" + data_trip_ada);
        mContext = ChatActivity.this;

        generalFunc = new GeneralFunctions(ChatActivity.this);
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        passengerImgName = generalFunc.getJsonValue("vImgName", userProfileJson);


        dbRef = FirebaseDatabase.getInstance().getReference().child(generalFunc.retrieveValue(CommonUtilities.APP_GCM_SENDER_ID_KEY) + "-chat").child(data_trip_ada.get("iTripId") + "-Trip");


        chatList = new ArrayList<>();
        show();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();

    }

    public void onGcmMessageArrived(String message) {

        String driverMsg = generalFunc.getJsonValue("Message", message);
        String currentTripId = generalFunc.getJsonValue("iTripId", message);

        Utils.generateNotification(ChatActivity.this, generalFunc.retrieveLangLBl("", "LBL_PREFIX_TRIP_CANCEL_DRIVER") + " " + message);

        buildMessage(generalFunc.retrieveLangLBl("", "LBL_PREFIX_TRIP_CANCEL_DRIVER") + " " + message,
                generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"), true);


    }


    public void buildMessage(String message, String positiveBtn, final boolean isRestart) {
        final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();
                if (isRestart == true) {
                    generalFunc.restartApp();
                }
            }
        });
        generateAlert.setContentMessage("", message);
        generateAlert.setPositiveBtn(positiveBtn);
        generateAlert.showAlertBox();
    }


    public Context getActContext() {
        return ChatActivity.this;
    }

    public void show() {

        ImageView msgbtn = (ImageView) findViewById(R.id.msgbtn);
        input = (EditText) findViewById(R.id.input);
        msgbtn.setColorFilter(ContextCompat.getColor(getActContext(), R.color.lightchatbtncolor), android.graphics.PorterDuff.Mode.SRC_IN);

        input.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {


            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {

            }

            @Override
            public void afterTextChanged(Editable s) {

                if (s.length() == 0) {
                    msgbtn.setColorFilter(ContextCompat.getColor(getActContext(), R.color.lightchatbtncolor), android.graphics.PorterDuff.Mode.SRC_IN);
                } else {
                    msgbtn.setColorFilter(null);
                }


            }
        });

        input.setHint(generalFunc.retrieveLangLBl("Enter a message", "LBL_ENTER_MSG_TXT"));

        (findViewById(R.id.backImgView)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Utils.hideKeyboard(ChatActivity.this);
                onBackPressed();

            }
        });

        msgbtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //  Utils.hideKeyboard(getActContext());

                if (Utils.checkText(input) && Utils.getText(input).length() > 0) {
                    // Read the input field and push a new instance
                    // of ChatMessage to the Firebase database
                    HashMap<String, Object> dataMap = new HashMap<String, Object>();
                    dataMap.put("eUserType", CommonUtilities.app_type);
                    dataMap.put("Text", input.getText().toString().trim());
                    dataMap.put("iTripId", data_trip_ada.get("iTripId"));
                    dataMap.put("passengerImageName", passengerImgName);
                    dataMap.put("driverImageName", data_trip_ada.get("FromMemberImageName"));
                    dataMap.put("passengerId", generalFunc.getMemberId());
                    dataMap.put("driverId", data_trip_ada.get("iFromMemberId"));

                    dbRef.push().setValue(dataMap, new DatabaseReference.CompletionListener() {
                        @Override
                        public void onComplete(DatabaseError databaseError, DatabaseReference databaseReference) {

                            if (databaseError != null) {

                            } else {

                                sendTripMessageNotification(input.getText().toString().trim());

                                // Clear the input
                                input.setText("");
                            }
                        }
                    });

                }


            }
        });


//        setTitle(mRecipient);
        ((MTextView) findViewById(R.id.titleTxt)).setText(getIntent().getStringExtra("FromMemberName"));


        final RecyclerView chatCategoryRecyclerView = (RecyclerView) findViewById(R.id.chatCategoryRecyclerView);


        chatAdapter = new ChatMessagesRecycleAdapter(mContext, chatList, generalFunc, data_trip_ada);
        chatCategoryRecyclerView.setAdapter(chatAdapter);
        chatAdapter.notifyDataSetChanged();

        dbRef.addChildEventListener(new ChildEventListener() {
            @Override
            public void onChildAdded(DataSnapshot dataSnapshot, String s) {

                if (dataSnapshot.getValue() != null && dataSnapshot.getValue() instanceof HashMap) {

                    Utils.printLog("DataConvert", ":::" + dataSnapshot.getValue().toString());

                    HashMap<String, Object> dataMap = (HashMap<String, Object>) dataSnapshot.getValue();
                    chatList.add(dataMap);

                    chatAdapter.notifyDataSetChanged();
                    chatCategoryRecyclerView.scrollToPosition(chatList.size() - 1);
                }
            }

            @Override
            public void onChildChanged(DataSnapshot dataSnapshot, String s) {

            }

            @Override
            public void onChildRemoved(DataSnapshot dataSnapshot) {

            }

            @Override
            public void onChildMoved(DataSnapshot dataSnapshot, String s) {

            }

            @Override
            public void onCancelled(DatabaseError databaseError) {

            }
        });


    }


    public void sendTripMessageNotification(String message) {

        HashMap<String, String> parameters = new HashMap<>();
        parameters.put("type", "SendTripMessageNotification");
        parameters.put("UserType", Utils.userType);
        parameters.put("iFromMemberId", generalFunc.getMemberId());
        parameters.put("iTripId", data_trip_ada.get("iTripId"));
        parameters.put("iToMemberId", data_trip_ada.get("iFromMemberId"));
        parameters.put("tMessage", message);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(mContext, parameters);
        exeWebServer.setLoaderConfig(mContext, false, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


            }
        });
        exeWebServer.execute();
    }

    public String lastChars(String a, String b) {
        String str1 = "";
        if (a.length() >= 1) {
            str1 = a.substring(b.length() - 1);
        }
        return str1;
    }
}
