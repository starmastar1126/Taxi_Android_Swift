package com.pubnub.api.models.server.access_manager;

import com.google.gson.JsonElement;
import com.google.gson.annotations.SerializedName;
import com.pubnub.api.models.consumer.access_manager.PNAccessManagerKeyData;
import com.pubnub.api.models.consumer.access_manager.PNAccessManagerKeysData;

import java.util.Map;

import lombok.Getter;

@Getter
public class AccessManagerGrantPayload {

    @SerializedName("level")
    private String level;

    private int ttl;

    @SerializedName("subscribe_key")
    private String subscribeKey;

    @SerializedName("channels")
    private Map<String, PNAccessManagerKeysData> channels;

    @SerializedName("channel-groups")
    private JsonElement channelGroups;

    @SerializedName("auths")
    private Map<String, PNAccessManagerKeyData> authKeys;

    @SerializedName("channel")
    private String channel;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getLevel() {
        return this.level;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getTtl() {
        return this.ttl;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSubscribeKey() {
        return this.subscribeKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, PNAccessManagerKeysData> getChannels() {
        return this.channels;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getChannelGroups() {
        return this.channelGroups;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, PNAccessManagerKeyData> getAuthKeys() {
        return this.authKeys;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getChannel() {
        return this.channel;
    }
}
