package com.adapter.files;

import android.app.Activity;
import android.content.Context;
import android.support.v4.content.ContextCompat;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;

import com.fastcabtaxi.passenger.R;
import com.general.files.GeneralFunctions;
import com.view.MTextView;

import java.util.ArrayList;

/**
 * Created by Admin on 01-07-2016.
 */
public class DrawerAdapter extends BaseAdapter {
    public static View view;
    static Context mContext;
    ArrayList<String[]> list_item;
    GeneralFunctions generalFunc;

    public DrawerAdapter(ArrayList<String[]> list_item, Context mContext) {
        this.list_item = list_item;
        this.mContext = mContext;

        generalFunc = new GeneralFunctions(mContext);
    }


    @Override
    public int getCount() {
        return list_item.size();
    }

    @Override
    public Object getItem(int i) {
        return list_item.get(i);
    }

    @Override
    public long getItemId(int i) {
        return i;
    }

    @Override
    public View getView(int i, View view, ViewGroup viewGroup) {

        LayoutInflater mInflater = (LayoutInflater)
                mContext.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);
        view = mInflater.inflate(R.layout.drawer_list_item, null);

        this.view = view;


        ImageView menuIcon = (ImageView) view.findViewById(R.id.menuIcon);
        MTextView menuTitleTxt = (MTextView) view.findViewById(R.id.menuTitleTxt);
        menuIcon.setImageResource(generalFunc.parseIntegerValue(0, list_item.get(i)[0]));
        menuTitleTxt.setText(list_item.get(i)[1]);
        LinearLayout mainarea = (LinearLayout) view.findViewById(R.id.mainarea);
        mainarea.setBackgroundColor(mContext.getResources().getColor(R.color.white));


        menuTitleTxt.setTextColor(mContext.getResources().getColor(R.color.appThemeColor_2));
        menuIcon.setColorFilter(ContextCompat.getColor(mContext, R.color.appThemeColor_2));


        return view;
    }
}
