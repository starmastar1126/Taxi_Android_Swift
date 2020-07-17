package com.pubnub.api.builder.dto;

import java.util.List;

public class UnsubscribeOperation {

    private List<String> channels;
    private List<String> channelGroups;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    UnsubscribeOperation(final List<String> channels, final List<String> channelGroups) {
        this.channels = channels;
        this.channelGroups = channelGroups;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class UnsubscribeOperationBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> channels;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> channelGroups;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        UnsubscribeOperationBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public UnsubscribeOperationBuilder channels(final List<String> channels) {
            this.channels = channels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public UnsubscribeOperationBuilder channelGroups(final List<String> channelGroups) {
            this.channelGroups = channelGroups;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public UnsubscribeOperation build() {
            return new UnsubscribeOperation(channels, channelGroups);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "UnsubscribeOperation.UnsubscribeOperationBuilder(channels=" + this.channels + ", channelGroups=" + this.channelGroups + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static UnsubscribeOperationBuilder builder() {
        return new UnsubscribeOperationBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getChannels() {
        return this.channels;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getChannelGroups() {
        return this.channelGroups;
    }
}
