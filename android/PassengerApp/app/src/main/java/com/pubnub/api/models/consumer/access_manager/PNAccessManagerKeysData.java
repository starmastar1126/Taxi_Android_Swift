package com.pubnub.api.models.consumer.access_manager;

import com.google.gson.annotations.SerializedName;

import java.util.Map;

public class PNAccessManagerKeysData {

    @SerializedName("auths")
    private Map<String, PNAccessManagerKeyData> authKeys;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, PNAccessManagerKeyData> getAuthKeys() {
        return this.authKeys;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNAccessManagerKeysData(authKeys=" + this.getAuthKeys() + ")";
    }

}
