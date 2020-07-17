package com.general.files;

import android.app.Dialog;
import android.content.Context;
import android.widget.EditText;

import com.adapter.files.ChatMessage;
import com.adapter.files.ChatMessagesRecycleAdapter;
import com.google.firebase.auth.FirebaseUser;
import com.google.firebase.database.DatabaseReference;
import com.utils.Utils;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 21-07-2016.
 */
public class OpenChatDetailDialog {
    Context mContext;
    HashMap<String, String> data_trip;
    GeneralFunctions generalFunc;
    String isFrom = "";

    ArrayList<ChatMessage> chatList;
    private int count=0;

    public OpenChatDetailDialog(Context mContext, HashMap<String, String> data_trip, GeneralFunctions generalFunc, String fromWhichScreen) {
        this.mContext = mContext;
        this.data_trip = data_trip;
        this.generalFunc = generalFunc;
        this.isFrom = fromWhichScreen;
        chatList=new ArrayList<>();
        count = 0;
        show();
    }


    public void show() {

        /*dialogMessages = new Dialog(mContext, R.style.Theme_Dialog);
        dialogMessages.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogMessages.getWindow().setBackgroundDrawable(new ColorDrawable(Color.LTGRAY));

        dialogMessages.setContentView(R.layout.design_trip_chat_detail_dialog);


        ImageView msgbtn = (ImageView) dialogMessages.findViewById(R.id.msgbtn);
        input = (EditText) dialogMessages.findViewById(R.id.input);

        input.setHint(generalFunc.retrieveLangLBl("Enter a message", "LBL_ENTER_MSG_TXT"));


        (dialogMessages.findViewById(R.id.backImgView)).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (dialogMessages != null) {
                    dialogMessages.dismiss();
                }
            }
        });

        msgbtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {


                if (Utils.checkText(input)) {
                    // Read the input field and push a new instance
                    // of ChatMessage to the Firebase database
                    FirebaseDatabase.getInstance()
                            .getReference("cubetaxiplus-app")
                            .push()
                            .setValue(new ChatMessage(input.getText().toString().trim(),
                                    data_trip.get("PName"), generalFunc.getMemberId() + "_" + data_trip.get("TripId") + "_" + CommonUtilities.app_type)
                            );


                    sendTripMessageNotification(input.getText().toString().trim());

                    // Clear the input
                    input.setText("");


                } else {

                }

            }
        });
        dialogMessages.setOnKeyListener(new DialogInterface.OnKeyListener() {
            @Override
            public boolean onKey(DialogInterface dialog, int keyCode, KeyEvent event) {
                if (keyCode == KeyEvent.KEYCODE_BACK) {
                    dialog.cancel();
                    return true;
                }
                return false;
            }
        });

        mFirebaseUser = FirebaseAuth.getInstance().getCurrentUser();
        mDatabase = FirebaseDatabase.getInstance().getReference("cubetaxiplus-app");

        final RecyclerView chatCategoryRecyclerView = (RecyclerView)dialogMessages.findViewById(R.id.chatCategoryRecyclerView);


        chatAdapter = new ChatMessagesRecycleAdapter(mContext, chatList, generalFunc,data_trip);
        chatCategoryRecyclerView.setAdapter(chatAdapter);
        chatAdapter.notifyDataSetChanged();

        ((MTextView) dialogMessages.findViewById(R.id.titleTxt)).setText(data_trip.get("PName"));

        DatabaseReference mainRef = FirebaseDatabase.getInstance().getReference("cubetaxiplus-app");
        com.google.firebase.database.ChildEventListener childEventListener = new com.google.firebase.database.ChildEventListener() {
            @Override
            public void onChildAdded(DataSnapshot dataSnapshot, String previousChildName) {
//                Log.d("Api", "onChildAdded:" + dataSnapshot.getKey());
                count++;
                // A new comment has been added, add it to the displayed list
                ChatMessage comment = dataSnapshot.getValue(ChatMessage.class);
                try
                {
                    if (comment.getMessageId().equals(generalFunc.getMemberId() + "_" + data_trip.get("TripId") + "_" + CommonUtilities.app_type) || comment.getMessageId().equals(data_trip.get("iFromMemberId") + "_" + data_trip.get("TripId") + "_Passenger")) {
                        chatList.add(comment);
                    }
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }

                if(count >= dataSnapshot.getChildrenCount()){
                    //stop progress bar here
                    ((ProgressBar) dialogMessages.findViewById(R.id.ProgressBar)).setVisibility(View.GONE);

                }
                chatAdapter.notifyDataSetChanged();
                chatCategoryRecyclerView.scrollToPosition(chatAdapter.getItemCount()-1);
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
        mainRef.addChildEventListener(childEventListener);


        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(dialogMessages);
        }
        dialogMessages.setCancelable(false);
        dialogMessages.setCanceledOnTouchOutside(false);
        dialogMessages.show();*/
    }


    public void sendTripMessageNotification(String message) {

        HashMap<String, String> parameters = new HashMap<>();
        parameters.put("type", "SendTripMessageNotification");
        parameters.put("UserType", Utils.userType);
        parameters.put("iFromMemberId", generalFunc.getMemberId());
        parameters.put("iTripId", data_trip.get("iTripId"));
        parameters.put("iToMemberId", data_trip.get("PassengerId"));
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
