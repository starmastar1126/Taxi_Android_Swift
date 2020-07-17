package com.general.files;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

import com.utils.CommonUtilities;

/**
 * Created by Admin on 12-07-2016.
 */
public class GcmBroadCastReceiver extends BroadcastReceiver {

    ConfigPubNub configPubNub;

    public GcmBroadCastReceiver(ConfigPubNub configPubNub) {
        this.configPubNub = configPubNub;
    }

    @Override
    public void onReceive(Context context, Intent intent) {

        if (intent.getAction().equals(CommonUtilities.driver_message_arrived_intent_action)) {
            final String message = intent.getExtras().getString(CommonUtilities.driver_message_arrived_intent_key);

            if (configPubNub != null && message != null) {

                //Do things..
                configPubNub.dispatchMsg(message);


            }
        }
    }
}
