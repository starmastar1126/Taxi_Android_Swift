package com.pubnub.api.models.consumer.pubsub;

import com.google.gson.JsonElement;


public class PNMessageResult {

    private JsonElement message;

    @Deprecated
    private String subscribedChannel;
    @Deprecated
    private String actualChannel;

    private String channel;
    private String subscription;

    private Long timetoken;
    private JsonElement userMetadata;

    private String publisher;

    PNMessageResult(final JsonElement message, final String subscribedChannel, final String actualChannel, final String channel, final String subscription, final Long timetoken, final JsonElement userMetadata, final String publisher) {
        this.message = message;
        this.subscribedChannel = subscribedChannel;
        this.actualChannel = actualChannel;
        this.channel = channel;
        this.subscription = subscription;
        this.timetoken = timetoken;
        this.userMetadata = userMetadata;
        this.publisher = publisher;
    }

    public static class PNMessageResultBuilder {

        private JsonElement message;

        private String subscribedChannel;

        private String actualChannel;

        private String channel;

        private String subscription;

        private Long timetoken;

        private JsonElement userMetadata;

        private String publisher;

        PNMessageResultBuilder() {
        }

        public PNMessageResultBuilder message(final JsonElement message) {
            this.message = message;
            return this;
        }

        public PNMessageResultBuilder subscribedChannel(final String subscribedChannel) {
            this.subscribedChannel = subscribedChannel;
            return this;
        }

        public PNMessageResultBuilder actualChannel(final String actualChannel) {
            this.actualChannel = actualChannel;
            return this;
        }

        public PNMessageResultBuilder channel(final String channel) {
            this.channel = channel;
            return this;
        }

        public PNMessageResultBuilder subscription(final String subscription) {
            this.subscription = subscription;
            return this;
        }

        public PNMessageResultBuilder timetoken(final Long timetoken) {
            this.timetoken = timetoken;
            return this;
        }

        public PNMessageResultBuilder userMetadata(final JsonElement userMetadata) {
            this.userMetadata = userMetadata;
            return this;
        }

        public PNMessageResultBuilder publisher(final String publisher) {
            this.publisher = publisher;
            return this;
        }

        public PNMessageResult build() {
            return new PNMessageResult(message, subscribedChannel, actualChannel, channel, subscription, timetoken, userMetadata, publisher);
        }

        public java.lang.String toString() {
            return "PNMessageResult.PNMessageResultBuilder(message=" + this.message + ", subscribedChannel=" + this.subscribedChannel + ", actualChannel=" + this.actualChannel + ", channel=" + this.channel + ", subscription=" + this.subscription + ", timetoken=" + this.timetoken + ", userMetadata=" + this.userMetadata + ", publisher=" + this.publisher + ")";
        }
    }

    public static PNMessageResultBuilder builder() {
        return new PNMessageResultBuilder();
    }

    public JsonElement getMessage() {
        return this.message;
    }

    public String getSubscribedChannel() {
        return this.subscribedChannel;
    }

    public String getActualChannel() {
        return this.actualChannel;
    }

    public String getChannel() {
        return this.channel;
    }


    public String getSubscription() {
        return this.subscription;
    }


    public Long getTimetoken() {
        return this.timetoken;
    }


    public JsonElement getUserMetadata() {
        return this.userMetadata;
    }

    public String getPublisher() {
        return this.publisher;
    }


    public java.lang.String toString() {
        return "PNMessageResult(message=" + this.getMessage() + ", subscribedChannel=" + this.getSubscribedChannel() + ", actualChannel=" + this.getActualChannel() + ", channel=" + this.getChannel() + ", subscription=" + this.getSubscription() + ", timetoken=" + this.getTimetoken() + ", userMetadata=" + this.getUserMetadata() + ", publisher=" + this.getPublisher() + ")";
    }
}

