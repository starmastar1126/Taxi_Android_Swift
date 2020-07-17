package com.pubnub.api.models.consumer;

public class PNTimeResult {
    private Long timetoken;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNTimeResult(final Long timetoken) {
        this.timetoken = timetoken;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNTimeResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Long timetoken;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNTimeResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNTimeResultBuilder timetoken(final Long timetoken) {
            this.timetoken = timetoken;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNTimeResult build() {
            return new PNTimeResult(timetoken);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNTimeResult.PNTimeResultBuilder(timetoken=" + this.timetoken + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNTimeResultBuilder builder() {
        return new PNTimeResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimetoken() {
        return this.timetoken;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNTimeResult(timetoken=" + this.getTimetoken() + ")";
    }
}
