package com.datepicker.files;

import android.content.Context;
import android.graphics.Paint;
import android.graphics.PorterDuff;
import android.graphics.PorterDuffColorFilter;
import android.graphics.drawable.Drawable;
import android.util.AttributeSet;
import android.view.View;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.NumberPicker;

import com.fastcabtaxi.driver.R;
import com.utils.Utils;

import java.lang.reflect.Field;


/**
 * A subclass of {@link DatePicker} that uses
 * reflection to allow for customization of the default blue
 * dividers.
 *
 * @author jjobes
 */
public class CustomDatePicker extends DatePicker {
    private static final String TAG = "CustomDatePicker";


//    public static boolean setNumberPickerTextColor(NumberPicker numberPicker, int color) {
//        final int count = numberPicker.getChildCount();
//        for (int i = 0; i < count; i++) {
//            View child = numberPicker.getChildAt(i);
//            if (child instanceof EditText) {
//                try {
//                    Field selectorWheelPaintField = numberPicker.getClass()
//                            .getDeclaredField("mSelectorWheelPaint");
//                    selectorWheelPaintField.setAccessible(true);
//                    ((Paint) selectorWheelPaintField.get(numberPicker)).setColor(color);
//                    ((EditText) child).setTextColor(color);
//                    numberPicker.invalidate();
//                    return true;
//                } catch (NoSuchFieldException e) {
//
//                } catch (IllegalAccessException e) {
//
//                } catch (IllegalArgumentException e) {
//                }
//            }
//        }
//        return false;
//    }

    public CustomDatePicker(Context context, AttributeSet attrs) {
        super(context, attrs);

        Class<?> idClass = null;
        Class<?> numberPickerClass = null;
        Field selectionDividerField = null;
        Field monthField = null;
        Field dayField = null;
        Field yearField = null;
        NumberPicker monthNumberPicker = null;
        NumberPicker dayNumberPicker = null;
        NumberPicker yearNumberPicker = null;

        try {
            // Create an instance of the id class
            idClass = Class.forName("com.android.internal.R$id");

            // Get the fields that store the resource IDs for the month, day and year NumberPickers
            monthField = idClass.getField("month");
            dayField = idClass.getField("day");
            yearField = idClass.getField("year");

            // Use the resource IDs to get references to the month, day and year NumberPickers
            monthNumberPicker = (NumberPicker) findViewById(monthField.getInt(null));

            dayNumberPicker = (NumberPicker) findViewById(dayField.getInt(null));
            yearNumberPicker = (NumberPicker) findViewById(yearField.getInt(null));

            numberPickerClass = Class.forName("android.widget.NumberPicker");

            // Set the value of the mSelectionDivider field in the month, day and year NumberPickers
            // to refer to our custom drawables
            selectionDividerField = numberPickerClass.getDeclaredField("mSelectionDivider");
            selectionDividerField.setAccessible(true);
            Drawable mDrawable = context.getResources().getDrawable(R.drawable.selection_divider);
            mDrawable.setColorFilter(new PorterDuffColorFilter(getResources().getColor(R.color.appThemeColor_1), PorterDuff.Mode.MULTIPLY));

            selectionDividerField.set(monthNumberPicker, mDrawable);
            selectionDividerField.set(dayNumberPicker, mDrawable);
            selectionDividerField.set(yearNumberPicker, mDrawable);
        } catch (ClassNotFoundException e) {
            Utils.printLog(TAG, "ClassNotFoundException in CustomDatePicker" + e.toString());
        } catch (NoSuchFieldException e) {
            Utils.printLog(TAG, "NoSuchFieldException in CustomDatePicker" + e.toString());
        } catch (IllegalAccessException e) {
            Utils.printLog(TAG, "IllegalAccessException in CustomDatePicker" + e.toString());
        } catch (IllegalArgumentException e) {
            Utils.printLog(TAG, "IllegalArgumentException in CustomDatePicker" + e.toString());
        }
    }
}
