package com.fastcabtaxi.passenger;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.pm.ResolveInfo;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Color;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.Parcelable;
import android.provider.MediaStore;
import android.support.annotation.NonNull;
import android.support.v4.content.FileProvider;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.text.InputType;
import android.text.method.PasswordTransformationMethod;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;

import com.fragments.EditProfileFragment;
import com.fragments.ProfileFragment;
import com.general.files.ExecuteWebServerUrl;
import com.general.files.GeneralFunctions;
import com.general.files.ImageFilePath;
import com.general.files.StartActProcess;
import com.general.files.UploadProfileImage;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.MTextView;
import com.view.SelectableRoundedImageView;
import com.view.editBox.MaterialEditText;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;

public class MyProfileActivity extends AppCompatActivity {

    public static final int MEDIA_TYPE_IMAGE = 1;
    private static final String IMAGE_DIRECTORY_NAME = "Temp";
    private static final int SELECT_PICTURE = 2;
    private static final int CROP_IMAGE = 3;
    private static final int CAMERA_CAPTURE_IMAGE_REQUEST_CODE = 100;
    public GeneralFunctions generalFunc;
    public String userProfileJson = "";
    public String isDriverAssigned = "";
    //ImageView profileback;
    public boolean isEdit = false;
    public boolean isMobile = false;
    public boolean isEmail = false;
    MTextView titleTxt;
    ImageView backImgView;
    SelectableRoundedImageView userProfileImgView;
    SelectableRoundedImageView editIconImgView;
    ImageView profileimageback;
    ProfileFragment profileFrag;
    EditProfileFragment editProfileFrag;
    RelativeLayout userImgArea;
    String SITE_TYPE = "";
    String SITE_TYPE_DEMO_MSG = "";
    Menu menu;
    android.support.v7.app.AlertDialog alertDialog;
    private Uri fileUri;

    public static Bitmap getBitmapFromView(View view) {
        Bitmap bitmap = Bitmap.createBitmap(view.getWidth(), view.getHeight(), Bitmap.Config.ARGB_8888);
        Canvas c = new Canvas(bitmap);
        view.layout(view.getLeft(), view.getTop(), view.getRight(), view.getBottom());
        view.draw(c);
        return bitmap;
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_my_profile);
        Toolbar mToolbar = (Toolbar) findViewById(R.id.toolbar);

        setSupportActionBar(mToolbar);

        generalFunc = new GeneralFunctions(getActContext());

        //userProfileJson = getIntent().getStringExtra("UserProfileJson");
        userProfileJson = generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON);
        isDriverAssigned = getIntent().getStringExtra("isDriverAssigned");
        isEdit = getIntent().getBooleanExtra("isEdit", false);
        isMobile = getIntent().getBooleanExtra("isMobile", false);
        isEmail = getIntent().getBooleanExtra("isEmail", false);

        titleTxt = (MTextView) findViewById(R.id.titleTxt);
        backImgView = (ImageView) findViewById(R.id.backImgView);
        userProfileImgView = (SelectableRoundedImageView) findViewById(R.id.userProfileImgView);
        profileimageback = (ImageView) findViewById(R.id.profileimageback);
        editIconImgView = (SelectableRoundedImageView) findViewById(R.id.editIconImgView);
        userImgArea = (RelativeLayout) findViewById(R.id.userImgArea);

        backImgView.setOnClickListener(new setOnClickList());
        userImgArea.setOnClickListener(new setOnClickList());


        new CreateRoundedView(getResources().getColor(R.color.editBox_primary), Utils.dipToPixels(getActContext(), 15), 0,
                Color.parseColor("#00000000"), editIconImgView);

        editIconImgView.setColorFilter(getResources().getColor(R.color.appThemeColor_TXT_1));

        userProfileImgView.setImageResource(R.mipmap.ic_no_pic_user);
        generalFunc.checkProfileImage(userProfileImgView, userProfileJson, "vImgName", profileimageback);


        SITE_TYPE = generalFunc.getJsonValue("SITE_TYPE", userProfileJson);
        SITE_TYPE_DEMO_MSG = generalFunc.getJsonValue("SITE_TYPE_DEMO_MSG", userProfileJson);


        if (isEdit) {
            openEditProfileFragment();
        } else {
            openProfileFragment();
        }
    }

    public void changePageTitle(String title) {
        titleTxt.setText(title);
    }


    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        switch (requestCode) {
            case GeneralFunctions.MY_PERMISSIONS_REQUEST: {
                if (generalFunc.isPermisionGranted()) {
                    new ImageSourceDialog().run();
                }
                break;

            }
        }
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_MENU) {

            // perform your desired action here

            // return 'true' to prevent further propagation of the key event
            return true;
        }

        // let the system handle all other key events
        return super.onKeyDown(keyCode, event);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        this.menu = menu;
        MenuInflater menuInflater = getMenuInflater();
        menuInflater.inflate(R.menu.my_profile_activity, menu);


        if (editProfileFrag == null) {
            menu.findItem(R.id.menu_edit_profile).setTitle(generalFunc.retrieveLangLBl("", "LBL_EDIT_PROFILE_TXT"));
        } else {
            menu.findItem(R.id.menu_edit_profile).setTitle(generalFunc.retrieveLangLBl("", "LBL_VIEW_PROFILE_TXT"));
        }
        menu.findItem(R.id.menu_change_password).setTitle(generalFunc.retrieveLangLBl("", "LBL_CHANGE_PASSWORD_TXT"));

        Utils.setMenuTextColor(menu.findItem(R.id.menu_edit_profile), getResources().getColor(R.color.appThemeColor_TXT_1));
        Utils.setMenuTextColor(menu.findItem(R.id.menu_change_password), getResources().getColor(R.color.appThemeColor_TXT_1));


        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {

        switch (item.getItemId()) {
            case R.id.menu_edit_profile:
                if (editProfileFrag == null) {
                    if (isDriverAssigned != null) {
                        if (isDriverAssigned.equalsIgnoreCase("true")) {
                            generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("", "LBL_EDIT_PROFILE_BLOCK"));
                        } else {
                            openEditProfileFragment();
                            item.setTitle(generalFunc.retrieveLangLBl("", "LBL_VIEW_PROFILE_TXT"));
                        }
                    } else {
                        openEditProfileFragment();
                        item.setTitle(generalFunc.retrieveLangLBl("", "LBL_VIEW_PROFILE_TXT"));
                    }

                } else {
                    checkEditProfileFrag();
                    item.setTitle(generalFunc.retrieveLangLBl("", "LBL_EDIT_PROFILE_TXT"));
                }
                return true;

            case R.id.menu_change_password:
                showPasswordBox();
                return true;


            default:
                return super.onOptionsItemSelected(item);
        }
    }

    public void showPasswordBox() {
        android.support.v7.app.AlertDialog.Builder builder = new android.support.v7.app.AlertDialog.Builder(getActContext());
        builder.setTitle(generalFunc.retrieveLangLBl("", "LBL_CHANGE_PASSWORD_TXT"));


        LayoutInflater inflater = (LayoutInflater) getActContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View dialogView = inflater.inflate(R.layout.input_box_view, null);

        final String required_str = generalFunc.retrieveLangLBl("", "LBL_FEILD_REQUIRD_ERROR_TXT");
        final String noWhiteSpace = generalFunc.retrieveLangLBl("Password should not contain whitespace.", "LBL_ERROR_NO_SPACE_IN_PASS");
        final String pass_length = generalFunc.retrieveLangLBl("Password must be", "LBL_ERROR_PASS_LENGTH_PREFIX")
                + " " + Utils.minPasswordLength + " " + generalFunc.retrieveLangLBl("or more character long.", "LBL_ERROR_PASS_LENGTH_SUFFIX");
        final String vPassword = generalFunc.getJsonValue("vPassword", userProfileJson);

        final MaterialEditText previous_passwordBox = (MaterialEditText) dialogView.findViewById(R.id.editBox);
        previous_passwordBox.setBothText(generalFunc.retrieveLangLBl("", "LBL_CURR_PASS_HEADER"));
        previous_passwordBox.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_VARIATION_PASSWORD);

        previous_passwordBox.setTransformationMethod(new AsteriskPasswordTransformationMethod());

        if (vPassword.equals("")) {
            previous_passwordBox.setVisibility(View.GONE);
        }

        final MaterialEditText newPasswordBox = (MaterialEditText) inflater.inflate(R.layout.editbox_form_design, null);
        newPasswordBox.setLayoutParams(previous_passwordBox.getLayoutParams());
        newPasswordBox.setId(Utils.generateViewId());


        newPasswordBox.setFloatingLabelText(generalFunc.retrieveLangLBl("", "LBL_UPDATE_PASSWORD_HEADER_TXT"));
        newPasswordBox.setHint(generalFunc.retrieveLangLBl("", "LBL_UPDATE_PASSWORD_HINT_TXT"));
        newPasswordBox.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_VARIATION_PASSWORD);

        newPasswordBox.setTransformationMethod(new AsteriskPasswordTransformationMethod());

        final MaterialEditText reNewPasswordBox = (MaterialEditText) inflater.inflate(R.layout.editbox_form_design, null);
        reNewPasswordBox.setLayoutParams(previous_passwordBox.getLayoutParams());
        reNewPasswordBox.setId(Utils.generateViewId());
        reNewPasswordBox.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_VARIATION_PASSWORD);

        reNewPasswordBox.setFloatingLabelText(generalFunc.retrieveLangLBl("", "LBL_UPDATE_CONFIRM_PASSWORD_HEADER_TXT"));
        reNewPasswordBox.setHint(generalFunc.retrieveLangLBl("", "LBL_UPDATE_CONFIRM_PASSWORD_HINT_TXT"));

        reNewPasswordBox.setTransformationMethod(new AsteriskPasswordTransformationMethod());

        ((LinearLayout) dialogView).addView(newPasswordBox);
        ((LinearLayout) dialogView).addView(reNewPasswordBox);

        builder.setView(dialogView);
        builder.setPositiveButton(generalFunc.retrieveLangLBl("", "LBL_BTN_OK_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {

            }
        });
        builder.setNegativeButton(generalFunc.retrieveLangLBl("", "LBL_CANCEL_TXT"), new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
            }
        });


        alertDialog = builder.create();
        if (generalFunc.isRTLmode() == true) {
            generalFunc.forceRTLIfSupported(alertDialog);
        }

        alertDialog.setCancelable(false);
        alertDialog.setCanceledOnTouchOutside(false);
        alertDialog.show();

        alertDialog.getButton(AlertDialog.BUTTON_POSITIVE).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                boolean isCurrentPasswordEnter = Utils.checkText(previous_passwordBox) ?
                        (Utils.getText(previous_passwordBox).contains(" ") ? Utils.setErrorFields(previous_passwordBox, noWhiteSpace)
                                : (Utils.getText(previous_passwordBox).length() >= Utils.minPasswordLength ? true : Utils.setErrorFields(previous_passwordBox, pass_length)))
                        : Utils.setErrorFields(previous_passwordBox, required_str);

                boolean isNewPasswordEnter = Utils.checkText(newPasswordBox) ?
                        (Utils.getText(newPasswordBox).contains(" ") ? Utils.setErrorFields(newPasswordBox, noWhiteSpace)
                                : (Utils.getText(newPasswordBox).length() >= Utils.minPasswordLength ? true : Utils.setErrorFields(newPasswordBox, pass_length)))
                        : Utils.setErrorFields(newPasswordBox, required_str);

                boolean isReNewPasswordEnter = Utils.checkText(reNewPasswordBox) ?
                        (Utils.getText(reNewPasswordBox).contains(" ") ? Utils.setErrorFields(reNewPasswordBox, noWhiteSpace)
                                : (Utils.getText(reNewPasswordBox).length() >= Utils.minPasswordLength ? true : Utils.setErrorFields(reNewPasswordBox, pass_length)))
                        : Utils.setErrorFields(reNewPasswordBox, required_str);

                if ((!vPassword.equals("") && isCurrentPasswordEnter == false) || isNewPasswordEnter == false || isReNewPasswordEnter == false) {
                    return;
                }

                if (!Utils.getText(newPasswordBox).equals(Utils.getText(reNewPasswordBox))) {
                    Utils.setErrorFields(reNewPasswordBox, generalFunc.retrieveLangLBl("", "LBL_VERIFY_PASSWORD_ERROR_TXT"));
                    return;
                }

                changePassword(Utils.getText(previous_passwordBox), Utils.getText(newPasswordBox));
            }
        });

        alertDialog.getButton(AlertDialog.BUTTON_NEGATIVE).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                alertDialog.dismiss();
            }
        });

    }

    public void changePassword(String currentPassword, String password) {

        if (SITE_TYPE.equals("Demo")) {
            generalFunc.showGeneralMessage("", SITE_TYPE_DEMO_MSG);
            return;
        }

        HashMap<String, String> parameters = new HashMap<String, String>();
        parameters.put("type", "updatePassword");
        parameters.put("UserID", generalFunc.getMemberId());
        parameters.put("pass", password);
        parameters.put("CurrentPassword", currentPassword);
        parameters.put("UserType", CommonUtilities.app_type);

        ExecuteWebServerUrl exeWebServer = new ExecuteWebServerUrl(getActContext(), parameters);
        exeWebServer.setLoaderConfig(getActContext(), true, generalFunc);
        exeWebServer.setDataResponseListener(new ExecuteWebServerUrl.SetDataResponse() {
            @Override
            public void setResponse(String responseString) {

                if (responseString != null && !responseString.equals("")) {

                    boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

                    if (isDataAvail == true) {
                        alertDialog.dismiss();
                        generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                        changeUserProfileJson(generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON));
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

    public Context getActContext() {
        return MyProfileActivity.this;
    }

    public void openProfileFragment() {

        if (profileFrag != null) {
            profileFrag = null;
            Utils.runGC();
        }
        profileFrag = new ProfileFragment();
        getSupportFragmentManager().beginTransaction()
                .replace(R.id.fragContainer, profileFrag).commit();
    }

    public void openEditProfileFragment() {

        if (editProfileFrag != null) {
            editProfileFrag = null;
            Utils.runGC();
        }
        editProfileFrag = new EditProfileFragment();
        getSupportFragmentManager().beginTransaction()
                .replace(R.id.fragContainer, editProfileFrag).commit();
    }

    public boolean checkEditProfileFrag() {
        if (editProfileFrag != null) {
            editProfileFrag = null;
            Utils.runGC();
            openProfileFragment();
            return true;
        }

        return false;
    }

    public EditProfileFragment getEditProfileFrag() {
        return this.editProfileFrag;
    }

    public ProfileFragment getProfileFrag() {
        return this.profileFrag;
    }

    public void changeUserProfileJson(String userProfileJson) {
        this.userProfileJson = userProfileJson;

        Bundle bn = new Bundle();
        //bn.putString("UserProfileJson", userProfileJson);

        generalFunc.storedata(CommonUtilities.WALLET_ENABLE, generalFunc.getJsonValue("WALLET_ENABLE", userProfileJson));
        generalFunc.storedata(CommonUtilities.REFERRAL_SCHEME_ENABLE, generalFunc.getJsonValue("REFERRAL_SCHEME_ENABLE", userProfileJson));


        new StartActProcess(getActContext()).setOkResult(bn);

        checkEditProfileFrag();

        generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("", "LBL_INFO_UPDATED_TXT"));
    }

    private boolean isDeviceSupportCamera() {
        if (getApplicationContext().getPackageManager().hasSystemFeature(PackageManager.FEATURE_CAMERA)) {
            // this device has a camera
            return true;
        } else {
            // no camera on this device
            return false;
        }
    }

    public void chooseFromGallery() {
        Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
        intent.setType("image/*");
        intent.setAction(Intent.ACTION_GET_CONTENT);
        startActivityForResult(Intent.createChooser(intent, "Select Picture"), SELECT_PICTURE);

    }

    public void chooseFromCamera() {

        Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);

        fileUri = getOutputMediaFileUri(MEDIA_TYPE_IMAGE);

        intent.putExtra(MediaStore.EXTRA_OUTPUT, fileUri);

        startActivityForResult(intent, CAMERA_CAPTURE_IMAGE_REQUEST_CODE);
    }

    @Override
    public void onSaveInstanceState(Bundle savedInstanceState) {
        // Save the rider's current state
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

    @Override
    public void onBackPressed() {

        if (checkEditProfileFrag() == true) {
            return;
        }

        super.onBackPressed();
    }

    public View getCurrView() {
        return generalFunc.getCurrentView(MyProfileActivity.this);
    }

    public boolean isValidImageResolution(String path) {
        BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;

        BitmapFactory.decodeFile(path, options);
        int width = options.outWidth;
        int height = options.outHeight;

        if (width >= Utils.ImageUpload_MINIMUM_WIDTH && height >= Utils.ImageUpload_MINIMUM_HEIGHT) {
            return true;
        }
        return false;
    }

    public String[] generateImageParams(String key, String content) {
        String[] tempArr = new String[2];
        tempArr[0] = key;
        tempArr[1] = content;

        return tempArr;
    }

    @SuppressLint("NewApi")
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {

            if (requestCode == CAMERA_CAPTURE_IMAGE_REQUEST_CODE) {
                // successfully captured the image
                // display it in image view
                try {
                    cropImage(fileUri, fileUri);

                } catch (Exception e) {
                    if (fileUri!=null) {
                        generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("Some problem occurred.can't able to get cropped image.so we are uploading original captured image.", "LBL_CROP_ERROR_TXT"));
                        imageUpload(fileUri);
                    }
                    else if (data!=null) {
                        generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("Some problem occurred.can't able to get cropped image.so we are uploading original captured image.", "LBL_CROP_ERROR_TXT"));
                        imageUpload(data.getData());
                    }
                    else
                    {
                        generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("", "LBL_ERROR_OCCURED"));

                    }
                    e.printStackTrace();
                }

            } else if (requestCode == SELECT_PICTURE) {

                try {
                    Uri cropPictureUrl = Uri.fromFile(getOutputMediaFile(MEDIA_TYPE_IMAGE));
                    String realPathFromURI = new ImageFilePath().getPath(getActContext(), data.getData());
                    File file = new File(realPathFromURI == null ? getImageUrlWithAuthority(this, data.getData()) : realPathFromURI);
                    if (file.exists()) {
                        if (Build.VERSION.SDK_INT > 23) {
                            cropImage(FileProvider.getUriForFile(this, this.getApplicationContext().getPackageName() + ".provider", file), cropPictureUrl);

                        } else {
                            cropImage(Uri.fromFile(file), cropPictureUrl);
                        }

                    } else {
                        cropImage(data.getData(), cropPictureUrl);
                    }

                } catch (Exception e) {
                    if (data!=null) {
                        generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("Some problem occurred.can't able to get cropped image.so we are uploading original captured image.", "LBL_CROP_ERROR_TXT"));
                        imageUpload(data.getData());
                    }
                    else
                    {
                        generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("", "LBL_ERROR_OCCURED"));

                    }
                    e.printStackTrace();
                }
            } else if (requestCode == CROP_IMAGE) {
                imageUpload(fileUri);
            }
        } else if (resultCode == RESULT_CANCELED) {
            if (fileUri!=null) {
                generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("Some problem occurred.can't able to get cropped image.so we are uploading original captured image.", "LBL_CROP_ERROR_TXT"));
                imageUpload(fileUri);
            }
            else
            {
                generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("", "LBL_ERROR_OCCURED"));

            }

        } else {
            if (requestCode == CAMERA_CAPTURE_IMAGE_REQUEST_CODE) {
                generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("", "LBL_FAILED_CAPTURE_IMAGE_TXT"));
            }
            else {
                generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("", "LBL_ERROR_OCCURED"));
            }
        }
    }

    public static String getImageUrlWithAuthority(Context context, Uri uri) {
        InputStream is = null;
        if (uri.getAuthority() != null) {
            try {
                is = context.getContentResolver().openInputStream(uri);
                Bitmap bmp = BitmapFactory.decodeStream(is);
                return writeToTempImageAndGetPathUri(context, bmp).toString();
            } catch (FileNotFoundException e) {
                e.printStackTrace();
            } finally {
                try {
                    is.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        }
        return null;
    }

    public static Uri writeToTempImageAndGetPathUri(Context inContext, Bitmap inImage) {
        ByteArrayOutputStream bytes = new ByteArrayOutputStream();
        inImage.compress(Bitmap.CompressFormat.JPEG, 100, bytes);
        String path = MediaStore.Images.Media.insertImage(inContext.getContentResolver(), inImage, Utils.TempProfileImageName, null);
        return Uri.parse(path);
    }


    private void cropImage(final Uri sourceImage, Uri destinationImage) {
       try {
           Intent intent = new Intent("com.android.camera.action.CROP");
           intent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION);
           intent.addFlags(Intent.FLAG_GRANT_WRITE_URI_PERMISSION);

           intent.setType("image/*");

           List<ResolveInfo> list = this.getPackageManager().queryIntentActivities(intent, 0);
           int size = list.size();
           if (size == 0) {
               //Utils.showToast(mContext, mContext.getString(R.string.error_cant_select_cropping_app));
               fileUri = sourceImage;
               intent.putExtra(MediaStore.EXTRA_OUTPUT, sourceImage);
               startActivityForResult(intent, CROP_IMAGE);
               return;
           } else {
               intent.setDataAndType(sourceImage, "image/*");
               intent.putExtra("aspectX", 1);
               intent.putExtra("aspectY", 1);
               intent.putExtra("outputY", 256);
               intent.putExtra("outputX", 256);
               fileUri = destinationImage;
               //intent.putExtra("return-data", true);
               intent.putExtra(MediaStore.EXTRA_OUTPUT, destinationImage);
               if (size == 1) {
                   Intent i = new Intent(intent);
                   ResolveInfo res = list.get(0);
                   i.setComponent(new ComponentName(res.activityInfo.packageName, res.activityInfo.name));
                   startActivityForResult(i, CROP_IMAGE);
               } else {
                   Intent i = new Intent(intent);
                   i.putExtra(Intent.EXTRA_INITIAL_INTENTS, list.toArray(new Parcelable[list.size()]));
                   startActivityForResult(i, CROP_IMAGE);
               }
           }
       }
       catch (Exception e)
       {
           generalFunc.showGeneralMessage("", "Sorry - It seems your device doesn't support the crop/edit action!");
           imageUpload(fileUri);

           e.printStackTrace();
       }
    }


    private void imageUpload(Uri fileUri) {
        if (SITE_TYPE == "Demo" && generalFunc.getJsonValue("vEmail", generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON)).equalsIgnoreCase("Driver@gmail.com")) {
            generalFunc.showGeneralMessage("", SITE_TYPE_DEMO_MSG);
            return;
        }

        if (fileUri==null)
        {
            generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("", "LBL_ERROR_OCCURED"));
            return;
        }

        ArrayList<String[]> paramsList = new ArrayList<>();
        paramsList.add(generateImageParams("iMemberId", generalFunc.getMemberId()));
        paramsList.add(generateImageParams("tSessionId", generalFunc.getMemberId().equals("") ? "" : generalFunc.retrieveValue(Utils.SESSION_ID_KEY)));
        paramsList.add(generateImageParams("GeneralUserType", CommonUtilities.app_type));
        paramsList.add(generateImageParams("GeneralMemberId", generalFunc.getMemberId()));

        paramsList.add(generateImageParams("type", "uploadImage"));

        String selectedImagePath = new ImageFilePath().getPath(getActContext(), fileUri);

        boolean isStoragePermissionAvail = generalFunc.isStoragePermissionGranted();

        if (isValidImageResolution(selectedImagePath) == true && isStoragePermissionAvail) {

            new UploadProfileImage(MyProfileActivity.this, selectedImagePath, Utils.TempProfileImageName, paramsList).execute();
        } else {
            generalFunc.showGeneralMessage("", generalFunc.retrieveLangLBl("Please select image which has minimum is 256 * 256 resolution.", "LBL_MIN_RES_IMAGE"));
        }

    }

    public void handleImgUploadResponse(String responseString) {

        if (responseString != null && !responseString.equals("")) {

            boolean isDataAvail = GeneralFunctions.checkDataAvail(CommonUtilities.action_str, responseString);

            if (isDataAvail == true) {
                generalFunc.storedata(CommonUtilities.USER_PROFILE_JSON, generalFunc.getJsonValue(CommonUtilities.message_str, responseString));
                changeUserProfileJson(generalFunc.retrieveValue(CommonUtilities.USER_PROFILE_JSON));
                generalFunc.checkProfileImage(userProfileImgView, userProfileJson, "vImgName", profileimageback);

//                generalFunc.checkProfileImage(userProfileImgView, userProfileJson, "vImgName");
            } else {
                generalFunc.showGeneralMessage("",
                        generalFunc.retrieveLangLBl("", generalFunc.getJsonValue(CommonUtilities.message_str, responseString)));
            }
        } else {
            generalFunc.showError();
        }
    }

    @Override
    public void onOptionsMenuClosed(Menu menu) {

        setLablesAsPerCurrentFrag(menu);

        super.onOptionsMenuClosed(menu);
    }

    @Override
    public boolean onPrepareOptionsMenu(Menu menu) {

        setLablesAsPerCurrentFrag(menu);

        return super.onPrepareOptionsMenu(menu);
    }

    public void setLablesAsPerCurrentFrag(Menu menu) {
        if (menu != null) {
            if (editProfileFrag == null) {
                menu.findItem(R.id.menu_edit_profile).setTitle(generalFunc.retrieveLangLBl("", "LBL_EDIT_PROFILE_TXT"));
            } else {
                menu.findItem(R.id.menu_edit_profile).setTitle(generalFunc.retrieveLangLBl("", "LBL_VIEW_PROFILE_TXT"));
            }

            Utils.setMenuTextColor(menu.findItem(R.id.menu_edit_profile), getResources().getColor(R.color.appThemeColor_TXT_1));
        }
    }

    public class setOnClickList implements View.OnClickListener {

        @Override
        public void onClick(View view) {
            Utils.hideKeyboard(getActContext());
            switch (view.getId()) {
                case R.id.backImgView:
                    if (checkEditProfileFrag() == true) {
                        return;
                    }
                    MyProfileActivity.super.onBackPressed();
                    break;

                case R.id.userImgArea:
                    if (generalFunc.isCameraStoragePermissionGranted()) {
                        new ImageSourceDialog().run();
                    } else {
                        generalFunc.showMessage(getCurrView(), "Allow this app to use camera.");
                    }

                    break;

            }
        }
    }

    public class AsteriskPasswordTransformationMethod extends PasswordTransformationMethod {
        @Override
        public CharSequence getTransformation(CharSequence source, View view) {
            return new PasswordCharSequence(source);
        }

        private class PasswordCharSequence implements CharSequence {
            private CharSequence mSource;

            public PasswordCharSequence(CharSequence source) {
                mSource = source; // Store char sequence
            }

            public char charAt(int index) {
                return '*'; // This is the important part
            }

            public int length() {
                return mSource.length(); // Return default
            }

            public CharSequence subSequence(int start, int end) {
                return mSource.subSequence(start, end); // Return default
            }
        }
    }

    class ImageSourceDialog implements Runnable {

        @Override
        public void run() {
            // TODO Auto-generated method stub

            final Dialog dialog_img_update = new Dialog(getActContext(), R.style.ImageSourceDialogStyle);

            dialog_img_update.setContentView(R.layout.design_image_source_select);

            MTextView chooseImgHTxt = (MTextView) dialog_img_update.findViewById(R.id.chooseImgHTxt);
            chooseImgHTxt.setText(generalFunc.retrieveLangLBl("Choose Category", "LBL_CHOOSE_CATEGORY"));

            SelectableRoundedImageView cameraIconImgView = (SelectableRoundedImageView) dialog_img_update.findViewById(R.id.cameraIconImgView);
            SelectableRoundedImageView galleryIconImgView = (SelectableRoundedImageView) dialog_img_update.findViewById(R.id.galleryIconImgView);

            ImageView closeDialogImgView = (ImageView) dialog_img_update.findViewById(R.id.closeDialogImgView);

            closeDialogImgView.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    // TODO Auto-generated method stub
                    if (dialog_img_update != null) {
                        dialog_img_update.cancel();
                    }
                }
            });

            new CreateRoundedView(getResources().getColor(R.color.appThemeColor_Dark_1), Utils.dipToPixels(getActContext(), 25), 0,
                    Color.parseColor("#00000000"), cameraIconImgView);

            cameraIconImgView.setColorFilter(getResources().getColor(R.color.appThemeColor_TXT_1));

            new CreateRoundedView(getResources().getColor(R.color.appThemeColor_Dark_1), Utils.dipToPixels(getActContext(), 25), 0,
                    Color.parseColor("#00000000"), galleryIconImgView);

            galleryIconImgView.setColorFilter(getResources().getColor(R.color.appThemeColor_TXT_1));


            cameraIconImgView.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    // TODO Auto-generated method stub
                    if (dialog_img_update != null) {
                        dialog_img_update.cancel();
                    }

                    if (!isDeviceSupportCamera()) {
                        generalFunc.showMessage(getCurrView(), generalFunc.retrieveLangLBl("", "LBL_NOT_SUPPORT_CAMERA_TXT"));
                    } else {
                        chooseFromCamera();
                    }

                }
            });

            galleryIconImgView.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    // TODO Auto-generated method stub
                    if (dialog_img_update != null) {
                        dialog_img_update.cancel();
                    }

                    chooseFromGallery();


                }
            });

            dialog_img_update.setCanceledOnTouchOutside(true);

            Window window = dialog_img_update.getWindow();
            window.setGravity(Gravity.BOTTOM);

            window.setLayout(ViewGroup.LayoutParams.FILL_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);

            dialog_img_update.getWindow().setLayout(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);

            dialog_img_update.show();

        }

    }
}
