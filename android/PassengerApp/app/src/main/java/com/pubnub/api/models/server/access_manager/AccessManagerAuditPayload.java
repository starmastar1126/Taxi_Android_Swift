package com.pubnub.api.models.server.access_manager;

import com.google.gson.annotations.SerializedName;
import com.pubnub.api.models.consumer.access_manager.PNAccessManagerKeyData;

import java.util.Map;

public class AccessManagerAuditPayload {

    @SerializedName("level")
    private String level;

    @SerializedName("subscribe_key")
    private String subscribeKey;

    @SerializedName("channel")
    private String channel;

    @SerializedName("channel-group")
    private String channelGroup;

    @SerializedName("auths")
    private Map<String, PNAccessManagerKeyData> authKeys;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getLevel() {
        return this.level;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSubscribeKey() {
        return this.subscribeKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getChannel() {
        return this.channel;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getChannelGroup() {
        return this.channelGroup;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, PNAccessManagerKeyData> getAuthKeys() {
        return this.authKeys;
    }

}
