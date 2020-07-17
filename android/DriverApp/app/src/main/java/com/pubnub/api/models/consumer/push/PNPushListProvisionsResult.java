package com.pubnub.api.models.consumer.push;

import java.util.List;

public class PNPushListProvisionsResult {

    private List<String> channels;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNPushListProvisionsResult(final List<String> channels) {
        this.channels = channels;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNPushListProvisionsResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> channels;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNPushListProvisionsResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPushListProvisionsResultBuilder channels(final List<String> channels) {
            this.channels = channels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPushListProvisionsResult build() {
            return new PNPushListProvisionsResult(channels);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNPushListProvisionsResult.PNPushListProvisionsResultBuilder(channels=" + this.channels + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNPushListProvisionsResultBuilder builder() {
        return new PNPushListProvisionsResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getChannels() {
        return this.channels;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNPushListProvisionsResult(channels=" + this.getChannels() + ")";
    }

}
