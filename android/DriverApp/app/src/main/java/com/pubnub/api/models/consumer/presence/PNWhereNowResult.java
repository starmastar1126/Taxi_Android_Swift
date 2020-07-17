package com.pubnub.api.models.consumer.presence;

import java.util.List;

public class PNWhereNowResult {
    private List<String> channels;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNWhereNowResult(final List<String> channels) {
        this.channels = channels;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNWhereNowResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> channels;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNWhereNowResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNWhereNowResultBuilder channels(final List<String> channels) {
            this.channels = channels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNWhereNowResult build() {
            return new PNWhereNowResult(channels);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNWhereNowResult.PNWhereNowResultBuilder(channels=" + this.channels + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNWhereNowResultBuilder builder() {
        return new PNWhereNowResultBuilder();
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
        return "PNWhereNowResult(channels=" + this.getChannels() + ")";
    }
}
