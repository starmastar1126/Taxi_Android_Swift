package com.pubnub.api.models.consumer.access_manager;

import java.util.Map;

public class PNAccessManagerGrantResult {

    private String level;
    private int ttl;
    private String subscribeKey;

    private Map<String, Map<String, PNAccessManagerKeyData>> channels;

    private Map<String, Map<String, PNAccessManagerKeyData>> channelGroups;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNAccessManagerGrantResult(final String level, final int ttl, final String subscribeKey, final Map<String, Map<String, PNAccessManagerKeyData>> channels, final Map<String, Map<String, PNAccessManagerKeyData>> channelGroups) {
        this.level = level;
        this.ttl = ttl;
        this.subscribeKey = subscribeKey;
        this.channels = channels;
        this.channelGroups = channelGroups;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNAccessManagerGrantResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String level;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private int ttl;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String subscribeKey;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Map<String, Map<String, PNAccessManagerKeyData>> channels;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Map<String, Map<String, PNAccessManagerKeyData>> channelGroups;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNAccessManagerGrantResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerGrantResultBuilder level(final String level) {
            this.level = level;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerGrantResultBuilder ttl(final int ttl) {
            this.ttl = ttl;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerGrantResultBuilder subscribeKey(final String subscribeKey) {
            this.subscribeKey = subscribeKey;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerGrantResultBuilder channels(final Map<String, Map<String, PNAccessManagerKeyData>> channels) {
            this.channels = channels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerGrantResultBuilder channelGroups(final Map<String, Map<String, PNAccessManagerKeyData>> channelGroups) {
            this.channelGroups = channelGroups;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerGrantResult build() {
            return new PNAccessManagerGrantResult(level, ttl, subscribeKey, channels, channelGroups);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNAccessManagerGrantResult.PNAccessManagerGrantResultBuilder(level=" + this.level + ", ttl=" + this.ttl + ", subscribeKey=" + this.subscribeKey + ", channels=" + this.channels + ", channelGroups=" + this.channelGroups + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNAccessManagerGrantResultBuilder builder() {
        return new PNAccessManagerGrantResultBuilder();
    }

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
    public Map<String, Map<String, PNAccessManagerKeyData>> getChannels() {
        return this.channels;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, Map<String, PNAccessManagerKeyData>> getChannelGroups() {
        return this.channelGroups;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNAccessManagerGrantResult(level=" + this.getLevel() + ", ttl=" + this.getTtl() + ", subscribeKey=" + this.getSubscribeKey() + ", channels=" + this.getChannels() + ", channelGroups=" + this.getChannelGroups() + ")";
    }

}
