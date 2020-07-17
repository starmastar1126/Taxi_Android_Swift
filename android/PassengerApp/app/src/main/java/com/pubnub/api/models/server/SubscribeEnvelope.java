package com.pubnub.api.models.server;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class SubscribeEnvelope {

    @SerializedName("m")
    private List<SubscribeMessage> messages;

    @SerializedName("t")
    private SubscribeMetadata metadata;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<SubscribeMessage> getMessages() {
        return this.messages;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public SubscribeMetadata getMetadata() {
        return this.metadata;
    }
}
