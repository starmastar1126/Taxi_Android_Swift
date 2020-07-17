package com.pubnub.api.models.consumer.presence;

import java.util.List;

public class PNHereNowChannelData {

    private String channelName;
    private int occupancy;
    private List<PNHereNowOccupantData> occupants;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNHereNowChannelData(final String channelName, final int occupancy, final List<PNHereNowOccupantData> occupants) {
        this.channelName = channelName;
        this.occupancy = occupancy;
        this.occupants = occupants;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNHereNowChannelDataBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String channelName;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private int occupancy;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<PNHereNowOccupantData> occupants;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNHereNowChannelDataBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowChannelDataBuilder channelName(final String channelName) {
            this.channelName = channelName;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowChannelDataBuilder occupancy(final int occupancy) {
            this.occupancy = occupancy;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowChannelDataBuilder occupants(final List<PNHereNowOccupantData> occupants) {
            this.occupants = occupants;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowChannelData build() {
            return new PNHereNowChannelData(channelName, occupancy, occupants);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNHereNowChannelData.PNHereNowChannelDataBuilder(channelName=" + this.channelName + ", occupancy=" + this.occupancy + ", occupants=" + this.occupants + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNHereNowChannelDataBuilder builder() {
        return new PNHereNowChannelDataBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getChannelName() {
        return this.channelName;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public int getOccupancy() {
        return this.occupancy;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<PNHereNowOccupantData> getOccupants() {
        return this.occupants;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNHereNowChannelData(channelName=" + this.getChannelName() + ", occupancy=" + this.getOccupancy() + ", occupants=" + this.getOccupants() + ")";
    }
}
