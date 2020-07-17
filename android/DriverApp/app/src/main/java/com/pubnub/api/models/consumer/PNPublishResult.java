package com.pubnub.api.models.consumer;

public class PNPublishResult {
    private Long timetoken;

    PNPublishResult(final Long timetoken) {
        this.timetoken = timetoken;
    }


    public static class PNPublishResultBuilder {

        private Long timetoken;

        PNPublishResultBuilder() {
        }

        public PNPublishResultBuilder timetoken(final Long timetoken) {
            this.timetoken = timetoken;
            return this;
        }

        public PNPublishResult build() {
            return new PNPublishResult(timetoken);
        }

        @java.lang.Override
        public java.lang.String toString() {
            return "PNPublishResult.PNPublishResultBuilder(timetoken=" + this.timetoken + ")";
        }
    }

    public static PNPublishResultBuilder builder() {
        return new PNPublishResultBuilder();
    }

    public Long getTimetoken() {
        return this.timetoken;
    }

    @java.lang.Override
    public java.lang.String toString() {
        return "PNPublishResult(timetoken=" + this.getTimetoken() + ")";
    }
}
