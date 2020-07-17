package com.pubnub.api.models.server;

import com.google.gson.annotations.SerializedName;

import lombok.Getter;

@Getter
public class OriginationMetaData {

    @SerializedName("t")
    private Long timetoken;

    @SerializedName("r")
    private Integer region;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimetoken() {
        return this.timetoken;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Integer getRegion() {
        return this.region;
    }

}
