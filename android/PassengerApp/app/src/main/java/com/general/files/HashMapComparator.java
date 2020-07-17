package com.general.files;

import java.util.Comparator;
import java.util.HashMap;

/**
 * Created by Admin on 11-08-2016.
 */
public class HashMapComparator implements Comparator<HashMap<String, String>> {

    String key;

    public HashMapComparator(String key) {
        this.key = key;
    }

    @Override
    public int compare(HashMap<String, String> map1, HashMap<String, String> map2) {

        if (Double.parseDouble(map1.get(key)) > Double.parseDouble(map2.get(key))) {
            return +1;
        } else if (Double.parseDouble(map1.get(key)) < Double.parseDouble(map2.get(key))) {
            return -1;
        } else {
            return 0;
        }
    }
}
