package com.pubnub.api.models.consumer.presence;

import java.util.Map;

public class PNHereNowResult {
    private int totalChannels;
    private int totalOccupancy;
    private Map<String, PNHereNowChannelData> channels;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNHereNowResult(final int totalChannels, final int totalOccupancy, final Map<String, PNHereNowChannelData> channels) {
        this.totalChannels = totalChannels;
        this.totalOccupancy = totalOccupancy;
        this.channels = channels;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNHereNowResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private int totalChannels;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private int totalOccupancy;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Map<String, PNHereNowChannelData> channels;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNHereNowResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowResultBuilder totalChannels(final int totalChannels) {
            this.totalChannels = totalChannels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowResultBuilder totalOccupancy(final int totalOccupancy) {
            this.totalOccupancy = totalOccupancy;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowResultBuilder channels(final Map<String, PNHereNowChannelData> channels) {
            this.channels = channels;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowResult build() {
            return new PNHereNowResult(totalChannels, totalOccupancy, channels);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNHereNowResult.PNHereNowResultBuilder(totalChannels=" + this.totalChannels + ", totalOccupancy=" + this.totalOccupancy + ", channels=" + this.channels + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNHereNowResultBuilder builder() {
        return new PNHereNowResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getTotalChannels() {
        return this.totalChannels;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getTotalOccupancy() {
        return this.totalOccupancy;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, PNHereNowChannelData> getChannels() {
        return this.channels;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNHereNowResult(totalChannels=" + this.getTotalChannels() + ", totalOccupancy=" + this.getTotalOccupancy() + ", channels=" + this.getChannels() + ")";
    }
}
