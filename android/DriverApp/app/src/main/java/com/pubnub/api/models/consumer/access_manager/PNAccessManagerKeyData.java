package com.pubnub.api.models.consumer.access_manager;

import com.google.gson.annotations.SerializedName;

import lombok.Getter;
import lombok.Setter;
import lombok.ToString;
import lombok.experimental.Accessors;


@Accessors(chain = true)
public class PNAccessManagerKeyData {

    @SerializedName("r")
    private boolean readEnabled;

    @SerializedName("w")
    private boolean writeEnabled;

    @SerializedName("m")
    private boolean manageEnabled;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isReadEnabled() {
        return this.readEnabled;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isWriteEnabled() {
        return this.writeEnabled;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isManageEnabled() {
        return this.manageEnabled;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNAccessManagerKeyData setReadEnabled(final boolean readEnabled) {
        this.readEnabled = readEnabled;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNAccessManagerKeyData setWriteEnabled(final boolean writeEnabled) {
        this.writeEnabled = writeEnabled;
        return this;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public PNAccessManagerKeyData setManageEnabled(final boolean manageEnabled) {
        this.manageEnabled = manageEnabled;
        return this;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNAccessManagerKeyData(readEnabled=" + this.isReadEnabled() + ", writeEnabled=" + this.isWriteEnabled() + ", manageEnabled=" + this.isManageEnabled() + ")";
    }

}
