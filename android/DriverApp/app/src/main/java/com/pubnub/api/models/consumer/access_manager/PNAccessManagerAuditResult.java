package com.pubnub.api.models.consumer.access_manager;

import java.util.Map;

public class PNAccessManagerAuditResult {

    private String level;
    private String subscribeKey;

    private String channel;

    private String channelGroup;

    private Map<String, PNAccessManagerKeyData> authKeys;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNAccessManagerAuditResult(final String level, final String subscribeKey, final String channel, final String channelGroup, final Map<String, PNAccessManagerKeyData> authKeys) {
        this.level = level;
        this.subscribeKey = subscribeKey;
        this.channel = channel;
        this.channelGroup = channelGroup;
        this.authKeys = authKeys;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNAccessManagerAuditResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String level;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String subscribeKey;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String channel;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String channelGroup;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Map<String, PNAccessManagerKeyData> authKeys;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNAccessManagerAuditResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerAuditResultBuilder level(final String level) {
            this.level = level;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerAuditResultBuilder subscribeKey(final String subscribeKey) {
            this.subscribeKey = subscribeKey;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerAuditResultBuilder channel(final String channel) {
            this.channel = channel;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerAuditResultBuilder channelGroup(final String channelGroup) {
            this.channelGroup = channelGroup;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerAuditResultBuilder authKeys(final Map<String, PNAccessManagerKeyData> authKeys) {
            this.authKeys = authKeys;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNAccessManagerAuditResult build() {
            return new PNAccessManagerAuditResult(level, subscribeKey, channel, channelGroup, authKeys);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNAccessManagerAuditResult.PNAccessManagerAuditResultBuilder(level=" + this.level + ", subscribeKey=" + this.subscribeKey + ", channel=" + this.channel + ", channelGroup=" + this.channelGroup + ", authKeys=" + this.authKeys + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNAccessManagerAuditResultBuilder builder() {
        return new PNAccessManagerAuditResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getLevel() {
        return this.level;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSubscribeKey() {
        return this.subscribeKey;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getChannel() {
        return this.channel;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getChannelGroup() {
        return this.channelGroup;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, PNAccessManagerKeyData> getAuthKeys() {
        return this.authKeys;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNAccessManagerAuditResult(level=" + this.getLevel() + ", subscribeKey=" + this.getSubscribeKey() + ", channel=" + this.getChannel() + ", channelGroup=" + this.getChannelGroup() + ", authKeys=" + this.getAuthKeys() + ")";
    }
}
