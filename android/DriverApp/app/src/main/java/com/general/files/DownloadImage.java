package com.general.files;

import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;

import com.view.RoundedImageView;
import com.view.SelectableRoundedImageView;

import java.io.BufferedInputStream;
import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;

/**
 * Created by Admin on 15-07-2016.
 */
public class DownloadImage extends AsyncTask<Void, Void, Bitmap> {

    private String url;
    private SelectableRoundedImageView imageView;
    Bitmap myBitmap = null;

    public DownloadImage(String url, SelectableRoundedImageView imageView) {
        this.url = url;
        this.imageView = imageView;
    }

    @Override
    protected Bitmap doInBackground(Void... params) {
        try {

            URL aURL = new URL(url);
            URLConnection conn = aURL.openConnection();
            conn.connect();
            InputStream is = conn.getInputStream();
            BufferedInputStream bis = new BufferedInputStream(is);
            myBitmap = BitmapFactory.decodeStream(bis);
            bis.close();
            is.close();
            return myBitmap;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    protected void onPostExecute(Bitmap result) {
        super.onPostExecute(result);
        if (result == null) {

        } else {
            imageView.setImageBitmap(result);
        }
    }

}
