package com.fastcabtaxi.driver;

import android.content.Context;
import android.content.res.AssetFileDescriptor;
import android.graphics.Color;
import android.media.MediaPlayer;
import android.media.Ringtone;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.os.Handler;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.view.WindowManager;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;

import com.general.files.ConfigPubNub;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.MyApp;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.CameraPosition;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.GenerateAlertBox;
import com.view.MTextView;
import com.view.simpleratingbar.SimpleRatingBar;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.File;
import java.io.IOException;
import java.util.HashMap;

@SuppressWarnings("ResourceType")
public class CabRequestedActivity extends AppCompatActivity implements GenerateAlertBox.HandleAlertBtnClick, OnMapReadyCallback {

    public GeneralFunctions generalFunc;
    MTextView leftTitleTxt;
    MTextView rightTitleTxt;
    ProgressBar mProgressBar;
    RelativeLayout progressLayout;
    String message_str;
    MTextView pNameTxtView;
    MTextView locationAddressTxt, ufxlocationAddressTxt;
    MTextView destAddressTxt;
    String pickUpAddress = "";
    String destinationAddress = "";
    ConfigPubNub configPubNub;

    GenerateAlertBox generateAlert;
    int maxProgressValue = 30;
    MediaPlayer mp = new MediaPlayer();
    private MTextView textViewShowTime, ufxtvTimeCount; // will show the time
    private CountDownTimer countDownTimer; // built in android class
    // CountDownTimer
    private long totalTimeCountInMilliseconds = maxProgressValue * 1 * 1000; // total count down time in
    // milliseconds
    private long timeBlinkInMilliseconds = 10 * 1000; // start time of start blinking
    private boolean blink; // controls the blinking .. on and off

    private MTextView locationAddressHintTxt, ufxlocationAddressHintTxt;
    private MTextView destAddressHintTxt;
    private MTextView serviceType, ufxserviceType;

    SimpleRatingBar ratingBar;
    boolean istimerfinish = false;
    String iCabRequestId = "";
    boolean isloadedAddress = false;
    FrameLayout progressLayout_frame, ufxprogressLayout_frame;
    MTextView specialHintTxt, specialValTxt;
    String specialUserComment = "";

    boolean isUfx = false;
    ImageView backImageView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED |
                WindowManager.LayoutParams.FLAG_DISMISS_KEYGUARD |
                WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON |
                WindowManager.LayoutParams.FLAG_TURN_SCREEN_ON |
                WindowManager.LayoutParams.FLAG_ALLOW_LOCK_WHILE_SCREEN_ON);
        setContentView(R.layout.activity_cab_requested);


        generalFunc = new GeneralFunctions(getActContext());
        generalFunc.removeValue(CommonUtilities.DRIVER_ACTIVE_REQ_MSG_KEY);


        Toolbar mToolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(mToolbar);

        MyApp.getInstance().stopAlertService();


        message_str = getIntent().getStringExtra("Message");


        configPubNub = new ConfigPubNub(getActContext(), true);

        String msgCode = generalFunc.getJsonValue("MsgCode", message_str);

        if (generalFunc.containsKey(CommonUtilities.DRIVER_REQ_COMPLETED_MSG_CODE_KEY + msgCode)) {
            // generalFunc.restartApp();
            generalFunc.restartwithGetDataApp();
            return;
        } else {
            generalFunc.storedata(CommonUtilities.DRIVER_REQ_COMPLETED_MSG_CODE_KEY + msgCode, "true");
            generalFunc.storedata(CommonUtilities.DRIVER_REQ_COMPLETED_MSG_CODE_KEY + msgCode, "" + System.currentTimeMillis());
        }
        generalFunc.storedata(CommonUtilities.DRIVER_CURRENT_REQ_OPEN_KEY, "true");

        leftTitleTxt = (MTextView) findViewById(R.id.leftTitleTxt);
        rightTitleTxt = (MTextView) findViewById(R.id.rightTitleTxt);
        pNameTxtView = (MTextView) findViewById(R.id.pNameTxtView);
        locationAddressTxt = (MTextView) findViewById(R.id.locationAddressTxt);
        ufxlocationAddressTxt = (MTextView) findViewById(R.id.ufxlocationAddressTxt);
        locationAddressHintTxt = (MTextView) findViewById(R.id.locationAddressHintTxt);
        ufxlocationAddressHintTxt = (MTextView) findViewById(R.id.ufxlocationAddressHintTxt);
        destAddressHintTxt = (MTextView) findViewById(R.id.destAddressHintTxt);
        destAddressTxt = (MTextView) findViewById(R.id.destAddressTxt);
        progressLayout = (RelativeLayout) findViewById(R.id.progressLayout);
        specialHintTxt = (MTextView) findViewById(R.id.specialHintTxt);
        specialValTxt = (MTextView) findViewById(R.id.specialValTxt);
        backImageView = (ImageView) findViewById(R.id.backImageView);
        backImageView.setVisibility(View.GONE);

        progressLayout_frame = (FrameLayout) findViewById(R.id.progressLayout_frame);
        ufxprogressLayout_frame = (FrameLayout) findViewById(R.id.ufxprogressLayout_frame);


        mProgressBar = (ProgressBar) findViewById(R.id.progressbar);
        ratingBar = (SimpleRatingBar) findViewById(R.id.ratingBar);

        textViewShowTime = (MTextView) findViewById(R.id.tvTimeCount);
        ufxtvTimeCount = (MTextView) findViewById(R.id.ufxtvTimeCount);
        serviceType = (MTextView) findViewById(R.id.serviceType);
        ufxserviceType = (MTextView) findViewById(R.id.ufxserviceType);

        (findViewById(R.id.menuImgView)).setVisibility(View.GONE);
        leftTitleTxt.setVisibility(View.VISIBLE);
        rightTitleTxt.setVisibility(View.VISIBLE);


        maxProgressValue = generalFunc.parseIntegerValue(30, generalFunc.getJsonValue("RIDER_REQUEST_ACCEPT_TIME", generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON)));
        totalTimeCountInMilliseconds = maxProgressValue * 1 * 1000; // total count down time in
        textViewShowTime.setText(maxProgressValue + ":" + "00");
        mProgressBar.setMax(maxProgressValue);
        mProgressBar.setProgress(maxProgressValue);

        setLabels();

        generateAlert = new GenerateAlertBox(getActContext());
        generateAlert.setBtnClickList(this);
        generateAlert.setCancelable(false);

        SupportMapFragment fm = (SupportMapFragment) getSupportFragmentManager()
                .findFragmentById(R.id.mapV2_calling_driver);

        fm.getMapAsync(this);

        setData();

        startTimer();

        progressLayout.setOnClickListener(new setOnClickList());
        leftTitleTxt.setOnClickListener(new setOnClickList());
        rightTitleTxt.setOnClickListener(new setOnClickList());

        if (generalFunc.retrieveValue(CommonUtilities.APP_TYPE).equals("Ride") || generalFunc.retrieveValue(CommonUtilities.APP_TYPE).equals("Delivery")) {
            (findViewById(R.id.requestType)).setVisibility(View.GONE);
        }
    }

    public void setLabels() {
        leftTitleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_DECLINE_TXT"));
        rightTitleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_ACCEPT_TXT"));
        locationAddressHintTxt.setText(generalFunc.retrieveLangLBl("", "LBL_PICKUP_LOCATION_HEADER_TXT"));
        ufxlocationAddressHintTxt.setText(generalFunc.retrieveLangLBl("Job Location", "LBL_JOB_LOCATION_TXT"));
        destAddressHintTxt.setText(generalFunc.retrieveLangLBl("", "LBL_DEST_ADD_TXT"));
        ((MTextView) findViewById(R.id.hintTxt)).setText(generalFunc.retrieveLangLBl("", "LBL_HINT_TAP_TXT"));
        specialHintTxt.setText(generalFunc.retrieveLangLBl("Special Instruction", "LBL_SPECIAL_INSTRUCTION_TXT"));

    }

    public void setData() {

        new CreateRoundedView(Color.parseColor("#000000"), Utils.dipToPixels(getActContext(), 122), 0, Color.parseColor("#FFFFFF"), findViewById(R.id.bgCircle));
        pNameTxtView.setText(generalFunc.getJsonValue("PName", message_str));
        ratingBar.setRating(generalFunc.parseFloatValue(0, generalFunc.getJsonValue("PRating", message_str)));

        double pickupLat = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("sourceLatitude", message_str));
        double pickupLog = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("sourceLongitude", message_str));

        iCabRequestId = generalFunc.getJsonValue("iCabRequestId", message_str);


        double desLat = 0.0;
        double destLog = 0.0;
        if (!generalFunc.getJsonValue("destLatitude", message_str).isEmpty() && !generalFunc.getJsonValue("destLongitude", message_str).isEmpty()) {

            desLat = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("destLatitude", message_str));
            destLog = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("destLongitude", message_str));

            if (desLat == 0.0 && destLog == 0.0) {
                destAddressTxt.setVisibility(View.GONE);
                destAddressHintTxt.setVisibility(View.GONE);
            } else {
                destAddressTxt.setVisibility(View.VISIBLE);
                destAddressHintTxt.setVisibility(View.VISIBLE);
            }
        }
        String serverKey = getResources().getString(R.string.google_api_get_address_from_location_serverApi);
        String url_str = "https://maps.googleapis.com/maps/api/directions/json?origin=" + pickupLat + "," + pickupLog + "&" + "destination=" + (desLat != 0.0 ? desLat : pickupLat) + "," + (destLog != 0.0 ? destLog : pickupLog) + "&sensor=true&key=" + serverKey + "&language=" + generalFunc.retrieveValue(CommonUtilities.GOOGLE_MAP_LANGUAGE_CODE_KEY) + "&sensor=true";


        if (iCabRequestId != null && !iCabRequestId.equals("")) {
            // api call

            getAddressFormServer();
        } else {

            findAddressByDirectionAPI(url_str);

        }


        String REQUEST_TYPE = generalFunc.getJsonValue("REQUEST_TYPE", message_str);


        Utils.printLog("REQUEST_TYPE", REQUEST_TYPE);

        LinearLayout packageInfoArea = (LinearLayout) findViewById(R.id.packageInfoArea);
        if (REQUEST_TYPE.equalsIgnoreCase(Utils.CabGeneralType_UberX)) {
            isUfx = true;
            //if (!generalFunc.getJsonValue("eFareType", message_str).equalsIgnoreCase(Utils.CabFaretypeRegular)) {
            progressLayout_frame.setVisibility(View.GONE);
            locationAddressTxt.setVisibility(View.GONE);
            locationAddressHintTxt.setVisibility(View.GONE);
            destAddressHintTxt.setVisibility(View.GONE);
            destAddressTxt.setVisibility(View.GONE);
            ufxlocationAddressTxt.setVisibility(View.VISIBLE);
            ufxlocationAddressHintTxt.setVisibility(View.VISIBLE);
            ufxprogressLayout_frame.setVisibility(View.VISIBLE);
            specialHintTxt.setVisibility(View.VISIBLE);
            specialValTxt.setVisibility(View.VISIBLE);
            //}

            ((MTextView) findViewById(R.id.requestType)).setText(generalFunc.retrieveLangLBl("Job", "LBL_JOB_TXT") + "  " + generalFunc.retrieveLangLBl("Request", "LBL_REQUEST"));
            (findViewById(R.id.ufxserviceType)).setVisibility(View.VISIBLE);
            ufxserviceType.setText(generalFunc.getJsonValue("SelectedTypeName", message_str));
            packageInfoArea.setVisibility(View.GONE);
        } else if (REQUEST_TYPE.equalsIgnoreCase(Utils.CabGeneralTypeRide_Delivery_UberX)) {
            ((MTextView) findViewById(R.id.requestType)).setText(generalFunc.retrieveLangLBl("Job", "LBL_JOB_TXT") + "  " + generalFunc.retrieveLangLBl("Request", "LBL_REQUEST"));
            (findViewById(R.id.serviceType)).setVisibility(View.VISIBLE);
            serviceType.setText(generalFunc.getJsonValue("SelectedTypeName", message_str));
            packageInfoArea.setVisibility(View.GONE);
        } else if (REQUEST_TYPE.equals("Deliver")) {
            (findViewById(R.id.packageInfoArea)).setVisibility(View.VISIBLE);
            ((MTextView) findViewById(R.id.packageInfoTxt)).setText(generalFunc.getJsonValue("PACKAGE_TYPE", message_str));
            ((MTextView) findViewById(R.id.requestType)).setText(/*generalFunc.retrieveLangLBl("Ride Type", "LBL_RIDE_TYPE") + ": " +*/
                    generalFunc.retrieveLangLBl("Delivery", "LBL_DELIVERY") + " " + generalFunc.retrieveLangLBl("Request", "LBL_REQUEST"));
        } else {
            (findViewById(R.id.packageInfoArea)).setVisibility(View.GONE);
            ((MTextView) findViewById(R.id.requestType)).setText(/*generalFunc.retrieveLangLBl("Ride Type", "LBL_RIDE_TYPE") + ": " +*/
                    generalFunc.retrieveLangLBl("Ride", "LBL_RIDE") + " " + generalFunc.retrieveLangLBl("Request", "LBL_REQUEST"));
        }
    }

    public void getAddressFormServer() {

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "getCabRequestAddress");
        parameters.put("iCabRequestId", iCabRequestId);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);

        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {

                        String MessageJson = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);
                        pickUpAddress = generalFunc.getJsonValue("tSourceAddress", MessageJson);
                        destinationAddress = generalFunc.getJsonValue("tDestAddress", MessageJson);
                        if (isUfx) {
                            if (generalFunc.getJsonValue("tUserComment", MessageJson) != null && !generalFunc.getJsonValue("tUserComment", MessageJson).equals("")) {
                                specialUserComment = generalFunc.getJsonValue("tUserComment", MessageJson);
                                specialValTxt.setText(generalFunc.getJsonValue("tUserComment", MessageJson));
                            } else {
                                specialValTxt.setText("------------");
                            }
                        }

                        isloadedAddress = true;

                        if (destinationAddress.equalsIgnoreCase("")) {
                            destinationAddress = "----";
                        }
                        destAddressTxt.setText(destinationAddress);
                        locationAddressTxt.setText(pickUpAddress);
                        ufxlocationAddressTxt.setText(pickUpAddress);


                    } else {
                        new Handler().postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                getAddressFormServer();
                            }
                        }, 2000);

                    }
                } else {
                    new Handler().postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            getAddressFormServer();
                        }
                    }, 2000);
                }
            }
        });
        exeWebServer.execute();
    }


    public void findAddressByDirectionAPI(final String url) {

        final ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), url, true);


        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {


                if (responseString != null && !responseString.equals("")) {

                    String status = generalFunc.getJsonValue("status", responseString);

                    if (status.equals("OK")) {

                        JSONArray obj_routes = generalFunc.getJsonArray("routes", responseString);
                        if (obj_routes != null && obj_routes.length() > 0) {
                            JSONObject obj_legs = generalFunc.getJsonObject(generalFunc.getJsonArray("legs", generalFunc.getJsonObject(obj_routes, 0).toString()), 0);

                            pickUpAddress = generalFunc.getJsonValue("start_address", obj_legs.toString());
                            destinationAddress = generalFunc.getJsonValue("end_address", obj_legs.toString());

                        }
                        isloadedAddress = true;

                        if (destinationAddress.equalsIgnoreCase("")) {
                            destinationAddress = "----";
                        }
                        destAddressTxt.setText(destinationAddress);
                        locationAddressTxt.setText(pickUpAddress);
                        ufxlocationAddressTxt.setText(pickUpAddress);


                    } else {
                        new Handler().postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                findAddressByDirectionAPI(url);
                            }
                        }, 2000);


                    }

                } else {
                    new Handler().postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            findAddressByDirectionAPI(url);
                        }
                    }, 2000);

                }
            }
        });
        exeWebServer.execute();

    }

    @Override
    protected void onResume() {
        super.onResume();
        if (istimerfinish) {

            finish();
            trimCache(getActContext());
            istimerfinish = false;
            backImageView.setVisibility(View.VISIBLE);
        }


//        playMedia();
    }

    public static void trimCache(Context context) {
        try {
            File dir = context.getCacheDir();
            if (dir != null && dir.isDirectory()) {
                deleteDir(dir);
            }
        } catch (Exception e) {
            // TODO: handle exception
        }
    }

    public static boolean deleteDir(File dir) {
        if (dir != null && dir.isDirectory()) {
            String[] children = dir.list();
            for (int i = 0; i < children.length; i++) {
                boolean success = deleteDir(new File(dir, children[i]));
                if (!success) {
                    return false;
                }
            }
        }
        // The directory is now empty so delete it
        return dir.delete();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        removeCustoNotiSound();
    }

    @Override
    protected void onPause() {
        super.onPause();
        removeSound();
    }

    @Override
    public void handleBtnClick(int btn_id) {
        Utils.hideKeyboard(CabRequestedActivity.this);

        cancelRequest();
    }

    public void acceptRequest() {

        progressLayout.setClickable(false);
        rightTitleTxt.setEnabled(false);
        leftTitleTxt.setEnabled(false);
        generateTrip();
    }

    public void generateTrip() {

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), generateTripParams());
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {


                        if (countDownTimer != null) {
                            countDownTimer.cancel();
                        }

                        removeCustoNotiSound();


                        generalFunc.restartwithGetDataApp();

                    } else {

                        final String msg_str = generalFunc.getJsonValue(CommonUtilities.message_str, responseString);

                        if (countDownTimer != null) {
                            countDownTimer.cancel();
                        }

                        removeCustoNotiSound();

                        GenerateAlertBox alertBox = generalFunc.notifyRestartApp("", generalFunc.retrieveLangLBl("", msg_str));
                        alertBox.setCancelable(false);
                        alertBox.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                            @Override
                            public void handleBtnClick(int btn_id) {
                                if (msg_str.equals(CommonUtilities.GCM_FAILED_KEY) || msg_str.equals(CommonUtilities.APNS_FAILED_KEY) || msg_str.equals("LBL_SERVER_COMM_ERROR")) {
                                    generalFunc.restartwithGetDataApp();

                                } else {
                                    CabRequestedActivity.super.onBackPressed();
                                }


                            }
                        });


                    }
                } else {
                    rightTitleTxt.setEnabled(true);
                    leftTitleTxt.setEnabled(true);
                    generalFunc.showError();
                }
            }
        });
        exeWebServer.execute();
    }

    public void declineTripRequest() {

        HashMap<String, String> parameters = new HashMap<>();
        parameters.put("type", "DeclineTripRequest");
        parameters.put("DriverID", generalFunc.getMemberId());
        parameters.put("PassengerID", generalFunc.getJsonValue("PassengerId", message_str));
        parameters.put("vMsgCode", generalFunc.getJsonValue("MsgCode", message_str));

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                Utils.printLog("Response", "::" + responseString);

                cancelRequest();
            }
        });
        exeWebServer.execute();
    }

    public HashMap<String, String> generateTripParams() {
        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "GenerateTrip");
        parameters.put("DriverID", generalFunc.getMemberId());
        parameters.put("PassengerID", generalFunc.getJsonValue("PassengerId", message_str));
        parameters.put("start_lat", generalFunc.getJsonValue("sourceLatitude", message_str));
        parameters.put("start_lon", generalFunc.getJsonValue("sourceLongitude", message_str));
        parameters.put("iCabBookingId", generalFunc.getJsonValue("iBookingId", message_str));
        parameters.put("iCabRequestId", generalFunc.getJsonValue("iCabRequestId", message_str));
        parameters.put("sAddress", pickUpAddress);
        parameters.put("GoogleServerKey", getResources().getString(R.string.google_api_get_address_from_location_serverApi));
        parameters.put("vMsgCode", generalFunc.getJsonValue("MsgCode", message_str));
        parameters.put("UserType", CommonUtilities.app_type);
//        parameters.put("TimeZone", generalFunc.getTimezone());


        return parameters;
    }

    public void cancelRequest() {
        if (countDownTimer != null) {
            countDownTimer.cancel();
        }
        generalFunc.storedata(CommonUtilities.DRIVER_CURRENT_REQ_OPEN_KEY, "false");

        cancelCabReq();

        try {
            CabRequestedActivity.super.onBackPressed();
        } catch (Exception e) {
            e.printStackTrace();
        }

    }

    private void startTimer() {
//        playMedia();
        countDownTimer = new CountDownTimer(totalTimeCountInMilliseconds, 1000) {
            // 1000 means, onTick function will be called at every 1000
            // milliseconds

            @Override
            public void onTick(long leftTimeInMilliseconds) {
                long seconds = leftTimeInMilliseconds / 1000;
                // i++;
                // Setting the Progress Bar to decrease wih the timer
                mProgressBar.setProgress((int) (leftTimeInMilliseconds / 1000));
                textViewShowTime.setTextAppearance(getActContext(), android.R.color.holo_green_dark);

                if ((seconds % 5) == 0) {
                    try {
                        Uri notification = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
                        Ringtone r = RingtoneManager.getRingtone(getApplicationContext(), notification);
                        r.play();
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
                if (leftTimeInMilliseconds < timeBlinkInMilliseconds) {

                    if (blink) {
                        textViewShowTime.setVisibility(View.VISIBLE);
                        ufxtvTimeCount.setVisibility(View.VISIBLE);
                    } else {
                        textViewShowTime.setVisibility(View.INVISIBLE);
                        ufxtvTimeCount.setVisibility(View.INVISIBLE);
                    }

                    blink = !blink;
                }

                textViewShowTime
                        .setText(String.format("%02d", seconds / 60) + ":" + String.format("%02d", seconds % 60));
                ufxtvTimeCount
                        .setText(String.format("%02d", seconds / 60) + ":" + String.format("%02d", seconds % 60));

            }

            @Override
            public void onFinish() {
                istimerfinish = true;
                textViewShowTime.setVisibility(View.VISIBLE);
//                textViewShowTime.setText("" + generalFunc.retrieveLangLBl("", "LBL_TIMER_FINISHED_TXT"));
                progressLayout.setClickable(false);
                rightTitleTxt.setEnabled(false);
                cancelRequest();
            }

        }.start();

    }


    public void playMedia() {
        removeSound();
        try {
            mp = new MediaPlayer();
            AssetFileDescriptor afd;
            afd = getAssets().openFd("ringtone.mp3");
            mp.setDataSource(afd.getFileDescriptor(), afd.getStartOffset(), afd.getLength());
            mp.prepare();
            mp.setLooping(true);
            mp.start();
        } catch (IllegalStateException e) {
            e.printStackTrace();
        } catch (IOException e) {
            e.printStackTrace();
        }

        //milan code for working all app

//        try { Utils.printLog("MediaPlayer", "MediaPlayer");
//            mp = MediaPlayer.create(getActContext(), R.raw.ringdriver); mp.setLooping(true); mp.start(); }
//        catch (IllegalStateException e) { } catch (Exception e) { }
    }


    private void removeCustoNotiSound() {
        if (mp != null) {
            mp.stop();
            mp = null;
        }


        if (countDownTimer != null) {
            countDownTimer.cancel();
        }

    }

    public void removeSound() {
        if (mp != null) {
            mp.stop();
        }

    }

    public void cancelCabReq() {


        if (configPubNub != null) {
            configPubNub.publishMsg("PASSENGER_" + generalFunc.getJsonValue("PassengerId", message_str),
                    generalFunc.buildRequestCancelJson(generalFunc.getJsonValue("PassengerId", message_str), generalFunc.getJsonValue("MsgCode", message_str)));
            configPubNub = null;
        }
        generalFunc.storedata(CommonUtilities.DRIVER_CURRENT_REQ_OPEN_KEY, "false");
    }

    public Context getActContext() {
        return CabRequestedActivity.this;
    }

    @Override
    public void onBackPressed() {
        cancelCabReq();
        removeCustoNotiSound();
        super.onBackPressed();


    }

    @Override
    public void onMapReady(GoogleMap googleMap) {
        double user_lat = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("sourceLatitude", message_str));
        double user_lon = generalFunc.parseDoubleValue(0.0, generalFunc.getJsonValue("sourceLongitude", message_str));

        googleMap.getUiSettings().setZoomControlsEnabled(false);

        MarkerOptions marker_opt = new MarkerOptions().position(new LatLng(user_lat, user_lon));

        marker_opt.icon(BitmapDescriptorFactory.fromResource(R.drawable.taxi_passanger)).anchor(0.5f, 0.5f);

        googleMap.addMarker(marker_opt);

        CameraPosition cameraPosition = new CameraPosition.Builder().target(new LatLng(user_lat, user_lon))
                .zoom(16).build();

        googleMap.animateCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));

    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(CabRequestedActivity.this);
            switch (view.getId()) {
                case R.id.progressLayout:
                    acceptRequest();
                    break;
                case R.id.leftTitleTxt:
                    //cancelRequest();
                    declineTripRequest();
                    break;
                case R.id.rightTitleTxt:
                    acceptRequest();
                    break;
            }
        }
    }

}
