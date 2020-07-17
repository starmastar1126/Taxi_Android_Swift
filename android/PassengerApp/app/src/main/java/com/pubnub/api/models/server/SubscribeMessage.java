package com.pubnub.api.models.server;

import com.google.gson.JsonElement;
import com.google.gson.annotations.SerializedName;

public class SubscribeMessage {

    @SerializedName("a")
    private String shard;

    @SerializedName("b")
    private String subscriptionMatch;

    @SerializedName("c")
    private String channel;

    @SerializedName("d")
    private JsonElement payload;

    // TODO: figure me out
    //@SerializedName("ear")
    //private String payload;

    @SerializedName("f")
    private String flags;

    @SerializedName("i")
    private String issuingClientId;

    @SerializedName("k")
    private String subscribeKey;

    //@SerializedName("s")
    //private String sequenceNumber;

    @SerializedName("o")
    private OriginationMetaData originationMetadata;

    @SerializedName("p")
    private PublishMetaData publishMetaData;

    //@SerializedName("r")
    //private Object replicationMap;

    @SerializedName("u")
    private JsonElement userMetadata;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getShard() {
        return this.shard;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSubscriptionMatch() {
        return this.subscriptionMatch;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getChannel() {
        return this.channel;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getPayload() {
        return this.payload;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getFlags() {
        return this.flags;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getIssuingClientId() {
        return this.issuingClientId;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSubscribeKey() {
        return this.subscribeKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public OriginationMetaData getOriginationMetadata() {
        return this.originationMetadata;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PublishMetaData getPublishMetaData() {
        return this.publishMetaData;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getUserMetadata() {
        return this.userMetadata;
    }
    //@SerializedName("w")
    //private String waypointList;
}
