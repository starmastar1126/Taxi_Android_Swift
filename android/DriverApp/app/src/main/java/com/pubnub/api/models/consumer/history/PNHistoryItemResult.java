package com.pubnub.api.models.consumer.history;

import com.google.gson.JsonElement;

import lombok.Getter;

public class PNHistoryItemResult {


    private Long timetoken;

    private JsonElement entry;

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    PNHistoryItemResult(final Long timetoken, final JsonElement entry) {
        this.timetoken = timetoken;
        this.entry = entry;
    }


    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static class PNHistoryItemResultBuilder {
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private Long timetoken;
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        private JsonElement entry;

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        PNHistoryItemResultBuilder() {
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHistoryItemResultBuilder timetoken(final Long timetoken) {
            this.timetoken = timetoken;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHistoryItemResultBuilder entry(final JsonElement entry) {
            this.entry = entry;
            return this;
        }

        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public PNHistoryItemResult build() {
            return new PNHistoryItemResult(timetoken, entry);
        }

        @java.lang.Override
        @java.lang.SuppressWarnings("all")
        @javax.annotation.Generated("lombok")
        public java.lang.String toString() {
            return "PNHistoryItemResult.PNHistoryItemResultBuilder(timetoken=" + this.timetoken + ", entry=" + this.entry + ")";
        }
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public static PNHistoryItemResultBuilder builder() {
        return new PNHistoryItemResultBuilder();
    }

    @java.lang.Override
    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public java.lang.String toString() {
        return "PNHistoryItemResult(timetoken=" + this.getTimetoken() + ", entry=" + this.getEntry() + ")";
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public Long getTimetoken() {
        return this.timetoken;
    }

    @java.lang.SuppressWarnings("all")
    @javax.annotation.Generated("lombok")
    public JsonElement getEntry() {
        return this.entry;
    }
}
