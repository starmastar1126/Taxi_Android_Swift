package com.view.pinnedListView;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.SectionIndexer;
import android.widget.TextView;

import com.fastcabtaxi.driver.R;

import java.util.ArrayList;

public class PinnedCategorySectionListAdapter extends BaseAdapter implements PinnedSectionListView.PinnedSectionListAdapter, SectionIndexer {

    private CategoryListItem[] sections;

    private LayoutInflater inflater;

    Context mContext;
    ArrayList<CategoryListItem> categoryListItems;

    CountryClick countryClickList;

    boolean isStateList = false;

    public PinnedCategorySectionListAdapter(Context mContext, ArrayList<CategoryListItem> categoryListItems, CategoryListItem[] sections) {
        // TODO Auto-generated constructor stub
        this.mContext = mContext;
        this.categoryListItems = categoryListItems;
        this.sections = sections;
    }

    public void isStateList(boolean value) {
        this.isStateList = value;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (inflater == null)
            inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);

        if (convertView == null)
            convertView = inflater.inflate(R.layout.country_list_item, null);

        TextView txt_view = (TextView) convertView.findViewById(R.id.txt);
        //TextView txt_count = (TextView) convertView.findViewById(R.id.txt_count);

        ImageView rightImage = (ImageView) convertView.findViewById(R.id.rightImage);
        rightImage.setVisibility(View.VISIBLE);

        txt_view.setTextColor(Color.BLACK);
        txt_view.setTag("" + position);
        final CategoryListItem categoryListItem = categoryListItems.get(position);


        if (categoryListItem.type == CountryListItem.SECTION) {

//			convertView.setBackgroundResource(R.drawable.bg_header_country_list);
            convertView.setBackgroundColor(mContext.getResources().getColor(R.color.appThemeColor_1));
//			convertView.setAlpha((float) 0.96);
            txt_view.setClickable(false);
            txt_view.setEnabled(false);
            txt_view.setText(categoryListItem.getvTitle());
            txt_view.setTextColor(Color.parseColor("#FFFFFF"));
            txt_view.setText(categoryListItem.text);
            rightImage.setVisibility(View.GONE);

        } else {
            txt_view.setText(categoryListItem.getvTitle());
            txt_view.setClickable(true);
            txt_view.setEnabled(true);
//			txt_count.setVisibility(View.GONE);

        }

        txt_view.setOnClickListener(new OnClickListener() {

            @Override
            public void onClick(View v) {
                // TODO Auto-generated method stub
//				Toast.makeText(mContext, "hi--" + countryListItem.text, Toast.LENGTH_LONG).show();
                if (countryClickList != null) {
                    countryClickList.countryClickList(categoryListItem);
                }
            }
        });

        return convertView;
    }

    public interface CountryClick {
        void countryClickList(CategoryListItem countryListItem);
    }

    public void setCountryClickListener(CountryClick countryClickList) {
        this.countryClickList = countryClickList;
    }

    @Override
    public int getViewTypeCount() {
        return 2;
    }

    @Override
    public CategoryListItem[] getSections() {
        return sections;
    }

    @Override
    public int getPositionForSection(int section) {
        if (section >= sections.length) {
            section = sections.length - 1;
        }
        return sections[section].listPosition;
    }

    @Override
    public int getSectionForPosition(int position) {
        if (position >= getCount()) {
            position = getCount() - 1;
        }
        return categoryListItems.get(position).sectionPosition;
    }

    @Override
    public int getItemViewType(int position) {
        return categoryListItems.get(position).type;
    }

    @Override
    public boolean isItemViewTypePinned(int viewType) {
        return viewType == CountryListItem.SECTION;
    }

    @Override
    public int getCount() {
        // TODO Auto-generated method stub
        return categoryListItems.size();
    }

    @Override
    public Object getItem(int position) {
        // TODO Auto-generated method stub
        return categoryListItems.get(position);
    }

    @Override
    public long getItemId(int position) {
        // TODO Auto-generated method stub
        return position;
    }

}
