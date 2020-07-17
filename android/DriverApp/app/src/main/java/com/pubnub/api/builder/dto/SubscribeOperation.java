package com.pubnub.api.builder.dto;

import java.util.List;

import lombok.Builder;
import lombok.Getter;

@Builder
@Getter
public class SubscribeOperation {

    private List<String> channels;
    private List<String> channelGroups;
    private boolean presenceEnabled;
    private Long timetoken;

    //    @java.beans.ConstructorProperties({"channels", "channelGroups", "presenceEnabled", "timetoken"})
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    SubscribeOperation(final List<String> channels, final List<String> channelGroups, final boolean presenceEnabled, final Long timetoken) {
        this.channels = channels;
        this.channelGroups = channelGroups;
        this.presenceEnabled = presenceEnabled;
        this.timetoken = timetoken;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class SubscribeOperationBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> channels;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> channelGroups;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private boolean presenceEnabled;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Long timetoken;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        SubscribeOperationBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public SubscribeOperationBuilder channels(final List<String> channels) {
            this.channels = channels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public SubscribeOperationBuilder channelGroups(final List<String> channelGroups) {
            this.channelGroups = channelGroups;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public SubscribeOperationBuilder presenceEnabled(final boolean presenceEnabled) {
            this.presenceEnabled = presenceEnabled;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public SubscribeOperationBuilder timetoken(final Long timetoken) {
            this.timetoken = timetoken;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public SubscribeOperation build() {
            return new SubscribeOperation(channels, channelGroups, presenceEnabled, timetoken);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "SubscribeOperation.SubscribeOperationBuilder(channels=" + this.channels + ", channelGroups=" + this.channelGroups + ", presenceEnabled=" + this.presenceEnabled + ", timetoken=" + this.timetoken + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static SubscribeOperationBuilder builder() {
        return new SubscribeOperationBuilder();
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

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public boolean isPresenceEnabled() {
        return this.presenceEnabled;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimetoken() {
        return this.timetoken;
    }
}
