package com.pubnub.api.models.server;

import com.google.gson.annotations.SerializedName;

public class SubscribeMetadata {

    @SerializedName("t")
    private Long timetoken;

    @SerializedName("r")
    private String region;


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimetoken() {
        return this.timetoken;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getRegion() {
        return this.region;
    }
}
