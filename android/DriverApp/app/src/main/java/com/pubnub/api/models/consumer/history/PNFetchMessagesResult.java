package com.pubnub.api.models.consumer.history;

import com.pubnub.api.models.consumer.pubsub.PNMessageResult;

import java.util.List;
import java.util.Map;

public class PNFetchMessagesResult {
    private Map<String, List<PNMessageResult>> channels;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNFetchMessagesResult(final Map<String, List<PNMessageResult>> channels) {
        this.channels = channels;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNFetchMessagesResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Map<String, List<PNMessageResult>> channels;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNFetchMessagesResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNFetchMessagesResultBuilder channels(final Map<String, List<PNMessageResult>> channels) {
            this.channels = channels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNFetchMessagesResult build() {
            return new PNFetchMessagesResult(channels);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNFetchMessagesResult.PNFetchMessagesResultBuilder(channels=" + this.channels + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNFetchMessagesResultBuilder builder() {
        return new PNFetchMessagesResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, List<PNMessageResult>> getChannels() {
        return this.channels;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNFetchMessagesResult(channels=" + this.getChannels() + ")";
    }
}
