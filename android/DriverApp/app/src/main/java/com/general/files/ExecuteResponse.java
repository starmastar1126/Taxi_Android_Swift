package com.general.files;

import com.utils.CommonUtilities;
import com.utils.Utils;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.InputStreamBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;

/**
 * Created by Admin on 29-01-2016.
 */
public class ExecuteResponse {

    public String getResponse(String url_str) {
        String responseString = "";
        HttpURLConnection urlConnection = null;
        try {

            Utils.printLog("System out", "url_str::" + url_str);
            URL url = new URL(url_str);
            urlConnection = (HttpURLConnection) url.openConnection();
            InputStream in = new BufferedInputStream(urlConnection.getInputStream());
            responseString = readStream(in);

        } catch (IOException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
            responseString = "";
        } finally {
            if (urlConnection != null) {
                urlConnection.disconnect();
            }

        }

        return responseString;
    }

    public String readStream(InputStream is) throws IOException {
        StringBuilder sb = new StringBuilder();

        BufferedReader r = new BufferedReader(new InputStreamReader(is), 1000);
        for (String line = r.readLine(); line != null; line = r.readLine()) {
            sb.append(line);
        }
        is.close();
        return sb.toString();
    }

    public String uploadImageAsFile(String sourceFileUri, String fileName, String imageParamKey, ArrayList<String[]> params) {

        String responseString = "";
        InputStream inputStream = null;
        try {
            if (!sourceFileUri.equals("")) {
                inputStream = new FileInputStream(new File(sourceFileUri));
            }
            byte[] data;
            MultipartEntity multipartEntity;
            HttpPost httpPost = null;
            HttpClient httpClient = null;
            try {
                if (!sourceFileUri.equals("")) {
                    data = convertToByteArray(inputStream);

                    httpClient = new DefaultHttpClient();
                    httpPost = new HttpPost(CommonUtilities.SERVER_URL_WEBSERVICE);

                    InputStreamBody inputStreamBody = new InputStreamBody(new ByteArrayInputStream(data), fileName);
                    multipartEntity = new MultipartEntity();
                    FileBody fileBody = new FileBody(new File(sourceFileUri));
                    multipartEntity.addPart(imageParamKey, fileBody);
                } else {
                    httpClient = new DefaultHttpClient();
                    httpPost = new HttpPost(CommonUtilities.SERVER_URL_WEBSERVICE);
                    multipartEntity = new MultipartEntity();
                }

                for (int i = 0; i < params.size(); i++) {
                    String[] paramsArr = params.get(i);
                    multipartEntity.addPart(paramsArr[0], new StringBody(paramsArr[1]));
                }

                httpPost.setEntity(multipartEntity);


                HttpResponse httpResponse = httpClient.execute(httpPost);

                // Handle response back from script.
                if (httpResponse != null) {

                    Utils.printLog("success", "success:" + httpResponse.toString());
                    responseString = EntityUtils.toString(httpResponse.getEntity());

                } else { // Error, no response.

                    Utils.printLog("Failed", "failed:" + httpResponse.toString());
                }
            } catch (IOException e) {
                e.printStackTrace();
            }
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }

        return responseString;
    }

    private byte[] convertToByteArray(InputStream inputStream) throws IOException {

        ByteArrayOutputStream bos = new ByteArrayOutputStream();

        int next = inputStream.read();
        while (next > -1) {
            bos.write(next);
            next = inputStream.read();
        }

        bos.flush();

        return bos.toByteArray();
    }
}
