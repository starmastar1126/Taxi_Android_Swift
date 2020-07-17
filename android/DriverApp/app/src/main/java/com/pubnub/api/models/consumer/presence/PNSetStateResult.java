package com.pubnub.api.models.consumer.presence;

import com.google.gson.JsonElement;

public class PNSetStateResult {

    private JsonElement state;

    PNSetStateResult(final JsonElement state) {
        this.state = state;
    }

    public static class PNSetStateResultBuilder {
        private JsonElement state;

        PNSetStateResultBuilder() {
        }

        public PNSetStateResultBuilder state(final JsonElement state) {
            this.state = state;
            return this;
        }

        public PNSetStateResult build() {
            return new PNSetStateResult(state);
        }

        @java.lang.Override
        public java.lang.String toString() {
            return "PNSetStateResult.PNSetStateResultBuilder(state=" + this.state + ")";
        }
    }

    public static PNSetStateResultBuilder builder() {
        return new PNSetStateResultBuilder();
    }

    public JsonElement getState() {
        return this.state;
    }

    @java.lang.Override
    public java.lang.String toString() {
        return "PNSetStateResult(state=" + this.getState() + ")";
    }
}
