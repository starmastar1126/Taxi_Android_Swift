package com.pubnub.api.models.consumer.presence;

import com.google.gson.JsonElement;

import java.util.Map;

public class PNGetStateResult {

    private Map<String, JsonElement> stateByUUID;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNGetStateResult(final Map<String, JsonElement> stateByUUID) {
        this.stateByUUID = stateByUUID;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNGetStateResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Map<String, JsonElement> stateByUUID;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNGetStateResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNGetStateResultBuilder stateByUUID(final Map<String, JsonElement> stateByUUID) {
            this.stateByUUID = stateByUUID;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNGetStateResult build() {
            return new PNGetStateResult(stateByUUID);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNGetStateResult.PNGetStateResultBuilder(stateByUUID=" + this.stateByUUID + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNGetStateResultBuilder builder() {
        return new PNGetStateResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Map<String, JsonElement> getStateByUUID() {
        return this.stateByUUID;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNGetStateResult(stateByUUID=" + this.getStateByUUID() + ")";
    }
}
