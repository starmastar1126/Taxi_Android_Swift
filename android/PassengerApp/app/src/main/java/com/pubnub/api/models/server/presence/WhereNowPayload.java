package com.pubnub.api.models.server.presence;

import java.util.List;

public class WhereNowPayload {
    private List<String> channels;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public WhereNowPayload() {
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getChannels() {
        return this.channels;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public void setChannels(final List<String> channels) {
        this.channels = channels;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean equals(final java.lang.Object o) {
        if (o == this) return true;
        if (!(o instanceof WhereNowPayload)) return false;
        final WhereNowPayload other = (WhereNowPayload) o;
        if (!other.canEqual((java.lang.Object) this)) return false;
        final java.lang.Object this$channels = this.getChannels();
        final java.lang.Object other$channels = other.getChannels();
        if (this$channels == null ? other$channels != null : !this$channels.equals(other$channels))
            return false;
        return true;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    protected boolean canEqual(final java.lang.Object other) {
        return other instanceof WhereNowPayload;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int hashCode() {
        final int PRIME = 59;
        int result = 1;
        final java.lang.Object $channels = this.getChannels();
        result = result * PRIME + ($channels == null ? 43 : $channels.hashCode());
        return result;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "WhereNowPayload(channels=" + this.getChannels() + ")";
    }
}
