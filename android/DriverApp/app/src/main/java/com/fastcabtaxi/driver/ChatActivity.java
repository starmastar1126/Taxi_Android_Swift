package com.fastcabtaxi.driver;

import android.app.Activity;
import android.content.Context;
import android.content.IntentFilter;
import android.os.Bundle;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.RecyclerView;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ProgressBar;

import com.adapter.files.ChatMessagesRecycleAdapter;
import com.general.files.ConfigPubNub;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.TripMessageReceiver;
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
    String isFrom = "";
    EditText input;

    private ChatMessagesRecycleAdapter chatAdapter;
    private ArrayList<HashMap<String, Object>> chatList;
    private int count = 0;
    ProgressBar LoadingProgressBar;
    HashMap<String, String> data_trip_ada;
    TripMessageReceiver tripMsgReceiver;
    ConfigPubNub configPubNub;
    GenerateAlertBox generateAlert;

    DatabaseReference dbRef;
    String userProfileJson;
    String driverImgName = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.design_trip_chat_detail_dialog);
        mContext = ChatActivity.this;

        generalFunc = new GeneralFunctions(ChatActivity.this);

        //  data_trip = (HashMap<String, String>) getIntent().getSerializableExtra("tripdata");
        tripMsgReceiver = new TripMessageReceiver((Activity) ChatActivity.this, true);

        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        driverImgName = generalFunc.getJsonValue("vImage", userProfileJson);

        data_trip_ada = new HashMap<>();
        data_trip_ada.put("iFromMemberId", getIntent().getStringExtra("iFromMemberId"));
        data_trip_ada.put("FromMemberImageName", getIntent().getStringExtra("FromMemberImageName"));
        data_trip_ada.put("iTripId", getIntent().getStringExtra("iTripId"));
        data_trip_ada.put("FromMemberName", getIntent().getStringExtra("FromMemberName"));

        dbRef = FirebaseDatabase.getInstance().getReference().child(generalFunc.retrieveValue(CommonUtilities.APP_GCM_SENDER_ID_KEY) + "-chat").child(data_trip_ada.get("iTripId") + "-Trip");

        Utils.printLog("data_trip_ada_list", data_trip_ada.toString());


        chatList = new ArrayList<>();
        count = 0;
        registerTripMsgReceiver();
        show();

    }

    public boolean isPubNubEnabled() {
        String ENABLE_PUBNUB = generalFunc.retrieveValue(Utils.ENABLE_PUBNUB_KEY);

        return ENABLE_PUBNUB.equalsIgnoreCase("Yes");
    }


    public void registerTripMsgReceiver() {
        IntentFilter filter = new IntentFilter();
        filter.addAction(CommonUtilities.passenger_message_arrived_intent_action_trip_msg);

        registerReceiver(tripMsgReceiver, filter);

        if (isPubNubEnabled()) {
            configPubNub = new ConfigPubNub(ChatActivity.this);
        }
    }

    public void tripCancelled(String msg) {

        if (generateAlert != null) {
            generateAlert.closeAlertBox();
        }
        generateAlert = new GenerateAlertBox(ChatActivity.this);

        generateAlert.setCancelable(false);
        generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
            @Override
            public void handleBtnClick(int btn_id) {
                generateAlert.closeAlertBox();
                generalFunc.saveGoOnlineInfo();
                generalFunc.restartApp();
                // generalFunc.restartwithGetDataApp();
            }
        });
        generateAlert.setContentMessage("", msg);
        generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));


        generateAlert.showAlertBox();
    }

    public void unRegisterReceiver() {
        if (tripMsgReceiver != null) {
            try {
                unregisterReceiver(tripMsgReceiver);
            } catch (Exception e) {

            }
        }
    }

    @Override
    protected void onDestroy() {
        stopPubNub();

        super.onDestroy();
    }


    public void stopPubNub() {
        if (configPubNub != null) {
            configPubNub.unSubscribeToPrivateChannel();
            configPubNub = null;
            Utils.runGC();
        }
        unRegisterReceiver();
    }

    public void show() {


        ImageView msgbtn = (ImageView) findViewById(R.id.msgbtn);
        input = (EditText) findViewById(R.id.input);

        input.setHint(generalFunc.retrieveLangLBl("Enter a message", "LBL_ENTER_MSG_TXT"));


        msgbtn.setColorFilter(ContextCompat.getColor(mContext, R.color.lightchatbtncolor), android.graphics.PorterDuff.Mode.SRC_IN);

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
                    msgbtn.setColorFilter(ContextCompat.getColor(mContext, R.color.lightchatbtncolor), android.graphics.PorterDuff.Mode.SRC_IN);
                } else {
                    msgbtn.setColorFilter(null);
                }


            }
        });


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

                // Utils.hideKeyboard(ChatActivity.this);

                if (Utils.checkText(input) && Utils.getText(input).length() > 0) {
                    // Read the input field and push a new instance
                    // of ChatMessage to the Firebase database


                    HashMap<String, Object> dataMap = new HashMap<String, Object>();
                    dataMap.put("eUserType", CommonUtilities.app_type);
                    dataMap.put("Text", input.getText().toString().trim());
                    dataMap.put("iTripId", data_trip_ada.get("iTripId"));
                    dataMap.put("driverImageName", driverImgName);
                    dataMap.put("passengerImageName", data_trip_ada.get("FromMemberImageName"));
                    dataMap.put("driverId", generalFunc.getMemberId());
                    dataMap.put("passengerId", data_trip_ada.get("iFromMemberId"));

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


                } else {

                }

            }
        });

        final RecyclerView chatCategoryRecyclerView = (RecyclerView) findViewById(R.id.chatCategoryRecyclerView);


        chatAdapter = new ChatMessagesRecycleAdapter(mContext, chatList, generalFunc, data_trip_ada);
        chatCategoryRecyclerView.setAdapter(chatAdapter);
        chatAdapter.notifyDataSetChanged();

        ((MTextView) findViewById(R.id.titleTxt)).setText(data_trip_ada.get("FromMemberName"));


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

        /*DatabaseReference mainRef = FirebaseDatabase.getInstance().getReference(Utils.FireBaseDataBase);
        com.google.firebase.database.ChildEventListener childEventListener = new com.google.firebase.database.ChildEventListener() {
            @Override
            public void onChildAdded(DataSnapshot dataSnapshot, String previousChildName) {
//                Log.d("Api", "onChildAdded:" + dataSnapshot.getKey());
                count++;
                // A new comment has been added, add it to the displayed list
                ChatMessage comment = dataSnapshot.getValue(ChatMessage.class);
                Log.d("Api", "onChildAdded:" + comment.getMessageId());

                try {
                    if (comment.getMessageId().equals(generalFunc.getMemberId() + "_" + data_trip_ada.get("iTripId") + "_" + CommonUtilities.app_type) || comment.getMessageId().equals(data_trip_ada.get("iFromMemberId") + "_" + data_trip_ada.get("iTripId") + "_Passenger")) {
                        chatList.add(comment);
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                }

                if (count >= dataSnapshot.getChildrenCount()) {
                    //stop progress bar here
                    ((ProgressBar) findViewById(R.id.ProgressBar)).setVisibility(View.GONE);

                }

                chatAdapter.notifyDataSetChanged();
                ((ProgressBar) findViewById(R.id.ProgressBar)).setVisibility(View.GONE);
                chatCategoryRecyclerView.scrollToPosition(chatAdapter.getItemCount() - 1);
            }

            @Override
            public void onChildChanged(DataSnapshot dataSnapshot, String previousChildName) {
//                Log.d("Api", "onChildChanged:" + dataSnapshot.getKey());

                // A comment has changed, use the key to determine if we are displaying this
                // comment and if so displayed the changed comment.
                ChatMessage newComment = dataSnapshot.getValue(ChatMessage.class);
                String commentKey = dataSnapshot.getKey();

                // ...
            }

            @Override
            public void onChildRemoved(DataSnapshot dataSnapshot) {
//                Log.d("Api", "onChildRemoved:" + dataSnapshot.getKey());

                // A comment has changed, use the key to determine if we are displaying this
                // comment and if so remove it.
                String commentKey = dataSnapshot.getKey();

                // ...
            }

            @Override
            public void onChildMoved(DataSnapshot dataSnapshot, String previousChildName) {
//                Log.d("Api", "onChildMoved:" + dataSnapshot.getKey());

                // A comment has changed position, use the key to determine if we are
                // displaying this comment and if so move it.
                ChatMessage movedComment = dataSnapshot.getValue(ChatMessage.class);
                String commentKey = dataSnapshot.getKey();

                // ...
            }

            @Override
            public void onCancelled(DatabaseError databaseError) {
//                Log.w("Api", "postComments:onCancelled", databaseError.toException());
//                Toast.makeText(mContext, "Failed to load comments.", Toast.LENGTH_SHORT).show();
            }
        };
        mainRef.addChildEventListener(childEventListener);*/


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

                Utils.printLog("Response", "::" + responseString);

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
