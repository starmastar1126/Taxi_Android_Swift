package com.datepicker.files;

import java.util.Date;

/**
 * <p>This listener class informs the client when the rider sets
 * a date and time by pressing "OK", or cancels the dialog.</p>
 * <p>
 * <p>Overriding {@code onDateTimeCancel()} is optional. The client
 * can override this to listen for when the rider cancels the dialog.
 * This is called when the rider presses the dialog's Cancel button,
 * touches outside the dialog or presses the device's Back button.</p>
 *
 * @author jjobes
 */
public abstract class SlideDateTimeListener {
    /**
     * Informs the client when the rider presses "OK"
     * and selects a date and time.
     *
     * @param date The {@code Date} object that contains the date
     *             and time that the rider has selected.
     */
    public abstract void onDateTimeSet(Date date);

    /**
     * Informs the client when the rider cancels the
     * dialog by pressing Cancel, touching outside
     * the dialog or pressing the Back button.
     * This override is optional.
     */
    public void onDateTimeCancel() {

    }
}
