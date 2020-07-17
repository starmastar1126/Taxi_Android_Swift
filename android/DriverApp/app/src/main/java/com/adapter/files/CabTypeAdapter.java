package com.adapter.files;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.drawable.Drawable;
import android.support.v7.widget.RecyclerView;
import android.util.DisplayMetrics;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import com.fastcabtaxi.driver.R;
import com.general.files.GeneralFunctions;
import com.squareup.picasso.Picasso;
import com.squareup.picasso.Target;
import com.utils.CommonUtilities;
import com.utils.Utils;
import com.view.CreateRoundedView;
import com.view.MTextView;
import com.view.SelectableRoundedImageView;
import com.view.anim.loader.AVLoadingIndicatorView;

import java.util.ArrayList;
import java.util.HashMap;

/**
 * Created by Admin on 04-07-2016.
 */
public class CabTypeAdapter extends RecyclerView.Adapter<CabTypeAdapter.ViewHolder> {

    public GeneralFunctions generalFunc;
    ArrayList<HashMap<String, String>> list_item;
    Context mContext;
    String vehicleIconPath = CommonUtilities.SERVER_URL + "webimages/icons/VehicleType/";
    String vehicleDefaultIconPath = CommonUtilities.SERVER_URL + "webimages/icons/DefaultImg/";

    OnItemClickList onItemClickList;
    ViewHolder viewHolder;

    boolean isFirstRun = true;
    private Target target = new Target() {

        @Override
        public void onBitmapLoaded(Bitmap bitmap, Picasso.LoadedFrom from) {
            viewHolder.loaderView.setVisibility(View.GONE);
            Utils.printLog("Api", "Bitmap" + bitmap);
            Utils.printLog("Api", "from" + from.name());
            // loading of the bitmap was a success
            // TODO do some action with the bitmap
        }


        @Override
        public void onBitmapFailed(Drawable errorDrawable) {
            // loading of the bitmap failed
            viewHolder.loaderView.setVisibility(View.VISIBLE);
            // TODO do some action/warning/error message
        }

        @Override
        public void onPrepareLoad(Drawable placeHolderDrawable) {
            viewHolder.loaderView.setVisibility(View.VISIBLE);
        }
    };

    public CabTypeAdapter(Context mContext, ArrayList<HashMap<String, String>> list_item, GeneralFunctions generalFunc) {
        this.mContext = mContext;
        this.list_item = list_item;
        this.generalFunc = generalFunc;
    }

    @Override
    public CabTypeAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_design_cab_type, parent, false);

        viewHolder = new ViewHolder(view);
        return viewHolder;
    }

    @Override
    public void onBindViewHolder(ViewHolder viewHolder, final int position) {

        setData(viewHolder, position, false);
    }

    public void setData(CabTypeAdapter.ViewHolder viewHolder, final int position, boolean isHover) {
        HashMap<String, String> item = list_item.get(position);

        viewHolder.carTypeTitle.setText(item.get("vVehicleTypeName"));


        isHover = item.get("isHover").equals("true") ? true : false;

        if (item.get("SubTotal") != null && !item.get("SubTotal").equals("")) {
            viewHolder.totalfare.setText(generalFunc.convertNumberWithRTL(item.get("SubTotal")));
        } else {
            viewHolder.infoimage.setVisibility(View.GONE);
            viewHolder.totalfare.setText("");
        }



      /*  Drawable drawable = mContext.getResources().getDrawable(R.mipmap.ic_car_default_hover);
        drawable = DrawableCompat.wrap(drawable);
        DrawableCompat.setTint(drawable.mutate(), mContext.getResources().getColor(R.color.appThemeColor_2));*/

        String imgUrl = "";
        String imgName = "";
        if (isHover) {
            imgName = getImageName(item.get("vLogo1"));
        } else {
            imgName = getImageName(item.get("vLogo"));
        }
//        loadImage(item, viewHolder, );

        if (imgName.equals("")) {
            if (isHover) {
                imgUrl = vehicleDefaultIconPath + "hover_ic_car.png";
            } else {
                imgUrl = vehicleDefaultIconPath + "ic_car.png";
            }
        } else {
            imgUrl = vehicleIconPath + item.get("iVehicleTypeId") + "/android/" + imgName;
        }
        loadImage(viewHolder, imgUrl);
        /*if (isHover == true) {
            Picasso.with(mContext)
                    .load(vehicleIconPath + item.get("iVehicleTypeId") + "/android/" + imageName)
                    .placeholder(R.mipmap.ic_car_default_hover)
                    .error(R.mipmap.ic_car_default_hover)
                    .into(viewHolder.carTypeImgView);
        } else {
            Picasso.with(mContext)
                    .load(vehicleIconPath + item.get("iVehicleTypeId") + "/android/" + imageName)
                    .placeholder(R.mipmap.ic_car_default)
                    .error(R.mipmap.ic_car_default)
                    .into(viewHolder.carTypeImgView);
        }*/


        if (position == 0) {
            viewHolder.leftSeperationLine.setVisibility(View.INVISIBLE);
            viewHolder.leftSeperationLine2.setVisibility(View.INVISIBLE);
        } else {
            viewHolder.leftSeperationLine.setVisibility(View.VISIBLE);
            viewHolder.leftSeperationLine2.setVisibility(View.VISIBLE);
        }

        if (position == list_item.size() - 1) {
            viewHolder.rightSeperationLine.setVisibility(View.INVISIBLE);
            viewHolder.rightSeperationLine2.setVisibility(View.INVISIBLE);
        } else {
            viewHolder.rightSeperationLine.setVisibility(View.VISIBLE);
            viewHolder.rightSeperationLine2.setVisibility(View.VISIBLE);
        }

        viewHolder.contentArea.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (onItemClickList != null) {
                    onItemClickList.onItemClick(position);
                }
            }
        });

        if (isHover == true) {
            viewHolder.imagareaselcted.setVisibility(View.VISIBLE);
            if (!item.get("SubTotal").equals("")) {
                viewHolder.infoimage.setVisibility(View.VISIBLE);
            }
            viewHolder.imagarea.setVisibility(View.GONE);
            viewHolder.carTypeTitle.setTextColor(mContext.getResources().getColor(R.color.appThemeColor_1));
            new CreateRoundedView(mContext.getResources().getColor(R.color.white), Utils.dipToPixels(mContext, 35), 2,
                    mContext.getResources().getColor(R.color.appThemeColor_1), viewHolder.carTypeImgViewselcted);
            // viewHolder.carTypeImgView.setBackgroundColor(Color.parseColor("#FFFFFF"));
            //  viewHolder.carTypeImgViewselcted.setColorFilter(mContext.getResources().getColor(R.color.white));
            viewHolder.carTypeImgViewselcted.setBorderColor(mContext.getResources().getColor(R.color.appThemeColor_1));

        } else {
            viewHolder.imagareaselcted.setVisibility(View.GONE);
            viewHolder.infoimage.setVisibility(View.GONE);
            viewHolder.imagarea.setVisibility(View.VISIBLE);
            viewHolder.carTypeTitle.setTextColor(mContext.getResources().getColor(R.color.appThemeColor_2));
            new CreateRoundedView(Color.parseColor("#ffffff"), Utils.dipToPixels(mContext, 30), 2,
                    Color.parseColor("#cbcbcb"), viewHolder.carTypeImgView);
            // viewHolder.carTypeImgView.setColorFilter(Color.parseColor("#999fa2"));
            viewHolder.carTypeImgView.setBorderColor(Color.parseColor("#cbcbcb"));

        }


    }

    private String getImageName(String vLogo) {
        String imageName = "";

        if (vLogo.equals("")) {
            return vLogo;
        }

        DisplayMetrics metrics = (mContext.getResources().getDisplayMetrics());
        int densityDpi = (int) (metrics.density * 160f);

//        switch (mContext.getResources().getDisplayMetrics().densityDpi) {
        switch (densityDpi) {
            case DisplayMetrics.DENSITY_LOW:
                imageName = "mdpi_" + vLogo;
                break;
            case DisplayMetrics.DENSITY_MEDIUM:
                imageName = "mdpi_" + vLogo;
                break;
            case DisplayMetrics.DENSITY_HIGH:
                imageName = "hdpi_" + vLogo;
                break;

            case DisplayMetrics.DENSITY_TV:
                imageName = "hdpi_" + vLogo;
                break;
            case DisplayMetrics.DENSITY_XHIGH:
                imageName = "xhdpi_" + vLogo;
                break;

            case DisplayMetrics.DENSITY_280:
                imageName = "xhdpi_" + vLogo;
                break;

            case DisplayMetrics.DENSITY_400:
                imageName = "xxhdpi_" + vLogo;
                break;

            case DisplayMetrics.DENSITY_360:
                imageName = "xxhdpi_" + vLogo;
                break;
            case DisplayMetrics.DENSITY_420:
                imageName = "xxhdpi_" + vLogo;
                break;
            case DisplayMetrics.DENSITY_XXHIGH:
                imageName = "xxhdpi_" + vLogo;
                break;
            case DisplayMetrics.DENSITY_XXXHIGH:
                imageName = "xxxhdpi_" + vLogo;
                break;

            case DisplayMetrics.DENSITY_560:
                imageName = "xxxhdpi_" + vLogo;
                break;
            default:
                imageName = "xxhdpi_" + vLogo;
                break;
        }

        return imageName;
    }

    private void loadImage(final CabTypeAdapter.ViewHolder holder, String imageUrl) {

        Picasso.with(mContext)
                .load(imageUrl)
                .into(holder.carTypeImgView, new com.squareup.picasso.Callback() {
                    @Override
                    public void onSuccess() {
                        holder.loaderView.setVisibility(View.GONE);
                    }

                    @Override
                    public void onError() {
                        holder.loaderView.setVisibility(View.VISIBLE);
                    }
                });

        Picasso.with(mContext)
                .load(imageUrl)
                .into(holder.carTypeImgViewselcted, new com.squareup.picasso.Callback() {
                    @Override
                    public void onSuccess() {
                        holder.loaderView.setVisibility(View.GONE);
                    }

                    @Override
                    public void onError() {
                        holder.loaderView.setVisibility(View.VISIBLE);
                    }
                });
    }

    @Override
    public int getItemCount() {
        if (list_item == null) {
            return 0;
        }
        return list_item.size();
    }

    public void setOnItemClickList(OnItemClickList onItemClickList) {
        this.onItemClickList = onItemClickList;
    }

    public void clickOnItem(int position) {
        if (onItemClickList != null) {
            onItemClickList.onItemClick(position);
        }
    }

    public interface OnItemClickList {
        void onItemClick(int position);
    }

    public class ViewHolder extends RecyclerView.ViewHolder {

        public SelectableRoundedImageView carTypeImgView, carTypeImgViewselcted;
        public MTextView carTypeTitle;
        public View leftSeperationLine;
        public View rightSeperationLine;
        public View leftSeperationLine2;
        public View rightSeperationLine2;
        public RelativeLayout contentArea;
        public AVLoadingIndicatorView loaderView, loaderViewselected;
        public MTextView totalfare;

        public FrameLayout imagarea, imagareaselcted;
        public ImageView infoimage;


        public ViewHolder(View view) {
            super(view);

            carTypeImgView = (SelectableRoundedImageView) view.findViewById(R.id.carTypeImgView);
            carTypeImgViewselcted = (SelectableRoundedImageView) view.findViewById(R.id.carTypeImgViewselcted);
            carTypeTitle = (MTextView) view.findViewById(R.id.carTypeTitle);
            leftSeperationLine = view.findViewById(R.id.leftSeperationLine);
            rightSeperationLine = view.findViewById(R.id.rightSeperationLine);
            leftSeperationLine2 = view.findViewById(R.id.leftSeperationLine2);
            rightSeperationLine2 = view.findViewById(R.id.rightSeperationLine2);
            contentArea = (RelativeLayout) view.findViewById(R.id.contentArea);
            loaderView = (AVLoadingIndicatorView) view.findViewById(R.id.loaderView);
            loaderViewselected = (AVLoadingIndicatorView) view.findViewById(R.id.loaderViewselected);
            totalfare = (MTextView) view.findViewById(R.id.totalfare);
            imagarea = (FrameLayout) view.findViewById(R.id.imagarea);
            imagareaselcted = (FrameLayout) view.findViewById(R.id.imagareaselcted);
            infoimage = (ImageView) view.findViewById(R.id.infoimage);
        }
    }
}

