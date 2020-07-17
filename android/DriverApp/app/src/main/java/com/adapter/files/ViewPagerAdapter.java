package com.adapter.files;

import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;

import java.util.ArrayList;

/**
 * Created by Admin on 28-06-2016.
 */
public class ViewPagerAdapter extends FragmentStatePagerAdapter {
    CharSequence titles[];
    ArrayList<Fragment> listOfFragments;

    public ViewPagerAdapter(FragmentManager fm, CharSequence mTitles[], ArrayList<Fragment> listOfFragments) {
        super(fm);

        this.titles = mTitles;
        this.listOfFragments = listOfFragments;
    }

    @Override
    public Fragment getItem(int position) {

        return listOfFragments.get(position);
    }

    @Override
    public CharSequence getPageTitle(int position) {
        return titles[position];
    }

    // This method return the Number of tabs for the tabs Strip

    @Override
    public int getCount() {
        return this.titles.length;
    }
}
