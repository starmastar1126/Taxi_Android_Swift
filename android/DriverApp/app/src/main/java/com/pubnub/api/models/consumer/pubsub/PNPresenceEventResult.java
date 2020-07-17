package com.pubnub.api.models.consumer.pubsub;

import com.google.gson.JsonElement;

import java.util.List;

public class PNPresenceEventResult {

    private String event;

    private String uuid;
    private Long timestamp;
    private Integer occupancy;
    private JsonElement state;

    @Deprecated
    private String subscribedChannel;
    @Deprecated
    private String actualChannel;

    private String channel;
    private String subscription;

    private Long timetoken;
    private Object userMetadata;
    private List<String> join;
    private List<String> leave;
    private List<String> timeout;
    private Boolean hereNowRefresh;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNPresenceEventResult(final String event, final String uuid, final Long timestamp, final Integer occupancy, final JsonElement state, final String subscribedChannel, final String actualChannel, final String channel, final String subscription, final Long timetoken, final Object userMetadata, final List<String> join, final List<String> leave, final List<String> timeout, final Boolean hereNowRefresh) {
        this.event = event;
        this.uuid = uuid;
        this.timestamp = timestamp;
        this.occupancy = occupancy;
        this.state = state;
        this.subscribedChannel = subscribedChannel;
        this.actualChannel = actualChannel;
        this.channel = channel;
        this.subscription = subscription;
        this.timetoken = timetoken;
        this.userMetadata = userMetadata;
        this.join = join;
        this.leave = leave;
        this.timeout = timeout;
        this.hereNowRefresh = hereNowRefresh;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNPresenceEventResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String event;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String uuid;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Long timestamp;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Integer occupancy;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private JsonElement state;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String subscribedChannel;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String actualChannel;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String channel;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private String subscription;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Long timetoken;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Object userMetadata;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> join;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> leave;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<String> timeout;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Boolean hereNowRefresh;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNPresenceEventResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder event(final String event) {
            this.event = event;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder uuid(final String uuid) {
            this.uuid = uuid;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder timestamp(final Long timestamp) {
            this.timestamp = timestamp;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder occupancy(final Integer occupancy) {
            this.occupancy = occupancy;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder state(final JsonElement state) {
            this.state = state;
            return this;
        }

        @java.lang.Deprecated
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder subscribedChannel(final String subscribedChannel) {
            this.subscribedChannel = subscribedChannel;
            return this;
        }

        @java.lang.Deprecated
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder actualChannel(final String actualChannel) {
            this.actualChannel = actualChannel;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder channel(final String channel) {
            this.channel = channel;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder subscription(final String subscription) {
            this.subscription = subscription;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder timetoken(final Long timetoken) {
            this.timetoken = timetoken;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder userMetadata(final Object userMetadata) {
            this.userMetadata = userMetadata;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder join(final List<String> join) {
            this.join = join;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder leave(final List<String> leave) {
            this.leave = leave;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder timeout(final List<String> timeout) {
            this.timeout = timeout;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResultBuilder hereNowRefresh(final Boolean hereNowRefresh) {
            this.hereNowRefresh = hereNowRefresh;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNPresenceEventResult build() {
            return new PNPresenceEventResult(event, uuid, timestamp, occupancy, state, subscribedChannel, actualChannel, channel, subscription, timetoken, userMetadata, join, leave, timeout, hereNowRefresh);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNPresenceEventResult.PNPresenceEventResultBuilder(event=" + this.event + ", uuid=" + this.uuid + ", timestamp=" + this.timestamp + ", occupancy=" + this.occupancy + ", state=" + this.state + ", subscribedChannel=" + this.subscribedChannel + ", actualChannel=" + this.actualChannel + ", channel=" + this.channel + ", subscription=" + this.subscription + ", timetoken=" + this.timetoken + ", userMetadata=" + this.userMetadata + ", join=" + this.join + ", leave=" + this.leave + ", timeout=" + this.timeout + ", hereNowRefresh=" + this.hereNowRefresh + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNPresenceEventResultBuilder builder() {
        return new PNPresenceEventResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getEvent() {
        return this.event;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getUuid() {
        return this.uuid;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimestamp() {
        return this.timestamp;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Integer getOccupancy() {
        return this.occupancy;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getState() {
        return this.state;
    }

    @java.lang.Deprecated
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSubscribedChannel() {
        return this.subscribedChannel;
    }

    @java.lang.Deprecated
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getActualChannel() {
        return this.actualChannel;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getChannel() {
        return this.channel;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public String getSubscription() {
        return this.subscription;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimetoken() {
        return this.timetoken;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Object getUserMetadata() {
        return this.userMetadata;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getJoin() {
        return this.join;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getLeave() {
        return this.leave;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<String> getTimeout() {
        return this.timeout;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Boolean getHereNowRefresh() {
        return this.hereNowRefresh;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNPresenceEventResult(event=" + this.getEvent() + ", uuid=" + this.getUuid() + ", timestamp=" + this.getTimestamp() + ", occupancy=" + this.getOccupancy() + ", state=" + this.getState() + ", subscribedChannel=" + this.getSubscribedChannel() + ", actualChannel=" + this.getActualChannel() + ", channel=" + this.getChannel() + ", subscription=" + this.getSubscription() + ", timetoken=" + this.getTimetoken() + ", userMetadata=" + this.getUserMetadata() + ", join=" + this.getJoin() + ", leave=" + this.getLeave() + ", timeout=" + this.getTimeout() + ", hereNowRefresh=" + this.getHereNowRefresh() + ")";
    }
}
