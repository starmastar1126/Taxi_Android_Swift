package com.pubnub.api.models.consumer.channel_group;

import java.util.List;

public class PNChannelGroupsAllChannelsResult {
    private List<String> channels;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNChannelGroupsAllChannelsResult(final List<String> channels) {
        this.channels = channels;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNChannelGroupsAllChannelsResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> channels;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNChannelGroupsAllChannelsResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNChannelGroupsAllChannelsResultBuilder channels(final List<String> channels) {
            this.channels = channels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNChannelGroupsAllChannelsResult build() {
            return new PNChannelGroupsAllChannelsResult(channels);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNChannelGroupsAllChannelsResult.PNChannelGroupsAllChannelsResultBuilder(channels=" + this.channels + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNChannelGroupsAllChannelsResultBuilder builder() {
        return new PNChannelGroupsAllChannelsResultBuilder();
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
        return "PNChannelGroupsAllChannelsResult(channels=" + this.getChannels() + ")";
    }
}
