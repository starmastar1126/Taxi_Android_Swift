package com.pubnub.api.models.consumer.history;

import java.util.List;

public class PNHistoryResult {

    private List<PNHistoryItemResult> messages;
    private Long startTimetoken;
    private Long endTimetoken;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNHistoryResult(final List<PNHistoryItemResult> messages, final Long startTimetoken, final Long endTimetoken) {
        this.messages = messages;
        this.startTimetoken = startTimetoken;
        this.endTimetoken = endTimetoken;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNHistoryResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private List<PNHistoryItemResult> messages;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Long startTimetoken;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Long endTimetoken;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNHistoryResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHistoryResultBuilder messages(final List<PNHistoryItemResult> messages) {
            this.messages = messages;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHistoryResultBuilder startTimetoken(final Long startTimetoken) {
            this.startTimetoken = startTimetoken;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHistoryResultBuilder endTimetoken(final Long endTimetoken) {
            this.endTimetoken = endTimetoken;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHistoryResult build() {
            return new PNHistoryResult(messages, startTimetoken, endTimetoken);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNHistoryResult.PNHistoryResultBuilder(messages=" + this.messages + ", startTimetoken=" + this.startTimetoken + ", endTimetoken=" + this.endTimetoken + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNHistoryResultBuilder builder() {
        return new PNHistoryResultBuilder();
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public List<PNHistoryItemResult> getMessages() {
        return this.messages;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getStartTimetoken() {
        return this.startTimetoken;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getEndTimetoken() {
        return this.endTimetoken;
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNHistoryResult(messages=" + this.getMessages() + ", startTimetoken=" + this.getStartTimetoken() + ", endTimetoken=" + this.getEndTimetoken() + ")";
    }

}
