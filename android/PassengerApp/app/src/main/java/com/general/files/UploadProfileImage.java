package com.general.files;

import com.fastcabtaxi.passenger.MyProfileActivity;
import com.rest.RestClient;
import com.utils.Utils;
import com.view.MyProgressDialog;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.RequestBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Created by Admin on 08-07-2016.
 */
public class UploadProfileImage {

    String selectedImagePath;
    String responseString = "";

    String temp_File_Name = "";
    ArrayList<String[]> paramsList;

    MyProfileActivity myProfileAct;
    MyProgressDialog myPDialog;

    public UploadProfileImage(MyProfileActivity myProfileAct, String selectedImagePath, String temp_File_Name, ArrayList<String[]> paramsList) {
        this.selectedImagePath = selectedImagePath;
        this.temp_File_Name = temp_File_Name;
        this.paramsList = paramsList;
        this.myProfileAct = myProfileAct;
    }


    public void execute() {
        myPDialog = new MyProgressDialog(myProfileAct.getActContext(), false, myProfileAct.generalFunc.retrieveLangLBl("Loading", "LBL_LOADING_TXT"));
        try {
            myPDialog.show();
        } catch (Exception e) {

        }

        String filePath = myProfileAct.generalFunc.decodeFile(selectedImagePath, Utils.ImageUpload_DESIREDWIDTH,
                Utils.ImageUpload_DESIREDHEIGHT, temp_File_Name);


        File file = new File(filePath);


        MultipartBody.Part filePart = MultipartBody.Part.createFormData("vImage", temp_File_Name, RequestBody.create(MediaType.parse("multipart/form-data"), file));


        HashMap<String, RequestBody> dataParams = new HashMap<>();

        for (int i = 0; i < paramsList.size(); i++) {
            String[] arrData = paramsList.get(i);

            dataParams.put(arrData[0], RequestBody.create(MediaType.parse("text/plain"), arrData[1]));
        }
        Call<Object> call = RestClient.getClient().uploadData(filePart, dataParams);

        call.enqueue(new Callback<Object>() {

            @Override
            public void onResponse(Call<Object> call, Response<Object> response) {
                if (response.isSuccessful()) {

                    responseString = RestClient.getGSONBuilder().toJson(response.body());

                    fireResponse();
                } else {
                    responseString = "";
                    fireResponse();
                }
            }

            @Override
            public void onFailure(Call<Object> call, Throwable t) {
                Utils.printLog("DataError", "::" + t.getMessage());
                responseString = "";
                fireResponse();
            }

        });

    }

    public void fireResponse() {
        try {
            if (myPDialog != null) {
                myPDialog.close();
            }
        } catch (Exception e) {

        }
        myProfileAct.handleImgUploadResponse(responseString);
    }

}


