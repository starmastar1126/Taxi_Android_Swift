package com.pubnub.api.models.consumer.presence;

import com.google.gson.JsonElement;

public class PNHereNowOccupantData {
    private String uuid;
    private JsonElement state;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNHereNowOccupantData(final String uuid, final JsonElement state) {
        this.uuid = uuid;
        this.state = state;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNHereNowOccupantDataBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String uuid;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private JsonElement state;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNHereNowOccupantDataBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowOccupantDataBuilder uuid(final String uuid) {
            this.uuid = uuid;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowOccupantDataBuilder state(final JsonElement state) {
            this.state = state;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHereNowOccupantData build() {
            return new PNHereNowOccupantData(uuid, state);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNHereNowOccupantData.PNHereNowOccupantDataBuilder(uuid=" + this.uuid + ", state=" + this.state + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNHereNowOccupantDataBuilder builder() {
        return new PNHereNowOccupantDataBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getUuid() {
        return this.uuid;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getState() {
        return this.state;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNHereNowOccupantData(uuid=" + this.getUuid() + ", state=" + this.getState() + ")";
    }
}
