package com.pubnub.api.models.server;

import com.google.gson.JsonElement;

public class HistoryForChannelsItem {

    private JsonElement message;

    private Long timetoken;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setMessage(final JsonElement message) {
        this.message = message;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setTimetoken(final Long timetoken) {
        this.timetoken = timetoken;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getMessage() {
        return this.message;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimetoken() {
        return this.timetoken;
    }
}
