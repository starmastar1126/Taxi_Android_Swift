package com.pubnub.api.models.server;

import com.google.gson.JsonElement;

public class PresenceEnvelope {

    private String action;
    private String uuid;
    private Integer occupancy;
    private Long timestamp;
    private JsonElement data;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getAction() {
        return this.action;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getUuid() {
        return this.uuid;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Integer getOccupancy() {
        return this.occupancy;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimestamp() {
        return this.timestamp;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getData() {
        return this.data;
    }
}
