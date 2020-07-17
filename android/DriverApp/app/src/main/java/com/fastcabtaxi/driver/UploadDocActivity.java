package com.fastcabtaxi.driver;

import android.app.Activity;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.pm.ResolveInfo;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.os.Parcelable;
import android.provider.MediaStore;
import android.support.annotation.NonNull;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.MotionEvent;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.ImageView;

import com.datepicker.files.SlideDateTimeListener;
import com.datepicker.files.SlideDateTimePicker;
import com.general.files.GeneralFunctions;
import com.general.files.ImageFilePath;
import com.general.files.UploadProfileImage;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.GenerateAlertBox;
import com.view.MButton;
import com.view.MTextView;
import com.view.MaterialRippleLayout;
import com.view.editBox.MaterialEditText;

import java.io.File;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Locale;

public class UploadDocActivity extends AppCompatActivity {

    public static final int MEDIA_TYPE_IMAGE = 1;
    private static final String IMAGE_DIRECTORY_NAME = "Temp";
    final int PICK_FILE_REQUEST_CODE = 159;
    MTextView titleTxt;
    ImageView backImgView;
    GeneralFunctions generalFunc;
    MButton btn_type2;
    MTextView helpInfoTxtView;
    ImageView dummyInfoCardImgView;
    private Uri fileUri;

    MaterialEditText expBox;
    FrameLayout expDateSelectArea;

    String selectedDocumentPath = "";

    ImageView imgeselectview;
    public boolean isuploadimageNew = true;

    boolean isbtnclick = false;


    @Override
    public void finishActivity(int requestCode) {
        super.finishActivity(requestCode);
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_upload_doc);

        Toolbar mToolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(mToolbar);

        generalFunc = new GeneralFunctions(getActContext());

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        dummyInfoCardImgView = (ImageView) findViewById(R.id.dummyInfoCardImgView);
        helpInfoTxtView = (MTextView) findViewById(R.id.helpInfoTxtView);
        expBox = (MaterialEditText) findViewById(R.id.expBox);
        imgeselectview = (ImageView) findViewById(R.id.imgeselectview);
        expDateSelectArea = (FrameLayout) findViewById(R.id.expDateSelectArea);

        btn_type2 = ((MaterialRippleLayout) findViewById(R.id.btn_type2)).getChildView();
        backImgView.setOnClickListener(new setOnClickList());

        btn_type2.setId(Utils.generateViewId());
        btn_type2.setOnClickListener(new setOnClickList());
        helpInfoTxtView.setOnClickListener(new setOnClickList());
        dummyInfoCardImgView.setOnClickListener(new setOnClickList());

        setLabels();
    }

    public void setLabels() {
        titleTxt.setText(generalFunc.retrieveLangLBl("", "LBL_UPLOAD_DOC"));
        btn_type2.setText(generalFunc.retrieveLangLBl("", "LBL_BTN_SUBMIT_TXT"));
        helpInfoTxtView.setText(generalFunc.retrieveLangLBl("", "LBL_SELECT_DOC"));
        expBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_EXPIRY_DATE"));

        if (getIntent().getStringExtra("ex_status").equals("yes")) {
            expBox.setText(getIntent().getStringExtra("ex_date"));
            expDateSelectArea.setVisibility(View.VISIBLE);
        } else {
            expDateSelectArea.setVisibility(View.GONE);
        }
        if (!getIntent().getStringExtra("doc_file").equals("")) {
            selectedDocumentPath = getIntent().getStringExtra("doc_file");
            imgeselectview.setVisibility(View.VISIBLE);
           // dummyInfoCardImgView.setAlpha(0.2f);
            dummyInfoCardImgView.setImageDrawable(getResources().getDrawable(R.drawable.default_doc_bg_sel));
            isuploadimageNew = false;
        }


        Utils.removeInput(expBox);
        expBox.setOnTouchListener(new setOnTouchList());
        expBox.setOnClickListener(new setOnClickList());
    }

    public class setOnTouchList implements View.OnTouchListener {

        @Override
        public boolean onTouch(View view, MotionEvent motionEvent) {
            if (motionEvent.getAction() == MotionEvent.ACTION_UP && !view.hasFocus()) {
                view.performClick();
            }
            return true;
        }
    }

    public Context getActContext() {
        return UploadDocActivity.this;
    }

    public void chooseDoc() {

        fileUri = getOutputMediaFileUri(MEDIA_TYPE_IMAGE);

        // Camera.
        final List<Intent> fileIntents = new ArrayList<Intent>();
        final Intent captureIntent = new Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
        final PackageManager packageManager = getPackageManager();
        final List<ResolveInfo> listCam = packageManager.queryIntentActivities(captureIntent, 0);
        for (ResolveInfo res : listCam) {
            final String packageName = res.activityInfo.packageName;
            final Intent intent = new Intent(captureIntent);
            intent.setComponent(new ComponentName(res.activityInfo.packageName, res.activityInfo.name));
            intent.setPackage(packageName);
            intent.putExtra(MediaStore.EXTRA_OUTPUT, fileUri);
            fileIntents.add(intent);
        }

        //Gallery.
        Intent galleryIntent = new Intent(Intent.ACTION_PICK, android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);

        // Filesystem.
        boolean isKitKat = Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT;
        if (isKitKat) {
            Intent intent = new Intent();
            intent.setType("*/*");
            intent.addCategory(Intent.CATEGORY_OPENABLE);
            intent.setAction(Intent.ACTION_GET_CONTENT);
            fileIntents.add(intent);

        } else {
            Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
            intent.setType("*/*");
            intent.addCategory(Intent.CATEGORY_OPENABLE);
            fileIntents.add(intent);
        }

        //Create the Chooser
        final Intent chooserIntent = Intent.createChooser(galleryIntent, "Select Source");
        chooserIntent.putExtra(Intent.EXTRA_INITIAL_INTENTS, fileIntents.toArray(new Parcelable[fileIntents.size()]));

        startActivityForResult(chooserIntent, PICK_FILE_REQUEST_CODE);


    }

    @Override
    public void onSaveInstanceState(Bundle savedInstanceState) {
        // Save the user's current state
        super.onSaveInstanceState(savedInstanceState);
        savedInstanceState.putParcelable("file_uri", fileUri);
    }

    @Override
    protected void onRestoreInstanceState(Bundle savedInstanceState) {
        super.onRestoreInstanceState(savedInstanceState);

        // get the file url
        fileUri = savedInstanceState.getParcelable("file_uri");
    }

    public Uri getOutputMediaFileUri(int type) {
        return Uri.fromFile(getOutputMediaFile(type));
    }

    private File getOutputMediaFile(int type) {

        // External sdcard location
        File mediaStorageDir = new File(Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_PICTURES),
                IMAGE_DIRECTORY_NAME);

        // Create the storage directory if it does not exist
        if (!mediaStorageDir.exists()) {
            if (!mediaStorageDir.mkdirs()) {
                Utils.printLog(IMAGE_DIRECTORY_NAME, "Oops! Failed create " + IMAGE_DIRECTORY_NAME + " directory");
                return null;
            }
        }

        // Create a media file name
        String timeStamp = new SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(new Date());
        File mediaFile;
        if (type == MEDIA_TYPE_IMAGE) {
            mediaFile = new File(mediaStorageDir.getPath() + File.separator + "IMG_" + timeStamp + ".jpg");
        } else {
            return null;
        }

        return mediaFile;
    }

    public void checkData() {

        if (expDateSelectArea.getVisibility() == View.VISIBLE && Utils.checkText(expBox) == false) {
            generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl("Expiry date is required.", "LBL_EXP_DATE_REQUIRED"));
            return;
        }

        if (selectedDocumentPath.equals("")) {
            generalFunc.showMessage(generalFunc.getCurrentView((Activity) getActContext()), generalFunc.retrieveLangLBl("Please attach your document.", "LBL_SELECT_DOC_ERROR"));
            return;
        }

        if (isbtnclick) {
            return;
        }
        isbtnclick = true;
        Handler handler = new Handler();
        handler.postDelayed(new Runnable() {
            @Override
            public void run() {
                isbtnclick = false;
            }
        }, 1000);

        ArrayList<String[]> paramsList = new ArrayList<>();
        paramsList.add(Utils.generateImageParams("type", "uploaddrivedocument"));
        paramsList.add(Utils.generateImageParams("iMemberId", generalFunc.getMemberId()));
        paramsList.add(Utils.generateImageParams("MemberType", CommonUtilities.app_type));
        paramsList.add(Utils.generateImageParams("doc_usertype", getIntent().getStringExtra("PAGE_TYPE")));
        paramsList.add(Utils.generateImageParams("doc_masterid", getIntent().getStringExtra("doc_masterid")));
        paramsList.add(Utils.generateImageParams("doc_name", getIntent().getStringExtra("doc_name")));
        paramsList.add(Utils.generateImageParams("doc_id", getIntent().getStringExtra("doc_id")));
        paramsList.add(Utils.generateImageParams("tSessionId", generalFunc.getMemberId().equals("") ? "" : generalFunc.retrieveValue(Utils.SESSION_ID_KEY)));
        paramsList.add(Utils.generateImageParams("GeneralUserType", CommonUtilities.app_type));
        paramsList.add(Utils.generateImageParams("GeneralMemberId", generalFunc.getMemberId()));
        paramsList.add(Utils.generateImageParams("ex_date", getIntent().getStringExtra("ex_status").equals("yes") ? Utils.getText(expBox) : ""));
        if (!getIntent().getStringExtra("iDriverVehicleId").equals("")) {
            paramsList.add(Utils.generateImageParams("iDriverVehicleId", getIntent().getStringExtra("iDriverVehicleId")));
            // new UploadProfileImage(UploadDocActivity.this, "", Utils.TempProfileImageName, paramsList, "FILE").execute();
        }
        Utils.printLog("parameter::", paramsList.toString());
        Utils.printLog("Extension::", Utils.getFileExt(selectedDocumentPath));
        if (!getIntent().getStringExtra("doc_file").equals("")) {

            if (isuploadimageNew) {
                new UploadProfileImage(UploadDocActivity.this, selectedDocumentPath, "TempFile." + Utils.getFileExt(selectedDocumentPath), paramsList, "FILE").execute();
            } else {
                paramsList.add(Utils.generateImageParams("doc_file", selectedDocumentPath));
                new UploadProfileImage(UploadDocActivity.this, "", "TempFile." + Utils.getFileExt(selectedDocumentPath), paramsList, "FILE").execute();
            }
        } else {
            new UploadProfileImage(UploadDocActivity.this, selectedDocumentPath, "TempFile." + Utils.getFileExt(selectedDocumentPath), paramsList, "FILE").execute();

        }


    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == PICK_FILE_REQUEST_CODE && resultCode == RESULT_OK/* && data != null*/) {

            File file = new File(new ImageFilePath().getPath(getActContext(), fileUri));

            String filePath = "";


            if (file.exists()) {
                String selPath = new ImageFilePath().getPath(getActContext(), fileUri);
                Utils.printLog("selectedImagePath", "Exist:" + selPath);

                filePath = selPath;
            } else if (data != null) {
                Uri selectedImageUri = data.getData();
                String selectedImagePath = new ImageFilePath().getPath(getActContext(), selectedImageUri);

                filePath = selectedImagePath;

                Utils.printLog("selectedImagePath", "::" + selectedImagePath);
            }

            if (filePath != null) {
                if (Utils.getFileExt(filePath).equalsIgnoreCase("jpg") || Utils.getFileExt(filePath).equalsIgnoreCase("gif") || Utils.getFileExt(filePath).equalsIgnoreCase("png")
                        || Utils.getFileExt(filePath).equalsIgnoreCase("jpeg") || Utils.getFileExt(filePath).equalsIgnoreCase("bmp") || Utils.getFileExt(filePath).equalsIgnoreCase("pdf")
                        || Utils.getFileExt(filePath).equalsIgnoreCase("doc") || Utils.getFileExt(filePath).equalsIgnoreCase("docx") || Utils.getFileExt(filePath).equalsIgnoreCase("txt")) {

                    selectedDocumentPath = filePath;
                    imgeselectview.setVisibility(View.VISIBLE);
                    //dummyInfoCardImgView.setAlpha(0.2f);
                    dummyInfoCardImgView.setImageDrawable(getResources().getDrawable(R.drawable.default_doc_bg_sel));
                    isuploadimageNew = true;

                } else {
                    imgeselectview.setVisibility(View.GONE);
                    generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("You have selected wrong file format for document. " +
                            "Valid formats are pdf, doc, docx, jpg, jpeg, gif, png, bmp, xls, xlxs, txt.", "LBL_WRONG_FILE_SELECTED_TXT"));
                }
            } else {
                generalFunc.showMessage(backImgView, generalFunc.retrieveLangLBl("", "LBL_TRY_AGAIN_TXT"));
            }
        }
    }

    public void handleImgUploadResponse(String responseString) {

        if (responseString != null && !responseString.equals("")) {

            boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

            if (isDataAvail == true) {
                final GenerateAlertBox generateAlert = new GenerateAlertBox(getActContext());
                generateAlert.setCancelable(false);
                generateAlert.setBtnClickList(new GenerateAlertBox.HandleAlertBtnClick() {
                    @Override
                    public void handleBtnClick(int btn_id) {
                        generateAlert.closeAlertBox();

                        setResult(RESULT_OK);
                        backImgView.performClick();
                    }
                });
                generateAlert.setContentMessage("", generalFunc.retrieveLangLBl("Your document is uploaded successfully", "LBL_UPLOAD_DOC_SUCCESS"));
                generateAlert.setPositiveBtn(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"));

                generateAlert.showAlertBox();
            } else {
                generalFunc.showGeneralMessage("",
                        generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
            }
        } else {
            generalFunc.showError();
        }
    }

    public void openDateSelection() {

        Utils.printLog("openDateSelection","::"+Calendar.getInstance().getTime());
        new SlideDateTimePicker.Builder(getSupportFragmentManager())
                .setListener(new SlideDateTimeListener() {
                    @Override
                    public void onDateTimeSet(Date date) {
                        expBox.setText(Utils.convertDateToFormat("yyyy-MM-dd", date));
                    }
                })
                .setInitialDate(new Date())
                .setMinDate(Calendar.getInstance().getTime())
                //.setMaxDate(maxDate)
                .setIs24HourTime(true)
                .setTimePickerEnabled(false)
                //.setTheme(SlideDateTimePicker.HOLO_DARK)
                .setIndicatorColor(getResources().getColor(R.color.appThemeColor_2))
                .build()
                .show();
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            int i = view.getId();
            Utils.hideKeyboard(UploadDocActivity.this);
            if (i == R.id.backImgView) {
                UploadDocActivity.super.onBackPressed();
            } else if (i == btn_type2.getId()) {
                checkData();
            } else if (i == helpInfoTxtView.getId() || i == dummyInfoCardImgView.getId()) {
                if (generalFunc.isCameraStoragePermissionGranted()) {
                    chooseDoc();
                }
            } else if (i == R.id.expBox) {
                openDateSelection();

            }
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        switch (requestCode) {
            case GeneralFunctions.MY_PERMISSIONS_REQUEST: {
                if (generalFunc.isPermisionGranted()) {
                    chooseDoc();
                }
                break;

            }
        }
    }
}
