package com.datepicker.files;

import android.content.Context;
import android.graphics.PorterDuff;
import android.graphics.PorterDuffColorFilter;
import android.graphics.drawable.Drawable;
import android.util.AttributeSet;
import android.widget.NumberPicker;
import android.widget.TimePicker;


import com.fastcabtaxi.driver.R;
import com.utils.Utils;
import java.lang.reflect.Field;


/**
 * A subclass of {@link TimePicker} that uses
 * reflection to allow for customization of the default blue
 * dividers.
 *
 * @author jjobes
 *
 */
public class CustomTimePicker extends TimePicker
{
    private static final String TAG = "CustomTimePicker";

    public CustomTimePicker(Context context, AttributeSet attrs)
    {
        super(context, attrs);

        Class<?> idClass = null;
        Class<?> numberPickerClass = null;
        Field selectionDividerField = null;
        Field hourField = null;
        Field minuteField = null;
        Field amPmField = null;
        NumberPicker hourNumberPicker = null;
        NumberPicker minuteNumberPicker = null;
        NumberPicker amPmNumberPicker = null;

        try
        {
            // Create an instance of the id class
            idClass = Class.forName("com.android.internal.R$id");

            // Get the fields that store the resource IDs for the hour, minute and amPm NumberPickers
            hourField = idClass.getField("hour");
            minuteField = idClass.getField("minute");
            amPmField = idClass.getField("amPm");

            // Use the resource IDs to get references to the hour, minute and amPm NumberPickers
            hourNumberPicker = (NumberPicker) findViewById(hourField.getInt(null));
            minuteNumberPicker = (NumberPicker) findViewById(minuteField.getInt(null));
            amPmNumberPicker = (NumberPicker) findViewById(amPmField.getInt(null));

            numberPickerClass = Class.forName("android.widget.NumberPicker");

            // Set the value of the mSelectionDivider field in the hour, minute and amPm NumberPickers
            // to refer to our custom drawables
//            selectionDividerField = numberPickerClass.getDeclaredField("mSelectionDivider");
//            selectionDividerField.setAccessible(true);
//            selectionDividerField.set(hourNumberPicker, getResources().getDrawable(R.drawable.selection_divider));
//            selectionDividerField.set(minuteNumberPicker, getResources().getDrawable(R.drawable.selection_divider));
//            selectionDividerField.set(amPmNumberPicker, getResources().getDrawable(R.drawable.selection_divider));

            selectionDividerField = numberPickerClass.getDeclaredField("mSelectionDivider");
            selectionDividerField.setAccessible(true);
            Drawable mDrawable = context.getResources().getDrawable(R.drawable.selection_divider);
            mDrawable.setColorFilter(new PorterDuffColorFilter(getResources().getColor(R.color.appThemeColor_1), PorterDuff.Mode.MULTIPLY));

            selectionDividerField.set(hourNumberPicker, mDrawable);
            selectionDividerField.set(minuteNumberPicker, mDrawable);
            selectionDividerField.set(amPmNumberPicker, mDrawable);

        }
        catch (ClassNotFoundException e)
        {
            Utils.printLog(TAG, "ClassNotFoundException in CustomTimePicker"+e.toString());
        }
        catch (NoSuchFieldException e)
        {
            Utils.printLog(TAG, "NoSuchFieldException in CustomTimePicker"+e.toString());
        }
        catch (IllegalAccessException e)
        {
            Utils.printLog(TAG, "IllegalAccessException in CustomTimePicker"+e.toString());
        }
        catch (IllegalArgumentException e)
        {
            Utils.printLog(TAG, "IllegalArgumentException in CustomTimePicker"+e.toString());
        }
    }
}
