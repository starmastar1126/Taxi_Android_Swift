package com.general.files;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.os.Environment;
import android.widget.ImageView;

import com.fastcabtaxi.driver.R;
import com.utils.Utils;
import com.view.SelectableRoundedImageView;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class DownloadProfileImg extends AsyncTask<String, String, String> {

    SelectableRoundedImageView imgViewProfile;
    String url_img;
    String ImageName;
    Context mContext;
    Bitmap bitmap_profile_icon;
    ImageView profileimageback;

    public DownloadProfileImg(Context mContext, SelectableRoundedImageView imgViewProfile, String url_img, String ImageName) {
        // TODO Auto-generated constructor stub
        this.imgViewProfile = imgViewProfile;
        this.url_img = url_img;
        this.ImageName = ImageName;
        this.mContext = mContext;
    }

    public DownloadProfileImg(Context mContext, SelectableRoundedImageView imgViewProfile, String url_img, String ImageName, ImageView profileimageback) {
        // TODO Auto-generated constructor stub
        this.imgViewProfile = imgViewProfile;
        this.url_img = url_img;
        this.ImageName = ImageName;
        this.mContext = mContext;
        this.profileimageback = profileimageback;
        Utils.printLog("ImageUrl", url_img);
    }


    private Bitmap getBitmap(String url, String imageName) {

        Bitmap bmp_profile = checkImage(imageName);
        if (bmp_profile != null) {
            return bmp_profile;
        }
        // check for above block for storage permission

        // from web
        try {
            Bitmap bitmap = null;
            URL imageUrl = new URL(url);
            HttpURLConnection conn = (HttpURLConnection) imageUrl.openConnection();
            conn.setConnectTimeout(30000);
            conn.setReadTimeout(30000);
            conn.setInstanceFollowRedirects(true);

            boolean redirect = false;

            // normally, 3xx is redirect
            int status = conn.getResponseCode();
            if (status != HttpURLConnection.HTTP_OK) {
                if (status == HttpURLConnection.HTTP_MOVED_TEMP || status == HttpURLConnection.HTTP_MOVED_PERM
                        || status == HttpURLConnection.HTTP_SEE_OTHER)
                    redirect = true;
            }

            if (redirect) {

                // get redirect url from "location" header field
                String newUrl = conn.getHeaderField("Location");

                // get the cookie if need, for login
                String cookies = conn.getHeaderField("Set-Cookie");

                // open the new connnection again
                conn = (HttpURLConnection) new URL(newUrl).openConnection();
                conn.setRequestProperty("Cookie", cookies);
                conn.addRequestProperty("Accept-Language", "en-US,en;q=0.8");
                conn.addRequestProperty("User-Agent", "Mozilla");
                conn.addRequestProperty("Referer", "google.com");

                // System.out.println("Redirect to URL : " + newUrl);

                imageUrl = new URL(newUrl);

                conn = (HttpURLConnection) imageUrl.openConnection();
            }

            InputStream input_stream_profileImg = conn.getInputStream();

            // File bitmap_img_profile_file = SaveIamge(imageName, input_stream_profileImg);

            //bitmap = decodeFile(bitmap_img_profile_file); // check here for storage permission
            BufferedInputStream bufferedInputStream = new BufferedInputStream(input_stream_profileImg);
            bitmap = BitmapFactory.decodeStream(bufferedInputStream);

            if (bitmap == null) {
                bitmap = BitmapFactory.decodeResource(mContext.getResources(), R.mipmap.ic_no_pic_user);
            }

            return bitmap;
        } catch (Exception ex) {
            ex.printStackTrace();
            return null;
        }
    }

    // decodes image and scales it to reduce memory consumption
    private Bitmap decodeFile(File f) {
        try {
            // decode image size
            BitmapFactory.Options o = new BitmapFactory.Options();
            o.inJustDecodeBounds = true;
            BitmapFactory.decodeStream(new FileInputStream(f), null, o);

            final int REQUIRED_SIZE = 70;
            int width_tmp = o.outWidth, height_tmp = o.outHeight;
            int scale = 1;
            while (true) {
                if (width_tmp / 2 < REQUIRED_SIZE || height_tmp / 2 < REQUIRED_SIZE)
                    break;
                width_tmp /= 2;
                height_tmp /= 2;
                scale *= 2;
            }

            // decode with inSampleSize
            BitmapFactory.Options o2 = new BitmapFactory.Options();
            o2.inSampleSize = scale;
            return BitmapFactory.decodeStream(new FileInputStream(f), null, o2);
        } catch (FileNotFoundException e) {
        }
        return null;
    }

    public Bitmap checkImage(String ImageName) {
        Bitmap bmp_profile_img = null;
        String root = Environment.getExternalStorageDirectory().toString();
        File myDir = new File(root + Utils.storedImageFolderName);

        if (!myDir.exists()) {
            myDir.mkdirs();
        }

        File file = new File(myDir, ImageName);
        if (file.exists()) {
            bmp_profile_img = decodeFile(file);
        }

        return bmp_profile_img;
    }

    private File SaveIamge(String name, InputStream in_originalFile) {

        String root = Environment.getExternalStorageDirectory().toString();
        File myDir = new File(root + Utils.storedImageFolderName);
        myDir.mkdirs();

        File file = new File(myDir, name);

        try {

            FileOutputStream out = new FileOutputStream(file);

            byte[] buf = new byte[1024];
            int len;
            while ((len = in_originalFile.read(buf)) > 0) {
                out.write(buf, 0, len);
            }
            in_originalFile.close();
            out.close();

        } catch (Exception e) {
            e.printStackTrace();
        }

        return file;
    }

    @Override
    protected String doInBackground(String... params) {
        // TODO Auto-generated method stub
        bitmap_profile_icon = getBitmap(url_img, ImageName);
        return null;
    }

    @Override
    protected void onPostExecute(String result) {
        // TODO Auto-generated method stub
        super.onPostExecute(result);

        if (bitmap_profile_icon == null) {
            bitmap_profile_icon = BitmapFactory.decodeResource(mContext.getResources(), R.mipmap.ic_no_pic_user);
        }

//        if (bitmap_profile_icon != null) {
//            imgViewProfile.setImageBitmap(bitmap_profile_icon);
//        } else {
//            imgViewProfile.setImageDrawable(mContext.getResources().getDrawable(R.mipmap.ic_no_pic_user));
//        }

        try {
            if (bitmap_profile_icon != null) {
                imgViewProfile.setImageBitmap(bitmap_profile_icon);

                if (profileimageback != null) {
                    Utils.setBlurImage(bitmap_profile_icon,profileimageback);
                }

//                if (profileimageback != null) {
//                    profileimageback.setImageBitmap(bitmap_profile_icon);
//                    Bitmap image = BlurBuilder.blur(profileimageback);
//                    profileimageback.setImageBitmap(image);
//                }
            } else {
                imgViewProfile.setImageDrawable(mContext.getResources().getDrawable(R.mipmap.ic_no_pic_user));
            }
        } catch (Exception e) {

        }

    }

}
