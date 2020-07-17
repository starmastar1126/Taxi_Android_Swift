package com.pubnub.api.models.server;

import com.google.gson.annotations.SerializedName;

public class PublishMetaData {

    @SerializedName("t")
    private Long publishTimetoken;

    @SerializedName("r")
    private Integer region;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getPublishTimetoken() {
        return this.publishTimetoken;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Integer getRegion() {
        return this.region;
    }
}
